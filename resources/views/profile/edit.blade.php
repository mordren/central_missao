@extends('layouts.app')

@section('title', 'Meu Perfil - ONÇAS DO OESTE')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-5 sm:py-6 space-y-6">

    <h1 class="text-lg sm:text-xl font-bold text-white tracking-tight uppercase">Meu Perfil</h1>

    {{-- Success --}}
    @if(session('success'))
        <div class="bg-green-900/30 border border-green-800 text-green-400 px-4 py-3 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- Validation errors --}}
    @if($errors->any())
        <div class="bg-red-900/40 border border-red-500 text-red-300 px-4 py-3 rounded-lg text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" novalidate>
        @csrf
        @method('PATCH')

        {{-- Avatar section --}}
        <div class="bg-brand-dark-card border border-brand-dark-border rounded-2xl p-5 mb-5">
            <h2 class="text-sm font-bold text-brand-gray uppercase tracking-wider mb-4">Avatar</h2>

            <div class="flex items-center gap-5 mb-4">
                {{-- Preview --}}
                <div id="avatar-preview-wrap" class="flex-shrink-0">
                    @if($user->avatarSrc())
                        <img id="avatar-preview" src="{{ $user->avatarSrc() }}" alt="Avatar"
                             class="w-20 h-20 rounded-full object-cover border-2 border-brand-dark-border">
                    @else
                        <div id="avatar-placeholder"
                             class="w-20 h-20 rounded-full bg-brand-dark-input border-2 border-brand-dark-border flex items-center justify-center">
                            <span class="text-2xl font-bold text-brand-gray select-none">
                                {{ strtoupper(substr($user->displayName(), 0, 1)) }}
                            </span>
                        </div>
                        <img id="avatar-preview" src="" alt="Avatar"
                             class="w-20 h-20 rounded-full object-cover border-2 border-brand-dark-border hidden">
                    @endif
                </div>

                <div class="flex-1 min-w-0">
                    <label class="block text-sm text-brand-gray mb-1">Carregar imagem
                        <span class="text-xs text-brand-gray/60">(JPG, PNG, WebP — máx. 2 MB)</span>
                    </label>
                    <input type="file" name="avatar_file" id="avatar_file"
                           accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                           class="block w-full text-sm text-brand-gray
                                  file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0
                                  file:text-xs file:font-bold file:bg-brand-yellow file:text-brand-dark
                                  hover:file:bg-brand-yellow-hover file:cursor-pointer cursor-pointer">
                </div>
            </div>

            <div>
                <label class="block text-sm text-brand-gray mb-1">Ou informe uma URL de imagem externa</label>
                <input type="url" name="avatar_url" id="avatar_url"
                       value="{{ old('avatar_url', $user->avatar_url) }}"
                       placeholder="https://exemplo.com/meu-avatar.png"
                       class="w-full px-3 py-2 rounded-lg bg-brand-dark-input border border-brand-dark-border text-white text-sm
                              {{ $errors->has('avatar_url') ? 'border-red-500' : '' }}">
                @error('avatar_url')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
                <p class="text-xs text-brand-gray/60 mt-1">Se você carregar uma imagem, a URL será ignorada.</p>
            </div>
        </div>

        {{-- Personal data section --}}
        <div class="bg-brand-dark-card border border-brand-dark-border rounded-2xl p-5 mb-5">
            <h2 class="text-sm font-bold text-brand-gray uppercase tracking-wider mb-4">Dados Pessoais</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                {{-- Name --}}
                <div class="sm:col-span-2">
                    <label class="block text-sm text-brand-gray mb-1">Nome completo <span class="text-red-400">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                           class="w-full px-3 py-2 rounded-lg bg-brand-dark-input border text-white text-sm
                                  {{ $errors->has('name') ? 'border-red-500' : 'border-brand-dark-border' }}">
                    @error('name')
                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email (read-only) --}}
                <div class="sm:col-span-2">
                    <label class="block text-sm text-brand-gray mb-1">E-mail
                        <span class="text-xs text-brand-gray/60">(não editável)</span>
                    </label>
                    <input type="email" value="{{ $user->email }}" disabled readonly
                           class="w-full px-3 py-2 rounded-lg bg-brand-dark-input/50 border border-brand-dark-border text-brand-gray text-sm cursor-not-allowed">
                </div>

                {{-- Phone --}}
                <div>
                    <label class="block text-sm text-brand-gray mb-1">Telefone <span class="text-red-400">*</span></label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" required
                           class="w-full px-3 py-2 rounded-lg bg-brand-dark-input border text-white text-sm
                                  {{ $errors->has('phone') ? 'border-red-500' : 'border-brand-dark-border' }}">
                    @error('phone')
                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Nickname --}}
                <div>
                    <label class="block text-sm text-brand-gray mb-1">Apelido (nickname)</label>
                    <input type="text" name="nickname" id="nickname"
                           value="{{ old('nickname', $user->nickname) }}"
                           placeholder="ex: guerreiro_oeste"
                           minlength="3" maxlength="30"
                           class="w-full px-3 py-2 rounded-lg bg-brand-dark-input border text-white text-sm
                                  {{ $errors->has('nickname') ? 'border-red-500' : 'border-brand-dark-border' }}">
                    <p class="text-xs text-brand-gray/60 mt-1">3–30 caracteres. Letras (com acentos), números, espaços e _. Exibido no ranking.</p>
                    @error('nickname')
                        <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- City --}}
                <div>
                    <label class="block text-sm text-brand-gray mb-1">Cidade</label>
                    <input type="text" name="city" value="{{ old('city', $user->city) }}"
                           class="w-full px-3 py-2 rounded-lg bg-brand-dark-input border border-brand-dark-border text-white text-sm">
                </div>

                {{-- Neighborhood --}}
                <div>
                    <label class="block text-sm text-brand-gray mb-1">Bairro</label>
                    <input type="text" name="neighborhood" value="{{ old('neighborhood', $user->neighborhood) }}"
                           class="w-full px-3 py-2 rounded-lg bg-brand-dark-input border border-brand-dark-border text-white text-sm">
                </div>

            </div>
        </div>

        {{-- Actions --}}
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
            <button type="submit"
                    class="flex-1 sm:flex-none bg-brand-yellow hover:bg-brand-yellow-hover text-brand-dark font-bold py-3 px-6 rounded-lg transition uppercase tracking-wider text-sm">
                Salvar alterações
            </button>
            <a href="{{ route('password.change') }}"
               class="flex-1 sm:flex-none text-center bg-brand-dark-input hover:bg-brand-dark-border text-white font-semibold py-3 px-6 rounded-lg transition text-sm border border-brand-dark-border">
                Alterar senha
            </a>
        </div>

    </form>
</div>

<script>
    // Avatar file preview
    document.getElementById('avatar_file').addEventListener('change', function () {
        const file = this.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function (e) {
            const preview = document.getElementById('avatar-preview');
            const placeholder = document.getElementById('avatar-placeholder');
            preview.src = e.target.result;
            preview.classList.remove('hidden');
            if (placeholder) placeholder.classList.add('hidden');
            // Clear URL field when a file is chosen
            document.getElementById('avatar_url').value = '';
        };
        reader.readAsDataURL(file);
    });
</script>
@endsection
