<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Kasir</h2>
    </x-slot>

    <div class="py-8" x-data="cashier(@js($products), @js($services))">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if ($errors->any())
                <div class="mb-4 p-3 bg-red-100 text-red-800 rounded text-sm">
                    @foreach ($errors->all() as $e)
                        <div>{{ $e }}</div>
                    @endforeach
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

                {{-- Kiri: pilih barang / jasa --}}
                <div class="lg:col-span-3 bg-white shadow sm:rounded-lg p-4">
                    <div class="flex gap-2 mb-3">
                        <button type="button" @click="tab = 'product'"
                                :class="tab === 'product' ? 'bg-gray-800 text-white' : 'bg-gray-100'"
                                class="px-4 py-2 rounded-md text-sm">Barang</button>
                        <button type="button" @click="tab = 'service'"
                                :class="tab === 'service' ? 'bg-gray-800 text-white' : 'bg-gray-100'"
                                class="px-4 py-2 rounded-md text-sm">Jasa</button>
                        <input type="text" x-model="search" placeholder="Cari..."
                               class="ms-auto rounded-md border-gray-300 shadow-sm text-sm">
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                        <template x-for="item in list" :key="tab + item.id">
                            <div class="border rounded-lg p-3 flex flex-col gap-2">
                                <div>
                                    <div class="font-medium text-sm" x-text="item.name"></div>
                                    <div class="text-xs text-gray-400" x-show="tab === 'product'"
                                         x-text="'stok ' + item.stock + ' ' + item.unit"></div>
                                </div>

                                {{-- Barang: tombol eceran + borongan --}}
                                <template x-if="tab === 'product'">
                                    <div class="flex flex-col gap-1">
                                        <button type="button" @click="add('product', item, 'base')"
                                                class="text-xs text-left px-2 py-1.5 rounded-md bg-blue-50 text-blue-700 hover:bg-blue-100">
                                            <span x-text="'Eceran · ' + rupiah(item.sell_price)"></span>
                                        </button>

                                        <template x-if="item.purchase_unit && item.bulk_price">
                                            <button type="button" @click="add('product', item, 'purchase')"
                                                    class="text-xs text-left px-2 py-1.5 rounded-md bg-amber-50 text-amber-700 hover:bg-amber-100">
                                                <span x-text="'1 ' + item.purchase_unit + ' (isi ' + item.units_per_purchase + ') · ' + rupiah(item.bulk_price)"></span>
                                            </button>
                                        </template>
                                    </div>
                                </template>

                                {{-- Jasa: satu tombol --}}
                                <template x-if="tab === 'service'">
                                    <button type="button" @click="add('service', item)"
                                            class="text-xs text-left px-2 py-1.5 rounded-md bg-blue-50 text-blue-700 hover:bg-blue-100">
                                        <span x-text="rupiah(item.price)"></span>
                                    </button>
                                </template>
                            </div>
                        </template>
                    </div>

                    <p class="text-sm text-gray-500 mt-3" x-show="list.length === 0">Tidak ada data.</p>
                </div>

                {{-- Kanan: keranjang --}}
                <form method="POST" action="{{ route('transactions.store') }}"
                      class="lg:col-span-2 bg-white shadow sm:rounded-lg p-4 space-y-4">
                    @csrf

                    <h3 class="font-semibold">Keranjang</h3>

                    <p class="text-sm text-gray-500" x-show="cart.length === 0">Belum ada item.</p>

                    <template x-for="(c, i) in cart" :key="c.key">
                        <div class="border-b pb-3">
                            <input type="hidden" :name="`items[${i}][type]`" :value="c.type">
                            <input type="hidden" :name="`items[${i}][id]`" :value="c.id">
                            <input type="hidden" :name="`items[${i}][unit]`" :value="c.unit">

                            <div class="flex justify-between text-sm">
                                <span>
                                    <span class="font-medium" x-text="c.name"></span>
                                    <span class="ml-1 text-xs px-1.5 py-0.5 rounded bg-gray-100 text-gray-600"
                                          x-show="c.unitName" x-text="c.unitName"></span>
                                </span>
                                <button type="button" @click="remove(i)" class="text-red-600 text-xs">hapus</button>
                            </div>

                            <div class="flex items-center gap-2 mt-2">
                                <input type="number" min="1" :max="c.max" x-model.number="c.qty"
                                       :name="`items[${i}][qty]`"
                                       class="w-20 rounded-md border-gray-300 shadow-sm text-sm">

                                <template x-if="c.type === 'service'">
                                    <input type="number" min="0" x-model.number="c.price"
                                           :name="`items[${i}][price]`"
                                           class="w-28 rounded-md border-gray-300 shadow-sm text-sm">
                                </template>
                                <template x-if="c.type === 'product'">
                                    <span class="text-sm text-gray-600" x-text="'x ' + rupiah(c.price)"></span>
                                </template>

                                <span class="ms-auto text-sm font-semibold" x-text="rupiah(c.qty * c.price)"></span>
                            </div>
                        </div>
                    </template>

                    <div class="flex justify-between text-lg font-bold">
                        <span>Total</span>
                        <span x-text="rupiah(total)"></span>
                    </div>

                    <div>
                        <x-input-label for="paid" value="Uang Dibayar (Rp)" />
                        <div class="flex gap-2 mt-1">
                            <input id="paid" type="number" min="0" name="paid" x-model.number="paid" required
                                   class="block w-full rounded-md border-gray-300 shadow-sm">
                            <button type="button" @click="paid = total"
                                    class="px-3 text-xs bg-gray-100 rounded-md whitespace-nowrap">Uang pas</button>
                        </div>
                    </div>

                    <div class="flex justify-between text-sm">
                        <span>Kembalian</span>
                        <span :class="change < 0 ? 'text-red-600' : 'text-green-700'"
                              x-text="rupiah(Math.max(change, 0))"></span>
                    </div>

                    <x-primary-button class="w-full justify-center"
                                      x-bind:disabled="cart.length === 0 || paid < total">
                        Simpan & Cetak Nota
                    </x-primary-button>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('cashier', (products, services) => ({
                products, services,
                tab: 'product',
                search: '',
                cart: [],
                paid: 0,

                get list() {
                    const src = this.tab === 'product' ? this.products : this.services;
                    return src.filter(i => i.name.toLowerCase().includes(this.search.toLowerCase()));
                },

                // unit: 'base' = eceran, 'purchase' = borongan
                add(type, item, unit = 'base') {
                    const key = `${type}-${item.id}-${unit}`;
                    const found = this.cart.find(c => c.key === key);

                    const factor = (type === 'product' && unit === 'purchase') ? item.units_per_purchase : 1;
                    const max = type === 'product' ? Math.floor(item.stock / factor) : null;

                    if (found) {
                        if (found.max !== null && found.qty >= found.max) return;
                        found.qty++;
                        return;
                    }

                    if (max !== null && max < 1) return; // stok nggak cukup untuk 1 satuan ini

                    this.cart.push({
                        key, type, id: item.id, name: item.name, unit,
                        unitName: type === 'service' ? null : (unit === 'purchase' ? item.purchase_unit : item.unit),
                        qty: 1,
                        price: type === 'service' ? item.price : (unit === 'purchase' ? item.bulk_price : item.sell_price),
                        max,
                    });
                },

                remove(i) { this.cart.splice(i, 1); },

                get total() { return this.cart.reduce((s, c) => s + c.qty * c.price, 0); },
                get change() { return this.paid - this.total; },

                rupiah(n) { return 'Rp ' + Number(n || 0).toLocaleString('id-ID'); },
            }));
        });
    </script>
</x-app-layout>