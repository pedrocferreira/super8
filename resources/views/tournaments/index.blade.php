<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Torneios') }}
            </h2>
            @auth
                <a href="{{ route('tournaments.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Novo Torneio
                </a>
            @endauth
        </div>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Estatísticas -->
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Total</p>
                                <p class="text-2xl font-semibold text-gray-900">{{ $stats['total'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Rascunho</p>
                                <p class="text-2xl font-semibold text-yellow-600">{{ $stats['draft'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Abertos</p>
                                <p class="text-2xl font-semibold text-green-600">{{ $stats['open'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Em Andamento</p>
                                <p class="text-2xl font-semibold text-blue-600">{{ $stats['in_progress'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Finalizados</p>
                                <p class="text-2xl font-semibold text-gray-600">{{ $stats['completed'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filtros -->
            <div class="bg-white shadow-sm rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('tournaments.index') }}" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                            <!-- Busca -->
                            <div>
                                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
                                <input type="text" name="search" id="search" value="{{ request('search') }}" 
                                       placeholder="Nome ou local..." 
                                       class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            
                            <!-- Status -->
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                <select name="status" id="status" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">Todos</option>
                                    @foreach($statusOptions as $value => $label)
                                        <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <!-- Tipo -->
                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
                                <select name="type" id="type" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">Todos</option>
                                    @foreach($typeOptions as $value => $label)
                                        <option value="{{ $value }}" {{ request('type') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <!-- Categoria -->
                            <div>
                                <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Categoria</label>
                                <select name="category" id="category" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">Todas</option>
                                    @foreach($categoryOptions as $value => $label)
                                        <option value="{{ $value }}" {{ request('category') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <!-- Temporada -->
                            <div>
                                <label for="season_id" class="block text-sm font-medium text-gray-700 mb-1">Temporada</label>
                                <select name="season_id" id="season_id" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">Todas</option>
                                    @foreach($seasons as $season)
                                        <option value="{{ $season->id }}" {{ request('season_id') == $season->id ? 'selected' : '' }}>{{ $season->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                Filtrar
                            </button>
                            
                            @if(request()->hasAny(['search', 'status', 'type', 'category', 'season_id']))
                                <a href="{{ route('tournaments.index') }}" class="text-sm text-gray-500 hover:text-gray-700">
                                    Limpar filtros
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            <!-- Desktop Table View -->
            <div class="hidden md:block bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="overflow-x-auto">
                        <table class="min-w-full table-auto">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'name', 'sort_direction' => request('sort_direction') == 'asc' ? 'desc' : 'asc']) }}" class="group inline-flex">
                                            Nome
                                            <svg class="ml-2 h-4 w-4 text-gray-400 group-hover:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                                            </svg>
                                        </a>
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Local</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Temporada</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoria</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jogadores</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'status', 'sort_direction' => request('sort_direction') == 'asc' ? 'desc' : 'asc']) }}" class="group inline-flex">
                                            Status
                                            <svg class="ml-2 h-4 w-4 text-gray-400 group-hover:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                                            </svg>
                                        </a>
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'created_at', 'sort_direction' => request('sort_direction') == 'asc' ? 'desc' : 'asc']) }}" class="group inline-flex">
                                            Data
                                            <svg class="ml-2 h-4 w-4 text-gray-400 group-hover:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                                            </svg>
                                        </a>
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($tournaments as $tournament)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                                        <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                        </svg>
                                                    </div>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">{{ $tournament->name }}</div>
                                                    <div class="text-sm text-gray-500">ID: {{ $tournament->id }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <svg class="h-4 w-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                </svg>
                                                {{ $tournament->location }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($tournament->season)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    {{ $tournament->season->name }}
                                                </span>
                                            @else
                                                <span class="text-gray-400 text-sm">Sem temporada</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                            {{ $tournament->type === 'super_8_doubles' ? 'Super 8' : 
                                                   ($tournament->type === 'super_8_fixed_pairs' ? 'Super 8 Fixas' : 
                                                   ($tournament->type === 'super_12_fixed_pairs' ? 'Super 12 Fixas' : 'Super 12 Selecionadas')) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                {{ $tournament->category === 'male' ? 'bg-blue-100 text-blue-800' : 
                                                   ($tournament->category === 'female' ? 'bg-pink-100 text-pink-800' : 'bg-green-100 text-green-800') }}">
                                                {{ $tournament->category === 'male' ? 'Masculino' : 
                                                   ($tournament->category === 'female' ? 'Feminino' : 'Mista') }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <svg class="h-4 w-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                                </svg>
                                                <span class="text-sm text-gray-900">{{ $tournament->players->count() }}/{{ $tournament->max_players }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $statusConfig = [
                                                    'draft' => ['class' => 'bg-yellow-100 text-yellow-800', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                                                    'open' => ['class' => 'bg-green-100 text-green-800', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                                                    'in_progress' => ['class' => 'bg-blue-100 text-blue-800', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                                                    'completed' => ['class' => 'bg-gray-100 text-gray-800', 'icon' => 'M5 13l4 4L19 7']
                                                ];
                                                $config = $statusConfig[$tournament->status] ?? $statusConfig['draft'];
                                            @endphp
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $config['class'] }}">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $config['icon'] }}"></path>
                                                </svg>
                                                {{ ucfirst($tournament->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $tournament->created_at->format('d/m/Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                            <a href="{{ route('tournaments.show', $tournament) }}"
                                                   class="text-indigo-600 hover:text-indigo-900"
                                                   title="Ver detalhes">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                    </svg>
                                                </a>
                                                @auth
                                                    <a href="{{ route('tournaments.create') }}?duplicate={{ $tournament->id }}"
                                                       class="text-gray-600 hover:text-gray-900"
                                                       title="Duplicar">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                                        </svg>
                                                    </a>
                                                @endauth
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $tournaments->links() }}
                    </div>
                </div>
            </div>

            <!-- Mobile Card View -->
            <div class="md:hidden space-y-4">
                @foreach($tournaments as $tournament)
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                        <div class="p-4">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center mb-2">
                                        <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center mr-3">
                                            <svg class="h-5 w-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                        </div>
                                        <div>
                                    <h3 class="text-lg font-semibold text-gray-900 truncate">{{ $tournament->name }}</h3>
                                            <p class="text-xs text-gray-500">ID: {{ $tournament->id }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center text-sm text-gray-500 mb-2">
                                        <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        {{ $tournament->location }}
                                    </div>
                                </div>
                                @php
                                    $statusConfig = [
                                        'draft' => ['class' => 'bg-yellow-100 text-yellow-800', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                                        'open' => ['class' => 'bg-green-100 text-green-800', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                                        'in_progress' => ['class' => 'bg-blue-100 text-blue-800', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                                        'completed' => ['class' => 'bg-gray-100 text-gray-800', 'icon' => 'M5 13l4 4L19 7']
                                    ];
                                    $config = $statusConfig[$tournament->status] ?? $statusConfig['draft'];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $config['class'] }}">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $config['icon'] }}"></path>
                                    </svg>
                                    {{ ucfirst($tournament->status) }}
                                </span>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-3 mb-4">
                                <div>
                                    <p class="text-xs text-gray-500 uppercase tracking-wide">Tipo</p>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        {{ $tournament->type === 'super_8_doubles' ? 'Super 8' : 
                                           ($tournament->type === 'super_8_fixed_pairs' ? 'Super 8 Fixas' : 
                                           ($tournament->type === 'super_12_fixed_pairs' ? 'Super 12 Fixas' : 'Super 12 Selecionadas')) }}
                                    </span>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 uppercase tracking-wide">Categoria</p>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium 
                                        {{ $tournament->category === 'male' ? 'bg-blue-100 text-blue-800' : 
                                           ($tournament->category === 'female' ? 'bg-pink-100 text-pink-800' : 'bg-green-100 text-green-800') }}">
                                        {{ $tournament->category === 'male' ? 'Masculino' : 
                                           ($tournament->category === 'female' ? 'Feminino' : 'Mista') }}
                                    </span>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-3 mb-4">
                                <div>
                                    <p class="text-xs text-gray-500 uppercase tracking-wide">Jogadores</p>
                                    <div class="flex items-center">
                                        <svg class="h-4 w-4 text-gray-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                        </svg>
                                        <span class="text-sm font-medium text-gray-900">{{ $tournament->players->count() }}/{{ $tournament->max_players }}</span>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 uppercase tracking-wide">Temporada</p>
                                    @if($tournament->season)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $tournament->season->name }}
                                        </span>
                                    @else
                                        <span class="text-gray-400 text-xs">Sem temporada</span>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <p class="text-xs text-gray-500">{{ $tournament->created_at->format('d/m/Y') }}</p>
                                <div class="flex space-x-2">
                                <a href="{{ route('tournaments.show', $tournament) }}"
                                   class="inline-flex items-center px-3 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 transition-colors">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    Ver
                                </a>
                                    @auth
                                        <a href="{{ route('tournaments.create') }}?duplicate={{ $tournament->id }}"
                                           class="inline-flex items-center px-3 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-200 transition-colors"
                                           title="Duplicar">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                            </svg>
                                        </a>
                                    @endauth
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
                
                <div class="mt-6">
                    {{ $tournaments->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
