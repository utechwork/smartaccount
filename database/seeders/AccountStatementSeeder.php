<?php

namespace Database\Seeders;

use App\Models\AccountStatement;
use App\Models\Flat;
use App\Models\Vendor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AccountStatementSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the account_statements table.
     */
    public function run(): void
    {
        // Create some flats first if they don't exist
        $flats = Flat::get();
        if ($flats->isEmpty()) {
            $flats = Flat::factory(5)->create();
        }

        // Create some vendors first if they don't exist
        $vendors = Vendor::get();
        if ($vendors->isEmpty()) {
            $vendors = Vendor::factory(5)->create();
        }

        // Create sample account statements
        $statements = [
            [
                'date' => now()->subDays(10),
                'narration' => 'Opening Balance',
                'chq_ref_no' => null,
                'value_date' => now()->subDays(10),
                'withdrawal_amt' => null,
                'deposit_amt' => 100000.00,
                'closing_balance' => 100000.00,
                'flat_id' => null,
                'vendor_id' => null,
            ],
            [
                'date' => now()->subDays(8),
                'narration' => 'Payment to Vendor - Maintenance',
                'chq_ref_no' => 'CHQ001',
                'value_date' => now()->subDays(8),
                'withdrawal_amt' => 5000.00,
                'deposit_amt' => null,
                'closing_balance' => 95000.00,
                'flat_id' => $flats->first()->id,
                'vendor_id' => $vendors->first()->id,
            ],
            [
                'date' => now()->subDays(5),
                'narration' => 'Water Bill - Flat 101',
                'chq_ref_no' => null,
                'value_date' => now()->subDays(5),
                'withdrawal_amt' => 2500.00,
                'deposit_amt' => null,
                'closing_balance' => 92500.00,
                'flat_id' => $flats->first()->id,
                'vendor_id' => $vendors->get(1)->id,
            ],
            [
                'date' => now()->subDays(3),
                'narration' => 'Electricity Charges - Common Area',
                'chq_ref_no' => 'TRF002',
                'value_date' => now()->subDays(3),
                'withdrawal_amt' => 8000.00,
                'deposit_amt' => null,
                'closing_balance' => 84500.00,
                'flat_id' => null,
                'vendor_id' => $vendors->get(2)->id,
            ],
            [
                'date' => now()->subDays(2),
                'narration' => 'Rent Collection - Flat 102',
                'chq_ref_no' => 'DEP001',
                'value_date' => now()->subDays(2),
                'withdrawal_amt' => null,
                'deposit_amt' => 15000.00,
                'closing_balance' => 99500.00,
                'flat_id' => $flats->get(1)->id,
                'vendor_id' => null,
            ],
            [
                'date' => now()->subDays(1),
                'narration' => 'Insurance Premium Payment',
                'chq_ref_no' => null,
                'value_date' => now()->subDays(1),
                'withdrawal_amt' => 12000.00,
                'deposit_amt' => null,
                'closing_balance' => 87500.00,
                'flat_id' => null,
                'vendor_id' => $vendors->get(3)->id,
            ],
            [
                'date' => now(),
                'narration' => 'Maintenance Fee Collection',
                'chq_ref_no' => 'DEP002',
                'value_date' => now(),
                'withdrawal_amt' => null,
                'deposit_amt' => 25000.00,
                'closing_balance' => 112500.00,
                'flat_id' => $flats->get(2)->id,
                'vendor_id' => null,
            ],
        ];

        foreach ($statements as $statement) {
            AccountStatement::create($statement);
        }
    }
}
