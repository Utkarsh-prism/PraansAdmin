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
        Schema::create('holidays', function (Blueprint $table) {
            $table->id();

           $table->string('state', 100);
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('short_desc')->nullable();

            // Holiday specific
            $table->string('holiday_name', 120); // e.g., Diwali, Holi

            // You can switch month to TINYINT(1-12) if you prefer numeric storage
            $table->enum('month', [
                'January','February','March','April','May','June',
                'July','August','September','October','November','December'
            ]);

            $table->date('date'); // stored as Y-m-d in DB
            $table->enum('day', [
                'Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'
            ]);

            $table->enum('type', ['Regional', 'National', 'Optional']);
            $table->string('holiday_pdf', 2048)->nullable(); // storage path

            $table->timestamps();

            // Optional index
            $table->index(['state', 'month', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('holidays');
    }
};
