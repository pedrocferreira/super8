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
     * Lista de temporadas para seleção pública
     */
    public function index()
    {
        $seasons = Season::orderBy('name', 'desc')->get();
        return view('public.rankings.index', compact('seasons'));
    }
}