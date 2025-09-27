<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Season;
use App\Models\Player;
use App\Services\RankingBalanceService;

class PublicRankingController extends Controller
{
    private RankingBalanceService $balanceService;

    public function __construct(RankingBalanceService $balanceService)
    {
        $this->balanceService = $balanceService;
    }

    /**
     * Mostra o ranking público de uma temporada
     */
    public function season(Request $request, $seasonId)
    {
        $season = Season::findOrFail($seasonId);
        $ranking = $this->balanceService->calculateSeasonRanking($seasonId);
        $balanceStats = $this->balanceService->getBalanceStatistics($seasonId);
        
        // Aplicar filtros se necessário
        $search = $request->get('search');
        if ($search) {
            $ranking = $ranking->filter(function ($player) use ($search) {
                return stripos($player['player_name'], $search) !== false;
            });
        }

        return view('public.rankings.season', compact('season', 'ranking', 'balanceStats'));
    }

    /**
     * Mostra informações de balanceamento de um jogador (público)
     */
    public function playerBalance(Request $request, $playerId, $seasonId)
    {
        $player = Player::findOrFail($playerId);
        $season = Season::findOrFail($seasonId);
        
        $balanceInfo = $this->balanceService->getPlayerBalanceInfo($playerId, $seasonId);
        $ranking = $this->balanceService->calculateSeasonRanking($seasonId);
        
        // Encontrar posição do jogador no ranking
        $playerPosition = $ranking->search(function ($p) use ($playerId) {
            return $p['player_id'] == $playerId;
        });
        
        $playerRanking = $playerPosition !== false ? $ranking[$playerPosition] : null;

        return view('public.rankings.player-balance', compact('player', 'season', 'balanceInfo', 'playerRanking'));
    }

    /**
     * Mostra estatísticas gerais do sistema de balanceamento (público)
     */
    public function statistics()
    {
        $seasons = Season::all();
        $seasonsWithStats = [];
        
        foreach ($seasons as $season) {
            $stats = $this->balanceService->getBalanceStatistics($season->id);
            $seasonsWithStats[] = [
                'season' => $season,
                'stats' => $stats
            ];
        }

        return view('public.rankings.statistics', compact('seasonsWithStats'));
    }

    /**
     * Mostra estatísticas públicas de um jogador
     */
    public function playerStats(Request $request, $playerId)
    {
        $player = Player::findOrFail($playerId);
        $selectedSeasons = $request->input('seasons', ['all']);
        $analysisType = $request->input('analysis_type', 'combined');
        
        // Carrega os dados do jogador com seus scores
        $player->load([
            'playerScores.tournament',
        ]);

        $seasons = Season::orderBy('name', 'desc')->get();

        // Se "all" está selecionado ou nenhuma temporada específica, usa todas
        if (in_array('all', $selectedSeasons) || empty(array_filter($selectedSeasons))) {
            $selectedSeasonIds = $seasons->pluck('id')->toArray();
        } else {
            $selectedSeasonIds = array_filter($selectedSeasons, function($id) {
                return $id !== 'all';
            });
        }

        // Filtra scores por temporadas selecionadas
        $playerScores = $player->playerScores->filter(function($score) use ($selectedSeasonIds) {
            return $score->tournament && in_array($score->tournament->season_id, $selectedSeasonIds);
        });

        // Calcula estatísticas baseadas no tipo de análise
        if ($analysisType === 'comparison') {
            $stats = $this->calculateComparisonStats($player, $selectedSeasonIds, $seasons);
            $courtStats = $this->calculateComparisonCourtStats($player, $selectedSeasonIds, $seasons);
            $partnershipStats = $this->calculateComparisonPartnershipStats($player, $selectedSeasonIds, $seasons);
            $headToHeadStats = $this->calculateComparisonHeadToHeadStats($player, $selectedSeasonIds, $seasons);
            $evolutionStats = $this->calculateComparisonEvolutionStats($player, $selectedSeasonIds, $seasons);
        } elseif ($analysisType === 'evolution') {
            $stats = $this->calculateEvolutionStats($player, $selectedSeasonIds, $seasons);
            $courtStats = $this->calculateEvolutionCourtStats($player, $selectedSeasonIds, $seasons);
            $partnershipStats = $this->calculateEvolutionPartnershipStats($player, $selectedSeasonIds, $seasons);
            $headToHeadStats = $this->calculateEvolutionHeadToHeadStats($player, $selectedSeasonIds, $seasons);
            $evolutionStats = $this->calculateEvolutionStats($player, $selectedSeasonIds, $seasons);
        } else {
            // Visualização Padrão (Dados Combinados)
            $stats = $this->calculateCombinedStats($player, $selectedSeasonIds);
            $courtStats = $this->calculateCombinedCourtStats($player, $selectedSeasonIds);
            $partnershipStats = $this->calculateCombinedPartnershipStats($player, $selectedSeasonIds);
            $headToHeadStats = $this->calculateCombinedHeadToHeadStats($player, $selectedSeasonIds);
            $evolutionStats = $this->calculateCombinedEvolutionStats($player, $selectedSeasonIds);
        }

        return view('public.players.stats', compact(
            'player', 
            'seasons', 
            'selectedSeasons', 
            'analysisType', 
            'stats', 
            'courtStats', 
            'partnershipStats', 
            'headToHeadStats', 
            'evolutionStats'
        ));
    }

