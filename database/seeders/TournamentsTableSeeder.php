<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tournament;
use Carbon\Carbon;

class TournamentsTableSeeder extends Seeder
{
    public function run()
    {
        $tournaments = [
            [
                'name' => 'Super 8 Janeiro 2024',
                'location' => 'Quadra Central',
                'type' => 'super_8_individual',
                'status' => 'completed',
                'start_date' => Carbon::create(2024, 1, 15),
                'end_date' => Carbon::create(2024, 1, 16),
                'min_players' => 8,
                'max_players' => 8,
                'number_of_courts' => 2
            ],
            [
                'name' => 'Super 8 Fevereiro 2024',
                'location' => 'Quadra Central',
                'type' => 'super_8_individual',
                'status' => 'completed',
                'start_date' => Carbon::create(2024, 2, 10),
                'end_date' => Carbon::create(2024, 2, 11),
                'min_players' => 8,
                'max_players' => 8,
                'number_of_courts' => 2
            ],
            [
                'name' => 'Super 12 Março 2024',
                'location' => 'Arena Principal',
                'type' => 'super_12_fixed_pairs',
                'status' => 'completed',
                'start_date' => Carbon::create(2024, 3, 1),
                'end_date' => Carbon::create(2024, 3, 2),
                'min_players' => 12,
                'max_players' => 12,
                'number_of_courts' => 3
            ]
        ];

        foreach ($tournaments as $tournamentData) {
            $tournament = Tournament::create($tournamentData);

            // Cria as quadras para cada torneio
            for ($i = 1; $i <= $tournamentData['number_of_courts']; $i++) {
                $tournament->courts()->create([
                    'name' => "Quadra {$i}",
                    'is_active' => true
                ]);
            }
        }
    }
}
