<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Editar Temporada</h2>
            <a href="{{ route('seasons.index') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-md text-sm hover:bg-gray-700">Voltar</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('seasons.update', $season) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700">Nome</label>
                            <input type="text" id="name" name="name" value="{{ old('name', $season->name) }}" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('name')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="start_date" class="block text-sm font-medium text-gray-700">Início</label>
                                <input type="date" id="start_date" name="start_date" value="{{ old('start_date', optional($season->start_date)->format('Y-m-d')) }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('start_date')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="end_date" class="block text-sm font-medium text-gray-700">Fim</label>
                                <input type="date" id="end_date" name="end_date" value="{{ old('end_date', optional($season->end_date)->format('Y-m-d')) }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('end_date')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                            </div>
                        </div>
                        <div class="mt-4">
                            <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                            <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="active" {{ old('status', $season->status) === 'active' ? 'selected' : '' }}>Ativa</option>
                                <option value="closed" {{ old('status', $season->status) === 'closed' ? 'selected' : '' }}>Encerrada</option>
                            </select>
                            @error('status')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div class="flex justify-end mt-6">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md text-sm hover:bg-indigo-700">Salvar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>




