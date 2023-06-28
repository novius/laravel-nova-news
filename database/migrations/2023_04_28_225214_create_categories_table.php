<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('nova_news_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('locale', 20);
            $table->unsignedBigInteger('locale_parent_id')->nullable();

            $table->string('seo_title')->nullable();
            $table->string('seo_description')->nullable();

            $table->string('og_title')->nullable();
            $table->string('og_description')->nullable();
            $table->string('og_image')->nullable();

            $table->json('extras')->nullable();

            $table->unique(['slug', 'locale']);

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('locale_parent_id')
                ->references('id')
                ->on('nova_news_categories')
                ->onDelete('restrict')
                ->onUpdate('restrict');
        });

        Schema::create('nova_news_post_category', function (Blueprint $table) {
            $table->unsignedBigInteger('news_post_id');
            $table->unsignedBigInteger('news_category_id');

            $table->foreign('news_post_id')
                ->references('id')
                ->on('nova_news_posts')
                ->cascadeOnDelete();

            $table->foreign('news_category_id')
                ->references('id')
                ->on('nova_news_categories')
                ->cascadeOnDelete();

            $table->primary(['news_post_id', 'news_category_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nova_news_post_category');
        Schema::dropIfExists('nova_news_categories');
    }
};
