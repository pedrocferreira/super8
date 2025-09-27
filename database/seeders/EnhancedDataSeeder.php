<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Player;
use App\Models\Tournament;
use App\Models\Season;
use App\Models\Court;
use App\Models\Round;
use App\Models\GameMatch;
use App\Models\PlayerScore;
use App\Models\Pair;
use Carbon\Carbon;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class EnhancedDataSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('pt_BR');

        echo "🌱 Iniciando criação de dados melhorados...\n";

        // Limpar dados existentes
        $this->cleanExistingData();

        // Criar temporadas
        $seasons = $this->createSeasons();
        echo "✅ Temporadas criadas: " . count($seasons) . "\n";

        // Criar jogadores com perfis realistas
        $players = $this->createPlayers();
        echo "✅ Jogadores criados: " . count($players) . "\n";

        // Criar torneios distribuídos ao longo das temporadas
        $tournaments = $this->createTournaments($seasons);
        echo "✅ Torneios criados: " . count($tournaments) . "\n";

        // Criar quadras para cada torneio
        $this->createCourts($tournaments);
        echo "✅ Quadras criadas\n";

        // Distribuir jogadores nos torneios
        $this->distributePlayersInTournaments($tournaments, $players);
        echo "✅ Jogadores distribuídos nos torneios\n";

        // Gerar partidas e resultados realistas
        $this->generateRealisticMatches($tournaments, $players);
        echo "✅ Partidas geradas\n";

        // Atualizar pontuações dos jogadores
        $this->updatePlayerScores();
        echo "✅ Pontuações atualizadas\n";

        echo "🎉 Dados melhorados criados com sucesso!\n";
    }

    private function cleanExistingData()
    {
        echo "🧹 Limpando dados existentes...\n";
        
        // Deletar em ordem para evitar problemas de foreign key
        GameMatch::query()->delete();
        PlayerScore::query()->delete();
        Pair::query()->delete();
        Round::query()->delete();
        Court::query()->delete();
        Tournament::query()->delete();
        Player::query()->delete();
        Season::query()->delete();
        
        // Limpar tabela de relacionamento
        DB::table('tournament_players')->delete();
    }

    private function createSeasons()
    {
        $seasons = [];
        $seasonData = [
            ['name' => 'Temporada 2022', 'year' => 2022, 'status' => 'closed'],
            ['name' => 'Temporada 2023', 'year' => 2023, 'status' => 'closed'],
            ['name' => 'Temporada 2024', 'year' => 2024, 'status' => 'active']
        ];

        foreach ($seasonData as $data) {
            $seasons[] = Season::create([
                'name' => $data['name'],
                'start_date' => Carbon::create($data['year'], 1, 1),
                'end_date' => Carbon::create($data['year'], 12, 31),
                'status' => $data['status']
            ]);
        }

        return collect($seasons);
    }

    private function createPlayers()
    {
        $faker = Faker::create('pt_BR');
        $players = collect();
        $playerProfiles = [
            // Jogadores de elite (top 10%)
            ['count' => 5, 'skill_level' => 'elite', 'consistency' => 'high'],
            // Jogadores avançados (20%)
            ['count' => 10, 'skill_level' => 'advanced', 'consistency' => 'medium'],
            // Jogadores intermediários (40%)
            ['count' => 20, 'skill_level' => 'intermediate', 'consistency' => 'medium'],
            // Jogadores iniciantes (30%)
            ['count' => 15, 'skill_level' => 'beginner', 'consistency' => 'low']
        ];

        foreach ($playerProfiles as $profile) {
            for ($i = 0; $i < $profile['count']; $i++) {
                $gender = $faker->randomElement(['male', 'female']);
                $player = Player::create([
                    'name' => $this->generatePlayerName($gender, $profile['skill_level']),
                    'email' => $faker->unique()->safeEmail,
                    'phone' => $faker->numerify('119#######'),
                    'gender' => $gender,
                    'category' => $this->getCategoryBySkillLevel($profile['skill_level'])
                ]);

                // Adicionar metadados para simulação realista
                $player->skill_level = $profile['skill_level'];
                $player->consistency = $profile['consistency'];
                $players->push($player);
            }
        }

        return $players;
    }

    private function generatePlayerName($gender, $skillLevel)
    {
        $faker = Faker::create('pt_BR');
        
        $name = $gender === 'male' 
            ? $faker->firstNameMale . ' ' . $faker->lastName 
            : $faker->firstNameFemale . ' ' . $faker->lastName;

        // Adicionar sufixos baseados no nível de habilidade
        $suffixes = [
            'elite' => [' Jr.', ' Sr.', ' III'],
            'advanced' => [' Neto', ' Filho'],
            'intermediate' => [],
            'beginner' => [' Jr.']
        ];

        if (!empty($suffixes[$skillLevel]) && $faker->boolean(30)) {
            $name .= $faker->randomElement($suffixes[$skillLevel]);
        }

        return $name;
    }

    private function createTournaments($seasons)
    {
        $faker = Faker::create('pt_BR');
        $tournaments = collect();
        $locations = [
            'Quadra Central', 'Arena Principal', 'Quadra 1', 'Quadra 2', 
            'Quadra 3', 'Quadra 4', 'Quadra VIP', 'Quadra Master'
        ];
        $types = ['super_8_doubles', 'super_8_fixed_pairs', 'super_12_fixed_pairs', 'super_12_selected_pairs'];
        $categories = ['male', 'female', 'mixed'];

        foreach ($seasons as $season) {
            $year = $season->start_date->year;
            
            // Criar 2-4 torneios por mês
            for ($month = 1; $month <= 12; $month++) {
                $tournamentsPerMonth = $faker->numberBetween(2, 4);
                
                for ($t = 0; $t < $tournamentsPerMonth; $t++) {
                    $day = $faker->numberBetween(1, 28);
                    $createdAt = Carbon::create($year, $month, $day, $faker->numberBetween(8, 18));
                    
                    $selectedType = $faker->randomElement($types);
                    $maxPlayers = $this->getMaxPlayersForType($selectedType);
                    
                    $tournament = Tournament::create([
                        'name' => $this->generateTournamentName($month, $t + 1),
                        'location' => $faker->randomElement($locations),
                        'type' => $selectedType,
                        'category' => $faker->randomElement($categories),
                        'max_players' => $maxPlayers,
                        'min_players' => $maxPlayers,
                        'status' => $this->getTournamentStatus($createdAt),
                        'start_date' => $createdAt,
                        'end_date' => $createdAt->copy()->addHours(4),
                        'number_of_courts' => $maxPlayers <= 8 ? 2 : 3,
                        'season_id' => $season->id,
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt
                    ]);

                    $tournaments->push($tournament);
                }
            }
        }

        return $tournaments;
    }

    private function generateTournamentName($month, $number)
    {
        $monthNames = [
            1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março', 4 => 'Abril',
            5 => 'Maio', 6 => 'Junho', 7 => 'Julho', 8 => 'Agosto',
            9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro'
        ];

        $templates = [
            'Copa de {month} #{number}',
            'Torneio {month} #{number}',
            'Championship {month} #{number}',
            'Super 8 {month} #{number}',
            'Arena {month} #{number}'
        ];

        $template = $templates[array_rand($templates)];
        return str_replace(['{month}', '{number}'], [$monthNames[$month], $number], $template);
    }

    private function getMaxPlayersForType($type)
    {
        return match($type) {
            'super_8_doubles' => 8,
            'super_8_fixed_pairs' => 8,
            'super_12_fixed_pairs' => 12,
            'super_12_selected_pairs' => 12,
            default => 8
        };
    }

    private function getTournamentStatus($createdAt)
    {
        $now = now();
        $daysDiff = $createdAt->diffInDays($now);

        if ($daysDiff > 30) return 'completed';
        if ($daysDiff > 7) return 'in_progress';
        if ($daysDiff > 0) return 'draft';
        return 'open';
    }

    private function createCourts($tournaments)
    {
        foreach ($tournaments as $tournament) {
            $courtCount = $tournament->max_players <= 8 ? 2 : 3;
            
            for ($i = 1; $i <= $courtCount; $i++) {
                Court::create([
                    'name' => "Quadra {$i}",
                    'tournament_id' => $tournament->id
                ]);
            }
        }
    }

    private function distributePlayersInTournaments($tournaments, $players)
    {
        foreach ($tournaments as $tournament) {
            $requiredPlayers = $tournament->max_players;
            $category = $tournament->category;
            
            // Filtrar jogadores por categoria
            $filteredPlayers = $players->filter(function($player) use ($category) {
                return $category === 'mixed' || $player->gender === $category;
            });

            // Se não há jogadores suficientes, usar todos
            if ($filteredPlayers->count() < $requiredPlayers) {
                $selectedPlayers = $players->random($requiredPlayers);
            } else {
                $selectedPlayers = $filteredPlayers->random($requiredPlayers);
            }

            // Adicionar jogadores ao torneio
            foreach ($selectedPlayers as $player) {
                $tournament->players()->attach($player->id);
            }
        }
    }

    private function generateRealisticMatches($tournaments, $players)
    {
        foreach ($tournaments as $tournament) {
            if ($tournament->status === 'completed' || $tournament->status === 'in_progress') {
                $this->generateMatchesForTournament($tournament, $players);
            }
        }
    }

    private function generateMatchesForTournament($tournament, $players)
    {
        $faker = Faker::create('pt_BR');
        $tournamentPlayers = $tournament->players;
        $courts = $tournament->courts;
        
        if ($tournamentPlayers->count() < 4) return;

        // Criar rodadas baseadas no tipo do torneio
        $rounds = $this->createRoundsForTournament($tournament);
        
        foreach ($rounds as $round) {
            $this->generateMatchesForRound($round, $tournamentPlayers, $courts, $faker);
        }
    }

    private function createRoundsForTournament($tournament)
    {
        $rounds = [];
        $roundCount = $tournament->max_players <= 8 ? 3 : 4; // Mais rodadas para torneios maiores
        
        for ($i = 1; $i <= $roundCount; $i++) {
            $rounds[] = Round::create([
                'tournament_id' => $tournament->id,
                'round_number' => $i
            ]);
        }
        
        return $rounds;
    }

    private function generateMatchesForRound($round, $players, $courts, $faker)
    {
        $playerList = $players->shuffle();
        $courtList = $courts->shuffle();
        $matchesPerRound = min(floor($players->count() / 4), $courts->count());
        
        for ($i = 0; $i < $matchesPerRound; $i++) {
            $team1Player1 = $playerList->shift();
            $team1Player2 = $playerList->shift();
            $team2Player1 = $playerList->shift();
            $team2Player2 = $playerList->shift();
            
            if (!$team1Player1 || !$team1Player2 || !$team2Player1 || !$team2Player2) break;
            
            $match = GameMatch::create([
                'round_id' => $round->id,
                'court_id' => $courtList[$i % $courtList->count()]->id,
                'team1_player1_id' => $team1Player1->id,
                'team1_player2_id' => $team1Player2->id,
                'team2_player1_id' => $team2Player1->id,
                'team2_player2_id' => $team2Player2->id,
                'score_details' => null,
                'winner_team' => null
            ]);
            
            // Gerar resultado realista baseado no nível dos jogadores
            $this->generateRealisticMatchResult($match, $faker);
        }
    }

    private function generateRealisticMatchResult($match, $faker)
    {
        // Obter níveis de habilidade dos jogadores
        $team1Skill = $this->getTeamSkillLevel($match->team1_player1_id, $match->team1_player2_id);
        $team2Skill = $this->getTeamSkillLevel($match->team2_player1_id, $match->team2_player2_id);
        
        // Calcular probabilidade de vitória baseada no nível
        $team1WinProbability = $this->calculateWinProbability($team1Skill, $team2Skill);
        
        // Adicionar fator de aleatoriedade
        $randomFactor = $faker->randomFloat(2, 0.1, 0.3);
        $team1WinProbability += $faker->boolean(50) ? $randomFactor : -$randomFactor;
        $team1WinProbability = max(0.1, min(0.9, $team1WinProbability));
        
        $team1Wins = $faker->randomFloat(2, 0, 1) < $team1WinProbability;
        
        // Gerar placar realista baseado no tipo de torneio
        $tournament = $match->round->tournament;
        
        if (in_array($tournament->type, ['super_8_doubles', 'super_8_fixed_pairs'])) {
            // Super 8: placar vai até 6 pontos
            $team1Score = $team1Wins ? 6 : $faker->numberBetween(0, 5);
            $team2Score = $team1Wins ? $faker->numberBetween(0, 5) : 6;
        } else {
            // Super 12: placar tradicional (11-21 pontos)
            $team1Score = $team1Wins ? $faker->numberBetween(11, 21) : $faker->numberBetween(5, 19);
            $team2Score = $team1Wins ? $faker->numberBetween(5, 19) : $faker->numberBetween(11, 21);
        }
        
        // Garantir que o placar seja válido
        if ($team1Score === $team2Score) {
            if (in_array($tournament->type, ['super_8_doubles', 'super_8_fixed_pairs'])) {
                $team1Score = 6;
                $team2Score = $faker->numberBetween(0, 5);
            } else {
                $team1Score += 2;
            }
        }
        
        $match->update([
            'winner_team' => $team1Wins ? 'team1' : 'team2',
            'score_details' => "{$team1Score} x {$team2Score}"
        ]);
    }

    private function getTeamSkillLevel($player1Id, $player2Id)
    {
        // Simular níveis de habilidade baseados no ID (para consistência)
        $skill1 = $this->getPlayerSkillLevel($player1Id);
        $skill2 = $this->getPlayerSkillLevel($player2Id);
        
        // Média dos dois jogadores
        return ($skill1 + $skill2) / 2;
    }

    private function getPlayerSkillLevel($playerId)
    {
        // Usar o ID para gerar um nível consistente
        $hash = crc32($playerId);
        $normalized = ($hash % 1000) / 1000; // 0 a 1
        
        // Distribuir em níveis
        if ($normalized < 0.1) return 0.9; // Elite
        if ($normalized < 0.3) return 0.7; // Avançado
        if ($normalized < 0.7) return 0.5; // Intermediário
        return 0.3; // Iniciante
    }

    private function calculateWinProbability($team1Skill, $team2Skill)
    {
        $skillDiff = $team1Skill - $team2Skill;
        
        // Função sigmóide para converter diferença de habilidade em probabilidade
        $probability = 1 / (1 + exp(-5 * $skillDiff));
        
        return $probability;
    }

    private function updatePlayerScores()
    {
        // Atualizar pontuações de todos os jogadores
        $players = Player::all();
        
        foreach ($players as $player) {
            $this->updatePlayerScore($player);
        }
    }

    private function updatePlayerScore($player)
    {
        $tournaments = $player->tournaments;
        
        if ($tournaments && $tournaments->count() > 0) {
            foreach ($tournaments as $tournament) {
                $existingScore = PlayerScore::where('player_id', $player->id)
                    ->where('tournament_id', $tournament->id)
                    ->first();
                
                if (!$existingScore) {
                    PlayerScore::create([
                        'player_id' => $player->id,
                        'tournament_id' => $tournament->id,
                        'points' => 0,
                        'games_won' => 0,
                        'games_lost' => 0
                    ]);
                }
            }
            
            // Recalcular pontuações
            $this->recalculatePlayerScores($player);
        }
    }

    private function recalculatePlayerScores($player)
    {
        $tournaments = $player->tournaments;
        
        foreach ($tournaments as $tournament) {
            $playerScore = PlayerScore::where('player_id', $player->id)
                ->where('tournament_id', $tournament->id)
                ->first();
            
            if ($playerScore) {
                $stats = $this->calculatePlayerTournamentStats($player, $tournament);
                
                $playerScore->update([
                    'points' => $stats['points'],
                    'games_won' => $stats['games_won'],
                    'games_lost' => $stats['games_lost']
                ]);
            }
        }
    }

    private function calculatePlayerTournamentStats($player, $tournament)
    {
        $matches = GameMatch::whereHas('round', function($query) use ($tournament) {
            $query->where('tournament_id', $tournament->id);
        })->where(function($query) use ($player) {
            $query->where('team1_player1_id', $player->id)
                  ->orWhere('team1_player2_id', $player->id)
                  ->orWhere('team2_player1_id', $player->id)
                  ->orWhere('team2_player2_id', $player->id);
        })->whereNotNull('winner_team')->get();

        $points = 0;
        $gamesWon = 0;
        $gamesLost = 0;

        foreach ($matches as $match) {
            $isTeam1 = in_array($player->id, [$match->team1_player1_id, $match->team1_player2_id]);
            $won = ($isTeam1 && $match->winner_team === 'team1') || (!$isTeam1 && $match->winner_team === 'team2');
            
            if ($won) {
                $gamesWon++;
                $points += 3; // 3 pontos por vitória
            } else {
                $gamesLost++;
                $points += 1; // 1 ponto por derrota
            }
        }

        return [
            'points' => $points,
            'games_won' => $gamesWon,
            'games_lost' => $gamesLost
        ];
    }

    private function getCategoryBySkillLevel($skillLevel)
    {
        return match($skillLevel) {
            'beginner' => 'D', // Iniciante
            'intermediate' => 'C', // Intermediário
            'advanced' => 'B', // Avançado
            default => 'D'
        };
    }
}