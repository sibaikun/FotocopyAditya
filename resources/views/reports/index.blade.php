<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Laporan Penjualan</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Filter --}}
            <form method="GET" class="bg-white p-4 shadow sm:rounded-lg flex flex-wrap gap-3 items-end">
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Periode</label>
                    <select name="period" class="rounded-md border-gray-300 shadow-sm text-sm">
                        <option value="daily"   @selected($period === 'daily')>Harian</option>
                        <option value="weekly"  @selected($period === 'weekly')>Mingguan</option>
                        <option value="monthly" @selected($period === 'monthly')>Bulanan</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Tanggal acuan</label>
                    <input type="date" name="date" value="{{ $date }}"
                           class="rounded-md border-gray-300 shadow-sm text-sm">
                </div>
                <x-primary-button>Tampilkan</x-primary-button>
            </form>

            <p class="text-gray-600">Menampilkan: <span class="font-semibold">{{ $label }}</span></p>

            {{-- Ringkasan --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white p-4 shadow sm:rounded-lg">
                    <div class="text-xs text-gray-500">Pendapatan (Penjualan)</div>
                    <div class="text-xl font-bold text-green-700">Rp {{ number_format($omzet, 0, ',', '.') }}</div>
                </div>
                <div class="bg-white p-4 shadow sm:rounded-lg">
                    <div class="text-xs text-gray-500">Pengeluaran (Belanja Stok)</div>
                    <div class="text-xl font-bold text-red-600">Rp {{ number_format($expense, 0, ',', '.') }}</div>
                </div>
                <div class="bg-white p-4 shadow sm:rounded-lg">
                    <div class="text-xs text-gray-500">Selisih</div>
                    <div class="text-xl font-bold {{ $net < 0 ? 'text-red-600' : 'text-slate-900' }}">
                        {{ $net < 0 ? '-' : '' }}Rp {{ number_format(abs($net), 0, ',', '.') }}
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white p-4 shadow sm:rounded-lg">
                    <div class="text-xs text-gray-500">Jumlah Nota</div>
                    <div class="text-xl font-bold">{{ $count }}</div>
                </div>
                <div class="bg-white p-4 shadow sm:rounded-lg">
                    <div class="text-xs text-gray-500">Dari Barang</div>
                    <div class="text-xl font-bold">Rp {{ number_format($productSale, 0, ',', '.') }}</div>
                </div>
                <div class="bg-white p-4 shadow sm:rounded-lg">
                    <div class="text-xs text-gray-500">Dari Jasa</div>
                    <div class="text-xl font-bold">Rp {{ number_format($serviceSale, 0, ',', '.') }}</div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Rincian per hari --}}
                <div class="bg-white shadow sm:rounded-lg overflow-x-auto">
                    <h3 class="px-4 pt-4 font-semibold">Rincian per Hari</h3>
                    <table class="min-w-full text-sm mt-2">
                        <thead class="bg-gray-50 text-left text-gray-600">
                            <tr>
                                <th class="px-4 py-2">Tanggal</th>
                                <th class="px-4 py-2">Nota</th>
                                <th class="px-4 py-2 text-right">Pendapatan</th>
                                <th class="px-4 py-2 text-right">Pengeluaran</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @forelse ($rows as $r)
                                <tr>
                                    <td class="px-4 py-2">{{ \Carbon\Carbon::parse($r['date'])->translatedFormat('D, d M Y') }}</td>
                                    <td class="px-4 py-2">{{ $r['count'] }}</td>
                                    <td class="px-4 py-2 text-right">Rp {{ number_format($r['income'], 0, ',', '.') }}</td>
                                    <td class="px-4 py-2 text-right">Rp {{ number_format($r['expense'], 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-4 py-6 text-center text-gray-500">Belum ada data.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Terlaris --}}
                <div class="bg-white shadow sm:rounded-lg overflow-x-auto">
                    <h3 class="px-4 pt-4 font-semibold">5 Terlaris</h3>
                    <table class="min-w-full text-sm mt-2">
                        <thead class="bg-gray-50 text-left text-gray-600">
                            <tr>
                                <th class="px-4 py-2">Item</th>
                                <th class="px-4 py-2">Qty</th>
                                <th class="px-4 py-2 text-right">Pendapatan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @forelse ($top as $t)
                                <tr>
                                    <td class="px-4 py-2">{{ $t->item_name }}</td>
                                    <td class="px-4 py-2">{{ $t->qty }}</td>
                                    <td class="px-4 py-2 text-right">Rp {{ number_format($t->total, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="px-4 py-6 text-center text-gray-500">Belum ada data.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>