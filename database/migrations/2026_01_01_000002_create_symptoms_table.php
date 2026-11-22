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
        Schema::create('symptoms', function (Blueprint $table) {
            $table->string('code', 10)->primary(); // G01, G02, etc.
            $table->text('text');
            $table->string('category', 50);
            $table->text('clinical_explanation');
            $table->boolean('is_safety_trigger')->default(false); // Aturan keselamatan (G25)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('symptoms');
    }
};
