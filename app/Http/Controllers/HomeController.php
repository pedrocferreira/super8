<?php

namespace App\Http\Controllers;

use App\Models\Player;
use App\Models\Season;
use App\Services\RankingBalanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    private RankingBalanceService $balanceService;

    public function __construct(RankingBalanceService $balanceService)
    {
        $this->balanceService = $balanceService;
    }

    /**
     * Página inicial com ranking público
     */
    public function index(Request $request)
    {
        try {
            // Obter temporada ativa
            $activeSeason = Season::where('status', 'active')->first();
            
            if ($activeSeason) {
                // Se há temporada ativa, usar o mesmo cálculo do ranking público
                $ranking = $this->balanceService->calculateSeasonRanking($activeSeason->id);
                $balanceStats = $this->balanceService->getBalanceStatistics($activeSeason->id);
                
                // Converter para formato compatível com a view
                $playerRanking = collect($ranking)->map(function ($player) {
                    return (object) [
                        'id' => $player['player_id'] ?? 0,
                        'name' => $player['player_name'] ?? 'Jogador',
                        'email' => $player['player_email'] ?? '',
                        'gender' => $player['player_gender'] ?? null,
                        'category' => $player['player_category'] ?? null,
                        'total_points' => $player['total_points'] ?? 0,
                        'total_wins' => $player['total_wins'] ?? 0,
                        'total_losses' => $player['total_losses'] ?? 0,
                        'tournaments_played' => $player['tournaments_played'] ?? 0
                    ];
                });
            } else {
                // Se não há temporada ativa, usar ranking geral
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
                $balanceStats = ['balance_active' => false];
            }

            // Obter temporadas ativas
            $activeSeasons = Season::where('status', 'active')->get();
            
            // Estatísticas gerais do sistema
            $systemStats = [
                'total_players' => Player::count(),
                'total_seasons' => Season::count(),
                'active_seasons' => $activeSeasons->count(),
                'total_tournaments' => DB::table('tournaments')->count(),
                'total_matches' => DB::table('matches')->count(),
                'balance_active_seasons' => $activeSeason ? ($balanceStats['balance_active'] ? 1 : 0) : 0,
            ];

            // Dados para o ranking público
            $balanceData = [];
            $playerBalanceMultipliers = [];
            
            return view('welcome', compact('playerRanking', 'balanceData', 'systemStats', 'activeSeasons', 'playerBalanceMultipliers', 'activeSeason'));
            
        } catch (\Exception $e) {
            // Log do erro para debug
            \Log::error('Erro no HomeController: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
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