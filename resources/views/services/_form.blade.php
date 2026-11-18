@php
    $s = $service ?? null;

    // Jasa baru: aktif secara default. Kalau form gagal divalidasi, ikuti pilihan terakhir.
    $isActive = session()->hasOldInput()
        ? old('is_active') !== null
        : ($s?->is_active ?? true);

        $supplyRows = old('supplies', $s
        ? $s->supplies->map(fn ($b) => ['product_id' => $b->id, 'qty' => $b->pivot->qty])->values()->all()
        : []);
@endphp

<div class="space-y-5">
    <div>
        <x-input-label for="name" value="Nama Jasa" />
        <input id="name" type="text" name="name" value="{{ old('name', $s?->name) }}" required
               placeholder="mis. Fotocopy A4"
               class="mt-1 block w-full rounded-xl border-slate-200 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        <x-input-error :messages="$errors->get('name')" class="mt-1" />
    </div>

    <div>
        <x-input-label for="price" value="Harga (Rp)" />
        <input id="price" type="number" min="0" name="price" value="{{ old('price', $s?->price) }}" required
               placeholder="300"
               class="mt-1 block w-full rounded-xl border-slate-200 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        <p class="mt-1 text-xs text-slate-500">
            Harga standar. Di Kasir, harga jasa tetap bisa diubah untuk tiap transaksi (misalnya biaya admin loket).
        </p>
        <x-input-error :messages="$errors->get('price')" class="mt-1" />
    </div>

    {{-- Bahan yang terpakai --}}
    <div class="rounded-xl border border-slate-200 p-4 space-y-3"
        x-data="serviceSupplies(@js($supplyOptions->map(fn ($o) => ['id' => $o->id, 'unit' => $o->unit])->values()), @js($supplyRows))">
        <div>
            <div class="text-sm font-medium text-slate-800">Bahan yang terpakai (opsional)</div>
            <p class="text-xs text-slate-500">
                Setiap jasa ini dijual, stok bahan di bawah berkurang otomatis.
                Contoh: Laminating memakai 1 lembar plastik laminating.
            </p>
        </div>

        <template x-for="(row, i) in rows" :key="row.key">
            <div class="flex items-center gap-2">
                <select :name="`supplies[${i}][product_id]`" x-model="row.product_id"
                        class="min-w-0 flex-1 rounded-xl border-slate-200 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">-- pilih bahan --</option>
                    @foreach ($supplyOptions as $o)
                        <option value="{{ $o->id }}">{{ $o->name }} ({{ $o->unit }})</option>
                    @endforeach
                </select>

                <input type="number" min="1" :name="`supplies[${i}][qty]`" x-model.number="row.qty"
                    class="w-20 rounded-xl border-slate-200 shadow-sm text-sm focus:border-blue-500 focus:ring-blue-500">

                <span class="w-14 shrink-0 text-xs text-slate-500" x-text="unitOf(row.product_id)"></span>

                <button type="button" @click="remove(i)" class="shrink-0 text-xs text-red-600 hover:text-red-700">hapus</button>
            </div>
        </template>

        <button type="button" @click="add()" x-show="options.length > 0"
                class="text-sm font-medium text-blue-600 hover:text-blue-700">+ Tambah bahan</button>

        @if ($supplyOptions->isEmpty())
            <p class="text-xs text-amber-700">Belum ada bahan. Tambahkan dulu lewat menu Bahan.</p>
        @endif

        @if (collect($errors->keys())->contains(fn ($k) => str_starts_with($k, 'supplies')))
            <p class="text-xs text-red-600">Periksa lagi bahannya: pilih bahan yang berbeda dan jumlah minimal 1.</p>
        @endif
    </div>

    <label class="flex items-start gap-3 cursor-pointer">
        <input type="checkbox" name="is_active" value="1" @checked($isActive)
               class="mt-1 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
        <span>
            <span class="block text-sm font-medium text-slate-800">Aktif</span>
            <span class="block text-xs text-slate-500">Jasa yang aktif muncul di Kasir.</span>
        </span>
    </label>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('serviceSupplies', (options, initial) => ({
            options,
            rows: [],
            seq: 0,

            init() {
                this.rows = initial.map(r => ({
                    key: ++this.seq,
                    product_id: String(r.product_id ?? ''),
                    qty: Number(r.qty) || 1,
                }));
            },

            add() { this.rows.push({ key: ++this.seq, product_id: '', qty: 1 }); },
            remove(i) { this.rows.splice(i, 1); },

            unitOf(id) {
                const o = this.options.find(x => String(x.id) === String(id));
                return o ? o.unit : '';
            },
        }));
    });
</script>