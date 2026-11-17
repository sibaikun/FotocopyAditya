<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

        <div>
            <h1 class="text-3xl font-bold text-slate-900">Dashboard</h1>
            <p class="text-slate-500 mt-1">Selamat datang di sistem Fotocopy Aditya!</p>
        </div>

        {{-- Kartu ringkasan --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <div class="flex items-center gap-4 bg-white p-5 rounded-2xl shadow-sm border border-slate-100">
                <div class="w-14 h-14 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center shrink-0">
                    <x-icon name="wallet" size="w-8 h-8" />
                </div>
                <div>
                    <div class="text-sm text-slate-500">Omzet Hari Ini</div>
                    <div class="text-2xl font-bold text-slate-900">Rp {{ number_format($omzetToday, 0, ',', '.') }}</div>
                </div>
            </div>

            <div class="flex items-center gap-4 bg-white p-5 rounded-2xl shadow-sm border border-slate-100">
                <div class="w-14 h-14 rounded-2xl bg-green-100 text-green-600 flex items-center justify-center shrink-0">
                    <x-icon name="document" size="w-8 h-8" />
                </div>
                <div>
                    <div class="text-sm text-slate-500">Jumlah Nota Hari Ini</div>
                    <div class="text-2xl font-bold text-slate-900">{{ $countToday }}</div>
                </div>
            </div>

            <div class="flex items-center gap-4 bg-white p-5 rounded-2xl shadow-sm border border-slate-100">
                <div class="w-14 h-14 rounded-2xl bg-indigo-100 text-indigo-600 flex items-center justify-center shrink-0">
                    <x-icon name="printer" size="w-8 h-8" />
                </div>
                <div>
                    <div class="text-sm text-slate-500">Omzet Bulan Ini</div>
                    <div class="text-2xl font-bold text-slate-900">Rp {{ number_format($omzetMonth, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>

        {{-- Transaksi terakhir --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-1.5 h-6 rounded-full bg-blue-600"></div>
                    <h2 class="text-lg font-semibold text-slate-900">Transaksi Terakhir</h2>
                </div>
                <a href="{{ route('transactions.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-50 text-blue-600 text-sm font-medium hover:bg-blue-100">
                    Lihat Semua <x-icon name="arrow" size="w-4 h-4" />
                </a>
            </div>

            <div class="overflow-x-auto rounded-xl border border-slate-100">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 text-left text-slate-700">
                        <tr>
                            <th class="px-5 py-3 font-semibold">No. Nota</th>
                            <th class="px-5 py-3 font-semibold">Tanggal</th>
                            <th class="px-5 py-3 font-semibold">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($latest as $t)
                            <tr class="hover:bg-slate-50">
                                <td class="px-5 py-3">
                                    <a href="{{ route('transactions.show', $t) }}" class="text-slate-800 hover:text-blue-600">
                                        {{ $t->invoice_no }}
                                    </a>
                                </td>
                                <td class="px-5 py-3">{{ $t->created_at->translatedFormat('d M Y, H:i') }}</td>
                                <td class="px-5 py-3">Rp {{ number_format($t->total, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-5 py-6 text-center text-slate-500">Belum ada transaksi.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Stok menipis --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-1.5 h-6 rounded-full bg-amber-500"></div>
                <h2 class="text-lg font-semibold text-slate-900">Stok Menipis</h2>
            </div>

            @forelse ($lowStock as $p)
                <div class="flex justify-between text-sm py-2 border-b border-slate-100 last:border-0">
                    <span>{{ $p->name }}</span>
                    <span class="{{ $p->stock == 0 ? 'text-red-600 font-semibold' : 'text-slate-600' }}">
                        {{ $p->stock }} {{ $p->unit }}
                    </span>
                </div>
            @empty
                <p class="text-sm text-slate-500">Semua stok aman.</p>
            @endforelse
        </div>
    </div>
</x-app-layout>