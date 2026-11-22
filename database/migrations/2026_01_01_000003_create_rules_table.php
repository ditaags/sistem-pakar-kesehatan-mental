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
        Schema::create('rules', function (Blueprint $table) {
            $table->string('id', 20)->primary(); // R01, R02, etc.
            $table->string('condition_code', 10);
            $table->string('symptom_code', 10);
            $table->decimal('cf_pakar', 4, 2); // 0.10 - 0.90
            $table->text('source_literature');
            $table->timestamps();

            $table->foreign('condition_code')->references('code')->on('conditions')->onDelete('cascade');
            $table->foreign('symptom_code')->references('code')->on('symptoms')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rules');
    }
};
