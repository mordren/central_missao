{{-- Logo --}}
<div class="px-4 py-3 border-b border-brand-dark-border">
    <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 hover:opacity-80 transition">
        <img src="{{ asset('public/images/logo.png') }}" alt="ONÇAS DO OESTE" class="h-11 w-auto flex-shrink-0">
        <h1 class="text-xs font-extrabold text-white tracking-tight leading-tight whitespace-nowrap">ONÇAS DO OESTE</h1>
    </a>
</div>

{{-- Perfil resumido --}}
<div class="px-4 py-3 border-b border-brand-dark-border">
    <div class="flex items-center gap-2 mb-1">
        @php $avatarSrc = auth()->user()->avatarSrc(); @endphp
        {{-- Avatar → links to /profile --}}
        <a href="{{ route('profile.show') }}" class="flex-shrink-0 hover:opacity-80 transition" title="Meu perfil">
            @if($avatarSrc)
                <img src="{{ $avatarSrc }}" alt="{{ auth()->user()->displayName() }}"
                     class="w-8 h-8 rounded-full object-cover border border-brand-dark-border">
            @else
                <div class="w-8 h-8 rounded-full bg-brand-dark-input border border-brand-dark-border flex items-center justify-center">
                    <span class="text-xs font-bold text-brand-gray select-none">{{ strtoupper(substr(auth()->user()->displayName(), 0, 1)) }}</span>
                </div>
            @endif
        </a>
        {{-- Name → links to /profile --}}
        <a href="{{ route('profile.show') }}" class="flex-1 min-w-0 text-sm font-semibold text-white truncate hover:text-brand-yellow transition" title="Meu perfil">
            {{ auth()->user()->displayName() }}
        </a>
        {{-- Gear icon → links to /profile/edit --}}
        <a href="{{ route('profile.edit') }}" title="Editar perfil" class="flex-shrink-0 text-brand-gray hover:text-brand-yellow transition p-0.5">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </a>
    </div>
    <div class="flex items-center gap-2">
        <span class="text-xs text-brand-yellow font-bold">{{ auth()->user()->points }} pts</span>
        <span class="text-xs text-brand-gray">•</span>
        @php $sidebarRank = \App\Models\User::where('points', '>', auth()->user()->points)->count() + 1; @endphp
        <span class="text-xs text-brand-gray">#{{ $sidebarRank }}º</span>
    </div>
    @if (!auth()->user()->profile_completed_at)
    <div class="mt-2">
        <a href="{{ route('profile.complete') }}" class="block bg-yellow-500 hover:bg-yellow-400 text-brand-dark font-bold text-center py-1.5 rounded-lg text-xs">Completar cadastro</a>
        <p class="text-[11px] text-brand-gray mt-0.5">+15 pontos</p>
    </div>
    @endif
</div>

