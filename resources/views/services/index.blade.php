<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

        {{-- Judul --}}
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-slate-900">Data Jasa</h1>
                <p class="text-slate-500 mt-1">Daftar layanan toko dan harganya. Jasa yang dinonaktifkan tidak muncul di Kasir.</p>
            </div>
            <a href="{{ route('services.create') }}"
               class="inline-flex items-center px-5 py-2.5 rounded-xl bg-blue-600 text-white text-sm font-semibold shadow-sm hover:bg-blue-700">
                + Tambah Jasa
            </a>
        </div>

        @if (session('success'))
            <div class="p-3 rounded-xl bg-green-100 text-green-800 text-sm">{{ session('success') }}</div>
        @endif

        <div class="bg-white rounded-2xl shadow-sm border border-slate-100">
            <div class="px-5 pt-4 text-sm text-slate-500">
                {{ $services->count() }} jasa &middot; {{ $services->where('is_active', true)->count() }} aktif
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm mt-3">
                    <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-5 py-3 font-semibold">Nama Jasa</th>
                            <th class="px-5 py-3 font-semibold">Harga</th>
                            <th class="px-5 py-3 font-semibold">Status</th>
                            <th class="px-5 py-3 font-semibold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($services as $s)
                            <tr x-data="serviceToggle(@js($s->is_active), @js(route('services.toggle', $s)))"
                                :class="on ? 'hover:bg-slate-50' : 'bg-slate-50/70'">

                                <td class="px-5 py-4 font-medium transition-colors"
                                    :class="on ? 'text-slate-900' : 'text-slate-400'">
                                    {{ $s->name }}
                                    @if ($s->supplies->isNotEmpty())
                                        <div class="mt-0.5 text-xs font-normal text-slate-400">
                                            Bahan: {{ $s->supplies->map(fn ($b) => $b->name . ' ×' . $b->pivot->qty)->implode(', ') }}
                                        </div>
                                    @endif
                                </td>

                                <td class="px-5 py-4 whitespace-nowrap transition-colors"
                                    :class="on ? 'text-slate-900' : 'text-slate-400'">
                                    Rp {{ number_format($s->price, 0, ',', '.') }}
                                </td>

                                {{-- Toggle switch --}}
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <button type="button" role="switch" :aria-checked="on"
                                                aria-label="Aktifkan {{ $s->name }}"
                                                @click="toggle()" :disabled="busy"
                                                :class="on ? 'bg-blue-600' : 'bg-slate-300'"
                                                class="relative inline-flex h-6 w-11 shrink-0 items-center rounded-full transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-60">
                                            <span :class="on ? 'translate-x-6' : 'translate-x-1'"
                                                  class="inline-block h-4 w-4 rounded-full bg-white shadow transition-transform duration-200"></span>
                                        </button>

                                        <span class="inline-block w-16 text-xs font-medium"
                                            :class="on ? 'text-blue-700' : 'text-slate-400'"
                                            x-text="on ? 'Aktif' : 'Nonaktif'">
                                            {{ $s->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </div>
                                </td>

                                {{-- Aksi --}}
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('services.edit', $s) }}"
                                           class="px-3 py-1.5 rounded-lg border border-slate-200 text-slate-700 text-xs font-medium hover:bg-slate-100">
                                            Edit
                                        </a>
                                        <form method="POST" action="{{ route('services.destroy', $s) }}"
                                              onsubmit="return confirm(@js('Hapus jasa ' . $s->name . '? Nota lama tetap aman. Kalau cuma mau berhenti dipakai, cukup nonaktifkan.'))">
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
                                <td colspan="4" class="px-5 py-10 text-center text-slate-500">
                                    Belum ada jasa. Klik <span class="font-medium">+ Tambah Jasa</span> untuk mulai.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('serviceToggle', (initial, url) => ({
                on: initial,
                busy: false,

                async toggle() {
                    if (this.busy) return;
                    this.busy = true;

                    const previous = this.on;
                    this.on = !this.on; // geser dulu biar terasa instan

                    try {
                        const res = await fetch(url, {
                            method: 'PATCH',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                'Accept': 'application/json',
                            },
                        });
                        if (!res.ok) throw new Error('gagal');

                        const data = await res.json();
                        this.on = data.is_active; // samakan dengan yang tersimpan di server
                    } catch (e) {
                        this.on = previous; // kembalikan kalau gagal
                        alert('Gagal mengubah status. Coba lagi.');
                    } finally {
                        this.busy = false;
                    }
                },
            }));
        });
    </script>
</x-app-layout>