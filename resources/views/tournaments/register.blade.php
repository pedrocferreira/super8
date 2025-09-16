<x-guest-layout>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
            <h2 class="text-2xl font-bold mb-4 text-center">{{ $tournament->name }}</h2>
            <p class="text-gray-600 mb-4 text-center">
                {{ $tournament->type === 'super_8_doubles' ? 'Super 8 Duplas' : 
                   ($tournament->type === 'super_8_fixed_pairs' ? 'Super 8 Duplas Fixas' : 'Super 12 Duplas Fixas') }}
            </p>

            <form id="registrationForm" class="space-y-4">
                @csrf
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" id="email" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                @if($tournament->type === 'super_12_selected_pairs')
                <div id="partnerSection" class="mt-4">
                    <label for="partner_email" class="block text-sm font-medium text-gray-700">Email do Parceiro</label>
                    <input type="email" 
                           name="partner_email" 
                           id="partner_email" 
                           placeholder="Digite o email do seu parceiro"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <p class="text-sm text-gray-500 mt-1">
                        Seu parceiro também deve se inscrever no torneio e informar seu email.
                    </p>
                </div>
                @endif

                <div>
                    <button type="submit"
                            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Verificar Email
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal de Novo Jogador -->
    <div id="newPlayerModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Complete seu Cadastro</h3>
                <form id="newPlayerForm" class="space-y-4">
                    @csrf
                    <input type="hidden" name="is_new" value="true">
                    <input type="hidden" name="email" id="modalEmail">

                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Nome</label>
                        <input type="text" name="name" id="name" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700">Telefone</label>
                        <input type="text" name="phone" id="phone" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeNewPlayerModal()"
                                class="inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                            Cancelar
                        </button>
                        <button type="submit"
                                class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md hover:bg-indigo-700">
                            Cadastrar e Inscrever
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const registrationForm = document.getElementById('registrationForm');
            const newPlayerForm = document.getElementById('newPlayerForm');
            const newPlayerModal = document.getElementById('newPlayerModal');

            registrationForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                const email = document.getElementById('email').value;
                
                try {
                    const response = await fetch(`/tournament-register/{{ $tournament->registration_code }}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ email })
                    });

                    const data = await response.json();

                    if (data.status === 'new_player') {
                        document.getElementById('modalEmail').value = email;
                        newPlayerModal.classList.remove('hidden');
                    } else if (data.status === 'success') {
                        alert(data.message);
                        window.location.reload();
                    } else {
                        alert(data.message);
                    }
                } catch (error) {
                    alert('Erro ao processar sua solicitação. Por favor, tente novamente.');
                }
            });

            newPlayerForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                const formData = new FormData(this);

                try {
                    const response = await fetch(`/tournament-register/{{ $tournament->registration_code }}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify(Object.fromEntries(formData))
                    });

                    const data = await response.json();
                    alert(data.message);

                    if (data.status === 'success') {
                        window.location.reload();
                    }
                } catch (error) {
                    alert('Erro ao processar sua solicitação. Por favor, tente novamente.');
                }
            });
        });

        function closeNewPlayerModal() {
            document.getElementById('newPlayerModal').classList.add('hidden');
        }
    </script>
</x-guest-layout> 