<?php

namespace Database\Seeders;

use App\Models\User;
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

        $operatorUser = User::create([
            'name' => 'Operator Payroll',
            'email' => 'operator@aplikasi.com',
            'password' => bcrypt('password123'),
            'role' => 'operator'
        ]);

        
    }
}
