<x-guest-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-gray-900">Estatísticas de Jogadores</h2>
                        <div class="flex space-x-2">
                            <a href="{{ route('public.rankings.index') }}" 
                               class="inline-flex items-center px-4 py-2 bg-green-500 text-white rounded-md text-sm hover:bg-green-600">
                                🏆 Ranking Público
                            </a>
                            <a href="{{ route('rankings.statistics') }}" 
                               class="inline-flex items-center px-4 py-2 bg-blue-500 text-white rounded-md text-sm hover:bg-blue-600">
                                📊 Estatísticas
                            </a>
                        </div>
                    </div>

                    @if(session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('player-stats.show') }}" method="GET" class="max-w-md">
                        <div class="mb-4">
                            <label for="email" class="block text-sm font-medium text-gray-700">
                                Email do Jogador
                            </label>
                            <input type="email"
                                   name="email"
                                   id="email"
                                   required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                   placeholder="Digite o email do jogador">
                        </div>

                        <button type="submit"
                                class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                            Buscar Estatísticas
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
