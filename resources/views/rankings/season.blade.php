<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                🏆 Ranking - {{ $season->name }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('rankings.statistics') }}" 
                   class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    📊 Estatísticas
                </a>
                <form action="{{ route('rankings.apply.balance', $season->id) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" 
                            class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded"
                            onclick="return confirm('Aplicar sistema de balanceamento a esta temporada?')">
                        ⚖️ Aplicar Balanceamento
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Informações do Sistema de Balanceamento -->
            @if($balanceStats['balance_active'])
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-semibold text-blue-800 mb-4">⚖️ Sistema de Balanceamento Ativo</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-blue-600">{{ $balanceStats['total_players'] }}</div>
                            <div class="text-sm text-blue-700">Jogadores</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600">{{ $balanceStats['multiplier_range']['max'] }}x</div>
                            <div class="text-sm text-green-700">Multiplicador Máximo</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-orange-600">{{ $balanceStats['multiplier_range']['min'] }}x</div>
                            <div class="text-sm text-orange-700">Multiplicador Mínimo</div>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <h4 class="font-semibold text-blue-800 mb-2">Distribuição por Tier:</h4>
                        <div class="grid grid-cols-2 md:grid-cols-5 gap-2 text-sm">
                            <div class="bg-red-100 p-2 rounded text-center">
                                <div class="font-bold text-red-800">{{ $balanceStats['tiers']['top_20_percent'] }}</div>
                                <div class="text-red-600">Top 20%</div>
                            </div>
                            <div class="bg-yellow-100 p-2 rounded text-center">
                                <div class="font-bold text-yellow-800">{{ $balanceStats['tiers']['high_tier'] }}</div>
                                <div class="text-yellow-600">High Tier</div>
                            </div>
                            <div class="bg-blue-100 p-2 rounded text-center">
                                <div class="font-bold text-blue-800">{{ $balanceStats['tiers']['mid_tier'] }}</div>
                                <div class="text-blue-600">Mid Tier</div>
                            </div>
                            <div class="bg-green-100 p-2 rounded text-center">
                                <div class="font-bold text-green-800">{{ $balanceStats['tiers']['low_tier'] }}</div>
                                <div class="text-green-600">Low Tier</div>
                            </div>
                            <div class="bg-purple-100 p-2 rounded text-center">
                                <div class="font-bold text-purple-800">{{ $balanceStats['tiers']['bottom_20_percent'] }}</div>
                                <div class="text-purple-600">Bottom 20%</div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                    <div class="flex items-center">
                        <div class="text-yellow-600 mr-2">⚠️</div>
                        <div class="text-yellow-800">
                            Sistema de balanceamento inativo. Mínimo de {{ $balanceStats['total_players'] }} jogadores necessários.
                        </div>
                    </div>
                </div>
            @endif

            <!-- Filtros -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" class="flex flex-wrap gap-4">
                        <div class="flex-1 min-w-64">
                            <input type="text" 
                                   name="search" 
                                   value="{{ request('search') }}"
                                   placeholder="Buscar jogador..."
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <button type="submit" 
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            🔍 Buscar
                        </button>
                        @if(request('search'))
                            <a href="{{ route('rankings.season', $season->id) }}" 
                               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                🗑️ Limpar
                            </a>
                        @endif
                    </form>
                </div>
            </div>

            <!-- Tabela de Ranking -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">📊 Classificação Geral</h3>
                    
                    @if($ranking->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Posição
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Jogador
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Pontos
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Vitórias
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Derrotas
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Aproveitamento
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Torneios
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Ações
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($ranking as $index => $player)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    @if($index < 3)
                                                        @if($index === 0)
                                                            <span class="text-2xl">🥇</span>
                                                        @elseif($index === 1)
                                                            <span class="text-2xl">🥈</span>
                                                        @else
                                                            <span class="text-2xl">🥉</span>
                                                        @endif
                                                    @else
                                                        <span class="text-lg font-semibold text-gray-600">
                                                            #{{ $index + 1 }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $player['player_name'] }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-bold text-gray-900">
                                                    {{ number_format($player['total_points']) }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-green-600 font-semibold">
                                                    {{ $player['total_wins'] }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-red-600 font-semibold">
                                                    {{ $player['total_losses'] }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-semibold 
                                                    @if($player['win_rate'] >= 70) text-green-600
                                                    @elseif($player['win_rate'] >= 50) text-yellow-600
                                                    @else text-red-600
                                                    @endif">
                                                    {{ $player['win_rate'] }}%
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-600">
                                                    {{ $player['tournaments_played'] }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <a href="{{ route('rankings.player.balance', [$player['player_id'], $season->id]) }}" 
                                                   class="text-blue-600 hover:text-blue-900">
                                                    ⚖️ Balanceamento
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="text-gray-500 text-lg">Nenhum jogador encontrado</div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Explicação do Sistema -->
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 mt-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">ℹ️ Como Funciona o Sistema de Balanceamento</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="font-semibold text-gray-800 mb-2">🎯 Objetivo</h4>
                        <p class="text-gray-600 text-sm">
                            O sistema ajusta os pontos ganhos baseado na posição do jogador no ranking, 
                            motivando jogadores com menos pontos e desafiando os líderes.
                        </p>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-800 mb-2">⚖️ Multiplicadores</h4>
                        <ul class="text-gray-600 text-sm space-y-1">
                            <li><strong>Top 20%:</strong> 0.3x - 0.6x (ganha menos pontos)</li>
                            <li><strong>Mid Tier:</strong> 0.6x - 1.0x (pontos normais)</li>
                            <li><strong>Bottom 20%:</strong> 1.0x - 2.0x (ganha mais pontos)</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>



