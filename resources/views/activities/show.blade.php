@extends('layouts.app')

@section('title', $activity->title . ' - ONÇAS DO OESTE')

@section('head')
    <meta property="og:title" content="{{ $activity->title }} - ONÇAS DO OESTE">
    <meta property="og:description" content="{{ \Illuminate\Support\Str::limit($activity->description ?? '', 200) }}">
    @if ($activity->banner)
        <meta property="og:image" content="{{ url($activity->banner) }}">
        <meta name="twitter:card" content="summary_large_image">
    @else
        <meta name="twitter:card" content="summary">
    @endif
    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
@endsection

@section('content')
    <div class="max-w-2xl mx-auto px-4 py-6 sm:py-8">
        <h1 class="text-lg sm:text-xl font-bold text-white tracking-tight uppercase mb-6">Detalhes da Missão</h1>

        @php
            $dateFormatted   = $activity->date_time?->format('d/m/Y \à\s H\hi') ?? '';
            $shareText = $activity->title . "\n\nTipo: " . $activity->typeLabel();
            if ($activity->date_time) {
                $shareText .= "\nData: " . $activity->date_time->format('d/m/Y') . " as " . $activity->date_time->format('H:i');
            }
            if ($activity->location) {
                $shareText .= "\nLocal: " . $activity->location;
            }
            $shareText .= "\nPontos: +" . $activity->points;
            if ($activity->description) {
                $shareText .= "\n\n" . $activity->description;
            }
            $shareText .= "\n\n" . route('activities.share', $activity);
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
            <div class="flex flex-col gap-3">
                <div class="flex items-center justify-between flex-wrap gap-2">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-xs font-bold text-brand-yellow bg-brand-yellow/10 px-3 py-1 rounded-full uppercase tracking-wider">{{ $activity->typeLabel() }}</span>
                        @if ($activity->status === 'completed')
                            <span class="text-xs font-bold text-green-400 bg-green-900/30 border border-green-800 px-3 py-1 rounded-full uppercase tracking-wider">Concluída</span>
                        @elseif ($activity->status === 'cancelled')
                            <span class="text-xs font-bold text-red-400 bg-red-900/30 border border-red-800 px-3 py-1 rounded-full uppercase tracking-wider">Cancelada</span>
                        @elseif ($activity->skip_points)
                            <span class="text-xs font-bold text-blue-400 bg-blue-900/30 border border-blue-800 px-3 py-1 rounded-full uppercase tracking-wider">Missão do Passado</span>
                        @endif
                    </div>
                    <div class="flex items-center gap-2 flex-wrap">
                        {{-- QR Code (apenas coord/admin) --}}
                        @if (auth()->user()->canManageActivities())
                            <a href="{{ route('activities.qrcode', $activity) }}" class="inline-flex items-center gap-1.5 bg-brand-dark-input border border-brand-dark-border hover:border-brand-yellow text-white text-xs font-semibold px-3 py-2 rounded-lg transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                                QR Code
                            </a>
                        @endif
                        @if (auth()->user()->canManageActivities())
                            <a href="{{ route('activities.edit', $activity) }}" class="inline-flex items-center gap-1.5 bg-brand-dark-input border border-brand-dark-border hover:border-brand-yellow text-white text-xs font-semibold px-3 py-2 rounded-lg transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                Editar
                            </a>
                        @endif
                    </div>
                </div>
                {{-- Bouton Excluir numa linha separada (menos destaque, só admin) --}}
                @if (auth()->user()->isAdmin())
                    <div class="flex justify-end">
                        <form method="POST" action="{{ route('activities.destroy', $activity) }}" style="display:inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    onclick="return confirm('Excluir permanentemente a missão \'{{ addslashes($activity->title) }}\'? Esta acção não pode ser desfeita.')"
                                    class="inline-flex items-center gap-1.5 text-red-600 hover:text-red-400 text-xs font-semibold border-b border-dashed border-red-800 hover:border-red-500 transition pb-0.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                Excluir missão
                            </button>
                        </form>
                    </div>
                @endif
            </div>

            <h2 class="text-xl sm:text-2xl font-extrabold text-white mt-4 break-words">{{ $activity->title }}</h2>

            @if ($activity->banner)
                <div class="mt-4">
                    <img src="{{ url($activity->banner) }}" alt="Banner" class="w-full h-56 object-cover rounded-lg border border-brand-dark-border">
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
                {{-- Prazo removido da interface (usado internamente para eventos presenciais) --}}
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
                <div class="bg-brand-dark-input rounded-lg p-4 border border-brand-dark-border">
                    <p class="text-xs text-brand-gray uppercase tracking-wider mb-1">Inscritos</p>
                    <p class="text-white font-semibold">{{ $rsvpCount }} inscrito{{ $rsvpCount === 1 ? '' : 's' }}</p>
                </div>
            </div>

            {{-- Botão listagem de inscritos — apenas coordenadores e admins --}}
            @if (auth()->user()->canManageActivities())
                <div class="mt-4">
                    <button onclick="document.getElementById('rsvp-list').classList.toggle('hidden')"
                            class="inline-flex items-center gap-2 bg-brand-dark-input border border-brand-dark-border hover:border-brand-yellow text-white text-sm font-semibold px-4 py-2.5 rounded-lg transition w-full sm:w-auto justify-center">
                        <svg class="w-4 h-4 text-brand-yellow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Ver inscritos ({{ $rsvpCount }})
                    </button>
                </div>
            @endif

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
                    }).catch(function() {});
                }
            </script>

            {{-- RSVP — only for participante users --}}
            @if (auth()->user()->role === 'participante')
                <div class="mt-6">
                    @if ($userRsvp)
                        <div class="flex items-center justify-between gap-3 bg-green-900/20 border border-green-800 px-4 py-3 rounded-lg">
                            <div class="flex items-center gap-2 text-sm font-semibold text-green-400">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Você está inscrito — apareça para ganhar pontos em dobro!
                            </div>
                            <form method="POST" action="{{ route('activities.rsvp.cancel', $activity) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        onclick="return confirm('Cancelar sua inscrição nesta missão?')"
                                        class="text-xs text-red-400 hover:text-red-300 font-semibold whitespace-nowrap transition">
                                    Cancelar inscrição
                                </button>
                            </form>
                        </div>
                    @elseif (!$activity->isExpired())
                        <form method="POST" action="{{ route('activities.rsvp', $activity) }}">
                            @csrf
                            <button type="submit"
                                    class="inline-flex items-center justify-center gap-2 w-full sm:w-auto bg-brand-yellow hover:bg-brand-yellow-hover text-brand-dark font-bold py-2.5 px-5 rounded-lg transition text-sm uppercase tracking-wider">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Confirmar Presença
                            </button>
                        </form>
                    @else
                        <p class="text-xs text-brand-gray italic">Inscrições encerradas para esta missão.</p>
                    @endif
                </div>
            @endif

            {{-- Presenças --}}
            <div class="mt-8">                <h3 class="text-sm font-bold text-brand-yellow uppercase tracking-wider mb-4">
                    Presenças Registradas ({{ $confirmedParticipants->count() }})
                </h3>
                @forelse ($confirmedParticipants as $participant)
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
                            <p class="text-sm text-white font-medium">Arquivo enviado: <span class="text-brand-yellow">{{ $userSubmission->original_name }}</span></p>
                            <p class="text-xs text-brand-gray mt-1">
                                Enviado em {{ $userSubmission->submitted_at?->format('d/m/Y H:i') ?? '—' }} •
                                Status:
                                @if ($userSubmission->status === 'approved')
                                    <span class="text-green-400 font-semibold">Aprovado (+{{ $userSubmission->points_awarded }} pts)</span>
                                @elseif ($userSubmission->status === 'rejected')
                                    <span class="text-red-400 font-semibold">Recusado</span>
                                @else
                                    <span class="text-yellow-300 font-semibold">Aguardando revisão</span>
                                @endif
                            </p>
                            @if ($userSubmission->status === 'rejected' && $userSubmission->reviewer_comment)
                                <p class="mt-2 text-xs text-red-300 bg-red-900/30 border border-red-800 rounded p-2">
                                    <span class="font-semibold">Motivo da recusa:</span> {{ $userSubmission->reviewer_comment }}
                                </p>
                            @endif
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
                            $submissionUrl = route('activities.submissions.store', $activity);
                            $hasExisting = isset($userSubmission) && $userSubmission;
                            $isRejected = $hasExisting && $userSubmission->status === 'rejected';
                        @endphp
                        @if ($hasExisting)
                            <p class="mt-3 text-xs text-brand-gray">
                                {{ $isRejected ? 'Você pode enviar um novo arquivo para substituir o recusado.' : 'Você pode substituir o arquivo enquanto aguarda revisão.' }}
                            </p>
                        @endif
                        <form method="POST" action="{{ $submissionUrl }}" enctype="multipart/form-data" class="mt-3 space-y-3">
                            @csrf
                            <div>
                                <label for="submission_file" class="block text-xs font-semibold text-brand-gray uppercase tracking-wider mb-2">{{ $hasExisting ? 'Novo arquivo' : 'Arquivo da tarefa' }}</label>

                                {{-- Drop Zone --}}
                                <div id="dz-submission_file"
                                     data-dz-target="submission_file"
                                     tabindex="0"
                                     role="button"
                                     aria-label="Área de upload de arquivo"
                                     onclick="document.getElementById('submission_file').click()"
                                     ondragover="dzDragOver(event,this)"
                                     ondragleave="dzDragLeave(event,this)"
                                     ondrop="dzDrop(event,this,'submission_file')"
                                     class="border-2 border-dashed border-brand-dark-border rounded-xl p-5 text-center cursor-pointer transition hover:border-brand-yellow/60 hover:bg-brand-yellow/5 focus:outline-none focus:border-brand-yellow focus:bg-brand-yellow/5">

                                    {{-- Hint --}}
                                    <div id="dz-hint-submission_file">
                                        <svg class="w-7 h-7 text-brand-gray/50 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                        </svg>
                                        <p class="text-xs text-brand-gray">Arraste o arquivo aqui ou <span class="text-brand-yellow font-semibold">clique para selecionar</span></p>
                                        <p class="text-xs text-brand-gray/50 mt-1">Imagens: cole com <kbd class="px-1 py-0.5 rounded bg-brand-dark-input border border-brand-dark-border text-brand-gray/70 font-mono text-[10px]">Ctrl+V</kbd></p>
                                        <p class="text-xs text-brand-gray/40 mt-1">JPG, PNG, PDF, DOC, TXT</p>
                                    </div>

                                    {{-- Preview --}}
                                    <div id="dz-preview-submission_file" class="hidden">
                                        <img id="dz-img-submission_file" src="" alt="Preview" class="mx-auto max-h-32 rounded-lg border border-brand-dark-border object-contain">
                                        <div id="dz-fileicon-submission_file" class="hidden mx-auto w-12 h-12 flex items-center justify-center rounded-lg bg-brand-dark-input border border-brand-dark-border">
                                            <svg class="w-6 h-6 text-brand-gray" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                        </div>
                                        <p id="dz-name-submission_file" class="text-xs text-brand-gray mt-2 truncate"></p>
                                        <button type="button" onclick="dzClear(event,'submission_file')" class="mt-1 text-xs text-red-400 hover:text-red-300 transition">Remover</button>
                                    </div>
                                </div>

                                <input id="submission_file" name="submission_file" type="file"
                                       accept=".jpg,.jpeg,.pdf,.png,.doc,.docx,.txt" required class="hidden"
                                       onchange="dzFileSelected(this,'submission_file')">
                                @error('submission_file')
                                    <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="submission_comment" class="block text-xs font-semibold text-brand-gray uppercase tracking-wider mb-2">Comentário (opcional)</label>
                                <textarea id="submission_comment" name="comment" rows="2"
                                          class="w-full rounded-lg bg-brand-dark border border-brand-dark-border text-white placeholder-brand-gray text-sm px-3 py-2 focus:outline-none focus:ring-1 focus:ring-brand-yellow resize-none"
                                          placeholder="Observações sobre o envio...">{{ old('comment', $userSubmission->comment ?? '') }}</textarea>
                            </div>
                            <button type="submit"
                                    class="inline-flex items-center justify-center gap-2 w-full sm:w-auto bg-brand-yellow hover:bg-brand-yellow-hover text-brand-dark font-bold py-2.5 px-4 rounded-lg transition text-sm uppercase tracking-wider">
                                {{ $hasExisting ? 'Substituir Arquivo' : 'Enviar Arquivo' }}
                            </button>
                        </form>
                    @else
                        <p class="text-xs text-brand-gray mt-4">Esta submissão já foi aprovada. Não é possível alterar após a pontuação.</p>
                    @endif
                </div>
            @endif
        </div>

        {{-- ===================== SEÇÃO DE FOTOS ===================== --}}
        @php
            $showPhotos = true;
        @endphp
        @if ($showPhotos)
            <div class="mt-6 bg-brand-dark-card border border-brand-dark-border rounded-2xl p-5 sm:p-8">
                <div class="flex items-center justify-between flex-wrap gap-3 mb-4">
                    <h3 class="text-sm font-bold text-brand-yellow uppercase tracking-wider flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Álbum de Fotos
                        @if ($approvedPhotos->count() > 0)
                            <span class="text-xs text-brand-gray font-normal">({{ $approvedPhotos->count() }} foto{{ $approvedPhotos->count() === 1 ? '' : 's' }})</span>
                        @endif
                    </h3>
                    <div class="flex items-center gap-2">
                        @if ($approvedPhotos->count() > 0)
                            <a href="{{ route('activities.album', $activity) }}" class="text-xs text-brand-yellow hover:underline underline-offset-2 font-semibold">
                                Ver álbum completo →
                            </a>
                        @endif
                        <button onclick="toggleUploadBox()" id="btn-toggle-upload"
                                class="inline-flex items-center gap-1.5 bg-brand-dark-input border border-brand-dark-border hover:border-brand-yellow text-white text-xs font-semibold px-3 py-2 rounded-lg transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Enviar Fotos
                        </button>
                    </div>
                </div>

                {{-- Upload form --}}
                <div id="upload-box" style="display:none" class="mb-6 p-4 bg-brand-dark-input border border-brand-dark-border rounded-xl">
                    @if (session('success') && str_contains(session('success'), 'foto'))
                        <div class="mb-3 bg-green-900/30 border border-green-800 text-green-400 px-3 py-2 rounded-lg text-sm">
                            {{ session('success') }}
                        </div>
                    @endif
                    <form method="POST" action="{{ route('activities.photos.store', $activity) }}" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label class="block text-xs font-semibold text-brand-gray uppercase tracking-wider mb-2">
                                Fotos <span class="font-normal normal-case text-brand-gray/60">(JPG, PNG, WebP · máx. 5MB cada · até 10 fotos)</span>
                            </label>
                            {{-- Drop zone --}}
                            <div id="dz-photos"
                                 tabindex="0"
                                 onclick="document.getElementById('photo-input').click()"
                                 ondragover="event.preventDefault();this.classList.add('border-brand-yellow','bg-brand-yellow/5')"
                                 ondragleave="this.classList.remove('border-brand-yellow','bg-brand-yellow/5')"
                                 ondrop="event.preventDefault();this.classList.remove('border-brand-yellow','bg-brand-yellow/5');handlePhotoDrop(event)"
                                 class="border-2 border-dashed border-brand-dark-border rounded-xl p-6 text-center cursor-pointer transition hover:border-brand-yellow/60 hover:bg-brand-yellow/5 focus:outline-none focus:border-brand-yellow">
                                <svg class="w-8 h-8 text-brand-gray/40 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <p id="dz-photos-label" class="text-sm text-brand-gray">Arraste as fotos aqui ou <span class="text-brand-yellow font-semibold">clique para selecionar</span></p>
                            </div>
                            <input type="file" id="photo-input" name="photos[]" multiple accept="image/jpeg,image/png,image/webp" class="hidden"
                                   onchange="updatePhotoLabel(this)">
                            @error('photos') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                            @error('photos.*') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="mb-4">
                            <label class="block text-xs font-semibold text-brand-gray uppercase tracking-wider mb-1">Legenda <span class="font-normal normal-case text-brand-gray/60">(opcional)</span></label>
                            <input type="text" name="captions[]" maxlength="255" placeholder="Ex: Distribuição de panfletos no centro"
                                   class="block w-full px-3 py-2 bg-brand-dark border border-brand-dark-border rounded-lg text-white placeholder-brand-gray/50 text-sm focus:outline-none focus:ring-1 focus:ring-brand-yellow">
                        </div>
                        <div class="flex items-center gap-3">
                            <button type="submit"
                                    class="inline-flex items-center gap-2 bg-brand-yellow hover:bg-brand-yellow-hover text-brand-dark font-bold py-2 px-5 rounded-lg transition text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                Enviar Fotos
                            </button>
                            <button type="button" onclick="toggleUploadBox()" class="text-sm text-brand-gray hover:text-white transition">Cancelar</button>
                        </div>
                    </form>
                </div>
                <script>
                    function toggleUploadBox() {
                        var box = document.getElementById('upload-box');
                        box.style.display = (box.style.display === 'none' || box.style.display === '') ? 'block' : 'none';
                    }
                    function updatePhotoLabel(input) {
                        var label = document.getElementById('dz-photos-label');
                        if (input.files && input.files.length > 0) {
                            label.textContent = input.files.length + ' foto(s) selecionada(s)';
                            label.classList.add('text-white');
                        } else {
                            label.innerHTML = 'Arraste as fotos aqui ou <span class="text-brand-yellow font-semibold">clique para selecionar</span>';
                            label.classList.remove('text-white');
                        }
                    }
                    function handlePhotoDrop(event) {
                        var input = document.getElementById('photo-input');
                        var dt = event.dataTransfer;
                        if (dt && dt.files.length) {
                            input.files = dt.files;
                            updatePhotoLabel(input);
                        }
                    }
                </script>

                {{-- Approved photos mini-grid --}}
                @if ($approvedPhotos->count() > 0)
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 mb-4">
                        @foreach ($approvedPhotos->take(6) as $photo)
                            <a href="{{ route('activities.album', $activity) }}" class="block">
                                <img src="{{ $photo->url() }}"
                                     alt="{{ e($photo->caption ?? 'Foto') }}"
                                     class="w-full h-24 sm:h-28 object-cover rounded-lg border border-brand-dark-border hover:opacity-80 transition">
                            </a>
                        @endforeach
                    </div>
                    @if ($approvedPhotos->count() > 6)
                        <a href="{{ route('activities.album', $activity) }}" class="text-xs text-brand-yellow hover:underline underline-offset-2">
                            +{{ $approvedPhotos->count() - 6 }} mais foto(s) no álbum →
                        </a>
                    @endif
                @else
                    <p class="text-sm text-brand-gray text-center py-4">Nenhuma foto aprovada ainda. Envie fotos da missão!</p>
                @endif

                {{-- Pending photos (own or all for moderators) --}}
                @if ($pendingPhotos->count() > 0)
                    <div class="mt-5 pt-5 border-t border-brand-dark-border">
                        <h4 class="text-xs font-bold text-brand-gray uppercase tracking-wider mb-3">
                            @if (auth()->user()->canManageActivities())
                                Fotos Aguardando Aprovação ({{ $pendingPhotos->count() }})
                            @else
                                Suas Fotos — Aguardando Aprovação
                            @endif
                        </h4>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                            @foreach ($pendingPhotos as $photo)
                                <div class="relative group">
                                    <img src="{{ $photo->url() }}"
                                         alt="{{ e($photo->caption ?? 'Foto pendente') }}"
                                         class="w-full h-24 sm:h-28 object-cover rounded-lg border border-brand-dark-border opacity-60">
                                    <div class="absolute inset-0 flex flex-col items-center justify-center rounded-lg bg-black/40">
                                        <span class="text-xs font-bold text-yellow-300 text-center px-1">Aguardando</span>
                                        @if (auth()->user()->canManageActivities())
                                            <span class="text-[10px] text-brand-gray">por {{ $photo->uploader->displayName() }}</span>
                                        @endif
                                    </div>
                                    {{-- Approve / Reject / Delete controls --}}
                                    <div class="absolute bottom-1 left-1 right-1 flex gap-1 opacity-0 group-hover:opacity-100 transition">
                                        @if (auth()->user()->canManageActivities())
                                            <form method="POST" action="{{ route('admin.photos.approve', $photo) }}">
                                                @csrf
                                                <button type="submit" title="Aprovar" class="bg-green-700 hover:bg-green-600 text-white text-[10px] font-bold px-2 py-1 rounded transition">✓</button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.photos.reject', $photo) }}">
                                                @csrf
                                                <button type="submit" title="Recusar" class="bg-orange-700 hover:bg-orange-600 text-white text-[10px] font-bold px-2 py-1 rounded transition">✗</button>
                                            </form>
                                        @endif
                                        @can('delete', $photo)
                                            <form method="POST" action="{{ route('activities.photos.destroy', [$activity, $photo]) }}"
                                                  @submit.prevent="if(confirm('Remover esta foto?')) $el.submit()">
                                                @csrf @method('DELETE')
                                                <button type="submit" title="Excluir" class="bg-red-800 hover:bg-red-700 text-white text-[10px] font-bold px-2 py-1 rounded transition">🗑</button>
                                            </form>
                                        @endcan
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        @endif
        {{-- ============================================================ --}}

        {{-- Inscritos — visível apenas para coordenadores e admins --}}
        @if (auth()->user()->canManageActivities())
            <div id="rsvp-list" class="hidden mt-6 bg-brand-dark-card border border-brand-dark-border rounded-2xl p-5 sm:p-8">
                <h3 class="text-sm font-bold text-brand-yellow uppercase tracking-wider mb-4">
                    Inscritos ({{ $rsvpCount }})
                </h3>

                @if ($rsvpParticipants->isEmpty())
                    <p class="text-sm text-brand-gray text-center py-4">Nenhuma inscrição ainda.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-brand-dark-border text-xs text-brand-gray uppercase tracking-wider">
                                    <th class="text-left pb-3 pr-4">Nome</th>
                                    <th class="text-left pb-3 pr-4">Telefone</th>
                                    <th class="text-left pb-3">WhatsApp</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($rsvpParticipants as $inscrito)
                                    @php
                                        $phone = preg_replace('/\D/', '', $inscrito->phone ?? '');
                                        $waLink = $phone ? 'https://wa.me/55' . $phone : null;
                                    @endphp
                                    <tr class="border-b border-brand-dark-border/50 last:border-0">
                                        <td class="py-3 pr-4 text-white font-medium">{{ $inscrito->name }}</td>
                                        <td class="py-3 pr-4 text-brand-gray">{{ $inscrito->phone ?? '—' }}</td>
                                        <td class="py-3">
                                            @if ($waLink)
                                                <a href="{{ $waLink }}" target="_blank" rel="noopener noreferrer"
                                                   class="inline-flex items-center gap-1.5 bg-green-700 hover:bg-green-600 text-white text-xs font-semibold px-3 py-1.5 rounded-lg transition">
                                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                                                    WhatsApp
                                                </a>
                                            @else
                                                <span class="text-brand-gray text-xs">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        @endif
    </div>
@endsection
