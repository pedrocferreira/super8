<?php

namespace App\Http\Controllers;

use App\Models\Player;
use App\Models\Season;
use App\Services\RankingBalanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function __construct()
    {
        // Construtor simplificado para evitar problemas de dependência
    }

    /**
     * Página inicial com ranking público
     */
    public function index(Request $request)
    {
        try {
            // Parâmetros de filtro
            $category = $request->get('category', 'all');
            $gender = $request->get('gender', 'all');
            $rankingType = $request->get('type', 'general');
            
            // Query base para ranking
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
            ->groupBy('players.id', 'players.name', 'players.email', 'players.gender', 'players.category');

            // Aplicar filtros
            if ($category !== 'all') {
                $query->where('players.category', $category);
            }
            
            if ($gender !== 'all') {
                $query->where('players.gender', $gender);
            }

            // Ordenação baseada no tipo de ranking
            if ($rankingType === 'category') {
                $query->orderBy('players.category', 'asc')
                      ->orderBy('total_points', 'desc')
                      ->orderBy('total_wins', 'desc');
            } else {
                $query->orderBy('total_points', 'desc')
                      ->orderBy('total_wins', 'desc')
                      ->orderBy('total_losses', 'asc');
            }

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
            ];

            // Dados para o ranking público
            $balanceData = [];
            $playerBalanceMultipliers = [];
            
            return view('simple-welcome', compact('playerRanking', 'balanceData', 'systemStats', 'activeSeasons', 'playerBalanceMultipliers', 'category', 'gender', 'rankingType'));
            
        } catch (\Exception $e) {
            // Em caso de erro, mostrar página vazia
            $playerRanking = collect();
            $balanceData = [];
            $playerBalanceMultipliers = [];
            $activeSeasons = collect();
            
            $systemStats = [
                'total_players' => 0,
                'total_seasons' => 0,
                'active_seasons' => 0,
                'total_tournaments' => 0,
                'total_matches' => 0,
            ];
            
            return view('simple-welcome', compact('playerRanking', 'balanceData', 'systemStats', 'activeSeasons', 'playerBalanceMultipliers'));
        }
    }
}