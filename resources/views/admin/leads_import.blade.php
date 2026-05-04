@extends('layouts.app')

@section('title', 'Importar Leads - ONÇAS DO OESTE')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-5 sm:py-6 space-y-6">

    <div class="flex items-center gap-3 flex-wrap">
        <a href="{{ route('leads.index') }}" class="text-brand-gray hover:text-white transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h1 class="text-lg sm:text-xl font-bold text-white tracking-tight uppercase">Importar Leads via Excel</h1>
    </div>

    {{-- Errors --}}
    @if($errors->any())
        <div class="bg-red-900/40 border border-red-500 text-red-300 px-4 py-3 rounded-lg text-sm whitespace-pre-wrap">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    {{-- Template download --}}
    <div class="bg-brand-dark-card border border-brand-dark-border rounded-2xl p-5">
        <h2 class="text-sm font-bold text-brand-gray uppercase tracking-wider mb-3">1. Baixe o modelo</h2>
        <p class="text-sm text-brand-gray mb-4">Use o modelo abaixo para preencher os dados. Não altere os cabeçalhos.</p>
        <a href="{{ route('leads.template') }}"
           class="inline-flex items-center gap-2 bg-brand-dark-input hover:bg-brand-dark-border border border-brand-dark-border text-white font-semibold text-sm px-4 py-2.5 rounded-lg transition">
            <svg class="w-4 h-4 text-brand-yellow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
            Baixar template_importacao_leads.xls
        </a>
    </div>

    {{-- Upload form --}}
    <div class="bg-brand-dark-card border border-brand-dark-border rounded-2xl p-5">
        <h2 class="text-sm font-bold text-brand-gray uppercase tracking-wider mb-3">2. Envie a planilha preenchida</h2>

        <form method="POST" action="{{ route('leads.import.preview') }}" enctype="multipart/form-data">
            @csrf

            <div class="mb-4">
                <label class="block text-sm text-brand-gray mb-1">
                    Arquivo <span class="text-red-400">*</span>
                    <span class="text-xs text-brand-gray/60">(somente .xls — máx. 5 MB)</span>
                </label>
                <input type="file" name="spreadsheet" accept=".xls,.xlsx,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
                       class="block w-full text-sm text-brand-gray
                              file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0
                              file:text-xs file:font-bold file:bg-brand-yellow file:text-brand-dark
                              hover:file:bg-brand-yellow-hover file:cursor-pointer cursor-pointer">
            </div>

            <div class="bg-brand-dark-input border border-brand-dark-border rounded-lg p-4 text-xs text-brand-gray space-y-1 mb-5">
                <p class="font-semibold text-white mb-2">Regras de importação:</p>
                <p>• Campos obrigatórios: <span class="text-white">name, email, phone</span>.</p>
                <p>• Senha padrão: <span class="text-white">primeiros 6 dígitos do telefone</span>. O usuário deverá trocá-la no primeiro acesso.</p>
                <p>• Se o e-mail já estiver cadastrado, a linha é ignorada (sem atualizar o usuário).</p>
                <p>• Se qualquer linha tiver erro de validação, nenhum usuário é criado até os erros serem corrigidos.</p>
                <p>• Linhas com e-mail duplicado são ignoradas mas não bloqueiam a importação das demais linhas válidas.</p>
            </div>

            <button type="submit"
                    class="bg-brand-yellow hover:bg-brand-yellow-hover text-brand-dark font-bold py-3 px-6 rounded-lg transition uppercase tracking-wider text-sm">
                Validar e visualizar
            </button>
        </form>
    </div>

</div>
@endsection
