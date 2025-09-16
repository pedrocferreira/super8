<?php

namespace App\Http\Controllers;

use App\Models\Tournament;
use App\Models\GameMatch;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Player;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Pair;

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
            'type' => 'required|in:super_8_doubles,super_8_fixed_pairs,super_12_fixed_pairs,super_12_selected_pairs',
            'number_of_courts' => 'required|integer|min:1|max:10',
        ]);

        // Adiciona as datas automaticamente
        $validated['start_date'] = Carbon::now();
        $validated['end_date'] = Carbon::now();

        // Define o número mínimo e máximo de jogadores com base no tipo
        if ($validated['type'] === 'super_8_doubles') {
            $validated['min_players'] = 8;
            $validated['max_players'] = 8;
        } elseif ($validated['type'] === 'super_8_fixed_pairs') {
            $validated['min_players'] = 16;
            $validated['max_players'] = 16;
        } else {
            // Tanto para super_12_fixed_pairs quanto para super_12_selected_pairs
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
            // Função de log personalizada
            $logInfo = function($message) {
                error_log("[INFO] " . $message);
            };
            
            $logError = function($message) {
                error_log("[ERROR] " . $message);
            };
            
            $logInfo("=== Início do carregamento do torneio ===");
            $logInfo("ID: " . $tournament->id);
            $logInfo("Tipo: " . $tournament->type);
            $logInfo("Status: " . $tournament->status);

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
                                            } else {
                                                $fixedPairs[$team2Key]['points'] = (int)($fixedPairs[$team2Key]['points'] ?? 0) + 2;
                                                $fixedPairs[$team2Key]['games_won'] = (int)($fixedPairs[$team2Key]['games_won'] ?? 0) + 1;
                                                $fixedPairs[$team1Key]['games_lost'] = (int)($fixedPairs[$team1Key]['games_lost'] ?? 0) + 1;
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
                    }
                    
                    $playerRanking = $fixedPairs;
                    
                } catch (\Exception $e) {
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

            $logInfo("=== Fim do carregamento do torneio ===");

            return view('tournaments.show', compact(
                'tournament',
                'player',
                'playerRanking',
                'availablePlayers',
                'selectedPlayers'
            ));

        } catch (\Exception $e) {
            $logError("Erro ao mostrar torneio: " . $e->getMessage());
            $logError("File: " . $e->getFile() . ":" . $e->getLine());
            $logError("Stack trace: " . $e->getTraceAsString());
            
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

    /**
     * Gera partidas para o torneio.
     */
    public function generateMatches(Request $request, Tournament $tournament)
    {
        try {
            error_log("[INFO] Iniciando geração de partidas para torneio {$tournament->id} - {$tournament->name} do tipo {$tournament->type}");
            
            // Verificar tipo de torneio e chamar método correspondente
            if ($tournament->type === 'super_8_doubles') {
                $this->generateSuper8Matches($tournament);
            } elseif ($tournament->type === 'super_8_fixed_pairs') {
                $this->generateSuper8FixedPairsMatches($tournament);
            } elseif ($tournament->type === 'super_12_fixed_pairs') {
                $this->generateSuper12Matches($tournament);
            } elseif ($tournament->type === 'super_12_selected_pairs') {
                // Verificar se as duplas foram definidas
                $pairsCount = Pair::where('tournament_id', $tournament->id)->count();
                if ($pairsCount < 6) {
                    return back()->with('error', 'Você precisa definir as 6 duplas antes de gerar as partidas.');
                }
                
                $this->generateSuper12SelectedPairsMatches($tournament);
            } else {
                throw new \Exception("Tipo de torneio não suportado: {$tournament->type}");
            }
            
            // Atualiza o status do torneio
            $tournament->update(['status' => 'in_progress']);
            
            return redirect()->route('tournaments.show', $tournament)
                ->with('success', 'Partidas geradas com sucesso!');
        } catch (\Exception $e) {
            error_log("[ERROR] Erro ao gerar partidas: " . $e->getMessage());
            error_log("[ERROR] Stack trace: " . $e->getTraceAsString());
            
            return redirect()->route('tournaments.show', $tournament)
                ->with('error', 'Erro ao gerar partidas: ' . $e->getMessage());
        }
    }

    /**
     * Gera partidas para torneio Super 8 Individual
     */
    private function generateSuper8Matches(Tournament $tournament)
    {
        try {
            error_log('[INFO] Iniciando geração de partidas Super 8 - LÓGICA FINAL CORRETA');

            $courts = $tournament->courts()->where('is_active', true)->get();
            if ($courts->count() < 1) {
                throw new \Exception('É necessária pelo menos 1 quadra para este tipo de torneio.');
            }

            $players = $tournament->players()->get()->pluck('id')->toArray();
            if (count($players) !== 8) {
                throw new \Exception("Número incorreto de jogadores. Necessário: 8, Atual: " . count($players));
            }

            // Matriz SUPER 8 - Cada jogador joga com cada um dos outros 7 como parceiro, e contra cada um dos outros 6 como adversário
            $schedule = [
                // Rodada 1
                [ [[0, 1], [2, 3]], [[4, 5], [6, 7]] ],
                // Rodada 2  
                [ [[0, 2], [1, 4]], [[3, 5], [6, 7]] ],
                // Rodada 3
                [ [[0, 3], [1, 5]], [[2, 4], [6, 7]] ],
                // Rodada 4
                [ [[0, 4], [1, 6]], [[2, 5], [3, 7]] ],
                // Rodada 5
                [ [[0, 5], [1, 7]], [[2, 6], [3, 4]] ],
                // Rodada 6
                [ [[0, 6], [2, 7]], [[1, 3], [4, 5]] ],
                // Rodada 7
                [ [[0, 7], [3, 6]], [[1, 2], [4, 5]] ],
            ];

            $match_day = 0;
            foreach ($schedule as $round_index => $round_matches) {
                $round = $tournament->rounds()->create([
                    'round_number' => $round_index + 1
                ]);

                foreach ($round_matches as $match_index => $match_setup) {
                    $court = $courts[$match_index % $courts->count()];
                    $scheduledTime = Carbon::now()->setTime(9, 0)->addDays($match_day)->addHours($match_index);

                    $team1 = $match_setup[0];
                    $team2 = $match_setup[1];

                    $round->matches()->create([
                        'court_id' => $court->id,
                        'team1_player1_id' => $players[$team1[0]],
                        'team1_player2_id' => $players[$team1[1]],
                        'team2_player1_id' => $players[$team2[0]],
                        'team2_player2_id' => $players[$team2[1]],
                        'scheduled_time' => $scheduledTime,
                        'status' => 'scheduled'
                    ]);
                }
                $match_day++;
            }

            error_log('[INFO] Geração de partidas Super 8 (14 jogos - LÓGICA FINAL) concluída com sucesso');

        } catch (\Exception $e) {
            error_log('[ERROR] ' . $e->getMessage());
            error_log('[ERROR] Stack trace: ' . $e->getTraceAsString());
            throw $e;
        }
    }

    /**
     * Gera partidas para torneio Super 8 com duplas fixas (8 duplas, cada dupla joga 7 jogos)
     */
    private function generateSuper8FixedPairsMatches(Tournament $tournament)
    {
        try {
            error_log('[INFO] Iniciando geração de partidas Super 8 Duplas Fixas');

            $courts = $tournament->courts()->where('is_active', true)->get();
            if ($courts->count() < 1) {
                throw new \Exception('É necessária pelo menos 1 quadra para este tipo de torneio.');
            }

            $players = $tournament->players()->get();
            if ($players->count() !== 16) {
                throw new \Exception("Número incorreto de jogadores. Necessário: 16, Atual: " . $players->count());
            }

            // Formar 8 duplas fixas (em ordem sequencial)
            $pairs = [];
            for ($i = 0; $i < 16; $i += 2) {
                $pairs[] = [
                    $players[$i]->id,
                    $players[$i + 1]->id
                ];
            }

            error_log('[INFO] 8 duplas fixas formadas: ' . json_encode($pairs));

            // Criar todas as combinações possíveis entre as 8 duplas
            // Cada dupla joga contra as outras 7 = 8*7/2 = 28 jogos total
            // Distribuídos em 7 rodadas com 4 jogos cada
            $schedule = [];
            
            // Algoritmo round-robin para 8 duplas
            // Duplas numeradas de 0 a 7
            for ($round = 0; $round < 7; $round++) {
                $roundMatches = [];
                
                // Para cada rodada, cada dupla joga exatamente uma vez
                $usedPairs = [];
                
                for ($pair1 = 0; $pair1 < 8; $pair1++) {
                    if (in_array($pair1, $usedPairs)) continue;
                    
                    // Encontrar um oponente que ainda não jogou nesta rodada
                    for ($pair2 = $pair1 + 1; $pair2 < 8; $pair2++) {
                        if (in_array($pair2, $usedPairs)) continue;
                        
                        // Verificar se essas duplas ainda não se enfrentaram
                        $alreadyPlayed = false;
                        foreach ($schedule as $previousRound) {
                            foreach ($previousRound as $match) {
                                if (($match[0] == $pair1 && $match[1] == $pair2) || 
                                    ($match[0] == $pair2 && $match[1] == $pair1)) {
                                    $alreadyPlayed = true;
                                    break 2;
                                }
                            }
                        }
                        
                        if (!$alreadyPlayed) {
                            $roundMatches[] = [$pair1, $pair2];
                            $usedPairs[] = $pair1;
                            $usedPairs[] = $pair2;
                            break;
                        }
                    }
                }
                
                $schedule[] = $roundMatches;
            }

            error_log('[INFO] Schedule criado: ' . json_encode($schedule));

            // Criar as partidas no banco de dados
            $match_day = 0;
            foreach ($schedule as $round_index => $round_matches) {
                $round = $tournament->rounds()->create([
                    'round_number' => $round_index + 1
                ]);

                foreach ($round_matches as $match_index => $match_setup) {
                    $court = $courts[$match_index % $courts->count()];
                    $scheduledTime = Carbon::now()->setTime(9, 0)->addDays($match_day)->addHours($match_index);

                    $pair1_index = $match_setup[0];
                    $pair2_index = $match_setup[1];
                    
                    $pair1 = $pairs[$pair1_index];
                    $pair2 = $pairs[$pair2_index];
                    
                    $round->matches()->create([
                        'court_id' => $court->id,
                        'team1_player1_id' => $pair1[0],
                        'team1_player2_id' => $pair1[1],
                        'team2_player1_id' => $pair2[0],
                        'team2_player2_id' => $pair2[1],
                        'scheduled_time' => $scheduledTime,
                        'status' => 'scheduled'
                    ]);
                }
                $match_day++;
            }
            
            error_log('[INFO] Geração de partidas Super 8 Duplas Fixas concluída com sucesso');
            
        } catch (\Exception $e) {
            error_log('[ERROR] ' . $e->getMessage());
            error_log('[ERROR] Stack trace: ' . $e->getTraceAsString());
            throw $e;
        }
    }

    /**
     * Gera partidas para torneio Super 12 com duplas fixas (sorteadas)
     */
    private function generateSuper12Matches(Tournament $tournament)
    {
        try {
            error_log('[INFO] Iniciando geração de partidas Super 12 Duplas Fixas');

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

            // Embaralha os jogadores aleatoriamente
            $shuffledPlayers = $players->shuffle()->values();
            
            // Formar duplas aleatoriamente
            $pairs = [];
            for ($i = 0; $i < 6; $i++) {
                $player1 = $shuffledPlayers[2 * $i];
                $player2 = $shuffledPlayers[2 * $i + 1];
                
                $pairs[] = [
                    'player1' => $player1,
                    'player2' => $player2
                ];
                
                // Salvar a dupla no banco
                Pair::create([
                    'tournament_id' => $tournament->id,
                    'player1_id' => $player1->id,
                    'player2_id' => $player2->id
                ]);
            }
            
            error_log('[INFO] 6 duplas formadas aleatoriamente');

            // Gerar todas as combinações possíveis de confrontos (15 jogos)
            $allMatches = [];
            for ($i = 0; $i < 5; $i++) {
                for ($j = $i + 1; $j < 6; $j++) {
                    $allMatches[] = [$i, $j];
                }
            }
            
            error_log('[INFO] Total de confrontos possíveis: 15');

            // Distribuir os 15 jogos em 5 rodadas com 3 jogos cada
            $rounds = [[], [], [], [], []];
            $pairGamesPerRound = array_fill(0, 6, array_fill(0, 5, 0)); // [dupla][rodada] = quantidade de jogos

            // Para cada rodada
            for ($roundNumber = 0; $roundNumber < 5; $roundNumber++) {
                $availableMatches = array_filter($allMatches, function($match) use ($pairGamesPerRound, $roundNumber) {
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
                error_log('[INFO] Rodada ' . ($roundNumber + 1) . ' configurada com ' . count($roundMatches) . ' partidas');
            }

            // Criar as rodadas no banco
            foreach ($rounds as $roundNumber => $matches) {
                error_log('[INFO] Criando rodada ' . ($roundNumber + 1) . ' no banco de dados');
                
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

                    $pair1 = $pairs[$pair1Index];
                    $pair2 = $pairs[$pair2Index];

                    error_log('[INFO] Criando partida: Dupla ' . $pair1['player1']->name . '/' . $pair1['player2']->name . 
                         ' vs ' . $pair2['player1']->name . '/' . $pair2['player2']->name);

                    $round->matches()->create([
                        'court_id' => $court->id,
                        'team1_player1_id' => $pair1['player1']->id,
                        'team1_player2_id' => $pair1['player2']->id,
                        'team2_player1_id' => $pair2['player1']->id,
                        'team2_player2_id' => $pair2['player2']->id,
                        'scheduled_time' => $scheduledTime,
                        'status' => 'scheduled'
                    ]);
                }
            }

            error_log('[INFO] Geração de partidas Super 12 Duplas Fixas concluída com sucesso');

        } catch (\Exception $e) {
            error_log('[ERROR] ' . $e->getMessage());
            error_log('[ERROR] Stack trace: ' . $e->getTraceAsString());
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
        $requiredPlayers = match($tournament->type) {
            'super_8_doubles' => 8,
            'super_8_fixed_pairs' => 16,
            default => 12
        };

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

    public function selectPairs(Request $request, Tournament $tournament)
    {
        $validated = $request->validate([
            'pairs' => ['required', 'array', 'size:8'],
            'pairs.*' => ['required', 'array', 'size:2'],
            'pairs.*.*' => ['exists:players,id']
        ]);

        // Verificar se temos exatamente 8 duplas com 2 jogadores cada
        if (count($validated['pairs']) !== 8) {
            return back()->with('error', 'É necessário formar exatamente 8 duplas.');
        }

        // Verificar se todos os jogadores são únicos
        $allPlayers = [];
        foreach ($validated['pairs'] as $pair) {
            foreach ($pair as $playerId) {
                if (in_array($playerId, $allPlayers)) {
                    return back()->with('error', 'Um jogador não pode estar em mais de uma dupla.');
                }
                $allPlayers[] = $playerId;
            }
        }

        if (count($allPlayers) !== 16) {
            return back()->with('error', 'É necessário selecionar exatamente 16 jogadores únicos.');
        }

        // Limpa as seleções anteriores
        $tournament->players()->detach();

        // Adiciona os jogadores selecionados (preserva a ordem das duplas)
        $playersToAttach = [];
        foreach ($validated['pairs'] as $pairIndex => $pair) {
            foreach ($pair as $playerId) {
                $playersToAttach[] = $playerId;
            }
        }
        
        $tournament->players()->attach($playersToAttach);

        // Atualiza o status do torneio
        $tournament->update(['status' => 'open']);

        return back()->with('success', 'Duplas formadas com sucesso!');
    }

    public function updateScore(Request $request, $matchId)
    {
        try {
            $gameMatch = GameMatch::findOrFail($matchId);
            
            $validated = $request->validate([
                'team1_score' => 'required|integer|min:0',
                'team2_score' => 'required|integer|min:0'
            ]);

            if ($validated['team1_score'] === $validated['team2_score']) {
                return back()->with('error', 'O placar não pode ser empate.');
            }

            // Numa partida de beach tennis, quem faz 6 pontos vence
            // Define explicitamente o vencedor com base nas regras do jogo
            if ($validated['team1_score'] >= 6) {
                $winnerTeam = 'team1';
            } elseif ($validated['team2_score'] >= 6) {
                $winnerTeam = 'team2';
            } else {
                // Se nenhum time atingiu 6 pontos, quem tem mais pontos vence
                $winnerTeam = $validated['team1_score'] > $validated['team2_score'] ? 'team1' : 'team2';
            }
            
            // Armazena o placar e o vencedor
            $gameMatch->winner_team = $winnerTeam;
            $gameMatch->score_details = "{$validated['team1_score']} x {$validated['team2_score']}";
            $gameMatch->status = 'completed';
            $gameMatch->save();

            // Atualiza os pontos dos jogadores
            $this->updatePlayerScores($gameMatch);

            return back()->with('success', 'Placar registrado com sucesso!');
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao registrar o placar. Por favor, tente novamente.');
        }
    }

    protected function updatePlayerScores(GameMatch $gameMatch)
    {
        $tournament = $gameMatch->round->tournament;
        
        if ($tournament->type === 'super_8_doubles' || $tournament->type === 'super_8_fixed_pairs') {
            // Atualiza pontos para torneio de duplas
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

    /**
     * Exibe o formulário para seleção de duplas.
     */
    public function selectPairsForm(Tournament $tournament)
    {
        // Verificar se o torneio é do tipo correto
        if ($tournament->type !== 'super_12_selected_pairs') {
            return redirect()->route('tournaments.show', $tournament)
                ->with('error', 'Este torneio não suporta seleção manual de duplas.');
        }

        // Obter todos os jogadores selecionados para o torneio
        $players = $tournament->players()->get();
        
        // Verificar se há jogadores suficientes
        if ($players->count() < 12) {
            return redirect()->route('tournaments.show', $tournament)
                ->with('error', 'Este torneio precisa ter 12 jogadores selecionados antes de definir as duplas.');
        }
        
        // Verificar se já existem pares formados
        $existingPairs = Pair::where('tournament_id', $tournament->id)->get()
            ->pluck('player2_id', 'player1_id')->toArray();

        return view('tournaments.select-pairs', compact('tournament', 'players', 'existingPairs'));
    }

    /**
     * Armazena as duplas selecionadas.
     */
    public function storePairs(Request $request, Tournament $tournament)
    {
        try {
            if ($tournament->type === 'super_8_fixed_pairs') {
                // Validação para Super 8 Duplas Fixas: 8 duplas, pares indexados [i][0],[i][1]
                $validated = $request->validate([
                    'pairs' => ['required', 'array', 'size:8'],
                    'pairs.*' => ['required', 'array', 'size:2'],
                    'pairs.*.*' => ['required', 'exists:players,id']
                ]);

                // Verificar unicidade (16 jogadores únicos)
                $allPlayers = [];
                foreach ($validated['pairs'] as $pair) {
                    foreach ($pair as $playerId) {
                        $allPlayers[] = (int) $playerId;
                    }
                }
                if (count($allPlayers) !== 16 || count(array_unique($allPlayers)) !== 16) {
                    return back()->with('error', 'É necessário selecionar 16 jogadores únicos para formar 8 duplas.');
                }

                DB::beginTransaction();

                // Persistir pares (sobrescreve anteriores)
                Pair::where('tournament_id', $tournament->id)->delete();
                foreach ($validated['pairs'] as $pair) {
                    Pair::create([
                        'tournament_id' => $tournament->id,
                        'player1_id' => $pair[0],
                        'player2_id' => $pair[1],
                    ]);
                }

                // Atualizar relação jogadores do torneio na ordem enviada
                $tournament->players()->detach();
                $tournament->players()->attach($allPlayers);

                $tournament->update(['status' => 'open']);

                DB::commit();

                return redirect()->route('tournaments.show', $tournament)
                    ->with('success', 'Duplas (Super 8) definidas com sucesso!');
            } else {
                // Validação anterior (ex.: Super 12 duplas selecionadas): 6 duplas nomeadas
                $validated = $request->validate([
                    'pairs' => 'required|array|size:6',
                    'pairs.*' => 'required|array',
                    'pairs.*.player1' => 'required|exists:players,id',
                    'pairs.*.player2' => 'required|exists:players,id',
                ]);

                // Verificar por jogadores duplicados
                $allPlayers = [];
                foreach ($validated['pairs'] as $pair) {
                    $allPlayers[] = (int) $pair['player1'];
                    $allPlayers[] = (int) $pair['player2'];
                }
                if (count($allPlayers) !== count(array_unique($allPlayers))) {
                    return back()->with('error', 'Um jogador não pode estar em mais de uma dupla.');
                }

                DB::beginTransaction();

                Pair::where('tournament_id', $tournament->id)->delete();
                foreach ($validated['pairs'] as $pair) {
                    Pair::create([
                        'tournament_id' => $tournament->id,
                        'player1_id' => $pair['player1'],
                        'player2_id' => $pair['player2'],
                    ]);
                }

                DB::commit();

                return redirect()->route('tournaments.show', $tournament)
                    ->with('success', 'Duplas definidas com sucesso!');
            }
        } catch (\Exception $e) {
            if (DB::transactionLevel() > 0) DB::rollBack();
            return back()->with('error', 'Erro ao definir as duplas: ' . $e->getMessage());
        }
    }

    /**
     * Gera partidas para torneio Super 12 com duplas pré-selecionadas
     */
    private function generateSuper12SelectedPairsMatches(Tournament $tournament)
    {
        try {
            // Verificar se as duplas foram selecionadas
            $pairs = Pair::where('tournament_id', $tournament->id)->get();
            
            if ($pairs->count() !== 6) {
                throw new \Exception("Número incorreto de duplas. Necessário: 6, Encontrado: {$pairs->count()}");
            }
            
            // Obter as quadras disponíveis
            $courts = $tournament->courts()->where('is_active', true)->get();
            $courtCount = $courts->count();
            
            if ($courtCount === 0) {
                throw new \Exception('Não há quadras disponíveis para o torneio.');
            }

            // Gerar todas as combinações possíveis de confrontos (15 jogos)
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
                $availableMatches = array_filter($allMatches, function($match) use ($pairGamesPerRound, $roundNumber) {
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

                    $pair1 = $pairs[$pair1Index];
                    $pair2 = $pairs[$pair2Index];

                    $round->matches()->create([
                        'court_id' => $court->id,
                        'team1_player1_id' => $pair1->player1_id,
                        'team1_player2_id' => $pair1->player2_id,
                        'team2_player1_id' => $pair2->player1_id,
                        'team2_player2_id' => $pair2->player2_id,
                        'scheduled_time' => $scheduledTime,
                        'status' => 'scheduled'
                    ]);
                }
            }

        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Exibe as duplas de um torneio.
     */
    public function showPairs(Tournament $tournament)
    {
        $pairs = Pair::where('tournament_id', $tournament->id)
            ->with(['player1', 'player2'])
            ->get();
        
        return view('tournaments.show-pairs', compact('tournament', 'pairs'));
    }

    /**
     * Gerar duplas automaticamente (aleatoriamente)
     */
    public function generateRandomPairs(Tournament $tournament)
    {
        try {
            // Verificar o tipo de torneio
            if ($tournament->type !== 'super_12_selected_pairs') {
                return back()->with('error', 'Esta função só está disponível para torneios Super 12 com Duplas Pré-selecionadas.');
            }
            
            // Pegar todos os jogadores
            $players = $tournament->players()->get();
            
            // Verificar se há 12 jogadores
            if ($players->count() !== 12) {
                return back()->with('error', 'É necessário ter exatamente 12 jogadores selecionados.');
            }
            
            // Embaralhar os jogadores
            $shuffledPlayers = $players->shuffle();
            
            // Iniciar uma transação
            DB::beginTransaction();
            
            // Remover pares existentes
            Pair::where('tournament_id', $tournament->id)->delete();
            
            // Criar 6 duplas aleatórias
            for ($i = 0; $i < 6; $i++) {
                $player1 = $shuffledPlayers[2 * $i];
                $player2 = $shuffledPlayers[2 * $i + 1];
                
                Pair::create([
                    'tournament_id' => $tournament->id,
                    'player1_id' => $player1->id,
                    'player2_id' => $player2->id,
                ]);
            }
            
            // Confirmar transação
            DB::commit();
            
            return redirect()->route('tournaments.show-pairs', $tournament)
                ->with('success', 'Duplas geradas aleatoriamente com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao gerar duplas: ' . $e->getMessage());
        }
    }
}