    /**
     * Lista de temporadas para seleção pública
     */
    public function index()
    {
        $seasons = Season::orderBy('name', 'desc')->get();
        return view('public.rankings.index', compact('seasons'));
    }

    // Métodos auxiliares para cálculo de estatísticas (copiados do PlayerController)
    private function calculateCombinedStats($player, $selectedSeasonIds)
    {
        // Filtrar scores por temporadas selecionadas
        $filteredScores = $player->playerScores->filter(function($score) use ($selectedSeasonIds) {
            return $score->tournament && in_array($score->tournament->season_id, $selectedSeasonIds);
        });

        $totalPoints = $filteredScores->sum('points');
        $totalWins = $filteredScores->sum('games_won');
        $totalLosses = $filteredScores->sum('games_lost');
        $tournamentsPlayed = $filteredScores->count();
        
        $winRate = ($totalWins + $totalLosses) > 0 ? round(($totalWins / ($totalWins + $totalLosses)) * 100, 1) : 0;

        return [
            'total_points' => $totalPoints,
            'total_wins' => $totalWins,
            'total_losses' => $totalLosses,
            'tournaments_played' => $tournamentsPlayed,
            'win_rate' => $winRate
        ];
    }

    private function calculateCombinedCourtStats($player, $selectedSeasonIds)
    {
        return [
            'best_court' => $player->getBestCourt(),
            'worst_court' => $player->getWorstCourt()
        ];
    }

    private function calculateCombinedPartnershipStats($player, $selectedSeasonIds)
    {
        return [
            'best_partner' => $player->getBestPartner(),
            'worst_partner' => $player->getWorstPartner(),
            'most_frequent_partner' => $player->getMostFrequentPartner(),
            'different_partners_count' => $player->getDifferentPartnersCount()
        ];
    }

    private function calculateCombinedHeadToHeadStats($player, $selectedSeasonIds)
    {
        return [
            'favorite_victim' => $player->getFavoriteVictim(),
            'toughest_opponent' => $player->getToughestOpponent(),
            'rivalries' => $player->getRivalries()
        ];
    }

    private function calculateCombinedEvolutionStats($player, $selectedSeasonIds)
    {
        return [
            'monthly_performance' => $player->getMonthlyPerformance(),
            'seasonal_performance' => $player->getSeasonalPerformance(),
            'performance_trend' => $player->getPerformanceTrend(),
            'consistency' => $player->getConsistency()
        ];
    }

    // Métodos placeholder para outros tipos de análise
    private function calculateComparisonStats($player, $selectedSeasonIds, $seasons) { return []; }
    private function calculateComparisonCourtStats($player, $selectedSeasonIds, $seasons) { return []; }
    private function calculateComparisonPartnershipStats($player, $selectedSeasonIds, $seasons) { return []; }
    private function calculateComparisonHeadToHeadStats($player, $selectedSeasonIds, $seasons) { return []; }
    private function calculateComparisonEvolutionStats($player, $selectedSeasonIds, $seasons) { return []; }
    private function calculateEvolutionStats($player, $selectedSeasonIds, $seasons) { return []; }
    private function calculateEvolutionCourtStats($player, $selectedSeasonIds, $seasons) { return []; }
    private function calculateEvolutionPartnershipStats($player, $selectedSeasonIds, $seasons) { return []; }
    private function calculateEvolutionHeadToHeadStats($player, $selectedSeasonIds, $seasons) { return []; }
}