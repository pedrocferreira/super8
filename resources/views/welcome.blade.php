<x-guest-layout>
    <div class="min-h-screen bg-gray-100">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Cabeçalho -->
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-4xl font-bold text-gray-900">
                        SKY ARENA
                    </h1>
                    <p class="mt-2 text-lg text-gray-600">Ranking Geral de Beach Tennis</p>
                </div>

                <div class="flex items-center gap-4">
                    @if (Route::has('login'))
                        <div class="flex gap-4">
                            @auth
                                <a href="{{ route('dashboard') }}"
                                   class="font-semibold text-gray-600 hover:text-gray-900">
                                    Dashboard
                                </a>
                            @else
                                <a href="{{ route('login') }}"
                                   class="font-semibold text-gray-600 hover:text-gray-900">
                                    Log in
                                </a>
                            @endauth
                        </div>
                    @endif

                    <a href="{{ route('player-stats.search') }}"
                       class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                        Buscar Jogador
                    </a>
                </div>
            </div>

            <!-- Ranking -->
            <div class="bg-white overflow-hidden shadow-xl rounded-lg">
                <div class="overflow-x-auto">
                    <table class="w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">
                                    Posição
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Jogador
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">
                                    Pontos
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">
                                    V/D
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">
                                    Torneios
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-48">
                                    Aproveitamento
                                </th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-32">
                                    Ações
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($playerRanking as $position => $player)
                                <tr class="{{ $position < 3 ? 'bg-gradient-to-r from-yellow-50 to-white' : '' }} hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($position === 0)
                                            <span class="inline-flex items-center justify-center w-8 h-8 bg-yellow-400 text-white rounded-full font-bold">1</span>
                                        @elseif($position === 1)
                                            <span class="inline-flex items-center justify-center w-8 h-8 bg-gray-400 text-white rounded-full font-bold">2</span>
                                        @elseif($position === 2)
                                            <span class="inline-flex items-center justify-center w-8 h-8 bg-amber-600 text-white rounded-full font-bold">3</span>
                                        @else
                                            <span class="px-3 py-1">{{ $position + 1 }}º</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $player->name }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                            {{ $player->total_points }} pts
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-green-600">{{ $player->total_wins }}</span>
                                        <span class="text-gray-400">/</span>
                                        <span class="text-red-600">{{ $player->total_losses }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $player->tournaments_played }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $total = $player->total_wins + $player->total_losses;
                                            $winRate = $total > 0 ? round(($player->total_wins / $total) * 100, 1) : 0;
                                        @endphp
                                        <div class="flex items-center">
                                            <div class="w-32 bg-gray-200 rounded-full h-2 mr-2">
                                                <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ $winRate }}%"></div>
                                            </div>
                                            <span class="text-sm text-gray-600">{{ $winRate }}%</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('player-stats.show', ['email' => $player->email]) }}"
                                           class="text-indigo-600 hover:text-indigo-900">
                                            Ver detalhes
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Rodapé -->
            <div class="mt-8 text-center text-sm text-gray-500">
                © {{ date('Y') }} SKY ARENA. Todos os direitos reservados.
            </div>
        </div>
    </div>
</x-guest-layout>
