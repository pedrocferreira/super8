<x-guest-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Cabeçalho com Informações do Jogador -->
            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-lg shadow-lg mb-6">
                <div class="p-8">
                    <div class="flex justify-between items-center">
                        <div>
                            <h2 class="text-3xl font-bold text-white mb-2">{{ $player->name }}</h2>
                            <p class="text-indigo-100">{{ $player->email }}</p>
                        </div>
                        <a href="{{ route('player-stats.search') }}"
                           class="bg-white/10 text-white px-4 py-2 rounded-lg hover:bg-white/20 transition duration-150">
                            ← Voltar para busca
                        </a>
                    </div>
                </div>
            </div>

            <!-- Estatísticas Gerais -->
            <div class="bg-white rounded-lg shadow-lg mb-8 overflow-hidden">
                <div class="p-6">
                    <h3 class="text-xl font-semibold text-gray-800 mb-6">Resumo da Carreira</h3>
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-6">
                        <div class="bg-gradient-to-br from-indigo-50 to-white rounded-xl p-6 text-center shadow-sm">
                            <div class="text-3xl font-bold text-indigo-600 mb-1">
                                {{ $player->playerScores->sum('points') }}
                            </div>
                            <div class="text-sm text-gray-600">Pontos Totais</div>
                        </div>
                        <div class="bg-gradient-to-br from-green-50 to-white rounded-xl p-6 text-center shadow-sm">
                            <div class="text-3xl font-bold text-green-600 mb-1">
                                {{ $player->playerScores->sum('games_won') }}
                            </div>
                            <div class="text-sm text-gray-600">Vitórias</div>
                        </div>
                        <div class="bg-gradient-to-br from-red-50 to-white rounded-xl p-6 text-center shadow-sm">
                            <div class="text-3xl font-bold text-red-600 mb-1">
                                {{ $player->playerScores->sum('games_lost') }}
                            </div>
                            <div class="text-sm text-gray-600">Derrotas</div>
                        </div>
                        <div class="bg-gradient-to-br from-blue-50 to-white rounded-xl p-6 text-center shadow-sm">
                            <div class="text-3xl font-bold text-blue-600 mb-1">
                                {{ $player->playerScores->count() }}
                            </div>
                            <div class="text-sm text-gray-600">Torneios</div>
                        </div>
                        <div class="bg-gradient-to-br from-purple-50 to-white rounded-xl p-6 text-center shadow-sm">
                            @php
                                $wins = $player->playerScores->sum('games_won');
                                $total = $wins + $player->playerScores->sum('games_lost');
                                $winRate = $total > 0 ? round(($wins / $total) * 100, 1) : 0;
                            @endphp
                            <div class="text-3xl font-bold text-purple-600 mb-1">{{ $winRate }}%</div>
                            <div class="text-sm text-gray-600">Aproveitamento</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Estatísticas Detalhadas -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Melhor Performance -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div class="bg-gradient-to-r from-indigo-500 to-indigo-600 px-6 py-3">
                        <h4 class="text-white font-semibold">Melhor Performance</h4>
                    </div>
                    <div class="p-6">
                        @php
                            $bestScore = $player->playerScores()
                                ->with('tournament')
                                ->orderBy('points', 'desc')
                                ->first();
                        @endphp
                        <div class="text-center">
                            <p class="text-3xl font-bold text-gray-800 mb-2">
                                {{ $bestScore ? $bestScore->points . ' pts' : 'N/A' }}
                            </p>
                            <p class="text-sm text-gray-600">
                                {{ $bestScore ? $bestScore->tournament->name : 'Nenhum torneio' }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Parceiro Mais Vitorioso -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div class="bg-gradient-to-r from-green-500 to-green-600 px-6 py-3">
                        <h4 class="text-white font-semibold">Melhor Parceria</h4>
                    </div>
                    <div class="p-6">
                        @php
                            $bestPartner = $player->getBestPartner();
                        @endphp
                        <div class="text-center">
                            @if($bestPartner)
                                <p class="text-2xl font-bold text-gray-800 mb-2">{{ $bestPartner->name }}</p>
                                <div class="inline-flex items-center bg-green-100 px-3 py-1 rounded-full">
                                    <span class="text-green-700">{{ $bestPartner->wins }} vitórias juntos</span>
                                </div>
                            @else
                                <p class="text-gray-500 italic">Nenhum parceiro ainda</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Adversário Mais Difícil -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div class="bg-gradient-to-r from-red-500 to-red-600 px-6 py-3">
                        <h4 class="text-white font-semibold">Rival Mais Desafiador</h4>
                    </div>
                    <div class="p-6">
                        @php
                            $toughestOpponent = $player->getToughestOpponent();
                        @endphp
                        <div class="text-center">
                            @if($toughestOpponent)
                                <p class="text-2xl font-bold text-gray-800 mb-2">{{ $toughestOpponent->name }}</p>
                                <div class="inline-flex items-center bg-red-100 px-3 py-1 rounded-full">
                                    <span class="text-red-700">{{ $toughestOpponent->wins }} vitórias contra você</span>
                                </div>
                            @else
                                <p class="text-gray-500 italic">Nenhum adversário ainda</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Histórico de Torneios -->
            @if($player->playerScores->isNotEmpty())
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div class="bg-gradient-to-r from-gray-700 to-gray-800 px-6 py-3">
                        <h4 class="text-white font-semibold">Histórico de Torneios</h4>
                    </div>
                    <div class="p-6">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr class="bg-gray-50">
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Torneio</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pontos</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">V/D</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($player->playerScores->sortByDesc('tournament.start_date') as $score)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4">
                                                <div class="text-sm font-medium text-indigo-600">{{ $score->tournament->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $score->tournament->location }}</div>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-500">
                                                {{ $score->tournament->start_date->format('d/m/Y') }}
                                            </td>
                                            <td class="px-6 py-4">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                                    {{ $score->points }} pts
                                                </span>
                                            </td>
                                            <td class="px-6 py-4">
                                                <span class="text-green-600">{{ $score->games_won }}</span>
                                                <span class="text-gray-400">/</span>
                                                <span class="text-red-600">{{ $score->games_lost }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-guest-layout>
