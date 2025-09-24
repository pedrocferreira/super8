<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tournament extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'location', 'start_date', 'end_date', 'type', 'category', 'season_id',
        'min_players', 'max_players', 'scoring_criteria', 'status',
        'number_of_courts', 'registration_code', 'registration_open'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'scoring_criteria' => 'json'
    ];

    public function rounds()
    {
        return $this->hasMany(Round::class);
    }

    public function matches()
    {
        return $this->hasManyThrough(GameMatch::class, Round::class);
    }

    public function pairs()
    {
        return $this->hasMany(Pair::class);
    }

    public function playerScores()
    {
        return $this->hasMany(PlayerScore::class);
    }

    public function courts()
    {
        return $this->hasMany(Court::class);
    }

    public function players()
    {
        return $this->belongsToMany(Player::class, 'tournament_players')
                    ->withTimestamps();
    }

    public function season()
    {
        return $this->belongsTo(Season::class);
    }
}
