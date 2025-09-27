<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Perfil do Jogador
            </h2>
            <a href="{{ route('players.index') }}"
               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Voltar
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Seleção de Temporadas -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">📊 Escolher Estatísticas por Temporada</h3>
                    
                    <form method="GET" action="{{ route('players.show', $player) }}" id="seasonForm">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Seleção de Temporadas -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-3">
                                    Selecione as temporadas para análise:
                                </label>
                                <div class="space-y-2 max-h-48 overflow-y-auto border border-gray-200 rounded-md p-3">
                                    <label class="flex items-center">
                                        <input type="checkbox" 
                                               name="seasons[]" 
                                               value="all" 
                                               {{ empty($selectedSeasons) || in_array('all', $selectedSeasons ?? []) ? 'checked' : '' }}
                                               class="season-checkbox rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <span class="ml-2 text-sm font-medium text-gray-900">Todas as temporadas</span>
                                    </label>
                                    @foreach($seasons as $season)
                                        <label class="flex items-center">
                                            <input type="checkbox" 
                                                   name="seasons[]" 
                                                   value="{{ $season->id }}"
                                                   {{ in_array($season->id, $selectedSeasons ?? []) ? 'checked' : '' }}
                                                   class="season-checkbox rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                            <span class="ml-2 text-sm text-gray-700">
                                                {{ $season->name }}
                                                <span class="text-xs text-gray-500">
                                                    ({{ $season->status === 'active' ? 'Ativa' : 'Encerrada' }})
                                                </span>
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Opções de Visualização -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-3">
                                    Tipo de análise:
                                </label>
                                <div class="space-y-3">
                                    <label class="flex items-center">
                                        <input type="radio" 
                                               name="analysis_type" 
                                               value="combined" 
                                               {{ ($analysisType ?? 'combined') === 'combined' ? 'checked' : '' }}
                                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <span class="ml-2 text-sm text-gray-700">
                                            <strong>Dados Combinados</strong><br>
                                            <span class="text-xs text-gray-500">Soma de todas as temporadas selecionadas</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" 
                                               name="analysis_type" 
                                               value="comparison" 
                                               {{ ($analysisType ?? '') === 'comparison' ? 'checked' : '' }}
                                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <span class="ml-2 text-sm text-gray-700">
                                            <strong>Comparação</strong><br>
                                            <span class="text-xs text-gray-500">Compare performance entre temporadas</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" 
                                               name="analysis_type" 
                                               value="evolution" 
                                               {{ ($analysisType ?? '') === 'evolution' ? 'checked' : '' }}
                                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <span class="ml-2 text-sm text-gray-700">
                                            <strong>Evolução</strong><br>
                                            <span class="text-xs text-gray-500">Mostra progresso ao longo do tempo</span>
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Botões de Ação -->
                        <div class="mt-6 flex flex-col sm:flex-row gap-3">
                            <button type="submit"
                                    class="flex-1 sm:flex-none inline-flex items-center justify-center px-6 py-3 bg-indigo-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                                Analisar Estatísticas
                            </button>
                            
                            <a href="{{ route('players.show', $player) }}"
                               class="flex-1 sm:flex-none inline-flex items-center justify-center px-6 py-3 bg-gray-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Limpar Filtros
                            </a>
                        </div>
                    </form>

                    <!-- Indicador de Filtros Ativos -->
                    @if(!empty($selectedSeasons) && !in_array('all', $selectedSeasons))
                        <div class="mt-4 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-blue-800">
                                        <strong>Filtros ativos:</strong>
                                        @foreach($selectedSeasons as $seasonId)
                                            @php
                                                $season = $seasons->find($seasonId);
                                            @endphp
                                            @if($season)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 ml-1">
                                                    {{ $season->name }}
                                                </span>
                                            @endif
                                        @endforeach
                                    </p>
                                    <p class="text-xs text-blue-600 mt-1">
                                        Tipo de análise: {{ ucfirst($analysisType ?? 'combined') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- JavaScript para controle de checkboxes -->
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const checkboxes = document.querySelectorAll('.season-checkbox');
                    const allCheckbox = document.querySelector('input[value="all"]');
                    
                    // Quando "Todas as temporadas" é selecionada
                    allCheckbox.addEventListener('change', function() {
                        if (this.checked) {
                            checkboxes.forEach(cb => {
                                if (cb.value !== 'all') cb.checked = false;
                            });
                        }
                    });
                    
                    // Quando uma temporada específica é selecionada
                    checkboxes.forEach(cb => {
                        if (cb.value !== 'all') {
                            cb.addEventListener('change', function() {
                                if (this.checked) {
                                    allCheckbox.checked = false;
                                }
                            });
                        }
                    });
                });
            </script>

            <!-- Informações Básicas -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informações Pessoais</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <span class="font-medium">Nome:</span>
                            <span class="ml-2">{{ $player->name }}</span>
                        </div>
                        <div>
                            <span class="font-medium">Email:</span>
                            <span class="ml-2">{{ $player->email }}</span>
                        </div>
                        <div>
                            <span class="font-medium">Telefone:</span>
                            <span class="ml-2">{{ $player->phone ?? 'Não informado' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Estatísticas Gerais -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Estatísticas Gerais</h3>
                    
                    @if($analysisType === 'comparison')
                        <!-- Visualização de Comparação -->
                        <div class="space-y-6">
                            @foreach($stats as $seasonId => $seasonStats)
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <h4 class="text-md font-semibold text-gray-800 mb-3">{{ $seasonStats['season_name'] }}</h4>
                                    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                                        <div class="bg-gray-50 p-3 rounded-lg text-center">
                                            <div class="text-xl font-bold text-indigo-600">{{ $seasonStats['total_points'] }}</div>
                                            <div class="text-xs text-gray-600">Pontos</div>
                                        </div>
                                        <div class="bg-gray-50 p-3 rounded-lg text-center">
                                            <div class="text-xl font-bold text-green-600">{{ $seasonStats['total_wins'] }}</div>
                                            <div class="text-xs text-gray-600">Vitórias</div>
                                        </div>
                                        <div class="bg-gray-50 p-3 rounded-lg text-center">
                                            <div class="text-xl font-bold text-red-600">{{ $seasonStats['total_losses'] }}</div>
                                            <div class="text-xs text-gray-600">Derrotas</div>
                                        </div>
                                        <div class="bg-gray-50 p-3 rounded-lg text-center">
                                            <div class="text-xl font-bold text-blue-600">{{ $seasonStats['tournaments_played'] }}</div>
                                            <div class="text-xs text-gray-600">Torneios</div>
                                        </div>
                                        <div class="bg-gray-50 p-3 rounded-lg text-center">
                                            <div class="text-xl font-bold text-purple-600">{{ $seasonStats['win_rate'] }}%</div>
                                            <div class="text-xs text-gray-600">Taxa de Vitórias</div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @elseif($analysisType === 'evolution')
                        <!-- Visualização de Evolução -->
                        <div class="space-y-4">
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <div class="bg-gradient-to-r from-blue-50 to-blue-100 p-4 rounded-lg text-center">
                                    <div class="text-2xl font-bold text-blue-600">{{ $stats['monthly_data']->count() }}</div>
                                    <div class="text-sm text-blue-700">Meses com Dados</div>
                                </div>
                                <div class="bg-gradient-to-r from-green-50 to-green-100 p-4 rounded-lg text-center">
                                    <div class="text-2xl font-bold text-green-600">{{ ucfirst($stats['trend']) }}</div>
                                    <div class="text-sm text-green-700">Tendência</div>
                                </div>
                                <div class="bg-gradient-to-r from-purple-50 to-purple-100 p-4 rounded-lg text-center">
                                    <div class="text-2xl font-bold text-purple-600">{{ ucfirst($stats['consistency']) }}</div>
                                    <div class="text-sm text-purple-700">Consistência</div>
                                </div>
                                <div class="bg-gradient-to-r from-orange-50 to-orange-100 p-4 rounded-lg text-center">
                                    <div class="text-2xl font-bold text-orange-600">{{ $stats['seasonal_data']->count() }}</div>
                                    <div class="text-sm text-orange-700">Temporadas</div>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- Visualização Padrão (Dados Combinados) -->
                        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                            <div class="bg-gray-50 p-4 rounded-lg text-center">
                                <div class="text-2xl font-bold text-indigo-600">{{ $stats['total_points'] }}</div>
                                <div class="text-sm text-gray-600">Pontos Totais</div>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg text-center">
                                <div class="text-2xl font-bold text-green-600">{{ $stats['total_wins'] }}</div>
                                <div class="text-sm text-gray-600">Vitórias</div>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg text-center">
                                <div class="text-2xl font-bold text-red-600">{{ $stats['total_losses'] }}</div>
                                <div class="text-sm text-gray-600">Derrotas</div>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg text-center">
                                <div class="text-2xl font-bold text-blue-600">{{ $stats['tournaments_played'] }}</div>
                                <div class="text-sm text-gray-600">Torneios</div>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg text-center">
                                <div class="text-2xl font-bold text-purple-600">{{ $stats['win_rate'] }}%</div>
                                <div class="text-sm text-gray-600">Aproveitamento</div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Sistema de Balanceamento -->
            @if(!empty($balanceData))
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">⚖️ Sistema de Balanceamento</h3>
                        
                        @if($analysisType === 'comparison')
                            <!-- Visualização de Comparação -->
                            <div class="space-y-6">
                                @foreach($balanceData as $seasonId => $balance)
                                    @if($balance['position'])
                                        <div class="border border-gray-200 rounded-lg p-4">
                                            <h4 class="text-md font-semibold text-gray-800 mb-3">
                                                {{ $seasons->find($seasonId)->name ?? 'Temporada ' . $seasonId }}
                                            </h4>
                                            
                                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                                <!-- Posição no Ranking -->
                                                <div class="bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200 rounded-lg p-4">
                                                    <div class="flex items-center justify-between">
                                                        <div>
                                                            <h5 class="text-sm font-medium text-blue-800">Posição no Ranking</h5>
                                                            <div class="text-2xl font-bold text-blue-600">#{{ $balance['position'] }}</div>
                                                            <div class="text-xs text-blue-600">de {{ $balance['total_players'] }} jogadores</div>
                                                        </div>
                                                        <div class="text-3xl">
                                                            @if($balance['position'] <= 5)
                                                                🏆
                                                            @elseif($balance['position'] <= 10)
                                                                🥇
                                                            @elseif($balance['position'] <= 20)
                                                                🥈
                                                            @else
                                                                🥉
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Multiplicador Atual -->
                                                <div class="bg-gradient-to-br from-green-50 to-green-100 border border-green-200 rounded-lg p-4">
                                                    <div class="flex items-center justify-between">
                                                        <div>
                                                            <h5 class="text-sm font-medium text-green-800">Multiplicador</h5>
                                                            <div class="text-2xl font-bold text-green-600">{{ $balance['multiplier'] }}x</div>
                                                            <div class="text-xs text-green-600">
                                                                @if($balance['multiplier'] > 1.0)
                                                                    +{{ $balance['points_boost'] }}% bonus
                                                                @elseif($balance['multiplier'] < 1.0)
                                                                    -{{ $balance['points_penalty'] }}% penalidade
                                                                @else
                                                                    Normal
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="text-3xl">
                                                            @if($balance['multiplier'] > 1.0)
                                                                📈
                                                            @elseif($balance['multiplier'] < 1.0)
                                                                📉
                                                            @else
                                                                ➡️
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Tier Atual -->
                                                <div class="bg-gradient-to-br from-purple-50 to-purple-100 border border-purple-200 rounded-lg p-4">
                                                    <div class="flex items-center justify-between">
                                                        <div>
                                                            <h5 class="text-sm font-medium text-purple-800">Tier Atual</h5>
                                                            <div class="text-lg font-bold text-purple-600">
                                                                @switch($balance['balance_status'])
                                                                    @case('top_player')
                                                                        Top Player
                                                                        @break
                                                                    @case('high_tier')
                                                                        High Tier
                                                                        @break
                                                                    @case('mid_tier')
                                                                        Mid Tier
                                                                        @break
                                                                    @case('low_tier')
                                                                        Low Tier
                                                                        @break
                                                                    @case('underdog')
                                                                        Underdog
                                                                        @break
                                                                    @default
                                                                        Não Classificado
                                                                @endswitch
                                                            </div>
                                                        </div>
                                                        <div class="text-3xl">
                                                            @switch($balance['balance_status'])
                                                                @case('top_player')
                                                                    👑
                                                                    @break
                                                                @case('high_tier')
                                                                    🥇
                                                                    @break
                                                                @case('mid_tier')
                                                                    🥈
                                                                    @break
                                                                @case('low_tier')
                                                                    🥉
                                                                    @break
                                                                @case('underdog')
                                                                    🎯
                                                                    @break
                                                                @default
                                                                    ❓
                                                            @endswitch
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Exemplo de Pontuação -->
                                                <div class="bg-gradient-to-br from-orange-50 to-orange-100 border border-orange-200 rounded-lg p-4">
                                                    <h5 class="text-sm font-medium text-orange-800 mb-2">Exemplo de Pontuação</h5>
                                                    <div class="text-sm text-orange-700">
                                                        <div>Vitória: <span class="font-bold">{{ round(3 * $balance['multiplier'], 1) }} pts</span></div>
                                                        <div>Derrota: <span class="font-bold">{{ round(1 * $balance['multiplier'], 1) }} pts</span></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                            <div class="flex items-center">
                                                <div class="text-yellow-600 mr-3">⚠️</div>
                                                <div>
                                                    <h4 class="font-semibold text-yellow-800">Jogador Não Classificado</h4>
                                                    <p class="text-yellow-700 text-sm">
                                                        Este jogador ainda não possui estatísticas suficientes para aparecer no ranking desta temporada.
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @else
                            <!-- Visualização Padrão (Dados Combinados) -->
                            <div class="space-y-4">
                                @foreach($balanceData as $seasonId => $balance)
                                    @if($balance['position'])
                                        <div class="border border-gray-200 rounded-lg p-4">
                                            <div class="flex justify-between items-center mb-3">
                                                <h4 class="text-md font-semibold text-gray-800">
                                                    {{ $seasons->find($seasonId)->name ?? 'Temporada ' . $seasonId }}
                                                </h4>
                                                <span class="text-sm text-gray-500">#{{ $balance['position'] }} de {{ $balance['total_players'] }}</span>
                                            </div>
                                            
                                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                                <div class="text-center">
                                                    <div class="text-2xl font-bold text-blue-600">{{ $balance['multiplier'] }}x</div>
                                                    <div class="text-sm text-gray-600">Multiplicador</div>
                                                </div>
                                                <div class="text-center">
                                                    <div class="text-2xl font-bold 
                                                        @if($balance['multiplier'] > 1.0) text-green-600
                                                        @elseif($balance['multiplier'] < 1.0) text-red-600
                                                        @else text-gray-600
                                                        @endif">
                                                        @if($balance['multiplier'] > 1.0)
                                                            +{{ $balance['points_boost'] }}%
                                                        @elseif($balance['multiplier'] < 1.0)
                                                            -{{ $balance['points_penalty'] }}%
                                                        @else
                                                            0%
                                                        @endif
                                                    </div>
                                                    <div class="text-sm text-gray-600">Ajuste de Pontos</div>
                                                </div>
                                                <div class="text-center">
                                                    <div class="text-lg font-bold text-purple-600">
                                                        @switch($balance['balance_status'])
                                                            @case('top_player')
                                                                Top Player
                                                                @break
                                                            @case('high_tier')
                                                                High Tier
                                                                @break
                                                            @case('mid_tier')
                                                                Mid Tier
                                                                @break
                                                            @case('low_tier')
                                                                Low Tier
                                                                @break
                                                            @case('underdog')
                                                                Underdog
                                                                @break
                                                            @default
                                                                Não Classificado
                                                        @endswitch
                                                    </div>
                                                    <div class="text-sm text-gray-600">Tier Atual</div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @endif

                        <!-- Explicação do Sistema -->
                        <div class="mt-6 bg-gray-50 border border-gray-200 rounded-lg p-4">
                            <h4 class="font-semibold text-gray-800 mb-2">ℹ️ Como Funciona o Sistema de Balanceamento</h4>
                            <div class="text-sm text-gray-600 space-y-1">
                                <p>• <strong>Top Players (Top 20%):</strong> Ganham menos pontos para manter o desafio</p>
                                <p>• <strong>Underdogs (Bottom 20%):</strong> Ganham mais pontos para motivar e equilibrar</p>
                                <p>• <strong>Mid Tier:</strong> Pontuação normal baseada na performance</p>
                                <p>• <strong>Objetivo:</strong> Criar competições mais equilibradas e motivar todos os jogadores</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Histórico de Torneios -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Histórico de Torneios</h3>
                    @if($player->playerScores->isEmpty())
                        <p class="text-gray-500 italic">Nenhum torneio encontrado.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Torneio</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pontos</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">V/D</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aproveitamento</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($player->playerScores->sortByDesc('tournament.start_date') as $score)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm">
                                                    <a href="{{ route('tournaments.show', ['tournament' => $score->tournament, 'player_id' => $player->id]) }}"
                                                       class="font-medium text-indigo-600 hover:text-indigo-900">
                                                        {{ $score->tournament->name }}
                                                    </a>
                                                    <div class="text-gray-500">{{ $score->tournament->location }}</div>
                                                    <div class="text-xs text-gray-400">
                                                        {{ $score->tournament->type === 'super_8_doubles' ? 'Super 8' : 
                                                           ($score->tournament->type === 'super_8_fixed_pairs' ? 'Super 8 Fixas' : 'Super 12') }}
                                                        • {{ $score->tournament->category === 'male' ? 'Masculino' : ($score->tournament->category === 'female' ? 'Feminino' : 'Mista') }}
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $score->tournament->start_date->format('d/m/Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                {{ $score->points }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="text-green-600">{{ $score->games_won }}</span>
                                                /
                                                <span class="text-red-600">{{ $score->games_lost }}</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                @php
                                                    $total = $score->games_won + $score->games_lost;
                                                    $winRate = $total > 0 ? round(($score->games_won / $total) * 100, 1) : 0;
                                                @endphp
                                                {{ $winRate }}%
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Seção de Estatísticas do Jogador -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Estatísticas de {{ $player->name }}</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- Melhor Posição -->
                        <div class="border rounded-lg p-4 bg-gradient-to-br from-indigo-50 to-white">
                            <h4 class="text-sm font-semibold text-indigo-600 mb-2">Melhor Posição em Torneio</h4>
                            @php
                                $bestRanking = $player->playerScores()
                                    ->with('tournament')
                                    ->orderBy('points', 'desc')
                                    ->first();
                            @endphp
                            <p class="text-2xl font-bold text-gray-900">
                                @if($bestRanking)
                                    {{ $bestRanking->points }} pontos
                                @else
                                    N/A
                                @endif
                            </p>
                            <p class="text-sm text-gray-600">
                                {{ $bestRanking ? $bestRanking->tournament->name : 'Nenhum torneio' }}
                            </p>
                        </div>

                        <!-- Parceiro Mais Vitorioso -->
                        <div class="border rounded-lg p-4 bg-gradient-to-br from-green-50 to-white">
                            <h4 class="text-sm font-semibold text-green-600 mb-2">Parceiro Mais Vitorioso</h4>
                            @php
                                $bestPartner = $player->getBestPartner();
                            @endphp
                            @if($bestPartner)
                                <p class="text-xl font-bold text-gray-900">{{ $bestPartner->name }}</p>
                                <p class="text-sm text-gray-600">
                                    {{ $bestPartner->wins }} vitórias juntos
                                </p>
                            @else
                                <p class="text-gray-500">Nenhum parceiro ainda</p>
                            @endif
                        </div>

                        <!-- Adversário Mais Difícil -->
                        <div class="border rounded-lg p-4 bg-gradient-to-br from-red-50 to-white">
                            <h4 class="text-sm font-semibold text-red-600 mb-2">Adversário Mais Difícil</h4>
                            @php
                                $toughestOpponent = $player->getToughestOpponent();
                            @endphp
                            @if($toughestOpponent)
                                <p class="text-xl font-bold text-gray-900">{{ $toughestOpponent->name }}</p>
                                <p class="text-sm text-gray-600">
                                    {{ $toughestOpponent->wins }} vitórias contra você
                                </p>
                            @else
                                <p class="text-gray-500">Nenhum adversário ainda</p>
                            @endif
                        </div>

                        <!-- Sequência de Vitórias -->
                        <div class="border rounded-lg p-4 bg-gradient-to-br from-yellow-50 to-white">
                            <h4 class="text-sm font-semibold text-yellow-600 mb-2">Melhor Sequência</h4>
                            <p class="text-2xl font-bold text-gray-900">{{ $player->getBestWinStreak() }} vitórias</p>
                            <p class="text-sm text-gray-600">Sequência atual: {{ $player->getCurrentWinStreak() }}</p>
                        </div>

                        <!-- Taxa de Vitória por Quadra -->
                        <div class="border rounded-lg p-4 bg-gradient-to-br from-purple-50 to-white">
                            <h4 class="text-sm font-semibold text-purple-600 mb-2">Melhor Quadra</h4>
                            @php
                                $bestCourt = $player->getBestCourt();
                            @endphp
                            @if($bestCourt)
                                <p class="text-xl font-bold text-gray-900">{{ $bestCourt->name }}</p>
                                <p class="text-sm text-gray-600">
                                    {{ number_format($bestCourt->win_rate, 1) }}% de aproveitamento
                                </p>
                            @else
                                <p class="text-gray-500">Sem dados suficientes</p>
                            @endif
                        </div>

                        <!-- Média de Pontos por Torneio -->
                        <div class="border rounded-lg p-4 bg-gradient-to-br from-blue-50 to-white">
                            <h4 class="text-sm font-semibold text-blue-600 mb-2">Média por Torneio</h4>
                            <p class="text-2xl font-bold text-gray-900">
                                {{ number_format($player->playerScores()->avg('points') ?? 0, 1) }}
                            </p>
                            <p class="text-sm text-gray-600">pontos por torneio</p>
                        </div>

                        <!-- Parceiro que Mais Ganhou Partidas -->
                        <div class="border rounded-lg p-4 bg-gradient-to-br from-emerald-50 to-white">
                            <h4 class="text-sm font-semibold text-emerald-600 mb-2">Parceiro Mais Vitorioso</h4>
                            @php
                                $bestPartnerByWins = $player->getBestPartnerByWins();
                            @endphp
                            @if($bestPartnerByWins)
                                <p class="text-xl font-bold text-gray-900">{{ $bestPartnerByWins->name }}</p>
                                <p class="text-sm text-gray-600">
                                    {{ $bestPartnerByWins->wins }} vitórias em {{ $bestPartnerByWins->total_matches }} partidas
                                </p>
                            @else
                                <p class="text-gray-500">Nenhum parceiro ainda</p>
                            @endif
                        </div>

                        <!-- Parceiro que Mais Perdeu Partidas -->
                        <div class="border rounded-lg p-4 bg-gradient-to-br from-orange-50 to-white">
                            <h4 class="text-sm font-semibold text-orange-600 mb-2">Parceiro Menos Vitorioso</h4>
                            @php
                                $worstPartnerByLosses = $player->getWorstPartnerByLosses();
                            @endphp
                            @if($worstPartnerByLosses)
                                <p class="text-xl font-bold text-gray-900">{{ $worstPartnerByLosses->name }}</p>
                                <p class="text-sm text-gray-600">
                                    {{ $worstPartnerByLosses->losses }} derrotas em {{ $worstPartnerByLosses->total_matches }} partidas
                                </p>
                            @else
                                <p class="text-gray-500">Nenhum parceiro ainda</p>
                            @endif
                        </div>

                        <!-- Adversário que Mais Ganhou -->
                        <div class="border rounded-lg p-4 bg-gradient-to-br from-rose-50 to-white">
                            <h4 class="text-sm font-semibold text-rose-600 mb-2">Adversário Mais Vitorioso</h4>
                            @php
                                $bestOpponentByWins = $player->getBestOpponentByWins();
                            @endphp
                            @if($bestOpponentByWins)
                                <p class="text-xl font-bold text-gray-900">{{ $bestOpponentByWins->name }}</p>
                                <p class="text-sm text-gray-600">
                                    {{ $bestOpponentByWins->wins }} vitórias contra você
                                </p>
                            @else
                                <p class="text-gray-500">Nenhum adversário ainda</p>
                            @endif
                        </div>

                        <!-- Adversário Mais Difícil -->
                        <div class="border rounded-lg p-4 bg-gradient-to-br from-red-50 to-white">
                            <h4 class="text-sm font-semibold text-red-600 mb-2">Adversário Mais Difícil</h4>
                            @php
                                $toughestOpponentByLosses = $player->getToughestOpponentByLosses();
                            @endphp
                            @if($toughestOpponentByLosses)
                                <p class="text-xl font-bold text-gray-900">{{ $toughestOpponentByLosses->name }}</p>
                                <p class="text-sm text-gray-600">
                                    {{ $toughestOpponentByLosses->losses }} derrotas para ele
                                </p>
                            @else
                                <p class="text-gray-500">Nenhum adversário ainda</p>
                            @endif
                        </div>

                        <!-- Estatísticas de Categoria -->
                        <div class="border rounded-lg p-4 bg-gradient-to-br from-indigo-50 to-white">
                            <h4 class="text-sm font-semibold text-indigo-600 mb-2">Categoria</h4>
                            @php
                                $categoryStats = $player->getCategoryStats();
                            @endphp
                            <p class="text-xl font-bold text-gray-900">{{ $categoryStats['category_name'] }}</p>
                            <p class="text-sm text-gray-600">
                                {{ $categoryStats['total_matches'] }} partidas • {{ $categoryStats['win_rate'] }}% aproveitamento
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Métricas de Quadras -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">🏟️ Performance por Quadra</h3>
                    
                    @if($analysisType === 'comparison')
                        <!-- Visualização de Comparação -->
                        <div class="space-y-6">
                            @foreach($courtStats as $seasonId => $seasonCourtStats)
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <h4 class="text-md font-semibold text-gray-800 mb-3">{{ $seasonCourtStats['season_name'] }}</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <!-- Quadra da Sorte -->
                                        <div class="border rounded-lg p-3 bg-gradient-to-br from-green-50 to-white">
                                            <h5 class="text-sm font-semibold text-green-600 mb-2">Quadra da Sorte</h5>
                                            @if($seasonCourtStats['best_court'])
                                                <p class="text-lg font-bold text-gray-900">{{ $seasonCourtStats['best_court']->name }}</p>
                                                <p class="text-sm text-gray-600">
                                                    {{ number_format($seasonCourtStats['best_court']->win_rate, 1) }}% de aproveitamento
                                                </p>
                                                <p class="text-xs text-gray-500">{{ $seasonCourtStats['best_court']->total_matches }} partidas</p>
                                            @else
                                                <p class="text-gray-500">Sem dados suficientes</p>
                                            @endif
                                        </div>

                                        <!-- Quadra do Azar -->
                                        <div class="border rounded-lg p-3 bg-gradient-to-br from-red-50 to-white">
                                            <h5 class="text-sm font-semibold text-red-600 mb-2">Quadra do Azar</h5>
                                            @if($seasonCourtStats['worst_court'])
                                                <p class="text-lg font-bold text-gray-900">{{ $seasonCourtStats['worst_court']->name }}</p>
                                                <p class="text-sm text-gray-600">
                                                    {{ number_format($seasonCourtStats['worst_court']->win_rate, 1) }}% de aproveitamento
                                                </p>
                                                <p class="text-xs text-gray-500">{{ $seasonCourtStats['worst_court']->total_matches }} partidas</p>
                                            @else
                                                <p class="text-gray-500">Sem dados suficientes</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <!-- Visualização Padrão (Dados Combinados ou Evolução) -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Quadra da Sorte -->
                            <div class="border rounded-lg p-4 bg-gradient-to-br from-green-50 to-white">
                                <h4 class="text-sm font-semibold text-green-600 mb-2">Quadra da Sorte</h4>
                                @if($courtStats['best_court'])
                                    <p class="text-xl font-bold text-gray-900">{{ $courtStats['best_court']->name }}</p>
                                    <p class="text-sm text-gray-600">
                                        {{ number_format($courtStats['best_court']->win_rate, 1) }}% de aproveitamento
                                    </p>
                                    <p class="text-xs text-gray-500">{{ $courtStats['best_court']->total_matches }} partidas</p>
                                @else
                                    <p class="text-gray-500">Sem dados suficientes</p>
                                @endif
                            </div>

                            <!-- Quadra do Azar -->
                            <div class="border rounded-lg p-4 bg-gradient-to-br from-red-50 to-white">
                                <h4 class="text-sm font-semibold text-red-600 mb-2">Quadra do Azar</h4>
                                @if($courtStats['worst_court'])
                                    <p class="text-xl font-bold text-gray-900">{{ $courtStats['worst_court']->name }}</p>
                                    <p class="text-sm text-gray-600">
                                        {{ number_format($courtStats['worst_court']->win_rate, 1) }}% de aproveitamento
                                    </p>
                                    <p class="text-xs text-gray-500">{{ $courtStats['worst_court']->total_matches }} partidas</p>
                                @else
                                    <p class="text-gray-500">Sem dados suficientes</p>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Métricas de Parcerias -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">🤝 Análise de Parcerias</h3>
                    
                    @if($analysisType === 'comparison')
                        <!-- Visualização de Comparação -->
                        <div class="space-y-6">
                            @foreach($partnershipStats as $seasonId => $seasonPartnershipStats)
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <h4 class="text-md font-semibold text-gray-800 mb-3">{{ $seasonPartnershipStats['season_name'] }}</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                        <!-- Melhor Parceiro -->
                                        <div class="border rounded-lg p-3 bg-gradient-to-br from-green-50 to-white">
                                            <h5 class="text-sm font-semibold text-green-600 mb-2">Melhor Parceiro</h5>
                                            @if($seasonPartnershipStats['best_partner'])
                                                <p class="text-lg font-bold text-gray-900">{{ $seasonPartnershipStats['best_partner']->name }}</p>
                                                <p class="text-sm text-gray-600">{{ $seasonPartnershipStats['best_partner']->wins }} vitórias juntos</p>
                                            @else
                                                <p class="text-gray-500">Sem dados suficientes</p>
                                            @endif
                                        </div>

                                        <!-- Pior Parceiro -->
                                        <div class="border rounded-lg p-3 bg-gradient-to-br from-red-50 to-white">
                                            <h5 class="text-sm font-semibold text-red-600 mb-2">Pior Parceiro</h5>
                                            @if($seasonPartnershipStats['worst_partner'])
                                                <p class="text-lg font-bold text-gray-900">{{ $seasonPartnershipStats['worst_partner']->name }}</p>
                                                <p class="text-sm text-gray-600">{{ number_format($seasonPartnershipStats['worst_partner']->win_rate, 1) }}% de aproveitamento</p>
                                                <p class="text-xs text-gray-500">{{ $seasonPartnershipStats['worst_partner']->total_matches }} partidas</p>
                                            @else
                                                <p class="text-gray-500">Sem dados suficientes</p>
                                            @endif
                                        </div>

                                        <!-- Parceiro Mais Frequente -->
                                        <div class="border rounded-lg p-3 bg-gradient-to-br from-blue-50 to-white">
                                            <h5 class="text-sm font-semibold text-blue-600 mb-2">Parceiro Mais Frequente</h5>
                                            @if($seasonPartnershipStats['most_frequent_partner'])
                                                <p class="text-lg font-bold text-gray-900">{{ $seasonPartnershipStats['most_frequent_partner']->name }}</p>
                                                <p class="text-sm text-gray-600">{{ $seasonPartnershipStats['most_frequent_partner']->matches_count }} partidas juntos</p>
                                            @else
                                                <p class="text-gray-500">Sem dados suficientes</p>
                                            @endif
                                        </div>

                                        <!-- Número de Parceiros Diferentes -->
                                        <div class="border rounded-lg p-3 bg-gradient-to-br from-purple-50 to-white">
                                            <h5 class="text-sm font-semibold text-purple-600 mb-2">Parceiros Diferentes</h5>
                                            <p class="text-xl font-bold text-gray-900">{{ $seasonPartnershipStats['different_partners_count'] }}</p>
                                            <p class="text-sm text-gray-600">jogadores diferentes</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <!-- Visualização Padrão (Dados Combinados ou Evolução) -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <!-- Melhor Parceiro -->
                            <div class="border rounded-lg p-4 bg-gradient-to-br from-green-50 to-white">
                                <h4 class="text-sm font-semibold text-green-600 mb-2">Melhor Parceiro</h4>
                                @if($partnershipStats['best_partner'])
                                    <p class="text-lg font-bold text-gray-900">{{ $partnershipStats['best_partner']->name }}</p>
                                    <p class="text-sm text-gray-600">{{ $partnershipStats['best_partner']->wins }} vitórias juntos</p>
                                @else
                                    <p class="text-gray-500">Sem dados suficientes</p>
                                @endif
                            </div>

                            <!-- Pior Parceiro -->
                            <div class="border rounded-lg p-4 bg-gradient-to-br from-red-50 to-white">
                                <h4 class="text-sm font-semibold text-red-600 mb-2">Pior Parceiro</h4>
                                @if($partnershipStats['worst_partner'])
                                    <p class="text-lg font-bold text-gray-900">{{ $partnershipStats['worst_partner']->name }}</p>
                                    <p class="text-sm text-gray-600">{{ number_format($partnershipStats['worst_partner']->win_rate, 1) }}% de aproveitamento</p>
                                    <p class="text-xs text-gray-500">{{ $partnershipStats['worst_partner']->total_matches }} partidas</p>
                                @else
                                    <p class="text-gray-500">Sem dados suficientes</p>
                                @endif
                            </div>

                            <!-- Parceiro Mais Frequente -->
                            <div class="border rounded-lg p-4 bg-gradient-to-br from-blue-50 to-white">
                                <h4 class="text-sm font-semibold text-blue-600 mb-2">Parceiro Mais Frequente</h4>
                                @if($partnershipStats['most_frequent_partner'])
                                    <p class="text-lg font-bold text-gray-900">{{ $partnershipStats['most_frequent_partner']->name }}</p>
                                    <p class="text-sm text-gray-600">{{ $partnershipStats['most_frequent_partner']->matches_count }} partidas juntos</p>
                                @else
                                    <p class="text-gray-500">Sem dados suficientes</p>
                                @endif
                            </div>

                            <!-- Número de Parceiros Diferentes -->
                            <div class="border rounded-lg p-4 bg-gradient-to-br from-purple-50 to-white">
                                <h4 class="text-sm font-semibold text-purple-600 mb-2">Parceiros Diferentes</h4>
                                <p class="text-2xl font-bold text-gray-900">{{ $partnershipStats['different_partners_count'] }}</p>
                                <p class="text-sm text-gray-600">jogadores diferentes</p>
                            </div>
                        </div>
                    @endif

                    <!-- Compatibilidade com Parceiros -->
                    @if($analysisType === 'comparison')
                        <!-- Visualização de Comparação -->
                        @foreach($partnershipStats as $seasonId => $seasonPartnershipStats)
                            @if($seasonPartnershipStats['compatibility']->count() > 0)
                                <div class="mt-6">
                                    <h4 class="text-sm font-semibold text-gray-700 mb-3">Compatibilidade com Parceiros - {{ $seasonPartnershipStats['season_name'] }}</h4>
                                    <div class="space-y-2">
                                        @foreach($seasonPartnershipStats['compatibility']->take(5) as $partner)
                                            <div class="flex justify-between items-center bg-gray-50 p-3 rounded-lg">
                                                <span class="font-medium">{{ $partner->name }}</span>
                                                <div class="flex items-center space-x-2">
                                                    <span class="text-sm text-gray-600">{{ $partner->total_matches }} partidas</span>
                                                    <span class="px-2 py-1 bg-indigo-100 text-indigo-800 text-xs rounded-full">
                                                        {{ number_format($partner->win_rate, 1) }}%
                                                    </span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    @else
                        <!-- Visualização Padrão (Dados Combinados ou Evolução) -->
                        @if($partnershipStats['compatibility']->count() > 0)
                            <div class="mt-6">
                                <h4 class="text-sm font-semibold text-gray-700 mb-3">Compatibilidade com Parceiros</h4>
                                <div class="space-y-2">
                                    @foreach($partnershipStats['compatibility']->take(5) as $partner)
                                        <div class="flex justify-between items-center bg-gray-50 p-3 rounded-lg">
                                            <span class="font-medium">{{ $partner->name }}</span>
                                            <div class="flex items-center space-x-2">
                                                <span class="text-sm text-gray-600">{{ $partner->total_matches }} partidas</span>
                                                <span class="px-2 py-1 bg-indigo-100 text-indigo-800 text-xs rounded-full">
                                                    {{ number_format($partner->win_rate, 1) }}%
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            </div>

            <!-- Métricas de Confrontos Diretos -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">⚔️ Confrontos Diretos</h3>
                    
                    @if($analysisType === 'comparison')
                        <!-- Visualização de Comparação -->
                        <div class="space-y-6">
                            @foreach($headToHeadStats as $seasonId => $seasonHeadToHeadStats)
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <h4 class="text-md font-semibold text-gray-800 mb-3">{{ $seasonHeadToHeadStats['season_name'] }}</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <!-- Maior Adversário -->
                                        <div class="border rounded-lg p-3 bg-gradient-to-br from-red-50 to-white">
                                            <h5 class="text-sm font-semibold text-red-600 mb-2">Maior Adversário</h5>
                                            @if($seasonHeadToHeadStats['toughest_opponent'])
                                                <p class="text-lg font-bold text-gray-900">{{ $seasonHeadToHeadStats['toughest_opponent']->name }}</p>
                                                <p class="text-sm text-gray-600">{{ $seasonHeadToHeadStats['toughest_opponent']->wins }} vitórias contra você</p>
                                            @else
                                                <p class="text-gray-500">Sem dados suficientes</p>
                                            @endif
                                        </div>

                                        <!-- Vítima Favorita -->
                                        <div class="border rounded-lg p-3 bg-gradient-to-br from-green-50 to-white">
                                            <h5 class="text-sm font-semibold text-green-600 mb-2">Vítima Favorita</h5>
                                            @if($seasonHeadToHeadStats['favorite_victim'])
                                                <p class="text-lg font-bold text-gray-900">{{ $seasonHeadToHeadStats['favorite_victim']->name }}</p>
                                                <p class="text-sm text-gray-600">{{ $seasonHeadToHeadStats['favorite_victim']->wins }} vitórias contra ele(a)</p>
                                            @else
                                                <p class="text-gray-500">Sem dados suficientes</p>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Rivalidades -->
                                    @if($seasonHeadToHeadStats['rivalries']->count() > 0)
                                        <div class="mt-4">
                                            <h5 class="text-sm font-semibold text-gray-700 mb-3">Maiores Rivalidades</h5>
                                            <div class="space-y-2">
                                                @foreach($seasonHeadToHeadStats['rivalries'] as $rival)
                                                    <div class="flex justify-between items-center bg-gray-50 p-3 rounded-lg">
                                                        <span class="font-medium">{{ $rival->name }}</span>
                                                        <span class="px-2 py-1 bg-orange-100 text-orange-800 text-xs rounded-full">
                                                            {{ $rival->matches_count }} partidas
                                                        </span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <!-- Visualização Padrão (Dados Combinados ou Evolução) -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Maior Adversário -->
                            <div class="border rounded-lg p-4 bg-gradient-to-br from-red-50 to-white">
                                <h4 class="text-sm font-semibold text-red-600 mb-2">Maior Adversário</h4>
                                @if($headToHeadStats['toughest_opponent'])
                                    <p class="text-lg font-bold text-gray-900">{{ $headToHeadStats['toughest_opponent']->name }}</p>
                                    <p class="text-sm text-gray-600">{{ $headToHeadStats['toughest_opponent']->wins }} vitórias contra você</p>
                                @else
                                    <p class="text-gray-500">Sem dados suficientes</p>
                                @endif
                            </div>

                            <!-- Vítima Favorita -->
                            <div class="border rounded-lg p-4 bg-gradient-to-br from-green-50 to-white">
                                <h4 class="text-sm font-semibold text-green-600 mb-2">Vítima Favorita</h4>
                                @if($headToHeadStats['favorite_victim'])
                                    <p class="text-lg font-bold text-gray-900">{{ $headToHeadStats['favorite_victim']->name }}</p>
                                    <p class="text-sm text-gray-600">{{ $headToHeadStats['favorite_victim']->wins }} vitórias contra ele(a)</p>
                                @else
                                    <p class="text-gray-500">Sem dados suficientes</p>
                                @endif
                            </div>
                        </div>

                        <!-- Rivalidades -->
                        @if($headToHeadStats['rivalries']->count() > 0)
                            <div class="mt-6">
                                <h4 class="text-sm font-semibold text-gray-700 mb-3">Maiores Rivalidades</h4>
                                <div class="space-y-2">
                                    @foreach($headToHeadStats['rivalries'] as $rival)
                                        <div class="flex justify-between items-center bg-gray-50 p-3 rounded-lg">
                                            <span class="font-medium">{{ $rival->name }}</span>
                                            <span class="px-2 py-1 bg-orange-100 text-orange-800 text-xs rounded-full">
                                                {{ $rival->matches_count }} partidas
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            </div>

            <!-- Métricas de Evolução -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">📈 Evolução e Tendências</h3>
                    
                    @if($analysisType === 'comparison')
                        <!-- Visualização de Comparação -->
                        <div class="space-y-6">
                            @foreach($evolutionStats as $seasonId => $seasonEvolutionStats)
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <h4 class="text-md font-semibold text-gray-800 mb-3">{{ $seasonEvolutionStats['season_name'] }}</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                        <!-- Tendência de Performance -->
                                        <div class="border rounded-lg p-3 bg-gradient-to-br from-indigo-50 to-white">
                                            <h5 class="text-sm font-semibold text-indigo-600 mb-2">Tendência</h5>
                                            @php
                                                $trend = $seasonEvolutionStats['performance_trend'];
                                                $trendConfig = [
                                                    'improving' => ['text' => 'Melhorando', 'class' => 'text-green-600', 'icon' => '↗️'],
                                                    'declining' => ['text' => 'Declinando', 'class' => 'text-red-600', 'icon' => '↘️'],
                                                    'stable' => ['text' => 'Estável', 'class' => 'text-blue-600', 'icon' => '→'],
                                                    'insufficient_data' => ['text' => 'Dados insuficientes', 'class' => 'text-gray-500', 'icon' => '❓']
                                                ];
                                                $config = $trendConfig[$trend] ?? $trendConfig['insufficient_data'];
                                            @endphp
                                            <p class="text-lg font-bold {{ $config['class'] }}">{{ $config['icon'] }} {{ $config['text'] }}</p>
                                        </div>

                                        <!-- Consistência -->
                                        <div class="border rounded-lg p-3 bg-gradient-to-br from-purple-50 to-white">
                                            <h5 class="text-sm font-semibold text-purple-600 mb-2">Consistência</h5>
                                            @php
                                                $consistency = $seasonEvolutionStats['consistency'];
                                                $consistencyConfig = [
                                                    'very_consistent' => ['text' => 'Muito Consistente', 'class' => 'text-green-600'],
                                                    'consistent' => ['text' => 'Consistente', 'class' => 'text-blue-600'],
                                                    'moderately_consistent' => ['text' => 'Moderadamente Consistente', 'class' => 'text-yellow-600'],
                                                    'inconsistent' => ['text' => 'Inconsistente', 'class' => 'text-red-600'],
                                                    'insufficient_data' => ['text' => 'Dados insuficientes', 'class' => 'text-gray-500']
                                                ];
                                                $consConfig = $consistencyConfig[$consistency] ?? $consistencyConfig['insufficient_data'];
                                            @endphp
                                            <p class="text-lg font-bold {{ $consConfig['class'] }}">{{ $consConfig['text'] }}</p>
                                        </div>

                                        <!-- Performance por Temporada -->
                                        <div class="border rounded-lg p-3 bg-gradient-to-br from-green-50 to-white">
                                            <h5 class="text-sm font-semibold text-green-600 mb-2">Melhor Temporada</h5>
                                            @if($seasonEvolutionStats['seasonal_performance']->count() > 0)
                                                @php $bestSeason = $seasonEvolutionStats['seasonal_performance']->first(); @endphp
                                                <p class="text-lg font-bold text-gray-900">{{ $bestSeason->season_name }}</p>
                                                <p class="text-sm text-gray-600">{{ number_format($bestSeason->win_rate, 1) }}% aproveitamento</p>
                                            @else
                                                <p class="text-gray-500">Sem dados</p>
                                            @endif
                                        </div>

                                        <!-- Performance Mensal Recente -->
                                        <div class="border rounded-lg p-3 bg-gradient-to-br from-blue-50 to-white">
                                            <h5 class="text-sm font-semibold text-blue-600 mb-2">Último Mês</h5>
                                            @if($seasonEvolutionStats['monthly_performance']->count() > 0)
                                                @php $lastMonth = $seasonEvolutionStats['monthly_performance']->first(); @endphp
                                                <p class="text-lg font-bold text-gray-900">{{ $lastMonth->points }} pontos</p>
                                                <p class="text-sm text-gray-600">{{ $lastMonth->wins }}V - {{ $lastMonth->losses }}D</p>
                                            @else
                                                <p class="text-gray-500">Sem dados</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <!-- Visualização Padrão (Dados Combinados ou Evolução) -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                            <!-- Tendência de Performance -->
                            <div class="border rounded-lg p-4 bg-gradient-to-br from-indigo-50 to-white">
                                <h4 class="text-sm font-semibold text-indigo-600 mb-2">Tendência</h4>
                                @php
                                    $trend = $evolutionStats['performance_trend'];
                                    $trendConfig = [
                                        'improving' => ['text' => 'Melhorando', 'class' => 'text-green-600', 'icon' => '↗️'],
                                        'declining' => ['text' => 'Declinando', 'class' => 'text-red-600', 'icon' => '↘️'],
                                        'stable' => ['text' => 'Estável', 'class' => 'text-blue-600', 'icon' => '→'],
                                        'insufficient_data' => ['text' => 'Dados insuficientes', 'class' => 'text-gray-500', 'icon' => '❓']
                                    ];
                                    $config = $trendConfig[$trend] ?? $trendConfig['insufficient_data'];
                                @endphp
                                <p class="text-lg font-bold {{ $config['class'] }}">{{ $config['icon'] }} {{ $config['text'] }}</p>
                            </div>

                            <!-- Consistência -->
                            <div class="border rounded-lg p-4 bg-gradient-to-br from-purple-50 to-white">
                                <h4 class="text-sm font-semibold text-purple-600 mb-2">Consistência</h4>
                                @php
                                    $consistency = $evolutionStats['consistency'];
                                    $consistencyConfig = [
                                        'very_consistent' => ['text' => 'Muito Consistente', 'class' => 'text-green-600'],
                                        'consistent' => ['text' => 'Consistente', 'class' => 'text-blue-600'],
                                        'moderately_consistent' => ['text' => 'Moderadamente Consistente', 'class' => 'text-yellow-600'],
                                        'inconsistent' => ['text' => 'Inconsistente', 'class' => 'text-red-600'],
                                        'insufficient_data' => ['text' => 'Dados insuficientes', 'class' => 'text-gray-500']
                                    ];
                                    $consConfig = $consistencyConfig[$consistency] ?? $consistencyConfig['insufficient_data'];
                                @endphp
                                <p class="text-lg font-bold {{ $consConfig['class'] }}">{{ $consConfig['text'] }}</p>
                            </div>

                            <!-- Performance por Temporada -->
                            <div class="border rounded-lg p-4 bg-gradient-to-br from-green-50 to-white">
                                <h4 class="text-sm font-semibold text-green-600 mb-2">Melhor Temporada</h4>
                                @if($evolutionStats['seasonal_performance']->count() > 0)
                                    @php $bestSeason = $evolutionStats['seasonal_performance']->first(); @endphp
                                    <p class="text-lg font-bold text-gray-900">{{ $bestSeason->season_name }}</p>
                                    <p class="text-sm text-gray-600">{{ number_format($bestSeason->win_rate, 1) }}% aproveitamento</p>
                                @else
                                    <p class="text-gray-500">Sem dados</p>
                                @endif
                            </div>

                            <!-- Performance Mensal Recente -->
                            <div class="border rounded-lg p-4 bg-gradient-to-br from-blue-50 to-white">
                                <h4 class="text-sm font-semibold text-blue-600 mb-2">Último Mês</h4>
                                @if($evolutionStats['monthly_performance']->count() > 0)
                                    @php $lastMonth = $evolutionStats['monthly_performance']->first(); @endphp
                                    <p class="text-lg font-bold text-gray-900">{{ $lastMonth->points }} pontos</p>
                                    <p class="text-sm text-gray-600">{{ $lastMonth->wins }}V - {{ $lastMonth->losses }}D</p>
                                @else
                                    <p class="text-gray-500">Sem dados</p>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Gráfico de Performance Mensal -->
                    @if($analysisType === 'comparison')
                        <!-- Gráficos de Comparação -->
                        @foreach($evolutionStats as $seasonId => $seasonEvolutionStats)
                            @if($seasonEvolutionStats['monthly_performance']->count() > 1)
                                <div class="mt-6">
                                    <h4 class="text-sm font-semibold text-gray-700 mb-3">Performance Mensal - {{ $seasonEvolutionStats['season_name'] }}</h4>
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <div class="flex items-end space-x-2 h-32">
                                            @foreach($seasonEvolutionStats['monthly_performance']->take(6) as $month)
                                                @php
                                                    $maxPoints = $seasonEvolutionStats['monthly_performance']->max('points');
                                                    $height = $maxPoints > 0 ? ($month->points / $maxPoints) * 100 : 0;
                                                @endphp
                                                <div class="flex flex-col items-center flex-1">
                                                    <div class="bg-indigo-500 w-full rounded-t" style="height: {{ $height }}%"></div>
                                                    <div class="text-xs text-gray-600 mt-2 text-center">
                                                        {{ $month->month }}/{{ $month->year }}<br>
                                                        {{ $month->points }}pts
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    @else
                        <!-- Gráfico Padrão -->
                        @if($evolutionStats['monthly_performance']->count() > 1)
                            <div class="mt-6">
                                <h4 class="text-sm font-semibold text-gray-700 mb-3">Performance Mensal</h4>
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <div class="flex items-end space-x-2 h-32">
                                        @foreach($evolutionStats['monthly_performance']->take(6) as $month)
                                            @php
                                                $maxPoints = $evolutionStats['monthly_performance']->max('points');
                                                $height = $maxPoints > 0 ? ($month->points / $maxPoints) * 100 : 0;
                                            @endphp
                                            <div class="flex flex-col items-center flex-1">
                                                <div class="bg-indigo-500 w-full rounded-t" style="height: {{ $height }}%"></div>
                                                <div class="text-xs text-gray-600 mt-2 text-center">
                                                    {{ $month->month }}/{{ $month->year }}<br>
                                                    {{ $month->points }}pts
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
