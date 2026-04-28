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
                    <div class="flex items-center gap-2 min-w-0">
                        <img src="{{ asset('public/images/logo.png') }}" alt="ONÇAS DO OESTE" class="h-8 w-auto flex-shrink-0">
                        <span class="font-bold text-sm tracking-tight truncate">ONÇAS DO OESTE</span>
                    </div>
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

</body>
</html>
