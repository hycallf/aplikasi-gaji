<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckDatabaseConstraints extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'db:check-constraints {table?}';

    /**
     * The console command description.
     */
    protected $description = 'Check indexes, foreign keys, and constraints for a table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tableName = $this->argument('table') ?? 'dosen_attendances';

        $this->info("Checking constraints for table: {$tableName}");
        $this->newLine();

        try {
            // Check if table exists
            if (!$this->tableExists($tableName)) {
                $this->error("Table '{$tableName}' does not exist!");
                return 1;
            }

            // Get all indexes
            $this->displayIndexes($tableName);
            $this->newLine();

            // Get foreign keys
            $this->displayForeignKeys($tableName);
            $this->newLine();

            // Get columns
            $this->displayColumns($tableName);

            return 0;

        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
            return 1;
        }
    }

    /**
     * Check if table exists
     */
    private function tableExists(string $table): bool
    {
        $query = "SHOW TABLES LIKE '{$table}'";
        $result = DB::select($query);
        return !empty($result);
    }

    /**
     * Display all indexes for the table
     */
    private function displayIndexes(string $table): void
    {
        $this->info("📋 INDEXES:");

        $indexes = DB::select("SHOW INDEX FROM {$table}");

        if (empty($indexes)) {
            $this->warn("  No indexes found");
            return;
        }

        // Group by index name
        $groupedIndexes = [];
        foreach ($indexes as $index) {
            $indexName = $index->Key_name;
            if (!isset($groupedIndexes[$indexName])) {
                $groupedIndexes[$indexName] = [
                    'columns' => [],
                    'unique' => $index->Non_unique == 0,
                    'type' => $index->Index_type
                ];
            }
            $groupedIndexes[$indexName]['columns'][] = $index->Column_name;
        }

        // Display grouped indexes
        foreach ($groupedIndexes as $name => $info) {
            $type = $info['unique'] ? 'UNIQUE' : 'INDEX';
            $columns = implode(', ', $info['columns']);

            $this->line("  <fg=cyan>{$name}</> ({$type})");
            $this->line("    Columns: <fg=yellow>{$columns}</>");
            $this->line("    Type: {$info['type']}");
        }
    }

    /**
     * Display all foreign keys for the table
     */
    private function displayForeignKeys(string $table): void
    {
        $this->info("🔗 FOREIGN KEYS:");

        $database = DB::getDatabaseName();

        $foreignKeys = DB::select("
            SELECT
                CONSTRAINT_NAME as name,
                COLUMN_NAME as column,
                REFERENCED_TABLE_NAME as ref_table,
                REFERENCED_COLUMN_NAME as ref_column
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = ?
            AND TABLE_NAME = ?
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ", [$database, $table]);

        if (empty($foreignKeys)) {
            $this->warn("  No foreign keys found");
            return;
        }

        foreach ($foreignKeys as $fk) {
            $this->line("  <fg=cyan>{$fk->name}</>");
            $this->line("    {$fk->column} → {$fk->ref_table}({$fk->ref_column})");
        }
    }

    /**
     * Display all columns for the table
     */
    private function displayColumns(string $table): void
    {
        $this->info("📊 COLUMNS:");

        $columns = DB::select("SHOW COLUMNS FROM {$table}");

        foreach ($columns as $column) {
            $nullable = $column->Null === 'YES' ? '(nullable)' : '(NOT NULL)';
            $default = $column->Default ? "default: {$column->Default}" : '';

            $this->line("  <fg=cyan>{$column->Field}</> <fg=yellow>{$column->Type}</> {$nullable} {$default}");
        }
    }
}
