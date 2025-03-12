<?php

namespace App\Http\Controllers;

use App\Models\Tournament;
use App\Models\Player;
use App\Models\PlayerScore;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $tournaments = Tournament::latest()->take(5)->get();

        // Busca de jogadores com filtro
        $search = $request->input('search');
        $players = Player::when($search, function($query) use ($search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
        })->latest()->take(5)->get();

        // Ranking geral
        $playerRanking = Player::select(
            'players.id',
            'players.name',
            'players.email',
            DB::raw('SUM(player_scores.points) as total_points'),
            DB::raw('SUM(player_scores.games_won) as total_wins'),
            DB::raw('SUM(player_scores.games_lost) as total_losses'),
            DB::raw('COUNT(DISTINCT player_scores.tournament_id) as tournaments_played')
        )
        ->leftJoin('player_scores', 'players.id', '=', 'player_scores.player_id')
        ->groupBy('players.id', 'players.name', 'players.email')
        ->orderBy('total_points', 'desc')
        ->orderBy('total_wins', 'desc')
        ->orderBy('total_losses', 'asc')
        ->take(10)
        ->get();

        return view('dashboard', compact('tournaments', 'players', 'playerRanking', 'search'));
    }
}
