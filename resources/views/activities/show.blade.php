@extends('layouts.app')

@section('title', $activity->title . ' - Central da Missão')

@section('content')
    <div class="max-w-2xl mx-auto px-4 py-6 sm:py-8">
        <h1 class="text-lg sm:text-xl font-bold text-white tracking-tight uppercase mb-6">Detalhes da Atividade</h1>

        @php
            $dateFormatted   = $activity->date_time?->format('d/m/Y \à\s H\hi') ?? '';
            $deadlineFormatted = $activity->deadline?->format('d/m/Y \à\s H\hi') ?? '';
            $shareText = $activity->title . "\n\nTipo: " . $activity->typeLabel();
            if ($activity->date_time) {
                $shareText .= "\nData: " . $activity->date_time->format('d/m/Y') . " as " . $activity->date_time->format('H:i');
            }
            if ($activity->deadline) {
                $shareText .= "\nPrazo: " . $activity->deadline->format('d/m/Y') . " as " . $activity->deadline->format('H:i');
            }
            if ($activity->location) {
                $shareText .= "\nLocal: " . $activity->location;
            }
            $shareText .= "\nPontos: +" . $activity->points;
            if ($activity->description) {
                $shareText .= "\n\n" . $activity->description;
            }
            $shareText .= "\n\n" . route('activities.show', $activity);
            $whatsAppHref = 'https://wa.me/?text=' . urlencode($shareText);
        @endphp

        @if (session('success'))
            <div class="mb-6 bg-green-900/30 border border-green-800 text-green-400 px-4 py-3 rounded-lg text-sm">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="mb-6 bg-red-900/30 border border-red-800 text-red-400 px-4 py-3 rounded-lg text-sm">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-brand-dark-card border border-brand-dark-border rounded-2xl p-5 sm:p-8">
            {{-- Tipo badge --}}
            <div class="flex items-center justify-between flex-wrap gap-3">
                <span class="text-xs font-bold text-brand-yellow bg-brand-yellow/10 px-3 py-1 rounded-full uppercase tracking-wider">{{ $activity->typeLabel() }}</span>
                @if (auth()->user()->canManageActivities())
                    <div class="flex items-center gap-2">
                        <a href="{{ route('activities.qrcode', $activity) }}" class="inline-flex items-center gap-1.5 bg-brand-dark-input border border-brand-dark-border hover:border-brand-yellow text-white text-xs font-semibold px-3 py-2 rounded-lg transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                            QR Code
                        </a>
                        <a href="{{ route('activities.edit', $activity) }}" class="inline-flex items-center gap-1.5 bg-brand-dark-input border border-brand-dark-border hover:border-brand-yellow text-white text-xs font-semibold px-3 py-2 rounded-lg transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            Editar
                        </a>
                    </div>
                @endif
            </div>

            <h2 class="text-xl sm:text-2xl font-extrabold text-white mt-4 break-words">{{ $activity->title }}</h2>

            @if ($activity->description)
                <p class="text-brand-gray mt-3">{{ $activity->description }}</p>
            @endif

            {{-- Info cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-6">
                <div class="bg-brand-dark-input rounded-lg p-4 border border-brand-dark-border">
                    <p class="text-xs text-brand-gray uppercase tracking-wider mb-1">Data e Hora</p>
                    <p class="text-white font-semibold">{{ $dateFormatted }}</p>
                </div>
                <div class="bg-brand-dark-input rounded-lg p-4 border border-brand-dark-border">
                    <p class="text-xs text-brand-gray uppercase tracking-wider mb-1">Prazo</p>
                    <p class="text-white font-semibold {{ $activity->isExpired() ? 'text-red-400' : '' }}">
                        {{ $deadlineFormatted }}
                        @if ($activity->isExpired())
                            <span class="text-xs text-red-400">(encerrado)</span>
                        @endif
                    </p>
                </div>
                <div class="bg-brand-dark-input rounded-lg p-4 border border-brand-dark-border">
                    <p class="text-xs text-brand-gray uppercase tracking-wider mb-1">Pontuação</p>
                    <p class="text-brand-yellow font-extrabold text-xl">+{{ $activity->points }}pts</p>
                </div>
                @if ($activity->location)
                <div class="bg-brand-dark-input rounded-lg p-4 border border-brand-dark-border">
                    <p class="text-xs text-brand-gray uppercase tracking-wider mb-1">Local</p>
                    <p class="text-white font-semibold">{{ $activity->location }}</p>
                </div>
                @endif
            </div>

            {{-- Criador --}}
            <div class="mt-6 text-sm text-brand-gray">
                Criada por <span class="text-white font-medium">{{ $activity->creator->name }}</span>
            </div>

            {{-- Compartilhar --}}
            <div class="mt-4 flex flex-col sm:flex-row gap-2">
                {{-- Botão nativo (celular) --}}
                <button id="btn-share-native" onclick="shareNative()" class="hidden items-center justify-center gap-2 w-full sm:w-auto bg-brand-yellow hover:bg-brand-yellow-hover text-brand-dark font-bold py-2.5 px-5 rounded-lg transition text-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
                    Compartilhar
                </button>

                {{-- Botão WhatsApp (fallback desktop) --}}
                <a id="btn-share-wa" href="{{ $whatsAppHref }}" target="_blank" rel="noopener noreferrer"
                    class="inline-flex items-center justify-center gap-2 w-full sm:w-auto bg-green-600 hover:bg-green-700 text-white font-bold py-2.5 px-5 rounded-lg transition text-sm">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    WhatsApp
                </a>
            </div>

            <script>
                // Se o navegador suporta Web Share API (celular), mostra nativo e esconde WhatsApp
                if (navigator.share) {
                    document.getElementById('btn-share-native').classList.remove('hidden');
                    document.getElementById('btn-share-native').classList.add('inline-flex');
                    document.getElementById('btn-share-wa').classList.add('hidden');
                }

                function shareNative() {
                    navigator.share({
                        title: @json($activity->title),
                        text: @json($shareText),
                        url: @json(route('activities.show', $activity))
                    }).catch(function() {});
                }
            </script>

            {{-- Presenças --}}
            <div class="mt-8">
                <h3 class="text-sm font-bold text-brand-yellow uppercase tracking-wider mb-4">
                    Presenças Registradas ({{ $activity->confirmedParticipants->count() }})
                </h3>
                @forelse ($activity->confirmedParticipants as $participant)
                    <div class="flex items-center gap-3 mb-3 last:mb-0 p-3 bg-brand-dark-input rounded-lg border border-brand-dark-border">
                        <div class="w-8 h-8 bg-brand-yellow/20 rounded-full flex items-center justify-center">
                            <span class="text-sm font-bold text-brand-yellow">{{ strtoupper(substr($participant->name, 0, 1)) }}</span>
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm text-white font-medium truncate">{{ $participant->name }}</p>
                            <p class="text-xs text-brand-gray">{{ $participant->pivot->confirmed_at ? \Carbon\Carbon::parse($participant->pivot->confirmed_at)->format('d/m \à\s H\hi') : '' }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-brand-gray text-center py-4">Nenhuma presença registrada ainda.</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection
