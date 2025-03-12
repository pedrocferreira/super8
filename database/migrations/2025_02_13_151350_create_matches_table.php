<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('round_id')->constrained()->onDelete('cascade');
            $table->foreignId('court_id')->nullable()->constrained()->onDelete('set null');
            // Para Super 8 Individual
            $table->foreignId('team1_player1_id')->nullable()->constrained('players')->onDelete('cascade');
            $table->foreignId('team1_player2_id')->nullable()->constrained('players')->onDelete('cascade');
            $table->foreignId('team2_player1_id')->nullable()->constrained('players')->onDelete('cascade');
            $table->foreignId('team2_player2_id')->nullable()->constrained('players')->onDelete('cascade');
            // Para Super 12 com Duplas Fixas
            $table->foreignId('team1_pair_id')->nullable()->constrained('pairs')->onDelete('cascade');
            $table->foreignId('team2_pair_id')->nullable()->constrained('pairs')->onDelete('cascade');
            $table->enum('winner_team', ['team1', 'team2'])->nullable();
            $table->json('score_details')->nullable();
            $table->datetime('scheduled_time')->nullable();
            $table->enum('status', ['scheduled', 'in_progress', 'completed', 'cancelled'])->default('scheduled');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('matches');
    }
}; 