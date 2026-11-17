<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Login - {{ config('app.name', 'Fotocopy Aditya') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700|playfair-display:600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">

    {{-- Latar: foto toko. Warna navy jadi cadangan kalau fotonya belum ada. --}}
    <div class="relative min-h-screen flex items-center justify-center p-4 bg-[#14285b] bg-cover bg-center bg-no-repeat"
         style="background-image: url('{{ asset('images/storelogin.png') }}');">

        {{-- Lapisan gelap tipis --}}
        <div class="absolute inset-0 bg-black/20"></div>

        {{-- Info toko (pojok kanan atas) --}}
        <div class="absolute top-3 right-3 sm:top-5 sm:right-5 max-w-[15rem] sm:max-w-xs rounded-xl bg-black/50 backdrop-blur-sm px-4 py-2.5 text-right text-white shadow-lg">
            <div class="text-sm font-semibold">Fotocopy Aditya</div>
            <div class="mt-0.5 text-xs leading-snug text-white/85">Jl.Kramatsari 1 No.12A, Kel.Kramatsari, Kec.Pekalongan Barat, Kota Pekalongan</div>
        </div>

        {{-- Kartu login: rasio 9:16, lebar maksimal 22.5rem (360px) --}}
        <div class="relative flex flex-col overflow-y-auto rounded-3xl border border-white/25 bg-black/35 backdrop-blur-xl shadow-2xl px-7 py-8"
             style="width: min(22.5rem, calc(88vh * 9 / 16), 92vw); aspect-ratio: 9 / 16;">

            <div class="my-auto">

                {{-- Judul --}}
                <div class="text-center">
                    <h1 class="inline-block rounded-2xl bg-slate-900/60 px-8 py-2 text-3xl text-white shadow"
                        style="font-family: 'Playfair Display', serif;">Login</h1>
                </div>

                <form method="POST" action="{{ route('login') }}" class="mt-7 space-y-4">
                    @csrf

                    @if (session('status'))
                        <div class="rounded-lg bg-green-600/90 px-3 py-2 text-xs text-white">{{ session('status') }}</div>
                    @endif

                    {{-- Email --}}
                    <div>
                        <label for="email"
                               class="inline-block rounded-lg bg-slate-900/70 px-3 py-1 text-xs font-semibold text-white shadow">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}"
                               required autofocus autocomplete="username" placeholder="nama@email.com"
                               class="mt-2 block w-full rounded-lg border-0 bg-white px-3 py-2.5 text-sm text-slate-900 placeholder-slate-400 shadow-sm focus:ring-2 focus:ring-amber-400">
                        @error('email')
                            <p class="mt-2 inline-block rounded-md bg-rose-600/90 px-2.5 py-1 text-xs text-white">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div>
                        <div class="flex items-center justify-between gap-2">
                            <label for="password"
                                   class="inline-block rounded-lg bg-slate-900/70 px-3 py-1 text-xs font-semibold text-white shadow">Password</label>

                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}"
                                   class="rounded-lg bg-slate-900/70 px-3 py-1 text-xs text-white/90 shadow hover:bg-slate-900/90 hover:text-white">Lupa password?</a>
                            @endif
                        </div>
                        <input id="password" type="password" name="password"
                               required autocomplete="current-password" placeholder="Masukkan password"
                               class="mt-2 block w-full rounded-lg border-0 bg-white px-3 py-2.5 text-sm text-slate-900 placeholder-slate-400 shadow-sm focus:ring-2 focus:ring-amber-400">
                        @error('password')
                            <p class="mt-2 inline-block rounded-md bg-rose-600/90 px-2.5 py-1 text-xs text-white">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Ingat saya --}}
                    <label class="inline-flex items-center gap-2 rounded-lg bg-slate-900/70 px-3 py-1.5 text-xs text-white shadow cursor-pointer">
                        <input type="checkbox" name="remember"
                               class="rounded border-white/40 text-amber-500 focus:ring-amber-400">
                        Ingat saya
                    </label>

                    {{-- Tombol --}}
                    <button type="submit"
                            class="w-full rounded-full bg-[#f5a623] py-3 text-sm font-bold tracking-wider text-white shadow-lg transition hover:bg-[#e69512] focus:outline-none focus:ring-2 focus:ring-amber-300">
                        LOGIN
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>