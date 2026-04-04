@extends('layouts.app')

@section('title', 'Completar Cadastro')

@section('content')
    <div class="max-w-3xl mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold text-white mb-4">Completar Cadastro</h1>

        <form method="POST" action="{{ route('profile.complete.update') }}">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm text-brand-gray mb-1">Data de nascimento</label>
                    <input type="date" name="date_of_birth" value="{{ old('date_of_birth', $user->date_of_birth) }}" class="w-full px-3 py-2 rounded-lg bg-brand-dark-input border border-brand-dark-border text-white">
                </div>

                <div>
                    <label class="block text-sm text-brand-gray mb-1">Religião</label>
                    <input type="text" name="religion" value="{{ old('religion', $user->religion) }}" class="w-full px-3 py-2 rounded-lg bg-brand-dark-input border border-brand-dark-border text-white">
                </div>

                <div>
                    <label class="block text-sm text-brand-gray mb-1">Nível de escolaridade</label>
                    <input type="text" name="education_level" value="{{ old('education_level', $user->education_level) }}" class="w-full px-3 py-2 rounded-lg bg-brand-dark-input border border-brand-dark-border text-white">
                </div>

                <div>
                    <label class="block text-sm text-brand-gray mb-1">Curso superior (se houver)</label>
                    <input type="text" name="higher_course" value="{{ old('higher_course', $user->higher_course) }}" class="w-full px-3 py-2 rounded-lg bg-brand-dark-input border border-brand-dark-border text-white">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm text-brand-gray mb-1">Profissão atual (se houver)</label>
                    <input type="text" name="profession" value="{{ old('profession', $user->profession) }}" class="w-full px-3 py-2 rounded-lg bg-brand-dark-input border border-brand-dark-border text-white">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm text-brand-gray mb-1">Como e quando conheceu o movimento?</label>
                    <textarea name="how_known" rows="3" class="w-full px-3 py-2 rounded-lg bg-brand-dark-input border border-brand-dark-border text-white">{{ old('how_known', $user->how_known) }}</textarea>
                </div>

                <div>
                    <label class="block text-sm text-brand-gray mb-1">Qual o primeiro porta-voz que chamou a sua atenção?</label>
                    <input type="text" name="first_spokesperson" value="{{ old('first_spokesperson', $user->first_spokesperson) }}" class="w-full px-3 py-2 rounded-lg bg-brand-dark-input border border-brand-dark-border text-white">
                </div>

                <div class="sm:col-span-2">
                    <p class="text-xs text-brand-gray mb-2">Pautas políticas — descreva suas pautas políticas. Use Enter para separar linhas.</p>
                </div>

                <div>
                    <label class="block text-sm text-brand-gray mb-1">Pauta 1</label>
                    <textarea name="pauta1" rows="3" class="w-full px-3 py-2 rounded-lg bg-brand-dark-input border border-brand-dark-border text-white">{{ old('pauta1', $user->pauta1) }}</textarea>
                </div>

                <div>
                    <label class="block text-sm text-brand-gray mb-1">Pauta 2</label>
                    <textarea name="pauta2" rows="3" class="w-full px-3 py-2 rounded-lg bg-brand-dark-input border border-brand-dark-border text-white">{{ old('pauta2', $user->pauta2) }}</textarea>
                </div>

                <div>
                    <label class="block text-sm text-brand-gray mb-1">Pauta 3</label>
                    <input type="text" name="pauta3" value="{{ old('pauta3', $user->pauta3) }}" class="w-full px-3 py-2 rounded-lg bg-brand-dark-input border border-brand-dark-border text-white">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm text-brand-gray mb-1">Qual a ambição política?</label>
                    <input type="text" name="political_ambition" value="{{ old('political_ambition', $user->political_ambition) }}" class="w-full px-3 py-2 rounded-lg bg-brand-dark-input border border-brand-dark-border text-white">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm text-brand-gray mb-1">Situação atual no movimento?</label>
                    <input type="text" name="current_status" value="{{ old('current_status', $user->current_status) }}" class="w-full px-3 py-2 rounded-lg bg-brand-dark-input border border-brand-dark-border text-white">
                </div>
            </div>

            <div class="mt-6 flex items-center gap-4">
                <button class="bg-brand-yellow hover:bg-brand-yellow-hover text-brand-dark font-bold py-3 px-6 rounded-lg">Salvar</button>
                <a href="{{ route('dashboard') }}" class="text-sm text-brand-gray">Cancelar</a>
            </div>
        </form>
    </div>
@endsection
