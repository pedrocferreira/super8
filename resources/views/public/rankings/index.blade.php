<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rankings Públicos - Super8</title>
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
            <div class="text-center mb-12">
                <h1 class="text-4xl font-bold text-gray-900 mb-4">
                    🏆 Rankings Públicos
                </h1>
                <p class="text-xl text-gray-600 mb-8">
                    Acompanhe o desempenho dos jogadores e o sistema de balanceamento
                </p>
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-6 max-w-4xl mx-auto">
                    <h2 class="text-lg font-semibold text-blue-800 mb-4">⚖️ Sistema de Balanceamento</h2>
                    <p class="text-blue-700 mb-4">
                        Nosso sistema ajusta automaticamente os pontos ganhos baseado na posição do jogador no ranking, 
                        motivando iniciantes e desafiando os líderes para criar competições mais equilibradas.
                    </p>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                        <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                            <div class="font-semibold text-red-800">Top Players</div>
                            <div class="text-red-600">Ganham menos pontos (0.3x - 0.6x)</div>
                        </div>
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                            <div class="font-semibold text-blue-800">Mid Tier</div>
                            <div class="text-blue-600">Pontuação normal (0.6x - 1.5x)</div>
                        </div>
                        <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                            <div class="font-semibold text-green-800">Underdogs</div>
                            <div class="text-green-600">Ganham mais pontos (1.5x - 2.0x)</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lista de Temporadas -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">📅 Escolha uma Temporada</h2>
                    
                    @if($seasons->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($seasons as $season)
                                <div class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow">
                                    <div class="flex items-center justify-between mb-4">
                                        <h3 class="text-lg font-semibold text-gray-900">{{ $season->name }}</h3>
                                        <span class="px-2 py-1 text-xs rounded-full {{ $season->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                            {{ $season->status === 'active' ? 'Ativa' : 'Encerrada' }}
                                        </span>
                                    </div>
                                    
                                    <div class="space-y-2 text-sm text-gray-600 mb-4">
                                        @if($season->start_date)
                                            <div><strong>Início:</strong> {{ $season->start_date->format('d/m/Y') }}</div>
                                        @endif
                                        @if($season->end_date)
                                            <div><strong>Fim:</strong> {{ $season->end_date->format('d/m/Y') }}</div>
                                        @endif
                                    </div>
                                    
                                    <div class="flex space-x-2">
                                        <a href="{{ route('public.rankings.season', $season->id) }}" 
                                           class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-center py-2 px-4 rounded-md text-sm font-medium">
                                            🏆 Ver Ranking
                                        </a>
                                        <a href="{{ route('public.rankings.statistics') }}" 
                                           class="bg-gray-500 hover:bg-gray-600 text-white py-2 px-4 rounded-md text-sm font-medium">
                                            📊 Stats
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="text-gray-500 text-lg">Nenhuma temporada encontrada</div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Informações Adicionais -->
            <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">🎯 Como Funciona</h3>
                        <ul class="space-y-2 text-sm text-gray-600">
                            <li>• <strong>Ranking Automático:</strong> Baseado em pontos totais por temporada</li>
                            <li>• <strong>Balanceamento Dinâmico:</strong> Multiplicadores ajustados por posição</li>
                            <li>• <strong>Transparência Total:</strong> Todos os dados são públicos</li>
                            <li>• <strong>Atualização em Tempo Real:</strong> Rankings atualizados após cada torneio</li>
                        </ul>
                    </div>
                </div>
                
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">📈 Benefícios</h3>
                        <ul class="space-y-2 text-sm text-gray-600">
                            <li>• <strong>Motivação:</strong> Iniciantes ganham mais pontos</li>
                            <li>• <strong>Desafio:</strong> Líderes precisam se esforçar mais</li>
                            <li>• <strong>Equilíbrio:</strong> Competições mais justas</li>
                            <li>• <strong>Crescimento:</strong> Comunidade mais engajada</li>
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



