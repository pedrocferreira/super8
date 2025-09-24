<?php

namespace App\Http\Controllers;

use App\Models\Player;
use App\Models\Season;
use App\Services\RankingBalanceService;
use Illuminate\Http\Request;

class PlayerController extends Controller
{
    private RankingBalanceService $balanceService;

    public function __construct(RankingBalanceService $balanceService)
    {
        $this->balanceService = $balanceService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $seasonId = $request->input('season_id');

        $query = Player::query();

        // Filtro por busca
        if ($search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
        }

        // Filtro por temporada - mostra apenas jogadores que participaram de torneios na temporada
        if ($seasonId) {
            $query->whereHas('playerScores.tournament', function($q) use ($seasonId) {
                $q->where('season_id', $seasonId);
            });
        }

        $players = $query->latest()->paginate(10);
        $seasons = Season::orderBy('name', 'desc')->get();

        return view('players.index', compact('players', 'search', 'seasonId', 'seasons'));
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
    public function show(Player $player, Request $request)
    {
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
            $evolutionStats = $this->calculateEvolutionEvolutionStats($player, $selectedSeasonIds, $seasons);
        } else {
            // Dados combinados (padrão)
        $stats = [
                'total_points' => $playerScores->sum('points'),
                'total_wins' => $playerScores->sum('games_won'),
                'total_losses' => $playerScores->sum('games_lost'),
                'tournaments_played' => $playerScores->count(),
                'win_rate' => $playerScores->sum('games_won') + $playerScores->sum('games_lost') > 0
                    ? round(($playerScores->sum('games_won') / ($playerScores->sum('games_won') + $playerScores->sum('games_lost'))) * 100, 1)
                    : 0
            ];

            $courtStats = [
                'best_court' => $player->getBestCourt($selectedSeasonIds),
                'worst_court' => $player->getWorstCourt($selectedSeasonIds),
            ];

            $partnershipStats = [
                'best_partner' => $player->getBestPartner($selectedSeasonIds),
                'worst_partner' => $player->getWorstPartner($selectedSeasonIds),
                'compatibility' => $player->getPartnershipCompatibility($selectedSeasonIds),
                'different_partners_count' => $player->getDifferentPartnersCount($selectedSeasonIds),
                'most_frequent_partner' => $player->getMostFrequentPartner($selectedSeasonIds),
            ];

            $headToHeadStats = [
                'toughest_opponent' => $player->getToughestOpponent($selectedSeasonIds),
                'favorite_victim' => $player->getFavoriteVictim($selectedSeasonIds),
                'rivalries' => $player->getRivalries($selectedSeasonIds),
            ];

            $evolutionStats = [
                'monthly_performance' => $player->getMonthlyPerformance($selectedSeasonIds),
                'seasonal_performance' => $player->getSeasonalPerformance(),
                'performance_trend' => $player->getPerformanceTrend($selectedSeasonIds),
                'consistency' => $player->getConsistency($selectedSeasonIds),
            ];
        }

        // Adicionar dados de balanceamento para cada temporada selecionada
        $balanceData = [];
        foreach ($selectedSeasonIds as $seasonId) {
            $balanceData[$seasonId] = $this->balanceService->getPlayerBalanceInfo($player->id, $seasonId);
        }

        return view('players.show', compact(
            'player', 
            'stats', 
            'courtStats', 
            'partnershipStats', 
            'headToHeadStats', 
            'evolutionStats',
            'seasons',
            'selectedSeasons',
            'analysisType',
            'balanceData'
        ));
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

    /**
     * Calcula estatísticas de comparação entre temporadas
     */
    private function calculateComparisonStats($player, $selectedSeasonIds, $seasons)
    {
        $comparisonData = [];
        
        foreach ($selectedSeasonIds as $seasonId) {
            $season = $seasons->find($seasonId);
            if (!$season) continue;
            
            $playerScores = $player->playerScores->filter(function($score) use ($seasonId) {
                return $score->tournament && $score->tournament->season_id == $seasonId;
            });
            
            $comparisonData[$seasonId] = [
                'season_name' => $season->name,
                'total_points' => $playerScores->sum('points'),
                'total_wins' => $playerScores->sum('games_won'),
                'total_losses' => $playerScores->sum('games_lost'),
                'tournaments_played' => $playerScores->count(),
                'win_rate' => $playerScores->sum('games_won') + $playerScores->sum('games_lost') > 0
                    ? round(($playerScores->sum('games_won') / ($playerScores->sum('games_won') + $playerScores->sum('games_lost'))) * 100, 1)
                    : 0
            ];
        }
        
        return $comparisonData;
    }

    /**
     * Calcula estatísticas de quadras para comparação
     */
    private function calculateComparisonCourtStats($player, $selectedSeasonIds, $seasons)
    {
        $comparisonData = [];
        
        foreach ($selectedSeasonIds as $seasonId) {
            $season = $seasons->find($seasonId);
            if (!$season) continue;
            
            $comparisonData[$seasonId] = [
                'season_name' => $season->name,
                'best_court' => $player->getBestCourt([$seasonId]),
                'worst_court' => $player->getWorstCourt([$seasonId]),
            ];
        }
        
        return $comparisonData;
    }

    /**
     * Calcula estatísticas de parcerias para comparação
     */
    private function calculateComparisonPartnershipStats($player, $selectedSeasonIds, $seasons)
    {
        $comparisonData = [];
        
        foreach ($selectedSeasonIds as $seasonId) {
            $season = $seasons->find($seasonId);
            if (!$season) continue;
            
            $comparisonData[$seasonId] = [
                'season_name' => $season->name,
                'best_partner' => $player->getBestPartner([$seasonId]),
                'worst_partner' => $player->getWorstPartner([$seasonId]),
                'compatibility' => $player->getPartnershipCompatibility([$seasonId]),
                'different_partners_count' => $player->getDifferentPartnersCount([$seasonId]),
                'most_frequent_partner' => $player->getMostFrequentPartner([$seasonId]),
            ];
        }
        
        return $comparisonData;
    }

    /**
     * Calcula estatísticas de confrontos diretos para comparação
     */
    private function calculateComparisonHeadToHeadStats($player, $selectedSeasonIds, $seasons)
    {
        $comparisonData = [];
        
        foreach ($selectedSeasonIds as $seasonId) {
            $season = $seasons->find($seasonId);
            if (!$season) continue;
            
            $comparisonData[$seasonId] = [
                'season_name' => $season->name,
                'toughest_opponent' => $player->getToughestOpponent([$seasonId]),
                'favorite_victim' => $player->getFavoriteVictim([$seasonId]),
                'rivalries' => $player->getRivalries([$seasonId]),
            ];
        }
        
        return $comparisonData;
    }

    /**
     * Calcula estatísticas de evolução para comparação
     */
    private function calculateComparisonEvolutionStats($player, $selectedSeasonIds, $seasons)
    {
        $comparisonData = [];
        
        foreach ($selectedSeasonIds as $seasonId) {
            $season = $seasons->find($seasonId);
            if (!$season) continue;
            
            $comparisonData[$seasonId] = [
                'season_name' => $season->name,
                'monthly_performance' => $player->getMonthlyPerformance([$seasonId]),
                'seasonal_performance' => $player->getSeasonalPerformance()->where('season_id', $seasonId),
                'performance_trend' => $player->getPerformanceTrend([$seasonId]),
                'consistency' => $player->getConsistency([$seasonId]),
            ];
        }
        
        return $comparisonData;
    }

    /**
     * Calcula estatísticas de evolução temporal
     */
    private function calculateEvolutionStats($player, $selectedSeasonIds, $seasons)
    {
        // Para evolução, mostra dados mensais ao longo do tempo
        return [
            'monthly_data' => $player->getMonthlyPerformance($selectedSeasonIds),
            'seasonal_data' => $player->getSeasonalPerformance(),
            'trend' => $player->getPerformanceTrend($selectedSeasonIds),
            'consistency' => $player->getConsistency($selectedSeasonIds),
        ];
    }

    /**
     * Métodos auxiliares para evolução (simplificados)
     */
    private function calculateEvolutionCourtStats($player, $selectedSeasonIds, $seasons)
    {
        return [
            'best_court' => $player->getBestCourt($selectedSeasonIds),
            'worst_court' => $player->getWorstCourt($selectedSeasonIds),
        ];
    }

    private function calculateEvolutionPartnershipStats($player, $selectedSeasonIds, $seasons)
    {
        return [
            'best_partner' => $player->getBestPartner($selectedSeasonIds),
            'worst_partner' => $player->getWorstPartner($selectedSeasonIds),
            'compatibility' => $player->getPartnershipCompatibility($selectedSeasonIds),
            'different_partners_count' => $player->getDifferentPartnersCount($selectedSeasonIds),
            'most_frequent_partner' => $player->getMostFrequentPartner($selectedSeasonIds),
        ];
    }

    private function calculateEvolutionHeadToHeadStats($player, $selectedSeasonIds, $seasons)
    {
        return [
            'toughest_opponent' => $player->getToughestOpponent($selectedSeasonIds),
            'favorite_victim' => $player->getFavoriteVictim($selectedSeasonIds),
            'rivalries' => $player->getRivalries($selectedSeasonIds),
        ];
    }

    private function calculateEvolutionEvolutionStats($player, $selectedSeasonIds, $seasons)
    {
        return [
            'monthly_performance' => $player->getMonthlyPerformance($selectedSeasonIds),
            'seasonal_performance' => $player->getSeasonalPerformance(),
            'performance_trend' => $player->getPerformanceTrend($selectedSeasonIds),
            'consistency' => $player->getConsistency($selectedSeasonIds),
        ];
    }
}
