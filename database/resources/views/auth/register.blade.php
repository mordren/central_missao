@extends('layouts.app')

@section('title', 'Cadastro - ONÇAS DO OESTE')

@section('content')
<div class="flex-1 flex items-center justify-center px-4 py-8 sm:py-12">
    <div class="w-full max-w-md">
        {{-- Logo / Header --}}
        <div class="text-center mb-10">
            <img src="{{ asset('public/images/logo.png') }}" alt="ONÇAS DO OESTE" class="h-24 w-auto mx-auto mb-5">
            <h1 class="text-3xl font-extrabold text-white tracking-tight">CRIAR CONTA</h1>
            <p class="text-brand-gray mt-2 text-sm">Preencha seus dados para se cadastrar</p>
        </div>

        {{-- Card de Cadastro --}}
        <div class="bg-brand-dark-card border border-brand-dark-border rounded-2xl p-6 sm:p-8">
            {{-- Mensagem de erro geral --}}
            @if ($errors->any())
                <div class="mb-6 bg-red-900/30 border border-red-800 text-red-400 px-4 py-3 rounded-lg text-sm">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}">
                @csrf

                {{-- Campo Nome --}}
                <div class="mb-5">
                    <label for="name" class="block text-sm font-semibold text-brand-gray mb-2 uppercase tracking-wider">Nome completo</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-brand-gray" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name') }}"
                            placeholder="Seu nome completo"
                            required
                            autofocus
                            class="block w-full pl-10 pr-4 py-3 bg-brand-dark-input border border-brand-dark-border rounded-lg text-white placeholder-brand-gray/60 focus:outline-none focus:ring-2 focus:ring-brand-yellow focus:border-brand-yellow transition @error('name') border-red-500 @enderror"
                        >
                    </div>
                    @error('name')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

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
                            class="block w-full pl-10 pr-4 py-3 bg-brand-dark-input border border-brand-dark-border rounded-lg text-white placeholder-brand-gray/60 focus:outline-none focus:ring-2 focus:ring-brand-yellow focus:border-brand-yellow transition @error('phone') border-red-500 @enderror"
                        >
                    </div>
                    @error('phone')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Campo E-mail --}}
                <div class="mb-5">
                    <label for="email" class="block text-sm font-semibold text-brand-gray mb-2 uppercase tracking-wider">E-mail <span class="text-brand-gray/50 normal-case">(opcional)</span></label>
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
                            class="block w-full pl-10 pr-4 py-3 bg-brand-dark-input border border-brand-dark-border rounded-lg text-white placeholder-brand-gray/60 focus:outline-none focus:ring-2 focus:ring-brand-yellow focus:border-brand-yellow transition @error('email') border-red-500 @enderror"
                        >
                    </div>
                    @error('email')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Cidade e Bairro lado a lado --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
                    <div>
                        <label for="city" class="block text-sm font-semibold text-brand-gray mb-2 uppercase tracking-wider">Cidade</label>
                        <input
                            type="text"
                            id="city"
                            name="city"
                            value="{{ old('city') }}"
                            placeholder="Sua cidade"
                            required
                            class="block w-full px-4 py-3 bg-brand-dark-input border border-brand-dark-border rounded-lg text-white placeholder-brand-gray/60 focus:outline-none focus:ring-2 focus:ring-brand-yellow focus:border-brand-yellow transition @error('city') border-red-500 @enderror"
                        >
                        @error('city')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="neighborhood" class="block text-sm font-semibold text-brand-gray mb-2 uppercase tracking-wider">Bairro</label>
                        <input
                            type="text"
                            id="neighborhood"
                            name="neighborhood"
                            value="{{ old('neighborhood') }}"
                            placeholder="Seu bairro"
                            required
                            class="block w-full px-4 py-3 bg-brand-dark-input border border-brand-dark-border rounded-lg text-white placeholder-brand-gray/60 focus:outline-none focus:ring-2 focus:ring-brand-yellow focus:border-brand-yellow transition @error('neighborhood') border-red-500 @enderror"
                        >
                        @error('neighborhood')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Código de indicação --}}
                <div class="mb-5">
                    <label for="referral_code_input" class="block text-sm font-semibold text-brand-gray mb-2 uppercase tracking-wider">Código de indicação <span class="text-brand-gray/50 normal-case">(opcional)</span></label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-brand-gray" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <input
                            type="text"
                            id="referral_code_input"
                            name="referral_code_input"
                            value="{{ old('referral_code_input') }}"
                            placeholder="Ex: A1B2C3D4"
                            class="block w-full pl-10 pr-4 py-3 bg-brand-dark-input border border-brand-dark-border rounded-lg text-white placeholder-brand-gray/60 focus:outline-none focus:ring-2 focus:ring-brand-yellow focus:border-brand-yellow transition"
                        >
                    </div>
                </div>

                {{-- Campo Senha --}}
                <div class="mb-5">
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
                            placeholder="Mínimo 8 caracteres"
                            required
                            class="block w-full pl-10 pr-4 py-3 bg-brand-dark-input border border-brand-dark-border rounded-lg text-white placeholder-brand-gray/60 focus:outline-none focus:ring-2 focus:ring-brand-yellow focus:border-brand-yellow transition @error('password') border-red-500 @enderror"
                        >
                    </div>
                    @error('password')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Campo Confirmar Senha --}}
                <div class="mb-6">
                    <label for="password_confirmation" class="block text-sm font-semibold text-brand-gray mb-2 uppercase tracking-wider">Confirmar senha</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-brand-gray" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            placeholder="Repita a senha"
                            required
                            class="block w-full pl-10 pr-4 py-3 bg-brand-dark-input border border-brand-dark-border rounded-lg text-white placeholder-brand-gray/60 focus:outline-none focus:ring-2 focus:ring-brand-yellow focus:border-brand-yellow transition"
                        >
                    </div>
                </div>

                {{-- Botão Cadastrar --}}
                <button type="submit" class="w-full bg-brand-yellow hover:bg-brand-yellow-hover text-brand-dark font-bold py-3.5 px-4 rounded-lg transition duration-200 focus:outline-none focus:ring-2 focus:ring-brand-yellow focus:ring-offset-2 focus:ring-offset-brand-dark uppercase tracking-wider text-sm">
                    Cadastrar
                </button>
            </form>
        </div>

        {{-- Link para login --}}
        <p class="text-center text-sm text-brand-gray mt-6">
            Já tem uma conta?
            <a href="{{ route('login') }}" class="text-brand-yellow hover:text-brand-yellow-light font-semibold">Entrar</a>
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
