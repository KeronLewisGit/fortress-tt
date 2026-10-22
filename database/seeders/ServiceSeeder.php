<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        foreach (require database_path('seeders/data/services.php') as $i => $row) {
            Service::updateOrCreate(['slug' => $row['slug']], $row + ['sort_order' => ($i + 1) * 10]);
        }
    }
}
