@php $backUrl = $back ?? route('stock-in.index'); @endphp

<x-app-layout>
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

        {{-- Judul --}}
        <div>
            <a href="{{ $backUrl }}" class="text-sm text-slate-500 hover:text-slate-700">
                &larr; {{ $back ? 'Kembali' : 'Kembali ke daftar' }}
            </a>
            <h1 class="text-3xl font-bold text-slate-900 mt-2">Tambah Barang Masuk</h1>
            <p class="text-slate-500 mt-1">Catat barang yang baru dibeli untuk menambah stok.</p>
        </div>

        <form method="POST" action="{{ route('stock-in.store') }}"
              @submit="if (! productId) { $event.preventDefault(); openPicker(); }"
              class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 space-y-5"
              x-data="stockIn(@js($products), @js([
                  'product_id'  => old('product_id', request('product')),
                  'unit_type'   => old('unit_type', 'purchase'),
                  'qty'         => (int) old('qty', 1),
                  'preselected' => ! session()->hasOldInput() && request()->filled('product'),
              ]))">
            @csrf

            @if ($back)
                <input type="hidden" name="back" value="{{ $back }}">
            @endif

            {{-- Barang: tombol pemicu popup --}}
            <div>
                <x-input-label value="Barang" />
                <input type="hidden" name="product_id" :value="productId">

                <button type="button" @click="openPicker()"
                        class="mt-1 w-full flex items-center gap-3 rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-left shadow-sm hover:border-blue-400 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none">

                    <template x-if="p">
                        <div class="flex items-center gap-3 min-w-0 flex-1">
                            <div class="w-10 h-10 shrink-0 rounded-lg overflow-hidden bg-slate-100 text-slate-300 flex items-center justify-center">
                                <template x-if="p.image"><img :src="p.image" alt="" class="w-full h-full object-cover"></template>
                                <template x-if="! p.image"><x-icon name="cube" size="w-5 h-5" /></template>
                            </div>
                            <div class="min-w-0">
                                <div class="font-medium text-slate-900 truncate" x-text="p.name"></div>
                                <div class="text-xs text-slate-500" x-text="'Stok saat ini: ' + p.stock + ' ' + p.unit"></div>
                            </div>
                        </div>
                    </template>

                    <template x-if="! p">
                        <span class="flex-1 text-slate-400">Pilih barang...</span>
                    </template>

                    <span class="text-slate-400"><x-icon name="chevron" size="w-4 h-4" /></span>
                </button>

                <x-input-error :messages="$errors->get('product_id')" class="mt-1" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="qty" value="Jumlah" />
                    <input id="qty" type="number" min="1" name="qty" x-model.number="qty" required
                           class="mt-1 block w-full rounded-xl border-slate-200 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <x-input-error :messages="$errors->get('qty')" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="unit_type" value="Satuan" />
                    <select id="unit_type" name="unit_type" x-model="unitType"
                            class="mt-1 block w-full rounded-xl border-slate-200 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <template x-if="p && p.purchase_unit">
                            <option value="purchase" x-text="p.purchase_unit"></option>
                        </template>
                        <option value="base" x-text="p ? p.unit : 'satuan'"></option>
                    </select>
                    <x-input-error :messages="$errors->get('unit_type')" class="mt-1" />
                </div>
            </div>

            <div>
                <x-input-label for="date" value="Tanggal" />
                <input id="date" type="date" name="date" value="{{ old('date', now()->toDateString()) }}" required
                       class="mt-1 block w-full sm:w-1/2 rounded-xl border-slate-200 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <x-input-error :messages="$errors->get('date')" class="mt-1" />
            </div>

            {{-- Pratinjau (harga beli dan toko diambil dari data barang) --}}
            <div class="rounded-xl bg-blue-50 p-4 text-sm space-y-1" x-cloak x-show="p">
                <div>
                    Stok akan bertambah:
                    <span class="font-bold" x-text="totalItems"></span>
                    <span x-text="p ? p.unit : ''"></span>
                </div>

                <div x-show="p && p.buy_price > 0">
                    Total pengeluaran:
                    <span class="font-bold" x-text="rupiah(totalCost)"></span>
                    <span class="text-slate-500"
                        x-text="'(' + rupiah(p ? p.buy_price : 0) + ' per ' + (p ? (p.buy_unit === 'purchase' ? p.purchase_unit : p.unit) : '') + ')'"></span>
                </div>

                <div x-show="p && p.supplier">
                    Dibeli dari: <span class="font-medium" x-text="p ? p.supplier : ''"></span>
                </div>

                <div x-show="p && ! (p.buy_price > 0)" class="text-amber-700">
                    Harga beli barang ini belum diisi, jadi pengeluaran dicatat Rp 0.
                    <a :href="p ? p.edit_url : '#'" class="underline font-medium">Isi harga beli</a>
                </div>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button class="px-5 py-2.5 rounded-xl bg-blue-600 text-white text-sm font-semibold shadow-sm hover:bg-blue-700">
                    Simpan Barang Masuk
                </button>
                <a href="{{ $backUrl }}" class="px-4 py-2.5 text-sm text-slate-600 hover:text-slate-800">Batal</a>
            </div>

            {{-- Popup pilih barang --}}
            <template x-teleport="body">
                <div class="fixed inset-0 z-50 flex items-center justify-center p-4 pointer-events-none"
                     @keydown.escape.window="closePicker()">

                    <div x-cloak x-show="pickerOpen" @click="closePicker()"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         class="absolute inset-0 bg-slate-900/50 pointer-events-auto"></div>

                    <div x-cloak x-show="pickerOpen"
                         x-transition:enter="transition duration-300 ease-[cubic-bezier(0.34,1.56,0.64,1)]"
                         x-transition:enter-start="opacity-0 scale-50"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition duration-200 ease-in"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-75"
                         role="dialog" aria-modal="true" aria-label="Pilih barang"
                         class="relative w-full max-w-2xl flex flex-col bg-white rounded-2xl shadow-2xl pointer-events-auto">

                        <div class="flex items-center justify-between px-4 pt-4 pb-2">
                            <h2 class="text-base font-semibold text-slate-900">Pilih Barang</h2>
                            <button type="button" @click="closePicker()" aria-label="Tutup"
                                    class="w-8 h-8 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center hover:bg-slate-200 hover:text-slate-900 transition">
                                <x-icon name="close" size="w-4 h-4" />
                            </button>
                        </div>

                        <div class="px-4 pb-3">
                            <input id="picker-search" type="text" x-model="q" autocomplete="off"
                                   placeholder="Cari nama barang..."
                                   @keydown.enter.prevent="filtered[0] && select(filtered[0])"
                                   class="block w-full rounded-xl border-slate-200 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
                            <div class="mt-2 text-xs text-slate-400" x-text="filtered.length + ' barang'"></div>
                        </div>

                        <div class="h-80 overflow-y-auto border-t border-slate-100 p-3">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                <template x-for="item in filtered" :key="item.id">
                                    <button type="button" @click="select(item)"
                                            :class="String(item.id) === String(productId)
                                                ? 'border-blue-500 bg-blue-50'
                                                : 'border-slate-200 hover:bg-slate-50'"
                                            class="flex items-center gap-3 p-2.5 rounded-xl border text-left transition">

                                        <div class="w-10 h-10 shrink-0 rounded-lg overflow-hidden bg-slate-100 text-slate-300 flex items-center justify-center">
                                            <template x-if="item.image"><img :src="item.image" alt="" class="w-full h-full object-cover"></template>
                                            <template x-if="! item.image"><x-icon name="cube" size="w-5 h-5" /></template>
                                        </div>

                                        <div class="min-w-0">
                                           <div class="text-sm font-medium leading-snug text-slate-900 line-clamp-2">
                                                <span x-text="item.name"></span>
                                                <span x-show="item.is_supply"
                                                    class="ml-1 px-1.5 py-0.5 rounded bg-amber-100 text-amber-700 text-[10px] font-medium align-middle">Bahan</span>
                                            </div>
                                            <div class="text-xs text-slate-500" x-text="'Stok ' + item.stock + ' ' + item.unit"></div>
                                            <div class="text-xs text-slate-400" x-show="item.purchase_unit"
                                                 x-text="'1 ' + item.purchase_unit + ' = ' + item.per + ' ' + item.unit"></div>
                                        </div>
                                    </button>
                                </template>

                                <div x-show="filtered.length === 0" class="col-span-full py-10 text-center text-sm text-slate-500">
                                    <span x-show="products.length === 0">Belum ada barang. Tambahkan dulu lewat menu Barang.</span>
                                    <span x-show="products.length > 0" x-text="'Barang &quot;' + q + '&quot; tidak ditemukan.'"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </form>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('stockIn', (products, old) => ({
                products,
                productId: old.product_id ?? '',
                unitType: old.unit_type,
                qty: old.qty,
                preselected: old.preselected,

                pickerOpen: false,
                q: '',

                get p() {
                    return this.products.find(x => String(x.id) === String(this.productId)) ?? null;
                },
                get filtered() {
                    const terms = this.q.toLowerCase().split(/\s+/).filter(Boolean);
                    return this.products.filter(x => terms.every(t => x.name.toLowerCase().includes(t)));
                },
                get factor() { return this.p && this.unitType === 'purchase' ? this.p.per : 1; },
                get totalItems() { return this.p ? (this.qty || 0) * this.factor : 0; },
                get totalCost() {
                    if (!this.p) return 0;
                    const divisor = this.p.buy_unit === 'purchase' ? (this.p.per || 1) : 1;
                    return Math.round(this.totalItems * (this.p.buy_price || 0) / divisor);
                },

                openPicker() {
                    this.q = '';
                    this.pickerOpen = true;
                    setTimeout(() => document.getElementById('picker-search')?.focus(), 50);
                },
                closePicker() { this.pickerOpen = false; },
                select(item) {
                    this.productId = item.id;
                    this.closePicker();
                },

                init() {
                    if (!this.p) this.unitType = 'base';

                    // Barang sudah terpilih dari awal (mis. dari tombol "+ Stok"):
                    // atur satuan, lalu arahkan kursor ke Jumlah.
                    if (this.preselected && this.p) {
                        this.unitType = this.p.purchase_unit ? 'purchase' : 'base';
                        setTimeout(() => document.getElementById('qty')?.select(), 100);
                    }

                    this.$watch('productId', () => {
                        this.unitType = (this.p && this.p.purchase_unit) ? 'purchase' : 'base';
                    });
                },

                rupiah(n) { return 'Rp ' + Number(n || 0).toLocaleString('id-ID'); },
            }));
        });
    </script>
</x-app-layout>