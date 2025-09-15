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
        Schema::create('holiday_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('holiday_id')->constrained()->cascadeOnDelete();
            $table->string('holiday_name', 120);
            $table->enum('type', ['Regional','National','Optional'])->default('Regional');
            $table->date('date');
            $table->string('day', 10)->nullable();    // auto from date
            $table->string('month', 15)->nullable();  // auto from date
            $table->string('holiday_pdf')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('holiday_details');
    }
};
