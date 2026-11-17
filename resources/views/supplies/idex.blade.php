<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-slate-900">Bahan Internal</h1>
                <p class="text-slate-500 mt-1">Bahan habis pakai untuk operasional (tinta, plastik laminating, dll). Nggak dijual dan nggak muncul di Kasir.</p>
            </div>
            <a href="{{ route('supplies.create') }}"
               class="inline-flex items-center px-5 py-2.5 rounded-xl bg-blue-600 text-white text-sm font-semibold shadow-sm hover:bg-blue-700">
                + Tambah Bahan
            </a>
        </div>

        @if (session('success'))
            <div class="p-3 rounded-xl bg-green-100 text-green-800 text-sm">{{ session('success') }}</div>
        @endif

        <div class="bg-white rounded-2xl shadow-sm border border-slate-100">
            <div class="px-5 pt-4 text-sm text-slate-500">{{ $supplies->count() }} bahan</div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm mt-3">
                    <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-5 py-3 font-semibold">Nama Bahan</th>
                            <th class="px-5 py-3 font-semibold">Harga Beli</th>
                            <th class="px-5 py-3 font-semibold">Dibeli dari</th>
                            <th class="px-5 py-3 font-semibold">Stok</th>
                            <th class="px-5 py-3 font-semibold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($supplies as $s)
                            <tr class="hover:bg-slate-50">
                                <td class="px-5 py-4 font-medium text-slate-900">{{ $s->name }}</td>

                                <td class="px-5 py-4 whitespace-nowrap text-slate-700">
                                    @if ($s->buy_price)
                                        Rp {{ number_format($s->buy_price, 0, ',', '.') }}
                                        <span class="text-slate-400">/ {{ $s->unit }}</span>
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>

                                <td class="px-5 py-4 text-slate-700">{{ $s->supplier ?? '-' }}</td>

                                <td class="px-5 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <span class="font-semibold text-slate-900">{{ $s->stock }} {{ $s->unit }}</span>
                                        @if ($s->stock <= 0)
                                            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">Habis</span>
                                        @elseif ($s->stock <= 5)
                                            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700">Menipis</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">Aman</span>
                                        @endif
                                    </div>
                                </td>

                                <td class="px-5 py-4 whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('stock-in.create', ['product' => $s->id, 'back' => request()->getRequestUri()]) }}"
                                           title="Beli / tambah stok {{ $s->name }}"
                                           class="px-3 py-1.5 rounded-lg bg-blue-50 text-blue-700 text-xs font-medium hover:bg-blue-100">
                                            + Stok
                                        </a>
                                        <a href="{{ route('supplies.edit', $s) }}"
                                           class="px-3 py-1.5 rounded-lg border border-slate-200 text-slate-700 text-xs font-medium hover:bg-slate-100">
                                            Edit
                                        </a>
                                        <form method="POST" action="{{ route('supplies.destroy', $s) }}"
                                              onsubmit="return confirm(@js('Hapus ' . $s->name . '? Riwayat pembelian dan pemakaian bahan ini ikut terhapus.'))">
                                            @csrf @method('DELETE')
                                            <button class="px-3 py-1.5 rounded-lg bg-red-50 text-red-600 text-xs font-medium hover:bg-red-100">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-10 text-center text-slate-500">
                                    Belum ada bahan. Klik <span class="font-medium">+ Tambah Bahan</span> untuk mulai.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>