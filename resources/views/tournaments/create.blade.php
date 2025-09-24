<x-app-layout>
    <x-slot name="header">
        <div class="space-y-4">
            <!-- Mobile Header -->
            <div class="md:hidden">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-semibold text-lg text-gray-800 leading-tight">
                        {{ __('Criar Novo Torneio') }}
                    </h2>
                    <a href="{{ route('tournaments.index') }}"
                       class="inline-flex items-center px-3 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Voltar
                    </a>
                </div>
            </div>

            <!-- Desktop Header -->
            <div class="hidden md:flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Criar Novo Torneio') }}
                </h2>
                <a href="{{ route('tournaments.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Voltar
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-6 text-gray-900">
                    <form action="{{ route('tournaments.store') }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700">Nome do Torneio</label>
                            <input type="text"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base sm:text-sm"
                                   id="name"
                                   name="name"
                                   value="{{ old('name') }}"
                                   required>
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="location" class="block text-sm font-medium text-gray-700">Local</label>
                            <input type="text"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base sm:text-sm"
                                   id="location"
                                   name="location"
                                   value="{{ old('location') }}"
                                   required>
                            @error('location')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="type" class="block text-sm font-medium text-gray-700">Tipo do Torneio</label>
                            <select class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base sm:text-sm"
                                    id="type"
                                    name="type"
                                    required>
                                <option value="">Selecione o tipo...</option>
                                <option value="super_8_doubles" {{ old('type') == 'super_8_doubles' ? 'selected' : '' }}>
                                    Super 8 Duplas (8 jogadores, parceiros rotativos)
                                </option>
                                <option value="super_8_fixed_pairs" {{ old('type') == 'super_8_fixed_pairs' ? 'selected' : '' }}>
                                    Super 8 Duplas Fixas (16 jogadores, 8 duplas fixas)
                                </option>
                                <option value="super_12_fixed_pairs" {{ old('type') == 'super_12_fixed_pairs' ? 'selected' : '' }}>
                                    Super 12 Duplas Sorteadas
                                </option>
                                <option value="super_12_selected_pairs" {{ old('type') == 'super_12_selected_pairs' ? 'selected' : '' }}>
                                    Super 12 Duplas Pré-selecionadas
                                </option>
                            </select>
                            @error('type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="category" class="block text-sm font-medium text-gray-700">Categoria</label>
                            <select class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base sm:text-sm"
                                    id="category"
                                    name="category"
                                    required>
                                <option value="mixed" {{ old('category', 'mixed') == 'mixed' ? 'selected' : '' }}>Mista (duplas homem e mulher)</option>
                                <option value="male" {{ old('category') == 'male' ? 'selected' : '' }}>Masculino</option>
                                <option value="female" {{ old('category') == 'female' ? 'selected' : '' }}>Feminino</option>
                            </select>
                            @error('category')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="season_id" class="block text-sm font-medium text-gray-700">Temporada</label>
                            <select class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-base sm:text-sm"
                                    id="season_id"
                                    name="season_id">
                                @foreach(($seasons ?? []) as $season)
                                    <option value="{{ $season->id }}" {{ old('season_id') == $season->id ? 'selected' : '' }}>
                                        {{ $season->name }}
                                        @if($season->start_date || $season->end_date)
                                            ({{ $season->start_date?->format('d/m/Y') }} - {{ $season->end_date?->format('d/m/Y') }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @if(empty($seasons) || count($seasons) === 0)
                                <p class="mt-1 text-xs text-gray-500">Nenhuma temporada cadastrada. Crie uma temporada em breve.</p>
                            @endif
                            <div class="mt-2">
                                <a href="{{ route('seasons.create') }}" class="text-sm text-indigo-600 hover:text-indigo-800">Criar nova temporada</a>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="number_of_courts" class="block text-sm font-medium text-gray-700">Número de Quadras</label>
                            <input type="number"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                   id="number_of_courts"
                                   name="number_of_courts"
                                   value="{{ old('number_of_courts', 2) }}"
                                   min="1"
                                   max="10"
                                   required>
                            @error('number_of_courts')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex flex-col sm:flex-row justify-end gap-3 mt-6">
                            <a href="{{ route('tournaments.index') }}"
                               class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-3 sm:py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Cancelar
                            </a>
                            <button type="submit"
                                    class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-3 sm:py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Criar Torneio
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
