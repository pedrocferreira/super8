<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                📊 Estatísticas do Sistema de Balanceamento
            </h2>
            <a href="{{ route('dashboard') }}" 
               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                ← Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Resumo Geral -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200 rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="text-4xl text-blue-600 mr-4">🏆</div>
                        <div>
                            <div class="text-2xl font-bold text-blue-800">
                                {{ collect($seasonsWithStats)->sum('stats.total_players') }}
                            </div>
                            <div class="text-blue-600">Total de Jogadores</div>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-green-50 to-green-100 border border-green-200 rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="text-4xl text-green-600 mr-4">⚖️</div>
                        <div>
                            <div class="text-2xl font-bold text-green-800">
                                {{ collect($seasonsWithStats)->where('stats.balance_active', true)->count() }}
                            </div>
                            <div class="text-green-600">Temporadas Ativas</div>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-purple-50 to-purple-100 border border-purple-200 rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="text-4xl text-purple-600 mr-4">📈</div>
                        <div>
                            <div class="text-2xl font-bold text-purple-800">
                                {{ count($seasonsWithStats) }}
                            </div>
                            <div class="text-purple-600">Total de Temporadas</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Estatísticas por Temporada -->
            @foreach($seasonsWithStats as $seasonData)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">{{ $seasonData['season']->name }}</h3>
                            <div class="flex space-x-2">
                                @if($seasonData['stats']['balance_active'])
                                    <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                        ✅ Ativo
                                    </span>
                                @else
                                    <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                        ⚠️ Inativo
                                    </span>
                                @endif
                                <a href="{{ route('rankings.season', $seasonData['season']->id) }}" 
                                   class="bg-blue-500 hover:bg-blue-700 text-white text-xs font-bold py-1 px-3 rounded">
                                    Ver Ranking
                                </a>
                            </div>
                        </div>

                        @if($seasonData['stats']['balance_active'])
                            <!-- Distribuição por Tier -->
                            <div class="mb-4">
                                <h4 class="text-sm font-semibold text-gray-700 mb-2">Distribuição por Tier</h4>
                                <div class="grid grid-cols-2 md:grid-cols-5 gap-2">
                                    <div class="bg-red-50 border border-red-200 rounded-lg p-3 text-center">
                                        <div class="text-lg font-bold text-red-800">{{ $seasonData['stats']['tiers']['top_20_percent'] }}</div>
                                        <div class="text-xs text-red-600">Top 20%</div>
                                        <div class="text-xs text-red-500">0.3x - 0.6x</div>
                                    </div>
                                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 text-center">
                                        <div class="text-lg font-bold text-yellow-800">{{ $seasonData['stats']['tiers']['high_tier'] }}</div>
                                        <div class="text-xs text-yellow-600">High Tier</div>
                                        <div class="text-xs text-yellow-500">0.6x - 0.8x</div>
                                    </div>
                                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 text-center">
                                        <div class="text-lg font-bold text-blue-800">{{ $seasonData['stats']['tiers']['mid_tier'] }}</div>
                                        <div class="text-xs text-blue-600">Mid Tier</div>
                                        <div class="text-xs text-blue-500">0.8x - 1.0x</div>
                                    </div>
                                    <div class="bg-green-50 border border-green-200 rounded-lg p-3 text-center">
                                        <div class="text-lg font-bold text-green-800">{{ $seasonData['stats']['tiers']['low_tier'] }}</div>
                                        <div class="text-xs text-green-600">Low Tier</div>
                                        <div class="text-xs text-green-500">1.0x - 1.5x</div>
                                    </div>
                                    <div class="bg-purple-50 border border-purple-200 rounded-lg p-3 text-center">
                                        <div class="text-lg font-bold text-purple-800">{{ $seasonData['stats']['tiers']['bottom_20_percent'] }}</div>
                                        <div class="text-xs text-purple-600">Bottom 20%</div>
                                        <div class="text-xs text-purple-500">1.5x - 2.0x</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Faixa de Multiplicadores -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <h5 class="font-semibold text-gray-700 mb-2">Faixa de Multiplicadores</h5>
                                    <div class="space-y-2">
                                        <div class="flex justify-between">
                                            <span class="text-sm text-gray-600">Mínimo:</span>
                                            <span class="text-sm font-semibold text-red-600">{{ $seasonData['stats']['multiplier_range']['min'] }}x</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-sm text-gray-600">Máximo:</span>
                                            <span class="text-sm font-semibold text-green-600">{{ $seasonData['stats']['multiplier_range']['max'] }}x</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <h5 class="font-semibold text-gray-700 mb-2">Jogadores</h5>
                                    <div class="text-2xl font-bold text-blue-600">{{ $seasonData['stats']['total_players'] }}</div>
                                    <div class="text-sm text-gray-600">participantes ativos</div>
                                </div>
                            </div>
                        @else
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                <div class="flex items-center">
                                    <div class="text-yellow-600 mr-3">⚠️</div>
                                    <div>
                                        <h4 class="font-semibold text-yellow-800">Sistema Inativo</h4>
                                        <p class="text-yellow-700 text-sm">
                                            Esta temporada tem apenas {{ $seasonData['stats']['total_players'] }} jogadores. 
                                            O sistema de balanceamento requer no mínimo 4 jogadores para ser ativado.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach

            <!-- Explicação do Sistema -->
            <div class="bg-gradient-to-r from-indigo-50 to-blue-50 border border-indigo-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-indigo-800 mb-4">ℹ️ Como Funciona o Sistema de Balanceamento</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="font-semibold text-indigo-800 mb-2">🎯 Objetivos</h4>
                        <ul class="text-indigo-700 text-sm space-y-1">
                            <li>• Motivar jogadores iniciantes</li>
                            <li>• Manter desafio para jogadores experientes</li>
                            <li>• Criar competições mais equilibradas</li>
                            <li>• Evitar desmotivação e abandono</li>
                            <li>• Promover crescimento da comunidade</li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-semibold text-indigo-800 mb-2">⚖️ Mecânica</h4>
                        <ul class="text-indigo-700 text-sm space-y-1">
                            <li>• Multiplicadores baseados na posição no ranking</li>
                            <li>• Jogadores no topo ganham menos pontos</li>
                            <li>• Jogadores no final ganham mais pontos</li>
                            <li>• Ajuste automático conforme o ranking muda</li>
                            <li>• Sistema transparente e justo</li>
                        </ul>
                    </div>
                </div>

                <div class="mt-6 bg-white rounded-lg p-4">
                    <h4 class="font-semibold text-indigo-800 mb-3">📊 Exemplo Prático</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                        <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                            <div class="font-semibold text-red-800">Top Player (1º lugar)</div>
                            <div class="text-red-600">Vitória: 3 pontos × 0.3 = 0.9 pontos</div>
                            <div class="text-red-600">Derrota: 1 ponto × 0.3 = 0.3 pontos</div>
                        </div>
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                            <div class="font-semibold text-blue-800">Mid Tier (50º lugar)</div>
                            <div class="text-blue-600">Vitória: 3 pontos × 1.0 = 3.0 pontos</div>
                            <div class="text-blue-600">Derrota: 1 ponto × 1.0 = 1.0 pontos</div>
                        </div>
                        <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                            <div class="font-semibold text-green-800">Underdog (100º lugar)</div>
                            <div class="text-green-600">Vitória: 3 pontos × 2.0 = 6.0 pontos</div>
                            <div class="text-green-600">Derrota: 1 ponto × 2.0 = 2.0 pontos</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
