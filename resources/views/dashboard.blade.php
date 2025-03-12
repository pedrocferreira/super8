<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Cards de Torneios e Jogadores -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Card de Torneios -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold">Torneios</h3>
                            <a href="{{ route('tournaments.create') }}"
                               class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Novo Torneio
                            </a>
                        </div>
                        <div class="space-y-3">
                            @foreach($tournaments ?? [] as $tournament)
                                <div class="border-b pb-3">
                                    <div class="flex justify-between items-center">
                                        <div>
                                            <h4 class="font-medium">{{ $tournament->name }}</h4>
                                            <p class="text-sm text-gray-600">{{ $tournament->location }}</p>
                                        </div>
                                        <a href="{{ route('tournaments.show', $tournament) }}"
                                           class="text-indigo-600 hover:text-indigo-900 font-medium">
                                            Ver Detalhes
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Card de Jogadores -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold">Jogadores</h3>
                            <a href="{{ route('players.create') }}"
                               class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-700 focus:bg-emerald-700 active:bg-emerald-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Novo Jogador
                            </a>
                        </div>
                        <div class="space-y-3">
                            @foreach($players ?? [] as $player)
                                <div class="border-b pb-3">
                                    <div class="flex justify-between items-center">
                                        <div>
                                            <h4 class="font-medium">{{ $player->name }}</h4>
                                            <p class="text-sm text-gray-600">{{ $player->email }}</p>
                                        </div>
                                        <a href="{{ route('players.show', $player) }}"
                                           class="text-emerald-600 hover:text-emerald-900 font-medium">
                                            Ver Perfil
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ranking Geral -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Ranking Geral</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Posição</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jogador</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pontos</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vitórias</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Derrotas</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Torneios</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aproveitamento</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($playerRanking as $position => $player)
                                    <tr class="{{ $position < 3 ? 'bg-yellow-50' : '' }}">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            {{ $position + 1 }}º
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $player->name }}
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    {{ $player->email }}
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $player->total_points }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">
                                            {{ $player->total_wins }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600">
                                            {{ $player->total_losses }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $player->tournaments_played }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            @php
                                                $total = $player->total_wins + $player->total_losses;
                                                $winRate = $total > 0 ? round(($player->total_wins / $total) * 100, 1) : 0;
                                            @endphp
                                            {{ $winRate }}%
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
