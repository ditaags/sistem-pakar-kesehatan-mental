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
        Schema::create('conditions', function (Blueprint $table) {
            $table->string('code', 10)->primary(); // K01, K02, etc.
            $table->string('name', 100);
            $table->string('original_name', 100);
            $table->text('description');
            $table->json('general_advice');
            $table->text('seeking_help_advice');
            $table->json('dataset_insights')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conditions');
    }
};
