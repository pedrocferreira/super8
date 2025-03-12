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
        Schema::create('tournaments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('location');
            $table->enum('type', ['super_8_individual', 'super_12_fixed_pairs']);
            $table->enum('status', ['draft', 'open', 'in_progress', 'completed'])->default('draft');
            $table->datetime('start_date');
            $table->datetime('end_date');
            $table->integer('min_players');
            $table->integer('max_players');
            $table->integer('number_of_courts')->default(2);
            $table->json('scoring_criteria')->nullable(); // Critérios específicos de pontuação
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tournaments');
    }
};
