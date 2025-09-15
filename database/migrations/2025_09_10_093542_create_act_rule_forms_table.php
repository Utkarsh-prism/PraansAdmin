<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('act_rule_forms', function (Blueprint $table) {
            $table->id();

            // Core meta
            $table->string('title');
            $table->string('slug')->unique()->index();
            $table->string('short_description')->nullable();
            $table->longText('act_desc')->nullable();
            $table->longText('rule_desc')->nullable();

            // State scoping
            $table->string('state')->index();

            // Files (parent-level)
            // Act document (PDF/DOC/etc)
            $table->string('upload_path')->nullable();

            // Rule document (keep name to match your existing code/API)
            $table->string('form_image_path')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('act_rule_forms');
    }
};
