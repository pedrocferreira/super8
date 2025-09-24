<?php

namespace App\Services;

use App\Models\Player;
use App\Models\Tournament;
use App\Models\PlayerScore;
use App\Models\Season;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class RankingBalanceService
{
    /**
     * Configurações do sistema de balanceamento
     */
    private const CONFIG = [
        'base_points_win' => 3,      // Pontos base por vitória
        'base_points_loss' => 1,     // Pontos base por derrota
        'max_multiplier' => 2.0,     // Multiplicador máximo (200%)
        'min_multiplier' => 0.3,     // Multiplicador mínimo (30%)
        'balance_factor' => 0.1,     // Fator de balanceamento (10% por posição)
        'min_players_for_balance' => 4, // Mínimo de jogadores para ativar balanceamento
    ];

    /**
     * Calcula o ranking geral de uma temporada
     */
    public function calculateSeasonRanking($seasonId): Collection
    {
        return PlayerScore::join('tournaments', 'tournaments.id', '=', 'player_scores.tournament_id')
            ->where('tournaments.season_id', $seasonId)
            ->select(
                'player_scores.player_id',
                DB::raw('SUM(player_scores.points) as total_points'),
                DB::raw('SUM(player_scores.games_won) as total_wins'),
                DB::raw('SUM(player_scores.games_lost) as total_losses'),
                DB::raw('COUNT(DISTINCT player_scores.tournament_id) as tournaments_played')
            )
            ->groupBy('player_scores.player_id')
            ->orderBy('total_points', 'desc')
            ->get()
            ->map(function ($score) {
                $player = Player::find($score->player_id);
                return [
                    'player_id' => $score->player_id,
                    'player_name' => $player->name,
                    'total_points' => $score->total_points,
                    'total_wins' => $score->total_wins,
                    'total_losses' => $score->total_losses,
                    'tournaments_played' => $score->tournaments_played,
                    'win_rate' => $score->total_wins + $score->total_losses > 0 
                        ? round(($score->total_wins / ($score->total_wins + $score->total_losses)) * 100, 1)
                        : 0
                ];
            });
    }

    /**
     * Calcula o multiplicador de pontos baseado na posição no ranking
     */
    public function calculatePointsMultiplier($playerId, $seasonId, $tournamentId): float
    {
        $ranking = $this->calculateSeasonRanking($seasonId);
        $totalPlayers = $ranking->count();
        
        if ($totalPlayers < self::CONFIG['min_players_for_balance']) {
            return 1.0; // Sem balanceamento se poucos jogadores
        }

        // Encontrar posição do jogador no ranking
        $playerPosition = $ranking->search(function ($player) use ($playerId) {
            return $player['player_id'] == $playerId;
        });

        if ($playerPosition === false) {
            return 1.0; // Jogador não encontrado no ranking
        }

        $position = $playerPosition + 1; // Posição baseada em 1
        $percentage = $position / $totalPlayers; // Percentual da posição (0-1)

        // Calcular multiplicador baseado na posição
        // Jogadores no topo (posições baixas) ganham menos pontos
        // Jogadores no final (posições altas) ganham mais pontos
        $multiplier = self::CONFIG['min_multiplier'] + 
                     (self::CONFIG['max_multiplier'] - self::CONFIG['min_multiplier']) * $percentage;

        return round($multiplier, 2);
    }

    /**
     * Aplica o sistema de balanceamento a um torneio
     */
    public function applyBalanceToTournament($tournamentId): void
    {
        $tournament = Tournament::find($tournamentId);
        if (!$tournament || !$tournament->season_id) {
            return;
        }

        $seasonId = $tournament->season_id;
        $playerScores = PlayerScore::where('tournament_id', $tournamentId)->get();

        foreach ($playerScores as $playerScore) {
            $multiplier = $this->calculatePointsMultiplier(
                $playerScore->player_id, 
                $seasonId, 
                $tournamentId
            );

            // Recalcular pontos com o multiplicador
            $this->recalculatePlayerScoreWithBalance($playerScore, $multiplier);
        }
    }

    /**
     * Recalcula a pontuação de um jogador aplicando o balanceamento
     */
    private function recalculatePlayerScoreWithBalance(PlayerScore $playerScore, float $multiplier): void
    {
        // Obter estatísticas originais do torneio
        $originalStats = $this->getOriginalTournamentStats($playerScore);
        
        if (!$originalStats) {
            return;
        }

        // Calcular pontos balanceados
        $balancedPoints = $this->calculateBalancedPoints($originalStats, $multiplier);

        // Atualizar pontuação
        $playerScore->update([
            'points' => $balancedPoints,
            'games_won' => $originalStats['games_won'],
            'games_lost' => $originalStats['games_lost']
        ]);
    }

    /**
     * Obtém as estatísticas originais de um jogador em um torneio
     */
    private function getOriginalTournamentStats(PlayerScore $playerScore): ?array
    {
        $matches = DB::table('matches')
            ->join('rounds', 'rounds.id', '=', 'matches.round_id')
            ->where('rounds.tournament_id', $playerScore->tournament_id)
            ->where(function($query) use ($playerScore) {
                $query->where('matches.team1_player1_id', $playerScore->player_id)
                      ->orWhere('matches.team1_player2_id', $playerScore->player_id)
                      ->orWhere('matches.team2_player1_id', $playerScore->player_id)
                      ->orWhere('matches.team2_player2_id', $playerScore->player_id);
            })
            ->whereNotNull('matches.winner_team')
            ->get();

        if ($matches->isEmpty()) {
            return null;
        }

        $gamesWon = 0;
        $gamesLost = 0;

        foreach ($matches as $match) {
            $isTeam1 = in_array($playerScore->player_id, [
                $match->team1_player1_id, 
                $match->team1_player2_id
            ]);
            
            $won = ($isTeam1 && $match->winner_team === 'team1') || 
                   (!$isTeam1 && $match->winner_team === 'team2');
            
            if ($won) {
                $gamesWon++;
            } else {
                $gamesLost++;
            }
        }

        return [
            'games_won' => $gamesWon,
            'games_lost' => $gamesLost
        ];
    }

    /**
     * Calcula pontos balanceados baseado nas estatísticas originais
     */
    private function calculateBalancedPoints(array $stats, float $multiplier): int
    {
        $basePoints = ($stats['games_won'] * self::CONFIG['base_points_win']) + 
                     ($stats['games_lost'] * self::CONFIG['base_points_loss']);
        
        return round($basePoints * $multiplier);
    }

    /**
     * Obtém informações sobre o balanceamento para um jogador
     */
    public function getPlayerBalanceInfo($playerId, $seasonId): array
    {
        $ranking = $this->calculateSeasonRanking($seasonId);
        $totalPlayers = $ranking->count();
        
        $playerPosition = $ranking->search(function ($player) use ($playerId) {
            return $player['player_id'] == $playerId;
        });

        if ($playerPosition === false) {
            return [
                'position' => null,
                'total_players' => $totalPlayers,
                'multiplier' => 1.0,
                'balance_status' => 'not_ranked'
            ];
        }

        $position = $playerPosition + 1;
        $multiplier = $this->calculatePointsMultiplier($playerId, $seasonId, null);
        
        $balanceStatus = $this->getBalanceStatus($position, $totalPlayers, $multiplier);

        return [
            'position' => $position,
            'total_players' => $totalPlayers,
            'multiplier' => $multiplier,
            'balance_status' => $balanceStatus,
            'points_boost' => $multiplier > 1.0 ? round(($multiplier - 1) * 100, 1) : 0,
            'points_penalty' => $multiplier < 1.0 ? round((1 - $multiplier) * 100, 1) : 0
        ];
    }

    /**
     * Determina o status do balanceamento
     */
    private function getBalanceStatus(int $position, int $totalPlayers, float $multiplier): string
    {
        $percentage = $position / $totalPlayers;

        if ($percentage <= 0.2) {
            return 'top_player'; // Top 20% - ganha menos pontos
        } elseif ($percentage <= 0.4) {
            return 'high_tier'; // 20-40% - ganha pontos normais
        } elseif ($percentage <= 0.6) {
            return 'mid_tier'; // 40-60% - ganha pontos normais
        } elseif ($percentage <= 0.8) {
            return 'low_tier'; // 60-80% - ganha mais pontos
        } else {
            return 'underdog'; // Bottom 20% - ganha muito mais pontos
        }
    }

    /**
     * Aplica balanceamento a todos os torneios de uma temporada
     */
    public function applyBalanceToSeason($seasonId): void
    {
        $tournaments = Tournament::where('season_id', $seasonId)->get();
        
        foreach ($tournaments as $tournament) {
            $this->applyBalanceToTournament($tournament->id);
        }
    }

    /**
     * Obtém estatísticas do sistema de balanceamento
     */
    public function getBalanceStatistics($seasonId): array
    {
        $ranking = $this->calculateSeasonRanking($seasonId);
        $totalPlayers = $ranking->count();
        
        // Contar torneios da temporada
        $totalTournaments = DB::table('tournaments')
            ->where('season_id', $seasonId)
            ->count();
        
        $statistics = [
            'total_players' => $totalPlayers,
            'total_tournaments' => $totalTournaments,
            'balance_active' => $totalPlayers >= self::CONFIG['min_players_for_balance'],
            'tiers' => [
                'top_20_percent' => 0,
                'high_tier' => 0,
                'mid_tier' => 0,
                'low_tier' => 0,
                'bottom_20_percent' => 0
            ],
            'multiplier_range' => [
                'min' => self::CONFIG['min_multiplier'],
                'max' => self::CONFIG['max_multiplier']
            ]
        ];

        foreach ($ranking as $index => $player) {
            $position = $index + 1;
            $percentage = $position / $totalPlayers;
            
            if ($percentage <= 0.2) {
                $statistics['tiers']['top_20_percent']++;
            } elseif ($percentage <= 0.4) {
                $statistics['tiers']['high_tier']++;
            } elseif ($percentage <= 0.6) {
                $statistics['tiers']['mid_tier']++;
            } elseif ($percentage <= 0.8) {
                $statistics['tiers']['low_tier']++;
            } else {
                $statistics['tiers']['bottom_20_percent']++;
            }
        }

        return $statistics;
    }
}
