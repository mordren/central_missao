@extends('layouts.app')

@section('title', 'Editar Missão - ONÇAS DO OESTE')

@section('content')
    <div class="max-w-2xl mx-auto px-4 py-6 sm:py-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-lg sm:text-xl font-bold text-white tracking-tight uppercase">Editar Missão</h1>
            <a href="{{ route('activities.show', $activity) }}" class="text-brand-gray hover:text-brand-yellow transition text-sm">&larr; Voltar</a>
        </div>

        <div class="bg-brand-dark-card border border-brand-dark-border rounded-2xl p-4 sm:p-8">
            @if ($errors->any())
                <div class="mb-6 bg-red-900/30 border border-red-800 text-red-400 px-4 py-3 rounded-lg text-sm">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('activities.update', $activity) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- Título --}}
                <div class="mb-5">
                    <label for="title" class="block text-sm font-semibold text-brand-gray mb-2 uppercase tracking-wider">Título da Missão</label>
                    <input type="text" id="title" name="title" value="{{ old('title', $activity->title) }}" required
                        class="block w-full px-4 py-3 bg-brand-dark-input border border-brand-dark-border rounded-lg text-white placeholder-brand-gray/60 focus:outline-none focus:ring-2 focus:ring-brand-yellow focus:border-brand-yellow transition @error('title') border-red-500 @enderror">
                    @error('title')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                    {{-- Banner atual e upload --}}
                    <div class="mb-5">
                        @if ($activity->banner)
                            <div class="mb-3">
                                <img src="{{ url($activity->banner) }}" alt="Banner" class="w-full h-48 object-cover rounded-lg border border-brand-dark-border">
                            </div>
                        @endif
                        <label class="block text-sm font-semibold text-brand-gray mb-2 uppercase tracking-wider">Alterar Banner <span class="text-brand-gray/50 normal-case">(opcional)</span></label>

                        {{-- Drop Zone --}}
                        <div id="dz-banner"
                             data-dz-target="banner"
                             tabindex="0"
                             role="button"
                             aria-label="Área de upload de imagem"
                             onclick="document.getElementById('banner').click()"
                             ondragover="dzDragOver(event,this)"
                             ondragleave="dzDragLeave(event,this)"
                             ondrop="dzDrop(event,this,'banner')"
                             class="border-2 border-dashed border-brand-dark-border rounded-xl p-6 text-center cursor-pointer transition hover:border-brand-yellow/60 hover:bg-brand-yellow/5 focus:outline-none focus:border-brand-yellow focus:bg-brand-yellow/5">

                            {{-- Hint --}}
                            <div id="dz-hint-banner">
                                <svg class="w-8 h-8 text-brand-gray/50 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <p class="text-sm text-brand-gray">Arraste a imagem aqui ou <span class="text-brand-yellow font-semibold">clique para selecionar</span></p>
                                <p class="text-xs text-brand-gray/50 mt-1">Clique aqui e cole com <kbd class="px-1 py-0.5 rounded bg-brand-dark-input border border-brand-dark-border text-brand-gray/70 font-mono text-[10px]">Ctrl+V</kbd></p>
                            </div>

                            {{-- Preview --}}
                            <div id="dz-preview-banner" class="hidden">
                                <img id="dz-img-banner" src="" alt="Preview" class="mx-auto max-h-40 rounded-lg border border-brand-dark-border object-contain">
                                <p id="dz-name-banner" class="text-xs text-brand-gray mt-2 truncate"></p>
                                <button type="button" onclick="dzClear(event,'banner')" class="mt-2 text-xs text-red-400 hover:text-red-300 transition">Remover</button>
                            </div>
                        </div>

                        <input id="banner" name="banner" type="file" accept="image/*" class="hidden"
                               onchange="dzFileSelected(this,'banner')">
                        @error('banner')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                {{-- Descrição --}}
                <div class="mb-5">
                    <label for="description" class="block text-sm font-semibold text-brand-gray mb-2 uppercase tracking-wider">Descrição <span class="text-brand-gray/50 normal-case">(opcional)</span></label>
                    <textarea id="description" name="description" rows="6"
                        class="block w-full px-4 py-3 bg-brand-dark-input border border-brand-dark-border rounded-lg text-white placeholder-brand-gray/60 focus:outline-none focus:ring-2 focus:ring-brand-yellow focus:border-brand-yellow transition resize-y min-h-[6rem] md:min-h-[8rem] lg:min-h-[10rem] leading-relaxed text-sm @error('description') border-red-500 @enderror">{{ old('description', $activity->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Tipo --}}
                <div class="mb-5">
                    <label for="type" class="block text-sm font-semibold text-brand-gray mb-2 uppercase tracking-wider">Tipo de Missão</label>
                    <select id="type" name="type" required
                        class="block w-full px-4 py-3 bg-brand-dark-input border border-brand-dark-border rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-brand-yellow focus:border-brand-yellow transition @error('type') border-red-500 @enderror">
                        <option value="evento_presencial" {{ old('type', $activity->type) === 'evento_presencial' ? 'selected' : '' }}>Evento Presencial</option>
                        <option value="denuncia" {{ old('type', $activity->type) === 'denuncia' ? 'selected' : '' }}>Denúncia</option>
                        <option value="tarefa_manual" {{ old('type', $activity->type) === 'tarefa_manual' ? 'selected' : '' }}>Tarefa Manual</option>
                        <option value="convite" {{ old('type', $activity->type) === 'convite' ? 'selected' : '' }}>Convite/Indicação</option>
                    </select>
                    @error('type')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Data/Hora --}}
                <div class="mb-5">
                    <label for="date_time" class="block text-sm font-semibold text-brand-gray mb-2 uppercase tracking-wider">Data e hora</label>
                    <input type="datetime-local" id="date_time" name="date_time" value="{{ old('date_time', $activity->date_time->format('Y-m-d\TH:i')) }}" required
                        class="block w-full px-4 py-3 bg-brand-dark-input border border-brand-dark-border rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-brand-yellow focus:border-brand-yellow transition @error('date_time') border-red-500 @enderror">
                    @error('date_time')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Local --}}
                <div class="mb-5">
                    <label for="location" class="block text-sm font-semibold text-brand-gray mb-2 uppercase tracking-wider">Local <span class="text-brand-gray/50 normal-case">(opcional)</span></label>
                    <input type="text" id="location" name="location" value="{{ old('location', $activity->location) }}"
                        class="block w-full px-4 py-3 bg-brand-dark-input border border-brand-dark-border rounded-lg text-white placeholder-brand-gray/60 focus:outline-none focus:ring-2 focus:ring-brand-yellow focus:border-brand-yellow transition @error('location') border-red-500 @enderror">
                    @error('location')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Pontuação --}}
                <div class="mb-5">
                    <label for="points" class="block text-sm font-semibold text-brand-gray mb-2 uppercase tracking-wider">Pontuação</label>
                    <input type="number" id="points" name="points" value="{{ old('points', $activity->points) }}" min="0" required
                        class="block w-full px-4 py-3 bg-brand-dark-input border border-brand-dark-border rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-brand-yellow focus:border-brand-yellow transition @error('points') border-red-500 @enderror">
                    @error('points')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Botão --}}
                <button type="submit" class="w-full bg-brand-yellow hover:bg-brand-yellow-hover text-brand-dark font-bold py-3.5 px-4 rounded-lg transition duration-200 focus:outline-none focus:ring-2 focus:ring-brand-yellow focus:ring-offset-2 focus:ring-offset-brand-dark uppercase tracking-wider text-sm">
                    Salvar Alterações
                </button>
            </form>
        </div>
    </div>
@endsection
