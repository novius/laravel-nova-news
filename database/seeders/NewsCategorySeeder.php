<?php

namespace Novius\LaravelNovaNews\Database\Seeders;

use Illuminate\Database\Seeder;
use Novius\LaravelNovaNews\Models\NewsCategory;

class NewsCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        NewsCategory::factory()->count(10)->create();
    }
}
