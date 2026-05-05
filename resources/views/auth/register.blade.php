@extends('layouts.app')

@section('title', 'Cadastro - ONÇAS DO OESTE')

@section('content')
<div class="flex-1 flex items-center justify-center px-4 py-8 sm:py-12">
    <div class="w-full max-w-md">
        {{-- Logo / Header --}}
        <div class="text-center mb-10">
            <img src="{{ asset('public/images/logo.png') }}" alt="ONÇAS DO OESTE" class="h-24 w-auto mx-auto mb-5">
            <h1 class="text-3xl font-extrabold text-white tracking-tight">CRIAR CONTA</h1>
            <p class="text-brand-gray mt-2 text-sm">Preencha seus dados para se cadastrar</p>
        </div>

        {{-- Card de Cadastro --}}
        <div class="bg-brand-dark-card border border-brand-dark-border rounded-2xl p-6 sm:p-8">
            {{-- Mensagem de erro geral --}}
            @if ($errors->any())
                <div class="mb-6 bg-red-900/30 border border-red-800 text-red-400 px-4 py-3 rounded-lg text-sm">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}">
                @csrf

                {{-- Campo Nome --}}
                <div class="mb-5">
                    <label for="name" class="block text-sm font-semibold text-brand-gray mb-2 uppercase tracking-wider">Nome completo</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-brand-gray" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name') }}"
                            placeholder="Seu nome completo"
                            required
                            autofocus
                            class="block w-full pl-10 pr-4 py-3 bg-brand-dark-input border border-brand-dark-border rounded-lg text-white placeholder-brand-gray/60 focus:outline-none focus:ring-2 focus:ring-brand-yellow focus:border-brand-yellow transition @error('name') border-red-500 @enderror"
                        >
                    </div>
                    @error('name')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Campo Telefone --}}
                <div class="mb-5">
                    <label for="phone_display" class="block text-sm font-semibold text-brand-gray mb-2 uppercase tracking-wider">Telefone <span class="text-red-400">*</span></label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-brand-gray" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                        </div>
                        {{-- Display field with mask (not submitted) --}}
                        <input
                            type="tel"
                            id="phone_display"
                            placeholder="(45) 99999-9999"
                            maxlength="15"
                            inputmode="tel"
                            autocomplete="tel"
                            class="block w-full pl-10 pr-4 py-3 bg-brand-dark-input border border-brand-dark-border rounded-lg text-white placeholder-brand-gray/60 focus:outline-none focus:ring-2 focus:ring-brand-yellow focus:border-brand-yellow transition @error('phone') border-red-500 @enderror"
                        >
                        {{-- Hidden real field with digits only --}}
                        <input type="hidden" id="phone" name="phone" value="{{ old('phone') }}">
                    </div>
                    @error('phone')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Campo E-mail --}}
                <div class="mb-5">
                    <label for="email" class="block text-sm font-semibold text-brand-gray mb-2 uppercase tracking-wider">E-mail <span class="text-red-400">*</span></label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-brand-gray" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="seu@email.com"
                            required
                            class="block w-full pl-10 pr-4 py-3 bg-brand-dark-input border border-brand-dark-border rounded-lg text-white placeholder-brand-gray/60 focus:outline-none focus:ring-2 focus:ring-brand-yellow focus:border-brand-yellow transition @error('email') border-red-500 @enderror"
                        >
                    </div>
                    @error('email')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Cidade e Bairro lado a lado --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
                    <div>
                        <label for="city" class="block text-sm font-semibold text-brand-gray mb-2 uppercase tracking-wider">Cidade</label>
                        <input
                            type="text"
                            id="city"
                            name="city"
                            value="{{ old('city') }}"
                            placeholder="Sua cidade"
                            required
                            class="block w-full px-4 py-3 bg-brand-dark-input border border-brand-dark-border rounded-lg text-white placeholder-brand-gray/60 focus:outline-none focus:ring-2 focus:ring-brand-yellow focus:border-brand-yellow transition @error('city') border-red-500 @enderror"
                        >
                        @error('city')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="neighborhood" class="block text-sm font-semibold text-brand-gray mb-2 uppercase tracking-wider">Bairro</label>
                        <input
                            type="text"
                            id="neighborhood"
                            name="neighborhood"
                            value="{{ old('neighborhood') }}"
                            placeholder="Seu bairro"
                            required
                            class="block w-full px-4 py-3 bg-brand-dark-input border border-brand-dark-border rounded-lg text-white placeholder-brand-gray/60 focus:outline-none focus:ring-2 focus:ring-brand-yellow focus:border-brand-yellow transition @error('neighborhood') border-red-500 @enderror"
                        >
                        @error('neighborhood')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Código de indicação --}}
                <div class="mb-5">
                    <label for="referral_code_input" class="block text-sm font-semibold text-brand-gray mb-2 uppercase tracking-wider">Código de indicação <span class="text-brand-gray/50 normal-case">(opcional)</span></label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-brand-gray" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <input
                            type="text"
                            id="referral_code_input"
                            name="referral_code_input"
                            value="{{ old('referral_code_input', request('ref')) }}"
                            placeholder="Ex: A1B2C3D4"
                            {{ request('ref') ? 'readonly' : '' }}
                            class="block w-full pl-10 pr-4 py-3 bg-brand-dark-input border border-brand-dark-border rounded-lg text-white placeholder-brand-gray/60 focus:outline-none focus:ring-2 focus:ring-brand-yellow focus:border-brand-yellow transition {{ request('ref') ? 'opacity-75 cursor-not-allowed' : '' }}"
                        >
                    </div>
                    @if(request('ref'))
                        <p class="text-xs text-brand-yellow mt-1">Você foi convidado! Código aplicado automaticamente.</p>
                    @endif
                </div>

                {{-- Campo Senha --}}
                <div class="mb-5">
                    <label for="password" class="block text-sm font-semibold text-brand-gray mb-2 uppercase tracking-wider">Senha</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-brand-gray" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Mínimo 8 caracteres"
                            required
                            class="block w-full pl-10 pr-12 py-3 bg-brand-dark-input border border-brand-dark-border rounded-lg text-white placeholder-brand-gray/60 focus:outline-none focus:ring-2 focus:ring-brand-yellow focus:border-brand-yellow transition @error('password') border-red-500 @enderror"
                        >
                        <button type="button" onclick="togglePasswordVisibility('password')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-brand-gray hover:text-brand-yellow transition">
                            <svg id="password-eye" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Campo Confirmar Senha --}}
                <div class="mb-6">
                    <label for="password_confirmation" class="block text-sm font-semibold text-brand-gray mb-2 uppercase tracking-wider">Confirmar senha</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-brand-gray" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            placeholder="Repita a senha"
                            required
                            class="block w-full pl-10 pr-4 py-3 bg-brand-dark-input border border-brand-dark-border rounded-lg text-white placeholder-brand-gray/60 focus:outline-none focus:ring-2 focus:ring-brand-yellow focus:border-brand-yellow transition"
                        >
                    </div>
                </div>

                {{-- Botão Cadastrar --}}
                <button type="submit" class="w-full bg-brand-yellow hover:bg-brand-yellow-hover text-brand-dark font-bold py-3.5 px-4 rounded-lg transition duration-200 focus:outline-none focus:ring-2 focus:ring-brand-yellow focus:ring-offset-2 focus:ring-offset-brand-dark uppercase tracking-wider text-sm">
                    Cadastrar
                </button>
            </form>
        </div>

        {{-- Link para login --}}
        <p class="text-center text-sm text-brand-gray mt-6">
            Já tem uma conta?
            <a href="{{ route('login') }}" class="text-brand-yellow hover:text-brand-yellow-light font-semibold">Entrar</a>
        </p>

        {{-- Linha decorativa --}}
        <div class="mt-8 flex items-center justify-center">
            <div class="h-px w-12 bg-brand-dark-border"></div>
            <span class="mx-3 text-xs text-brand-gray/50 uppercase tracking-widest">Missão</span>
            <div class="h-px w-12 bg-brand-dark-border"></div>
        </div>
    </div>
