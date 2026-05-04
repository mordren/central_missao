@extends('layouts.app')

@section('title', $activity->title . ' - ONÇAS DO OESTE')

@section('head')
    <meta property="og:title" content="{{ $activity->title }} - ONÇAS DO OESTE">
    <meta property="og:description" content="{{ \Illuminate\Support\Str::limit($activity->description ?? '', 200) }}">
    @if ($activity->banner)
        <meta property="og:image" content="{{ asset($activity->banner) }}">
        <meta name="twitter:card" content="summary_large_image">
    @else
        <meta name="twitter:card" content="summary">
    @endif
@endsection

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

            @if ($activity->banner)
                <div class="mt-4">
                    <img src="{{ public/asset($activity->banner) }}" alt="Banner" class="w-full h-56 object-cover rounded-lg border border-brand-dark-border">
                </div>
            @endif

            @if ($activity->description)
                <div class="text-brand-gray mt-3 whitespace-pre-wrap break-words">{{ $activity->description }}</div>
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

            {{-- Submissão para tarefas manuais --}}
            @if ($activity->type === 'tarefa_manual')
                <div class="mt-8 bg-brand-dark-input border border-brand-dark-border rounded-xl p-4 sm:p-5">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <h3 class="text-sm font-bold text-brand-yellow uppercase tracking-wider">Envio da Tarefa Manual</h3>
                        <span class="text-xs text-brand-gray">JPG, PDF, PNG, DOC ou TXT • até 10MB</span>
                    </div>

                    <p class="text-sm text-brand-gray mt-2">
                        Os pontos só são somados quando a coordenação/admin aprovar o envio.
                    </p>

                    @if (isset($userSubmission) && $userSubmission)
                        <div class="mt-4 p-3 rounded-lg border border-brand-dark-border bg-brand-dark">
                            <p class="text-sm text-white font-medium">Arquivo atual: {{ $userSubmission->original_name }}</p>
                            <p class="text-xs text-brand-gray mt-1">
                                Enviado em {{ $userSubmission->submitted_at?->format('d/m/Y H:i') ?? '—' }} •
                                Status:
                                @if ($userSubmission->status === 'approved')
                                    <span class="text-green-400 font-semibold">Aprovado (+{{ $userSubmission->points_awarded }} pts)</span>
                                @elseif ($userSubmission->status === 'rejected')
                                    <span class="text-red-400 font-semibold">Rejeitado</span>
                                @else
                                    <span class="text-yellow-300 font-semibold">Aguardando revisão</span>
                                @endif
                            </p>
                            <a href="{{ asset('storage/' . $userSubmission->file_path) }}" target="_blank" rel="noopener noreferrer"
                               class="inline-flex mt-2 text-xs text-brand-yellow hover:underline underline-offset-2">
                                Abrir arquivo enviado ↗
                            </a>
                        </div>
                    @endif

                    @if ($activity->isExpired())
                        <p class="text-sm text-red-400 mt-4">O prazo para envio desta tarefa foi encerrado.</p>
                    @elseif (!isset($userSubmission) || !$userSubmission || $userSubmission->status !== 'approved')
                        @php
                            $submissionUrl = Route::has('activities.submissions.store')
                                ? route('activities.submissions.store', $activity)
                                : url('activities/' . $activity->id . '/submissions');
                        @endphp
                        <form method="POST" action="{{ $submissionUrl }}" enctype="multipart/form-data" class="mt-4 space-y-3">
                            @csrf
                            <div>
                                <label for="submission_file" class="block text-xs font-semibold text-brand-gray uppercase tracking-wider mb-2">Arquivo da tarefa</label>
                                <input id="submission_file" name="submission_file" type="file"
                                       accept=".jpg,.jpeg,.pdf,.png,.doc,.docx,.txt" required
                                       class="block w-full text-sm text-brand-gray file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:bg-brand-yellow file:text-brand-dark file:font-semibold hover:file:bg-brand-yellow-hover">
                                @error('submission_file')
                                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="submission_comment" class="block text-xs font-semibold text-brand-gray uppercase tracking-wider mb-2">Comentário (opcional)</label>
                                <textarea id="submission_comment" name="comment" rows="2"
                                          class="w-full rounded-lg bg-brand-dark border border-brand-dark-border text-white placeholder-brand-gray text-sm px-3 py-2 focus:outline-none focus:ring-1 focus:ring-brand-yellow resize-none"
                                          placeholder="Observações sobre o envio...">{{ old('comment') }}</textarea>
                            </div>
                            <button type="submit"
                                    class="inline-flex items-center justify-center gap-2 w-full sm:w-auto bg-brand-yellow hover:bg-brand-yellow-hover text-brand-dark font-bold py-2.5 px-4 rounded-lg transition text-sm uppercase tracking-wider">
                                {{ isset($userSubmission) && $userSubmission ? 'Atualizar Arquivo' : 'Enviar Arquivo' }}
                            </button>
                        </form>
                    @else
                        <p class="text-xs text-brand-gray mt-4">Esta submissão já foi aprovada. Não é possível alterar após a pontuação.</p>
                    @endif
                </div>
            @endif
        </div>
    </div>
@endsection
