<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Balanceamento {{ $player->name }} - {{ $season->name }} - Super8</title>
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
                    <a href="{{ route('public.rankings.season', $season->id) }}" 
                       class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">
                        ← Voltar ao Ranking
                    </a>
                    <a href="{{ route('public.rankings.index') }}" 
                       class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium">
                        🏠 Início
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
            
            <!-- Informações do Jogador -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">{{ $player->name }}</h1>
                            <p class="text-gray-600">{{ $season->name }}</p>
                        </div>
                        @if($playerRanking)
                            <div class="text-right">
                                <div class="text-3xl font-bold text-blue-600">
                                    #{{ $balanceInfo['position'] }}
                                </div>
                                <div class="text-sm text-gray-600">de {{ $balanceInfo['total_players'] }} jogadores</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            @if($balanceInfo['position'])
                <!-- Status do Balanceamento -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <!-- Multiplicador Atual -->
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200 rounded-lg p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-sm font-medium text-blue-800">Multiplicador Atual</h4>
                                <div class="text-3xl font-bold text-blue-600 mt-2">
                                    {{ $balanceInfo['multiplier'] }}x
                                </div>
                            </div>
                            <div class="text-4xl">
                                @if($balanceInfo['multiplier'] > 1.0)
                                    📈
                                @elseif($balanceInfo['multiplier'] < 1.0)
                                    📉
                                @else
                                    ➡️
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Boost/Penalty -->
                    <div class="bg-gradient-to-br from-green-50 to-green-100 border border-green-200 rounded-lg p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-sm font-medium text-green-800">Ajuste de Pontos</h4>
                                <div class="text-3xl font-bold text-green-600 mt-2">
                                    @if($balanceInfo['points_boost'] > 0)
                                        +{{ $balanceInfo['points_boost'] }}%
                                    @elseif($balanceInfo['points_penalty'] > 0)
                                        -{{ $balanceInfo['points_penalty'] }}%
                                    @else
                                        0%
                                    @endif
                                </div>
                            </div>
                            <div class="text-4xl">
                                @if($balanceInfo['points_boost'] > 0)
                                    🚀
                                @elseif($balanceInfo['points_penalty'] > 0)
                                    ⚠️
                                @else
                                    ⚖️
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Status do Tier -->
                    <div class="bg-gradient-to-br from-purple-50 to-purple-100 border border-purple-200 rounded-lg p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-sm font-medium text-purple-800">Tier Atual</h4>
                                <div class="text-2xl font-bold text-purple-600 mt-2">
                                    @switch($balanceInfo['balance_status'])
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
                            <div class="text-4xl">
                                @switch($balanceInfo['balance_status'])
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
                </div>

                <!-- Estatísticas do Jogador -->
                @if($playerRanking)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">📊 Estatísticas da Temporada</h3>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                                <div class="text-center">
                                    <div class="text-3xl font-bold text-blue-600">{{ $playerRanking['total_points'] }}</div>
                                    <div class="text-sm text-gray-600">Pontos Totais</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-3xl font-bold text-green-600">{{ $playerRanking['total_wins'] }}</div>
                                    <div class="text-sm text-gray-600">Vitórias</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-3xl font-bold text-red-600">{{ $playerRanking['total_losses'] }}</div>
                                    <div class="text-sm text-gray-600">Derrotas</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-3xl font-bold 
                                        @if($playerRanking['win_rate'] >= 70) text-green-600
                                        @elseif($playerRanking['win_rate'] >= 50) text-yellow-600
                                        @else text-red-600
                                        @endif">
                                        {{ $playerRanking['win_rate'] }}%
                                    </div>
                                    <div class="text-sm text-gray-600">Aproveitamento</div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Explicação do Balanceamento -->
                <div class="bg-gradient-to-r from-indigo-50 to-blue-50 border border-indigo-200 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-indigo-800 mb-4">ℹ️ Como o Balanceamento Afeta Você</h3>
                    
                    @if($balanceInfo['balance_status'] === 'top_player')
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                            <div class="flex items-center">
                                <div class="text-red-600 mr-3 text-2xl">👑</div>
                                <div>
                                    <h4 class="font-semibold text-red-800">Você é um Top Player!</h4>
                                    <p class="text-red-700 text-sm">
                                        Por estar no topo do ranking, você ganha menos pontos por vitória 
                                        ({{ $balanceInfo['multiplier'] }}x). Isso mantém o desafio e motiva outros jogadores.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @elseif($balanceInfo['balance_status'] === 'underdog')
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                            <div class="flex items-center">
                                <div class="text-green-600 mr-3 text-2xl">🎯</div>
                                <div>
                                    <h4 class="font-semibold text-green-800">Você é um Underdog!</h4>
                                    <p class="text-green-700 text-sm">
                                        Por estar no final do ranking, você ganha mais pontos por vitória 
                                        ({{ $balanceInfo['multiplier'] }}x). Isso te ajuda a subir no ranking mais rapidamente!
                                    </p>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                            <div class="flex items-center">
                                <div class="text-blue-600 mr-3 text-2xl">⚖️</div>
                                <div>
                                    <h4 class="font-semibold text-blue-800">Tier Intermediário</h4>
                                    <p class="text-blue-700 text-sm">
                                        Você está em uma posição equilibrada no ranking. Seu multiplicador 
                                        ({{ $balanceInfo['multiplier'] }}x) reflete sua posição atual.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <h4 class="font-semibold text-indigo-800 mb-2">🎯 Objetivo do Sistema</h4>
                            <ul class="text-indigo-700 text-sm space-y-1">
                                <li>• Motivar jogadores com menos pontos</li>
                                <li>• Manter desafio para jogadores experientes</li>
                                <li>• Criar competições mais equilibradas</li>
                                <li>• Evitar desmotivação de iniciantes</li>
                            </ul>
                        </div>
                        <div>
                            <h4 class="font-semibold text-indigo-800 mb-2">📈 Como Subir no Ranking</h4>
                            <ul class="text-indigo-700 text-sm space-y-1">
                                <li>• Participe de mais torneios</li>
                                <li>• Melhore sua taxa de vitórias</li>
                                <li>• Jogue contra oponentes mais fortes</li>
                                <li>• Mantenha consistência</li>
                            </ul>
                        </div>
                    </div>
                </div>
            @else
                <!-- Jogador não classificado -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="text-yellow-600 mr-3 text-2xl">⚠️</div>
                        <div>
                            <h3 class="font-semibold text-yellow-800">Jogador Não Classificado</h3>
                            <p class="text-yellow-700">
                                Este jogador ainda não possui estatísticas suficientes para aparecer no ranking desta temporada.
                            </p>
                        </div>
                    </div>
                </div>
            @endif
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



