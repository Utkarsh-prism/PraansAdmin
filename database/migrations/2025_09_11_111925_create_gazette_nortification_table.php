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
        Schema::create('gazette_nortifications', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique()->index();
            $table->string('short_description')->nullable();
            $table->string('state', 100)->index();
            $table->date('updated_date')->nullable()->index();
            $table->date('effective_date')->nullable()->index();
            $table->longText('description')->nullable();
            $table->string('pdf_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gazette_nortifications');
    }
};
