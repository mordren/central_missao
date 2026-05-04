<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ONÇAS DO OESTE')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            yellow: '#FFD600',
                            'yellow-hover': '#FFC107',
                            'yellow-light': '#FFF176',
                            dark: '#0A0A0A',
                            'dark-card': '#141414',
                            'dark-input': '#1A1A1A',
                            'dark-border': '#2A2A2A',
                            gray: '#888888',
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        html { -webkit-text-size-adjust: 100%; }
        *, *::before, *::after { box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; }
        img, svg, video, canvas { max-width: 100%; height: auto; }
        input:-webkit-autofill,
        input:-webkit-autofill:hover,
        input:-webkit-autofill:focus {
            -webkit-box-shadow: 0 0 0 30px #1A1A1A inset !important;
            -webkit-text-fill-color: #ffffff !important;
            caret-color: #ffffff;
        }
        select option { background: #1A1A1A; color: #fff; }
        @media (max-width: 1023px) {
            input, select, textarea { font-size: 16px !important; }
        }
    </style>
    @yield('head')
</head>
<body class="@yield('bodyClass', 'bg-brand-dark min-h-screen text-white') overflow-x-hidden">

    @auth
        {{-- LAYOUT COM SIDEBAR --}}
        <div class="flex min-h-screen">
            {{-- Sidebar desktop (fixa) --}}
            <aside id="sidebar" class="hidden lg:flex flex-col w-64 bg-brand-dark-card border-r border-brand-dark-border fixed inset-y-0 left-0 z-30">
                @include('layouts.partials.sidebar-content')
            </aside>

            {{-- Sidebar mobile (overlay) --}}
            <div id="mobile-overlay" class="fixed inset-0 bg-black/60 z-40 hidden lg:hidden" onclick="toggleSidebar()"></div>
            <aside id="mobile-sidebar" class="fixed inset-y-0 left-0 w-[85vw] max-w-xs bg-brand-dark-card border-r border-brand-dark-border z-50 transform -translate-x-full transition-transform duration-300 lg:hidden flex flex-col">
                <div class="flex items-center justify-end p-4">
                    <button onclick="toggleSidebar()" class="text-brand-gray hover:text-brand-yellow transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                @include('layouts.partials.sidebar-content')
            </aside>

            {{-- Conteúdo principal --}}
            <div class="flex-1 lg:ml-64 flex flex-col min-h-screen">
                {{-- Top bar mobile --}}
                <header class="lg:hidden bg-brand-dark-card border-b border-brand-dark-border px-4 py-3 flex items-center justify-between sticky top-0 z-20">
                    <button onclick="toggleSidebar()" class="text-brand-gray hover:text-brand-yellow transition p-1">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2 min-w-0 hover:opacity-80 transition">
                        <img src="{{ asset('public/images/logo.png') }}" alt="ONÇAS DO OESTE" class="h-8 w-auto flex-shrink-0">
                        <span class="font-bold text-sm tracking-tight truncate">ONÇAS DO OESTE</span>

                    </a>
                    <div class="w-8"></div>
                </header>

                {{-- Conteúdo da página --}}
                <main class="flex-1 w-full">
                    @yield('content')
                </main>
            </div>
        </div>

        <script>
            function toggleSidebar() {
                const sidebar = document.getElementById('mobile-sidebar');
                const overlay = document.getElementById('mobile-overlay');
                const isOpen = !sidebar.classList.contains('-translate-x-full');
                if (isOpen) {
                    sidebar.classList.add('-translate-x-full');
                    overlay.classList.add('hidden');
                } else {
                    sidebar.classList.remove('-translate-x-full');
                    overlay.classList.remove('hidden');
                }
            }
        </script>
    @else
        {{-- LAYOUT SEM SIDEBAR (login/registro) --}}
        <div class="min-h-screen flex flex-col">
            @yield('content')
        </div>
    @endauth
@auth
{{-- Banner de permissão de notificações push --}}
<div id="push-box"
     class="fixed bottom-4 left-1/2 -translate-x-1/2 z-50 w-[calc(100%-2rem)] max-w-sm
            bg-brand-dark-card border border-brand-dark-border rounded-xl shadow-xl
            flex items-center gap-3 px-4 py-3 text-sm"
     style="display:none!important">
    <svg class="w-5 h-5 text-brand-yellow flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
        <path d="M10 2a6 6 0 00-6 6v2.586l-.707.707A1 1 0 004 13h12a1 1 0 00.707-1.707L16 10.586V8a6 6 0 00-6-6zm0 16a2 2 0 01-2-2h4a2 2 0 01-2 2z"/>
    </svg>
    <span class="flex-1 text-white">Receber avisos importantes?</span>
    <button onclick="ativarPush()"
            class="bg-brand-yellow text-brand-dark font-semibold text-xs px-3 py-1.5 rounded-lg hover:bg-brand-yellow-hover transition">
        Ativar
    </button>
    <button onclick="fecharPushBox()"
            class="text-brand-gray hover:text-white text-xs px-2 py-1.5 transition">
        Agora não
    </button>
</div>
@endauth

<script type="module">
import { initializeApp, getApps, getApp } from "https://www.gstatic.com/firebasejs/12.12.1/firebase-app.js";
import { getMessaging, getToken, onMessage } from "https://www.gstatic.com/firebasejs/12.12.1/firebase-messaging.js";

const firebaseConfig = {
    apiKey:            "AIzaSyCl6wDjBt69wm4tLZMlui6B-1QCDtqs58M",
    authDomain:        "central-4a98b.firebaseapp.com",
    projectId:         "central-4a98b",
    storageBucket:     "central-4a98b.firebasestorage.app",
    messagingSenderId: "438092450467",
    appId:             "1:438092450467:web:0458699fb2c5c26d378d16",
    measurementId:     "G-Z097BMHD4J",
};
const vapidKey = "BBOosSnqmEUfkGyeDqfORYj9pXSIj-MQ5-a6yqIkK-5rknrIh4ZPFMl0ZxxxaIiZiqcDxUZxZ7fwFgQppTvD1bg";

// Garante que initializeApp não é chamado duas vezes
let _fcmMessaging = null; // instância única — evita múltiplos onMessage

function getFirebase() {
    if (_fcmMessaging) return _fcmMessaging;

    const app = getApps().length ? getApp() : initializeApp(firebaseConfig);
    _fcmMessaging = getMessaging(app);

    // FOREGROUND: usa localStorage partilhado entre abas para mostrar só 1 notificação
    onMessage(_fcmMessaging, function (payload) {
        const msgId  = payload.fcmMessageId || JSON.stringify(payload.notification);
        const lsKey  = 'fcm_shown_' + msgId;
        const already = localStorage.getItem(lsKey);
        if (already) return; // outra aba já processou
        localStorage.setItem(lsKey, '1');
        setTimeout(() => localStorage.removeItem(lsKey), 5000);

        const title = (payload.notification && payload.notification.title) || 'ONÇAS DO OESTE';
        const body  = (payload.notification && payload.notification.body)  || '';
        if (Notification.permission === 'granted') {
            navigator.serviceWorker.getRegistration('/firebase-messaging-sw.js').then(function (reg) {
                if (reg) reg.showNotification(title, {
                    body,
                    icon:  'https://grey-finch-461274.hostingersite.com/images/logo.png',
                    badge: 'https://grey-finch-461274.hostingersite.com/images/logo.png',
                    data:  payload.data || {},
                    tag:   msgId,
                });
            });
        }
    });

    return _fcmMessaging;
}

async function registarSW() {
    if (!('serviceWorker' in navigator)) return null;
    try {
        const reg = await navigator.serviceWorker.register('/firebase-messaging-sw.js', { scope: '/' });
        await navigator.serviceWorker.ready;
        return reg;
    } catch (e) {
        console.error('Erro ao registar service worker:', e);
        return null;
    }
}

async function salvarToken() {
    try {
        const swReg = await registarSW();
        if (!swReg) { console.warn('FCM: service worker não registado'); return; }

        // Cancelar subscrição push antiga (VAPID key diferente causa "push service error")
        const existingSub = await swReg.pushManager.getSubscription();
        if (existingSub) await existingSub.unsubscribe();

        const token = await getToken(getFirebase(), { vapidKey, serviceWorkerRegistration: swReg });
        if (!token) { console.warn('FCM: token vazio'); return; }
        await fetch('/salvar-token', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ token }),
        });
        localStorage.setItem('push_subscribed', '1');
    } catch (e) {
        if (e && e.name === 'AbortError' && e.message && e.message.includes('push service')) {
            console.warn('FCM: push service bloqueado pelo browser. No Brave, ative "Use Google services for push messaging" em brave://settings/privacy');
        } else {
            console.error('Erro ao salvar token push:', e);
        }
    }
}

