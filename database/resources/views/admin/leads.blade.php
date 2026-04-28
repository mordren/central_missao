@extends('layouts.app')

@section('title', 'Leads - ONÇAS DO OESTE')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-5 sm:py-6">
    <h1 class="text-lg sm:text-xl font-bold text-white tracking-tight uppercase mb-6">Leads do Site</h1>

    <div class="bg-brand-dark-card border border-brand-dark-border rounded-2xl overflow-x-auto pb-2">
        <table class="w-full min-w-[1240px] divide-y divide-brand-dark-border whitespace-nowrap">
            <thead>
                <tr>
                    <th class="sticky left-0 z-10 bg-brand-dark-card border-r border-brand-dark-border px-5 py-3 text-center text-xs font-bold text-brand-gray uppercase tracking-wider">Ação</th>
                    <th class="px-5 py-3 text-left text-xs font-bold text-brand-gray uppercase tracking-wider">Nome</th>
                    <th class="px-5 py-3 text-left text-xs font-bold text-brand-gray uppercase tracking-wider">Telefone</th>
                    <th class="px-5 py-3 text-left text-xs font-bold text-brand-gray uppercase tracking-wider">E-mail</th>
                    <th class="px-5 py-3 text-left text-xs font-bold text-brand-gray uppercase tracking-wider">Cidade</th>
                    <th class="px-5 py-3 text-left text-xs font-bold text-brand-gray uppercase tracking-wider">Bairro</th>
                    <th class="px-5 py-3 text-left text-xs font-bold text-brand-gray uppercase tracking-wider">Indicação</th>
                    <th class="px-5 py-3 text-left text-xs font-bold text-brand-gray uppercase tracking-wider">Cadastro</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($leads as $lead)
                    <tr class="border-b border-brand-dark-border/50">
                        <td class="sticky left-0 z-10 bg-brand-dark-card border-r border-brand-dark-border px-5 py-4 text-center">
                            @if ($lead->phone)
                                @php
                                    $waPhone = preg_replace('/\D/', '', $lead->phone);
                                    $waPhone = ltrim($waPhone, '0');
                                    if ($waPhone !== '' && substr($waPhone, 0, 2) !== '55') {
                                        $waPhone = '55' . $waPhone;
                                    }
                                @endphp
                                <a href="https://wa.me/{{ $waPhone }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white rounded-lg text-xs font-semibold transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.72 11.06a5.5 5.5 0 10-9.94 4.73L5 19l3.21-.84a5.5 5.5 0 008.51-7.1z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.5 10.5h.01M12 14.5h.01M15.5 10.5h.01"/></svg>
                                    WhatsApp
                                </a>
                            @else
                                <span class="text-brand-gray text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-white">{{ $lead->name }}</td>
                        <td class="px-5 py-4 text-brand-gray">{{ $lead->phone ?? '—' }}</td>
                        <td class="px-5 py-4 text-brand-gray">{{ $lead->email ?? '—' }}</td>
                        <td class="px-5 py-4 text-brand-gray">{{ $lead->city ?? '—' }}</td>
                        <td class="px-5 py-4 text-brand-gray">{{ $lead->neighborhood ?? '—' }}</td>
                        <td class="px-5 py-4 text-brand-gray">{{ $lead->referred_by ?? '—' }}</td>
                        <td class="px-5 py-4 text-brand-gray">{{ optional($lead->created_at)->format('d/m/Y H:i') ?? '—' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-brand-gray">Nenhum lead encontrado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
