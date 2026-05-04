@extends('layouts.app')

@section('title', 'Prévia da Importação - ONÇAS DO OESTE')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-5 sm:py-6 space-y-6">

    <div class="flex items-center gap-3 flex-wrap">
        <a href="{{ route('leads.import') }}" class="text-brand-gray hover:text-white transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h1 class="text-lg sm:text-xl font-bold text-white tracking-tight uppercase">Prévia da Importação</h1>
    </div>

    {{-- Summary badges --}}
    <div class="flex flex-wrap gap-3">
        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-green-900/40 border border-green-700 text-green-400">
            <span class="w-2 h-2 rounded-full bg-green-400"></span>
            {{ count($valid) }} válido(s)
        </span>
        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-yellow-900/40 border border-yellow-700 text-yellow-400">
            <span class="w-2 h-2 rounded-full bg-yellow-400"></span>
            {{ count($duplicates) }} duplicado(s)
        </span>
        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-red-900/40 border border-red-700 text-red-400">
            <span class="w-2 h-2 rounded-full bg-red-400"></span>
            {{ count($errors) }} erro(s)
        </span>
    </div>

    {{-- Blocking errors --}}
    @if(count($errors) > 0)
        <div class="bg-red-900/30 border border-red-700 rounded-2xl p-5">
            <h2 class="text-sm font-bold text-red-400 uppercase tracking-wider mb-3">Linhas com erros — importação bloqueada</h2>
            <p class="text-xs text-red-300 mb-4">Corrija os erros abaixo na planilha e envie novamente. Nenhum usuário foi criado.</p>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[640px] text-sm">
                    <thead>
                        <tr class="border-b border-red-800/50">
                            <th class="px-3 py-2 text-left text-xs font-bold text-red-400 uppercase">Linha</th>
                            <th class="px-3 py-2 text-left text-xs font-bold text-red-400 uppercase">Nome</th>
                            <th class="px-3 py-2 text-left text-xs font-bold text-red-400 uppercase">E-mail</th>
                            <th class="px-3 py-2 text-left text-xs font-bold text-red-400 uppercase">Motivo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($errors as $e)
                        <tr class="border-b border-red-900/30">
                            <td class="px-3 py-2 text-red-300">{{ $e['row'] }}</td>
                            <td class="px-3 py-2 text-white">{{ e($e['data']['name'] ?? '—') }}</td>
                            <td class="px-3 py-2 text-brand-gray">{{ e($e['data']['email'] ?? '—') }}</td>
                            <td class="px-3 py-2 text-red-300">{{ $e['reason'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- Duplicates --}}
    @if(count($duplicates) > 0)
        <div class="bg-yellow-900/20 border border-yellow-700/50 rounded-2xl p-5">
            <h2 class="text-sm font-bold text-yellow-400 uppercase tracking-wider mb-3">Linhas ignoradas — e-mails duplicados</h2>
            <p class="text-xs text-yellow-300/80 mb-4">Estes usuários já existem no sistema. Suas linhas serão ignoradas, mas não bloqueiam a importação.</p>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[480px] text-sm">
                    <thead>
                        <tr class="border-b border-yellow-800/40">
                            <th class="px-3 py-2 text-left text-xs font-bold text-yellow-500 uppercase">Linha</th>
                            <th class="px-3 py-2 text-left text-xs font-bold text-yellow-500 uppercase">Nome</th>
                            <th class="px-3 py-2 text-left text-xs font-bold text-yellow-500 uppercase">E-mail</th>
                            <th class="px-3 py-2 text-left text-xs font-bold text-yellow-500 uppercase">Motivo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($duplicates as $d)
                        <tr class="border-b border-yellow-900/20">
                            <td class="px-3 py-2 text-yellow-300">{{ $d['row'] }}</td>
                            <td class="px-3 py-2 text-white">{{ e($d['data']['name'] ?? '—') }}</td>
                            <td class="px-3 py-2 text-brand-gray">{{ e($d['data']['email'] ?? '—') }}</td>
                            <td class="px-3 py-2 text-yellow-300">{{ $d['reason'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- Valid rows --}}
    @if(count($valid) > 0)
        <div class="bg-brand-dark-card border border-brand-dark-border rounded-2xl p-5">
            <h2 class="text-sm font-bold text-green-400 uppercase tracking-wider mb-3">{{ count($valid) }} linha(s) prontas para importar</h2>
            <div class="overflow-x-auto pb-2">
                <table class="w-full min-w-[900px] text-sm divide-y divide-brand-dark-border">
                    <thead>
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-bold text-brand-gray uppercase">Linha</th>
                            <th class="px-3 py-2 text-left text-xs font-bold text-brand-gray uppercase">Nome</th>
                            <th class="px-3 py-2 text-left text-xs font-bold text-brand-gray uppercase">E-mail</th>
                            <th class="px-3 py-2 text-left text-xs font-bold text-brand-gray uppercase">Telefone</th>
                            <th class="px-3 py-2 text-left text-xs font-bold text-brand-gray uppercase">Cidade</th>
                            <th class="px-3 py-2 text-left text-xs font-bold text-brand-gray uppercase">Bairro</th>
                            <th class="px-3 py-2 text-left text-xs font-bold text-brand-gray uppercase">Senha padrão</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($valid as $v)
                        @php $digits = preg_replace('/\D/', '', $v['data']['phone']); $pass = substr($digits, 0, 6); @endphp
                        <tr class="border-b border-brand-dark-border/40">
                            <td class="px-3 py-2 text-brand-gray">{{ $v['row'] }}</td>
                            <td class="px-3 py-2 text-white">{{ e($v['data']['name']) }}</td>
                            <td class="px-3 py-2 text-brand-gray">{{ e($v['data']['email']) }}</td>
                            <td class="px-3 py-2 text-brand-gray">{{ e($v['data']['phone']) }}</td>
                            <td class="px-3 py-2 text-brand-gray">{{ e($v['data']['city'] ?: '—') }}</td>
                            <td class="px-3 py-2 text-brand-gray">{{ e($v['data']['neighborhood'] ?: '—') }}</td>
                            <td class="px-3 py-2 font-mono text-brand-yellow text-xs">{{ $pass }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Only show confirm if no blocking errors --}}
        @if(count($errors) === 0)
            <div class="bg-brand-dark-card border border-green-800 rounded-2xl p-5">
                <p class="text-sm text-white mb-4">
                    Confirme para criar <span class="font-bold text-green-400">{{ count($valid) }}</span> usuário(s).
                    A senha padrão de cada um será os primeiros 6 dígitos do telefone.
                    O usuário será solicitado a trocar a senha no primeiro acesso.
                </p>
                <form method="POST" action="{{ route('leads.import.confirm') }}">
                    @csrf
                    <div class="flex gap-3 flex-wrap">
                        <button type="submit"
                                class="bg-brand-yellow hover:bg-brand-yellow-hover text-brand-dark font-bold py-3 px-6 rounded-lg transition uppercase tracking-wider text-sm">
                            Confirmar importação
                        </button>
                        <a href="{{ route('leads.import') }}"
                           class="bg-brand-dark-input hover:bg-brand-dark-border border border-brand-dark-border text-white font-semibold py-3 px-6 rounded-lg transition text-sm">
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        @else
            <div class="bg-red-900/20 border border-red-700 rounded-xl px-5 py-4 text-sm text-red-300">
                Corrija os erros acima e envie a planilha novamente para habilitar a importação.
            </div>
        @endif

    @elseif(count($errors) === 0 && count($duplicates) > 0)
        <div class="bg-brand-dark-card border border-brand-dark-border rounded-xl px-5 py-4 text-sm text-brand-gray">
            Todas as linhas são duplicadas. Nenhum usuário novo para importar.
        </div>
    @endif

</div>
@endsection
