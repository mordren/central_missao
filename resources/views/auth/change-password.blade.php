@extends('layouts.app')

@section('title', 'Alterar Senha - ONÇAS DO OESTE')

@section('content')
<div class="flex-1 flex items-center justify-center px-4 py-8 sm:py-12">
    <div class="w-full max-w-md">
        {{-- Logo / Header --}}
        <div class="text-center mb-10">
            <img src="{{ asset('public/images/logo.png') }}" alt="ONÇAS DO OESTE" class="h-28 w-auto mx-auto mb-5">
            <p class="text-brand-gray mt-2 text-sm">É necessário alterar sua senha antes de continuar</p>
        </div>

        {{-- Card de Alterar Senha --}}
        <div class="bg-brand-dark-card border border-brand-dark-border rounded-2xl p-6 sm:p-8">
            {{-- Mensagem de erro geral --}}
            @if ($errors->any())
                <div class="mb-6 bg-red-900/30 border border-red-800 text-red-400 px-4 py-3 rounded-lg text-sm">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('password.change.update') }}">
                @csrf

                {{-- Nova Senha --}}
                <div class="mb-5">
                    <label for="password" class="block text-sm font-semibold text-brand-gray mb-2 uppercase tracking-wider">Nova Senha</label>
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
                            placeholder="Nova senha"
                            required
                            class="block w-full pl-10 pr-4 py-3 bg-brand-dark-input border border-brand-dark-border rounded-lg text-white placeholder-brand-gray/60 focus:outline-none focus:ring-2 focus:ring-brand-yellow focus:border-brand-yellow transition @error('password') border-red-500 @enderror"
                        >
                    </div>
                    @error('password')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Confirmar Senha --}}
                <div class="mb-6">
                    <label for="password_confirmation" class="block text-sm font-semibold text-brand-gray mb-2 uppercase tracking-wider">Confirmar Senha</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-brand-gray" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            placeholder="Repita a nova senha"
                            required
                            class="block w-full pl-10 pr-4 py-3 bg-brand-dark-input border border-brand-dark-border rounded-lg text-white placeholder-brand-gray/60 focus:outline-none focus:ring-2 focus:ring-brand-yellow focus:border-brand-yellow transition"
                        >
                    </div>
                </div>

                {{-- Botão Atualizar --}}
                <button type="submit" class="w-full bg-brand-yellow hover:bg-brand-yellow-hover text-brand-dark font-bold py-3.5 px-4 rounded-lg transition duration-200 focus:outline-none focus:ring-2 focus:ring-brand-yellow focus:ring-offset-2 focus:ring-offset-brand-dark uppercase tracking-wider text-sm">
                    Atualizar senha
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
