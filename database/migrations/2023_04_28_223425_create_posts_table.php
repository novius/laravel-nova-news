<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Novius\LaravelNovaNews\Models\NewsPost;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('nova_news_posts', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->string('slug');
            $table->string('locale', 20);
            $table->boolean('featured')->default(false);
            $table->longText('intro')->nullable();
            $table->longText('content')->nullable();
            $table->string('featured_image')->nullable();
            $table->string('card_image')->nullable();

            $table->enum('post_status', [NewsPost::STATUS_DRAFT, NewsPost::STATUS_PUBLISHED])->default(NewsPost::STATUS_DRAFT);
            $table->dateTime('publication_date');
            $table->dateTime('end_publication_date')->nullable();
            $table->string('preview_token')->nullable();

            $table->string('seo_title')->nullable();
            $table->string('seo_description')->nullable();

            $table->string('og_title')->nullable();
            $table->string('og_description')->nullable();
            $table->string('og_image')->nullable();

            $table->json('extras')->nullable();

            $table->unique(['slug', 'locale']);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nova_news_posts');
    }
};
