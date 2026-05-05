@extends('layouts.app')

@section('title', $activity->title . ' — Álbum · ONÇAS DO OESTE')

@section('head')
    {{-- PhotoSwipe --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/photoswipe@5/dist/photoswipe.css">
    <script type="module">
        import PhotoSwipeLightbox from 'https://cdn.jsdelivr.net/npm/photoswipe@5/dist/photoswipe-lightbox.esm.min.js';
        import PhotoSwipe from 'https://cdn.jsdelivr.net/npm/photoswipe@5/dist/photoswipe.esm.min.js';

        const lightbox = new PhotoSwipeLightbox({
            gallery: '#photo-gallery',
            children: 'a.pswp-item',
            pswpModule: PhotoSwipe,
            showHideAnimationType: 'fade',
            bgOpacity: 0.95,
        });
        lightbox.init();
    </script>
    {{-- FilePond --}}
    <link href="https://unpkg.com/filepond/dist/filepond.min.css" rel="stylesheet">
    <script defer src="https://unpkg.com/filepond/dist/filepond.min.js"></script>
    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
        .filepond--root { font-family: 'Inter', sans-serif; }
        .filepond--panel-root { background: #1A1A1A; border: 1px solid #2A2A2A; border-radius: 0.75rem; }
        .filepond--drop-label { color: #888; }
        .filepond--label-action { color: #FFD600; text-decoration: underline; }
        /* PhotoSwipe caption */
        .pswp__caption__center { text-align: center; font-size: 0.85rem; color: #ccc; padding: 0 1rem; }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const input = document.getElementById('photo-filepond');
            if (input && window.FilePond) {
                FilePond.create(input, {
                    allowMultiple: true,
                    maxFiles: 10,
                    maxFileSize: '5MB',
                    acceptedFileTypes: ['image/jpeg', 'image/png', 'image/webp'],
                    labelIdle: 'Arraste fotos aqui ou <span class="filepond--label-action">Escolher</span>',
                    labelMaxFileSizeExceeded: 'Foto muito grande (máx. 5MB)',
                    labelMaxFileSize: 'Tamanho máximo: 5MB',
                    labelFileTypeNotAllowed: 'Formato inválido.',
                    fileValidateTypeLabelExpectedTypes: 'Aceitos: JPG, PNG, WebP',
                    storeAsFile: true,
                });
            }
        });
    </script>
@endsection

@section('content')
    <div class="max-w-5xl mx-auto px-4 py-6 sm:py-8" x-data="{ showUpload: false }">

        {{-- Back link --}}
        <div class="mb-4 flex items-center gap-3 flex-wrap">
            <a href="{{ route('albums.index') }}" class="text-brand-gray hover:text-brand-yellow transition text-sm">&larr; Todos os Álbuns</a>
            <span class="text-brand-gray/40">·</span>
            <a href="{{ route('activities.show', $activity) }}" class="text-brand-gray hover:text-brand-yellow transition text-sm">Ver Missão</a>
        </div>

        {{-- Flash messages --}}
        @if (session('success'))
            <div class="mb-4 bg-green-900/30 border border-green-800 text-green-400 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="mb-4 bg-red-900/30 border border-red-800 text-red-400 px-4 py-3 rounded-lg text-sm">{{ session('error') }}</div>
        @endif

        {{-- Mission header --}}
        <div class="bg-brand-dark-card border border-brand-dark-border rounded-2xl overflow-hidden mb-6">
            @php
                $coverUrl = $activity->banner ? url($activity->banner) : null;
                $initials = strtoupper(substr($activity->title, 0, 2));
            @endphp

            {{-- Cover --}}
            @if ($coverUrl)
                <div class="relative h-48 sm:h-64 overflow-hidden">
                    <img src="{{ $coverUrl }}" alt="{{ e($activity->title) }}" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-brand-dark-card/90 via-transparent to-transparent"></div>
                </div>
            @else
                <div class="h-32 bg-gradient-to-br from-brand-dark-input to-brand-dark flex items-center justify-center border-b border-brand-dark-border">
                    <span class="text-6xl font-extrabold text-brand-yellow/20 select-none tracking-widest">{{ $initials }}</span>
                </div>
            @endif

            <div class="p-5 sm:p-7">
                <div class="flex items-start justify-between flex-wrap gap-3">
                    <div class="min-w-0">
                        <span class="text-xs font-bold text-brand-yellow bg-brand-yellow/10 px-2 py-0.5 rounded-full uppercase tracking-wider">Álbum</span>
                        <h1 class="text-xl sm:text-2xl font-extrabold text-white mt-2 break-words">{{ $activity->title }}</h1>
                        <div class="flex items-center gap-4 mt-2 text-sm text-brand-gray flex-wrap">
                            @if ($activity->date_time)
                                <span class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    {{ $activity->date_time->format('d/m/Y') }}
                                </span>
                            @endif
                            @if ($activity->location)
                                <span class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    {{ e($activity->location) }}
                                </span>
                            @endif
                            <span class="flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                {{ $approvedPhotos->count() }} foto{{ $approvedPhotos->count() === 1 ? '' : 's' }}
                            </span>
                        </div>
                    </div>
                    <button @click="showUpload = !showUpload"
                            class="inline-flex items-center gap-2 bg-brand-dark-input border border-brand-dark-border hover:border-brand-yellow text-white text-sm font-semibold px-4 py-2.5 rounded-lg transition flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        Enviar Fotos
                    </button>
                </div>

                @if ($activity->description)
                    <div class="mt-4 text-sm text-brand-gray whitespace-pre-wrap break-words border-t border-brand-dark-border pt-4">{{ $activity->description }}</div>
                @endif
            </div>
        </div>

        {{-- Upload form --}}
        <div x-show="showUpload" x-cloak class="mb-6 bg-brand-dark-card border border-brand-dark-border rounded-2xl p-5 sm:p-7">
            <h2 class="text-sm font-bold text-white uppercase tracking-wider mb-4">Enviar Fotos</h2>
            <form method="POST" action="{{ route('activities.photos.store', $activity) }}" enctype="multipart/form-data">
                @csrf
                @if ($errors->any())
                    <div class="mb-4 bg-red-900/30 border border-red-800 text-red-400 px-3 py-2 rounded-lg text-sm">
                        @foreach ($errors->all() as $err) <p>{{ $err }}</p> @endforeach
                    </div>
                @endif
                <div class="mb-4">
                    <label class="block text-xs font-semibold text-brand-gray uppercase tracking-wider mb-2">
                        Fotos <span class="text-brand-gray/50 normal-case">(JPG, PNG, WebP · até 5MB cada · máx. 10)</span>
                    </label>
                    <input type="file" name="photos[]" id="photo-filepond" multiple accept="image/jpeg,image/png,image/webp">
                </div>
                <div class="mb-5">
                    <label class="block text-xs font-semibold text-brand-gray uppercase tracking-wider mb-1">Legenda <span class="text-brand-gray/50 normal-case">(opcional)</span></label>
                    <input type="text" name="captions[]" maxlength="255" placeholder="Ex: Distribuição de panfletos no centro"
                           value="{{ old('captions.0') }}"
                           class="block w-full px-3 py-2.5 bg-brand-dark-input border border-brand-dark-border rounded-lg text-white placeholder-brand-gray/50 text-sm focus:outline-none focus:ring-1 focus:ring-brand-yellow">
                </div>
                <div class="flex items-center gap-3">
                    <button type="submit"
                            class="inline-flex items-center gap-2 bg-brand-yellow hover:bg-brand-yellow-hover text-brand-dark font-bold py-2.5 px-5 rounded-lg transition text-sm">
                        Enviar Fotos
                    </button>
                    <button type="button" @click="showUpload = false"
                            class="text-sm text-brand-gray hover:text-white transition">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>

        {{-- Approved photo gallery --}}
        @if ($approvedPhotos->count() > 0)
            <div class="mb-8">
                <h2 class="text-sm font-bold text-brand-yellow uppercase tracking-wider mb-4">
                    Fotos ({{ $approvedPhotos->count() }})
                </h2>
                <div id="photo-gallery" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2 sm:gap-3">
                    @foreach ($approvedPhotos as $photo)
                        <div class="group relative">
                            <a class="pswp-item block"
                               href="{{ $photo->url() }}"
                               data-pswp-width="1200"
                               data-pswp-height="900"
                               data-caption="{{ e($photo->caption ?? '') }}"
                               target="_blank">
                                <div class="aspect-square overflow-hidden rounded-xl border border-brand-dark-border bg-brand-dark-input">
                                    <img src="{{ $photo->url() }}"
                                         alt="{{ e($photo->caption ?? 'Foto') }}"
                                         loading="lazy"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                </div>
                                @if ($photo->caption)
                                    <p class="mt-1.5 text-xs text-brand-gray line-clamp-2 leading-relaxed px-0.5">{{ e($photo->caption) }}</p>
                                @endif
                            </a>
                            {{-- Moderator controls --}}
                            @if (auth()->user()->canManageActivities() || auth()->id() === $photo->user_id)
                                <div class="absolute top-1.5 right-1.5 flex gap-1 opacity-0 group-hover:opacity-100 transition">
                                    @can('delete', $photo)
                                        <form method="POST" action="{{ route('activities.photos.destroy', [$activity, $photo]) }}"
                                              x-data
                                              @submit.prevent="if(confirm('Excluir esta foto?')) $el.submit()">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                    class="bg-red-800/90 hover:bg-red-700 text-white text-[10px] font-bold w-6 h-6 rounded flex items-center justify-center transition"
                                                    title="Excluir">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            @endif
                            {{-- Uploader info for moderators --}}
                            @if (auth()->user()->canManageActivities())
                                <p class="text-[10px] text-brand-gray/60 mt-0.5 px-0.5 truncate">por {{ $photo->uploader->displayName() }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="text-center py-16">
                <svg class="w-14 h-14 text-brand-gray/25 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="text-brand-gray font-semibold">Nenhuma foto aprovada ainda</p>
                <p class="text-brand-gray/60 text-sm mt-1">Seja o primeiro a enviar uma foto desta missão!</p>
                <button @click="showUpload = true"
                        class="mt-4 inline-flex items-center gap-2 bg-brand-yellow hover:bg-brand-yellow-hover text-brand-dark font-bold py-2 px-4 rounded-lg transition text-sm">
                    Enviar Fotos
                </button>
            </div>
        @endif

        {{-- Pending photos section --}}
        @if ($pendingPhotos->count() > 0)
            <div class="border-t border-brand-dark-border pt-8">
                <h2 class="text-sm font-bold text-brand-gray uppercase tracking-wider mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    @if (auth()->user()->canManageActivities())
                        Aguardando Aprovação ({{ $pendingPhotos->count() }})
                    @else
                        Suas Fotos — Aguardando Aprovação
                    @endif
                </h2>
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2 sm:gap-3">
                    @foreach ($pendingPhotos as $photo)
                        <div class="group relative">
                            <div class="aspect-square overflow-hidden rounded-xl border border-yellow-700/50 bg-brand-dark-input relative">
                                <img src="{{ $photo->url() }}"
                                     alt="{{ e($photo->caption ?? 'Foto pendente') }}"
                                     loading="lazy"
                                     class="w-full h-full object-cover opacity-50">
                                <div class="absolute inset-0 flex flex-col items-center justify-center">
                                    <span class="bg-yellow-600/80 text-white text-[11px] font-bold px-2 py-1 rounded-full">Aguardando</span>
                                    @if (auth()->user()->canManageActivities())
                                        <span class="text-[10px] text-white/70 mt-1">{{ $photo->uploader->displayName() }}</span>
                                    @endif
                                </div>
                            </div>
                            @if ($photo->caption)
                                <p class="mt-1.5 text-xs text-brand-gray line-clamp-2 leading-relaxed px-0.5">{{ e($photo->caption) }}</p>
                            @endif
                            {{-- Moderation actions --}}
                            <div class="mt-1.5 flex gap-1.5 flex-wrap">
                                @if (auth()->user()->canManageActivities())
                                    <form method="POST" action="{{ route('admin.photos.approve', $photo) }}">
                                        @csrf
                                        <button type="submit"
                                                class="inline-flex items-center gap-1 bg-green-800 hover:bg-green-700 text-white text-xs font-semibold px-2.5 py-1 rounded transition">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            Aprovar
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.photos.reject', $photo) }}">
                                        @csrf
                                        <button type="submit"
                                                class="inline-flex items-center gap-1 bg-orange-800 hover:bg-orange-700 text-white text-xs font-semibold px-2.5 py-1 rounded transition">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                            Recusar
                                        </button>
                                    </form>
                                @endif
                                @can('delete', $photo)
                                    <form method="POST" action="{{ route('activities.photos.destroy', [$activity, $photo]) }}"
                                          x-data
                                          @submit.prevent="if(confirm('Excluir esta foto?')) $el.submit()">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center gap-1 bg-red-900 hover:bg-red-800 text-white text-xs font-semibold px-2.5 py-1 rounded transition">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            Excluir
                                        </button>
                                    </form>
                                @endcan
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    </div>
@endsection
