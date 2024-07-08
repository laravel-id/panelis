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
        Schema::create('blog_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained();
            $table->string('slug')->unique();
            $table->string('title');
            $table->string('storage')->default('public');
            $table->string('image')->nullable();
            $table->mediumText('content');
            $table->boolean('is_visible')->default(true);
            $table->dateTime('published_at');
            $table->json('metadata');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('blog_category_post', function (Blueprint $table) {
            $table->foreignId('category_id')
                ->constrained('blog_categories')
                ->cascadeOnDelete();
            $table->foreignId('post_id')
                ->constrained('blog_posts')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // make sure delete relationship first
        Schema::dropIfExists('blog_category_post');
        Schema::dropIfExists('blog_posts');
    }
};
