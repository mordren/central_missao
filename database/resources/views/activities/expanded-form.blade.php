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
        <div class="mb-4">
            <label class="block font-semibold mb-1">Religião</label>
            <select name="religiao" class="form-select w-full" required onchange="document.getElementById('religiao_outro').style.display = this.value === 'Outro' ? 'block' : 'none';">
                <option value="">Selecione</option>
                <option value="Católico" {{ old('religiao') == 'Católico' ? 'selected' : '' }}>Católico</option>
                <option value="Evangélico" {{ old('religiao') == 'Evangélico' ? 'selected' : '' }}>Evangélico</option>
                <option value="Ateu" {{ old('religiao') == 'Ateu' ? 'selected' : '' }}>Ateu</option>
                <option value="Outro" {{ old('religiao') == 'Outro' ? 'selected' : '' }}>Outro</option>
            </select>
            <input type="text" name="religiao_outro" id="religiao_outro" class="form-input w-full mt-2" placeholder="Qual?" style="display:{{ old('religiao') == 'Outro' ? 'block' : 'none' }}" value="{{ old('religiao_outro') }}">
        </div>
        <div class="mb-4">
            <label class="block font-semibold mb-1">Graduação</label>
            <select name="graduacao" class="form-select w-full" required>
                <option value="">Selecione</option>
                <option value="Médio Incompleto" {{ old('graduacao') == 'Médio Incompleto' ? 'selected' : '' }}>Médio Incompleto</option>
                <option value="Médio completo" {{ old('graduacao') == 'Médio completo' ? 'selected' : '' }}>Médio completo</option>
                <option value="Superior Incompleto" {{ old('graduacao') == 'Superior Incompleto' ? 'selected' : '' }}>Superior Incompleto</option>
                <option value="Superior Completo" {{ old('graduacao') == 'Superior Completo' ? 'selected' : '' }}>Superior Completo</option>
                <option value="Acima" {{ old('graduacao') == 'Acima' ? 'selected' : '' }}>Acima</option>
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
@endsection
