<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Riwayat Nota</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow sm:rounded-lg overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-left text-gray-600">
                        <tr>
                            <th class="px-4 py-3">No. Nota</th>
                            <th class="px-4 py-3">Tanggal</th>
                            <th class="px-4 py-3">Kasir</th>
                            <th class="px-4 py-3">Total</th>
                            <th class="px-4 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse ($transactions as $t)
                            <tr>
                                <td class="px-4 py-3">{{ $t->invoice_no }}</td>
                                <td class="px-4 py-3">{{ \Carbon\Carbon::parse($t->date)->translatedFormat('d F Y') }}</td>
                                <td class="px-4 py-3">{{ $t->user->name }}</td>
                                <td class="px-4 py-3">Rp {{ number_format($t->total, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('transactions.show', $t) }}" class="text-indigo-600">Lihat</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-4 py-6 text-center text-gray-500">Belum ada transaksi.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">{{ $transactions->links() }}</div>
        </div>
    </div>
</x-app-layout>