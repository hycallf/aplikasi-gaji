<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\CompanyProfile;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call(OperatorSeeder::class);
        $this->call(CompanyProfileSeeder::class);

        
    }
}
