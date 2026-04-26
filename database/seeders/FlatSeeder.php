<?php

namespace Database\Seeders;

use App\Models\Flat;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FlatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing flats
        DB::table('flats')->truncate();
        
        // Define flats to exclude
        $excludedFlats = ['801', '802', '810'];
        
        // 1BHK flats (flat numbers 1, 4, 5, 10 on each floor)
        $oneBhkNumbers = [1, 4, 5, 10];

        $flatsToCreate = [];

        // Create flats for 11 floors
        for ($floor = 1; $floor <= 11; $floor++) {
            for ($unit = 1; $unit <= 10; $unit++) {
                $flatNumber = $floor . str_pad($unit, 2, '0', STR_PAD_LEFT);

                // Skip excluded flats
                if (in_array($flatNumber, $excludedFlats)) {
                    continue;
                }

                // Determine flat type
                $flatType = in_array($unit, $oneBhkNumbers) ? '1BHK' : '2BHK';
                
                // Calculate maintenance amount based on flat type (default owner)
                $maintenanceAmount = $flatType === '1BHK' ? 2500 : 2700;

                $flatsToCreate[] = [
                    'flat_number' => $flatNumber,
                    'floor_number' => $floor,
                    'flat_type' => $flatType,
                    'occupancy_type' => 'owner',
                    'owner_name' => null,
                    'owner_email' => null,
                    'owner_phone' => null,
                    'maintenance_amount' => $maintenanceAmount,
                    'maintenance_status' => 'pending',
                    'last_maintenance_date' => null,
                    'notes' => null,
                    'builder_paid_exception' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // Insert in chunks to avoid memory issues
        collect($flatsToCreate)->chunk(100)->each(function ($chunk) {
            Flat::insert($chunk->toArray());
        });
    }
}
