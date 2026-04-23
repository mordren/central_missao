@extends('layouts.app')

@section('title', 'Submissão #' . $submission->id)

@section('content')
<div class="max-w-2xl mx-auto px-4 py-6 sm:py-8">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.activity_submissions.index') }}" class="text-brand-gray hover:text-white transition text-sm">← Voltar</a>
        <span class="text-brand-gray">/</span>
        <h1 class="text-lg font-bold text-white tracking-tight uppercase">Submissão #{{ $submission->id }}</h1>
    </div>

    @if(session('success'))
        <div class="mb-4 bg-green-900/30 border border-green-800 text-green-400 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 bg-red-900/30 border border-red-800 text-red-400 px-4 py-3 rounded-lg text-sm">{{ session('error') }}</div>
    @endif

    <div class="bg-brand-dark-card border border-brand-dark-border rounded-2xl p-5 sm:p-7 space-y-5">

        {{-- Status badge --}}
        <div class="flex items-center justify-between flex-wrap gap-2">
            <span class="text-xs text-brand-gray uppercase tracking-wider font-semibold">Status da Submissão</span>
            @if($submission->status === 'pending')
                <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold bg-yellow-500/20 text-yellow-300 border border-yellow-500/30">Aguardando revisão</span>
            @elseif($submission->status === 'approved')
                <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold bg-green-500/20 text-green-400 border border-green-500/30">Aprovada</span>
            @else
                <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold bg-red-500/20 text-red-400 border border-red-500/30">Recusada</span>
            @endif
        </div>

        <hr class="border-brand-dark-border">

        {{-- Missão --}}
        <div>
            <p class="text-xs text-brand-gray uppercase tracking-wider mb-1">Missão</p>
            <p class="text-white font-semibold">{{ $submission->activity->title }}</p>
            <p class="text-xs text-brand-yellow mt-0.5">Pontuação padrão: +{{ $submission->activity->points }} pts</p>
        </div>

        {{-- Usuário --}}
        <div>
            <p class="text-xs text-brand-gray uppercase tracking-wider mb-1">Participante</p>
            <p class="text-white font-semibold">{{ $submission->user->name }}</p>
            <p class="text-xs text-brand-gray">{{ $submission->user->email }}</p>
        </div>

        {{-- Arquivo enviado --}}
        <div>
            <p class="text-xs text-brand-gray uppercase tracking-wider mb-2">Arquivo enviado</p>
            <div class="flex items-center gap-3 p-3 bg-brand-dark-input rounded-lg border border-brand-dark-border">
                <div class="flex-1 min-w-0">
                    <p class="text-sm text-white font-medium truncate">{{ $submission->original_name }}</p>
                    <p class="text-xs text-brand-gray mt-0.5">
                        {{ $submission->mime_type }} •
                        {{ $submission->file_size ? number_format($submission->file_size / 1024, 1) . ' KB' : '' }} •
                        {{ optional($submission->submitted_at)->format('d/m/Y \à\s H:i') }}
                    </p>
                </div>
                <a href="{{ asset('storage/' . $submission->file_path) }}" target="_blank" rel="noopener noreferrer"
                   class="flex-shrink-0 inline-flex items-center gap-1.5 px-3 py-2 bg-brand-yellow hover:bg-brand-yellow-hover text-brand-dark text-xs font-bold rounded-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Abrir
                </a>
            </div>
        </div>

        {{-- Comentário do participante --}}
        @if($submission->comment)
            <div>
                <p class="text-xs text-brand-gray uppercase tracking-wider mb-1">Comentário do participante</p>
                <p class="text-sm text-white bg-brand-dark-input border border-brand-dark-border rounded-lg p-3 whitespace-pre-line">{{ $submission->comment }}</p>
            </div>
        @endif

        {{-- Se já revisado --}}
        @if($submission->reviewed_at)
            <hr class="border-brand-dark-border">
            <div>
                <p class="text-xs text-brand-gray uppercase tracking-wider mb-1">Revisado por</p>
                <p class="text-white text-sm">{{ $submission->reviewer?->name ?? '—' }}</p>
                <p class="text-xs text-brand-gray">{{ optional($submission->reviewed_at)->format('d/m/Y H:i') }}</p>
            </div>
            @if($submission->points_awarded)
                <div class="p-3 bg-green-900/20 border border-green-800/40 rounded-lg">
                    <p class="text-sm text-green-400 font-semibold">+{{ $submission->points_awarded }} pts atribuídos ao participante.</p>
                </div>
            @endif
            @if($submission->reviewer_comment)
                <div class="p-3 bg-red-900/20 border border-red-800/40 rounded-lg">
                    <p class="text-xs text-brand-gray uppercase tracking-wider mb-1">Motivo da recusa</p>
                    <p class="text-sm text-red-300 whitespace-pre-line">{{ $submission->reviewer_comment }}</p>
                </div>
            @endif
        @endif

        {{-- Ações --}}
        @if($submission->status === 'pending')
            <hr class="border-brand-dark-border">

            {{-- Aprovação --}}
            <div class="bg-green-900/10 border border-green-800/30 rounded-xl p-4 space-y-3">
                <p class="text-xs text-green-400 font-bold uppercase tracking-wider">Aprovar Submissão</p>
                <form action="{{ route('admin.activity_submissions.approve', $submission) }}" method="POST" class="space-y-3">
                    @csrf
                    <div>
                        <label for="points_to_award" class="block text-xs text-brand-gray font-semibold uppercase tracking-wider mb-1">Pontos a conceder</label>
                        <input type="number" id="points_to_award" name="points_to_award"
                               value="{{ $submission->activity->points }}" min="0" max="9999"
                               class="w-full sm:w-32 rounded-lg bg-brand-dark border border-brand-dark-border text-white text-sm px-3 py-2 focus:outline-none focus:ring-1 focus:ring-green-500">
                        <p class="text-xs text-brand-gray mt-1">Padrão da Missão: {{ $submission->activity->points }} pts</p>
                    </div>
                    <button type="submit"
                            class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-green-700 hover:bg-green-600 text-white font-bold rounded-xl transition text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Aprovar e Conceder Pontos
                    </button>
                </form>
            </div>

            {{-- Recusa --}}
            <div class="bg-red-900/10 border border-red-800/30 rounded-xl p-4 space-y-3">
                <p class="text-xs text-red-400 font-bold uppercase tracking-wider">Recusar Submissão</p>
                <form action="{{ route('admin.activity_submissions.reject', $submission) }}" method="POST" class="space-y-3">
                    @csrf
                    <div>
                        <label for="reviewer_comment" class="block text-xs text-brand-gray font-semibold uppercase tracking-wider mb-1">Motivo da recusa (opcional)</label>
                        <textarea id="reviewer_comment" name="reviewer_comment" rows="3"
                                  class="w-full rounded-lg bg-brand-dark border border-brand-dark-border text-white placeholder-brand-gray text-sm px-3 py-2 focus:outline-none focus:ring-1 focus:ring-red-500 resize-none"
                                  placeholder="Explique o motivo para o participante..."></textarea>
                    </div>
                    <button type="submit"
                            class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-red-800 hover:bg-red-700 text-white font-bold rounded-xl transition text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        Recusar Submissão
                    </button>
                </form>
            </div>
        @else
            <a href="{{ route('admin.activity_submissions.index') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-brand-dark-input border border-brand-dark-border hover:border-brand-yellow text-white text-sm font-semibold rounded-lg transition">
                ← Voltar à lista
            </a>
        @endif
    </div>
</div>
@endsection
