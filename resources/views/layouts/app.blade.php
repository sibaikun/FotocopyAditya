<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Fotocopy Aditya') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @verbatim
    <style>
        [x-cloak] { display: none !important; }

        /* Di desktop sidebar langsung tampil, nggak perlu nunggu Alpine */
        @media (min-width: 1024px) {
            aside[x-cloak] { display: flex !important; }
        }

        /* Aktifkan transisi antar halaman */
        @view-transition { navigation: auto; }

        /* Bagian yang tetap diam saat pindah halaman */
        .vt-sidebar { view-transition-name: sidebar; }
        .vt-topbar  { view-transition-name: topbar; }
        /* Bagian yang bergeser */
        .vt-page    { view-transition-name: page; }

        /* Latar belakang jangan ikut cross-fade (sumber kedip) */
        ::view-transition-old(root),
        ::view-transition-new(root) { animation: none; }

        /* Isi halaman: lama geser ke kiri, baru masuk dari kanan */
        ::view-transition-group(page) { animation: none; }

        ::view-transition-old(page) {
            animation: 200ms ease-in both vt-slide-out;
        }
        ::view-transition-new(page) {
            animation: 300ms ease-out both vt-slide-in;
        }

        @keyframes vt-slide-out {
            to { opacity: 0; transform: translateX(-60px); }
        }
        @keyframes vt-slide-in {
            from { opacity: 0; transform: translateX(60px); }
        }

        /* Hormati pengguna yang mematikan animasi di sistemnya */
        @media (prefers-reduced-motion: reduce) {
            ::view-transition-group(*),
            ::view-transition-old(*),
            ::view-transition-new(*) { animation: none !important; }
        }

        /* Scrollbar sidebar: tipis dan senada dengan navy */
        .sidebar-scroll {
            scrollbar-width: thin;                                   /* Firefox & Chrome terbaru */
            scrollbar-color: rgba(255, 255, 255, 0.18) transparent;  /* warna batang, warna jalur */
        }
        .sidebar-scroll::-webkit-scrollbar { width: 6px; }
        .sidebar-scroll::-webkit-scrollbar-track { background: transparent; }
        .sidebar-scroll::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.18);
            border-radius: 9999px;
        }
        .sidebar-scroll::-webkit-scrollbar-thumb:hover { background: rgba(255, 255, 255, 0.35); }
        .sidebar-scroll::-webkit-scrollbar-button { display: none; } /* hilangkan panah atas-bawah */
    </style>
    @endverbatim
</head>
<body class="font-sans antialiased bg-slate-50 text-slate-800">
<div x-data="{ sidebarOpen: false }" class="min-h-screen">

    {{-- Overlay (khusus layar kecil) --}}
    <div x-cloak x-show="sidebarOpen" @click="sidebarOpen = false"
         class="fixed inset-0 z-30 bg-black/40 lg:hidden"></div>

    {{-- Sidebar --}}
    <aside x-cloak
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
           class="vt-sidebar fixed inset-y-0 left-0 z-40 w-64 bg-[#14285b] text-white flex flex-col transition-transform duration-200">

        <div class="px-6 pt-8 pb-6 text-center">
            <div class="mx-auto w-14 h-14 rounded-2xl bg-white/10 flex items-center justify-center">
                <x-icon name="printer" size="w-9 h-9" />
            </div>
            <div class="mt-3 text-xl font-bold leading-tight">Fotocopy<br>Aditya</div>
        </div>

        <div class="mx-6 border-t border-white/10"></div>

        <nav class="sidebar-scroll flex-1 overflow-y-auto px-4 py-6 space-y-2">
            <x-sidebar-link :href="route('dashboard')" icon="home" :active="request()->routeIs('dashboard')">Dashboard</x-sidebar-link>
            <x-sidebar-link :href="route('transactions.create')" icon="cart" :active="request()->routeIs('transactions.create')">Kasir</x-sidebar-link>
            <x-sidebar-link :href="route('products.index')" icon="cube" :active="request()->routeIs('products.*')">Barang</x-sidebar-link>
            <x-sidebar-link :href="route('stock-in.index')" icon="download" :active="request()->routeIs('stock-in.*')">Barang Masuk</x-sidebar-link>
            <x-sidebar-link :href="route('stock-out.index')" icon="upload" :active="request()->routeIs('stock-out.*')">Barang Keluar</x-sidebar-link>
            <x-sidebar-link :href="route('services.index')" icon="tag" :active="request()->routeIs('services.*')">Jasa</x-sidebar-link>
            <x-sidebar-link :href="route('transactions.index')" icon="document" :active="request()->routeIs('transactions.index', 'transactions.show')">Nota</x-sidebar-link>
            <x-sidebar-link :href="route('reports.index')" icon="chart" :active="request()->routeIs('reports.*')">Laporan</x-sidebar-link>
            <x-sidebar-link :href="route('profile.edit')" icon="cog" :active="request()->routeIs('profile.*')">Pengaturan</x-sidebar-link>
        </nav>

        <div class="mx-6 border-t border-white/10"></div>
        <div class="px-6 py-5 text-center text-xs text-blue-100/60">
            © {{ date('Y') }} Fotocopy Aditya
        </div>
    </aside>

    {{-- Konten --}}
    <div class="lg:pl-64">

        {{-- Topbar --}}
        <div class="vt-topbar sticky top-0 z-20 h-16 bg-white shadow-sm flex items-center px-4 sm:px-6">
            <button @click="sidebarOpen = ! sidebarOpen" class="lg:hidden p-2 rounded-lg text-slate-600 hover:bg-slate-100">
                <x-icon name="menu" size="w-7 h-7" />
            </button>

            <div class="ms-auto">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="flex items-center gap-2 text-sm font-medium text-slate-700 hover:text-slate-900 focus:outline-none">
                            <x-icon name="user" size="w-9 h-9" class="text-slate-800" />
                            <span class="hidden sm:inline">{{ Auth::user()->name }}</span>
                            <x-icon name="chevron" size="w-4 h-4" />
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">Profil</x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                Keluar
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>
        </div>

        {{-- Bagian yang bergeser saat pindah halaman --}}
        <div class="vt-page">
            @isset($header)
                <header class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8">
                    {{ $header }}
                </header>
            @endisset

            <main>
                {{ $slot }}
            </main>
        </div>
    </div>
</div>
</body>
</html>