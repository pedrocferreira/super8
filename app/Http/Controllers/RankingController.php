<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Season;
use App\Models\Player;
use App\Services\RankingBalanceService;

class RankingController extends Controller
{
    private RankingBalanceService $balanceService;

    public function __construct(RankingBalanceService $balanceService)
    {
        $this->balanceService = $balanceService;
    }

    /**
     * Mostra o ranking de uma temporada
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

        return view('rankings.season', compact('season', 'ranking', 'balanceStats'));
    }

    /**
     * Mostra informações de balanceamento de um jogador
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

        return view('rankings.player-balance', compact('player', 'season', 'balanceInfo', 'playerRanking'));
    }

    /**
     * Aplica balanceamento a uma temporada
     */
    public function applyBalance(Request $request, $seasonId)
    {
        $season = Season::findOrFail($seasonId);
        
        try {
            $this->balanceService->applyBalanceToSeason($seasonId);
            
            return redirect()->back()->with('success', 
                "Sistema de balanceamento aplicado com sucesso à temporada {$season->name}!"
            );
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 
                "Erro ao aplicar balanceamento: " . $e->getMessage()
            );
        }
    }

    /**
     * Mostra estatísticas gerais do sistema de balanceamento
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

        return view('rankings.statistics', compact('seasonsWithStats'));
    }
}