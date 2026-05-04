@extends('layouts.app')

@section('title', 'Completar Cadastro')

@section('content')
    @php
        $religionOpts    = ['Católico','Protestante','Matrizes Africanas','Judeu','Ateu'];
        $curReligion     = old('religion', $user->religion);
        $religionIsOther = $curReligion !== null && $curReligion !== '' && !in_array($curReligion, $religionOpts);

        $spokesOpts      = ['Kim Kataguiri','Arthur do Val (Mamãe Falei)','Renan Santos','Guto Zacarias','Amanda Vettorazzo'];
        $curSpokes       = old('first_spokesperson', $user->first_spokesperson);
        $spokesIsOther   = $curSpokes !== null && $curSpokes !== '' && !in_array($curSpokes, $spokesOpts);

        // Data de nascimento em dd/mm/aaaa para exibição
        $dobRaw     = old('date_of_birth', $user->date_of_birth ? \Carbon\Carbon::parse($user->date_of_birth)->format('Y-m-d') : '');
        $dobDisplay = $dobRaw ? \Carbon\Carbon::parse($dobRaw)->format('d/m/Y') : '';
    @endphp

    <style>
        .cm-reveal {
            display: grid;
            grid-template-rows: 0fr;
            opacity: 0;
            transition: grid-template-rows 0.28s ease, opacity 0.22s ease;
        }
        .cm-reveal > div { overflow: hidden; }
        .cm-reveal.open { grid-template-rows: 1fr; opacity: 1; }
        select.cm-select option { background: #1a1a2e; }
        .req-star { color: #f87171; margin-left: 2px; }
        .field-error { border-color: #f87171 !important; }
        #form-errors { display: none; }
        #form-errors.visible { display: block; }
    </style>

    <div class="max-w-3xl mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold text-white mb-1">Completar Cadastro</h1>
        <p class="text-xs text-brand-gray mb-5">Campos com <span class="req-star">*</span> são obrigatórios.</p>

        {{-- Erros do servidor --}}
        @if($errors->any())
            <div class="mb-4 p-4 rounded-lg bg-red-900/40 border border-red-500 text-red-300 text-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Erros JS (client-side) --}}
        <div id="form-errors" class="mb-4 p-4 rounded-lg bg-red-900/40 border border-red-500 text-red-300 text-sm">
            <p class="font-semibold mb-1">Por favor, preencha os campos obrigatórios:</p>
            <ul id="form-errors-list" class="list-disc list-inside space-y-1"></ul>
        </div>

        <form id="profile-form" method="POST" action="{{ route('profile.complete.update') }}" novalidate>
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                {{-- Data de nascimento --}}
                <div>
                    <label class="block text-sm text-brand-gray mb-1">
                        Data de nascimento <span class="req-star">*</span>
                    </label>
                    {{-- Campo visível com máscara dd/mm/aaaa --}}
                    <input type="text" id="dob_display" placeholder="dd/mm/aaaa"
                        value="{{ $dobDisplay }}" maxlength="10" autocomplete="off"
                        class="w-full px-3 py-2 rounded-lg bg-brand-dark-input border border-brand-dark-border text-white"
                        oninput="dobMask(this)">
                    {{-- Campo oculto enviado ao servidor em yyyy-mm-dd --}}
                    <input type="hidden" id="date_of_birth" name="date_of_birth" value="{{ $dobRaw }}">
                </div>

                {{-- Religião --}}
                <div>
                    <label class="block text-sm text-brand-gray mb-1">
                        Religião <span class="req-star">*</span>
                    </label>
                    <select name="religion" id="sel_religion"
                        class="cm-select w-full px-3 py-2 rounded-lg bg-brand-dark-input border border-brand-dark-border text-white"
                        onchange="cmToggle(this,'rev_religion','religion_outro')">
                        <option value="">Selecione</option>
                        @foreach($religionOpts as $opt)
                            <option value="{{ $opt }}" {{ $curReligion === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                        @endforeach
                        <option value="Outra" {{ $religionIsOther ? 'selected' : '' }}>Outra</option>
                    </select>
                    <div id="rev_religion" class="cm-reveal {{ $religionIsOther ? 'open' : '' }}">
                        <div>
                            <input type="text" id="religion_outro" name="religion_outro"
                                placeholder="Qual religião?"
                                value="{{ $religionIsOther ? $curReligion : '' }}"
                                {{ $religionIsOther ? 'required' : '' }}
                                class="w-full px-3 py-2 rounded-lg bg-brand-dark-input border border-brand-dark-border text-white mt-2">
                        </div>
                    </div>
                </div>

                {{-- Nível de escolaridade --}}
                <div>
                    <label class="block text-sm text-brand-gray mb-1">
                        Nível de escolaridade <span class="req-star">*</span>
                    </label>
                    <select name="education_level" id="sel_education"
                        class="cm-select w-full px-3 py-2 rounded-lg bg-brand-dark-input border border-brand-dark-border text-white">
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
                            <option value="{{ $opt }}" {{ old('education_level', $user->education_level) === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Curso superior --}}
                <div>
                    <label class="block text-sm text-brand-gray mb-1">Curso superior (se houver)</label>
                    <input type="text" name="higher_course" value="{{ old('higher_course', $user->higher_course) }}"
                        class="w-full px-3 py-2 rounded-lg bg-brand-dark-input border border-brand-dark-border text-white">
                </div>

                {{-- Profissão --}}
                <div class="sm:col-span-2">
                    <label class="block text-sm text-brand-gray mb-1">Profissão atual (se houver)</label>
                    <input type="text" name="profession" value="{{ old('profession', $user->profession) }}"
                        class="w-full px-3 py-2 rounded-lg bg-brand-dark-input border border-brand-dark-border text-white">
                </div>

                {{-- Como conheceu --}}
                <div class="sm:col-span-2">
                    <label class="block text-sm text-brand-gray mb-1">
                        Como e quando conheceu o movimento? <span class="req-star">*</span>
                    </label>
                    <textarea name="how_known" id="how_known" rows="3"
                        class="w-full px-3 py-2 rounded-lg bg-brand-dark-input border border-brand-dark-border text-white">{{ old('how_known', $user->how_known) }}</textarea>
                </div>

                {{-- Primeiro porta-voz --}}
                <div class="sm:col-span-2">
                    <label class="block text-sm text-brand-gray mb-1">
                        Qual o primeiro porta-voz que chamou a sua atenção? <span class="req-star">*</span>
                    </label>
                    <select name="first_spokesperson" id="sel_spokes"
                        class="cm-select w-full px-3 py-2 rounded-lg bg-brand-dark-input border border-brand-dark-border text-white"
                        onchange="cmToggle(this,'rev_spokes','spokes_outro')">
                        <option value="">Selecione</option>
                        @foreach($spokesOpts as $opt)
                            <option value="{{ $opt }}" {{ $curSpokes === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                        @endforeach
                        <option value="Outro" {{ $spokesIsOther ? 'selected' : '' }}>Outro</option>
                    </select>
                    <div id="rev_spokes" class="cm-reveal {{ $spokesIsOther ? 'open' : '' }}">
                        <div>
                            <input type="text" id="spokes_outro" name="first_spokesperson_outro"
                                placeholder="Qual porta-voz?"
                                value="{{ $spokesIsOther ? $curSpokes : '' }}"
                                {{ $spokesIsOther ? 'required' : '' }}
                                class="w-full px-3 py-2 rounded-lg bg-brand-dark-input border border-brand-dark-border text-white mt-2">
                        </div>
                    </div>
                </div>

                {{-- Pautas políticas --}}
                <div class="sm:col-span-2">
                    <p class="text-xs text-brand-gray mb-2">Pautas políticas — descreva suas pautas políticas. Use Enter para separar linhas.</p>
                </div>

                <div>
                    <label class="block text-sm text-brand-gray mb-1">
                        Pauta 1 <span class="req-star">*</span>
                    </label>
                    <textarea name="pauta1" id="pauta1" rows="3"
                        class="w-full px-3 py-2 rounded-lg bg-brand-dark-input border border-brand-dark-border text-white">{{ old('pauta1', $user->pauta1) }}</textarea>
                </div>

                <div>
                    <label class="block text-sm text-brand-gray mb-1">Pauta 2</label>
                    <textarea name="pauta2" rows="3"
                        class="w-full px-3 py-2 rounded-lg bg-brand-dark-input border border-brand-dark-border text-white">{{ old('pauta2', $user->pauta2) }}</textarea>
                </div>

                <div>
                    <label class="block text-sm text-brand-gray mb-1">Pauta 3</label>
                    <input type="text" name="pauta3" value="{{ old('pauta3', $user->pauta3) }}"
                        class="w-full px-3 py-2 rounded-lg bg-brand-dark-input border border-brand-dark-border text-white">
                </div>

                {{-- Ambição política --}}
                <div class="sm:col-span-2">
                    <label class="block text-sm text-brand-gray mb-1">
                        Qual a ambição política? <span class="req-star">*</span>
                    </label>
                    <select name="political_ambition" id="sel_ambition"
                        class="cm-select w-full px-3 py-2 rounded-lg bg-brand-dark-input border border-brand-dark-border text-white">
                        <option value="">Selecione</option>
                        @foreach([
                            'Observar e se informar',
                            'Trabalhar com política e militância',
                            'Interesse em candidatar-se',
                        ] as $opt)
                            <option value="{{ $opt }}" {{ old('political_ambition', $user->political_ambition) === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Situação no movimento --}}
                <div class="sm:col-span-2">
                    <label class="block text-sm text-brand-gray mb-1">
                        Situação atual no movimento? <span class="req-star">*</span>
                    </label>
                    <select name="current_status" id="sel_status"
                        class="cm-select w-full px-3 py-2 rounded-lg bg-brand-dark-input border border-brand-dark-border text-white">
                        <option value="">Selecione</option>
                        @foreach([
                            'Curioso (apenas observa de longe)',
                            'Apoiador (ajuda o movimento financeiramente e/ou com atividades de militância)',
                            'Membro (cursou ou está cursando a academia MBL)',
                            'Coordenador',
                        ] as $opt)
                            <option value="{{ $opt }}" {{ old('current_status', $user->current_status) === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                        @endforeach
                    </select>
                </div>

            </div>

            <div class="mt-6 flex items-center gap-4">
                <button type="submit" class="bg-brand-yellow hover:bg-brand-yellow-hover text-brand-dark font-bold py-3 px-6 rounded-lg">Salvar</button>
                <a href="{{ route('dashboard') }}" class="text-sm text-brand-gray">Cancelar</a>
            </div>
        </form>
    </div>

    <script>
        // ── Máscara dd/mm/aaaa ──────────────────────────────────────────────
        function dobMask(el) {
            let v = el.value.replace(/\D/g, '').slice(0, 8);
            if (v.length > 4) v = v.slice(0,2) + '/' + v.slice(2,4) + '/' + v.slice(4);
            else if (v.length > 2) v = v.slice(0,2) + '/' + v.slice(2);
            el.value = v;

            // Atualiza o campo oculto yyyy-mm-dd
            const hidden = document.getElementById('date_of_birth');
            if (v.length === 10) {
                const [d, m, y] = v.split('/');
                const parsed = new Date(`${y}-${m}-${d}`);
                if (!isNaN(parsed)) {
                    hidden.value = `${y}-${m}-${d}`;
                    return;
                }
            }
            hidden.value = '';
        }

        // ── Revelar campo "Outro" ───────────────────────────────────────────
        function cmToggle(select, revealId, inputId) {
            const reveal = document.getElementById(revealId);
            const inp    = document.getElementById(inputId);
            const isOther = select.value === 'Outra' || select.value === 'Outro';
            reveal.classList.toggle('open', isOther);
            if (inp) {
                inp.required = isOther;
                if (isOther) inp.focus();
                else inp.value = '';
            }
        }

        // ── Validação antes de enviar ───────────────────────────────────────
        document.getElementById('profile-form').addEventListener('submit', function(e) {
            const errors = [];

            const checks = [
                { id: 'dob_display',   label: 'Data de nascimento',
                  test: () => {
                      const v = document.getElementById('dob_display').value;
                      const h = document.getElementById('date_of_birth').value;
                      return v.length === 10 && h !== '';
                  }
                },
                { id: 'sel_religion',  label: 'Religião',
                  test: () => document.getElementById('sel_religion').value !== ''
                },
                { id: null,            label: 'Religião (especifique)',
                  test: () => {
                      const sel = document.getElementById('sel_religion');
                      if (sel.value !== 'Outra') return true;
                      return document.getElementById('religion_outro').value.trim() !== '';
                  }
                },
                { id: 'sel_education', label: 'Nível de escolaridade',
                  test: () => document.getElementById('sel_education').value !== ''
                },
                { id: 'how_known',     label: 'Como e quando conheceu o movimento',
                  test: () => document.getElementById('how_known').value.trim() !== ''
                },
                { id: 'sel_spokes',    label: 'Primeiro porta-voz',
                  test: () => document.getElementById('sel_spokes').value !== ''
                },
                { id: null,            label: 'Porta-voz (especifique)',
                  test: () => {
                      const sel = document.getElementById('sel_spokes');
                      if (sel.value !== 'Outro') return true;
                      return document.getElementById('spokes_outro').value.trim() !== '';
                  }
                },
                { id: 'pauta1',        label: 'Pauta 1',
                  test: () => document.getElementById('pauta1').value.trim() !== ''
                },
                { id: 'sel_ambition',  label: 'Ambição política',
                  test: () => document.getElementById('sel_ambition').value !== ''
                },
                { id: 'sel_status',    label: 'Situação atual no movimento',
                  test: () => document.getElementById('sel_status').value !== ''
                },
            ];

            checks.forEach(function(c) {
                if (!c.test()) {
                    errors.push(c.label);
                    if (c.id) {
                        const el = document.getElementById(c.id);
                        if (el) el.classList.add('field-error');
                    }
                } else if (c.id) {
                    const el = document.getElementById(c.id);
                    if (el) el.classList.remove('field-error');
                }
            });

            if (errors.length > 0) {
                e.preventDefault();
                const box  = document.getElementById('form-errors');
                const list = document.getElementById('form-errors-list');
                list.innerHTML = errors.map(function(msg) {
                    return '<li>' + msg + '</li>';
                }).join('');
                box.classList.add('visible');
                box.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });

        // Remove borda de erro ao corrigir o campo
        document.querySelectorAll('.field-error, select, textarea, input').forEach(function(el) {
            el.addEventListener('input',  function() { el.classList.remove('field-error'); });
            el.addEventListener('change', function() { el.classList.remove('field-error'); });
        });
    </script>
@endsection
