<?php

namespace Novius\LaravelNovaNews\Database\Seeders;

use Illuminate\Database\Seeder;
use Novius\LaravelNovaNews\Models\NewsPost;

class NewsPostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        NewsPost::factory()->count(10)->create();
    }
}
