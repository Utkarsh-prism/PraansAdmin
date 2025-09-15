<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('act_forms', function (Blueprint $table) {
            $table->id();

            // FK -> parent
            $table->unsignedBigInteger('act_rule_form_id');
            $table->foreign('act_rule_form_id', 'act_forms_act_rule_form_id_foreign')
                  ->references('id')->on('act_rule_forms')
                  ->cascadeOnDelete();

            // Child payload
            $table->string('form_no', 100);      // e.g., "Form I", "Form II"
            $table->string('title');
            $table->text('short_desc')->nullable();
            $table->string('pdf_path');          // stored on 'public' disk
            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();

            // Enforce unique Form No per Act (best practice)
            $table->unique(
                ['act_rule_form_id', 'form_no'],
                'act_forms_act_rule_form_id_form_no_unique'
            );

            // Helpful index for FK (explicit name; avoids MySQL name clashes)
            $table->index('act_rule_form_id', 'act_forms_act_rule_form_id_index');
        });
    }

    public function down(): void
    {
        Schema::table('act_forms', function (Blueprint $table) {
            // Drop in reverse order to avoid 1553 FK/index errors
            $table->dropForeign('act_forms_act_rule_form_id_foreign');
            $table->dropUnique('act_forms_act_rule_form_id_form_no_unique');
            $table->dropIndex('act_forms_act_rule_form_id_index');
        });

        Schema::dropIfExists('act_forms');
    }
};
