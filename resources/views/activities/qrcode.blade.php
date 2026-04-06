@extends('layouts.app')

@section('title', 'QR Code - ' . $activity->title)

@section('content')
    <div class="max-w-2xl mx-auto px-4 py-6 sm:py-8">
        <div class="flex items-center justify-between gap-3 flex-wrap mb-6">
            <h1 class="text-lg sm:text-xl font-bold text-white tracking-tight uppercase">QR Code da Atividade</h1>
            <a href="{{ route('activities.show', $activity) }}" class="text-brand-gray hover:text-brand-yellow transition text-sm">← Voltar</a>
        </div>

        <div class="bg-brand-dark-card border border-brand-dark-border rounded-2xl p-5 sm:p-8 text-center">
            {{-- Título --}}
            <span class="text-xs font-bold text-brand-yellow bg-brand-yellow/10 px-3 py-1 rounded-full uppercase tracking-wider">{{ $activity->typeLabel() }}</span>
            <h2 class="text-xl font-extrabold text-white mt-3">{{ $activity->title }}</h2>
            <p class="text-brand-gray text-sm mt-1">{{ $activity->date_time->format('d/m/Y \à\s H\hi') }} • +{{ $activity->points }}pts</p>

            {{-- QR Code --}}
            <div class="mt-8 mb-6 flex justify-center">
                <div id="qrcode" class="bg-white p-3 sm:p-4 rounded-2xl inline-block"></div>
            </div>

            <p class="text-brand-gray text-sm mb-2">Peça para os participantes lerem este QR Code</p>
            <p class="text-brand-gray text-xs">A presença e os pontos serão registrados automaticamente.</p>

            @if ($activity->isExpired())
                <div class="mt-6 bg-red-900/30 border border-red-800 text-red-400 px-4 py-3 rounded-lg text-sm">
                    Esta atividade já foi encerrada. O QR Code não confirmará novas presenças.
                </div>
            @endif

            {{-- Presenças --}}
            <div class="mt-8 text-left">
                <h3 class="text-sm font-bold text-brand-yellow uppercase tracking-wider mb-4">
                    Presenças Registradas ({{ $activity->confirmedParticipants->count() }})
                </h3>
                <div class="space-y-2 max-h-60 overflow-y-auto">
                    @forelse ($activity->confirmedParticipants as $participant)
                        <div class="flex items-center gap-3 p-3 bg-brand-dark-input rounded-lg border border-brand-dark-border">
                            <div class="w-8 h-8 bg-green-900/30 rounded-full flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <div class="flex-1 min-w-0">
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
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const url = @json(route('activities.confirmPresence', ['activity' => $activity->id, 'token' => $activity->qr_code]));
            const safePadding = 24;
            const available = Math.max(180, window.innerWidth - safePadding * 2);
            const size = Math.min(280, available);
            new QRCode(document.getElementById('qrcode'), {
                text: url,
                width: size,
                height: size,
                colorDark: '#000000',
                colorLight: '#ffffff',
                correctLevel: QRCode.CorrectLevel.H
            });
        });
    </script>
@endsection
