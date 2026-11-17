<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

        {{-- Judul --}}
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-slate-900">Barang Masuk</h1>
                <p class="text-slate-500 mt-1">Riwayat belanja stok dan pengeluarannya.</p>
            </div>
            <a href="{{ route('stock-in.create') }}"
               class="inline-flex items-center px-5 py-2.5 rounded-xl bg-blue-600 text-white text-sm font-semibold shadow-sm hover:bg-blue-700">
                + Tambah Barang Masuk
            </a>
        </div>

        @if (session('success'))
            <div class="p-3 rounded-xl bg-green-100 text-green-800 text-sm">{{ session('success') }}</div>
        @endif

        {{-- Ringkasan --}}
        <div class="flex items-center gap-4 bg-white p-5 rounded-2xl shadow-sm border border-slate-100 w-full sm:w-96">
            <div class="w-14 h-14 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
                <x-icon name="download" size="w-8 h-8" />
            </div>
            <div>
                <div class="text-sm text-slate-500">Pengeluaran Belanja Stok Bulan Ini</div>
                <div class="text-2xl font-bold text-slate-900">Rp {{ number_format($spentMonth, 0, ',', '.') }}</div>
            </div>
        </div>

        {{-- Daftar riwayat --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-5 py-3 font-semibold">Tanggal</th>
                            <th class="px-5 py-3 font-semibold">Barang</th>
                            <th class="px-5 py-3 font-semibold">Jumlah Dibeli</th>
                            <th class="px-5 py-3 font-semibold">Masuk ke Stok</th>
                            <th class="px-5 py-3 font-semibold">Total Beli</th>
                            <th class="px-5 py-3 font-semibold">Dibeli dari</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($movements as $m)
                            <tr class="hover:bg-slate-50">
                                <td class="px-5 py-4 whitespace-nowrap text-slate-700">
                                    {{ \Carbon\Carbon::parse($m->date)->translatedFormat('d M Y') }}
                                </td>
                                <td class="px-5 py-4 font-medium text-slate-900">{{ $m->product->name }}</td>
                                <td class="px-5 py-4 whitespace-nowrap text-slate-700">{{ $m->input_qty }} {{ $m->input_unit }}</td>
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                        +{{ $m->qty }} {{ $m->product->unit }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap text-slate-900">
                                    {{ $m->total_cost ? 'Rp ' . number_format($m->total_cost, 0, ',', '.') : '-' }}
                                </td>
                                <td class="px-5 py-4 text-slate-700">{{ $m->note ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-10 text-center text-slate-500">
                                    Belum ada riwayat. Klik <span class="font-medium">+ Tambah Barang Masuk</span> untuk mencatat belanja stok.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($movements->hasPages())
                <div class="p-4 border-t border-slate-100">{{ $movements->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>