window.ativarPush = async function () {
    fecharPushBox();
    if (!('Notification' in window)) return;
    const permission = await Notification.requestPermission();
    if (permission !== 'granted') return;
    await salvarToken();
};

window.fecharPushBox = function () {
    const box = document.getElementById('push-box');
    if (box) box.style.setProperty('display', 'none', 'important');
    localStorage.setItem('push_dismissed_until', Date.now() + 1 * 24 * 60 * 60 * 1000);
};

function initBanner() {
    const box = document.getElementById('push-box');
    if (!box) return;

    // Push só funciona em HTTPS (ou localhost)
    if (location.protocol !== 'https:' && location.hostname !== 'localhost') return;

    // Notificações bloqueadas pelo utilizador — nada a fazer
    if (Notification.permission === 'denied') return;

    // Se a permissão foi revogada, limpar flag
    if (Notification.permission !== 'granted') {
        localStorage.removeItem('push_subscribed');
    }

    const permission     = Notification.permission;
    const subscribed     = localStorage.getItem('push_subscribed');
    const dismissedUntil = parseInt(localStorage.getItem('push_dismissed_until') || '0', 10);

    if (permission === 'granted' && !subscribed) {
        // Permissão dada mas token perdido (ex: SW substituído)
        salvarToken();
    } else if (permission !== 'granted' && !subscribed && Date.now() > dismissedUntil) {
        // Mostrar banner
        box.style.removeProperty('display');
    } else if (permission === 'granted' && subscribed) {
        // Verifica se o token ainda está registado no servidor
        registarSW().then(async function(swReg) {
            if (!swReg) return;
            try {
                const currentToken = await getToken(getFirebase(), { vapidKey, serviceWorkerRegistration: swReg });
                if (!currentToken) { localStorage.removeItem('push_subscribed'); return; }
                const res = await fetch('/check-token', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ token: currentToken }),
                });
                const json = await res.json();
                if (!json.registered) {
                    // Token não existe no servidor — re-registar silenciosamente
                    localStorage.removeItem('push_subscribed');
                    await salvarToken();
                }
            } catch (e) { /* silencioso */ }
        });
        getFirebase();
    }
}

