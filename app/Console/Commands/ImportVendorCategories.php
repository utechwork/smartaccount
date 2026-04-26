<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Vendor;
use App\Models\Category;
use League\Csv\Reader;
use League\Csv\Statement;

class ImportVendorCategories extends Command
{
    protected $signature = 'import:vendor-categories {file=storage/app/import/vendors.csv}';
    protected $description = 'Import vendors, categories, and their mapping from CSV';

    public function handle()
    {
        $filePath = $this->argument('file');

        if (!file_exists($filePath)) {
            $this->error("File not found: {$filePath}");
            return 1;
        }

        $csv = Reader::createFromPath($filePath, 'r');
        $csv->setHeaderOffset(0); // assumes first row is header
        $records = Statement::create()->process($csv);

        foreach ($records as $record) {
            $vendorName   = trim($record['vendor']);
            $categoryName = trim($record['category']);

            if (!$vendorName || !$categoryName) {
                $this->warn("Skipping row with missing vendor/category");
                continue;
            }

            // Create/find category
            $category = Category::firstOrCreate(
                ['name' => $categoryName, 'type' => 'vendor'],
                ['description' => null, 'color' => '#808080']
            );

            // Create/find vendor
            $vendor = Vendor::firstOrCreate(['name' => $vendorName]);

            // Attach mapping
            $vendor->categories()->syncWithoutDetaching([$category->id]);

            $this->info("Mapped vendor '{$vendor->name}' to category '{$category->name}'");
        }

        $this->info("Import completed successfully.");
        return 0;
    }
}