</div>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // ── Name: no digits ──────────────────────────────────────
                const nameInput = document.getElementById('name');
                if (nameInput) {
                    nameInput.addEventListener('keydown', function (e) {
                        if (e.ctrlKey || e.metaKey || e.altKey) return;
                        if (e.key && e.key.length === 1 && /[0-9]/.test(e.key)) e.preventDefault();
                    });
                    nameInput.addEventListener('input', function () {
                        const pos = this.selectionStart;
                        const cleaned = this.value.replace(/[0-9]/g, '');
                        if (cleaned !== this.value) {
                            this.value = cleaned;
                            try { this.setSelectionRange(Math.max(pos - 1, 0), Math.max(pos - 1, 0)); } catch (e) {}
                        }
                    });
                    nameInput.addEventListener('paste', function (e) {
                        e.preventDefault();
                        const paste = (e.clipboardData || window.clipboardData).getData('text');
                        const sanitized = paste.replace(/[0-9]/g, '');
                        const s = this.selectionStart, end = this.selectionEnd;
                        this.value = this.value.slice(0, s) + sanitized + this.value.slice(end);
                        try { this.setSelectionRange(s + sanitized.length, s + sanitized.length); } catch (e) {}
                    });
                }

                // ── Phone mask: (DD) 9XXXX-XXXX ─────────────────────────
                const phoneDisplay = document.getElementById('phone_display');
                const phoneHidden  = document.getElementById('phone');

                function applyPhoneMask(raw) {
                    // keep digits only, cap at 11
                    const d = raw.replace(/\D/g, '').slice(0, 11);
                    if (d.length === 0) return '';
                    if (d.length <= 2)  return '(' + d;
                    if (d.length <= 7)  return '(' + d.slice(0, 2) + ') ' + d.slice(2);
                    if (d.length <= 10) return '(' + d.slice(0, 2) + ') ' + d.slice(2, 6) + '-' + d.slice(6);
                    return '(' + d.slice(0, 2) + ') ' + d.slice(2, 7) + '-' + d.slice(7);
                }

                // Pre-fill display from hidden (old value on validation error)
                if (phoneHidden && phoneHidden.value) {
                    phoneDisplay.value = applyPhoneMask(phoneHidden.value);
                }

                if (phoneDisplay) {
                    phoneDisplay.addEventListener('input', function () {
                        const caret = this.selectionStart;
                        const digsBefore = this.value.slice(0, caret).replace(/\D/g, '').length;
                        const masked = applyPhoneMask(this.value);
                        this.value = masked;
                        // restore caret approximately
                        let newCaret = 0, dCount = 0;
                        for (let i = 0; i < masked.length; i++) {
                            if (/\d/.test(masked[i])) dCount++;
                            if (dCount >= digsBefore) { newCaret = i + 1; break; }
                        }
                        try { this.setSelectionRange(newCaret, newCaret); } catch (e) {}
                        phoneHidden.value = masked.replace(/\D/g, '');
                    });

                    phoneDisplay.addEventListener('keydown', function (e) {
                        // allow only digits, navigation and control keys
                        if (e.ctrlKey || e.metaKey || e.altKey) return;
                        if (e.key && e.key.length === 1 && /[^0-9]/.test(e.key)) e.preventDefault();
                    });
                }

                // ── On submit: validate phone has 11 digits ──────────────
                const form = phoneDisplay && phoneDisplay.closest('form');
                if (form) {
                    form.addEventListener('submit', function () {
                        if (phoneHidden) phoneHidden.value = phoneHidden.value.replace(/\D/g, '');
                    });
                }
            });
        </script>

        <script>
            function togglePasswordVisibility(fieldId) {
                const input = document.getElementById(fieldId);
                const icon = document.getElementById(fieldId + '-eye');
                
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>';
                } else {
                    input.type = 'password';
                    icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>';
                }
            }
        </script>

    @endsection
