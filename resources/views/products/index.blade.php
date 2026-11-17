<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

        {{-- Judul --}}
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-slate-900">Data Barang</h1>
                <p class="text-slate-500 mt-1">Daftar barang, kemasan, harga, dan stok saat ini.</p>
            </div>
            <a href="{{ route('products.create') }}"
               class="inline-flex items-center px-5 py-2.5 rounded-xl bg-blue-600 text-white text-sm font-semibold shadow-sm hover:bg-blue-700">
                + Tambah Barang
            </a>
        </div>

        @if (session('success'))
            <div class="p-3 rounded-xl bg-green-100 text-green-800 text-sm">{{ session('success') }}</div>
        @endif

        <div class="bg-white rounded-2xl shadow-sm border border-slate-100"
             x-data="liveSearch(@js(request('q', '')), @js(route('products.index')))">

            {{-- Pencarian (langsung jalan saat mengetik) --}}
            <form @submit.prevent="run()" class="flex flex-wrap items-center gap-3 p-4 border-b border-slate-100">
                <input type="text" x-model="q" @input.debounce.300ms="run()"
                       placeholder="Cari nama barang..." autocomplete="off"
                       class="w-full sm:w-72 rounded-xl border-slate-200 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">

                <button type="button" x-cloak x-show="q" @click="q = ''; run()"
                        class="text-sm text-slate-500 hover:text-slate-700">Reset</button>

                <span x-cloak x-show="loading" class="text-xs text-slate-400">Mencari...</span>
            </form>

            {{-- Popup gambar --}}
            <template x-teleport="body">
                <div class="fixed inset-0 z-50 flex items-center justify-center p-4 pointer-events-none"
                    @keydown.escape.window="closeImage()">

                    {{-- latar gelap --}}
                    <div x-cloak x-show="imageOpen" @click="closeImage()"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                        class="absolute inset-0 bg-slate-900/50 pointer-events-auto"></div>

                    {{-- kotak popup --}}
                    <div x-cloak x-show="imageOpen"
                        x-transition:enter="transition duration-300 ease-[cubic-bezier(0.34,1.56,0.64,1)]"
                        x-transition:enter-start="opacity-0 scale-50"
                        x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition duration-200 ease-in"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-75"
                        role="dialog" aria-modal="true"
                        class="relative w-full max-w-sm bg-white rounded-2xl shadow-2xl p-3 pointer-events-auto">

                        <div class="relative rounded-xl overflow-hidden bg-slate-100">
                            <img :src="imageSrc" :alt="imageName" class="w-full max-h-[60vh] object-contain">

                            <button type="button" @click="closeImage()" aria-label="Tutup"
                                    class="absolute top-2 right-2 w-9 h-9 rounded-full bg-white/90 text-slate-700 shadow flex items-center justify-center hover:bg-white hover:text-slate-900 transition">
                                <x-icon name="close" size="w-5 h-5" />
                            </button>
                        </div>

                        <p class="mt-3 px-1 text-center text-sm font-medium text-slate-800" x-text="imageName"></p>
                    </div>
                </div>
            </template>

            {{-- Bagian ini yang diganti saat mencari --}}
            <div id="product-list" class="transition-opacity" :class="loading ? 'opacity-60' : ''">

                <div class="px-5 pt-3 text-sm text-slate-500">{{ $products->total() }} barang</div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm mt-2">
                        <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-5 py-3 font-semibold">Nama Barang</th>
                                <th class="px-5 py-3 font-semibold">Kemasan</th>
                                <th class="px-5 py-3 font-semibold">Harga Eceran</th>
                                <th class="px-5 py-3 font-semibold">Harga Borongan</th>
                                <th class="px-5 py-3 font-semibold">Stok</th>
                                <th class="px-5 py-3 font-semibold text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($products as $p)
                                @php
                                    $hasPack = $p->purchase_unit && $p->units_per_purchase > 1;
                                    $packs   = $hasPack ? intdiv($p->stock, $p->units_per_purchase) : 0;
                                    $rest    = $hasPack ? $p->stock % $p->units_per_purchase : $p->stock;
                                @endphp
                                <tr class="hover:bg-slate-50">
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-3">
                                            @if ($p->image_url)
                                                <button type="button"
                                                        data-src="{{ $p->image_url }}" data-name="{{ $p->name }}"
                                                        @click="showImage($el)"
                                                        aria-label="Lihat gambar {{ $p->name }}"
                                                        class="w-12 h-12 shrink-0 rounded-lg overflow-hidden border border-slate-200 transition hover:ring-2 hover:ring-blue-500">
                                                    <img src="{{ $p->image_url }}" alt="{{ $p->name }}" loading="lazy" class="w-full h-full object-cover">
                                                </button>
                                            @else
                                                <div class="w-12 h-12 shrink-0 rounded-lg bg-slate-100 text-slate-300 flex items-center justify-center">
                                                    <x-icon name="cube" size="w-6 h-6" />
                                                </div>
                                            @endif

                                            <span class="font-medium text-slate-900">{{ $p->name }}</span>
                                        </div>
                                    </td>

                                    <td class="px-5 py-4 text-slate-700 whitespace-nowrap">
                                        @if ($p->purchase_unit)
                                            1 {{ $p->purchase_unit }} = {{ $p->units_per_purchase }} {{ $p->unit }}
                                        @else
                                            <span class="text-slate-400">-</span>
                                        @endif
                                    </td>

                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <span class="font-medium text-slate-900">Rp {{ number_format($p->sell_price, 0, ',', '.') }}</span>
                                        <span class="text-slate-400">/ {{ $p->unit }}</span>
                                    </td>

                                    <td class="px-5 py-4 whitespace-nowrap">
                                        @if ($p->purchase_unit && $p->bulk_price)
                                            <span class="font-medium text-slate-900">
                                                Rp {{ number_format($p->bulk_price / $p->units_per_purchase, 0, ',', '.') }}
                                            </span>
                                            <span class="text-slate-400">/ {{ $p->unit }}</span>
                                            <div class="text-xs text-slate-500">
                                                1 {{ $p->purchase_unit }} = Rp {{ number_format($p->bulk_price, 0, ',', '.') }}
                                            </div>
                                        @else
                                            <span class="text-slate-400">-</span>
                                        @endif
                                    </td>

                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            <span class="font-semibold text-slate-900">{{ $p->stock }} {{ $p->unit }}</span>

                                            @if ($p->stock <= 0)
                                                <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">Habis</span>
                                            @elseif ($p->stock <= 5)
                                                <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700">Menipis</span>
                                            @else
                                                <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">Aman</span>
                                            @endif
                                        </div>

                                        @if ($packs > 0)
                                            <div class="text-xs text-slate-500 mt-0.5">
                                                = {{ $packs }} {{ $p->purchase_unit }}@if ($rest > 0) + {{ $rest }} {{ $p->unit }}@endif
                                            </div>
                                        @endif
                                    </td>

                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('stock-in.create', ['product' => $p->id, 'back' => request()->getRequestUri()]) }}"
                                            title="Tambah stok {{ $p->name }}"
                                            class="px-3 py-1.5 rounded-lg bg-blue-50 text-blue-700 text-xs font-medium hover:bg-blue-100">
                                                + Stok
                                            </a>

                                            <a href="{{ route('products.edit', $p) }}"
                                            class="px-3 py-1.5 rounded-lg border border-slate-200 text-slate-700 text-xs font-medium hover:bg-slate-100">
                                                Edit
                                            </a>
                                            <form method="POST" action="{{ route('products.destroy', $p) }}"
                                                  onsubmit="return confirm(@js('Hapus ' . $p->name . '? Riwayat barang masuk barang ini ikut terhapus.'))">
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
                                    <td colspan="6" class="px-5 py-10 text-center text-slate-500">
                                        @if (request('q'))
                                            Barang "{{ request('q') }}" tidak ditemukan.
                                        @else
                                            Belum ada barang. Klik <span class="font-medium">+ Tambah Barang</span> untuk mulai.
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($products->hasPages())
                    <div class="p-4 border-t border-slate-100">{{ $products->links() }}</div>
                @endif
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('liveSearch', (initial, url) => ({
                q: initial,
                loading: false,
                controller: null,

                // popup gambar
                imageOpen: false,
                imageSrc: '',
                imageName: '',

                showImage(el) {
                    this.imageSrc = el.dataset.src;
                    this.imageName = el.dataset.name;
                    this.imageOpen = true;
                },
                closeImage() {
                    this.imageOpen = false; // gambar sengaja nggak dikosongkan, biar animasi tutup masih terlihat
                },

                async run() {
                    if (this.controller) this.controller.abort();
                    const ctrl = this.controller = new AbortController();

                    this.loading = true;

                    const params = new URLSearchParams();
                    if (this.q.trim()) params.set('q', this.q.trim());
                    const target = url + (params.toString() ? '?' + params : '');

                    try {
                        const res = await fetch(target, { signal: ctrl.signal });
                        const html = await res.text();
                        const doc = new DOMParser().parseFromString(html, 'text/html');
                        const fresh = doc.getElementById('product-list');

                        if (fresh) {
                            document.getElementById('product-list').innerHTML = fresh.innerHTML;
                            history.replaceState(null, '', target);
                        }
                    } catch (e) {
                        if (e.name !== 'AbortError') console.error(e);
                    } finally {
                        if (this.controller === ctrl) this.loading = false;
                    }
                },
            }));
        });
    </script>
</x-app-layout>