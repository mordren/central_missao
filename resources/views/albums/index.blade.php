@extends('layouts.app')

@section('title', 'Álbuns de Missões - ONÇAS DO OESTE')

@section('head')
    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
@endsection

@section('content')
    <div class="max-w-5xl mx-auto px-4 py-6 sm:py-8">
        <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
            <h1 class="text-lg sm:text-xl font-bold text-white tracking-tight uppercase">Álbuns de Missões</h1>
            <p class="text-sm text-brand-gray">Fotos das missões concluídas</p>
        </div>

        @if (session('success'))
            <div class="mb-6 bg-green-900/30 border border-green-800 text-green-400 px-4 py-3 rounded-lg text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if ($missions->isEmpty())
            <div class="text-center py-20">
                <svg class="w-16 h-16 text-brand-gray/30 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="text-brand-gray text-lg font-semibold">Nenhum álbum disponível</p>
                <p class="text-brand-gray/60 text-sm mt-1">Álbuns aparecem quando missões concluídas têm fotos aprovadas.</p>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach ($missions as $mission)
                    @php
                        $coverUrl = $mission->banner ? url($mission->banner) : null;
                        $initials = strtoupper(substr($mission->title, 0, 2));
                    @endphp
                    <a href="{{ route('activities.album', $mission) }}"
                       class="group block bg-brand-dark-card border border-brand-dark-border rounded-2xl overflow-hidden hover:border-brand-yellow/50 transition-all duration-200 hover:shadow-lg hover:shadow-brand-yellow/5">
                        {{-- Cover --}}
                        <div class="relative h-44 overflow-hidden">
                            @if ($coverUrl)
                                <img src="{{ $coverUrl }}"
                                     alt="{{ e($mission->title) }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            @else
                                {{-- Fallback gradient cover --}}
                                <div class="w-full h-full bg-gradient-to-br from-brand-dark-input to-brand-dark flex items-center justify-center border-b border-brand-dark-border">
                                    <span class="text-4xl font-extrabold text-brand-yellow/30 select-none tracking-widest">{{ $initials }}</span>
                                </div>
                            @endif
                            {{-- Photo count badge --}}
                            <div class="absolute bottom-2 right-2 bg-black/60 backdrop-blur-sm text-white text-xs font-bold px-2.5 py-1 rounded-full flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                {{ $mission->approved_photos_count }}
                            </div>
                        </div>
                        {{-- Info --}}
                        <div class="p-4">
                            <h2 class="text-base font-bold text-white truncate group-hover:text-brand-yellow transition">{{ $mission->title }}</h2>
                            <div class="flex items-center gap-3 mt-2 text-xs text-brand-gray flex-wrap">
                                <span class="flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    {{ $mission->date_time?->format('d/m/Y') ?? '—' }}
                                </span>
                                @if ($mission->location)
                                    <span class="flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        {{ e($mission->location) }}
                                    </span>
                                @endif
                            </div>
                            <div class="mt-3 flex items-center justify-between">
                                <span class="text-xs text-brand-yellow font-semibold">
                                    {{ $mission->approved_photos_count }} foto{{ $mission->approved_photos_count === 1 ? '' : 's' }}
                                </span>
                                <span class="text-xs text-brand-gray group-hover:text-brand-yellow transition">Ver álbum →</span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
@endsection
