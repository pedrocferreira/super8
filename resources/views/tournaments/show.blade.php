<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $tournament->name }}
            </h2>
            <div class="flex space-x-4">
                <a href="{{ route('tournaments.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    Voltar
                </a>
                @if($tournament->status === 'draft')
                    <button type="button"
                            onclick="openSelectPlayersModal()"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                        Selecionar Jogadores
                    </button>
                @elseif($tournament->status === 'open')
                    <button type="button"
                            onclick="openGenerateMatchesModal()"
                            class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-700">
                        Gerar Partidas
                    </button>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Informações Básicas -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informações do Torneio</h3>
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Local:</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $tournament->location }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Tipo:</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $tournament->type === 'super_8_individual' ? 'Super 8 Individual' : 'Super 12 Duplas Fixas' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Status:</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $tournament->status }}</dd>
                        </div>
                    </dl>

                    @if($tournament->registration_code && $tournament->registration_open)
                        <div class="mt-6 p-4 bg-indigo-50 rounded-lg">
                            <h4 class="text-sm font-medium text-indigo-800 mb-2">Link de Inscrição</h4>
                            <p class="text-sm text-indigo-600 mb-3">
                                Compartilhe este link com os jogadores para que eles possam se inscrever no torneio:
                            </p>
                            <div class="flex items-center space-x-2">
                                <input type="text" 
                                       value="{{ route('tournament.register', $tournament->registration_code) }}" 
                                       readonly
                                       class="flex-1 block w-full rounded-md border-indigo-300 bg-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <button onclick="copyRegistrationLink(this)" 
                                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Copiar Link
                                </button>
                            </div>
                            <div class="mt-2 text-sm text-indigo-600">
                                Inscritos: {{ $tournament->players()->count() }}/{{ $tournament->max_players }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Ranking -->
            @if($tournament->type === 'super_12_fixed_pairs')
                <!-- Ranking de Duplas -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-4">
                    <div class="p-6 text-gray-900">
                        <h2 class="text-lg font-semibold mb-4">Ranking de Duplas</h2>
                        @if(empty($playerRanking))
                            <p class="text-gray-500">
                                @if($tournament->rounds->isEmpty())
                                    Nenhuma rodada criada ainda.
                                @elseif($tournament->rounds->first()->matches->isEmpty())
                                    Nenhuma partida criada ainda.
                                @else
                                    Nenhuma partida registrada ainda.
                                @endif
                            </p>
                        @else
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead>
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Posição</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dupla</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pontos</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">V/D</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($playerRanking as $pair)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                    {{ $pair['position'] ?? loop->iteration }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $pair['player1']->name }} & {{ $pair['player2']->name }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $pair['points'] ?? 0 }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $pair['games_won'] ?? 0 }}/{{ $pair['games_lost'] ?? 0 }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <!-- Ranking Individual -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Ranking</h3>
                        @if(!is_object($playerRanking) || count($playerRanking) === 0)
                            <p class="text-gray-500">Nenhuma partida registrada ainda.</p>
                        @else
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Posição</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jogador</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pontos</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">V/D</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($playerRanking as $position => $score)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $position + 1 }}º
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                    {{ $score->player->name }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $score->points }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $score->games_won }}/{{ $score->games_lost }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Rodadas -->
            @if($tournament->rounds->isNotEmpty())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
                                <div class="p-6">
                                    <h3 class="text-lg font-medium text-gray-900 mb-4">Rodadas</h3>
                                    @foreach($tournament->rounds as $round)
                                        <div class="mb-8 last:mb-0">
                                            <h4 class="font-medium text-gray-700 mb-4">Rodada {{ $round->round_number }}</h4>

                                            @php
                                                $matchesByCourt = $round->matches->groupBy('court_id');
                                            @endphp

                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                                @foreach($matchesByCourt as $courtId => $matches)
                                                    <div class="border rounded-lg p-4">
                                                        <h5 class="font-medium text-indigo-600 mb-3">
                                                            {{ $matches->first()->court->name }}
                                                        </h5>
                                                        <div class="space-y-4">
                                                            @foreach($matches as $match)
                                                    <div class="border rounded p-3 {{ $match->status === 'completed' ? 'bg-green-50' : 'bg-white' }}">
                                                                    <div class="flex justify-between items-center mb-2">
                                                                        <span class="text-sm text-gray-500">
                                                                            {{ $match->scheduled_time ? $match->scheduled_time->format('H:i') : 'Horário não definido' }}
                                                                            </span>
                                                                            @if($match->status !== 'completed')
                                                                                <button type="button"
                                                                        class="text-sm text-indigo-600 hover:text-indigo-900"
                                                                        onclick="openScoreModal({{ json_encode($match) }})">
                                                                                    Registrar Placar
                                                                                </button>
                                                                            @endif
                                                                        </div>

                                                                    <div class="flex justify-between items-center">
                                                                        <div class="flex-1">
                                                                            <p class="font-medium">
                                                                                {{ $match->team1_player1->name }} /
                                                                                {{ $match->team1_player2->name }}
                                                                            </p>
                                                                        </div>
                                                                        <div class="mx-4 text-gray-500">vs</div>
                                                                        <div class="flex-1 text-right">
                                                                            <p class="font-medium">
                                                                                {{ $match->team2_player1->name }} /
                                                                                {{ $match->team2_player2->name }}
                                                                            </p>
                                                                        </div>
                                                                    </div>

                                                                    @if($match->score_details)
                                                                        <div class="mt-2 text-sm text-gray-500 text-center">
                                                                            Placar: {{ $match->score_details }}
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Modal de Seleção de Jogadores -->
    <div id="selectPlayersModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Selecionar Jogadores</h3>
                <form action="{{ route('tournaments.select-players', $tournament) }}" method="POST">
                    @csrf
                    <div class="mt-2">
                        <p class="text-sm text-gray-500 mb-4">
                            Selecione {{ $tournament->type === 'super_8_individual' ? '8' : '12' }} jogadores para o torneio.
                        </p>
                        <div class="max-h-60 overflow-y-auto">
                        @foreach($availablePlayers as $player)
                                <div class="flex items-center mb-2">
                                <input type="checkbox"
                                           id="player_{{ $player->id }}" 
                                       name="selected_players[]"
                                       value="{{ $player->id }}"
                                       {{ in_array($player->id, $selectedPlayers) ? 'checked' : '' }}
                                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                    <label for="player_{{ $player->id }}" class="ml-2 block text-sm text-gray-900">
                                        {{ $player->name }}
                                    </label>
                                </div>
                            @endforeach
                            </div>
                    </div>
                    <div class="flex justify-end mt-4 space-x-3">
                        <button type="button"
                                onclick="document.getElementById('selectPlayersModal').classList.add('hidden')"
                                class="inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Cancelar
                        </button>
                        <button type="submit"
                                class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Confirmar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal de Gerar Partidas -->
    <div id="generateMatchesModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Gerar Partidas</h3>
                <form action="{{ route('tournaments.generate-matches', $tournament) }}" method="POST">
                    @csrf
                    <div class="mt-2">
                        <p class="text-sm text-gray-500 mb-4">
                        Tem certeza que deseja gerar as partidas para este torneio?
                            @if($tournament->type === 'super_8_individual')
                                Serão geradas partidas para o formato Super 8 Individual.
                            @else
                                Serão geradas partidas para o formato Super 12 Duplas Fixas.
                            @endif
                    </p>
                </div>
                <div class="flex justify-end mt-4 space-x-3">
                        <button type="button"
                                onclick="closeGenerateMatchesModal()"
                                class="inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Cancelar
                    </button>
                        <button type="submit"
                                class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-emerald-600 border border-transparent rounded-md hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500">
                            Confirmar
                        </button>
                    </div>
                    </form>
                </div>
        </div>
    </div>

    <!-- Modal de Registrar Placar -->
    <div id="scoreModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Registrar Placar</h3>
                <form id="scoreForm" action="" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mt-2">
                        <div class="mb-4">
                            <p class="text-sm font-medium text-gray-700 mb-2">Time 1</p>
                            <div id="team1Names" class="text-sm text-gray-600 mb-2"></div>
                            <input type="number" 
                                   name="team1_score" 
                                   id="team1Score"
                                   min="0"
                                   required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>
                        <div class="mb-4">
                            <p class="text-sm font-medium text-gray-700 mb-2">Time 2</p>
                            <div id="team2Names" class="text-sm text-gray-600 mb-2"></div>
                            <input type="number" 
                                   name="team2_score" 
                                   id="team2Score"
                                   min="0"
                                   required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>
                    </div>
                    <div class="flex justify-end mt-4 space-x-3">
                        <button type="button"
                                onclick="closeScoreModal()"
                                class="inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Cancelar
                        </button>
                        <button type="submit"
                                class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Salvar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Função para abrir o modal de seleção de jogadores
        window.openSelectPlayersModal = function() {
            document.getElementById('selectPlayersModal').classList.remove('hidden');
        }

        // Função para fechar o modal quando clicar fora dele
        document.getElementById('selectPlayersModal').addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.add('hidden');
            }
        });

        // Limitar o número de jogadores que podem ser selecionados
        const maxPlayers = {{ $tournament->type === 'super_8_individual' ? 8 : 12 }};
        const checkboxes = document.querySelectorAll('input[name="selected_players[]"]');
        
        checkboxes.forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                const checkedBoxes = document.querySelectorAll('input[name="selected_players[]"]:checked');
                
                if (checkedBoxes.length > maxPlayers) {
                    this.checked = false;
                    alert(`Você só pode selecionar ${maxPlayers} jogadores.`);
                }
            });
        });

        // Funções para o modal de gerar partidas
        window.openGenerateMatchesModal = function() {
            document.getElementById('generateMatchesModal').classList.remove('hidden');
        }

        window.closeGenerateMatchesModal = function() {
            document.getElementById('generateMatchesModal').classList.add('hidden');
        }

        // Fechar modal quando clicar fora
        document.getElementById('generateMatchesModal').addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.add('hidden');
            }
        });

        // Funções para o modal de placar
        window.openScoreModal = function(match) {
            const modal = document.getElementById('scoreModal');
            const form = document.getElementById('scoreForm');
            const team1Names = document.getElementById('team1Names');
            const team2Names = document.getElementById('team2Names');

            // Configura o formulário
            form.action = `/tournaments/matches/${match.id}/score`;
            
            // Mostra os nomes dos jogadores
            if (match.team1_player2) {
                team1Names.textContent = `${match.team1_player1.name} / ${match.team1_player2.name}`;
                team2Names.textContent = `${match.team2_player1.name} / ${match.team2_player2.name}`;
            } else {
                team1Names.textContent = match.team1_player1.name;
                team2Names.textContent = match.team2_player1.name;
            }

            // Limpa os campos de placar
            document.getElementById('team1Score').value = '';
            document.getElementById('team2Score').value = '';

            // Mostra o modal
            modal.classList.remove('hidden');
        }

        window.closeScoreModal = function() {
            document.getElementById('scoreModal').classList.add('hidden');
        }

        // Fechar modal quando clicar fora
        document.getElementById('scoreModal').addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.add('hidden');
            }
        });

        // Validação do formulário de placar
        document.getElementById('scoreForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const team1Score = parseInt(document.getElementById('team1Score').value);
            const team2Score = parseInt(document.getElementById('team2Score').value);

            if (team1Score === team2Score) {
                alert('O placar não pode ser empate.');
                return;
            }

            this.submit();
        });

        function copyRegistrationLink(button) {
            const input = button.previousElementSibling;
            input.select();
            document.execCommand('copy');
            
            const originalText = button.textContent;
            button.textContent = 'Copiado!';
            setTimeout(() => {
                button.textContent = originalText;
            }, 2000);
        }
        });
    </script>
</x-app-layout>
