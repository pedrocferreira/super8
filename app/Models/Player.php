<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Player extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'email', 'phone'];

    public function pairs()
    {
        return $this->hasMany(Pair::class, 'player1_id')
            ->orWhere('player2_id', $this->id);
    }

    public function playerScores()
    {
        return $this->hasMany(PlayerScore::class);
    }

    public function getBestPartner()
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
            ->select('players.id', 'players.name', DB::raw('COUNT(*) as wins'))
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
}
