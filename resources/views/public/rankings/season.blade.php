<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ranking {{ $season->name }} - Super8</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .animate-fade-in-up {
            animation: fadeInUp 0.8s ease-out forwards;
            opacity: 0;
        }
        
        .hover-scale:hover {
            transform: scale(1.02);
            transition: transform 0.3s ease;
        }
        
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .rank-1 { background: linear-gradient(135deg, #ffd700 0%, #ffed4e 100%); }
        .rank-2 { background: linear-gradient(135deg, #c0c0c0 0%, #e5e5e5 100%); }
        .rank-3 { background: linear-gradient(135deg, #cd7f32 0%, #daa520 100%); }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <a href="{{ route('public.rankings.index') }}" class="flex items-center">
                            <img src="{{ asset('images/logoSemSub.png') }}" 
                                 alt="Sky Arena" 
                                 class="h-8 w-auto">
                            <span class="ml-2 text-xl font-bold text-gray-900">Sky Arena</span>
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
            
            <!-- Hero Section -->
            <div class="gradient-bg text-white py-12 rounded-2xl mb-8 animate-fade-in-up">
                <div class="text-center">
                    <h1 class="text-4xl font-bold mb-4">
                        🏆 Ranking - {{ $season->name }}
                    </h1>
                    <p class="text-xl text-blue-100 mb-6">
                        @if($season->start_date && $season->end_date)
                            {{ $season->start_date->format('d/m/Y') }} - {{ $season->end_date->format('d/m/Y') }}
                        @elseif($season->start_date)
                            Desde {{ $season->start_date->format('d/m/Y') }}
                        @endif
                    </p>
                    <div class="inline-flex items-center px-4 py-2 bg-white/20 backdrop-blur-sm rounded-full">
                        <span class="text-sm font-medium">{{ $ranking->count() }} Jogadores</span>
                    </div>
                </div>
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
                                        <tr class="hover:bg-gray-50 hover-scale transition-all duration-300 {{ $index < 3 ? 'ring-2 ring-opacity-20' : '' }} {{ $index === 0 ? 'ring-yellow-400 bg-yellow-50/30' : ($index === 1 ? 'ring-gray-400 bg-gray-50/30' : ($index === 2 ? 'ring-amber-400 bg-amber-50/30' : '')) }}">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    @if($index < 3)
                                                        <div class="flex items-center justify-center w-12 h-12 rounded-full {{ $index === 0 ? 'rank-1' : ($index === 1 ? 'rank-2' : 'rank-3') }} shadow-lg">
                                                            @if($index === 0)
                                                                <span class="text-2xl">🥇</span>
                                                            @elseif($index === 1)
                                                                <span class="text-2xl">🥈</span>
                                                            @else
                                                                <span class="text-2xl">🥉</span>
                                                            @endif
                                                        </div>
                                                    @else
                                                        <div class="flex items-center justify-center w-10 h-10 rounded-full bg-gray-100 border-2 border-gray-300">
                                                            <span class="text-lg font-bold text-gray-700">
                                                                {{ $index + 1 }}
                                                            </span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-10 w-10">
                                                        <div class="h-10 w-10 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold text-lg">
                                                            {{ strtoupper(substr($player['player_name'], 0, 2)) }}
                                                        </div>
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium text-gray-900">
                                                            {{ $player['player_name'] }}
                                                        </div>
                                                        @if($index < 3)
                                                            <div class="text-xs text-gray-500">
                                                                @if($index === 0) Líder da Temporada @endif
                                                                @if($index === 1) Vice-Líder @endif
                                                                @if($index === 2) 3º Colocado @endif
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="text-lg font-bold {{ $index < 3 ? 'text-gray-900' : 'text-gray-700' }}">
                                                        {{ number_format($player['total_points']) }}
                                                    </div>
                                                    <div class="text-xs text-gray-500 ml-1">pts</div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="w-2 h-2 bg-green-500 rounded-full mr-2"></div>
                                                    <div class="text-sm text-green-600 font-semibold">
                                                        {{ $player['total_wins'] }}
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="w-2 h-2 bg-red-500 rounded-full mr-2"></div>
                                                    <div class="text-sm text-red-600 font-semibold">
                                                        {{ $player['total_losses'] }}
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="flex-1 bg-gray-200 rounded-full h-2 mr-2">
                                                        <div class="h-2 rounded-full {{ $player['win_rate'] >= 70 ? 'bg-green-500' : ($player['win_rate'] >= 50 ? 'bg-yellow-500' : 'bg-red-500') }}" 
                                                             style="width: {{ $player['win_rate'] }}%"></div>
                                                    </div>
                                                    <div class="text-sm font-semibold 
                                                        @if($player['win_rate'] >= 70) text-green-600
                                                        @elseif($player['win_rate'] >= 50) text-yellow-600
                                                        @else text-red-600
                                                        @endif">
                                                        {{ $player['win_rate'] }}%
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="text-sm text-gray-600 font-medium">
                                                        {{ $player['tournaments_played'] }}
                                                    </div>
                                                    <div class="text-xs text-gray-400 ml-1">torneios</div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <a href="{{ route('public.rankings.player.balance', [$player['player_id'], $season->id]) }}" 
                                                   class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 hover:bg-blue-200 transition-colors duration-200">
                                                    ⚖️ Detalhes
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Animate elements on scroll
            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            });

            document.querySelectorAll('.animate-fade-in-up').forEach(el => {
                observer.observe(el);
            });

            // Add hover effects to table rows
            document.querySelectorAll('tbody tr').forEach(row => {
                row.addEventListener('mouseenter', function() {
                    this.style.transform = 'scale(1.01)';
                });
                
                row.addEventListener('mouseleave', function() {
                    this.style.transform = 'scale(1)';
                });
            });
        });
    </script>
</body>
</html>