// ?reset-push=1 na URL limpa o estado local — útil para forçar re-registo em mobile sem consola
if (new URLSearchParams(location.search).get('reset-push') === '1') {
    localStorage.removeItem('push_subscribed');
    localStorage.removeItem('push_dismissed_until');
    // Remove o parâmetro da URL sem recarregar
    const url = new URL(location.href);
    url.searchParams.delete('reset-push');
    history.replaceState(null, '', url.toString());
}

// CORREÇÃO CRÍTICA: type="module" com imports remotos resolve os imports de forma assíncrona.
// O DOMContentLoaded pode já ter disparado quando o módulo termina de carregar.
// Usamos readyState para garantir que initBanner() é sempre chamado.
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initBanner);
} else {
    initBanner();
}
</script>

{{-- DropZone utility (drag-drop + Ctrl+V paste for file inputs) --}}
<script>
(function () {
    function setFile(inputId, file) {
        var input = document.getElementById(inputId);
        if (!input) return;
        var dt = new DataTransfer();
        dt.items.add(file);
        input.files = dt.files;
        input.dispatchEvent(new Event('change'));
    }

    function showPreview(inputId, file) {
        var hint    = document.getElementById('dz-hint-' + inputId);
        var preview = document.getElementById('dz-preview-' + inputId);
        var img     = document.getElementById('dz-img-' + inputId);
        var icon    = document.getElementById('dz-fileicon-' + inputId);
        var name    = document.getElementById('dz-name-' + inputId);
        if (!preview) return;
        if (hint) hint.classList.add('hidden');
        preview.classList.remove('hidden');
        if (name) name.textContent = file.name;
        if (file.type.startsWith('image/')) {
            if (img) { img.classList.remove('hidden'); var r = new FileReader(); r.onload = function(e){ img.src = e.target.result; }; r.readAsDataURL(file); }
            if (icon) icon.classList.add('hidden');
        } else {
            if (img) img.classList.add('hidden');
            if (icon) icon.classList.remove('hidden');
        }
    }

    window.dzDragOver = function (e, el) {
        e.preventDefault();
        el.classList.add('!border-brand-yellow', 'bg-brand-yellow/5');
    };

    window.dzDragLeave = function (e, el) {
        el.classList.remove('!border-brand-yellow', 'bg-brand-yellow/5');
    };

    window.dzDrop = function (e, el, inputId) {
        e.preventDefault();
        dzDragLeave(e, el);
        var files = e.dataTransfer.files;
        if (!files.length) return;
        setFile(inputId, files[0]);
        showPreview(inputId, files[0]);
    };

    window.dzFileSelected = function (input, inputId) {
        if (!input.files.length) return;
        showPreview(inputId, input.files[0]);
    };

    window.dzClear = function (e, inputId) {
        e.stopPropagation();
        var input   = document.getElementById(inputId);
        var hint    = document.getElementById('dz-hint-' + inputId);
        var preview = document.getElementById('dz-preview-' + inputId);
        var img     = document.getElementById('dz-img-' + inputId);
        if (input)   input.value = '';
        if (hint)    hint.classList.remove('hidden');
        if (preview) preview.classList.add('hidden');
        if (img)     img.src = '';
    };

    // Ctrl+V paste — only when a drop zone div is focused
    document.addEventListener('paste', function (e) {
        var active = document.activeElement;
        if (!active || !active.dataset.dzTarget) return;
        var inputId = active.dataset.dzTarget;
        var items = e.clipboardData && e.clipboardData.items;
        if (!items) return;
        for (var i = 0; i < items.length; i++) {
            if (items[i].type.startsWith('image/')) {
                var file = items[i].getAsFile();
                if (file) { setFile(inputId, file); showPreview(inputId, file); break; }
            }
        }
    });
}());
</script>
</body>
</html>
