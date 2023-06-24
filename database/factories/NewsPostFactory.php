<?php

namespace Novius\LaravelNovaNews\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Novius\LaravelNovaNews\Models\NewsPost;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<NewsPost>
 */
class NewsPostFactory extends Factory
{
    /**
     * {@inheritdoc}
     */
    protected $model = NewsPost::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(4),
            'locale' => 'en',
            'featured' => false,
            'intro' => $this->faker->paragraph(2),
            'content' => $this->faker->paragraph(5, true),
            'seo_title' => $this->faker->sentence(4),
            'seo_description' => $this->faker->paragraph(2),
            'og_title' => $this->faker->sentence(4),
            'og_description' => $this->faker->paragraph(2),
        ];
    }
}
