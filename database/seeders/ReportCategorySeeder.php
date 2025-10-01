<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ReportCategory;

class ReportCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = ReportCategory::getDefaultCategories();

        foreach ($categories as $category) {
            ReportCategory::updateOrCreate(
                ['name' => $category['name'], 'report_type' => $category['report_type']],
                $category
            );
        }
    }
}