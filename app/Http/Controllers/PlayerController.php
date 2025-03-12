<?php

namespace App\Http\Controllers;

use App\Models\Player;
use Illuminate\Http\Request;

class PlayerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $players = Player::when($search, function($query) use ($search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
        })->latest()->paginate(10);

        return view('players.index', compact('players', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('players.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:players',
            'phone' => 'nullable|string|max:20'
        ]);

        Player::create($validated);
        return redirect()->route('players.index')->with('success', 'Jogador cadastrado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Player $player)
    {
        // Carrega os dados do jogador com seus scores
        $player->load([
            'playerScores.tournament',  // Alterado de scores para playerScores
        ]);

        // Calcula estatísticas gerais
        $stats = [
            'total_points' => $player->playerScores->sum('points'),
            'total_wins' => $player->playerScores->sum('games_won'),
            'total_losses' => $player->playerScores->sum('games_lost'),
            'tournaments_played' => $player->playerScores->count(),
            'win_rate' => $player->playerScores->sum('games_won') + $player->playerScores->sum('games_lost') > 0
                ? round(($player->playerScores->sum('games_won') / ($player->playerScores->sum('games_won') + $player->playerScores->sum('games_lost'))) * 100, 1)
                : 0
        ];

        return view('players.show', compact('player', 'stats'));
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
}
