@extends('layouts.app')

@section('title', 'Missões - ONÇAS DO OESTE')

@section('content')
    <div class="max-w-4xl mx-auto px-4 py-5 sm:py-6 space-y-6">
        <div class="flex items-center justify-between gap-3 flex-wrap">
            <h1 class="text-lg sm:text-xl font-bold text-white tracking-tight uppercase">Missões</h1>
            @if (auth()->user()->canManageActivities())
                <a href="{{ route('activities.create') }}" class="inline-flex items-center justify-center gap-2 w-full sm:w-auto bg-brand-yellow hover:bg-brand-yellow-hover text-brand-dark font-bold py-2 px-4 rounded-lg transition text-sm uppercase tracking-wider">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Nova
                </a>
            @endif
        </div>

        {{-- Filtros --}}
        <div class="flex gap-2 flex-wrap">
            <a href="{{ route('activities.index') }}" class="px-4 py-2 rounded-lg text-sm font-medium transition {{ !request('filter') ? 'bg-brand-yellow text-brand-dark' : 'bg-brand-dark-card text-brand-gray border border-brand-dark-border hover:text-white' }}">
                Todas
            </a>
            <a href="{{ route('activities.index', ['filter' => 'abertas']) }}" class="px-4 py-2 rounded-lg text-sm font-medium transition {{ request('filter') === 'abertas' ? 'bg-brand-yellow text-brand-dark' : 'bg-brand-dark-card text-brand-gray border border-brand-dark-border hover:text-white' }}">
                Abertas
            </a>
            <a href="{{ route('activities.index', ['filter' => 'encerradas']) }}" class="px-4 py-2 rounded-lg text-sm font-medium transition {{ request('filter') === 'encerradas' ? 'bg-brand-yellow text-brand-dark' : 'bg-brand-dark-card text-brand-gray border border-brand-dark-border hover:text-white' }}">
                Encerradas
            </a>
        </div>

        {{-- Lista --}}
        <div class="space-y-3">
            @forelse ($activities as $activity)
                <a href="{{ route('activities.show', $activity) }}" class="block bg-brand-dark-card border border-brand-dark-border rounded-xl p-4 sm:p-5 hover:border-brand-yellow/50 transition">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1 flex-wrap">
                                <span class="text-xs font-bold text-brand-yellow bg-brand-yellow/10 px-2 py-0.5 rounded uppercase tracking-wider">{{ $activity->typeLabel() }}</span>
                                @if ($activity->isExpired())
                                    <span class="text-xs font-bold text-red-400 bg-red-900/30 px-2 py-0.5 rounded">Encerrada</span>
                                @endif
                            </div>
                            <h3 class="text-white font-semibold break-words leading-snug">{{ $activity->title }}</h3>
                            <div class="flex flex-wrap items-center gap-x-2 gap-y-1 mt-2 text-xs text-brand-gray">
                                <span class="whitespace-nowrap">{{ $activity->date_time->format('d/m/Y \à\s H\hi') }}</span>
                                @if ($activity->location)
                                    <span class="break-words">• {{ $activity->location }}</span>
                                @endif
                                <span class="whitespace-nowrap">• {{ $activity->rsvp_participants_count }} inscrito{{ $activity->rsvp_participants_count === 1 ? '' : 's' }}</span>
                            </div>
                        </div>
                        <span class="text-sm font-bold text-brand-yellow bg-brand-yellow/10 px-3 py-1.5 rounded-lg flex-shrink-0 whitespace-nowrap">+{{ $activity->points }}pts</span>
                    </div>
                </a>
            @empty
                <div class="text-center py-12">
                    <svg class="w-12 h-12 text-brand-dark-border mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    <p class="text-brand-gray">Nenhuma missão encontrada.</p>
                </div>
            @endforelse
        </div>

        {{-- Paginação --}}
        @if ($activities->hasPages())
            <div class="flex justify-center">
                {{ $activities->appends(request()->query())->links('pagination::simple-tailwind') }}
            </div>
        @endif
    </div>
@endsection
