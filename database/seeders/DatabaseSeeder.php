<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Seed categories
        $this->call(CategorySeeder::class);
        
        // Seed petty cash data
        $this->call(PettyCashSeeder::class);
        
        // Seed account statements with test data
        $this->call(AccountStatementSeeder::class);
    }
}
