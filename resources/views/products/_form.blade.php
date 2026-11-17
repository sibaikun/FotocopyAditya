@php
    $p = $product ?? null;

    // Daftar dropdown. Satuan lama yang sudah tersimpan tetap ikut muncul.
    $unitOptions = collect(['pcs', 'buah', 'lembar', 'rim', 'pak', 'box', 'set', 'botol', 'roll'])
        ->push(old('unit', $p?->unit))->filter()->unique()->values();

    $bulkOptions = collect(['kardus', 'pak', 'box', 'dus', 'rim'])
        ->push(old('purchase_unit', $p?->purchase_unit))->filter()->unique()->values();

    $hasBulk = session()->hasOldInput()
        ? old('has_bulk') !== null
        : (bool) ($p && $p->purchase_unit);

    // harga borongan disimpan per kardus, di form ditampilkan per item
    $perItem = ($p && $p->bulk_price && $p->units_per_purchase)
        ? (int) round($p->bulk_price / $p->units_per_purchase)
        : null;
@endphp

<div class="space-y-5"
     x-data="{
        unit: @js(old('unit', $p?->unit ?? 'pcs')),
        hasBulk: @js($hasBulk),
        bulkUnit: @js(old('purchase_unit', $p?->purchase_unit ?? 'kardus')),
        per: @js((int) old('units_per_purchase', $p?->purchase_unit ? $p->units_per_purchase : 12)),
        itemPrice: @js(old('bulk_item_price', $perItem)),
        buyPrice: @js(old('buy_price', $p?->buy_price ?: null)),
        buyUnit: @js(old('buy_price_unit', $p?->buy_price_unit ?? 'base')),
        get perItemCost() { return this.buyUnit === 'purchase' && this.per > 0 ? this.buyPrice / this.per : this.buyPrice; },
        get packCost() { return this.buyUnit === 'purchase' ? this.buyPrice : this.buyPrice * this.per; },
        init() { this.$watch('hasBulk', (on) => { if (! on) this.buyUnit = 'base'; }); },
     }">

    {{-- 1. Nama --}}
    <div>
        <x-input-label for="name" value="Nama Barang" />
        <x-text-input id="name" name="name" class="mt-1 block w-full" :value="old('name', $p?->name)" required />
        <x-input-error :messages="$errors->get('name')" class="mt-1" />
    </div>

    {{-- Gambar (opsional) --}}
    <div x-data="imagePicker(@js($p?->image_url))">
        <x-input-label value="Gambar Barang (opsional)" />

        <div class="mt-2 flex items-center gap-4">
            <div class="w-24 h-24 shrink-0 rounded-xl border border-dashed border-slate-300 bg-slate-50 flex items-center justify-center overflow-hidden text-slate-300">
                <img x-show="preview" :src="preview" alt="" class="w-full h-full object-cover">
                <x-icon name="cube" size="w-8 h-8" x-show="! preview" />
            </div>

            <div>
                <label class="inline-flex items-center px-4 py-2 rounded-xl border border-slate-200 bg-white text-sm font-medium text-slate-700 cursor-pointer hover:bg-slate-50">
                    <span x-text="preview ? 'Ganti Gambar' : 'Pilih Gambar'"></span>
                    <input type="file" name="image" accept="image/*" class="sr-only" x-ref="file" @change="pick($event)">
                </label>

                <button type="button" x-cloak x-show="preview" @click="clear($refs.file)"
                        class="ml-2 text-sm text-red-600 hover:text-red-700">Hapus gambar</button>

                <p class="mt-2 text-xs text-slate-500">
                    JPG, PNG, atau WebP. Otomatis diperkecil.
                    <span x-cloak x-show="busy" class="text-blue-600">Memproses...</span>
                </p>
            </div>
        </div>

        {{-- dikirim hanya kalau gambar lama dihapus --}}
        <input type="hidden" name="remove_image" value="1" :disabled="! removed">

        <x-input-error :messages="$errors->get('image')" class="mt-1" />
    </div>

    {{-- 2. Satuan + harga eceran --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <x-input-label for="unit" value="Satuan" />
            <select id="unit" name="unit" x-model="unit"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                @foreach ($unitOptions as $u)
                    <option value="{{ $u }}">{{ $u }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('unit')" class="mt-1" />
        </div>
        <div>
            <x-input-label for="sell_price" value="Harga Eceran per Item (Rp)" />
            <x-text-input id="sell_price" type="number" min="0" name="sell_price" class="mt-1 block w-full"
                          :value="old('sell_price', $p?->sell_price)" placeholder="3000" required />
            <x-input-error :messages="$errors->get('sell_price')" class="mt-1" />
        </div>
    </div>

    {{-- 3. Kardus / borongan (opsional) --}}
    <div class="rounded-xl border border-amber-200 bg-amber-50/60 p-4 space-y-4">
        <label class="flex items-start gap-3 cursor-pointer">
            <input type="checkbox" name="has_bulk" value="1" x-model="hasBulk"
                   class="mt-1 rounded border-gray-300">
            <span>
                <span class="block text-sm font-medium text-gray-800">Barang ini punya satuan kardus / pak</span>
                <span class="block text-xs text-gray-500">Centang kalau barang ini dibeli atau dijual per kardus, pak, dll.</span>
            </span>
        </label>

        <div x-cloak x-show="hasBulk" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="purchase_unit" value="Nama Satuan Besar" />
                    <select id="purchase_unit" name="purchase_unit" x-model="bulkUnit" :disabled="! hasBulk"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        @foreach ($bulkOptions as $b)
                            <option value="{{ $b }}">{{ $b }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('purchase_unit')" class="mt-1" />
                </div>
                <div>
                    <x-input-label for="units_per_purchase">
                        Isi per <span x-text="bulkUnit"></span> (berapa <span x-text="unit"></span>)
                    </x-input-label>
                    <input id="units_per_purchase" type="number" min="1" name="units_per_purchase"
                           x-model.number="per" :disabled="! hasBulk"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    <x-input-error :messages="$errors->get('units_per_purchase')" class="mt-1" />
                </div>
            </div>

            <div>
                <x-input-label for="bulk_item_price" value="Harga Borongan per Item (Rp)" />
                <input id="bulk_item_price" type="number" min="0" name="bulk_item_price"
                       x-model.number="itemPrice" :disabled="! hasBulk" placeholder="2500"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                <p class="mt-1 text-xs text-gray-500">Kosongkan kalau tidak dijual borongan.</p>
                <x-input-error :messages="$errors->get('bulk_item_price')" class="mt-1" />
            </div>

            <p class="text-sm font-medium text-amber-700" x-show="per > 0 && itemPrice > 0"
               x-text="`1 ${bulkUnit} = ${per} ${unit} = Rp ${(per * itemPrice).toLocaleString('id-ID')}`"></p>
        </div>
    </div>

    {{-- 4. Pembelian dari distributor --}}
    <div class="rounded-xl border border-slate-200 p-4 space-y-4">
        <div class="text-sm font-medium text-slate-800">Pembelian dari Distributor</div>

        <div>
            <x-input-label for="buy_price" value="Harga Beli (Rp)" />
            <div class="mt-1 flex gap-2">
                <input id="buy_price" type="number" min="0" name="buy_price" x-model.number="buyPrice"
                    placeholder="22000"
                    class="block w-full rounded-md border-gray-300 shadow-sm">

                <div class="flex items-center gap-2 shrink-0">
                    <span class="text-sm text-slate-500">per</span>
                    <select name="buy_price_unit" x-model="buyUnit"
                            class="w-32 rounded-md border-gray-300 shadow-sm">
                        <option value="base" x-text="unit"></option>
                        <template x-if="hasBulk">
                            <option value="purchase" x-text="bulkUnit"></option>
                        </template>
                    </select>
                </div>
            </div>

            <p class="mt-1 text-xs text-slate-500">
                Isi sesuai nota dari distributor, lalu pilih satuannya. Dipakai untuk menghitung pengeluaran saat barang masuk dicatat.
            </p>

            <p class="mt-1 text-sm font-medium text-slate-700" x-cloak
            x-show="hasBulk && buyPrice > 0 && per > 0"
            x-text="`1 ${bulkUnit} (isi ${per} ${unit}) = Rp ${Math.round(packCost).toLocaleString('id-ID')} · ≈ Rp ${Math.round(perItemCost).toLocaleString('id-ID')} per ${unit}`"></p>

            <x-input-error :messages="$errors->get('buy_price')" class="mt-1" />
        </div>

        <div>
            <x-input-label for="supplier" value="Dibeli dari (toko / distributor)" />
            <input id="supplier" type="text" name="supplier" value="{{ old('supplier', $p?->supplier) }}"
                placeholder="mis. Toko Mataram ATK"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            <x-input-error :messages="$errors->get('supplier')" class="mt-1" />
        </div>
    </div>
    </details>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('imagePicker', (current) => ({
            preview: current,
            removed: false,
            busy: false,

            async pick(event) {
                const input = event.target;
                const file = input.files[0];
                if (!file) return;

                this.removed = false;
                this.busy = true;

                try {
                    // perkecil dulu, lalu ganti file di input dengan versi kecilnya
                    const small = await this.shrink(file, 800);
                    const dt = new DataTransfer();
                    dt.items.add(small);
                    input.files = dt.files;
                    this.preview = URL.createObjectURL(small);
                } catch (e) {
                    // kalau gagal diperkecil, pakai file aslinya
                    this.preview = URL.createObjectURL(file);
                } finally {
                    this.busy = false;
                }
            },

            shrink(file, max) {
                return new Promise((resolve, reject) => {
                    const img = new Image();
                    img.onload = () => {
                        const scale = Math.min(1, max / Math.max(img.width, img.height));
                        const w = Math.round(img.width * scale);
                        const h = Math.round(img.height * scale);

                        const canvas = document.createElement('canvas');
                        canvas.width = w;
                        canvas.height = h;

                        const ctx = canvas.getContext('2d');
                        ctx.fillStyle = '#fff'; // latar putih untuk PNG transparan
                        ctx.fillRect(0, 0, w, h);
                        ctx.drawImage(img, 0, 0, w, h);

                        canvas.toBlob(
                            (blob) => blob
                                ? resolve(new File([blob], 'barang.jpg', { type: 'image/jpeg' }))
                                : reject(),
                            'image/jpeg', 0.85
                        );
                        URL.revokeObjectURL(img.src);
                    };
                    img.onerror = reject;
                    img.src = URL.createObjectURL(file);
                });
            },

            clear(input) {
                input.value = '';
                this.preview = null;
                this.removed = true;
            },
        }));
    });
</script>