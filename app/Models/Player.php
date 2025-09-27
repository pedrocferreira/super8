<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Player extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'email', 'phone', 'gender', 'category'];

    public function pairs()
    {
        return $this->hasMany(Pair::class, 'player1_id')
            ->orWhere('player2_id', $this->id);
    }

    public function playerScores()
    {
        return $this->hasMany(PlayerScore::class);
    }

    public function tournaments()
    {
        return $this->belongsToMany(Tournament::class, 'tournament_players');
    }

    /**
     * Obtém informações de balanceamento para uma temporada
     */
    public function getBalanceInfo($seasonId)
    {
        $balanceService = app(\App\Services\RankingBalanceService::class);
        return $balanceService->getPlayerBalanceInfo($this->id, $seasonId);
    }

    /**
     * Obtém o ranking de uma temporada
     */
    public function getSeasonRanking($seasonId)
    {
        $balanceService = app(\App\Services\RankingBalanceService::class);
        return $balanceService->calculateSeasonRanking($seasonId);
    }

    /**
     * Obtém estatísticas de balanceamento de uma temporada
     */
    public function getSeasonBalanceStats($seasonId)
    {
        $balanceService = app(\App\Services\RankingBalanceService::class);
        return $balanceService->getBalanceStatistics($seasonId);
    }

    public function getBestPartner($seasonId = null)
    {
        $query = DB::table('matches')
            ->join('players', function($join) {
                $join->on('players.id', '=', 'matches.team1_player2_id')
                     ->orOn('players.id', '=', 'matches.team2_player2_id');
            })
            ->join('rounds', 'rounds.id', '=', 'matches.round_id')
            ->join('tournaments', 'tournaments.id', '=', 'rounds.tournament_id')
            ->where(function($q) {
                $q->where('team1_player1_id', $this->id)
                  ->orWhere('team2_player1_id', $this->id);
            })
            ->where('matches.winner_team', 'IS NOT', null);

        if ($seasonId) {
            $query->where('tournaments.season_id', $seasonId);
        }

        return $query->select('players.id', 'players.name', DB::raw('COUNT(*) as wins'))
            ->groupBy('players.id', 'players.name')
            ->orderBy('wins', 'desc')
            ->first();
    }

    public function getToughestOpponent()
    {
        return DB::table('matches')
            ->join('players', 'players.id', '=', DB::raw('CASE
                WHEN team1_player1_id = ' . $this->id . ' OR team1_player2_id = ' . $this->id . '
                THEN team2_player1_id
                ELSE team1_player1_id
                END'))
            ->where(function($query) {
                $query->whereRaw('(team1_player1_id = ? OR team1_player2_id = ?) AND winner_team = "team2"', [$this->id, $this->id])
                      ->orWhereRaw('(team2_player1_id = ? OR team2_player2_id = ?) AND winner_team = "team1"', [$this->id, $this->id]);
            })
            ->select('players.id', 'players.name', DB::raw('COUNT(*) as wins'))
            ->groupBy('players.id', 'players.name')
            ->orderBy('wins', 'desc')
            ->first();
    }

    public function getBestWinStreak()
    {
        // Implementar lógica para calcular maior sequência de vitórias
        return 0; // Placeholder
    }

    public function getCurrentWinStreak()
    {
        // Implementar lógica para calcular sequência atual de vitórias
        return 0; // Placeholder
    }

    public function getBestCourt()
    {
        $playerId = $this->id;

        return DB::table('matches')
            ->join('courts', 'courts.id', '=', 'matches.court_id')
            ->where(function($query) use ($playerId) {
                $query->where('team1_player1_id', $playerId)
                      ->orWhere('team1_player2_id', $playerId)
                      ->orWhere('team2_player1_id', $playerId)
                      ->orWhere('team2_player2_id', $playerId);
            })
            ->select(
                'courts.name',
                DB::raw('COUNT(*) as total_matches'),
                DB::raw("SUM(CASE
                    WHEN (team1_player1_id = {$playerId} OR team1_player2_id = {$playerId}) AND winner_team = 'team1'
                    OR (team2_player1_id = {$playerId} OR team2_player2_id = {$playerId}) AND winner_team = 'team2'
                    THEN 1 ELSE 0 END) as wins"),
                DB::raw("(SUM(CASE
                    WHEN (team1_player1_id = {$playerId} OR team1_player2_id = {$playerId}) AND winner_team = 'team1'
                    OR (team2_player1_id = {$playerId} OR team2_player2_id = {$playerId}) AND winner_team = 'team2'
                    THEN 1 ELSE 0 END) * 100.0 / COUNT(*)) as win_rate")
            )
            ->groupBy('courts.id', 'courts.name')
            ->orderBy('win_rate', 'desc')
            ->first();
    }

    public function getAveragePointsPerTournament()
    {
        return $this->playerScores()->avg('points') ?? 0;
    }

    /**
     * Métricas de Quadras
     */
    public function getWorstCourt()
    {
        $playerId = $this->id;

        return DB::table('matches')
            ->join('courts', 'courts.id', '=', 'matches.court_id')
            ->where(function($query) use ($playerId) {
                $query->where('team1_player1_id', $playerId)
                      ->orWhere('team1_player2_id', $playerId)
                      ->orWhere('team2_player1_id', $playerId)
                      ->orWhere('team2_player2_id', $playerId);
            })
            ->select(
                'courts.name',
                DB::raw('COUNT(*) as total_matches'),
                DB::raw("SUM(CASE
                    WHEN (team1_player1_id = {$playerId} OR team1_player2_id = {$playerId}) AND winner_team = 'team1'
                    OR (team2_player1_id = {$playerId} OR team2_player2_id = {$playerId}) AND winner_team = 'team2'
                    THEN 1 ELSE 0 END) as wins"),
                DB::raw("(SUM(CASE
                    WHEN (team1_player1_id = {$playerId} OR team1_player2_id = {$playerId}) AND winner_team = 'team1'
                    OR (team2_player1_id = {$playerId} OR team2_player2_id = {$playerId}) AND winner_team = 'team2'
                    THEN 1 ELSE 0 END) * 100.0 / COUNT(*)) as win_rate")
            )
            ->groupBy('courts.id', 'courts.name')
            ->orderBy('win_rate', 'asc')
            ->first();
    }

    /**
     * Métricas de Parcerias
     */
    public function getWorstPartner()
    {
        return DB::table('matches')
            ->join('players', function($join) {
                $join->on('players.id', '=', 'matches.team1_player2_id')
                     ->orOn('players.id', '=', 'matches.team2_player2_id');
            })
            ->where(function($query) {
                $query->where('team1_player1_id', $this->id)
                      ->orWhere('team2_player1_id', $this->id);
            })
            ->where('matches.winner_team', 'IS NOT', null)
            ->select('players.id', 'players.name', DB::raw('COUNT(*) as total_matches'), 
                    DB::raw("SUM(CASE
                        WHEN (team1_player1_id = {$this->id} AND winner_team = 'team1') OR 
                             (team2_player1_id = {$this->id} AND winner_team = 'team2')
                        THEN 1 ELSE 0 END) as wins"),
                    DB::raw("(SUM(CASE
                        WHEN (team1_player1_id = {$this->id} AND winner_team = 'team1') OR 
                             (team2_player1_id = {$this->id} AND winner_team = 'team2')
                        THEN 1 ELSE 0 END) * 100.0 / COUNT(*)) as win_rate"))
            ->groupBy('players.id', 'players.name')
            ->having('total_matches', '>=', 2) // Pelo menos 2 partidas
            ->orderBy('win_rate', 'asc')
            ->first();
    }

    public function getPartnershipCompatibility()
    {
        return DB::table('matches')
            ->join('players', function($join) {
                $join->on('players.id', '=', 'matches.team1_player2_id')
                     ->orOn('players.id', '=', 'matches.team2_player2_id');
            })
            ->where(function($query) {
                $query->where('team1_player1_id', $this->id)
                      ->orWhere('team2_player1_id', $this->id);
            })
            ->where('matches.winner_team', 'IS NOT', null)
            ->select('players.id', 'players.name', DB::raw('COUNT(*) as total_matches'), 
                    DB::raw("SUM(CASE
                        WHEN (team1_player1_id = {$this->id} AND winner_team = 'team1') OR 
                             (team2_player1_id = {$this->id} AND winner_team = 'team2')
                        THEN 1 ELSE 0 END) as wins"),
                    DB::raw("(SUM(CASE
                        WHEN (team1_player1_id = {$this->id} AND winner_team = 'team1') OR 
                             (team2_player1_id = {$this->id} AND winner_team = 'team2')
                        THEN 1 ELSE 0 END) * 100.0 / COUNT(*)) as win_rate"))
            ->groupBy('players.id', 'players.name')
            ->having('total_matches', '>=', 1)
            ->orderBy('win_rate', 'desc')
            ->get();
    }

    public function getDifferentPartnersCount()
    {
        return DB::table('matches')
            ->where(function($query) {
                $query->where('team1_player1_id', $this->id)
                      ->orWhere('team2_player1_id', $this->id);
            })
            ->where('matches.winner_team', 'IS NOT', null)
            ->select(DB::raw('COUNT(DISTINCT CASE 
                WHEN team1_player1_id = ' . $this->id . ' THEN team1_player2_id 
                ELSE team2_player2_id 
                END) as partners_count'))
            ->value('partners_count') ?? 0;
    }

    public function getMostFrequentPartner()
    {
        return DB::table('matches')
            ->join('players', function($join) {
                $join->on('players.id', '=', 'matches.team1_player2_id')
                     ->orOn('players.id', '=', 'matches.team2_player2_id');
            })
            ->where(function($query) {
                $query->where('team1_player1_id', $this->id)
                      ->orWhere('team2_player1_id', $this->id);
            })
            ->where('matches.winner_team', 'IS NOT', null)
            ->select('players.id', 'players.name', DB::raw('COUNT(*) as matches_count'))
            ->groupBy('players.id', 'players.name')
            ->orderBy('matches_count', 'desc')
            ->first();
    }

    /**
     * Métricas de Confrontos Diretos
     */
    public function getFavoriteVictim()
    {
        return DB::table('matches')
            ->join('players', 'players.id', '=', DB::raw('CASE
                WHEN team1_player1_id = ' . $this->id . ' OR team1_player2_id = ' . $this->id . '
                THEN team2_player1_id
                ELSE team1_player1_id
                END'))
            ->where(function($query) {
                $query->whereRaw('(team1_player1_id = ? OR team1_player2_id = ?) AND winner_team = "team1"', [$this->id, $this->id])
                      ->orWhereRaw('(team2_player1_id = ? OR team2_player2_id = ?) AND winner_team = "team2"', [$this->id, $this->id]);
            })
            ->select('players.id', 'players.name', DB::raw('COUNT(*) as wins'))
            ->groupBy('players.id', 'players.name')
            ->orderBy('wins', 'desc')
            ->first();
    }

    public function getHeadToHeadRecord($opponentId)
    {
        return DB::table('matches')
            ->where(function($query) use ($opponentId) {
                $query->where(function($q) use ($opponentId) {
                    $q->where('team1_player1_id', $this->id)
                      ->where('team2_player1_id', $opponentId);
                })->orWhere(function($q) use ($opponentId) {
                    $q->where('team1_player1_id', $opponentId)
                      ->where('team2_player1_id', $this->id);
                })->orWhere(function($q) use ($opponentId) {
                    $q->where('team1_player2_id', $this->id)
                      ->where('team2_player2_id', $opponentId);
                })->orWhere(function($q) use ($opponentId) {
                    $q->where('team1_player2_id', $opponentId)
                      ->where('team2_player2_id', $this->id);
                });
            })
            ->where('winner_team', 'IS NOT', null)
            ->select(
                DB::raw("SUM(CASE
                    WHEN (team1_player1_id = {$this->id} OR team1_player2_id = {$this->id}) AND winner_team = 'team1'
                    OR (team2_player1_id = {$this->id} OR team2_player2_id = {$this->id}) AND winner_team = 'team2'
                    THEN 1 ELSE 0 END) as wins"),
                DB::raw("SUM(CASE
                    WHEN (team1_player1_id = {$opponentId} OR team1_player2_id = {$opponentId}) AND winner_team = 'team1'
                    OR (team2_player1_id = {$opponentId} OR team2_player2_id = {$opponentId}) AND winner_team = 'team2'
                    THEN 1 ELSE 0 END) as losses"),
                DB::raw('COUNT(*) as total_matches')
            )
            ->first();
    }

    public function getRivalries()
    {
        return DB::table('matches')
            ->join('players', 'players.id', '=', DB::raw('CASE
                WHEN team1_player1_id = ' . $this->id . ' OR team1_player2_id = ' . $this->id . '
                THEN team2_player1_id
                ELSE team1_player1_id
                END'))
            ->where(function($query) {
                $query->where('team1_player1_id', $this->id)
                      ->orWhere('team1_player2_id', $this->id)
                      ->orWhere('team2_player1_id', $this->id)
                      ->orWhere('team2_player2_id', $this->id);
            })
            ->where('winner_team', 'IS NOT', null)
            ->select('players.id', 'players.name', DB::raw('COUNT(*) as matches_count'))
            ->groupBy('players.id', 'players.name')
            ->having('matches_count', '>=', 2)
            ->orderBy('matches_count', 'desc')
            ->limit(5)
            ->get();
    }

    /**
     * Métricas de Evolução e Tendências
     */
    public function getMonthlyPerformance()
    {
        return DB::table('player_scores')
            ->join('tournaments', 'tournaments.id', '=', 'player_scores.tournament_id')
            ->where('player_scores.player_id', $this->id)
            ->select(
                DB::raw('YEAR(tournaments.created_at) as year'),
                DB::raw('MONTH(tournaments.created_at) as month'),
                DB::raw('SUM(player_scores.points) as points'),
                DB::raw('SUM(player_scores.games_won) as wins'),
                DB::raw('SUM(player_scores.games_lost) as losses'),
                DB::raw('COUNT(*) as tournaments')
            )
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();
    }

    public function getSeasonalPerformance()
    {
        return DB::table('player_scores')
            ->join('tournaments', 'tournaments.id', '=', 'player_scores.tournament_id')
            ->join('seasons', 'seasons.id', '=', 'tournaments.season_id')
            ->where('player_scores.player_id', $this->id)
            ->select(
                'seasons.name as season_name',
                'seasons.id as season_id',
                DB::raw('SUM(player_scores.points) as points'),
                DB::raw('SUM(player_scores.games_won) as wins'),
                DB::raw('SUM(player_scores.games_lost) as losses'),
                DB::raw('COUNT(*) as tournaments'),
                DB::raw('(SUM(player_scores.games_won) * 100.0 / (SUM(player_scores.games_won) + SUM(player_scores.games_lost))) as win_rate')
            )
            ->groupBy('seasons.id', 'seasons.name')
            ->orderBy('points', 'desc')
            ->get();
    }

    public function getPerformanceTrend()
    {
        $monthlyData = $this->getMonthlyPerformance();
        
        if ($monthlyData->count() < 2) {
            return 'insufficient_data';
        }

        $recentMonths = $monthlyData->take(3);
        $olderMonths = $monthlyData->skip(3)->take(3);

        if ($olderMonths->count() < 2) {
            return 'insufficient_data';
        }

        $recentAvg = $recentMonths->avg('win_rate');
        $olderAvg = $olderMonths->avg('win_rate');

        if ($recentAvg > $olderAvg + 5) {
            return 'improving';
        } elseif ($recentAvg < $olderAvg - 5) {
            return 'declining';
        } else {
            return 'stable';
        }
    }

    public function getConsistency()
    {
        $monthlyData = $this->getMonthlyPerformance();
        
        if ($monthlyData->count() < 3) {
            return 'insufficient_data';
        }

        $winRates = $monthlyData->pluck('win_rate')->filter();
        
        if ($winRates->count() < 3) {
            return 'insufficient_data';
        }

        $variance = $winRates->map(function($rate) use ($winRates) {
            return pow($rate - $winRates->avg(), 2);
        })->avg();

        $stdDev = sqrt($variance);

        if ($stdDev < 10) {
            return 'very_consistent';
        } elseif ($stdDev < 20) {
            return 'consistent';
        } elseif ($stdDev < 30) {
            return 'moderately_consistent';
        } else {
            return 'inconsistent';
        }
    }

    /**
     * Métricas Avançadas de Parceiros
     */
    public function getBestPartnerByWins()
    {
        return DB::table('matches')
            ->join('players', function($join) {
                $join->on('players.id', '=', 'matches.team1_player2_id')
                     ->orOn('players.id', '=', 'matches.team2_player2_id');
            })
            ->where(function($query) {
                $query->where('team1_player1_id', $this->id)
                      ->orWhere('team2_player1_id', $this->id);
            })
            ->where('matches.winner_team', 'IS NOT', null)
            ->select('players.id', 'players.name', 
                    DB::raw("SUM(CASE
                        WHEN (team1_player1_id = {$this->id} AND winner_team = 'team1') OR 
                             (team2_player1_id = {$this->id} AND winner_team = 'team2')
                        THEN 1 ELSE 0 END) as wins"),
                    DB::raw('COUNT(*) as total_matches'))
            ->groupBy('players.id', 'players.name')
            ->having('total_matches', '>=', 2)
            ->orderBy('wins', 'desc')
            ->first();
    }

    public function getWorstPartnerByLosses()
    {
        return DB::table('matches')
            ->join('players', function($join) {
                $join->on('players.id', '=', 'matches.team1_player2_id')
                     ->orOn('players.id', '=', 'matches.team2_player2_id');
            })
            ->where(function($query) {
                $query->where('team1_player1_id', $this->id)
                      ->orWhere('team2_player1_id', $this->id);
            })
            ->where('matches.winner_team', 'IS NOT', null)
            ->select('players.id', 'players.name', 
                    DB::raw("SUM(CASE
                        WHEN (team1_player1_id = {$this->id} AND winner_team = 'team2') OR 
                             (team2_player1_id = {$this->id} AND winner_team = 'team1')
                        THEN 1 ELSE 0 END) as losses"),
                    DB::raw('COUNT(*) as total_matches'))
            ->groupBy('players.id', 'players.name')
            ->having('total_matches', '>=', 2)
            ->orderBy('losses', 'desc')
            ->first();
    }

    /**
     * Métricas Avançadas de Adversários
     */
    public function getBestOpponentByWins()
    {
        return DB::table('matches')
            ->join('players', 'players.id', '=', DB::raw('CASE
                WHEN team1_player1_id = ' . $this->id . ' OR team1_player2_id = ' . $this->id . '
                THEN team2_player1_id
                ELSE team1_player1_id
                END'))
            ->where(function($query) {
                $query->whereRaw('(team1_player1_id = ? OR team1_player2_id = ?) AND winner_team = "team1"', [$this->id, $this->id])
                      ->orWhereRaw('(team2_player1_id = ? OR team2_player2_id = ?) AND winner_team = "team2"', [$this->id, $this->id]);
            })
            ->select('players.id', 'players.name', DB::raw('COUNT(*) as wins'))
            ->groupBy('players.id', 'players.name')
            ->having('wins', '>=', 2)
            ->orderBy('wins', 'desc')
            ->first();
    }

    public function getToughestOpponentByLosses()
    {
        return DB::table('matches')
            ->join('players', 'players.id', '=', DB::raw('CASE
                WHEN team1_player1_id = ' . $this->id . ' OR team1_player2_id = ' . $this->id . '
                THEN team2_player1_id
                ELSE team1_player1_id
                END'))
            ->where(function($query) {
                $query->whereRaw('(team1_player1_id = ? OR team1_player2_id = ?) AND winner_team = "team2"', [$this->id, $this->id])
                      ->orWhereRaw('(team2_player1_id = ? OR team2_player2_id = ?) AND winner_team = "team1"', [$this->id, $this->id]);
            })
            ->select('players.id', 'players.name', DB::raw('COUNT(*) as losses'))
            ->groupBy('players.id', 'players.name')
            ->having('losses', '>=', 2)
            ->orderBy('losses', 'desc')
            ->first();
    }

    /**
     * Métricas de Categoria
     */
    public function getCategoryStats()
    {
        return [
            'category' => $this->category,
            'category_name' => $this->getCategoryName(),
            'total_matches' => $this->getTotalMatches(),
            'wins' => $this->getTotalWins(),
            'losses' => $this->getTotalLosses(),
            'win_rate' => $this->getWinRate()
        ];
    }

    public function getCategoryName()
    {
        return match($this->category) {
            'D' => 'Iniciante',
            'C' => 'Intermediário', 
            'B' => 'Avançado',
            default => 'Não definido'
        };
    }

    public function getTotalMatches()
    {
        return DB::table('matches')
            ->where(function($query) {
                $query->where('team1_player1_id', $this->id)
                      ->orWhere('team1_player2_id', $this->id)
                      ->orWhere('team2_player1_id', $this->id)
                      ->orWhere('team2_player2_id', $this->id);
            })
            ->where('winner_team', 'IS NOT', null)
            ->count();
    }

    public function getTotalWins()
    {
        return DB::table('matches')
            ->where(function($query) {
                $query->whereRaw('(team1_player1_id = ? OR team1_player2_id = ?) AND winner_team = "team1"', [$this->id, $this->id])
                      ->orWhereRaw('(team2_player1_id = ? OR team2_player2_id = ?) AND winner_team = "team2"', [$this->id, $this->id]);
            })
            ->count();
    }

    public function getTotalLosses()
    {
        return DB::table('matches')
            ->where(function($query) {
                $query->whereRaw('(team1_player1_id = ? OR team1_player2_id = ?) AND winner_team = "team2"', [$this->id, $this->id])
                      ->orWhereRaw('(team2_player1_id = ? OR team2_player2_id = ?) AND winner_team = "team1"', [$this->id, $this->id]);
            })
            ->count();
    }

    public function getWinRate()
    {
        $total = $this->getTotalMatches();
        if ($total == 0) return 0;
        
        $wins = $this->getTotalWins();
        return round(($wins / $total) * 100, 1);
    }
}
