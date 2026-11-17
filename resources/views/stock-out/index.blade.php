<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

        {{-- Judul --}}
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-slate-900">Barang Keluar</h1>
                <p class="text-slate-500 mt-1">Penjualan dari Kasir tercatat otomatis. Catat manual untuk barang rusak, hilang, atau dipakai sendiri.</p>
            </div>
            <a href="{{ route('stock-out.create') }}"
               class="inline-flex items-center px-5 py-2.5 rounded-xl bg-blue-600 text-white text-sm font-semibold shadow-sm hover:bg-blue-700">
                + Catat Barang Keluar
            </a>
        </div>

        @if (session('success'))
            <div class="p-3 rounded-xl bg-green-100 text-green-800 text-sm">{{ session('success') }}</div>
        @endif

        {{-- Ringkasan bulan ini --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div class="flex items-center gap-4 bg-white p-5 rounded-2xl shadow-sm border border-slate-100">
                <div class="w-14 h-14 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center shrink-0">
                    <x-icon name="cart" size="w-8 h-8" />
                </div>
                <div>
                    <div class="text-sm text-slate-500">Barang Terjual Bulan Ini</div>
                    <div class="text-2xl font-bold text-slate-900">{{ number_format($soldMonth, 0, ',', '.') }} item</div>
                </div>
            </div>
            <div class="flex items-center gap-4 bg-white p-5 rounded-2xl shadow-sm border border-slate-100">
                <div class="w-14 h-14 rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center shrink-0">
                    <x-icon name="upload" size="w-8 h-8" />
                </div>
                <div>
                    <div class="text-sm text-slate-500">Keluar Manual Bulan Ini</div>
                    <div class="text-2xl font-bold text-slate-900">{{ number_format($manualMonth, 0, ',', '.') }} item</div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-100">

            {{-- Filter alasan --}}
            <div class="flex flex-wrap items-center gap-2 p-4 border-b border-slate-100">
                <a href="{{ route('stock-out.index') }}"
                   class="px-3 py-1.5 rounded-full text-xs font-medium {{ $reason === null ? 'bg-slate-800 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    Semua
                </a>
                @foreach ($reasons as $key => $label)
                    <a href="{{ route('stock-out.index', ['reason' => $key]) }}"
                       class="px-3 py-1.5 rounded-full text-xs font-medium {{ $reason === $key ? 'bg-slate-800 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-5 py-3 font-semibold">Tanggal</th>
                            <th class="px-5 py-3 font-semibold">Barang</th>
                            <th class="px-5 py-3 font-semibold">Jumlah</th>
                            <th class="px-5 py-3 font-semibold">Keluar dari Stok</th>
                            <th class="px-5 py-3 font-semibold">Alasan</th>
                            <th class="px-5 py-3 font-semibold">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($movements as $m)
                            @php
                                $badge = [
                                    'sale'    => 'bg-blue-100 text-blue-700',
                                    'damaged' => 'bg-red-100 text-red-700',
                                    'lost'    => 'bg-amber-100 text-amber-700',
                                    'own_use' => 'bg-violet-100 text-violet-700',
                                    'other'   => 'bg-slate-100 text-slate-600',
                                ][$m->reason] ?? 'bg-slate-100 text-slate-600';
                            @endphp
                            <tr class="hover:bg-slate-50">
                                <td class="px-5 py-4 whitespace-nowrap text-slate-700">
                                    {{ \Carbon\Carbon::parse($m->date)->translatedFormat('d M Y') }}
                                </td>
                                <td class="px-5 py-4 font-medium text-slate-900">{{ $m->product->name }}</td>
                                <td class="px-5 py-4 whitespace-nowrap text-slate-700">{{ $m->input_qty }} {{ $m->input_unit }}</td>
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                        -{{ $m->qty }} {{ $m->product->unit }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $badge }}">
                                        {{ $reasons[$m->reason] ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-slate-700">
                                    @if ($m->reason === 'sale' && $m->transaction)
                                        <a href="{{ route('transactions.show', $m->transaction) }}"
                                           class="text-blue-600 hover:underline">{{ $m->transaction->invoice_no }}</a>
                                    @else
                                        {{ $m->note ?? '-' }}
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-10 text-center text-slate-500">
                                    @if ($reason)
                                        Belum ada barang keluar dengan alasan ini.
                                    @else
                                        Belum ada barang keluar. Penjualan di Kasir akan muncul di sini otomatis.
                                    @endif
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