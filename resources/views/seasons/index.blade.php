<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Temporadas</h2>
            <a href="{{ route('seasons.create') }}"
               class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md text-sm hover:bg-indigo-700">Nova Temporada</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if(session('success'))
                        <div class="mb-4 text-sm text-green-700 bg-green-100 p-3 rounded">{{ session('success') }}</div>
                    @endif
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Início</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fim</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-4 py-2"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse($seasons as $season)
                                    <tr>
                                        <td class="px-4 py-2">{{ $season->name }}</td>
                                        <td class="px-4 py-2">{{ $season->start_date?->format('d/m/Y') }}</td>
                                        <td class="px-4 py-2">{{ $season->end_date?->format('d/m/Y') }}</td>
                                        <td class="px-4 py-2">
                                            <span class="px-2 py-1 text-xs rounded {{ $season->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                                                {{ $season->status === 'active' ? 'Ativa' : 'Encerrada' }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-2 text-right">
                                            <a href="{{ route('rankings.season', $season->id) }}" 
                                               class="inline-flex items-center px-2 py-1 bg-blue-500 text-white text-xs rounded hover:bg-blue-600 mr-2">
                                                🏆 Ranking
                                            </a>
                                            <a href="{{ route('seasons.edit', $season) }}" class="text-indigo-600 hover:text-indigo-800 text-sm mr-3">Editar</a>
                                            <form action="{{ route('seasons.destroy', $season) }}" method="POST" class="inline" onsubmit="return confirm('Remover esta temporada?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-800 text-sm">Remover</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="px-4 py-4 text-center text-sm text-gray-500" colspan="5">Nenhuma temporada encontrada.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">{{ $seasons->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>



