<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tournament;
use App\Models\Player;

class TournamentPlayersSeeder extends Seeder
{
    public function run()
    {
        $tournaments = Tournament::all();
        $allPlayers = Player::all();

        foreach ($tournaments as $tournament) {
            // Seleciona aleatoriamente 8 ou 12 jogadores dependendo do tipo do torneio
            $numberOfPlayers = match($tournament->type) {
                'super_8_doubles' => 8,
                'super_8_fixed_pairs' => 16,
                default => 12
            };
            $selectedPlayers = $allPlayers->random($numberOfPlayers);

            // Vincula os jogadores ao torneio
            $tournament->players()->attach($selectedPlayers->pluck('id'));
        }
    }
}
