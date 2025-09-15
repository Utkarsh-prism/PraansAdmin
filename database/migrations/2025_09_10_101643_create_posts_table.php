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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->string('slug', 255)->unique();
            $table->longText('content');
            $table->text('short_description')->nullable();
            $table->foreignId('author_id')->constrained('authors')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->date('published_date')->nullable();
            $table->json('tags')->nullable();
            $table->string('image', 255)->nullable();
            $table->string('meta_image', 255)->nullable();
            $table->string('meta_title', 255)->nullable();
            $table->string('meta_description', 355)->nullable();
            $table->string('meta_keywords', 355)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
