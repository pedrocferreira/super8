@php
    if (!function_exists('getAnimalIcon')) {
        function getAnimalIcon($playerId) {
            $animals = [
                '🦊', '🦁', '🐻', '🐅', '🐺', '🐘', '🐵', '🐼', '🐨', '🦒'
            ];
            return $animals[$playerId % count($animals)];
        }
    }
@endphp

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sky Arena - Ranking</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
        
        .animate-fade-in-up:nth-child(1) { animation-delay: 0.1s; }
        .animate-fade-in-up:nth-child(2) { animation-delay: 0.2s; }
        .animate-fade-in-up:nth-child(3) { animation-delay: 0.3s; }
        .animate-fade-in-up:nth-child(4) { animation-delay: 0.4s; }
        .animate-fade-in-up:nth-child(5) { animation-delay: 0.5s; }
        .animate-fade-in-up:nth-child(6) { animation-delay: 0.6s; }
        .animate-fade-in-up:nth-child(7) { animation-delay: 0.7s; }
        .animate-fade-in-up:nth-child(8) { animation-delay: 0.8s; }
        
        .hover-scale:hover {
            transform: scale(1.05);
            transition: transform 0.3s ease;
        }
        
        .hover-lift:hover {
            transform: translateY(-5px);
            transition: transform 0.3s ease;
        }
        
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .glass-effect {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Hero Section -->
    <div class="gradient-bg text-white py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <div class="animate-fade-in-up mb-8">
                    <img src="{{ asset('images/logoComSub.png') }}" 
                         alt="Sky Arena" 
                         class="h-24 mx-auto mb-6 drop-shadow-lg">
                </div>
                
                <h1 class="text-5xl font-bold mb-6 animate-fade-in-up">
                    Sky Arena
                </h1>
                
                <p class="text-xl mb-4 max-w-3xl mx-auto animate-fade-in-up">
                    O sistema de ranking mais avançado para esportes de areia
                </p>
                
                @if(isset($activeSeason))
                    <div class="inline-flex items-center px-4 py-2 bg-white/20 backdrop-blur-sm rounded-full mb-8 animate-fade-in-up">
                        <span class="text-sm font-medium">🏆 Temporada Ativa: {{ $activeSeason->name }}</span>
                    </div>
                @endif
                
                <div class="flex flex-col sm:flex-row gap-4 justify-center animate-fade-in-up">
                    <a href="{{ route('public.rankings.index') }}" 
                       class="bg-white text-blue-600 px-8 py-4 rounded-full font-bold text-lg hover-scale shadow-lg">
                        🏆 Ver Ranking Público
                    </a>
                    <a href="{{ route('public.rankings.statistics') }}" 
                       class="bg-blue-500 text-white px-8 py-4 rounded-full font-bold text-lg hover-scale shadow-lg">
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
                <h2 class="text-3xl font-bold text-gray-900 mb-4">
                    Estatísticas Gerais
                </h2>
                <p class="text-lg text-gray-600">
                    Acompanhe o desempenho dos jogadores e o sistema de balanceamento
                </p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-6 text-center hover-lift shadow-lg">
                    <div class="text-4xl mb-4">👥</div>
                    <div class="text-3xl font-bold text-blue-800 mb-2">{{ $systemStats['total_players'] }}</div>
                    <div class="text-blue-600 font-medium">Jogadores Ativos</div>
                </div>

                <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-6 text-center hover-lift shadow-lg">
                    <div class="text-4xl mb-4">🏆</div>
                    <div class="text-3xl font-bold text-green-800 mb-2">{{ $systemStats['active_seasons'] }}</div>
                    <div class="text-green-600 font-medium">Temporadas Ativas</div>
                </div>

                <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-6 text-center hover-lift shadow-lg">
                    <div class="text-4xl mb-4">⚖️</div>
                    <div class="text-3xl font-bold text-purple-800 mb-2">{{ $systemStats['balance_active_seasons'] ?? 0 }}</div>
                    <div class="text-purple-600 font-medium">Com Balanceamento</div>
                </div>

                <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl p-6 text-center hover-lift shadow-lg">
                    <div class="text-4xl mb-4">🎯</div>
                    <div class="text-3xl font-bold text-orange-800 mb-2">{{ $systemStats['total_tournaments'] }}</div>
                    <div class="text-orange-600 font-medium">Torneios Realizados</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Ranking Section -->
    <div class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">
                    🏆 Ranking {{ isset($activeSeason) ? 'da ' . $activeSeason->name : 'Geral' }}
                </h2>
                <p class="text-lg text-gray-600">
                    @if(isset($activeSeason))
                        Os melhores jogadores da temporada {{ $activeSeason->name }}
                    @else
                        Ranking geral de todos os jogadores
                    @endif
                </p>
            </div>

            @if($playerRanking->count() >= 3)
                <!-- Podium -->
                <div class="flex flex-col md:flex-row justify-center items-end gap-8 mb-16">
                    <!-- 2nd Place -->
                    <div class="bg-gradient-to-br from-gray-100 to-gray-300 rounded-2xl p-6 shadow-xl hover-lift">
                        <div class="text-center">
                            <div class="text-6xl mb-4">{!! getAnimalIcon($playerRanking[1]->id ?? 1) !!}</div>
                            <div class="text-2xl font-bold text-gray-700 mb-2">🥈 2º Lugar</div>
                            <h3 class="font-bold text-lg text-gray-900 mb-2">{{ $playerRanking[1]->name ?? 'Jogador' }}</h3>
                            <div class="text-2xl font-bold text-gray-700 mb-1">{{ $playerRanking[1]->total_points ?? 0 }} pts</div>
                            <div class="text-sm text-gray-600">{{ $playerRanking[1]->total_wins ?? 0 }}W - {{ $playerRanking[1]->total_losses ?? 0 }}L</div>
                        </div>
                    </div>

                    <!-- 1st Place -->
                    <div class="bg-gradient-to-br from-yellow-100 to-yellow-300 rounded-2xl p-8 shadow-2xl hover-lift border-4 border-yellow-400">
                        <div class="text-center">
                            <div class="text-8xl mb-4">{!! getAnimalIcon($playerRanking[0]->id ?? 0) !!}</div>
                            <div class="text-2xl font-bold text-yellow-700 mb-2">🥇 1º Lugar</div>
                            <h3 class="font-bold text-xl text-gray-900 mb-2">{{ $playerRanking[0]->name ?? 'Líder' }}</h3>
                            <div class="text-3xl font-bold text-yellow-800 mb-1">{{ $playerRanking[0]->total_points ?? 0 }} pts</div>
                            <div class="text-sm text-gray-600">{{ $playerRanking[0]->total_wins ?? 0 }}W - {{ $playerRanking[0]->total_losses ?? 0 }}L</div>
                        </div>
                    </div>

                    <!-- 3rd Place -->
                    <div class="bg-gradient-to-br from-amber-100 to-amber-300 rounded-2xl p-6 shadow-xl hover-lift">
                        <div class="text-center">
                            <div class="text-6xl mb-4">{!! getAnimalIcon($playerRanking[2]->id ?? 2) !!}</div>
                            <div class="text-2xl font-bold text-amber-700 mb-2">🥉 3º Lugar</div>
                            <h3 class="font-bold text-lg text-gray-900 mb-2">{{ $playerRanking[2]->name ?? 'Terceiro' }}</h3>
                            <div class="text-2xl font-bold text-amber-800 mb-1">{{ $playerRanking[2]->total_points ?? 0 }} pts</div>
                            <div class="text-sm text-gray-600">{{ $playerRanking[2]->total_wins ?? 0 }}W - {{ $playerRanking[2]->total_losses ?? 0 }}L</div>
                        </div>
                    </div>
                </div>

                <!-- Other Players -->
                @if($playerRanking->count() > 3)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($playerRanking->slice(3, 10) as $position => $player)
                        <div class="bg-white rounded-xl p-6 shadow-lg hover-lift border border-gray-200">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-4">
                                    <div class="text-4xl">{!! getAnimalIcon($player->id) !!}</div>
                                    <div>
                                        <h4 class="font-bold text-lg text-gray-900">{{ $player->name }}</h4>
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
                                <div class="text-sm text-gray-500">{{ $position + 4 }}º lugar</div>
                                <div class="text-sm text-gray-500">{{ $player->tournaments_played }} torneios</div>
                            </div>
                            <div class="mt-4">
                                <a href="{{ route('public.player.stats', $player->id) }}" 
                                   class="block w-full bg-blue-600 text-white text-center py-2 px-4 rounded-lg font-medium hover:bg-blue-700 transition-colors duration-300">
                                    📊 Ver Estatísticas
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
                @endif
            @else
                <div class="text-center py-12">
                    <div class="text-6xl mb-4">🏆</div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Sistema Vazio</h3>
                    <p class="text-gray-600 mb-6">Não há jogadores cadastrados ainda.</p>
                    <a href="/login" class="bg-blue-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-blue-700 transition-colors duration-300">
                        🔐 Fazer Login
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <div class="inline-flex items-center justify-center mb-3">
                    <img src="{{ asset('images/logoSemSub.png') }}" 
                         alt="Sky Arena" 
                         class="h-12 w-auto">
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

    <script>
        // Add some interactive effects
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
        });
    </script>
</body>
</html>