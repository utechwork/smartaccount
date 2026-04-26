<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Vendor;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create vendor categories
        $vendorCategories = [
            ['name' => 'Utilities', 'description' => 'Water, Electricity, Gas', 'type' => 'vendor', 'color' => '#FF6B6B'],
            ['name' => 'Maintenance', 'description' => 'Repairs and Maintenance', 'type' => 'vendor', 'color' => '#4ECDC4'],
            ['name' => 'Cleaning', 'description' => 'Cleaning Services', 'type' => 'vendor', 'color' => '#45B7D1'],
            ['name' => 'Security', 'description' => 'Security Services', 'type' => 'vendor', 'color' => '#FFA07A'],
            ['name' => 'Supplies', 'description' => 'Office and Cleaning Supplies', 'type' => 'vendor', 'color' => '#98D8C8'],
            ['name' => 'Contractors', 'description' => 'Construction and Renovation', 'type' => 'vendor', 'color' => '#F7DC6F'],
        ];

        foreach ($vendorCategories as $category) {
            Category::firstOrCreate(['name' => $category['name']], $category);
        }

        // Create account statement categories
        $statementCategories = [
            ['name' => 'Income', 'description' => 'Revenue and Income', 'type' => 'account_statement', 'color' => '#52C966'],
            ['name' => 'Expense', 'description' => 'Expenditures', 'type' => 'account_statement', 'color' => '#E74C3C'],
            ['name' => 'Transfer', 'description' => 'Internal Transfers', 'type' => 'account_statement', 'color' => '#3498DB'],
            ['name' => 'Deduction', 'description' => 'Deductions and Fees', 'type' => 'account_statement', 'color' => '#9B59B6'],
        ];

        foreach ($statementCategories as $category) {
            Category::firstOrCreate(['name' => $category['name']], $category);
        }
    }
}
