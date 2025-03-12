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
                </div>
            </div>

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
                                                        {{ $score->tournament->type === 'super_8_individual' ? 'Super 8' : 'Super 12' }}
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
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
