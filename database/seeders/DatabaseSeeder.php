<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([SettingsSeeder::class, ServiceSeeder::class, MessageTemplateSeeder::class, JobPostingSeeder::class, AdminSeeder::class]);
    }
}
