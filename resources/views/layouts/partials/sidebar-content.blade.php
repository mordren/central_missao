{{-- Logo --}}
<div class="px-6 py-5 border-b border-brand-dark-border">
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 bg-brand-yellow rounded-xl flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-brand-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </div>
        <div>
            <h1 class="text-sm font-extrabold text-white tracking-tight leading-tight">CENTRAL DA<br>MISSÃO</h1>
        </div>
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

    {{-- Menu coord/admin --}}
    @if (auth()->user()->canManageActivities())
        <p class="px-3 text-xs font-bold text-brand-gray uppercase tracking-widest mt-6 mb-3">Coordenação</p>

        <a href="{{ route('activities.create') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition text-brand-gray hover:text-white hover:bg-brand-dark-input">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Criar Atividade
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
