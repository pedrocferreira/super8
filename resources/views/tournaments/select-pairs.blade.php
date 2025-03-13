<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Etapa 2: Definir Duplas - {{ $tournament->name }}
            </h2>
            <a href="{{ route('tournaments.show', $tournament) }}"
               class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                Voltar
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Seção para mostrar os jogadores selecionados -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Jogadores Selecionados ({{ count($players) }})</h3>
                    
                    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                        @foreach($players as $player)
                            <div class="border rounded p-3 bg-blue-50">
                                <div class="font-medium">{{ $player->name }}</div>
                                <div class="text-sm text-gray-600">{{ $player->email }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Adicione antes do formulário na view select-pairs.blade.php -->
            <div class="mb-4 flex justify-end">
                <form action="{{ route('tournaments.generate-random-pairs', $tournament) }}" method="POST">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700">
                        Gerar Duplas Aleatoriamente
                    </button>
                </form>
            </div>

            <!-- Formulário para definir as duplas -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Definir 6 Duplas com os Jogadores Selecionados</h3>
                    
                    <form action="{{ route('tournaments.store-pairs', $tournament) }}" method="POST">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            @for ($i = 0; $i < 6; $i++)
                                <div class="border rounded-lg p-4 bg-gray-50">
                                    <h4 class="font-medium text-blue-600 mb-3">Dupla {{ $i + 1 }}</h4>
                                    
                                    <div class="mb-3">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Jogador 1</label>
                                        <select name="pairs[{{ $i }}][player1]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="">Selecione o jogador 1</option>
                                            @foreach ($players as $player)
                                                <option value="{{ $player->id }}">{{ $player->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Jogador 2</label>
                                        <select name="pairs[{{ $i }}][player2]" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="">Selecione o jogador 2</option>
                                            @foreach ($players as $player)
                                                <option value="{{ $player->id }}">{{ $player->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            @endfor
                        </div>
                        
                        <div class="flex justify-end">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                                Salvar Duplas e Prosseguir
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Script para prevenir a seleção do mesmo jogador em diferentes duplas
        document.addEventListener('DOMContentLoaded', function() {
            const selects = document.querySelectorAll('select');
            
            selects.forEach(select => {
                select.addEventListener('change', function() {
                    updateOptions();
                });
            });
            
            function updateOptions() {
                // Obter todos os jogadores selecionados
                const selectedPlayers = [];
                selects.forEach(select => {
                    if (select.value) {
                        selectedPlayers.push(select.value);
                    }
                });
                
                // Atualizar opções em todos os selects
                selects.forEach(select => {
                    const currentValue = select.value;
                    
                    // Armazenar as opções originais se ainda não tiverem sido armazenadas
                    if (!select.originalOptions) {
                        select.originalOptions = Array.from(select.options);
                    }
                    
                    // Limpar opções atuais, exceto a primeira (placeholder)
                    while (select.options.length > 1) {
                        select.remove(1);
                    }
                    
                    // Adicionar opções originais, desabilitando as já selecionadas
                    select.originalOptions.forEach((option, index) => {
                        if (index === 0) return; // Pular a opção placeholder
                        
                        const newOption = option.cloneNode(true);
                        // Desabilitar se já estiver selecionado em outro select, exceto se for o valor atual deste select
                        if (selectedPlayers.includes(newOption.value) && newOption.value !== currentValue) {
                            newOption.disabled = true;
                        } else {
                            newOption.disabled = false;
                        }
                        select.add(newOption);
                    });
                    
                    // Restaurar valor atual
                    select.value = currentValue;
                });
            }
            
            // Inicializar
            updateOptions();
        });
    </script>
</x-app-layout> 