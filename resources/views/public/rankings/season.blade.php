<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ranking {{ $season->name }} - Super8</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <a href="{{ route('public.rankings.index') }}" class="flex items-center">
                            <svg class="w-8 h-8 text-yellow-400" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12,2C6.48,2 2,6.48 2,12C2,17.52 6.48,22 12,22C17.52,22 22,17.52 22,12C22,6.48 17.52,2 12,2M12,4C16.42,4 20,7.58 20,12C20,16.42 16.42,20 12,20C7.58,20 4,16.42 4,12C4,7.58 7.58,4 12,4M12,6C8.69,6 6,8.69 6,12C6,15.31 8.69,18 12,18C15.31,18 18,15.31 18,12C18,8.69 15.31,6 12,6M12,8C14.21,8 16,9.79 16,12C16,14.21 14.21,16 12,16C9.79,16 8,14.21 8,12C8,9.79 9.79,8 12,8Z"/>
                            </svg>
                            <span class="ml-2 text-xl font-bold text-gray-900">Super8</span>
                        </a>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('public.rankings.index') }}" 
                       class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">
                        ← Voltar
                    </a>
                    <a href="{{ route('public.rankings.statistics') }}" 
                       class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">
                        📊 Estatísticas
                    </a>
                    <a href="{{ route('login') }}" 
                       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                        Entrar
                    </a>
                </div>
            </div>
        </div>
    </header>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Título e Informações -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">
                    🏆 Ranking - {{ $season->name }}
                </h1>
                <p class="text-gray-600">
                    @if($season->start_date && $season->end_date)
                        {{ $season->start_date->format('d/m/Y') }} - {{ $season->end_date->format('d/m/Y') }}
                    @elseif($season->start_date)
                        Desde {{ $season->start_date->format('d/m/Y') }}
                    @endif
                </p>
            </div>

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
                            <a href="{{ route('public.rankings.season', $season->id) }}" 
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
                                            Balanceamento
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
                                                <a href="{{ route('public.rankings.player.balance', [$player['player_id'], $season->id]) }}" 
                                                   class="text-blue-600 hover:text-blue-900">
                                                    ⚖️ Ver Detalhes
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
                            <li><strong>Mid Tier:</strong> 0.6x - 1.5x (pontos normais)</li>
                            <li><strong>Bottom 20%:</strong> 1.5x - 2.0x (ganha mais pontos)</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <p class="text-gray-400">© 2024 Super8 - Sistema de Rankings Públicos</p>
                <p class="text-sm text-gray-500 mt-2">Dados atualizados em tempo real</p>
            </div>
        </div>
    </footer>
</body>
</html>



