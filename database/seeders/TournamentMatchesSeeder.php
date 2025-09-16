<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tournament;
use App\Models\Round;
use App\Models\GameMatch;
use App\Models\PlayerScore;
use Carbon\Carbon;

class TournamentMatchesSeeder extends Seeder
{
    public function run()
    {
        $tournaments = Tournament::with('players', 'courts')->get();

        foreach ($tournaments as $tournament) {
            $this->generateMatchesForTournament($tournament);
        }
    }

    private function generateMatchesForTournament($tournament)
    {
        $players = $tournament->players;
        $courts = $tournament->courts;
        $numberOfRounds = match($tournament->type) {
            'super_8_doubles' => 7,
            'super_8_fixed_pairs' => 7,
            default => 11
        };

        for ($roundNumber = 1; $roundNumber <= $numberOfRounds; $roundNumber++) {
            $round = Round::create([
                'tournament_id' => $tournament->id,
                'round_number' => $roundNumber
            ]);

            // Embaralha os jogadores para criar partidas aleatórias
            $shuffledPlayers = $players->shuffle();

            if ($tournament->type === 'super_8_doubles') {
                // Cria 2 partidas por rodada (4 jogadores por partida)
                for ($i = 0; $i < 8; $i += 4) {
                    $match = GameMatch::create([
                        'round_id' => $round->id,
                        'court_id' => $courts->random()->id,
                        'team1_player1_id' => $shuffledPlayers[$i]->id,
                        'team1_player2_id' => $shuffledPlayers[$i + 1]->id,
                        'team2_player1_id' => $shuffledPlayers[$i + 2]->id,
                        'team2_player2_id' => $shuffledPlayers[$i + 3]->id,
                        'status' => 'completed',
                        'scheduled_time' => Carbon::now()->addHours($roundNumber)
                    ]);

                    // Gera um placar aleatório
                    $team1Score = rand(4, 6);
                    $team2Score = $team1Score === 6 ? rand(0, 4) : 6;
                    $winner = $team1Score > $team2Score ? 'team1' : 'team2';

                    $match->update([
                        'score_details' => $team1Score . ' - ' . $team2Score,
                        'winner_team' => $winner
                    ]);
                }
            } elseif ($tournament->type === 'super_8_fixed_pairs') {
                // Cria 4 partidas por rodada (8 duplas = 16 jogadores)
                for ($i = 0; $i < 16; $i += 4) {
                    $match = GameMatch::create([
                        'round_id' => $round->id,
                        'court_id' => $courts->random()->id,
                        'team1_player1_id' => $shuffledPlayers[$i]->id,
                        'team1_player2_id' => $shuffledPlayers[$i + 1]->id,
                        'team2_player1_id' => $shuffledPlayers[$i + 2]->id,
                        'team2_player2_id' => $shuffledPlayers[$i + 3]->id,
                        'status' => 'completed',
                        'scheduled_time' => Carbon::now()->addHours($roundNumber)
                    ]);

                    // Gera um placar aleatório
                    $team1Score = rand(4, 6);
                    $team2Score = $team1Score === 6 ? rand(0, 4) : 6;
                    $winner = $team1Score > $team2Score ? 'team1' : 'team2';

                    $match->update([
                        'score_details' => $team1Score . ' - ' . $team2Score,
                        'winner_team' => $winner
                    ]);

                    // Atualiza as pontuações dos jogadores
                    $this->updatePlayerScores($match, $tournament, $winner);
                }
            }
            // Adicione lógica similar para Super 12 se necessário
        }
    }

    private function updatePlayerScores($match, $tournament, $winner)
    {
        $winnerPoints = 3;
        $loserPoints = 1;

        if ($winner === 'team1') {
            $this->updateScore($match->team1_player1, $tournament, true);
            $this->updateScore($match->team1_player2, $tournament, true);
            $this->updateScore($match->team2_player1, $tournament, false);
            $this->updateScore($match->team2_player2, $tournament, false);
        } else {
            $this->updateScore($match->team2_player1, $tournament, true);
            $this->updateScore($match->team2_player2, $tournament, true);
            $this->updateScore($match->team1_player1, $tournament, false);
            $this->updateScore($match->team1_player2, $tournament, false);
        }
    }

    private function updateScore($player, $tournament, $isWinner)
    {
        $playerScore = PlayerScore::firstOrCreate(
            [
                'tournament_id' => $tournament->id,
                'player_id' => $player->id
            ],
            [
                'points' => 0,
                'games_won' => 0,
                'games_lost' => 0
            ]
        );

        if ($isWinner) {
            $playerScore->increment('points', 3);
            $playerScore->increment('games_won');
        } else {
            $playerScore->increment('points', 1);
            $playerScore->increment('games_lost');
        }
    }
}
