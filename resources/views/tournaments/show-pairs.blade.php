<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Duplas do Torneio - {{ $tournament->name }}
            </h2>
            <a href="{{ route('tournaments.show', $tournament) }}"
               class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                Voltar
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if($pairs->isEmpty())
                        <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4">
                            Não há duplas definidas para este torneio.
                        </div>
                    @else
                        <h3 class="text-lg font-medium mb-4">Duplas definidas:</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($pairs as $index => $pair)
                                <div class="border rounded p-4 bg-gray-50">
                                    <h4 class="font-medium text-blue-600">Dupla {{ $index + 1 }}</h4>
                                    <div class="mt-2">
                                        <div class="flex items-center mb-2">
                                            <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold mr-2">
                                                1
                                            </div>
                                            <span>{{ $pair->player1->name }}</span>
                                        </div>
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold mr-2">
                                                2
                                            </div>
                                            <span>{{ $pair->player2->name }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 