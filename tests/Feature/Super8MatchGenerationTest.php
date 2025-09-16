<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Tournament;
use App\Models\Player;
use App\Models\User;

class Super8MatchGenerationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_generates_matches_for_a_super_8_tournament_with_correct_pairing_logic()
    {
        // 1. Arrange
        $user = User::factory()->create();
        $this->actingAs($user);

        $tournament = Tournament::factory()->create(['type' => 'super_8_doubles', 'status' => 'open']);
        $players = Player::factory()->count(8)->create();
        $tournament->players()->attach($players->pluck('id'));
        $tournament->courts()->create(['name' => 'Court 1', 'is_active' => true]);
        $tournament->courts()->create(['name' => 'Court 2', 'is_active' => true]);

        // 2. Act
        $response = $this->post(route('tournaments.generate-matches', $tournament));

        // 3. Assert
        $response->assertRedirect(route('tournaments.show', $tournament));
        $this->assertDatabaseCount('matches', 14);

        $matches = $tournament->matches()->get();
        
        // Verificar se cada jogador joga 7 partidas
        foreach ($players as $player) {
            $playerMatchesCount = $matches->filter(function ($match) use ($player) {
                return in_array($player->id, [
                    $match->team1_player1_id, $match->team1_player2_id,
                    $match->team2_player1_id, $match->team2_player2_id
                ]);
            })->count();
            $this->assertEquals(7, $playerMatchesCount, "Player {$player->id} should have 7 matches.");
        }

        // Verificar se cada jogador joga com cada parceiro diferente uma única vez
        foreach ($players as $player) {
            $partners = [];
            $opponents = [];
            $player_matches = $matches->filter(function ($match) use ($player) {
                return in_array($player->id, [
                    $match->team1_player1_id, $match->team1_player2_id,
                    $match->team2_player1_id, $match->team2_player2_id
                ]);
            });

            foreach ($player_matches as $match) {
                if ($match->team1_player1_id == $player->id) {
                    $partners[] = $match->team1_player2_id;
                    $opponents[] = $match->team2_player1_id;
                    $opponents[] = $match->team2_player2_id;
                } elseif ($match->team1_player2_id == $player->id) {
                    $partners[] = $match->team1_player1_id;
                    $opponents[] = $match->team2_player1_id;
                    $opponents[] = $match->team2_player2_id;
                } elseif ($match->team2_player1_id == $player->id) {
                    $partners[] = $match->team2_player2_id;
                    $opponents[] = $match->team1_player1_id;
                    $opponents[] = $match->team1_player2_id;
                } else { // team2_player2_id
                    $partners[] = $match->team2_player1_id;
                    $opponents[] = $match->team1_player1_id;
                    $opponents[] = $match->team1_player2_id;
                }
            }

            $this->assertCount(7, $partners, "Player {$player->id} should have 7 partners in total.");
            $this->assertCount(7, array_unique($partners), "Player {$player->id} should have 7 unique partners.");
            
            // Para um Super 8, verificar que cada jogador enfrenta múltiplos adversários únicos
            $unique_opponents = array_unique($opponents);
            $this->assertGreaterThanOrEqual(4, count($unique_opponents), "Player {$player->id} should face multiple unique opponents.");
        }

        // Verificar se nenhuma dupla se repete
        $all_pairs = [];
        foreach ($matches as $match) {
            $team1_pair = [$match->team1_player1_id, $match->team1_player2_id];
            $team2_pair = [$match->team2_player1_id, $match->team2_player2_id];
            
            sort($team1_pair);
            sort($team2_pair);
            
            $all_pairs[] = implode('-', $team1_pair);
            $all_pairs[] = implode('-', $team2_pair);
        }
        
        $unique_pairs = array_unique($all_pairs);
        $this->assertCount(count($all_pairs), $unique_pairs, "No duplicate pairs should exist in the tournament.");
    }
}
