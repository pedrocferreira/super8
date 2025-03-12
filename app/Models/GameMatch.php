<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameMatch extends Model
{
    protected $table = 'matches'; // Define o nome da tabela explicitamente

    protected $fillable = [
        'round_id',
        'court_id',
        'team1_player1_id',
        'team1_player2_id',
        'team2_player1_id',
        'team2_player2_id',
        'team1_pair_id',
        'team2_pair_id',
        'winner_team',
        'score_details',
        'status',
        'scheduled_time',
        'team1_score',
        'team2_score'
    ];

    protected $casts = [
        'scheduled_time' => 'datetime',
        'score_details' => 'json'
    ];

    public function round()
    {
        return $this->belongsTo(Round::class);
    }

    public function tournament()
    {
        return $this->hasOneThrough(Tournament::class, Round::class, 'id', 'id', 'round_id', 'tournament_id');
    }

    public function team1_player1()
    {
        return $this->belongsTo(Player::class, 'team1_player1_id');
    }

    public function team1_player2()
    {
        return $this->belongsTo(Player::class, 'team1_player2_id');
    }

    public function team2_player1()
    {
        return $this->belongsTo(Player::class, 'team2_player1_id');
    }

    public function team2_player2()
    {
        return $this->belongsTo(Player::class, 'team2_player2_id');
    }

    public function team1_pair()
    {
        return $this->belongsTo(Pair::class, 'team1_pair_id');
    }

    public function team2_pair()
    {
        return $this->belongsTo(Pair::class, 'team2_pair_id');
    }

    public function court()
    {
        return $this->belongsTo(Court::class);
    }
}
