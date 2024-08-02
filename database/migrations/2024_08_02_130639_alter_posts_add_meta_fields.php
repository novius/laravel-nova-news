<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Novius\LaravelMeta\Enums\IndexFollow;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('nova_news_posts', static function (Blueprint $table) {
            $table->addMeta();
        });

        DB::table('nova_news_posts')->orderBy('id')->chunk(100, function ($posts) {
            foreach ($posts as $post) {
                $meta = [
                    'seo_robots' => IndexFollow::index_follow->value,
                    'seo_title' => $post->seo_title,
                    'seo_description' => $post->seo_description,
                    'og_title' => $post->og_title,
                    'og_description' => $post->og_description,
                    'og_image' => $post->og_image,
                ];
                DB::table('nova_news_posts')->where('id', $post->id)->update([
                    'meta' => json_encode($meta, JSON_THROW_ON_ERROR),
                ]);
            }
        });

        Schema::table('nova_news_posts', static function (Blueprint $table) {
            $table->dropColumn([
                'seo_title',
                'seo_description',
                'og_title',
                'og_description',
                'og_image',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nova_news_posts', static function (Blueprint $table) {
            $table->string('seo_title')->nullable();
            $table->string('seo_description')->nullable();

            $table->string('og_title')->nullable();
            $table->string('og_description')->nullable();
            $table->string('og_image')->nullable();
        });

        DB::table('nova_news_posts')->orderBy('id')->chunk(100, function ($posts) {
            foreach ($posts as $post) {
                $meta = json_decode($post->meta, false, 512, JSON_THROW_ON_ERROR);
                DB::table('nova_news_posts')->where('id', $post->id)->update([
                    'seo_title' => $meta->seo_title ?? null,
                    'seo_description' => $meta->seo_description ?? null,
                    'og_title' => $meta->og_title ?? null,
                    'og_description' => $meta->og_description ?? null,
                    'og_image' => $meta->og_image ?? null,
                ]);
            }
        });

        Schema::table('nova_news_posts', static function (Blueprint $table) {
            $table->dropColumn('meta');
        });
    }
};
