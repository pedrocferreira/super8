<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tournament;
use Illuminate\Support\Str;

class UpdateTournamentRegistrationCodes extends Command
{
    protected $signature = 'tournaments:update-codes';
    protected $description = 'Update registration codes for existing tournaments';

    public function handle()
    {
        $tournaments = Tournament::whereNull('registration_code')->get();

        foreach ($tournaments as $tournament) {
            $tournament->update([
                'registration_code' => Str::random(8),
                'registration_open' => true
            ]);
        }

        $this->info("{$tournaments->count()} tournaments updated.");
    }
} 