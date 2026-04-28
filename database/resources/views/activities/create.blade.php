@extends('layouts.app')

@section('title', 'Criar Atividade - ONÇAS DO OESTE')

@section('content')
    <div class="max-w-2xl mx-auto px-4 py-6 sm:py-8">
        <h1 class="text-lg sm:text-xl font-bold text-white tracking-tight uppercase mb-6">Criar Atividade</h1>
        <div class="bg-brand-dark-card border border-brand-dark-border rounded-2xl p-4 sm:p-8">
            @if ($errors->any())
                <div class="mb-6 bg-red-900/30 border border-red-800 text-red-400 px-4 py-3 rounded-lg text-sm">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('activities.store') }}" enctype="multipart/form-data">
                @csrf

                {{-- Título --}}
                <div class="mb-5">
                    <label for="title" class="block text-sm font-semibold text-brand-gray mb-2 uppercase tracking-wider">Título da atividade</label>
                    <input type="text" id="title" name="title" value="{{ old('title') }}" placeholder="Ex: Panfletagem Centro" required
                        class="block w-full px-4 py-3 bg-brand-dark-input border border-brand-dark-border rounded-lg text-white placeholder-brand-gray/60 focus:outline-none focus:ring-2 focus:ring-brand-yellow focus:border-brand-yellow transition @error('title') border-red-500 @enderror">
                    @error('title')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Descrição --}}
                <div class="mb-5">
                    <label for="description" class="block text-sm font-semibold text-brand-gray mb-2 uppercase tracking-wider">Descrição <span class="text-brand-gray/50 normal-case">(opcional)</span></label>
                    <textarea id="description" name="description" rows="3" placeholder="Descreva a atividade..."
                        class="block w-full px-4 py-3 bg-brand-dark-input border border-brand-dark-border rounded-lg text-white placeholder-brand-gray/60 focus:outline-none focus:ring-2 focus:ring-brand-yellow focus:border-brand-yellow transition resize-none @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                    {{-- Banner (imagem) --}}
                    <div class="mb-5">
                        <label for="banner" class="block text-sm font-semibold text-brand-gray mb-2 uppercase tracking-wider">Banner <span class="text-brand-gray/50 normal-case">(opcional)</span></label>
                        <input id="banner" name="banner" type="file" accept="image/*"
                            class="block w-full text-sm text-brand-gray file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:bg-brand-yellow file:text-brand-dark file:font-semibold hover:file:bg-brand-yellow-hover">
                        @error('banner')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                {{-- Tipo --}}
                <div class="mb-5">
                    <label for="type" class="block text-sm font-semibold text-brand-gray mb-2 uppercase tracking-wider">Tipo de atividade</label>
                    <select id="type" name="type" required
                        class="block w-full px-4 py-3 bg-brand-dark-input border border-brand-dark-border rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-brand-yellow focus:border-brand-yellow transition @error('type') border-red-500 @enderror">
                        <option value="">Selecione...</option>
                        <option value="evento_presencial" {{ old('type') === 'evento_presencial' ? 'selected' : '' }}>Evento Presencial</option>
                        <option value="denuncia" {{ old('type') === 'denuncia' ? 'selected' : '' }}>Denúncia</option>
                        <option value="tarefa_manual" {{ old('type') === 'tarefa_manual' ? 'selected' : '' }}>Tarefa Manual</option>
                        <option value="convite" {{ old('type') === 'convite' ? 'selected' : '' }}>Convite/Indicação</option>
                    </select>
                    @error('type')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Data/Hora e Prazo lado a lado --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
                    <div>
                        <label for="date_time" class="block text-sm font-semibold text-brand-gray mb-2 uppercase tracking-wider">Data e hora</label>
                        <input type="datetime-local" id="date_time" name="date_time" value="{{ old('date_time') }}" required
                            class="block w-full px-4 py-3 bg-brand-dark-input border border-brand-dark-border rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-brand-yellow focus:border-brand-yellow transition @error('date_time') border-red-500 @enderror">
                        @error('date_time')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="deadline" class="block text-sm font-semibold text-brand-gray mb-2 uppercase tracking-wider">Prazo</label>
                        <input type="datetime-local" id="deadline" name="deadline" value="{{ old('deadline') }}" required
                            class="block w-full px-4 py-3 bg-brand-dark-input border border-brand-dark-border rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-brand-yellow focus:border-brand-yellow transition @error('deadline') border-red-500 @enderror">
                        @error('deadline')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Local --}}
                <div class="mb-5">
                    <label for="location" class="block text-sm font-semibold text-brand-gray mb-2 uppercase tracking-wider">Local <span class="text-brand-gray/50 normal-case">(opcional)</span></label>
                    <input type="text" id="location" name="location" value="{{ old('location') }}" placeholder="Ex: Praça Central, Centro"
                        class="block w-full px-4 py-3 bg-brand-dark-input border border-brand-dark-border rounded-lg text-white placeholder-brand-gray/60 focus:outline-none focus:ring-2 focus:ring-brand-yellow focus:border-brand-yellow transition @error('location') border-red-500 @enderror">
                    @error('location')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Pontuação --}}
                <div class="mb-8">
                    <label for="points" class="block text-sm font-semibold text-brand-gray mb-2 uppercase tracking-wider">Pontuação</label>
                    <input type="number" id="points" name="points" value="{{ old('points', 10) }}" min="1" required
                        class="block w-full px-4 py-3 bg-brand-dark-input border border-brand-dark-border rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-brand-yellow focus:border-brand-yellow transition @error('points') border-red-500 @enderror">
                    @error('points')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Botão --}}
                <button type="submit" class="w-full bg-brand-yellow hover:bg-brand-yellow-hover text-brand-dark font-bold py-3.5 px-4 rounded-lg transition duration-200 focus:outline-none focus:ring-2 focus:ring-brand-yellow focus:ring-offset-2 focus:ring-offset-brand-dark uppercase tracking-wider text-sm">
                    Criar Atividade
                </button>
            </form>
        </div>
    </div>
@endsection
