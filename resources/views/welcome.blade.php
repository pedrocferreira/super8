@php
    if (!function_exists('getAnimalIcon')) {
        function getAnimalIcon($playerId) {
            $animals = [
                // 🦊 Fox
                '<svg class="w-full h-full" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.94-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/></svg>',
                // 🦁 Lion
                '<svg class="w-full h-full" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z"/></svg>',
                // 🐻 Bear
                '<svg class="w-full h-full" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z"/></svg>',
                // 🐅 Tiger
                '<svg class="w-full h-full" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z"/></svg>',
                // 🐺 Wolf
                '<svg class="w-full h-full" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z"/></svg>',
                // 🐘 Elephant
                '<svg class="w-full h-full" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z"/></svg>',
                // 🐵 Monkey
                '<svg class="w-full h-full" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z"/></svg>',
                // 🐼 Panda
                '<svg class="w-full h-full" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z"/></svg>',
                // 🐨 Koala
                '<svg class="w-full h-full" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z"/></svg>',
                // 🦒 Giraffe
                '<svg class="w-full h-full" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z"/></svg>'
            ];
            return $animals[$playerId % count($animals)];
        }
    }
@endphp

<x-guest-layout>
    <div class="min-h-screen">
        <!-- Hero Section -->
        <div class="relative overflow-hidden bg-gradient-to-br from-blue-600 via-blue-700 to-blue-800">
            <div class="absolute inset-0 bg-black/20"></div>
            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
                <div class="text-center">
                    <div class="flex justify-center mb-8">
                        <div class="relative">
                            <img src="{{ asset('images/logoComSub.png') }}" 
                                 alt="Sky Arena - Esportes de Areia" 
                                 class="h-32 md:h-40 w-auto object-contain drop-shadow-lg"
                                 style="filter: drop-shadow(0 10px 25px rgba(0,0,0,0.1));">
                        </div>
                    </div>

                    <p class="text-xl md:text-2xl text-blue-100 mb-8 max-w-3xl mx-auto">
                        O sistema de ranking mais avançado para esportes de areia
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center" style="margin-bottom: 10%">
                        <a href="{{ route('public.rankings.index') }}" 
                           class="bg-white text-blue-600 px-8 py-4 rounded-full font-bold text-lg hover:bg-blue-50 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105">
                            🏆 Ver Ranking Público
                        </a>
                        <a href="{{ route('public.rankings.statistics') }}" 
                           class="bg-blue-500 text-white px-8 py-4 rounded-full font-bold text-lg hover:bg-blue-400 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105">
                            📊 Estatísticas
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Section -->
        <div class="py-16 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                        Estatísticas Gerais
                    </h2>
                    <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                        Acompanhe o desempenho dos jogadores e o sistema de balanceamento
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <!-- Total Players -->
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl p-8 text-center shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                        <div class="text-4xl mb-4">👥</div>
                        <div class="text-3xl font-bold text-blue-800 mb-2">
                            {{ $systemStats['total_players'] }}
                        </div>
                        <div class="text-blue-600 font-medium">Jogadores Ativos</div>
                    </div>

                    <!-- Active Seasons -->
                    <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-2xl p-8 text-center shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                        <div class="text-4xl mb-4">🏆</div>
                        <div class="text-3xl font-bold text-green-800 mb-2">
                            {{ $systemStats['active_seasons'] }}
                        </div>
                        <div class="text-green-600 font-medium">Temporadas Ativas</div>
                    </div>

                    <!-- Balance Active -->
                    <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-2xl p-8 text-center shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                        <div class="text-4xl mb-4">⚖️</div>
                        <div class="text-3xl font-bold text-purple-800 mb-2">
                            {{ $systemStats['balance_active_seasons'] }}
                        </div>
                        <div class="text-purple-600 font-medium">Com Balanceamento</div>
                    </div>

                    <!-- Total Tournaments -->
                    <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-2xl p-8 text-center shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                        <div class="text-4xl mb-4">🎯</div>
                        <div class="text-3xl font-bold text-orange-800 mb-2">
                            {{ $systemStats['total_tournaments'] }}
                        </div>
                        <div class="text-orange-600 font-medium">Torneios Realizados</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ranking Section -->
        <div class="py-16 bg-gradient-to-br from-gray-50 to-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                        🏆 Ranking Geral
                    </h2>
                    <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                        Os melhores jogadores da temporada atual
                    </p>
                </div>

                <!-- Podium -->
                <div class="flex justify-center items-end gap-8 mb-16">
                    <!-- 2nd Place -->
                    <div class="relative player-card" data-rank="2">
                        <div class="bg-gradient-to-br from-gray-100 via-gray-200 to-gray-300 rounded-2xl p-6 shadow-2xl transform hover:scale-105 transition-all duration-300 border-4 border-gray-400/50">
                            <div class="text-center">
                                <div class="relative mb-4">
                                    <div class="w-20 h-20 mx-auto rounded-full bg-gradient-to-br from-gray-300 to-gray-500 flex items-center justify-center text-white shadow-lg">
                                        {!! getAnimalIcon($playerRanking[1]->id) !!}
                                    </div>
                                    <div class="absolute -top-2 -right-2 w-8 h-8 bg-gray-400 rounded-full flex items-center justify-center">
                                        <span class="text-white font-bold text-sm">🥈</span>
                                    </div>
                                </div>
                                <h3 class="font-bold text-lg text-gray-900 mb-2">{{ $playerRanking[1]->name }}</h3>
                                <div class="text-2xl font-bold text-gray-700 mb-1">{{ $playerRanking[1]->total_points }} pts</div>
                                <div class="text-sm text-gray-600">{{ $playerRanking[1]->total_wins }}W - {{ $playerRanking[1]->total_losses }}L</div>
                            </div>
                        </div>
                    </div>

                    <!-- 1st Place -->
                    <div class="relative player-card" data-rank="1">
                        <div class="bg-gradient-to-br from-yellow-100 via-yellow-200 to-yellow-300 rounded-2xl p-6 shadow-2xl transform hover:scale-105 transition-all duration-300 border-4 border-yellow-500/50 ring-4 ring-yellow-300/30">
                            <div class="text-center">
                                <div class="relative mb-4">
                                    <div class="w-24 h-24 mx-auto rounded-full bg-gradient-to-br from-yellow-400 to-yellow-600 flex items-center justify-center text-white shadow-lg">
                                        {!! getAnimalIcon($playerRanking[0]->id) !!}
                                    </div>
                                    <div class="absolute -top-3 -right-3 w-10 h-10 bg-yellow-500 rounded-full flex items-center justify-center animate-bounce">
                                        <span class="text-white font-bold">🥇</span>
                                    </div>
                                </div>
                                <h3 class="font-bold text-xl text-gray-900 mb-2">{{ $playerRanking[0]->name }}</h3>
                                <div class="text-3xl font-bold text-yellow-800 mb-1">{{ $playerRanking[0]->total_points }} pts</div>
                                <div class="text-sm text-gray-600">{{ $playerRanking[0]->total_wins }}W - {{ $playerRanking[0]->total_losses }}L</div>
                            </div>
                        </div>
                    </div>

                    <!-- 3rd Place -->
                    <div class="relative player-card" data-rank="3">
                        <div class="bg-gradient-to-br from-amber-100 via-amber-200 to-amber-300 rounded-2xl p-6 shadow-2xl transform hover:scale-105 transition-all duration-300 border-4 border-amber-600/50">
                            <div class="text-center">
                                <div class="relative mb-4">
                                    <div class="w-20 h-20 mx-auto rounded-full bg-gradient-to-br from-amber-600 to-amber-800 flex items-center justify-center text-white shadow-lg">
                                        {!! getAnimalIcon($playerRanking[2]->id) !!}
                                    </div>
                                    <div class="absolute -top-2 -right-2 w-8 h-8 bg-amber-600 rounded-full flex items-center justify-center">
                                        <span class="text-white font-bold text-sm">🥉</span>
                                    </div>
                                </div>
                                <h3 class="font-bold text-lg text-gray-900 mb-2">{{ $playerRanking[2]->name }}</h3>
                                <div class="text-2xl font-bold text-amber-800 mb-1">{{ $playerRanking[2]->total_points }} pts</div>
                                <div class="text-sm text-gray-600">{{ $playerRanking[2]->total_wins }}W - {{ $playerRanking[2]->total_losses }}L</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Other Players -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($playerRanking->slice(3, 10) as $position => $player)
                        <div class="bg-white rounded-xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 group border border-gray-200">
                            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                                    <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white shadow-md">
                                        {!! getAnimalIcon($player->id) !!}
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-lg text-gray-900 group-hover:text-blue-600 transition-colors">
                                            {{ $player->name }}
                                        </h4>
                                        <div class="flex items-center gap-3 text-sm text-gray-600">
                                            <span>{{ $player->total_wins }}W</span>
                                            <span>•</span>
                                            <span>{{ $player->total_losses }}L</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-2xl font-bold text-gray-900">{{ $player->total_points }}</div>
                                    <div class="text-sm text-gray-500">pts</div>
                                </div>
                            </div>
                            <div class="mt-4 flex items-center justify-between">
                                <div class="text-sm text-gray-500">
                                    {{ $position + 4 }}º lugar
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ $player->tournaments_played }} torneios
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Balance System Section -->
        <div class="py-16 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                        Sistema de Balanceamento
                    </h2>
                    <p class="text-lg text-gray-600 max-w-3xl mx-auto">
                        Algoritmo proprietário que ajusta pontos baseado na posição no ranking para manter a competitividade
                    </p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                    <!-- Left Side - Info -->
                    <div>
                        <div class="space-y-8">
                            <div class="flex items-start gap-4">
                                <div class="inline-flex p-4 bg-blue-600/20 rounded-full mb-4">
                                    <img src="{{ asset('images/logoSemSub.png') }}" 
                                         alt="Sky Arena" 
                                         class="h-12 w-12 object-contain">
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900 mb-2">Algoritmo Proprietário</h3>
                                    <p class="text-gray-600">
                                        Sistema inteligente que ajusta automaticamente os pontos ganhos baseado na posição atual do jogador no ranking.
                                    </p>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                                    <span class="text-gray-700"><strong>Líderes:</strong> Ganham menos pontos para evitar dominação</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                                    <span class="text-gray-700"><strong>Underdogs:</strong> Ganham mais pontos para incentivar participação</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                                    <span class="text-gray-700"><strong>Meio da tabela:</strong> Pontuação balanceada</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Side - Stats -->
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl p-8">
                        <h3 class="text-2xl font-bold text-gray-900 mb-6 text-center">Estatísticas do Sistema</h3>
                        
                        <div class="space-y-6">
                            @foreach($balanceData as $season)
                                <div class="bg-white rounded-xl p-6 shadow-lg">
                                    <div class="flex items-center justify-between mb-4">
                                        <h4 class="font-bold text-lg text-gray-900">{{ $season['season']->name }}</h4>
                                        <span class="px-3 py-1 rounded-full text-sm font-medium {{ $season['stats']['balance_active'] ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                            {{ $season['stats']['balance_active'] ? 'Ativo' : 'Inativo' }}
                                        </span>
                                    </div>
                                    
                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="text-center">
                                            <div class="text-2xl font-bold text-blue-600">{{ $season['stats']['total_players'] }}</div>
                                            <div class="text-sm text-gray-600">Jogadores</div>
                                            </div>
                                        <div class="text-center">
                                            <div class="text-2xl font-bold text-green-600">{{ $season['stats']['total_tournaments'] }}</div>
                                            <div class="text-sm text-gray-600">Torneios</div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                </div>
            </div>

        <!-- Footer -->
        <footer class="bg-gray-900 text-white py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <div class="inline-flex items-center justify-center mb-3">
                        <img src="{{ asset('images/logoSemSub.png') }}" 
                             alt="Sky Arena" 
                             class="h-12 w-auto object-contain">
                    </div>
                    <h3 class="text-2xl font-bold mb-4">Sky Arena</h3>
                    <p class="text-gray-400 mb-6 max-w-2xl mx-auto">
                        Sistema de ranking e balanceamento para esportes de areia. 
                        Competitividade justa e engajamento máximo.
                    </p>
                    <div class="flex justify-center gap-6">
                        <a href="{{ route('public.rankings.index') }}" class="text-gray-400 hover:text-white transition-colors">
                            Ranking Público
                        </a>
                        <a href="{{ route('public.rankings.statistics') }}" class="text-gray-400 hover:text-white transition-colors">
                            Estatísticas
                        </a>
                    </div>
                </div>
            </div>
        </footer>
    </div>

</x-guest-layout>