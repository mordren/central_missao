@extends('layouts.app')

@section('title', 'Sobre o Site - Onças do Oeste')

@section('content')
    <div class="max-w-2xl mx-auto px-4 py-8 sm:py-12 space-y-8">

        {{-- Cabeçalho --}}
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight uppercase">Sobre o Site</h1>
            <p class="text-sm text-brand-gray mt-1">Transparência e natureza desta plataforma</p>
        </div>

        {{-- Card de declaração --}}
        <div class="bg-brand-dark-card border border-brand-dark-border rounded-2xl p-6 sm:p-8 space-y-4">
            <div class="flex items-start gap-3">
                <div class="flex-shrink-0 mt-1">
                    <svg class="w-5 h-5 text-brand-yellow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20A10 10 0 0012 2z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-base font-bold text-white mb-2">Declaração de Independência</h2>
                    <p class="text-sm text-gray-300 leading-relaxed">
                        Somos apoiadores do partido Missão por iniciativa própria e não temos relação oficial,
                        nem somos direcionados pelo partido Missão.
                    </p>
                </div>
            </div>
        </div>

        {{-- Informações adicionais --}}
        <div class="bg-brand-dark-card border border-brand-dark-border rounded-2xl p-6 sm:p-8 space-y-4">
            <h2 class="text-base font-bold text-white">Sobre esta plataforma</h2>
            <p class="text-sm text-brand-gray leading-relaxed">
                Esta plataforma foi criada de forma independente por apoiadores voluntários.
                Seu objetivo é organizar e engajar o trabalho de base da nossa comunidade,
                sem nenhum vínculo formal com o partido Missão ou suas lideranças oficiais.
            </p>
        </div>

    </div>
@endsection
