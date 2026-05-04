@extends('layouts.app')

@section('content')
<div class="max-w-lg mx-auto px-4 py-6">
    <div class="bg-white rounded shadow p-4 sm:p-6">
    <h2 class="text-xl sm:text-2xl font-bold mb-4 text-center">Formulário Expandido</h2>
    <form method="POST" action="{{ route('activities.expandedForm.submit', $activity) }}">
        @csrf
        <div class="mb-4">
            <label class="block font-semibold mb-1">Nome completo</label>
            <input type="text" name="nome_completo" class="form-input w-full" required value="{{ old('nome_completo') }}">
        </div>
        <div class="mb-4">
            <label class="block font-semibold mb-1">Idade</label>
            <input type="number" name="idade" class="form-input w-full" required min="10" max="120" value="{{ old('idade') }}">
        </div>
        <div class="mb-4">
            <label class="block font-semibold mb-1">Cidade</label>
            <input type="text" name="cidade" class="form-input w-full" required value="{{ old('cidade') }}">
        </div>
        <div class="mb-4">
            <label class="block font-semibold mb-1">Telefone</label>
            <input type="text" name="telefone" class="form-input w-full" required value="{{ old('telefone') }}">
        </div>

        {{-- Religião --}}
        <div class="mb-4">
            <label class="block font-semibold mb-1">Religião</label>
            <select name="religiao" class="form-select w-full" required onchange="cmToggle(this,'rev_religiao')">
                <option value="">Selecione</option>
                <option value="Católico"           {{ old('religiao') == 'Católico'           ? 'selected' : '' }}>Católico</option>
                <option value="Protestante"        {{ old('religiao') == 'Protestante'        ? 'selected' : '' }}>Protestante</option>
                <option value="Matrizes Africanas" {{ old('religiao') == 'Matrizes Africanas' ? 'selected' : '' }}>Matrizes Africanas</option>
                <option value="Judeu"              {{ old('religiao') == 'Judeu'              ? 'selected' : '' }}>Judeu</option>
                <option value="Ateu"               {{ old('religiao') == 'Ateu'               ? 'selected' : '' }}>Ateu</option>
                <option value="Outra"              {{ old('religiao') == 'Outra'              ? 'selected' : '' }}>Outra</option>
            </select>
            <div id="rev_religiao" class="cm-reveal {{ old('religiao') == 'Outra' ? 'open' : '' }}">
                <div>
                    <input type="text" name="religiao_outro" placeholder="Qual religião?"
                        class="form-input w-full mt-2" value="{{ old('religiao_outro') }}">
                </div>
            </div>
        </div>

        {{-- Nível de escolaridade --}}
        <div class="mb-4">
            <label class="block font-semibold mb-1">Nível de escolaridade</label>
            <select name="graduacao" class="form-select w-full" required>
                <option value="">Selecione</option>
                @foreach([
                    'Ensino Fundamental Incompleto',
                    'Ensino Fundamental Completo',
                    'Ensino Médio Incompleto',
                    'Ensino Médio Completo',
                    'Ensino Superior Incompleto',
                    'Ensino Superior Completo',
                    'Pós-Graduação Incompleta',
                    'Pós-Graduação Completa',
                ] as $opt)
                    <option value="{{ $opt }}" {{ old('graduacao') == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-4">
            <label class="block font-semibold mb-1">Interesses (opcional)</label>
            <input type="text" name="interesses" class="form-input w-full" value="{{ old('interesses') }}">
        </div>
        <button type="submit" class="bg-yellow-400 hover:bg-yellow-500 text-black font-bold py-2.5 px-4 rounded w-full">Enviar e ganhar 25 pontos</button>
    </form>
    </div>
</div>

<style>
    .cm-reveal { display:grid; grid-template-rows:0fr; opacity:0; transition:grid-template-rows .28s ease,opacity .22s ease; }
    .cm-reveal > div { overflow:hidden; }
    .cm-reveal.open { grid-template-rows:1fr; opacity:1; }
</style>
<script>
    function cmToggle(select, revealId) {
        const reveal = document.getElementById(revealId);
        const isOther = select.value === 'Outra' || select.value === 'Outro';
        reveal.classList.toggle('open', isOther);
        if (isOther) { const inp = reveal.querySelector('input'); if (inp) inp.focus(); }
    }
</script>
@endsection
