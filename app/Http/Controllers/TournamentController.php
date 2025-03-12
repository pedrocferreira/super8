<?php

namespace App\Http\Controllers;

use App\Models\Tournament;
use App\Models\GameMatch;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Player;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Helpers\Logger;
use Illuminate\Support\Str;

class TournamentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tournaments = Tournament::latest()->paginate(10);
        return view('tournaments.index', compact('tournaments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('tournaments.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'type' => 'required|in:super_8_individual,super_12_fixed_pairs',
            'number_of_courts' => 'required|integer|min:1|max:10',
        ]);

        // Adiciona as datas automaticamente
        $validated['start_date'] = Carbon::now();
        $validated['end_date'] = Carbon::now();

        // Define o número mínimo e máximo de jogadores com base no tipo
        if ($validated['type'] === 'super_8_individual') {
            $validated['min_players'] = 8;
            $validated['max_players'] = 8;
        } else {
            $validated['min_players'] = 12;
            $validated['max_players'] = 12;
        }

        // Gera um código único para registro
        $validated['registration_code'] = Str::random(8);
        $validated['registration_open'] = true;
        $validated['status'] = 'draft';

        // Cria o torneio
        $tournament = Tournament::create($validated);

        // Cria as quadras
        for ($i = 1; $i <= $request->number_of_courts; $i++) {
            $tournament->courts()->create([
                'name' => "Quadra {$i}",
                'is_active' => true
            ]);
        }

        return redirect()->route('tournaments.index')
            ->with('success', 'Torneio criado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Tournament $tournament, Request $request)
    {
        try {
            Logger::info("=== Início do carregamento do torneio ===");
            Logger::info("ID: " . $tournament->id);
            Logger::info("Tipo: " . $tournament->type);
            Logger::info("Status: " . $tournament->status);

            // Carrega as relações necessárias
        $tournament->load([
                'rounds' => function($query) {
                    $query->orderBy('round_number');
                },
                'rounds.matches' => function($query) {
                    $query->orderBy('scheduled_time');
                },
                'rounds.matches.court',
            'rounds.matches.team1_player1',
            'rounds.matches.team1_player2',
            'rounds.matches.team2_player1',
            'rounds.matches.team2_player2',
            'playerScores.player'
        ]);

            $playerRanking = [];

            // Carrega o ranking
            if ($tournament->type === 'super_12_fixed_pairs') {
                try {
                    Logger::info("Calculando ranking para Super 12 Duplas Fixas");
                    $fixedPairs = [];
                    
                    if (!$tournament->rounds->isEmpty()) {
                        $firstRound = $tournament->rounds->first();
                        
                        if (!$firstRound->matches->isEmpty()) {
                            // Identifica as duplas fixas
                            foreach ($firstRound->matches as $match) {
                                if (!$match->team1_player1 || !$match->team1_player2 || 
                                    !$match->team2_player1 || !$match->team2_player2) {
                                    continue;
                                }

                                $pair1Key = min($match->team1_player1_id, $match->team1_player2_id) . 
                                          '_' . max($match->team1_player1_id, $match->team1_player2_id);
                                $pair2Key = min($match->team2_player1_id, $match->team2_player2_id) . 
                                          '_' . max($match->team2_player1_id, $match->team2_player2_id);

                                if (!isset($fixedPairs[$pair1Key])) {
                                    $fixedPairs[$pair1Key] = [
                                        'player1' => $match->team1_player1,
                                        'player2' => $match->team1_player2,
                                        'points' => 0,
                                        'games_won' => 0,
                                        'games_lost' => 0
                                    ];
                                }

                                if (!isset($fixedPairs[$pair2Key])) {
                                    $fixedPairs[$pair2Key] = [
                                        'player1' => $match->team2_player1,
                                        'player2' => $match->team2_player2,
                                        'points' => 0,
                                        'games_won' => 0,
                                        'games_lost' => 0
                                    ];
                                }
                            }

                            // Calcula pontos
                            foreach ($tournament->rounds as $round) {
                                foreach ($round->matches as $match) {
                                    if ($match->winner_team) {
                                        $team1Key = min($match->team1_player1_id, $match->team1_player2_id) . 
                                                  '_' . max($match->team1_player1_id, $match->team1_player2_id);
                                        $team2Key = min($match->team2_player1_id, $match->team2_player2_id) . 
                                                  '_' . max($match->team2_player1_id, $match->team2_player2_id);

                                        if (isset($fixedPairs[$team1Key]) && isset($fixedPairs[$team2Key])) {
                                            if ($match->winner_team === 'team1') {
                                                $fixedPairs[$team1Key]['points'] = (int)($fixedPairs[$team1Key]['points'] ?? 0) + 2;
                                                $fixedPairs[$team1Key]['games_won'] = (int)($fixedPairs[$team1Key]['games_won'] ?? 0) + 1;
                                                $fixedPairs[$team2Key]['games_lost'] = (int)($fixedPairs[$team2Key]['games_lost'] ?? 0) + 1;
                                                
                                                // Registra para depuração
                                                Logger::info("Partida {$match->id}: Pontos adicionados para dupla {$team1Key} - agora tem {$fixedPairs[$team1Key]['points']} pontos");
                                            } else {
                                                $fixedPairs[$team2Key]['points'] = (int)($fixedPairs[$team2Key]['points'] ?? 0) + 2;
                                                $fixedPairs[$team2Key]['games_won'] = (int)($fixedPairs[$team2Key]['games_won'] ?? 0) + 1;
                                                $fixedPairs[$team1Key]['games_lost'] = (int)($fixedPairs[$team1Key]['games_lost'] ?? 0) + 1;
                                                
                                                // Registra para depuração
                                                Logger::info("Partida {$match->id}: Pontos adicionados para dupla {$team2Key} - agora tem {$fixedPairs[$team2Key]['points']} pontos");
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    
                    if (!empty($fixedPairs)) {
                        // Armazena os resultados dos confrontos diretos para desempate
                        $directMatches = [];
                        
                        // Primeiro, vamos registrar todos os confrontos diretos entre as duplas
                        foreach ($tournament->rounds as $round) {
                            foreach ($round->matches as $match) {
                                if ($match->status === 'completed' && $match->winner_team) {
                                    $team1Key = min($match->team1_player1_id, $match->team1_player2_id) . 
                                              '_' . max($match->team1_player1_id, $match->team1_player2_id);
                                    $team2Key = min($match->team2_player1_id, $match->team2_player2_id) . 
                                              '_' . max($match->team2_player1_id, $match->team2_player2_id);
                                    
                                    // Registra o resultado do confronto direto
                                    if ($match->winner_team === 'team1') {
                                        $directMatches[$team1Key][$team2Key] = true; // team1 venceu team2
                                        $directMatches[$team2Key][$team1Key] = false; // team2 perdeu para team1
                                    } else {
                                        $directMatches[$team1Key][$team2Key] = false; // team1 perdeu para team2
                                        $directMatches[$team2Key][$team1Key] = true; // team2 venceu team1
                                    }
                                }
                            }
                        }
                        
                        // Agora fazemos a ordenação com o critério de desempate por confronto direto
                        uasort($fixedPairs, function($a, $b) use ($directMatches) {
                            $keyA = min($a['player1']->id, $a['player2']->id) . '_' . max($a['player1']->id, $a['player2']->id);
                            $keyB = min($b['player1']->id, $b['player2']->id) . '_' . max($b['player1']->id, $b['player2']->id);
                            
                            // Primeiro critério: pontos
                            $pointsA = intval($a['points']);
                            $pointsB = intval($b['points']);
                            if ($pointsA !== $pointsB) return $pointsB - $pointsA;
                            
                            // Segundo critério: vitórias
                            $winsA = intval($a['games_won']);
                            $winsB = intval($b['games_won']);
                            if ($winsA !== $winsB) return $winsB - $winsA;
                            
                            // Terceiro critério: derrotas (menos derrotas primeiro)
                            $lossesA = intval($a['games_lost']);
                            $lossesB = intval($b['games_lost']);
                            if ($lossesA !== $lossesB) return $lossesA - $lossesB;
                            
                            // Quarto critério: confronto direto
                            if (isset($directMatches[$keyA][$keyB])) {
                                Logger::info("Desempate por confronto direto entre {$keyA} e {$keyB}: " . 
                                             ($directMatches[$keyA][$keyB] ? "{$keyA} venceu" : "{$keyB} venceu"));
                                return $directMatches[$keyA][$keyB] ? -1 : 1;
                            }
                            
                            // Se não houver confronto direto, mantém a ordem atual
                            return 0;
                        });
                        
                        // Adicionando numeração de posição explícita
                        $position = 1;
                        foreach ($fixedPairs as $key => $pair) {
                            $fixedPairs[$key]['position'] = $position++;
                        }
                        
                        // Registre o ranking para depuração
                        Logger::info("Ranking calculado: " . json_encode(array_map(function($pair) {
                            return [
                                'dupla' => $pair['player1']->name . ' & ' . $pair['player2']->name,
                                'pontos' => $pair['points'],
                                'vitórias' => $pair['games_won'],
                                'derrotas' => $pair['games_lost'],
                                'posição' => $pair['position'] ?? 'N/A'
                            ];
                        }, $fixedPairs)));
                    }
                    
                    $playerRanking = $fixedPairs;
                    
                } catch (\Exception $e) {
                    Logger::error("Erro ao calcular ranking de duplas: " . $e->getMessage());
                    $playerRanking = [];
                }
            } else {
                // Ranking individual
        $playerRanking = $tournament->playerScores()
                    ->with('player:id,name')
            ->orderBy('points', 'desc')
            ->orderBy('games_won', 'desc')
            ->orderBy('games_lost', 'asc')
                    ->get();
            }

            // Carrega dados adicionais
            $player = null;
            if ($request->has('player_id')) {
                $player = Player::select('id', 'name', 'email')->findOrFail($request->player_id);
            }

            $availablePlayers = Player::select('id', 'name', 'email')
                ->orderBy('name')
            ->get();

            $selectedPlayers = $tournament->players()
                ->pluck('players.id')
                ->toArray();

            Logger::info("=== Fim do carregamento do torneio ===");

        return view('tournaments.show', compact(
            'tournament',
            'player',
            'playerRanking',
            'availablePlayers',
            'selectedPlayers'
        ));

        } catch (\Exception $e) {
            Logger::error("Erro ao mostrar torneio: " . $e->getMessage());
            Logger::error("File: " . $e->getFile() . ":" . $e->getLine());
            Logger::error("Stack trace: " . $e->getTraceAsString());
            
            return redirect()
                ->route('tournaments.index')
                ->with('error', 'Erro ao carregar o torneio. Por favor, tente novamente.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function generateMatches(Tournament $tournament)
    {
        try {
            if ($tournament->type === 'super_8_individual') {
                $this->generateSuper8Matches($tournament);
            } else {
                $this->generateSuper12Matches($tournament);
            }

            return redirect()
                ->route('tournaments.show', $tournament)
                ->with('success', 'Partidas geradas com sucesso!');
        } catch (\Exception $e) {
            Logger::error("Erro ao gerar partidas: " . $e->getMessage());
            Logger::error($e->getTraceAsString());

            return redirect()
                ->route('tournaments.show', $tournament)
                ->with('error', 'Erro ao gerar partidas. Por favor, tente novamente.');
        }
    }

    private function generateSuper8Matches(Tournament $tournament)
    {
        $logFile = base_path('debug.log');
        $log = function($message) use ($logFile) {
            file_put_contents($logFile, date('[Y-m-d H:i:s] ') . $message . "\n", FILE_APPEND);
        };

        try {
            $log('Iniciando geração de partidas Super 8');

        // Pega todas as quadras ativas
        $courts = $tournament->courts()->where('is_active', true)->get();
        $courtCount = $courts->count();

        if ($courtCount === 0) {
            throw new \Exception('Não há quadras disponíveis para o torneio.');
        }

        // Lista de jogadores
        $players = $tournament->players()->get();
            $playerCount = $players->count();

            if ($playerCount !== 8) {
                throw new \Exception("Número incorreto de jogadores. Necessário: 8, Atual: {$playerCount}");
            }

            // Gera todas as combinações possíveis de rodadas
            $playerIds = $players->pluck('id')->toArray();
            $rounds = $this->generateRoundCombinations($playerIds);
            
            $log("Total de rodadas geradas: " . count($rounds));

            // Cria as rodadas no banco
            foreach ($rounds as $roundNumber => $roundPairs) {
                $log("Criando rodada " . ($roundNumber + 1));
                
                $round = $tournament->rounds()->create([
                    'round_number' => $roundNumber + 1
                ]);

                // Cria as partidas da rodada
                foreach ($roundPairs as $matchIndex => $match) {
                    $courtIndex = $matchIndex % $courtCount;
                    $court = $courts[$courtIndex];

                    $matchTimeOffset = floor($matchIndex / $courtCount) * 2;
                    $scheduledTime = Carbon::now()
                        ->setTime(9, 0)
                        ->addDays($roundNumber)
                        ->addHours($matchTimeOffset);

                    $log("Criando partida - Rodada: " . ($roundNumber + 1) . 
                         ", Time1: {$match[0][0]},{$match[0][1]} vs Time2: {$match[1][0]},{$match[1][1]}");

                $round->matches()->create([
                    'court_id' => $court->id,
                        'team1_player1_id' => $match[0][0],
                        'team1_player2_id' => $match[0][1],
                        'team2_player1_id' => $match[1][0],
                        'team2_player2_id' => $match[1][1],
                    'scheduled_time' => $scheduledTime,
                    'status' => 'scheduled'
                ]);
            }
        }

        // Atualiza o status do torneio
        $tournament->update(['status' => 'in_progress']);
            $log("Torneio atualizado para status: in_progress");

        } catch (\Exception $e) {
            $log("ERRO em generateSuper8Matches: " . $e->getMessage());
            $log("Stack trace:\n" . $e->getTraceAsString());
            throw $e;
        }
    }

    private function generateRoundCombinations($players)
    {
        // Embaralha os jogadores para aumentar a aleatoriedade
        shuffle($players);
        
        $rounds = [];
        $usedPairs = [];
        $usedMatches = [];

        // Precisamos de 7 rodadas
        for ($round = 0; $round < 7; $round++) {
            $roundPairs = [];
            $availablePlayers = $players;

            // Cada rodada tem 2 partidas
            while (count($availablePlayers) >= 4) {
                // Tenta encontrar uma combinação válida de 4 jogadores
                $validCombination = $this->findValidCombination(
                    $availablePlayers,
                    $usedPairs,
                    $usedMatches
                );

                if (!$validCombination) {
                    // Se não encontrou combinação válida, recomeça a geração
                    return $this->generateRoundCombinations($players);
                }

                $pair1 = $validCombination[0];
                $pair2 = $validCombination[1];

                // Registra os pares e a partida
                $usedPairs[] = $pair1;
                $usedPairs[] = $pair2;
                $usedMatches[] = [$pair1, $pair2];

                // Remove os jogadores usados
                $availablePlayers = array_diff($availablePlayers, array_merge($pair1, $pair2));

                $roundPairs[] = [$pair1, $pair2];
            }

            $rounds[] = $roundPairs;
        }

        return $rounds;
    }

    private function findValidCombination($players, $usedPairs, $usedMatches)
    {
        // Tenta todas as combinações possíveis de 4 jogadores
        $attempts = 0;
        $maxAttempts = 100;

        while ($attempts < $maxAttempts) {
            $attempts++;
            
            // Escolhe 4 jogadores aleatórios
            $fourPlayers = array_rand(array_flip($players), 4);
            
            // Tenta todas as combinações possíveis de pares com esses 4 jogadores
            for ($i = 0; $i < 3; $i++) {
                for ($j = $i + 1; $j < 4; $j++) {
                    $pair1 = [$fourPlayers[$i], $fourPlayers[$j]];
                    $remainingPlayers = array_diff($fourPlayers, $pair1);
                    $pair2 = array_values($remainingPlayers);

                    // Verifica se esta combinação é válida
                    if (!$this->pairHasPlayed($pair1, $usedPairs) &&
                        !$this->pairHasPlayed($pair2, $usedPairs) &&
                        !$this->matchHasOccurred($pair1, $pair2, $usedMatches)) {
                        return [$pair1, $pair2];
                    }
                }
            }
        }

        return null;
    }

    private function pairHasPlayed($pair, $usedPairs)
    {
        foreach ($usedPairs as $usedPair) {
            if (($pair[0] == $usedPair[0] && $pair[1] == $usedPair[1]) ||
                ($pair[0] == $usedPair[1] && $pair[1] == $usedPair[0])) {
                return true;
            }
        }
        return false;
    }

    private function matchHasOccurred($pair1, $pair2, $usedMatches)
    {
        foreach ($usedMatches as $match) {
            if ($this->pairsAreEqual($match[0], $pair1) && $this->pairsAreEqual($match[1], $pair2) ||
                $this->pairsAreEqual($match[0], $pair2) && $this->pairsAreEqual($match[1], $pair1)) {
                return true;
            }
        }
        return false;
    }

    private function pairsAreEqual($pair1, $pair2)
    {
        return ($pair1[0] == $pair2[0] && $pair1[1] == $pair2[1]) ||
               ($pair1[0] == $pair2[1] && $pair1[1] == $pair2[0]);
    }

    private function generateSuper12Matches(Tournament $tournament)
    {
        $logFile = base_path('debug.log');
        $log = function($message) use ($logFile) {
            file_put_contents($logFile, date('[Y-m-d H:i:s] ') . $message . "\n", FILE_APPEND);
        };

        try {
            $log('Iniciando geração de partidas Super 12 Duplas Fixas');

            // Pega todas as quadras ativas
            $courts = $tournament->courts()->where('is_active', true)->get();
            $courtCount = $courts->count();
            
            if ($courtCount === 0) {
                throw new \Exception('Não há quadras disponíveis para o torneio.');
            }

            // Lista de jogadores
            $players = $tournament->players()->get();
            $playerCount = $players->count();

            if ($playerCount !== 12) {
                throw new \Exception("Número incorreto de jogadores. Necessário: 12, Atual: {$playerCount}");
            }

            // Criar as 6 duplas fixas
            $playerIds = $players->pluck('id')->toArray();
            shuffle($playerIds);
            
            $fixedPairs = [];
            for ($i = 0; $i < 12; $i += 2) {
                $fixedPairs[] = [
                    $playerIds[$i],
                    $playerIds[$i + 1]
                ];
            }

            $log("Duplas fixas formadas: " . json_encode($fixedPairs));

            // Gerar todas as combinações possíveis de confrontos
            $allMatches = [];
            for ($i = 0; $i < 5; $i++) {
                for ($j = $i + 1; $j < 6; $j++) {
                    $allMatches[] = [$i, $j];
                }
            }

            // Distribuir os 15 jogos em 5 rodadas com 3 jogos cada
            $rounds = [[], [], [], [], []];
            $pairGamesPerRound = array_fill(0, 6, array_fill(0, 5, 0)); // [dupla][rodada] = quantidade de jogos

            // Para cada rodada
            for ($roundNumber = 0; $roundNumber < 5; $roundNumber++) {
                $availableMatches = array_filter($allMatches, function($match) use ($rounds, $roundNumber, $pairGamesPerRound) {
                    $pair1 = $match[0];
                    $pair2 = $match[1];
                    
                    // Verifica se alguma das duplas já joga nesta rodada
                    return $pairGamesPerRound[$pair1][$roundNumber] == 0 && 
                           $pairGamesPerRound[$pair2][$roundNumber] == 0;
                });

                // Pegar 3 jogos para esta rodada
                $roundMatches = [];
                $matchCount = 0;
                
                foreach ($availableMatches as $match) {
                    if ($matchCount >= 3) break;
                    
                    $pair1 = $match[0];
                    $pair2 = $match[1];
                    
                    // Adicionar o jogo à rodada
                    $roundMatches[] = $match;
                    $pairGamesPerRound[$pair1][$roundNumber] = 1;
                    $pairGamesPerRound[$pair2][$roundNumber] = 1;
                    
                    // Remover este jogo dos disponíveis
                    $key = array_search($match, $allMatches);
                    if ($key !== false) {
                        unset($allMatches[$key]);
                    }
                    
                    $matchCount++;
                }
                
                $rounds[$roundNumber] = $roundMatches;
            }

            // Criar as rodadas no banco
            foreach ($rounds as $roundNumber => $matches) {
                $log("Gerando rodada " . ($roundNumber + 1) . " com " . count($matches) . " jogos");
                
                $round = $tournament->rounds()->create([
                    'round_number' => $roundNumber + 1
                ]);

                foreach ($matches as $matchIndex => $match) {
                    $pair1Index = $match[0];
                    $pair2Index = $match[1];

                    $courtIndex = $matchIndex % $courtCount;
                    $court = $courts[$courtIndex];

                    $matchTimeOffset = floor($matchIndex / $courtCount) * 2;
                    $scheduledTime = Carbon::now()
                        ->setTime(9, 0)
                        ->addDays($roundNumber)
                        ->addHours($matchTimeOffset);

                    $log("Criando partida - Rodada: " . ($roundNumber + 1) . 
                         ", Dupla {$pair1Index} vs Dupla {$pair2Index}" .
                         ", Quadra: {$court->id}, Horário: {$scheduledTime->format('H:i')}");

                    $round->matches()->create([
                        'court_id' => $court->id,
                        'team1_player1_id' => $fixedPairs[$pair1Index][0],
                        'team1_player2_id' => $fixedPairs[$pair1Index][1],
                        'team2_player1_id' => $fixedPairs[$pair2Index][0],
                        'team2_player2_id' => $fixedPairs[$pair2Index][1],
                        'scheduled_time' => $scheduledTime,
                        'status' => 'scheduled'
                    ]);
                }
            }

            // Atualiza o status do torneio
            $tournament->update(['status' => 'in_progress']);
            $log("Torneio atualizado para status: in_progress");

        } catch (\Exception $e) {
            $log("ERRO em generateSuper12Matches: " . $e->getMessage());
            $log("Stack trace:\n" . $e->getTraceAsString());
            throw $e;
        }
    }

    public function selectPlayers(Request $request, Tournament $tournament)
    {
        $validated = $request->validate([
            'selected_players' => ['required', 'array'],
            'selected_players.*' => ['exists:players,id']
        ]);

        $playerCount = count($validated['selected_players']);
        $requiredPlayers = $tournament->type === 'super_8_individual' ? 8 : 12;

        if ($playerCount !== $requiredPlayers) {
            return back()->with('error', "Este torneio requer exatamente {$requiredPlayers} jogadores.");
        }

        // Limpa as seleções anteriores
        $tournament->players()->detach();

        // Adiciona os jogadores selecionados
        $tournament->players()->attach($validated['selected_players']);

        // Atualiza o status do torneio
        $tournament->update(['status' => 'open']);

        return back()->with('success', 'Jogadores selecionados com sucesso!');
    }

    public function updateScore(Request $request, $matchId)
    {
        try {
            $gameMatch = GameMatch::findOrFail($matchId);
            
            $validated = $request->validate([
                'team1_score' => 'required|integer|min:0',
                'team2_score' => 'required|integer|min:0'
            ]);

            Logger::info("Atualizando placar da partida {$matchId}: {$validated['team1_score']} x {$validated['team2_score']}");

            if ($validated['team1_score'] === $validated['team2_score']) {
                return back()->with('error', 'O placar não pode ser empate.');
            }

            // Numa partida de beach tennis, quem faz 6 pontos vence
            // Define explicitamente o vencedor com base nas regras do jogo
            if ($validated['team1_score'] >= 6) {
                $winnerTeam = 'team1';
                Logger::info("Time 1 venceu com {$validated['team1_score']} pontos");
            } elseif ($validated['team2_score'] >= 6) {
                $winnerTeam = 'team2';
                Logger::info("Time 2 venceu com {$validated['team2_score']} pontos");
            } else {
                // Se nenhum time atingiu 6 pontos, quem tem mais pontos vence
                $winnerTeam = $validated['team1_score'] > $validated['team2_score'] ? 'team1' : 'team2';
                Logger::info("Vencedor determinado pelo maior placar: {$winnerTeam}");
            }
            
            // Armazena o placar e o vencedor
            $gameMatch->winner_team = $winnerTeam;
            $gameMatch->score_details = "{$validated['team1_score']} x {$validated['team2_score']}";
            $gameMatch->status = 'completed';
            $gameMatch->save();

            Logger::info("Partida {$matchId} atualizada: winner_team={$winnerTeam}, status=completed");

            // Atualiza os pontos dos jogadores
            $this->updatePlayerScores($gameMatch);
            Logger::info("Pontuação dos jogadores atualizada");

            return back()->with('success', 'Placar registrado com sucesso!');
        } catch (\Exception $e) {
            Logger::error("Erro ao atualizar placar: " . $e->getMessage());
            return back()->with('error', 'Erro ao registrar o placar. Por favor, tente novamente.');
        }
    }

    protected function updatePlayerScores(GameMatch $gameMatch)
    {
        $tournament = $gameMatch->round->tournament;
        
        if ($tournament->type === 'super_8_individual') {
            // Atualiza pontos para torneio individual
            if ($gameMatch->winner_team === 'team1') {
                // Time 1 venceu - atualiza todos os jogadores
                $this->updatePlayerScore($tournament, $gameMatch->team1_player1_id, true);
                $this->updatePlayerScore($tournament, $gameMatch->team1_player2_id, true);
                $this->updatePlayerScore($tournament, $gameMatch->team2_player1_id, false);
                $this->updatePlayerScore($tournament, $gameMatch->team2_player2_id, false);
            } else {
                // Time 2 venceu - atualiza todos os jogadores
                $this->updatePlayerScore($tournament, $gameMatch->team2_player1_id, true);
                $this->updatePlayerScore($tournament, $gameMatch->team2_player2_id, true);
                $this->updatePlayerScore($tournament, $gameMatch->team1_player1_id, false);
                $this->updatePlayerScore($tournament, $gameMatch->team1_player2_id, false);
            }
        } else {
            // Atualiza pontos para torneio de duplas
            if ($gameMatch->winner_team === 'team1') {
                $this->updatePairScore($tournament, $gameMatch->team1_player1_id, $gameMatch->team1_player2_id, true);
                $this->updatePairScore($tournament, $gameMatch->team2_player1_id, $gameMatch->team2_player2_id, false);
            } else {
                $this->updatePairScore($tournament, $gameMatch->team2_player1_id, $gameMatch->team2_player2_id, true);
                $this->updatePairScore($tournament, $gameMatch->team1_player1_id, $gameMatch->team1_player2_id, false);
            }
        }
    }

    protected function updatePlayerScore($tournament, $player_id, $isWinner)
    {
        if (!$player_id) return; // Garante que há um jogador válido
        
        $score = $tournament->playerScores()->firstOrCreate(['player_id' => $player_id]);
        
        if ($isWinner) {
            $score->increment('points', 2);
            $score->increment('games_won');
        } else {
            $score->increment('games_lost');
        }
    }

    protected function updatePairScore($tournament, $player1_id, $player2_id, $isWinner)
    {
        $score1 = $tournament->playerScores()->firstOrCreate(['player_id' => $player1_id]);
        $score2 = $tournament->playerScores()->firstOrCreate(['player_id' => $player2_id]);

        if ($isWinner) {
            $score1->increment('points', 2);
            $score2->increment('points', 2);
            $score1->increment('games_won');
            $score2->increment('games_won');
        } else {
            $score1->increment('games_lost');
            $score2->increment('games_lost');
        }
    }
}
