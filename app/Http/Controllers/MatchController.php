<?php

namespace App\Http\Controllers;

use App\Models\GameMatch;
use Illuminate\Http\Request;

class MatchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function updateScore(Request $request, GameMatch $match)
    {
        $validated = $request->validate([
            'team1_score' => 'required|integer|min:0',
            'team2_score' => 'required|integer|min:0',
        ]);

        // Determina o vencedor
        $winner = $validated['team1_score'] > $validated['team2_score'] ? 'team1' : 'team2';

        // Carrega o torneio com a partida
        $match->load('round.tournament');
        $tournament = $match->round->tournament;

        // Atualiza a partida
        $match->update([
            'score_details' => $validated['team1_score'] . ' - ' . $validated['team2_score'],
            'winner_team' => $winner,
            'status' => 'completed'
        ]);

        // Atualiza as pontuações dos jogadores
        if ($tournament && ($tournament->type === 'super_8_doubles' || $tournament->type === 'super_8_fixed_pairs')) {
            // Atualiza pontuação para time vencedor
            if ($winner === 'team1') {
                $this->updatePlayerScore($match->team1_player1, $tournament, true);
                $this->updatePlayerScore($match->team1_player2, $tournament, true);
                $this->updatePlayerScore($match->team2_player1, $tournament, false);
                $this->updatePlayerScore($match->team2_player2, $tournament, false);
            } else {
                $this->updatePlayerScore($match->team2_player1, $tournament, true);
                $this->updatePlayerScore($match->team2_player2, $tournament, true);
                $this->updatePlayerScore($match->team1_player1, $tournament, false);
                $this->updatePlayerScore($match->team1_player2, $tournament, false);
            }
        }

        return back()->with('success', 'Placar registrado com sucesso!');
    }

    private function updatePlayerScore($player, $tournament, $isWinner)
    {
        $playerScore = $tournament->playerScores()->firstOrCreate(
            ['player_id' => $player->id],
            ['points' => 0, 'games_won' => 0, 'games_lost' => 0]
        );

        if ($isWinner) {
            $playerScore->increment('points', 3); // 3 pontos por vitória
            $playerScore->increment('games_won');
        } else {
            $playerScore->increment('points', 1); // 1 ponto por derrota
            $playerScore->increment('games_lost');
        }
    }
}
