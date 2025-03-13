<x-guest-layout>
    <div class="min-h-screen flex">
        <!-- Coluna de imagem/branding - Lado Esquerdo -->
        <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-r from-purple-600 to-indigo-800 justify-center items-center">
            <div class="max-w-md text-center px-8">
                <h1 class="text-5xl font-bold text-white mb-6">Bem-vindo(a) de volta!</h1>
                <p class="text-xl text-white/80 mb-8">
                    Acesse sua conta para gerenciar seus torneios de tênis de mesa e acompanhar seu progresso.
                </p>
                <img src="{{ asset('images/table-tennis.svg') }}" alt="Tênis de Mesa" class="w-64 h-64 mx-auto opacity-90">
            </div>
        </div>

        <!-- Coluna de formulário - Lado Direito -->
        <div class="w-full lg:w-1/2 flex items-center justify-center px-6 py-8">
            <div class="w-full max-w-md">
                <!-- Logo -->
                <div class="flex justify-center mb-8">
                    <a href="/" class="inline-flex items-center">
                        <x-application-logo class="w-20 h-20 fill-current text-indigo-600" />
                    </a>
                </div>

                <h2 class="text-3xl font-extrabold text-gray-900 text-center mb-6">
                    Entre em sua conta
                </h2>

                <!-- Status da Sessão -->
                @if (session('status'))
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                        {{ session('status') }}
                    </div>
                @endif

                <!-- Erros de Validação -->
                @if ($errors->any())
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                        <div class="font-medium">{{ __('Oops! Algo deu errado.') }}</div>

                        <ul class="mt-3 list-disc list-inside text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">
                            Email
                        </label>
                        <div class="mt-1">
                            <input id="email" type="email" name="email" :value="old('email')" required autofocus
                                class="appearance-none block w-full px-3 py-3 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out"
                                placeholder="seu@email.com" />
                        </div>
                    </div>

                    <!-- Senha -->
                    <div>
                        <div class="flex items-center justify-between">
                            <label for="password" class="block text-sm font-medium text-gray-700">
                                Senha
                            </label>
                            @if (Route::has('password.request'))
                                <a class="text-sm font-medium text-indigo-600 hover:text-indigo-500" 
                                   href="{{ route('password.request') }}">
                                    Esqueceu sua senha?
                                </a>
                            @endif
                        </div>
                        <div class="mt-1">
                            <input id="password" type="password" name="password" required 
                                class="appearance-none block w-full px-3 py-3 border border-gray-300 rounded-lg shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out"
                                placeholder="••••••••" />
                        </div>
                    </div>

                    <!-- Lembrar-me -->
                    <div class="flex items-center">
                        <input id="remember_me" type="checkbox" name="remember"
                            class="h-5 w-5 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        <label for="remember_me" class="ml-2 block text-sm text-gray-700">
                            Lembrar-me
                        </label>
                    </div>

                    <div>
                        <button type="submit"
                            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                            Entrar
                        </button>
                    </div>
                </form>

                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-600">
                        Não tem uma conta?
                        <a href="{{ route('register') }}" class="font-medium text-indigo-600 hover:text-indigo-500">
                            Registre-se agora
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
