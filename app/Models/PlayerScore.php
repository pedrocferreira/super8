<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlayerScore extends Model
{
    protected $fillable = [
        'tournament_id',
        'player_id',
        'points',
        'games_won',
        'games_lost'
    ];

    public function tournament()
    {
        return $this->belongsTo(Tournament::class);
    }

    public function player()
    {
        return $this->belongsTo(Player::class);
    }

    // Método para calcular o aproveitamento
    public function getWinRateAttribute()
    {
        $total = $this->games_won + $this->games_lost;
        if ($total === 0) return 0;
        return round(($this->games_won / $total) * 100, 1);
    }
}
