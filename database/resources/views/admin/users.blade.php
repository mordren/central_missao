@extends('layouts.app')

@section('title', 'Gerenciar Usuários - ONÇAS DO OESTE')

@section('content')
    <div class="max-w-4xl mx-auto px-4 py-5 sm:py-6 space-y-6">
        <h1 class="text-xl font-bold text-white tracking-tight uppercase">Gerenciar Usuários</h1>

        @if (session('success'))
            <div class="bg-green-900/30 border border-green-800 text-green-400 px-4 py-3 rounded-lg text-sm">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="bg-red-900/30 border border-red-800 text-red-400 px-4 py-3 rounded-lg text-sm">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-brand-dark-card border border-brand-dark-border rounded-2xl overflow-x-auto">
            <table class="w-full table-fixed min-w-0 sm:min-w-[680px]">
                <thead>
                    <tr class="border-b border-brand-dark-border">
                        <th class="px-5 py-3 text-left text-xs font-bold text-brand-gray uppercase tracking-wider">Nome</th>
                        <th class="px-5 py-3 text-left text-xs font-bold text-brand-gray uppercase tracking-wider hidden sm:table-cell">Telefone</th>
                        <th class="px-5 py-3 text-left text-xs font-bold text-brand-gray uppercase tracking-wider hidden md:table-cell">Cidade</th>
                        <th class="px-5 py-3 text-left text-xs font-bold text-brand-gray uppercase tracking-wider">Papel</th>
                        <th class="px-5 py-3 text-right text-xs font-bold text-brand-gray uppercase tracking-wider">Pontos</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $u)
                        <tr class="border-b border-brand-dark-border/50 {{ $u->id === auth()->id() ? 'bg-brand-yellow/5' : '' }}">
                            <td class="px-5 py-3">
                                <span class="text-sm font-medium text-white break-words">{{ $u->name }}</span>
                            </td>
                            <td class="px-5 py-3 hidden sm:table-cell">
                                <span class="text-sm text-brand-gray break-words">{{ $u->phone }}</span>
                            </td>
                            <td class="px-5 py-3 hidden md:table-cell">
                                <span class="text-sm text-brand-gray break-words">{{ $u->city ?? '—' }}</span>
                            </td>
                            <td class="px-5 py-3">
                                @if ($u->id === auth()->id())
                                    <span class="text-xs font-bold text-brand-yellow bg-brand-yellow/10 px-2 py-1 rounded capitalize">{{ $u->role }}</span>
                                @else
                                    <form method="POST" action="{{ route('admin.users.updateRole', $u) }}" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <select name="role" onchange="this.form.submit()"
                                            class="text-xs font-medium bg-brand-dark-input border border-brand-dark-border rounded px-2 py-1 text-white focus:outline-none focus:ring-1 focus:ring-brand-yellow cursor-pointer">
                                            <option value="participante" {{ $u->role === 'participante' ? 'selected' : '' }}>Participante</option>
                                            <option value="coordenador" {{ $u->role === 'coordenador' ? 'selected' : '' }}>Coordenador</option>
                                            <option value="administrador" {{ $u->role === 'administrador' ? 'selected' : '' }}>Administrador</option>
                                        </select>
                                    </form>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-right">
                                <span class="text-sm font-bold text-brand-yellow">{{ number_format($u->points) }}</span>
                            </td>
                        </tr>
                    @endforeach
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
