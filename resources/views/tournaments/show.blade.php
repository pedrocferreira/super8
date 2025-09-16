<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $tournament->name }}
            </h2>
            <div class="flex space-x-4">
                <a href="{{ route('tournaments.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    Voltar
                </a>
                @if($tournament->status === 'draft')
                    <button type="button"
                            onclick="openSelectPlayersModal()"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                        Etapa 1: Selecionar Jogadores
                    </button>
                @elseif($tournament->status === 'open')
                    @if($tournament->type === 'super_12_selected_pairs')
                        <a href="{{ route('tournaments.select-pairs-form', $tournament) }}"
                           class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                            Etapa 2: Definir Duplas
                        </a>
                    @endif
                    <button type="button"
                            onclick="openGenerateMatchesModal()"
                            class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-700">
                        Etapa {{ $tournament->type === 'super_12_selected_pairs' ? '3' : '2' }}: Gerar Partidas
                    </button>
                @endif
                @if($tournament->type === 'super_12_fixed_pairs' || $tournament->type === 'super_12_selected_pairs')
                    <a href="{{ route('tournaments.show-pairs', $tournament) }}"
                       class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700">
                        Ver Duplas
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Mensagens de alerta -->
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif
            
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif
            
            <!-- Informações Básicas -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informações do Torneio</h3>
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Local:</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $tournament->location }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Tipo:</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $tournament->type === 'super_8_doubles' ? 'Super 8 Duplas' : 
                                   ($tournament->type === 'super_8_fixed_pairs' ? 'Super 8 Duplas Fixas' : 'Super 12 Duplas Fixas') }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Status:</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $tournament->status }}</dd>
                        </div>
                    </dl>

                    @if($tournament->registration_code && $tournament->registration_open)
                        <div class="mt-6 p-4 bg-indigo-50 rounded-lg">
                            <h4 class="text-sm font-medium text-indigo-800 mb-2">Link de Inscrição</h4>
                            <p class="text-sm text-indigo-600 mb-3">
                                Compartilhe este link com os jogadores para que eles possam se inscrever no torneio:
                            </p>
                            <div class="flex items-center space-x-2">
                                <input type="text" 
                                       value="{{ route('tournament.register', $tournament->registration_code) }}" 
                                       readonly
                                       class="flex-1 block w-full rounded-md border-indigo-300 bg-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <button onclick="copyRegistrationLink(this)" 
                                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Copiar Link
                                </button>
                            </div>
                            <div class="mt-2 text-sm text-indigo-600">
                                Inscritos: {{ $tournament->players()->count() }}/{{ $tournament->max_players }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Ranking -->
            @if($tournament->type === 'super_12_fixed_pairs')
                <!-- Ranking de Duplas -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-4">
                    <div class="p-6 text-gray-900">
                        <h2 class="text-lg font-semibold mb-4">Ranking de Duplas</h2>
                        @if(empty($playerRanking))
                            <p class="text-gray-500">
                                @if($tournament->rounds->isEmpty())
                                    Nenhuma rodada criada ainda.
                                @elseif($tournament->rounds->first()->matches->isEmpty())
                                    Nenhuma partida criada ainda.
                                @else
                                    Nenhuma partida registrada ainda.
                                @endif
                            </p>
                        @else
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead>
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Posição</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dupla</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pontos</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">V/D</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($playerRanking as $pair)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                    {{ $pair['position'] ?? loop->iteration }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $pair['player1']->name }} & {{ $pair['player2']->name }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $pair['points'] ?? 0 }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $pair['games_won'] ?? 0 }}/{{ $pair['games_lost'] ?? 0 }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <!-- Ranking Individual -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Ranking</h3>
                        @if(!is_object($playerRanking) || count($playerRanking) === 0)
                            <p class="text-gray-500">Nenhuma partida registrada ainda.</p>
                        @else
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Posição</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jogador</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pontos</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">V/D</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($playerRanking as $position => $score)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $position + 1 }}º
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                    {{ $score->player->name }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $score->points }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $score->games_won }}/{{ $score->games_lost }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Rodadas -->
            @if($tournament->rounds->isNotEmpty())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
                                <div class="p-6">
                                    <h3 class="text-lg font-medium text-gray-900 mb-4">Rodadas</h3>
                                    @foreach($tournament->rounds as $round)
                                        <div class="mb-8 last:mb-0">
                                            <h4 class="font-medium text-gray-700 mb-4">Rodada {{ $round->round_number }}</h4>

                                            @php
                                                $matchesByCourt = $round->matches->groupBy('court_id');
                                            @endphp

                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                                @foreach($matchesByCourt as $courtId => $matches)
                                                    <div class="border rounded-lg p-4">
                                                        <h5 class="font-medium text-indigo-600 mb-3">
                                                            {{ $matches->first()->court->name }}
                                                        </h5>
                                                        <div class="space-y-4">
                                                            @foreach($matches as $match)
                                                    <div class="border rounded p-3 {{ $match->status === 'completed' ? 'bg-green-50' : 'bg-white' }}">
                                                                    <div class="flex justify-between items-center mb-2">
                                                                        <span class="text-sm text-gray-500">
                                                                            {{ $match->scheduled_time ? $match->scheduled_time->format('H:i') : 'Horário não definido' }}
                                                                            </span>
                                                                            @if($match->status !== 'completed')
                                                                                <button type="button"
                                                                        class="text-sm text-indigo-600 hover:text-indigo-900"
                                                                        onclick="openScoreModal({{ json_encode($match) }})">
                                                                                    Registrar Placar
                                                                                </button>
                                                                            @endif
                                                                        </div>

                                                                    <div class="flex justify-between items-center">
                                                                        <div class="flex-1">
                                                                            <p class="font-medium">
                                @if($tournament->type === 'super_8_doubles' || $tournament->type === 'super_8_fixed_pairs')
                                    {{ $match->team1_player1->name }} /
                                    {{ $match->team1_player2->name }}
                                @else
                                    {{ $match->team1_player1->name }}
                                @endif
                                                                            </p>
                                                                        </div>
                                                                        <div class="mx-4 text-gray-500">vs</div>
                                                                        <div class="flex-1 text-right">
                                                                            <p class="font-medium">
                                                                                @if($tournament->type === 'super_8_doubles' || $tournament->type === 'super_8_fixed_pairs')
                                                                                    {{ $match->team2_player1->name }} /
                                                                                    {{ $match->team2_player2->name }}
                                                                                @else
                                                                                    {{ $match->team2_player1->name }}
                                                                                @endif
                                                                            </p>
                                                                        </div>
                                                                    </div>

                                                                    @if($match->score_details)
                                                                        <div class="mt-2 text-sm text-gray-500 text-center">
                                                                            Placar: {{ $match->score_details }}
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Modal de Seleção de Jogadores -->
    <div id="selectPlayersModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-10 mx-auto p-6 border shadow-lg rounded-md bg-white {{ $tournament->type === 'super_8_fixed_pairs' ? 'w-4/5 max-w-4xl' : 'w-96' }}">
            <div class="mt-3">
                @if($tournament->type === 'super_8_fixed_pairs')
                    <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Formar as 8 Duplas</h3>
                    <form id="pairsForm" action="{{ route('tournaments.store-pairs', $tournament) }}" method="POST">
                        @csrf
                        <div class="mt-2">
                            <p class="text-sm text-gray-500 mb-4">
                                Selecione os jogadores e organize-os em 8 duplas. Arraste os jogadores para formar as duplas.
                            </p>

                            <!-- Barra de ferramentas -->
                            <div class="mb-4 flex flex-col gap-3">
                                <div class="flex items-center gap-3">
                                    <div class="flex-1">
                                        <input id="playerSearch" type="text" placeholder="Buscar jogador pelo nome..." class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                                    </div>
                                    <button type="button" id="randomizePairsBtn" class="px-3 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700">Randomizar</button>
                                    <button type="button" id="autoCompletePairsBtn" class="px-3 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700">Auto completar</button>
                                    <button type="button" id="clearPairsBtn" class="px-3 py-2 text-sm font-medium text-white bg-gray-600 rounded-md hover:bg-gray-700">Limpar</button>
                                </div>
                                <div class="flex items-center justify-between">
                                    <div class="text-sm text-gray-600">
                                        <span id="selectedCount" class="font-medium">Selecionados: 0/16</span>
                                        <span class="mx-2">•</span>
                                        <span id="pairsCount" class="font-medium">Duplas completas: 0/8</span>
                                    </div>
                                    <label class="inline-flex items-center gap-2 text-sm text-gray-700 cursor-pointer select-none">
                                        <input id="clickModeToggle" type="checkbox" class="h-4 w-4 text-indigo-600 border-gray-300 rounded" checked>
                                        Modo clique para formar duplas
                                    </label>
                                </div>
                            </div>
                            
                            <!-- Lista de jogadores disponíveis -->
                            <div class="mb-6">
                                <h4 class="text-md font-medium text-gray-700 mb-2">Jogadores Disponíveis</h4>
                                <div id="available-players" class="min-h-20 p-3 border-2 border-dashed border-gray-300 rounded-lg bg-gray-50">
                                    @foreach($availablePlayers as $player)
                                        <div class="player-item inline-block m-1 px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm cursor-move border border-blue-200 hover:bg-blue-200 transition-colors" 
                                             data-player-id="{{ $player->id }}" 
                                             draggable="true">
                                            {{ $player->name }}
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- 8 Duplas -->
                            <div class="grid grid-cols-2 gap-4">
                                @for($i = 1; $i <= 8; $i++)
                                    <div class="pair-container">
                                        <h5 class="text-sm font-medium text-gray-600 mb-2">Dupla {{ $i }}</h5>
                                        <div class="pair-dropzone min-h-16 p-3 border-2 border-dashed border-gray-300 rounded-lg bg-white hover:border-blue-400 transition-colors" 
                                             data-pair="{{ $i }}">
                                            <div class="pair-players flex flex-wrap gap-1">
                                                <!-- Jogadores serão adicionados aqui via JavaScript -->
                                            </div>
                                        </div>
                                    </div>
                                @endfor
                            </div>

                            <!-- Hidden inputs para enviar as duplas -->
                            <div id="pairs-input-container">
                                <!-- Inputs serão gerados via JavaScript -->
                            </div>
                        </div>
                @else
                    <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Selecionar Jogadores</h3>
                    <form action="{{ route('tournaments.select-players', $tournament) }}" method="POST">
                        @csrf
                        <div class="mt-2">
                            <p class="text-sm text-gray-500 mb-4">
                                Selecione {{ $tournament->type === 'super_8_doubles' ? '8' : '12' }} jogadores para o torneio.
                            </p>
                            <div class="max-h-60 overflow-y-auto">
                            @foreach($availablePlayers as $player)
                                    <div class="flex items-center mb-2">
                                    <input type="checkbox"
                                               id="player_{{ $player->id }}" 
                                           name="selected_players[]"
                                           value="{{ $player->id }}"
                                           {{ in_array($player->id, $selectedPlayers) ? 'checked' : '' }}
                                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                        <label for="player_{{ $player->id }}" class="ml-2 block text-sm text-gray-900">
                                            {{ $player->name }}
                                        </label>
                                    </div>
                                @endforeach
                                </div>
                        </div>
                @endif
                    <div class="flex justify-end mt-4 space-x-3">
                        <button type="button"
                                onclick="document.getElementById('selectPlayersModal').classList.add('hidden')"
                                class="inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Cancelar
                        </button>
                        <button id="selectPlayersConfirmBtn" type="submit" form="{{ $tournament->type === 'super_8_fixed_pairs' ? 'pairsForm' : '' }}" {{ $tournament->type === 'super_8_fixed_pairs' ? 'disabled' : '' }}
                                class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md disabled:opacity-50 disabled:cursor-not-allowed hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Confirmar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal de Gerar Partidas -->
    <div id="generateMatchesModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Gerar Partidas</h3>
                <form action="{{ route('tournaments.generate-matches', $tournament) }}" method="POST">
                    @csrf
                    <div class="mt-2">
                        <p class="text-sm text-gray-500 mb-4">
                        Tem certeza que deseja gerar as partidas para este torneio?
                            @if($tournament->type === 'super_8_doubles')
                                Serão geradas partidas para o formato Super 8 Duplas.
                            @elseif($tournament->type === 'super_8_fixed_pairs')
                                Serão geradas partidas para o formato Super 8 Duplas Fixas.
                            @elseif($tournament->type === 'super_12_fixed_pairs')
                                Serão geradas partidas para o formato Super 12 Duplas Sorteadas.
                            @elseif($tournament->type === 'super_12_selected_pairs')
                                Serão geradas partidas para o formato Super 12 Duplas Pré-selecionadas.
                            @endif
                    </p>
                </div>
                <div class="flex justify-end mt-4 space-x-3">
                        <button type="button"
                                onclick="closeGenerateMatchesModal()"
                                class="inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Cancelar
                    </button>
                        <button type="submit"
                                class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-emerald-600 border border-transparent rounded-md hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                            Confirmar
                        </button>
                    </div>
                    </form>
                </div>
        </div>
    </div>

    <!-- Modal de Registrar Placar -->
    <div id="scoreModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Registrar Placar</h3>
                <form id="scoreForm" action="" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mt-2">
                        <div class="mb-4">
                            <p class="text-sm font-medium text-gray-700 mb-2">Time 1</p>
                            <div id="team1Names" class="text-sm text-gray-600 mb-2"></div>
                            <input type="number" 
                                   name="team1_score" 
                                   id="team1Score"
                                   min="0"
                                   required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>
                        <div class="mb-4">
                            <p class="text-sm font-medium text-gray-700 mb-2">Time 2</p>
                            <div id="team2Names" class="text-sm text-gray-600 mb-2"></div>
                            <input type="number" 
                                   name="team2_score" 
                                   id="team2Score"
                                   min="0"
                                   required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>
                    </div>
                    <div class="flex justify-end mt-4 space-x-3">
                        <button type="button"
                                onclick="closeScoreModal()"
                                class="inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Cancelar
                        </button>
                        <button type="submit"
                                class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Salvar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Função para abrir o modal de seleção de jogadores
        window.openSelectPlayersModal = function() {
            document.getElementById('selectPlayersModal').classList.remove('hidden');
        }

        // Função para fechar o modal quando clicar fora dele
        document.getElementById('selectPlayersModal').addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.add('hidden');
            }
        });

        // Limitar o número de jogadores que podem ser selecionados
        const maxPlayers = {{ $tournament->type === 'super_8_doubles' ? 8 : 
                           ($tournament->type === 'super_8_fixed_pairs' ? 16 : 12) }};
        const checkboxes = document.querySelectorAll('input[name="selected_players[]"]');
        
        checkboxes.forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                const checkedBoxes = document.querySelectorAll('input[name="selected_players[]"]:checked');
                
                if (checkedBoxes.length > maxPlayers) {
                    this.checked = false;
                    alert(`Você só pode selecionar ${maxPlayers} jogadores.`);
                }
            });
        });

        // Sistema de Drag & Drop para formação de duplas
        @if($tournament->type === 'super_8_fixed_pairs')
        let pairs = {};
        let clickMode = true;
        let selectedClickPlayers = [];

        // Utilitários
        function normalizeName(text) {
            return (text || '').replace(' ×', '').trim().toLowerCase();
        }

        function bindPlayerItemEvents(item) {
            // Drag events
            item.addEventListener('dragstart', function(e) {
                e.dataTransfer.setData('text/plain', this.dataset.playerId);
                e.dataTransfer.setData('text/html', this.outerHTML);
                this.style.opacity = '0.5';
            });
            item.addEventListener('dragend', function() {
                this.style.opacity = '1';
            });
            // Click selection (para modo clique)
            item.addEventListener('click', function() {
                if (!clickMode) return;
                // Ignorar clique no botão de remover
                if (event.target && event.target.closest('.remove-player-btn')) return;
                const isInPair = !!this.closest('.pair-dropzone');
                if (isInPair) return; // seleção só via lista disponível
                this.classList.toggle('ring');
                this.classList.toggle('ring-indigo-500');
                const pid = this.dataset.playerId;
                const selIndex = selectedClickPlayers.indexOf(pid);
                if (selIndex > -1) {
                    selectedClickPlayers.splice(selIndex, 1);
                } else {
                    selectedClickPlayers.push(pid);
                }
                if (selectedClickPlayers.length === 2) {
                    assignSelectedToNextDropzone();
                }
            });
        }
        
        // Configurar drag & drop
        document.querySelectorAll('.player-item').forEach(item => bindPlayerItemEvents(item));

        // Configurar drop zones
        document.querySelectorAll('.pair-dropzone').forEach(zone => {
            zone.addEventListener('dragover', function(e) {
                e.preventDefault();
                this.classList.add('border-blue-400', 'bg-blue-50');
            });
            
            zone.addEventListener('dragleave', function(e) {
                this.classList.remove('border-blue-400', 'bg-blue-50');
            });
            
            zone.addEventListener('drop', function(e) {
                e.preventDefault();
                this.classList.remove('border-blue-400', 'bg-blue-50');
                
                const playerId = e.dataTransfer.getData('text/plain');
                const playerHtml = e.dataTransfer.getData('text/html');
                const pairNumber = this.dataset.pair;
                
                // Verificar se a dupla já está completa (2 jogadores)
                const currentPlayers = this.querySelectorAll('.player-item');
                if (currentPlayers.length >= 2) {
                    alert('Esta dupla já está completa! Máximo 2 jogadores por dupla.');
                    return;
                }
                
                // Remover o jogador da posição anterior
                const originalPlayer = document.querySelector(`[data-player-id="${playerId}"]`);
                if (originalPlayer) {
                    originalPlayer.remove();
                }
                
                // Adicionar o jogador à nova dupla
                const playerDiv = document.createElement('div');
                playerDiv.innerHTML = playerHtml;
                const newPlayerItem = playerDiv.firstElementChild;
                
                // Modificar o estilo para dupla
                newPlayerItem.classList.remove('bg-blue-100', 'text-blue-800', 'border-blue-200', 'hover:bg-blue-200');
                newPlayerItem.classList.add('bg-green-100', 'text-green-800', 'border-green-200', 'hover:bg-green-200');
                
                // Adicionar botão de remoção
                const removeBtn = document.createElement('span');
                removeBtn.innerHTML = ' ×';
                removeBtn.classList.add('remove-player-btn', 'ml-1', 'cursor-pointer', 'font-bold', 'text-red-500', 'hover:text-red-700');
                removeBtn.onclick = function() {
                    // Voltar para jogadores disponíveis
                    const availablePlayersDiv = document.getElementById('available-players');
                    const returnPlayer = newPlayerItem.cloneNode(true);
                    returnPlayer.classList.remove('bg-green-100', 'text-green-800', 'border-green-200', 'hover:bg-green-200');
                    returnPlayer.classList.add('bg-blue-100', 'text-blue-800', 'border-blue-200', 'hover:bg-blue-200');
                    returnPlayer.innerHTML = returnPlayer.textContent.replace(' ×', '');
                    availablePlayersDiv.appendChild(returnPlayer);
                    bindPlayerItemEvents(returnPlayer);
                    // Atualiza objeto pairs removendo este player
                    const parentPairZone = newPlayerItem.closest('.pair-dropzone');
                    if (parentPairZone) {
                        const pn = parentPairZone.dataset.pair;
                        if (pairs[pn]) {
                            const idx = pairs[pn].indexOf(String(newPlayerItem.dataset.playerId));
                            if (idx > -1) pairs[pn].splice(idx, 1);
                            if (pairs[pn].length === 0) delete pairs[pn];
                        }
                    }
                    newPlayerItem.remove();
                    updatePairsInput();
                    updateCountersAndConfirmButton();
                };
                newPlayerItem.appendChild(removeBtn);
                
                this.querySelector('.pair-players').appendChild(newPlayerItem);
                
                // Atualizar o objeto de duplas
                if (!pairs[pairNumber]) pairs[pairNumber] = [];
                pairs[pairNumber].push(String(playerId));
                
                updatePairsInput();
                updateCountersAndConfirmButton();
            });
        });

        // Área de jogadores disponíveis também pode receber drop
        document.getElementById('available-players').addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('border-blue-400');
        });
        
        document.getElementById('available-players').addEventListener('dragleave', function(e) {
            this.classList.remove('border-blue-400');
        });
        
        document.getElementById('available-players').addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('border-blue-400');
            
            const playerId = e.dataTransfer.getData('text/plain');
            const playerHtml = e.dataTransfer.getData('text/html');
            
            // Remover o jogador da dupla anterior
            const originalPlayer = document.querySelector(`[data-player-id="${playerId}"]`);
            if (originalPlayer && originalPlayer.closest('.pair-dropzone')) {
                // Remove do objeto pairs
                Object.keys(pairs).forEach(pairNum => {
                    const index = pairs[pairNum].indexOf(playerId);
                    if (index > -1) {
                        pairs[pairNum].splice(index, 1);
                        if (pairs[pairNum].length === 0) delete pairs[pairNum];
                    }
                });
                originalPlayer.remove();
            }
            
            // Adicionar de volta aos disponíveis se não estiver lá
            if (!this.querySelector(`[data-player-id="${playerId}"]`)) {
                const playerDiv = document.createElement('div');
                playerDiv.innerHTML = playerHtml;
                const newPlayerItem = playerDiv.firstElementChild;
                newPlayerItem.classList.remove('bg-green-100', 'text-green-800', 'border-green-200', 'hover:bg-green-200');
                newPlayerItem.classList.add('bg-blue-100', 'text-blue-800', 'border-blue-200', 'hover:bg-blue-200');
                newPlayerItem.innerHTML = newPlayerItem.textContent.replace(' ×', '');
                this.appendChild(newPlayerItem);
                bindPlayerItemEvents(newPlayerItem);
            }
            
            updatePairsInput();
            updateCountersAndConfirmButton();
        });

        function updatePairsInput() {
            const container = document.getElementById('pairs-input-container');
            container.innerHTML = '';
            
            Object.keys(pairs).forEach(pairNumber => {
                if (pairs[pairNumber].length === 2) {
                    pairs[pairNumber].forEach((playerId, index) => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = `pairs[${pairNumber}][${index}]`;
                        input.value = playerId;
                        container.appendChild(input);
                    });
                }
            });
        }

        function updateCountersAndConfirmButton() {
            const selected = Object.values(pairs).reduce((acc, arr) => acc + arr.length, 0);
            const completePairs = Object.values(pairs).filter(arr => arr.length === 2).length;
            const selectedCountEl = document.getElementById('selectedCount');
            const pairsCountEl = document.getElementById('pairsCount');
            if (selectedCountEl) selectedCountEl.textContent = `Selecionados: ${selected}/16`;
            if (pairsCountEl) pairsCountEl.textContent = `Duplas completas: ${completePairs}/8`;
            const confirmBtn = document.getElementById('selectPlayersConfirmBtn');
            if (confirmBtn) confirmBtn.disabled = !(selected === 16 && completePairs === 8);
        }

        function clearAllPairs() {
            // mover todos para disponíveis
            const available = document.getElementById('available-players');
            document.querySelectorAll('.pair-dropzone .player-item').forEach(el => {
                const clone = el.cloneNode(true);
                clone.classList.remove('bg-green-100', 'text-green-800', 'border-green-200', 'hover:bg-green-200');
                clone.classList.add('bg-blue-100', 'text-blue-800', 'border-blue-200', 'hover:bg-blue-200');
                clone.innerHTML = clone.textContent.replace(' ×', '');
                available.appendChild(clone);
                bindPlayerItemEvents(clone);
                el.remove();
            });
            pairs = {};
            updatePairsInput();
            updateCountersAndConfirmButton();
        }

        function getAllPlayersFromUI() {
            const map = new Map();
            document.querySelectorAll('#available-players .player-item, .pair-dropzone .player-item').forEach(el => {
                map.set(String(el.dataset.playerId), normalizeName(el.textContent));
            });
            return Array.from(map.entries()).map(([id, name]) => ({ id, name }));
        }

        function assignPlayerIdToDropzone(playerId, dropzone) {
            // cria elemento
            const available = document.getElementById('available-players');
            // remover se existir em qualquer lugar
            const existing = document.querySelector(`[data-player-id="${playerId}"]`);
            let label = '';
            if (existing) {
                label = normalizeName(existing.textContent);
                existing.remove();
            }
            const el = document.createElement('div');
            el.className = 'player-item inline-block m-1 px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm cursor-move border border-green-200 hover:bg-green-200 transition-colors';
            el.setAttribute('data-player-id', playerId);
            el.setAttribute('draggable', 'true');
            el.textContent = label;
            const removeBtn = document.createElement('span');
            removeBtn.innerHTML = ' ×';
            removeBtn.classList.add('remove-player-btn', 'ml-1', 'cursor-pointer', 'font-bold', 'text-red-500', 'hover:text-red-700');
            removeBtn.onclick = function() {
                const availableDiv = document.getElementById('available-players');
                const back = el.cloneNode(true);
                back.classList.remove('bg-green-100', 'text-green-800', 'border-green-200', 'hover:bg-green-200');
                back.classList.add('bg-blue-100', 'text-blue-800', 'border-blue-200', 'hover:bg-blue-200');
                back.innerHTML = back.textContent.replace(' ×', '');
                availableDiv.appendChild(back);
                bindPlayerItemEvents(back);
                const pn = dropzone.dataset.pair;
                if (pairs[pn]) {
                    const idx = pairs[pn].indexOf(String(playerId));
                    if (idx > -1) pairs[pn].splice(idx, 1);
                    if (pairs[pn].length === 0) delete pairs[pn];
                }
                el.remove();
                updatePairsInput();
                updateCountersAndConfirmButton();
            };
            el.appendChild(removeBtn);
            dropzone.querySelector('.pair-players').appendChild(el);
            const pn = dropzone.dataset.pair;
            if (!pairs[pn]) pairs[pn] = [];
            pairs[pn].push(String(playerId));
        }

        function firstAvailableDropzone() {
            const zones = Array.from(document.querySelectorAll('.pair-dropzone'));
            return zones.find(z => z.querySelectorAll('.player-item').length < 2) || null;
        }

        function assignSelectedToNextDropzone() {
            const zone = firstAvailableDropzone();
            if (!zone || selectedClickPlayers.length < 2) return;
            const [a, b] = selectedClickPlayers;
            assignPlayerIdToDropzone(a, zone);
            assignPlayerIdToDropzone(b, zone);
            // limpar seleção visual e array
            document.querySelectorAll('#available-players .player-item').forEach(el => {
                el.classList.remove('ring', 'ring-indigo-500');
            });
            selectedClickPlayers = [];
            updatePairsInput();
            updateCountersAndConfirmButton();
        }

        // Toolbar handlers
        const searchInput = document.getElementById('playerSearch');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const q = normalizeName(this.value);
                document.querySelectorAll('#available-players .player-item').forEach(el => {
                    const name = normalizeName(el.textContent);
                    el.style.display = name.includes(q) ? '' : 'none';
                });
            });
        }

        const clickToggle = document.getElementById('clickModeToggle');
        if (clickToggle) {
            clickMode = !!clickToggle.checked;
            clickToggle.addEventListener('change', function() {
                clickMode = !!this.checked;
                // limpar seleções visuais quando desativa
                if (!clickMode) {
                    selectedClickPlayers = [];
                    document.querySelectorAll('#available-players .player-item').forEach(el => el.classList.remove('ring', 'ring-indigo-500'));
                }
            });
        }

        const clearBtn = document.getElementById('clearPairsBtn');
        if (clearBtn) clearBtn.addEventListener('click', clearAllPairs);

        const autoBtn = document.getElementById('autoCompletePairsBtn');
        if (autoBtn) autoBtn.addEventListener('click', function() {
            const available = Array.from(document.querySelectorAll('#available-players .player-item')).map(el => el.dataset.playerId);
            if (available.length === 0) return;
            const zones = Array.from(document.querySelectorAll('.pair-dropzone'));
            for (const zone of zones) {
                const need = 2 - zone.querySelectorAll('.player-item').length;
                for (let i = 0; i < need; i++) {
                    const pid = available.shift();
                    if (!pid) break;
                    assignPlayerIdToDropzone(pid, zone);
                }
            }
            updatePairsInput();
            updateCountersAndConfirmButton();
        });

        const randBtn = document.getElementById('randomizePairsBtn');
        if (randBtn) randBtn.addEventListener('click', function() {
            const all = getAllPlayersFromUI();
            if (all.length < 16) {
                alert('É necessário ter 16 jogadores disponíveis para randomizar.');
                return;
            }
            // shuffle
            for (let i = all.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [all[i], all[j]] = [all[j], all[i]];
            }
            clearAllPairs();
            const zones = Array.from(document.querySelectorAll('.pair-dropzone'));
            let idx = 0;
            for (const zone of zones) {
                assignPlayerIdToDropzone(all[idx++].id, zone);
                assignPlayerIdToDropzone(all[idx++].id, zone);
            }
            updatePairsInput();
            updateCountersAndConfirmButton();
        });

        // Inicializa contadores
        updateCountersAndConfirmButton();
        @endif

        // Funções para o modal de gerar partidas
        window.openGenerateMatchesModal = function() {
            document.getElementById('generateMatchesModal').classList.remove('hidden');
        }

        window.closeGenerateMatchesModal = function() {
            document.getElementById('generateMatchesModal').classList.add('hidden');
        }

        // Fechar modal quando clicar fora
        document.getElementById('generateMatchesModal').addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.add('hidden');
            }
        });

        // Funções para o modal de placar
        window.openScoreModal = function(match) {
            const modal = document.getElementById('scoreModal');
            const form = document.getElementById('scoreForm');
            const team1Names = document.getElementById('team1Names');
            const team2Names = document.getElementById('team2Names');

            // Configura o formulário
            form.action = `/tournaments/matches/${match.id}/score`;
            
            // Mostra os nomes dos jogadores
            if (match.team1_player2) {
                team1Names.textContent = `${match.team1_player1.name} / ${match.team1_player2.name}`;
                team2Names.textContent = `${match.team2_player1.name} / ${match.team2_player2.name}`;
            } else {
                team1Names.textContent = match.team1_player1.name;
                team2Names.textContent = match.team2_player1.name;
            }

            // Limpa os campos de placar
            document.getElementById('team1Score').value = '';
            document.getElementById('team2Score').value = '';

            // Mostra o modal
            modal.classList.remove('hidden');
        }

        window.closeScoreModal = function() {
            document.getElementById('scoreModal').classList.add('hidden');
        }

        // Fechar modal quando clicar fora
        document.getElementById('scoreModal').addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.add('hidden');
            }
        });

        // Validação do formulário de placar
        document.getElementById('scoreForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const team1Score = parseInt(document.getElementById('team1Score').value);
            const team2Score = parseInt(document.getElementById('team2Score').value);

            if (team1Score === team2Score) {
                alert('O placar não pode ser empate.');
                return;
            }

            this.submit();
        });

        function copyRegistrationLink(button) {
            const input = button.previousElementSibling;
            input.select();
            document.execCommand('copy');
            
            const originalText = button.textContent;
            button.textContent = 'Copiado!';
            setTimeout(() => {
                button.textContent = originalText;
            }, 2000);
        }
        });
    </script>
</x-app-layout>
