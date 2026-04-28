@extends('layouts.app')

@section('title', 'Dashboard - ONÇAS DO OESTE')

@section('content')
    <div class="max-w-6xl mx-auto px-5 sm:px-6 lg:px-4 py-5 sm:py-6 space-y-6">
        {{-- Mensagem de sucesso --}}
        @if (session('success'))
            <div class="bg-green-900/30 border border-green-800 text-green-400 px-4 py-3 rounded-lg text-sm">
                {{ session('success') }}
            </div>
        @endif

        {{-- Cards de resumo --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
            <div class="bg-brand-dark-card border border-brand-dark-border rounded-xl p-4 text-center">
                <p class="text-2xl font-extrabold text-brand-yellow">{{ $user->points }}</p>
                <p class="text-xs text-brand-gray uppercase tracking-wider mt-1">Pontos</p>
            </div>
            <div class="bg-brand-dark-card border border-brand-dark-border rounded-xl p-4 text-center">
                <p class="text-2xl font-extrabold text-white">{{ $openActivities->count() }}</p>
                <p class="text-xs text-brand-gray uppercase tracking-wider mt-1">Missões Abertas</p>
            </div>
            <div class="bg-brand-dark-card border border-brand-dark-border rounded-xl p-4 text-center">
                <p class="text-2xl font-extrabold text-white">{{ $history->count() }}</p>
                <p class="text-xs text-brand-gray uppercase tracking-wider mt-1">Concluídas</p>
            </div>
            <div class="bg-brand-dark-card border border-brand-dark-border rounded-xl p-4 text-center">
                <p class="text-2xl font-extrabold text-white">{{ $monthActivities->count() }}</p>
                <p class="text-xs text-brand-gray uppercase tracking-wider mt-1">Este Mês</p>
            </div>
        </div>

        {{-- Botão criar Missão (só coord/admin) --}}
        @if ($user->canManageActivities())
            <a href="{{ route('activities.create') }}" class="inline-flex items-center justify-center gap-2 w-full sm:w-auto bg-brand-yellow hover:bg-brand-yellow-hover text-brand-dark font-bold py-3 px-6 rounded-lg transition uppercase tracking-wider text-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Criar Missão
            </a>
        @endif

        {{-- Botão completar cadastro (oculto após completar) --}}
        @if (!auth()->user()->profile_completed_at)
        <div class="mt-4">
            <a href="{{ route('profile.complete') }}" class="inline-flex items-center justify-center gap-3 w-full sm:w-auto bg-yellow-500 hover:bg-yellow-400 text-brand-dark font-bold py-4 px-6 sm:px-8 rounded-lg transition uppercase tracking-wider text-base">
                Completar cadastro
            </a>
            <div class="text-xs text-brand-gray mt-1">+15 pontos</div>
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Calendário --}}
            <div class="lg:col-span-2 bg-brand-dark-card border border-brand-dark-border rounded-2xl p-4 sm:p-6">
                <div class="flex items-center justify-between mb-6">
                    <a href="?month={{ $currentDate->copy()->subMonth()->month }}&year={{ $currentDate->copy()->subMonth()->year }}" class="text-brand-gray hover:text-brand-yellow transition p-1.5 sm:p-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </a>
                    <h2 class="text-base sm:text-lg font-bold text-white uppercase tracking-wider text-center">
                        {{ ucfirst($currentDate->translatedFormat('F Y')) }}
                    </h2>
                    <a href="?month={{ $currentDate->copy()->addMonth()->month }}&year={{ $currentDate->copy()->addMonth()->year }}" class="text-brand-gray hover:text-brand-yellow transition p-1.5 sm:p-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>

                {{-- Dias da semana --}}
                <div class="grid grid-cols-7 gap-1 mb-2">
                    @foreach (['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'] as $day)
                        <div class="text-center text-xs font-semibold text-brand-gray uppercase tracking-wider py-2">{{ $day }}</div>
                    @endforeach
                </div>

                {{-- Dias do mês --}}
                <div class="grid grid-cols-7 gap-1">
                    @php
                        $startOfMonth = $currentDate->copy()->startOfMonth();
                        $endOfMonth = $currentDate->copy()->endOfMonth();
                        $startDay = $startOfMonth->dayOfWeek;
                        $today = now();
                    @endphp

                    {{-- Espaços vazios antes do primeiro dia --}}
                    @for ($i = 0; $i < $startDay; $i++)
                        <div class="aspect-square"></div>
                    @endfor

                    {{-- Dias do mês --}}
                    @for ($day = 1; $day <= $endOfMonth->day; $day++)
                        @php
                            $hasActivities = isset($activitiesByDay[$day]);
                            $isToday = $today->day === $day && $today->month === $month && $today->year === $year;
                            $dayActivities = $hasActivities ? $activitiesByDay[$day] : collect();
                            $actCount = $dayActivities->count();
                        @endphp
                        <div class="aspect-square rounded-lg flex flex-col items-center justify-start relative transition group
                            {{ $isToday ? 'bg-brand-yellow text-brand-dark font-bold' : ($hasActivities ? 'bg-brand-dark-input border border-brand-yellow/40 hover:border-brand-yellow cursor-pointer' : 'bg-brand-dark-input hover:bg-brand-dark-border') }}"
                            @if($hasActivities) onclick="toggleDayDetail({{ $day }})" @endif
                        >
                            <span class="text-sm mt-1.5 {{ $isToday ? 'text-brand-dark' : 'text-white' }}">{{ $day }}</span>
                            @if ($hasActivities)
                                <span class="text-[10px] font-bold mt-0.5 leading-none {{ $isToday ? 'text-brand-dark/70' : 'text-brand-yellow' }}">{{ $actCount }} {{ $actCount === 1 ? 'missão' : 'missões' }}</span>
                                <div class="absolute bottom-0 left-0 right-0 h-1 rounded-b-lg {{ $isToday ? 'bg-brand-dark/30' : 'bg-brand-yellow' }}"></div>
                            @endif
                        </div>
                    @endfor
                </div>

                {{-- Painel de detalhe do dia (aparece ao clicar) --}}
                <div id="day-detail" class="hidden mt-4 bg-brand-dark-input border border-brand-dark-border rounded-xl p-4">
                    <div class="flex items-center justify-between mb-3">
                        <h4 id="day-detail-title" class="text-sm font-bold text-brand-yellow uppercase tracking-wider"></h4>
                        <button onclick="document.getElementById('day-detail').classList.add('hidden')" class="text-brand-gray hover:text-white transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <div id="day-detail-list" class="space-y-2"></div>
                </div>

                <script>
                    const activitiesByDay = @json($activitiesByDayJs);

                    function toggleDayDetail(day) {
                        const panel = document.getElementById('day-detail');
                        const title = document.getElementById('day-detail-title');
                        const list = document.getElementById('day-detail-list');
                        const acts = activitiesByDay[day];

                        if (!acts) return;

                        title.textContent = 'Dia ' + day;
                        list.innerHTML = '';

                        acts.forEach(function(a) {
                            const link = document.createElement('a');
                            link.href = '/activities/' + a.id;
                            link.className = 'flex items-center justify-between p-3 bg-brand-dark rounded-lg border border-brand-dark-border hover:border-brand-yellow/50 transition';
                            const bannerHtml = a.banner ? '<div class="w-12 h-10 rounded-lg overflow-hidden flex-shrink-0 border border-brand-dark-border mr-3"><img src="' + a.banner + '" class="w-full h-full object-cover">' + '</div>' : '';
                            link.innerHTML = '<div class="flex items-start justify-between gap-3"><div class="flex items-center gap-2 flex-shrink-0">' + bannerHtml + '</div><div class="flex-1 min-w-0">' +
                                '<p class="text-sm font-semibold text-white truncate">' + a.title + '</p>' +
                                '<p class="text-xs text-brand-gray mt-0.5">' + a.time + ' • ' + a.type + '</p>' +
                                '</div></div>' +
                                '<span class="text-xs font-bold text-brand-yellow bg-brand-yellow/10 px-2 py-1 rounded ml-2">+' + a.points + 'pts</span>';
                            list.appendChild(link);
                        });

                        panel.classList.remove('hidden');
                    }
                </script>
            </div>

            {{-- Sidebar: Missões abertas --}}
            <div class="space-y-6">
                <div class="bg-brand-dark-card border border-brand-dark-border rounded-2xl p-4 sm:p-6">
                    <h3 class="text-sm font-bold text-brand-yellow uppercase tracking-wider mb-4">Missões Abertas</h3>
                    @forelse ($openActivities as $activity)
                        <a href="{{ route('activities.show', $activity) }}" class="block mb-3 last:mb-0 p-3 bg-brand-dark-input rounded-lg border border-brand-dark-border hover:border-brand-yellow/50 transition">
                            <div class="flex items-start justify-between gap-3">
                                
                                <div class="flex items-center gap-2 flex-shrink-0">
                                    @if ($activity->banner)
                                        <div class="w-10 h-10 rounded-lg overflow-hidden flex-shrink-0 border border-brand-dark-border">
                                            <img src="{{ url($activity->banner) }}" alt="{{ $activity->title }}" class="w-full h-full object-cover">
                                        </div>
                                    @endif
                                    
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-white">{{ $activity->title }}</p>
                                    <p class="text-xs text-brand-gray mt-1">{{ $activity->date_time->format('d/m • H\hi') }}</p>
                                </div>
                                <span class="text-xs font-bold text-brand-yellow bg-brand-yellow/10 px-2 py-1 rounded whitespace-nowrap">+{{ $activity->points }}pts</span>
                            </div>
                            <div class="mt-2">
                                <span class="text-xs text-brand-gray bg-brand-dark-border px-2 py-0.5 rounded">{{ $activity->typeLabel() }}</span>
                            </div>
                        </a>
                    @empty
                        <p class="text-sm text-brand-gray text-center py-4">Nenhuma missão aberta no momento.</p>
                    @endforelse
                </div>

                {{-- Histórico recente --}}
                <div class="bg-brand-dark-card border border-brand-dark-border rounded-2xl p-4 sm:p-6">
                    <h3 class="text-sm font-bold text-brand-yellow uppercase tracking-wider mb-4">Histórico Recente</h3>
                    @forelse ($history as $item)
                        <div class="flex items-center gap-3 mb-3 last:mb-0">
                            <div class="w-8 h-8 bg-green-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-white truncate">{{ $item->title }}</p>
                                <p class="text-xs text-brand-gray">+{{ $item->points }}pts</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-brand-gray text-center py-4">Nenhuma Missão concluída ainda.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
