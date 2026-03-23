@extends('layouts.app')

@section('title', 'Ranking - Central da Missão')

@section('content')
    <div class="max-w-3xl mx-auto px-4 py-6 space-y-6">
        <h1 class="text-xl font-bold text-white tracking-tight uppercase">Ranking</h1>

        <div class="bg-brand-dark-card border border-brand-dark-border rounded-2xl overflow-hidden">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-brand-dark-border">
                        <th class="px-5 py-3 text-left text-xs font-bold text-brand-gray uppercase tracking-wider">#</th>
                        <th class="px-5 py-3 text-left text-xs font-bold text-brand-gray uppercase tracking-wider">Nome</th>
                        <th class="px-5 py-3 text-left text-xs font-bold text-brand-gray uppercase tracking-wider hidden sm:table-cell">Cidade</th>
                        <th class="px-5 py-3 text-right text-xs font-bold text-brand-gray uppercase tracking-wider">Pontos</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $index => $u)
                        @php $pos = $users->firstItem() + $index; @endphp
                        <tr class="border-b border-brand-dark-border/50 {{ $u->id === auth()->id() ? 'bg-brand-yellow/5' : '' }}">
                            <td class="px-5 py-3">
                                @if ($pos <= 3)
                                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-full text-sm font-bold {{ $pos === 1 ? 'bg-yellow-500 text-black' : ($pos === 2 ? 'bg-gray-400 text-black' : 'bg-amber-700 text-white') }}">
                                        {{ $pos }}
                                    </span>
                                @else
                                    <span class="text-sm text-brand-gray ml-1.5">{{ $pos }}</span>
                                @endif
                            </td>
                            <td class="px-5 py-3">
                                <span class="text-sm font-medium text-white {{ $u->id === auth()->id() ? 'text-brand-yellow' : '' }}">{{ $u->name }}</span>
                            </td>
                            <td class="px-5 py-3 hidden sm:table-cell">
                                <span class="text-sm text-brand-gray">{{ $u->city ?? '—' }}</span>
                            </td>
                            <td class="px-5 py-3 text-right">
                                <span class="text-sm font-bold text-brand-yellow">{{ number_format($u->points) }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-8 text-center text-brand-gray">Nenhum usuário encontrado.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($users->hasPages())
            <div class="flex justify-center">
                {{ $users->links('pagination::simple-tailwind') }}
            </div>
        @endif
    </div>
@endsection
