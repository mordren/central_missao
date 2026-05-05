@extends('layouts.app')

@section('title', 'Meu Perfil - ONÇAS DO OESTE')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-5 sm:py-6 space-y-5">

    {{-- Success flash --}}
    @if(session('success'))
        <div class="bg-green-900/30 border border-green-800 text-green-400 px-4 py-3 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- ===== Hero card ===== --}}
    <div class="bg-brand-dark-card border border-brand-dark-border rounded-2xl p-5">
        <div class="flex items-start gap-4">
            {{-- Avatar --}}
            @php $avatarSrc = $user->avatarSrc(); @endphp
            @if($avatarSrc)
                <img src="{{ $avatarSrc }}" alt="{{ $user->displayName() }}"
                     class="w-20 h-20 rounded-full object-cover border-2 border-brand-dark-border flex-shrink-0">
            @else
                <div class="w-20 h-20 rounded-full bg-brand-dark-input border-2 border-brand-dark-border flex items-center justify-center flex-shrink-0">
                    <span class="text-3xl font-extrabold text-brand-yellow select-none">
                        {{ strtoupper(substr($user->displayName(), 0, 1)) }}
                    </span>
                </div>
            @endif

            {{-- Identity --}}
            <div class="flex-1 min-w-0">
                <div class="flex items-start justify-between gap-2">
                    <div class="min-w-0">
                        <h1 class="text-lg font-extrabold text-white leading-tight break-words">{{ $user->name }}</h1>
                        @if($user->nickname)
                            <p class="text-sm text-brand-yellow font-semibold mt-0.5">{{ $user->nickname }}</p>
                        @endif
                        <p class="text-xs text-brand-gray mt-1 capitalize">{{ $user->role }}</p>
                    </div>
                    {{-- Edit button --}}
                    <a href="{{ route('profile.edit') }}"
                       title="Editar perfil"
                       class="flex-shrink-0 flex items-center gap-1.5 bg-brand-dark-input hover:bg-brand-dark-border border border-brand-dark-border text-brand-gray hover:text-white text-xs font-semibold px-3 py-1.5 rounded-lg transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Editar
                    </a>
                </div>

                {{-- Meta info --}}
                <div class="flex flex-wrap gap-x-4 gap-y-1 mt-2">
                    @if($user->city)
                        <span class="text-xs text-brand-gray flex items-center gap-1">
                            <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            {{ $user->city }}@if($user->neighborhood), {{ $user->neighborhood }}@endif
                        </span>
                    @endif
                    <span class="text-xs text-brand-gray flex items-center gap-1">
                        <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Membro desde {{ $user->created_at->translatedFormat('M Y') }}
                    </span>
                    @if($user->profile_completed_at)
                        <span class="text-xs text-green-400 flex items-center gap-1">
                            <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Cadastro completo
                        </span>
                    @else
                        <a href="{{ route('profile.complete') }}" class="text-xs text-yellow-400 hover:text-yellow-300 flex items-center gap-1 transition">
                            <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
                            Cadastro incompleto (+15 pts)
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ===== Stats grid ===== --}}
    <div class="grid grid-cols-3 gap-3">
        <div class="bg-brand-dark-card border border-brand-dark-border rounded-xl p-3 sm:p-4 text-center">
            <p class="text-xl sm:text-2xl font-extrabold text-brand-yellow">{{ $user->points }}</p>
            <p class="text-[11px] sm:text-xs text-brand-gray uppercase tracking-wider mt-0.5">Pts Atuais</p>
        </div>
        <div class="bg-brand-dark-card border border-brand-dark-border rounded-xl p-3 sm:p-4 text-center">
            <p class="text-xl sm:text-2xl font-extrabold text-white">#{{ $userRank }}º</p>
            <p class="text-[11px] sm:text-xs text-brand-gray uppercase tracking-wider mt-0.5">Ranking</p>
        </div>
        <div class="bg-brand-dark-card border border-brand-dark-border rounded-xl p-3 sm:p-4 text-center">
            <p class="text-xl sm:text-2xl font-extrabold text-white">{{ (int) $lifetimePoints }}</p>
            <p class="text-[11px] sm:text-xs text-brand-gray uppercase tracking-wider mt-0.5">Pts Totais</p>
        </div>
    </div>

    {{-- ===== Referral code ===== --}}
    @if($user->referral_code)
    @php $inviteLink = 'https://grey-finch-461274.hostingersite.com/register?ref=' . $user->referral_code; @endphp
    <div class="bg-brand-dark-card border border-brand-dark-border rounded-xl px-4 py-3">
        <p class="text-xs font-bold text-brand-gray uppercase tracking-wider mb-1">Link de convite</p>
        <div class="flex items-center gap-2">
            <p class="flex-1 min-w-0 text-xs text-brand-gray font-mono truncate">{{ $inviteLink }}</p>
            <button onclick="navigator.clipboard.writeText('{{ $inviteLink }}').then(function(){var b=document.getElementById('profile-copy-btn');b.textContent='Copiado!';setTimeout(function(){b.textContent='Copiar';},2000)})"
                    id="profile-copy-btn"
                    class="flex-shrink-0 bg-brand-yellow hover:bg-brand-yellow-hover text-brand-dark font-bold text-xs px-3 py-1.5 rounded-lg transition">
                Copiar
            </button>
        </div>
        @if($referralCount > 0)
            <p class="text-xs text-brand-gray mt-1">{{ $referralCount }} {{ $referralCount === 1 ? 'pessoa indicada' : 'pessoas indicadas' }}</p>
        @endif
    </div>
    @endif

    {{-- ===== Realizações ===== --}}
    <div class="bg-brand-dark-card border border-brand-dark-border rounded-2xl p-5">
        <h2 class="text-sm font-bold text-brand-gray uppercase tracking-wider mb-4">Realizações</h2>

        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
            {{-- Total missões --}}
            <div class="bg-brand-dark-input border border-brand-dark-border rounded-xl p-3 text-center">
                <p class="text-xl font-extrabold text-brand-yellow">{{ $attendedActivities->count() }}</p>
                <p class="text-xs text-brand-gray mt-0.5">Missões concluídas</p>
            </div>

            {{-- Pontos totais --}}
            <div class="bg-brand-dark-input border border-brand-dark-border rounded-xl p-3 text-center">
                <p class="text-xl font-extrabold text-white">{{ (int) $lifetimePoints }}</p>
                <p class="text-xs text-brand-gray mt-0.5">Pontos no total</p>
            </div>

            {{-- Referrals --}}
            <div class="bg-brand-dark-input border border-brand-dark-border rounded-xl p-3 text-center">
                <p class="text-xl font-extrabold text-white">{{ $referralCount }}</p>
                <p class="text-xs text-brand-gray mt-0.5">Usuários trazidos</p>
            </div>

            {{-- Cadastro completado --}}
            <div class="bg-brand-dark-input border border-brand-dark-border rounded-xl p-3 text-center">
                @if($user->profile_completed_at)
                    <p class="text-xl font-extrabold text-green-400">✓</p>
                    <p class="text-xs text-brand-gray mt-0.5">Cadastro completo</p>
                @else
                    <p class="text-xl font-extrabold text-yellow-400">!</p>
                    <p class="text-xs text-brand-gray mt-0.5">Cadastro incompleto</p>
                @endif
            </div>

        </div>
    </div>

    {{-- ===== Atividades frequentadas ===== --}}
    <div class="bg-brand-dark-card border border-brand-dark-border rounded-2xl p-5">
        <h2 class="text-sm font-bold text-brand-gray uppercase tracking-wider mb-4">
            Missões Concluídas
            @if($attendedActivities->isNotEmpty())
                <span class="ml-2 font-normal normal-case text-brand-yellow">({{ $attendedActivities->count() }})</span>
            @endif
        </h2>

        @forelse($attendedActivities as $activity)
            <a href="{{ route('activities.show', $activity) }}"
               class="flex items-start gap-3 p-3 mb-2 last:mb-0 bg-brand-dark-input rounded-xl border border-brand-dark-border hover:border-brand-yellow/40 transition group">

                {{-- Banner thumbnail --}}
                @if($activity->banner)
                    <div class="w-12 h-12 rounded-lg overflow-hidden flex-shrink-0 border border-brand-dark-border">
                        <img src="{{ url($activity->banner) }}" alt="{{ $activity->title }}" class="w-full h-full object-cover">
                    </div>
                @else
                    <div class="w-12 h-12 rounded-lg bg-brand-dark border border-brand-dark-border flex-shrink-0 flex items-center justify-center">
                        <svg class="w-5 h-5 text-brand-gray" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    </div>
                @endif

                {{-- Info --}}
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-white break-words group-hover:text-brand-yellow transition leading-tight">{{ $activity->title }}</p>
                    <div class="flex flex-wrap items-center gap-x-3 gap-y-0.5 mt-1">
                        @if($activity->date_time)
                            <span class="text-xs text-brand-gray">{{ $activity->date_time->translatedFormat('d M Y') }}</span>
                        @endif
                        @if($activity->location)
                            <span class="text-xs text-brand-gray">{{ $activity->location }}</span>
                        @endif
                        <span class="text-xs text-brand-gray">{{ $activity->typeLabel() }}</span>
                    </div>
                </div>

                {{-- Points awarded --}}
                @if($activity->pivot->points_awarded > 0)
                    <span class="flex-shrink-0 text-xs font-bold text-brand-yellow bg-brand-yellow/10 px-2 py-1 rounded-lg self-start">
                        +{{ $activity->pivot->points_awarded }} pts
                    </span>
                @elseif($activity->points > 0)
                    <span class="flex-shrink-0 text-xs font-bold text-brand-gray bg-brand-dark-border/50 px-2 py-1 rounded-lg self-start">
                        +{{ $activity->points }} pts
                    </span>
                @endif
            </a>
        @empty
            <div class="text-center py-8">
                <svg class="w-10 h-10 text-brand-gray/40 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                <p class="text-sm text-brand-gray">Ainda não há missões concluídas.</p>
                <a href="{{ route('activities.index') }}" class="inline-block mt-3 text-xs font-semibold text-brand-yellow hover:underline">Ver missões disponíveis →</a>
            </div>
        @endforelse
    </div>

</div>
@endsection
