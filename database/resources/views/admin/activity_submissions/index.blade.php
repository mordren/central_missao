@extends('layouts.app')

@section('title', 'Submissões de Tarefas')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-6 sm:py-8">
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <h1 class="text-lg sm:text-xl font-bold text-white tracking-tight uppercase">Submissões de Tarefas Manuais</h1>
        <a href="{{ route('activities.index') }}" class="text-xs text-brand-gray hover:text-white transition">← Atividades</a>
    </div>

    @if(session('success'))
        <div class="mb-4 bg-green-900/30 border border-green-800 text-green-400 px-4 py-3 rounded-lg text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 bg-red-900/30 border border-red-800 text-red-400 px-4 py-3 rounded-lg text-sm">{{ session('error') }}</div>
    @endif

    @php $currentStatus = request()->get('status', ''); @endphp
    <div class="flex flex-wrap gap-2 mb-5">
        <a href="{{ route('admin.activity_submissions.index') }}"
           class="px-4 py-1.5 rounded-full text-xs font-semibold transition {{ $currentStatus === '' ? 'bg-brand-yellow text-brand-dark' : 'bg-brand-dark-input text-brand-gray border border-brand-dark-border hover:text-white' }}">
            Todas
        </a>
        <a href="?status=pending"
           class="px-4 py-1.5 rounded-full text-xs font-semibold transition {{ $currentStatus === 'pending' ? 'bg-yellow-500 text-black' : 'bg-brand-dark-input text-brand-gray border border-brand-dark-border hover:text-white' }}">
            Pendentes
        </a>
        <a href="?status=approved"
           class="px-4 py-1.5 rounded-full text-xs font-semibold transition {{ $currentStatus === 'approved' ? 'bg-green-600 text-white' : 'bg-brand-dark-input text-brand-gray border border-brand-dark-border hover:text-white' }}">
            Aprovadas
        </a>
        <a href="?status=rejected"
           class="px-4 py-1.5 rounded-full text-xs font-semibold transition {{ $currentStatus === 'rejected' ? 'bg-red-600 text-white' : 'bg-brand-dark-input text-brand-gray border border-brand-dark-border hover:text-white' }}">
            Recusadas
        </a>
    </div>

    <div class="bg-brand-dark-card border border-brand-dark-border rounded-2xl overflow-x-auto pb-2">
        <table class="w-full min-w-[680px] divide-y divide-brand-dark-border">
            <thead>
                <tr class="bg-brand-dark-input/50">
                    <th class="px-4 py-3 text-left text-xs font-bold text-brand-gray uppercase tracking-wider">Usuário</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-brand-gray uppercase tracking-wider">Atividade</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-brand-gray uppercase tracking-wider">Enviado em</th>
                    <th class="px-4 py-3 text-center text-xs font-bold text-brand-gray uppercase tracking-wider">Status</th>
                    <th class="px-4 py-3 text-center text-xs font-bold text-brand-gray uppercase tracking-wider">Pts</th>
                    <th class="px-4 py-3 text-center text-xs font-bold text-brand-gray uppercase tracking-wider">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-brand-dark-border/40">
                @forelse($submissions as $submission)
                    <tr class="hover:bg-brand-dark-input/30 transition">
                        <td class="px-4 py-4">
                            <p class="text-sm text-white font-medium">{{ $submission->user->name }}</p>
                            <p class="text-xs text-brand-gray">{{ $submission->user->email }}</p>
                        </td>
                        <td class="px-4 py-4">
                            <p class="text-sm text-white max-w-[180px] truncate">{{ $submission->activity->title }}</p>
                            <p class="text-xs text-brand-yellow">{{ $submission->activity->points }} pts</p>
                        </td>
                        <td class="px-4 py-4 text-sm text-brand-gray whitespace-nowrap">
                            {{ optional($submission->submitted_at)->format('d/m/Y H:i') ?? '—' }}
                        </td>
                        <td class="px-4 py-4 text-center">
                            @if($submission->status === 'pending')
                                <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold bg-yellow-500/20 text-yellow-300 border border-yellow-500/30">Pendente</span>
                            @elseif($submission->status === 'approved')
                                <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-500/20 text-green-400 border border-green-500/30">Aprovada</span>
                            @else
                                <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-500/20 text-red-400 border border-red-500/30">Recusada</span>
                            @endif
                        </td>
                        <td class="px-4 py-4 text-center text-sm font-bold {{ $submission->points_awarded ? 'text-brand-yellow' : 'text-brand-gray' }}">
                            {{ $submission->points_awarded ? '+' . $submission->points_awarded : '—' }}
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex items-center justify-center gap-1.5 flex-wrap">
                                <a href="{{ route('admin.activity_submissions.show', $submission) }}"
                                   class="px-3 py-1.5 bg-brand-dark-input border border-brand-dark-border hover:border-brand-yellow text-white text-xs font-semibold rounded-lg transition">
                                    Ver
                                </a>
                                @if($submission->status === 'pending')
                                    <form action="{{ route('admin.activity_submissions.approve', $submission) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit"
                                                onclick="return confirm('Aprovar e conceder {{ $submission->activity->points }} pts para {{ $submission->user->name }}?')"
                                                class="px-3 py-1.5 bg-green-700 hover:bg-green-600 text-white text-xs font-semibold rounded-lg transition">
                                            ✓ Aprovar
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.activity_submissions.reject', $submission) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit"
                                                onclick="return confirm('Recusar esta submissão?')"
                                                class="px-3 py-1.5 bg-red-800 hover:bg-red-700 text-white text-xs font-semibold rounded-lg transition">
                                            ✕ Recusar
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-brand-gray text-sm">Nenhuma submissão encontrada.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($submissions->hasPages())
        <div class="mt-4">{{ $submissions->links() }}</div>
    @endif
</div>
@endsection
