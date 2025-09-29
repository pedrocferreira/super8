<?php

namespace App\Http\Controllers;

use App\Models\Player;
use App\Models\Season;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeControllerBackup extends Controller
{
    /**
     * Página inicial com ranking público (versão simples)
     */
    public function index(Request $request)
    {
        try {
            // Query simples para ranking geral
            $query = Player::select(
                'players.id',
                'players.name',
                'players.email',
                'players.gender',
                'players.category',
                DB::raw('COALESCE(SUM(player_scores.points), 0) as total_points'),
                DB::raw('COALESCE(SUM(player_scores.games_won), 0) as total_wins'),
                DB::raw('COALESCE(SUM(player_scores.games_lost), 0) as total_losses'),
                DB::raw('COUNT(DISTINCT player_scores.tournament_id) as tournaments_played')
            )
            ->leftJoin('player_scores', 'players.id', '=', 'player_scores.player_id')
            ->groupBy('players.id', 'players.name', 'players.email', 'players.gender', 'players.category')
            ->orderBy('total_points', 'desc')
            ->orderBy('total_wins', 'desc')
            ->orderBy('total_losses', 'asc');

            $playerRanking = $query->get();

            // Obter temporadas ativas
            $activeSeasons = Season::where('status', 'active')->get();
            
            // Estatísticas gerais do sistema
            $systemStats = [
                'total_players' => Player::count(),
                'total_seasons' => Season::count(),
                'active_seasons' => $activeSeasons->count(),
                'total_tournaments' => DB::table('tournaments')->count(),
                'total_matches' => DB::table('matches')->count(),
                'balance_active_seasons' => 0,
            ];

            // Dados para o ranking público
            $balanceData = [];
            $playerBalanceMultipliers = [];
            $activeSeason = $activeSeasons->first();
            
            return view('welcome', compact('playerRanking', 'balanceData', 'systemStats', 'activeSeasons', 'playerBalanceMultipliers', 'activeSeason'));
            
        } catch (\Exception $e) {
            // Em caso de erro, mostrar página vazia
            $playerRanking = collect();
            $balanceData = [];
            $playerBalanceMultipliers = [];
            $activeSeasons = collect();
            $activeSeason = null;
            
            $systemStats = [
                'total_players' => 0,
                'total_seasons' => 0,
                'active_seasons' => 0,
                'total_tournaments' => 0,
                'total_matches' => 0,
                'balance_active_seasons' => 0,
            ];
            
            return view('welcome', compact('playerRanking', 'balanceData', 'systemStats', 'activeSeasons', 'playerBalanceMultipliers', 'activeSeason'));
        }
    }
}
