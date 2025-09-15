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
        Schema::table('act_rule_forms', function (Blueprint $table) {
            $table->unsignedInteger('section_count')->default(0)->after('form_image_path');
            $table->unsignedInteger('rule_count')->default(0)->after('section_count');
            $table->unsignedInteger('form_count')->default(0)->after('rule_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('act_rule_forms', function (Blueprint $table) {
            $table->dropColumn(['section_count', 'rule_count', 'form_count']);
        });
    }
};
