@extends('layouts.app')

@section('title', 'Leads - ONÇAS DO OESTE')

@section('content')
<div class="max-w-full mx-auto px-4 py-5 sm:py-6 space-y-5">

    {{-- Header --}}
    <div class="flex items-center justify-between flex-wrap gap-3">
        <h1 class="text-lg sm:text-xl font-bold text-white tracking-tight uppercase">Leads</h1>
        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('leads.export') }}"
               class="inline-flex items-center gap-2 bg-brand-dark-input hover:bg-brand-dark-border border border-brand-dark-border text-white font-semibold text-sm px-4 py-2.5 rounded-lg transition">
                <svg class="w-4 h-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
                Exportar Excel
            </a>
            @if(auth()->user()->isAdmin())
                <a href="{{ route('leads.import') }}"
                   class="inline-flex items-center gap-2 bg-brand-yellow hover:bg-brand-yellow-hover text-brand-dark font-bold text-sm px-4 py-2.5 rounded-lg transition uppercase tracking-wider">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    Importar Excel
                </a>
            @endif
        </div>
    </div>

    {{-- Flash --}}
    @if(session('success'))
        <div class="bg-green-900/30 border border-green-800 text-green-400 px-4 py-3 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- Search --}}
    <form method="GET" action="{{ route('leads.index') }}" class="flex gap-2 flex-wrap">
        <input type="text" name="search" value="{{ $search ?? '' }}"
               placeholder="Buscar por nome, e-mail, telefone, cidade ou bairro…"
               class="flex-1 min-w-[260px] px-3 py-2 rounded-lg bg-brand-dark-input border border-brand-dark-border text-white text-sm placeholder-brand-gray focus:outline-none focus:border-brand-yellow transition">
        <button type="submit"
                class="bg-brand-yellow hover:bg-brand-yellow-hover text-brand-dark font-bold text-sm px-4 py-2 rounded-lg transition">
            Buscar
        </button>
        @if(!empty($search))
            <a href="{{ route('leads.index') }}"
               class="bg-brand-dark-input hover:bg-brand-dark-border border border-brand-dark-border text-brand-gray hover:text-white font-semibold text-sm px-4 py-2 rounded-lg transition">
                Limpar
            </a>
        @endif
    </form>

    {{-- Table --}}
    <div class="bg-brand-dark-card border border-brand-dark-border rounded-2xl overflow-x-auto pb-2">
        <table class="w-full min-w-[1800px] divide-y divide-brand-dark-border whitespace-nowrap text-sm">
            <thead>
                <tr class="bg-brand-dark-card">
                    <th class="sticky left-0 z-10 bg-brand-dark-card border-r border-brand-dark-border px-4 py-3 text-center text-xs font-bold text-brand-gray uppercase tracking-wider">Ação</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-brand-gray uppercase tracking-wider">Nome</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-brand-gray uppercase tracking-wider">Telefone</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-brand-gray uppercase tracking-wider">E-mail</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-brand-gray uppercase tracking-wider">Cidade</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-brand-gray uppercase tracking-wider">Bairro</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-brand-gray uppercase tracking-wider">Indicação</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-brand-gray uppercase tracking-wider">Nasc.</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-brand-gray uppercase tracking-wider">Religião</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-brand-gray uppercase tracking-wider">Escolaridade</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-brand-gray uppercase tracking-wider">Curso</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-brand-gray uppercase tracking-wider">Profissão</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-brand-gray uppercase tracking-wider">Como conheceu</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-brand-gray uppercase tracking-wider">1º Porta-voz</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-brand-gray uppercase tracking-wider">Pauta 1</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-brand-gray uppercase tracking-wider">Pauta 2</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-brand-gray uppercase tracking-wider">Pauta 3</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-brand-gray uppercase tracking-wider">Ambição Política</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-brand-gray uppercase tracking-wider">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-brand-gray uppercase tracking-wider">Cadastro</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($leads as $lead)
                    <tr class="border-b border-brand-dark-border/40 hover:bg-white/[0.02] transition">
                        <td class="sticky left-0 z-10 bg-brand-dark-card border-r border-brand-dark-border px-4 py-3 text-center">
                            @if ($lead->phone)
                                @php
                                    $waPhone = preg_replace('/\D/', '', $lead->phone);
                                    $waPhone = ltrim($waPhone, '0');
                                    if ($waPhone !== '' && substr($waPhone, 0, 2) !== '55') {
                                        $waPhone = '55' . $waPhone;
                                    }
                                @endphp
                                <a href="https://wa.me/{{ $waPhone }}" target="_blank" rel="noopener noreferrer"
                                   class="inline-flex items-center gap-1.5 px-2.5 py-1.5 bg-green-700 hover:bg-green-600 text-white rounded-lg text-xs font-semibold transition">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M11.999 2C6.477 2 2 6.477 2 12c0 1.821.487 3.53 1.338 5L2 22l5.15-1.312A9.95 9.95 0 0012 22c5.523 0 10-4.477 10-10S17.522 2 11.999 2z"/></svg>
                                    WA
                                </a>
                            @else
                                <span class="text-brand-gray text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-white font-medium">{{ e($lead->name) }}</td>
                        <td class="px-4 py-3 text-brand-gray">{{ e($lead->phone ?? '—') }}</td>
                        <td class="px-4 py-3 text-brand-gray">{{ e($lead->email ?? '—') }}</td>
                        <td class="px-4 py-3 text-brand-gray">{{ e($lead->city ?? '—') }}</td>
                        <td class="px-4 py-3 text-brand-gray">{{ e($lead->neighborhood ?? '—') }}</td>
                        <td class="px-4 py-3 text-brand-gray">{{ e($lead->referred_by ?? '—') }}</td>
                        <td class="px-4 py-3 text-brand-gray">{{ $lead->date_of_birth ? \Carbon\Carbon::parse($lead->date_of_birth)->format('d/m/Y') : '—' }}</td>
                        <td class="px-4 py-3 text-brand-gray">{{ e($lead->religion ?? '—') }}</td>
                        <td class="px-4 py-3 text-brand-gray">{{ e($lead->education_level ?? '—') }}</td>
                        <td class="px-4 py-3 text-brand-gray">{{ e($lead->higher_course ?? '—') }}</td>
                        <td class="px-4 py-3 text-brand-gray">{{ e($lead->profession ?? '—') }}</td>
                        <td class="px-4 py-3 text-brand-gray max-w-[200px] truncate" title="{{ e($lead->how_known ?? '') }}">{{ e($lead->how_known ?? '—') }}</td>
                        <td class="px-4 py-3 text-brand-gray">{{ e($lead->first_spokesperson ?? '—') }}</td>
                        <td class="px-4 py-3 text-brand-gray max-w-[160px] truncate" title="{{ e($lead->pauta1 ?? '') }}">{{ e($lead->pauta1 ?? '—') }}</td>
                        <td class="px-4 py-3 text-brand-gray max-w-[160px] truncate" title="{{ e($lead->pauta2 ?? '') }}">{{ e($lead->pauta2 ?? '—') }}</td>
                        <td class="px-4 py-3 text-brand-gray max-w-[160px] truncate" title="{{ e($lead->pauta3 ?? '') }}">{{ e($lead->pauta3 ?? '—') }}</td>
                        <td class="px-4 py-3 text-brand-gray">{{ e($lead->political_ambition ?? '—') }}</td>
                        <td class="px-4 py-3 text-brand-gray">{{ e($lead->current_status ?? '—') }}</td>
                        <td class="px-4 py-3 text-brand-gray">{{ optional($lead->created_at)->format('d/m/Y H:i') ?? '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="20" class="px-6 py-8 text-center text-brand-gray">Nenhum lead encontrado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($leads->hasPages())
        <div class="flex justify-center">
            {{ $leads->links('pagination::simple-tailwind') }}
        </div>
    @endif

</div>
@endsection
