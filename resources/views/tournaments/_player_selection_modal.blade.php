<!-- Modal de Seleção de Jogadores -->
<div id="selectPlayersModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-2 sm:top-10 mx-auto p-3 sm:p-5 border w-full max-w-4xl shadow-lg rounded-md bg-white m-2 sm:m-0">
        <div class="mt-3">
            @if($tournament->type === 'super_8_fixed_pairs')
                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Formar as 8 Duplas</h3>
                <form id="pairsForm" action="{{ route('tournaments.store-pairs', $tournament) }}" method="POST">
                    @csrf
                    <div class="mt-2">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-blue-800">
                                        Formação de Duplas Fixas
                                    </h3>
                                    <div class="mt-1 text-sm text-blue-700">
                                        <p>Organize 16 jogadores em 8 duplas. Use arrastar e soltar ou clique para formar as duplas.</p>
                                        @if($tournament->category === 'mixed')
                                            <p class="mt-1 font-medium">⚠️ Torneio Misto: Cada dupla deve ter 1 homem e 1 mulher</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Barra de ferramentas -->
                        <div class="mb-6 bg-gray-50 rounded-lg p-4">
                            <div class="flex flex-col gap-4">
                                <!-- Busca e botões -->
                                <div class="flex items-center gap-3">
                                    <div class="flex-1">
                                        <input id="playerSearch" type="text" placeholder="Buscar jogador pelo nome..." 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" />
                                    </div>
                                    <div class="flex gap-2">
                                        <button type="button" id="randomizePairsBtn" 
                                                class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 transition-colors">
                                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                            </svg>
                                            Randomizar
                                        </button>
                                        <button type="button" id="autoCompletePairsBtn" 
                                                class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700 transition-colors">
                                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Auto Completar
                                        </button>
                                        <button type="button" id="clearPairsBtn" 
                                                class="px-4 py-2 text-sm font-medium text-white bg-gray-600 rounded-md hover:bg-gray-700 transition-colors">
                                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                            Limpar
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- Status e controles -->
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-4 text-sm">
                                        <div class="flex items-center gap-2">
                                            <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                                            <span class="font-medium text-gray-700">Selecionados: <span id="selectedCount" class="text-indigo-600">0/16</span></span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                                            <span class="font-medium text-gray-700">Duplas: <span id="pairsCount" class="text-green-600">0/8</span></span>
                                        </div>
                                    </div>
                                    <label class="inline-flex items-center gap-2 text-sm text-gray-700 cursor-pointer select-none">
                                        <input id="clickModeToggle" type="checkbox" 
                                               class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500" checked>
                                        Modo clique para formar duplas
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Lista de jogadores disponíveis -->
                        <div class="mb-6">
                            <h4 class="text-base sm:text-lg font-medium text-gray-800 mb-3 flex items-center justify-between">
                                <span>Jogadores Disponíveis</span>
                                <span class="text-xs sm:text-sm font-normal text-gray-500 bg-gray-100 px-2 sm:px-3 py-1 rounded-full">
                                    {{ $tournament->category === 'male' ? 'Masculino' : 
                                       ($tournament->category === 'female' ? 'Feminino' : 'Misto') }}
                                </span>
                            </h4>
                            <div id="available-players" class="min-h-20 sm:min-h-24 p-3 sm:p-4 border-2 border-dashed border-gray-300 rounded-lg bg-gray-50 hover:border-gray-400 transition-colors">
                                @php
                                    $filteredPlayers = $availablePlayers;
                                    if ($tournament->category === 'male') {
                                        $filteredPlayers = $availablePlayers->where('gender', 'male');
                                    } elseif ($tournament->category === 'female') {
                                        $filteredPlayers = $availablePlayers->where('gender', 'female');
                                    }
                                @endphp
                                @foreach($filteredPlayers as $player)
                                    <div class="player-item inline-flex items-center gap-1 sm:gap-2 m-1 px-2 sm:px-3 py-1.5 sm:py-2 bg-white text-gray-800 rounded-lg text-xs sm:text-sm cursor-move border border-gray-200 hover:border-indigo-300 hover:bg-indigo-50 transition-all duration-200 shadow-sm" 
                                         data-player-id="{{ $player->id }}"
                                         data-gender="{{ $player->gender }}"
                                         draggable="true">
                                        <span class="inline-block w-2.5 h-2.5 sm:w-3 sm:h-3 rounded-full {{ $player->gender === 'male' ? 'bg-blue-500' : 'bg-pink-500' }}"></span>
                                        <span class="font-medium">{{ $player->name }}</span>
                                        <span class="text-xs text-gray-500">({{ $player->gender === 'male' ? 'M' : 'F' }})</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- 8 Duplas -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                            @for($i = 1; $i <= 8; $i++)
                                <div class="pair-container bg-white border border-gray-200 rounded-lg p-3 sm:p-4 shadow-sm">
                                    <h5 class="text-xs sm:text-sm font-medium text-gray-700 mb-2 sm:mb-3 flex items-center justify-between">
                                        <span class="flex items-center gap-2">
                                            <div class="w-5 h-5 sm:w-6 sm:h-6 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center text-xs font-bold">
                                                {{ $i }}
                                            </div>
                                            <span class="hidden sm:inline">Dupla {{ $i }}</span>
                                            <span class="sm:hidden">D{{ $i }}</span>
                                        </span>
                                        <span class="text-xs text-gray-400" data-pair-hint="{{ $i }}"></span>
                                    </h5>
                                    <div class="pair-dropzone min-h-16 sm:min-h-20 p-2 sm:p-3 border-2 border-dashed border-gray-300 rounded-lg bg-gray-50 hover:border-indigo-400 hover:bg-indigo-50 transition-all duration-200" 
                                         data-pair="{{ $i }}">
                                        <div class="pair-players flex flex-wrap gap-1 sm:gap-2 min-h-8 sm:min-h-12">
                                            <!-- Jogadores serão adicionados aqui via JavaScript -->
                                        </div>
                                        <div class="text-xs text-gray-400 text-center mt-2" data-empty-hint="{{ $i }}">
                                            Arraste jogadores aqui ou clique para formar a dupla
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
            @elseif($tournament->type === 'super_12_fixed_pairs')
                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Formar as 6 Duplas</h3>
                <form id="pairsForm" action="{{ route('tournaments.store-pairs', $tournament) }}" method="POST">
                    @csrf
                    <div class="mt-2">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-blue-800">
                                        Formação de Duplas Fixas (Super 12)
                                    </h3>
                                    <div class="mt-1 text-sm text-blue-700">
                                        <p>Organize 12 jogadores em 6 duplas. Use arrastar e soltar ou clique para formar as duplas.</p>
                                        @if($tournament->category === 'mixed')
                                            <p class="mt-1 font-medium">⚠️ Torneio Misto: Selecione exatamente 6 homens e 6 mulheres</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Barra de ferramentas -->
                        <div class="mb-6 bg-gray-50 rounded-lg p-4">
                            <div class="flex flex-col gap-4">
                                <!-- Busca e botões -->
                                <div class="flex items-center gap-3">
                                    <div class="flex-1">
                                        <input id="playerSearch" type="text" placeholder="Buscar jogador pelo nome..." 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" />
                                    </div>
                                    <div class="flex gap-2">
                                        <button type="button" id="randomizePairsBtn" 
                                                class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 transition-colors">
                                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                            </svg>
                                            Randomizar
                                        </button>
                                        <button type="button" id="autoCompletePairsBtn" 
                                                class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700 transition-colors">
                                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Auto Completar
                                        </button>
                                        <button type="button" id="clearPairsBtn" 
                                                class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700 transition-colors">
                                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                            Limpar
                                        </button>
                                    </div>
                                </div>

                                <!-- Contadores -->
                                <div class="flex items-center gap-6 text-sm">
                                    <div class="flex items-center gap-2">
                                        <span class="font-medium text-gray-700">Jogadores:</span>
                                        <span id="selectedCount" class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full">0</span>
                                        <span class="text-gray-500">/ 12</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="font-medium text-gray-700">Duplas:</span>
                                        <span id="pairsCount" class="px-2 py-1 bg-green-100 text-green-800 rounded-full">0</span>
                                        <span class="text-gray-500">/ 6</span>
                                    </div>
                                    @if($tournament->category === 'mixed')
                                        <div class="flex items-center gap-2">
                                            <span class="font-medium text-gray-700">Homens:</span>
                                            <span id="maleCount" class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full">0</span>
                                            <span class="text-gray-500">/ 6</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="font-medium text-gray-700">Mulheres:</span>
                                            <span id="femaleCount" class="px-2 py-1 bg-pink-100 text-pink-800 rounded-full">0</span>
                                            <span class="text-gray-500">/ 6</span>
                                        </div>
                                    @endif
                                </div>

                                <!-- Toggle modo clique -->
                                <div class="flex items-center gap-2">
                                    <input type="checkbox" id="clickModeToggle" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    <label for="clickModeToggle" class="text-sm text-gray-700">Modo clique (alternar com arrastar e soltar)</label>
                                </div>
                            </div>
                        </div>

                        <!-- Área de duplas -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4 mb-6">
                            @for($i = 1; $i <= 6; $i++)
                                <div class="border-2 border-dashed border-gray-300 rounded-lg p-3 sm:p-4 min-h-[100px] sm:min-h-[120px] pair-slot" data-pair="{{ $i }}">
                                    <div class="text-center text-xs sm:text-sm text-gray-500 mb-2">Dupla {{ $i }}</div>
                                    <div class="flex flex-col gap-2 min-h-[60px] sm:min-h-[80px] pair-players" data-pair="{{ $i }}">
                                        <div class="pair-hint text-xs text-gray-400 text-center py-2">
                                            @if($tournament->category === 'mixed')
                                                Aguardando homem/mulher
                                            @else
                                                Aguardando jogadores
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endfor
                        </div>

                        <!-- Lista de jogadores disponíveis -->
                        <div class="border rounded-lg bg-white max-h-64 overflow-y-auto">
                            <div class="p-3 bg-gray-50 border-b">
                                <h4 class="font-medium text-gray-700">Jogadores Disponíveis</h4>
                            </div>
                            <div id="available-players" class="p-3 space-y-2">
                                @php
                                    $filteredPlayers = $availablePlayers;
                                    if ($tournament->category === 'male') {
                                        $filteredPlayers = $availablePlayers->where('gender', 'male');
                                    } elseif ($tournament->category === 'female') {
                                        $filteredPlayers = $availablePlayers->where('gender', 'female');
                                    }
                                @endphp
                                @foreach($filteredPlayers as $player)
                                    <div class="player-item flex items-center justify-between p-2 bg-gray-50 rounded-lg cursor-pointer hover:bg-gray-100 transition-colors" 
                                         data-player-id="{{ $player->id }}" 
                                         data-player-name="{{ strtolower($player->name) }}"
                                         data-player-gender="{{ $player->gender }}">
                                        <div class="flex items-center gap-2">
                                            <div class="w-3 h-3 rounded-full {{ $player->gender === 'male' ? 'bg-blue-500' : 'bg-pink-500' }}"></div>
                                            <span class="font-medium">{{ $player->name }}</span>
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $player->gender === 'male' ? 'Homem' : 'Mulher' }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Inputs hidden para os pares -->
                        <div id="pairs-input-container">
                            @for($i = 1; $i <= 6; $i++)
                                <input type="hidden" name="pairs[{{ $i-1 }}][0]" class="pair-input-1" data-pair="{{ $i }}">
                                <input type="hidden" name="pairs[{{ $i-1 }}][1]" class="pair-input-2" data-pair="{{ $i }}">
                            @endfor
                        </div>

                        <!-- Botões de ação -->
                        <div class="flex flex-col sm:flex-row justify-end gap-3 mt-6">
                            <button type="button" onclick="closeModal()" 
                                    class="w-full sm:w-auto px-4 py-3 sm:py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200 transition-colors">
                                Cancelar
                            </button>
                            <button type="submit" id="selectPlayersConfirmBtn" 
                                    class="w-full sm:w-auto px-4 py-3 sm:py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                    disabled>
                                Confirmar Duplas
                            </button>
                        </div>
                    </div>
                </form>
            @else
                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Selecionar Jogadores</h3>
                <form action="{{ route('tournaments.select-players', $tournament) }}" method="POST" id="selectPlayersForm">
                    @csrf
                    <div class="mt-2">
                        <div class="mb-4 flex items-center justify-between">
                            <p class="text-sm text-gray-500">
                                Selecione {{ $tournament->type === 'super_8_doubles' ? '8' : '12' }} jogadores para o torneio.
                            </p>
                            <span class="text-xs text-gray-500">
                                {{ $tournament->category === 'male' ? 'Torneio Masculino' : 
                                   ($tournament->category === 'female' ? 'Torneio Feminino' : 'Torneio Misto') }}
                            </span>
                        </div>

                        <!-- Barra de busca -->
                        <div class="mb-4">
                            <input type="text" 
                                   id="playerListSearch" 
                                   placeholder="Buscar jogador..." 
                                   class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>

                        <!-- Contador de selecionados -->
                        <div class="mb-4 p-3 bg-blue-50 rounded-lg">
                            <p class="text-sm font-medium text-blue-700">
                                Jogadores selecionados: 
                                <span id="selectedPlayersCount">{{ count($selectedPlayers) }}</span> / 
                                {{ $tournament->type === 'super_8_doubles' ? '8' : '12' }}
                            </p>
                        </div>

                        <!-- Lista de jogadores -->
                        <div class="max-h-96 overflow-y-auto border rounded-lg bg-white">
                            <div id="playersList" class="divide-y divide-gray-200">
                                @php
                                    $checkboxPlayers = $availablePlayers;
                                    if ($tournament->category === 'male') {
                                        $checkboxPlayers = $availablePlayers->where('gender', 'male');
                                    } elseif ($tournament->category === 'female') {
                                        $checkboxPlayers = $availablePlayers->where('gender', 'female');
                                    }
                                @endphp
                                @foreach($checkboxPlayers as $player)
                                    <label class="player-checkbox-item flex items-center p-3 hover:bg-gray-50 cursor-pointer" 
                                           data-player-name="{{ strtolower($player->name) }}">
                                        <input type="checkbox"
                                               name="selected_players[]"
                                               value="{{ $player->id }}"
                                               {{ in_array($player->id, $selectedPlayers) ? 'checked' : '' }}
                                               class="player-checkbox h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                        <div class="ml-3 flex items-center gap-2 flex-1">
                                            <span class="inline-block w-2.5 h-2.5 rounded-full {{ $player->gender === 'male' ? 'bg-blue-500' : 'bg-pink-500' }}"></span>
                                            <span class="text-sm text-gray-900">{{ $player->name }}</span>
                                            <span class="text-xs text-gray-500">({{ $player->gender === 'male' ? 'M' : 'F' }})</span>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
                    <div class="flex justify-end mt-6 space-x-3">
                        <button type="button"
                                onclick="document.getElementById('selectPlayersModal').classList.add('hidden')"
                                class="inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Cancelar
                        </button>
                        <button id="selectPlayersConfirmBtn" 
                                type="submit" 
                                form="{{ $tournament->type === 'super_8_fixed_pairs' ? 'pairsForm' : 'selectPlayersForm' }}" 
                                {{ $tournament->type === 'super_8_fixed_pairs' ? 'disabled' : '' }}
                                class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md disabled:opacity-50 disabled:cursor-not-allowed hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Confirmar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Função global para abrir o modal
    window.openSelectPlayersModal = function() {
        document.getElementById('selectPlayersModal').classList.remove('hidden');
    }

    @if($tournament->type === 'super_8_fixed_pairs')
        // Configuração para Super 8 Duplas Fixas
        const tournamentCategory = '{{ $tournament->category }}';
        const isMixed = tournamentCategory === 'mixed';
        
        let selectedPlayers = [];
        let pairs = {};
        
        // Inicializar pares vazios
        for (let i = 1; i <= 8; i++) {
            pairs[i] = [];
        }
        
        // Elementos DOM
        const availablePlayers = document.getElementById('available-players');
        const playerSearch = document.getElementById('playerSearch');
        const selectedCount = document.getElementById('selectedCount');
        const pairsCount = document.getElementById('pairsCount');
        const randomizeBtn = document.getElementById('randomizePairsBtn');
        const autoCompleteBtn = document.getElementById('autoCompletePairsBtn');
        const clearBtn = document.getElementById('clearPairsBtn');
        const clickModeToggle = document.getElementById('clickModeToggle');
        const confirmBtn = document.getElementById('selectPlayersConfirmBtn');
        const pairsInputContainer = document.getElementById('pairs-input-container');
        
        // Atualizar contadores
        function updateCounters() {
            selectedCount.textContent = `${selectedPlayers.length}/16`;
            const completedPairs = Object.values(pairs).filter(pair => pair.length === 2).length;
            pairsCount.textContent = `${completedPairs}/8`;
            
            // Habilitar/desabilitar botão de confirmação
            confirmBtn.disabled = completedPairs !== 8;
            confirmBtn.classList.toggle('opacity-50', completedPairs !== 8);
            confirmBtn.classList.toggle('cursor-not-allowed', completedPairs !== 8);
        }
        
        // Atualizar hints das duplas
        function updatePairHints() {
            Object.keys(pairs).forEach(pairNum => {
                const pair = pairs[pairNum];
                const hint = document.querySelector(`[data-pair-hint="${pairNum}"]`);
                const emptyHint = document.querySelector(`[data-empty-hint="${pairNum}"]`);
                
                if (pair.length === 0) {
                    hint.textContent = isMixed ? 'Aguardando homem/mulher' : 'Aguardando 2 jogadores';
                    emptyHint.style.display = 'block';
                } else if (pair.length === 1) {
                    const player = pair[0];
                    const needsOpposite = isMixed && player.gender === pair[0].gender;
                    hint.textContent = isMixed ? 
                        `Aguardando ${player.gender === 'male' ? 'mulher' : 'homem'}` : 
                        'Aguardando 1 jogador';
                    emptyHint.style.display = 'none';
                } else {
                    hint.textContent = 'Dupla completa ✓';
                    emptyHint.style.display = 'none';
                }
            });
        }
        
        // Renderizar jogadores nas duplas
        function renderPairPlayers() {
            Object.keys(pairs).forEach(pairNum => {
                const dropzone = document.querySelector(`[data-pair="${pairNum}"] .pair-players`);
                dropzone.innerHTML = '';
                
                pairs[pairNum].forEach(player => {
                    const playerDiv = document.createElement('div');
                    playerDiv.className = 'inline-flex items-center gap-2 px-2 py-1 bg-indigo-100 text-indigo-800 rounded text-xs border border-indigo-200';
                    playerDiv.innerHTML = `
                        <span class="inline-block w-2 h-2 rounded-full ${player.gender === 'male' ? 'bg-blue-500' : 'bg-pink-500'}"></span>
                        <span>${player.name}</span>
                        <button type="button" class="ml-1 text-indigo-600 hover:text-indigo-800" onclick="removePlayerFromPair(${pairNum}, ${player.id})">×</button>
                    `;
                    dropzone.appendChild(playerDiv);
                });
            });
        }
        
        // Adicionar jogador à dupla
        function addPlayerToPair(pairNum, player) {
            if (pairs[pairNum].length >= 2) return false;
            
            // Validação para torneio misto
            if (isMixed && pairs[pairNum].length === 1) {
                const existingPlayer = pairs[pairNum][0];
                if (existingPlayer.gender === player.gender) {
                    alert('Em torneio misto, cada dupla deve ter 1 homem e 1 mulher!');
                    return false;
                }
            }
            
            pairs[pairNum].push(player);
            selectedPlayers.push(player);
            updateCounters();
            updatePairHints();
            renderPairPlayers();
            return true;
        }
        
        // Remover jogador da dupla
        window.removePlayerFromPair = function(pairNum, playerId) {
            const playerIndex = pairs[pairNum].findIndex(p => p.id === playerId);
            if (playerIndex === -1) return;
            
            const player = pairs[pairNum][playerIndex];
            pairs[pairNum].splice(playerIndex, 1);
            selectedPlayers = selectedPlayers.filter(p => p.id !== playerId);
            
            // Adicionar de volta à lista de disponíveis
            const playerDiv = document.createElement('div');
            playerDiv.className = 'player-item inline-flex items-center gap-2 m-1 px-3 py-2 bg-white text-gray-800 rounded-lg text-sm cursor-move border border-gray-200 hover:border-indigo-300 hover:bg-indigo-50 transition-all duration-200 shadow-sm';
            playerDiv.setAttribute('data-player-id', player.id);
            playerDiv.setAttribute('data-gender', player.gender);
            playerDiv.setAttribute('draggable', 'true');
            playerDiv.innerHTML = `
                <span class="inline-block w-3 h-3 rounded-full ${player.gender === 'male' ? 'bg-blue-500' : 'bg-pink-500'}"></span>
                <span class="font-medium">${player.name}</span>
                <span class="text-xs text-gray-500">(${player.gender === 'male' ? 'M' : 'F'})</span>
            `;
            availablePlayers.appendChild(playerDiv);
            
            updateCounters();
            updatePairHints();
            renderPairPlayers();
        };
        
        // Busca de jogadores
        playerSearch.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const playerItems = availablePlayers.querySelectorAll('.player-item');
            
            playerItems.forEach(item => {
                const playerName = item.querySelector('span.font-medium').textContent.toLowerCase();
                item.style.display = playerName.includes(searchTerm) ? 'inline-flex' : 'none';
            });
        });
        
        // Drag and Drop
        availablePlayers.addEventListener('dragstart', function(e) {
            if (e.target.classList.contains('player-item')) {
                e.dataTransfer.setData('text/plain', e.target.dataset.playerId);
                e.target.style.opacity = '0.5';
            }
        });
        
        availablePlayers.addEventListener('dragend', function(e) {
            e.target.style.opacity = '1';
        });
        
        // Drop zones
        document.querySelectorAll('.pair-dropzone').forEach(dropzone => {
            dropzone.addEventListener('dragover', function(e) {
                e.preventDefault();
                this.classList.add('border-indigo-400', 'bg-indigo-50');
            });
            
            dropzone.addEventListener('dragleave', function(e) {
                this.classList.remove('border-indigo-400', 'bg-indigo-50');
            });
            
            dropzone.addEventListener('drop', function(e) {
                e.preventDefault();
                this.classList.remove('border-indigo-400', 'bg-indigo-50');
                
                const playerId = parseInt(e.dataTransfer.getData('text/plain'));
                const playerDiv = availablePlayers.querySelector(`[data-player-id="${playerId}"]`);
                
                if (playerDiv) {
                    const player = {
                        id: playerId,
                        name: playerDiv.querySelector('span.font-medium').textContent,
                        gender: playerDiv.dataset.gender
                    };
                    
                    const pairNum = parseInt(this.dataset.pair);
                    if (addPlayerToPair(pairNum, player)) {
                        playerDiv.remove();
                    }
                }
            });
        });
        
        // Modo clique
        clickModeToggle.addEventListener('change', function() {
            const playerItems = availablePlayers.querySelectorAll('.player-item');
            playerItems.forEach(item => {
                if (this.checked) {
                    item.style.cursor = 'pointer';
                    item.addEventListener('click', handlePlayerClick);
                } else {
                    item.style.cursor = 'move';
                    item.removeEventListener('click', handlePlayerClick);
                }
            });
        });
        
        function handlePlayerClick(e) {
            const playerId = parseInt(e.currentTarget.dataset.playerId);
            const player = {
                id: playerId,
                name: e.currentTarget.querySelector('span.font-medium').textContent,
                gender: e.currentTarget.dataset.gender
            };
            
            // Encontrar primeira dupla disponível
            for (let i = 1; i <= 8; i++) {
                if (addPlayerToPair(i, player)) {
                    e.currentTarget.remove();
                    break;
                }
            }
        }
        
        // Botões de ação
        randomizeBtn.addEventListener('click', function() {
            const allPlayers = Array.from(availablePlayers.querySelectorAll('.player-item')).map(div => ({
                id: parseInt(div.dataset.playerId),
                name: div.querySelector('span.font-medium').textContent,
                gender: div.dataset.gender,
                element: div
            }));
            
            // Limpar duplas
            Object.keys(pairs).forEach(pairNum => {
                pairs[pairNum] = [];
            });
            selectedPlayers = [];
            
            // Embaralhar jogadores
            const shuffled = allPlayers.sort(() => Math.random() - 0.5);
            
            // Distribuir em duplas
            let pairIndex = 1;
            shuffled.forEach(player => {
                if (pairIndex <= 8) {
                    addPlayerToPair(pairIndex, player);
                    player.element.remove();
                    pairIndex++;
                }
            });
        });
        
        autoCompleteBtn.addEventListener('click', function() {
            const allPlayers = Array.from(availablePlayers.querySelectorAll('.player-item')).map(div => ({
                id: parseInt(div.dataset.playerId),
                name: div.querySelector('span.font-medium').textContent,
                gender: div.dataset.gender,
                element: div
            }));
            
            // Separar por gênero para torneio misto
            let malePlayers = allPlayers.filter(p => p.gender === 'male');
            let femalePlayers = allPlayers.filter(p => p.gender === 'female');
            
            if (isMixed) {
                // Para torneio misto, formar duplas homem-mulher
                const minPairs = Math.min(malePlayers.length, femalePlayers.length, 8);
                for (let i = 0; i < minPairs; i++) {
                    addPlayerToPair(i + 1, malePlayers[i]);
                    addPlayerToPair(i + 1, femalePlayers[i]);
                    malePlayers[i].element.remove();
                    femalePlayers[i].element.remove();
                }
            } else {
                // Para torneio não-misto, distribuir aleatoriamente
                const shuffled = allPlayers.sort(() => Math.random() - 0.5);
                let pairIndex = 1;
                shuffled.forEach(player => {
                    if (pairIndex <= 8) {
                        addPlayerToPair(pairIndex, player);
                        player.element.remove();
                        pairIndex++;
                    }
                });
            }
        });
        
        clearBtn.addEventListener('click', function() {
            // Limpar duplas
            Object.keys(pairs).forEach(pairNum => {
                pairs[pairNum] = [];
            });
            selectedPlayers = [];
            
            // Limpar container de duplas
            document.querySelectorAll('.pair-players').forEach(container => {
                container.innerHTML = '';
            });
            
            // Recarregar jogadores disponíveis
            location.reload();
        });
        
        // Gerar inputs para envio
        function generatePairInputs() {
            pairsInputContainer.innerHTML = '';
            Object.keys(pairs).forEach(pairNum => {
                pairs[pairNum].forEach((player, index) => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = `pairs[${pairNum}][${index}]`;
                    input.value = player.id;
                    pairsInputContainer.appendChild(input);
                });
            });
        }
        
        // Atualizar inputs antes do envio
        document.getElementById('pairsForm').addEventListener('submit', function() {
            generatePairInputs();
        });
        
        // Inicializar
        updateCounters();
        updatePairHints();
        
        // Ativar modo clique por padrão
        clickModeToggle.dispatchEvent(new Event('change'));
    @elseif($tournament->type === 'super_12_fixed_pairs')
        // Configuração para Super 12 Duplas Fixas
        const tournamentCategory = '{{ $tournament->category }}';
        const isMixed = tournamentCategory === 'mixed';
        
        let selectedPlayers = [];
        let pairs = {};
        
        // Inicializar pares vazios
        for (let i = 1; i <= 6; i++) {
            pairs[i] = [];
        }
        
        // Elementos DOM
        const availablePlayers = document.getElementById('available-players');
        const playerSearch = document.getElementById('playerSearch');
        const selectedCount = document.getElementById('selectedCount');
        const pairsCount = document.getElementById('pairsCount');
        const randomizeBtn = document.getElementById('randomizePairsBtn');
        const autoCompleteBtn = document.getElementById('autoCompletePairsBtn');
        const clearBtn = document.getElementById('clearPairsBtn');
        const clickModeToggle = document.getElementById('clickModeToggle');
        const confirmBtn = document.getElementById('selectPlayersConfirmBtn');
        const pairsInputContainer = document.getElementById('pairs-input-container');
        
        // Contadores específicos para torneios mistos
        let maleCount = 0;
        let femaleCount = 0;
        
        // Atualizar contadores
        function updateCounters() {
            selectedCount.textContent = `${selectedPlayers.length}/12`;
            const completedPairs = Object.values(pairs).filter(pair => pair.length === 2).length;
            pairsCount.textContent = `${completedPairs}/6`;
            
            // Contadores de gênero para torneios mistos
            if (isMixed) {
                maleCount = selectedPlayers.filter(id => {
                    const player = document.querySelector(`[data-player-id="${id}"]`);
                    return player && player.dataset.playerGender === 'male';
                }).length;
                femaleCount = selectedPlayers.filter(id => {
                    const player = document.querySelector(`[data-player-id="${id}"]`);
                    return player && player.dataset.playerGender === 'female';
                }).length;
                
                document.getElementById('maleCount').textContent = maleCount;
                document.getElementById('femaleCount').textContent = femaleCount;
            }
            
            // Habilitar/desabilitar botão de confirmação
            const canConfirm = completedPairs === 6 && (!isMixed || (maleCount === 6 && femaleCount === 6));
            confirmBtn.disabled = !canConfirm;
            confirmBtn.classList.toggle('opacity-50', !canConfirm);
            confirmBtn.classList.toggle('cursor-not-allowed', !canConfirm);
        }
        
        // Atualizar hints das duplas
        function updatePairHints() {
            Object.keys(pairs).forEach(pairNum => {
                const pair = pairs[pairNum];
                const pairElement = document.querySelector(`[data-pair="${pairNum}"]`);
                const hint = pairElement.querySelector('.pair-hint');
                
                if (pair.length === 0) {
                    hint.textContent = isMixed ? 'Aguardando homem/mulher' : 'Aguardando 2 jogadores';
                } else if (pair.length === 1) {
                    const player = document.querySelector(`[data-player-id="${pair[0]}"]`);
                    const gender = player ? player.dataset.playerGender : 'unknown';
                    hint.textContent = isMixed ? 
                        (gender === 'male' ? 'Aguardando mulher' : 'Aguardando homem') : 
                        'Aguardando 1 jogador';
                } else {
                    hint.textContent = 'Dupla completa';
                }
            });
        }
        
        // Adicionar jogador à dupla
        function addPlayerToPair(playerId, pairNum) {
            if (pairs[pairNum].length >= 2) return false;
            
            // Verificar se já está em outra dupla
            if (selectedPlayers.includes(playerId)) return false;
            
            // Validação para torneios mistos
            if (isMixed && pairs[pairNum].length === 1) {
                const existingPlayer = document.querySelector(`[data-player-id="${pairs[pairNum][0]}"]`);
                const newPlayer = document.querySelector(`[data-player-id="${playerId}"]`);
                
                if (existingPlayer && newPlayer && 
                    existingPlayer.dataset.playerGender === newPlayer.dataset.playerGender) {
                    return false; // Mesmo gênero
                }
            }
            
            pairs[pairNum].push(playerId);
            selectedPlayers.push(playerId);
            
            // Atualizar UI
            updatePlayerInPair(playerId, pairNum);
            updateCounters();
            updatePairHints();
            updateHiddenInputs();
            
            return true;
        }
        
        // Remover jogador da dupla
        function removePlayerFromPair(playerId, pairNum) {
            const index = pairs[pairNum].indexOf(playerId);
            if (index === -1) return false;
            
            pairs[pairNum].splice(index, 1);
            selectedPlayers.splice(selectedPlayers.indexOf(playerId), 1);
            
            // Atualizar UI
            updatePlayerInPair(playerId, pairNum);
            updateCounters();
            updatePairHints();
            updateHiddenInputs();
            
            return true;
        }
        
        // Atualizar jogador na dupla (UI)
        function updatePlayerInPair(playerId, pairNum) {
            const pairElement = document.querySelector(`[data-pair="${pairNum}"]`);
            const playersContainer = pairElement.querySelector('.pair-players');
            const player = document.querySelector(`[data-player-id="${playerId}"]`);
            
            if (!player) return;
            
            const isInPair = pairs[pairNum].includes(playerId);
            const existingChip = playersContainer.querySelector(`[data-player-chip="${playerId}"]`);
            
            if (isInPair && !existingChip) {
                // Adicionar chip
                const chip = document.createElement('div');
                chip.className = 'flex items-center justify-between p-2 bg-indigo-100 rounded-lg';
                chip.dataset.playerChip = playerId;
                chip.innerHTML = `
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full ${player.dataset.playerGender === 'male' ? 'bg-blue-500' : 'bg-pink-500'}"></div>
                        <span class="text-sm font-medium">${player.querySelector('span').textContent}</span>
                    </div>
                    <button type="button" class="text-red-500 hover:text-red-700" onclick="removePlayerFromPair(${playerId}, ${pairNum})">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                `;
                playersContainer.appendChild(chip);
            } else if (!isInPair && existingChip) {
                // Remover chip
                existingChip.remove();
            }
        }
        
        // Atualizar inputs hidden
        function updateHiddenInputs() {
            Object.keys(pairs).forEach(pairNum => {
                const pair = pairs[pairNum];
                const input1 = document.querySelector(`[data-pair="${pairNum}"].pair-input-1`);
                const input2 = document.querySelector(`[data-pair="${pairNum}"].pair-input-2`);
                
                if (input1) input1.value = pair[0] || '';
                if (input2) input2.value = pair[1] || '';
            });
        }
        
        // Busca de jogadores
        playerSearch.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const players = availablePlayers.querySelectorAll('.player-item');
            
            players.forEach(player => {
                const name = player.dataset.playerName;
                const matches = name.includes(searchTerm);
                player.style.display = matches ? 'flex' : 'none';
            });
        });
        
        // Drag and Drop
        let draggedPlayer = null;
        
        availablePlayers.addEventListener('dragstart', function(e) {
            if (e.target.classList.contains('player-item')) {
                draggedPlayer = e.target;
                e.target.style.opacity = '0.5';
            }
        });
        
        availablePlayers.addEventListener('dragend', function(e) {
            if (e.target.classList.contains('player-item')) {
                e.target.style.opacity = '1';
                draggedPlayer = null;
            }
        });
        
        document.querySelectorAll('.pair-slot').forEach(slot => {
            slot.addEventListener('dragover', function(e) {
                e.preventDefault();
                this.classList.add('border-indigo-400', 'bg-indigo-50');
            });
            
            slot.addEventListener('dragleave', function(e) {
                this.classList.remove('border-indigo-400', 'bg-indigo-50');
            });
            
            slot.addEventListener('drop', function(e) {
                e.preventDefault();
                this.classList.remove('border-indigo-400', 'bg-indigo-50');
                
                if (draggedPlayer) {
                    const playerId = parseInt(draggedPlayer.dataset.playerId);
                    const pairNum = parseInt(this.dataset.pair);
                    addPlayerToPair(playerId, pairNum);
                }
            });
        });
        
        // Modo clique
        let clickMode = false;
        
        clickModeToggle.addEventListener('change', function() {
            clickMode = this.checked;
            availablePlayers.classList.toggle('cursor-pointer', clickMode);
        });
        
        availablePlayers.addEventListener('click', function(e) {
            if (!clickMode) return;
            
            const playerItem = e.target.closest('.player-item');
            if (!playerItem) return;
            
            const playerId = parseInt(playerItem.dataset.playerId);
            
            // Encontrar primeira dupla disponível
            for (let i = 1; i <= 6; i++) {
                if (addPlayerToPair(playerId, i)) break;
            }
        });
        
        // Botões de ação
        randomizeBtn.addEventListener('click', function() {
            // Limpar duplas atuais
            Object.keys(pairs).forEach(pairNum => {
                pairs[pairNum] = [];
            });
            selectedPlayers = [];
            
            // Embaralhar jogadores disponíveis
            const availablePlayerIds = Array.from(availablePlayers.querySelectorAll('.player-item'))
                .map(item => parseInt(item.dataset.playerId));
            
            // Para torneios mistos, separar por gênero
            if (isMixed) {
                const males = availablePlayerIds.filter(id => {
                    const player = document.querySelector(`[data-player-id="${id}"]`);
                    return player && player.dataset.playerGender === 'male';
                });
                const females = availablePlayerIds.filter(id => {
                    const player = document.querySelector(`[data-player-id="${id}"]`);
                    return player && player.dataset.playerGender === 'female';
                });
                
                // Embaralhar e pegar apenas 6 de cada
                males.sort(() => Math.random() - 0.5);
                females.sort(() => Math.random() - 0.5);
                
                const selectedMales = males.slice(0, 6);
                const selectedFemales = females.slice(0, 6);
                
                // Formar duplas mistas
                for (let i = 0; i < 6; i++) {
                    pairs[i + 1] = [selectedMales[i], selectedFemales[i]];
                    selectedPlayers.push(selectedMales[i], selectedFemales[i]);
                }
            } else {
                // Embaralhar todos e formar duplas
                availablePlayerIds.sort(() => Math.random() - 0.5);
                const selected = availablePlayerIds.slice(0, 12);
                
                for (let i = 0; i < 6; i++) {
                    pairs[i + 1] = [selected[i * 2], selected[i * 2 + 1]];
                    selectedPlayers.push(selected[i * 2], selected[i * 2 + 1]);
                }
            }
            
            // Atualizar UI
            updateCounters();
            updatePairHints();
            updateHiddenInputs();
            
            // Atualizar chips
            Object.keys(pairs).forEach(pairNum => {
                pairs[pairNum].forEach(playerId => {
                    updatePlayerInPair(playerId, pairNum);
                });
            });
        });
        
        autoCompleteBtn.addEventListener('click', function() {
            // Implementar auto completar baseado em critérios
            // Por enquanto, apenas randomizar
            randomizeBtn.click();
        });
        
        clearBtn.addEventListener('click', function() {
            Object.keys(pairs).forEach(pairNum => {
                pairs[pairNum] = [];
            });
            selectedPlayers = [];
            
            // Limpar UI
            document.querySelectorAll('.pair-players').forEach(container => {
                container.innerHTML = '<div class="pair-hint text-xs text-gray-400 text-center py-2">Aguardando jogadores</div>';
            });
            
            updateCounters();
            updatePairHints();
            updateHiddenInputs();
        });
        
        // Inicializar
        updateCounters();
        updatePairHints();
        
        // Ativar modo clique por padrão
        clickModeToggle.dispatchEvent(new Event('change'));
    @endif
});
</script>
