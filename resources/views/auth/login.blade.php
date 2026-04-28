@extends('layouts.app')

@section('title', 'Login - ONÇAS DO OESTE')

@section('content')
<div class="flex-1 flex items-center justify-center px-4 py-8 sm:py-12">
    <div class="w-full max-w-md">
        {{-- Logo / Header --}}
        <div class="text-center mb-10">
            <img src="{{ asset('public/images/logo.png') }}" alt="ONÇAS DO OESTE" class="h-28 w-auto mx-auto mb-5">
            <p class="text-brand-gray mt-2 text-sm">Entre com seu e-mail e senha</p>
        </div>

        {{-- Card de Login --}}
        <div class="bg-brand-dark-card border border-brand-dark-border rounded-2xl p-6 sm:p-8">
            {{-- Mensagem de erro geral --}}
            @if ($errors->any())
                <div class="mb-6 bg-red-900/30 border border-red-800 text-red-400 px-4 py-3 rounded-lg text-sm">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            {{-- Mensagem de sucesso --}}
            @if (session('success'))
                <div class="mb-6 bg-green-900/30 border border-green-800 text-green-400 px-4 py-3 rounded-lg text-sm">
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                {{-- Campo E-mail --}}
                <div class="mb-5">
                    <label for="email" class="block text-sm font-semibold text-brand-gray mb-2 uppercase tracking-wider">E-mail</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-brand-gray" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="seu@email.com"
                            required
                            autofocus
                            class="block w-full pl-10 pr-4 py-3 bg-brand-dark-input border border-brand-dark-border rounded-lg text-white placeholder-brand-gray/60 focus:outline-none focus:ring-2 focus:ring-brand-yellow focus:border-brand-yellow transition @error('email') border-red-500 @enderror"
                        >
                    </div>
                    @error('email')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Campo Senha --}}
                <div class="mb-6">
                    <label for="password" class="block text-sm font-semibold text-brand-gray mb-2 uppercase tracking-wider">Senha</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-brand-gray" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Sua senha"
                            required
                            class="block w-full pl-10 pr-12 py-3 bg-brand-dark-input border border-brand-dark-border rounded-lg text-white placeholder-brand-gray/60 focus:outline-none focus:ring-2 focus:ring-brand-yellow focus:border-brand-yellow transition @error('password') border-red-500 @enderror"
                        >
                        <button type="button"
                                onclick="toggleSenha()"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-brand-gray hover:text-brand-yellow transition"
                                tabindex="-1"
                                aria-label="Mostrar/ocultar senha">
                            {{-- Ícone olho fechado (padrão) --}}
                            <svg id="icon-eye" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            {{-- Ícone olho riscado (quando senha visível) --}}
                            <svg id="icon-eye-off" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                <script>
                    function toggleSenha() {
                        const input = document.getElementById('password');
                        const isText = input.type === 'text';
                        input.type = isText ? 'password' : 'text';
                        document.getElementById('icon-eye').classList.toggle('hidden', !isText);
                        document.getElementById('icon-eye-off').classList.toggle('hidden', isText);
                    }
                </script>

                {{-- Lembrar-me --}}
                <div class="flex items-center justify-between mb-6">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="remember" value="1" class="w-4 h-4 bg-brand-dark-input border-brand-dark-border rounded text-brand-yellow focus:ring-brand-yellow focus:ring-offset-brand-dark" {{ old('remember') ? 'checked' : '' }}>
                        <span class="ml-2 text-sm text-brand-gray">Lembrar-me</span>
                    </label>
                </div>

                {{-- Botão Entrar --}}
                <button type="submit" class="w-full bg-brand-yellow hover:bg-brand-yellow-hover text-brand-dark font-bold py-3.5 px-4 rounded-lg transition duration-200 focus:outline-none focus:ring-2 focus:ring-brand-yellow focus:ring-offset-2 focus:ring-offset-brand-dark uppercase tracking-wider text-sm">
                    Entrar
                </button>
            </form>
        </div>

        {{-- Link para registro --}}
        <p class="text-center text-sm text-brand-gray mt-6">
            Não tem uma conta?
            <a href="{{ route('register') }}" class="text-brand-yellow hover:text-brand-yellow-light font-semibold">Cadastre-se</a>
        </p>

        {{-- Sobre o Site --}}
        <div class="mt-6 bg-brand-yellow/10 border border-brand-yellow/40 rounded-2xl px-6 py-5 space-y-3">
            <p class="text-brand-yellow font-extrabold text-base leading-snug uppercase tracking-wide">
                Apoiadores do partido Missão que por conta própria decidiram criar este site
            </p>
            <p class="text-white/80 text-sm leading-relaxed">
                Somos apoiadores do partido Missão por iniciativa própria e não temos relação oficial, nem somos direcionados pelo partido Missão.
            </p>
            <a href="{{ route('sobre') }}" class="inline-flex items-center gap-1.5 text-brand-yellow font-semibold text-sm hover:text-brand-yellow-light transition">
                Saiba mais
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>

        {{-- Linha decorativa --}}
        <div class="mt-8 flex items-center justify-center">
            <div class="h-px w-12 bg-brand-dark-border"></div>
            <span class="mx-3 text-xs text-brand-gray/50 uppercase tracking-widest"></span>
            <div class="h-px w-12 bg-brand-dark-border"></div>
        </div>
    </div>
</div>
@endsection
