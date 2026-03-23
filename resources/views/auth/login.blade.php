@extends('layouts.app')

@section('title', 'Login - Central da Missão')

@section('content')
<div class="flex-1 flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-md">
        {{-- Logo / Header --}}
        <div class="text-center mb-10">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-brand-yellow rounded-2xl mb-5 shadow-lg shadow-brand-yellow/20">
                <svg class="w-10 h-10 text-brand-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <h1 class="text-3xl font-extrabold text-white tracking-tight">CENTRAL DA MISSÃO</h1>
            <p class="text-brand-gray mt-2 text-sm">Entre com seu telefone e senha</p>
        </div>

        {{-- Card de Login --}}
        <div class="bg-brand-dark-card border border-brand-dark-border rounded-2xl p-8">
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

                {{-- Campo Telefone --}}
                <div class="mb-5">
                    <label for="phone" class="block text-sm font-semibold text-brand-gray mb-2 uppercase tracking-wider">Telefone</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-brand-gray" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                        </div>
                        <input
                            type="tel"
                            id="phone"
                            name="phone"
                            value="{{ old('phone') }}"
                            placeholder="(11) 99999-9999"
                            required
                            autofocus
                            class="block w-full pl-10 pr-4 py-3 bg-brand-dark-input border border-brand-dark-border rounded-lg text-white placeholder-brand-gray/60 focus:outline-none focus:ring-2 focus:ring-brand-yellow focus:border-brand-yellow transition @error('phone') border-red-500 @enderror"
                        >
                    </div>
                    @error('phone')
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
                            class="block w-full pl-10 pr-4 py-3 bg-brand-dark-input border border-brand-dark-border rounded-lg text-white placeholder-brand-gray/60 focus:outline-none focus:ring-2 focus:ring-brand-yellow focus:border-brand-yellow transition @error('password') border-red-500 @enderror"
                        >
                    </div>
                    @error('password')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Lembrar-me --}}
                <div class="flex items-center justify-between mb-6">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 bg-brand-dark-input border-brand-dark-border rounded text-brand-yellow focus:ring-brand-yellow focus:ring-offset-brand-dark" {{ old('remember', true) ? 'checked' : '' }}>
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

        {{-- Linha decorativa --}}
        <div class="mt-8 flex items-center justify-center">
            <div class="h-px w-12 bg-brand-dark-border"></div>
            <span class="mx-3 text-xs text-brand-gray/50 uppercase tracking-widest">Missão</span>
            <div class="h-px w-12 bg-brand-dark-border"></div>
        </div>
    </div>
</div>
@endsection
