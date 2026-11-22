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
        Schema::create('consultations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('patient_name', 100);
            $table->integer('patient_age');
            $table->string('patient_gender', 20);
            $table->json('selected_symptoms');
            $table->string('top_condition_code', 10)->nullable();
            $table->string('top_condition_name', 100)->nullable();
            $table->decimal('top_cf_score', 6, 4)->nullable();
            $table->decimal('top_percentage', 5, 2)->nullable();
            $table->boolean('is_indication_sufficient')->default(true);
            $table->boolean('has_safety_alert')->default(false);
            $table->json('full_results');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consultations');
    }
};