{{-- Navegação --}}
<nav class="flex-1 px-3 py-3 space-y-0.5 overflow-y-auto">
    <p class="px-2 text-[10px] font-bold text-brand-gray uppercase tracking-widest mb-2">Geral</p>

    <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 px-2 py-2 rounded-lg text-sm transition {{ request()->routeIs('dashboard') ? 'bg-brand-yellow/10 text-brand-yellow font-semibold' : 'text-brand-gray hover:text-white hover:bg-brand-dark-input' }}">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
        Dashboard
    </a>

    <a href="{{ route('activities.index') }}" class="flex items-center gap-2.5 px-2 py-2 rounded-lg text-sm transition {{ request()->routeIs('activities.*') && !request()->routeIs('activities.album') ? 'bg-brand-yellow/10 text-brand-yellow font-semibold' : 'text-brand-gray hover:text-white hover:bg-brand-dark-input' }}">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
        Missões
    </a>

    <a href="{{ route('ranking') }}" class="flex items-center gap-2.5 px-2 py-2 rounded-lg text-sm transition {{ request()->routeIs('ranking') ? 'bg-brand-yellow/10 text-brand-yellow font-semibold' : 'text-brand-gray hover:text-white hover:bg-brand-dark-input' }}">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
        Ranking
    </a>

    <a href="{{ route('albums.index') }}" class="flex items-center gap-2.5 px-2 py-2 rounded-lg text-sm transition {{ request()->routeIs('albums.*') || request()->routeIs('activities.album') ? 'bg-brand-yellow/10 text-brand-yellow font-semibold' : 'text-brand-gray hover:text-white hover:bg-brand-dark-input' }}">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        Álbuns
    </a>

    <a href="{{ route('sobre') }}" class="flex items-center gap-2.5 px-2 py-2 rounded-lg text-sm transition {{ request()->routeIs('sobre') ? 'bg-brand-yellow/10 text-brand-yellow font-semibold' : 'text-brand-gray hover:text-white hover:bg-brand-dark-input' }}">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20A10 10 0 0012 2z"/></svg>
        Sobre o Site
    </a>

    <a href="{{ route('profile.show') }}" class="flex items-center gap-2.5 px-2 py-2 rounded-lg text-sm transition {{ request()->routeIs('profile.show', 'profile.edit') ? 'bg-brand-yellow/10 text-brand-yellow font-semibold' : 'text-brand-gray hover:text-white hover:bg-brand-dark-input' }}">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        Meu Perfil
    </a>

    {{-- Menu coord/admin --}}
    @if (auth()->user()->canManageActivities())
        <p class="px-2 text-[10px] font-bold text-brand-gray uppercase tracking-widest mt-4 mb-2">Coordenação</p>

        <a href="{{ route('activities.create') }}" class="flex items-center gap-2.5 px-2 py-2 rounded-lg text-sm transition text-brand-gray hover:text-white hover:bg-brand-dark-input">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Criar Missão
        </a>

        <a href="{{ route('admin.activity_submissions.index') }}" class="flex items-center gap-2.5 px-2 py-2 rounded-lg text-sm transition {{ request()->routeIs('admin.activity_submissions.*') ? 'bg-brand-yellow/10 text-brand-yellow font-semibold' : 'text-brand-gray hover:text-white hover:bg-brand-dark-input' }}">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Tarefas Manuais
        </a>
    @endif

    {{-- Menu admin --}}
    @if (auth()->user()->isAdmin())
        <p class="px-2 text-[10px] font-bold text-brand-gray uppercase tracking-widest mt-4 mb-2">Administração</p>

        <a href="{{ route('admin.users') }}" class="flex items-center gap-2.5 px-2 py-2 rounded-lg text-sm transition {{ request()->routeIs('admin.users*') ? 'bg-brand-yellow/10 text-brand-yellow font-semibold' : 'text-brand-gray hover:text-white hover:bg-brand-dark-input' }}">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            Gerenciar Usuários
        </a>

        <a href="{{ route('leads.index') }}" class="flex items-center gap-2.5 px-2 py-2 rounded-lg text-sm transition {{ request()->routeIs('leads.index') ? 'bg-brand-yellow/10 text-brand-yellow font-semibold' : 'text-brand-gray hover:text-white hover:bg-brand-dark-input' }}">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 01-8 0M12 11v2m0 4h.01M6 21h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
            Leads
        </a>

        <a href="{{ route('leads.import') }}" class="flex items-center gap-2.5 px-2 py-2 rounded-lg text-sm transition {{ request()->routeIs('leads.import*') ? 'bg-brand-yellow/10 text-brand-yellow font-semibold' : 'text-brand-gray hover:text-white hover:bg-brand-dark-input' }}">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
            Importar Leads
        </a>
    @endif
</nav>

{{-- Rodapé / Logout --}}
<div class="px-3 py-3 border-t border-brand-dark-border">
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="flex items-center gap-2.5 px-2 py-2 rounded-lg text-sm text-brand-gray hover:text-red-400 hover:bg-red-900/20 transition w-full">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
            Sair
        </button>
    </form>
</div>
