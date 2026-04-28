{{-- Logo --}}
<div class="px-6 py-5 border-b border-brand-dark-border">
    <div class="flex items-center gap-3">
        <img src="{{ asset('public/images/logo.png') }}" alt="ONÇAS DO OESTE" class="h-10 w-auto">
        <h1 class="text-sm font-extrabold text-white tracking-tight leading-tight whitespace-nowrap">ONÇAS DO OESTE</h1>
    </div>
</div>

{{-- Perfil resumido --}}
<div class="px-6 py-4 border-b border-brand-dark-border">
    <p class="text-sm font-semibold text-white truncate">{{ auth()->user()->name }}</p>
    <div class="flex items-center gap-2 mt-1">
        <span class="text-xs text-brand-yellow font-bold">{{ auth()->user()->points }} pts</span>
        <span class="text-xs text-brand-gray">•</span>
        <span class="text-xs text-brand-gray capitalize">{{ auth()->user()->role }}</span>
    </div>
    @if (!auth()->user()->profile_completed_at)
    <div class="mt-3">
        <a href="{{ route('profile.complete') }}" class="block bg-yellow-500 hover:bg-yellow-400 text-brand-dark font-bold text-center py-2 rounded-lg text-xs">Completar cadastro</a>
        <p class="text-[11px] text-brand-gray mt-1">+15 pontos</p>
    </div>
    @endif
</div>

{{-- Navegação --}}
<nav class="flex-1 px-4 py-4 space-y-1 overflow-y-auto">
    <p class="px-3 text-xs font-bold text-brand-gray uppercase tracking-widest mb-3">Geral</p>

    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition {{ request()->routeIs('dashboard') ? 'bg-brand-yellow/10 text-brand-yellow font-semibold' : 'text-brand-gray hover:text-white hover:bg-brand-dark-input' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
        Dashboard
    </a>

    <a href="{{ route('activities.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition {{ request()->routeIs('activities.*') ? 'bg-brand-yellow/10 text-brand-yellow font-semibold' : 'text-brand-gray hover:text-white hover:bg-brand-dark-input' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
        Missões
    </a>

    <a href="{{ route('ranking') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition {{ request()->routeIs('ranking') ? 'bg-brand-yellow/10 text-brand-yellow font-semibold' : 'text-brand-gray hover:text-white hover:bg-brand-dark-input' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
        Ranking
    </a>

    <a href="{{ route('sobre') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition {{ request()->routeIs('sobre') ? 'bg-brand-yellow/10 text-brand-yellow font-semibold' : 'text-brand-gray hover:text-white hover:bg-brand-dark-input' }}">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20A10 10 0 0012 2z"/></svg>
        Sobre o Site
    </a>

    {{-- Menu coord/admin --}}
    @if (auth()->user()->canManageActivities())
        <p class="px-3 text-xs font-bold text-brand-gray uppercase tracking-widest mt-6 mb-3">Coordenação</p>

        <a href="{{ route('activities.create') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition text-brand-gray hover:text-white hover:bg-brand-dark-input">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Criar Atividade
        </a>

        <a href="{{ route('leads.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition {{ request()->routeIs('leads.index') ? 'bg-brand-yellow/10 text-brand-yellow font-semibold' : 'text-brand-gray hover:text-white hover:bg-brand-dark-input' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 01-8 0M12 11v2m0 4h.01M6 21h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
            Leads
        </a>
    @endif

    {{-- Menu admin --}}
    @if (auth()->user()->isAdmin())
        <p class="px-3 text-xs font-bold text-brand-gray uppercase tracking-widest mt-6 mb-3">Administração</p>

        <a href="{{ route('admin.users') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition {{ request()->routeIs('admin.*') ? 'bg-brand-yellow/10 text-brand-yellow font-semibold' : 'text-brand-gray hover:text-white hover:bg-brand-dark-input' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            Gerenciar Usuários
        </a>
    @endif
</nav>

{{-- Rodapé / Logout --}}
<div class="px-4 py-4 border-t border-brand-dark-border">
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-brand-gray hover:text-red-400 hover:bg-red-900/20 transition w-full">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
            Sair
        </button>
    </form>
</div>
