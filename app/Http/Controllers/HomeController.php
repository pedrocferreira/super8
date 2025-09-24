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
     * Página inicial com ranking geral e dados de balanceamento
     */
    public function index()
    {
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
        ->get();

        // Obter temporadas ativas para balanceamento
        $activeSeasons = Season::where('status', 'active')->get();
        
        // Dados de balanceamento para cada temporada ativa
        $balanceData = [];
        $playerBalanceMultipliers = [];
        
        foreach ($activeSeasons as $season) {
            $balanceData[$season->id] = [
                'season' => $season,
                'stats' => $this->balanceService->getBalanceStatistics($season->id),
                'ranking' => $this->balanceService->calculateSeasonRanking($season->id)
            ];
        }

        // Calcular multiplicadores médios para cada jogador
        foreach ($playerRanking as $player) {
            $totalMultiplier = 0;
            $activeSeasonsCount = 0;
            
            foreach($balanceData as $seasonId => $data) {
                if($data['stats']['balance_active']) {
                    $playerBalance = $this->balanceService->getPlayerBalanceInfo($player->id, $seasonId);
                    if($playerBalance['position']) {
                        $totalMultiplier += $playerBalance['multiplier'];
                        $activeSeasonsCount++;
                    }
                }
            }
            
            $playerBalanceMultipliers[$player->id] = [
                'multiplier' => $activeSeasonsCount > 0 ? round($totalMultiplier / $activeSeasonsCount, 1) : 1.0,
                'seasons_count' => $activeSeasonsCount
            ];
        }

        // Contar temporadas com balanceamento ativo
        $balanceActiveSeasons = 0;
        foreach ($balanceData as $data) {
            if ($data['stats']['balance_active']) {
                $balanceActiveSeasons++;
            }
        }

        // Estatísticas gerais do sistema
        $systemStats = [
            'total_players' => $playerRanking->count(),
            'total_seasons' => Season::count(),
            'active_seasons' => $activeSeasons->count(),
            'balance_active_seasons' => $balanceActiveSeasons,
            'total_tournaments' => DB::table('tournaments')->count(),
            'total_matches' => DB::table('matches')->count(),
        ];

        return view('welcome', compact('playerRanking', 'balanceData', 'systemStats', 'activeSeasons', 'playerBalanceMultipliers'));
    }
}