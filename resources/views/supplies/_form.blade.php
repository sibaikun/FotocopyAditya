@php
    $s = $supply ?? null;

    // "item" = tanpa satuan khusus (tinta, dll). Satuan lama yang sudah tersimpan tetap muncul.
    $unitOptions = collect([
        'item'   => 'Tanpa satuan (item)',
        'lembar' => 'lembar',
        'pcs'    => 'pcs',
        'botol'  => 'botol',
        'pak'    => 'pak',
        'box'    => 'box',
        'rim'    => 'rim',
        'roll'   => 'roll',
    ]);

    $currentUnit = old('unit', $s?->unit ?? 'item');
    if (! $unitOptions->has($currentUnit)) {
        $unitOptions->put($currentUnit, $currentUnit);
    }
@endphp

<div class="space-y-5">
    <div>
        <x-input-label for="name" value="Nama Bahan" />
        <input id="name" type="text" name="name" value="{{ old('name', $s?->name) }}" required
               placeholder="mis. Tinta Hitam, Plastik Laminating A4"
               class="mt-1 block w-full rounded-xl border-slate-200 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        <x-input-error :messages="$errors->get('name')" class="mt-1" />
    </div>

    <div>
        <x-input-label for="unit" value="Satuan" />
        <select id="unit" name="unit"
                class="mt-1 block w-full rounded-xl border-slate-200 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            @foreach ($unitOptions as $value => $label)
                <option value="{{ $value }}" @selected($currentUnit === $value)>{{ $label }}</option>
            @endforeach
        </select>
        <p class="mt-1 text-xs text-slate-500">Tinta cukup pilih "Tanpa satuan". Plastik laminating pilih "lembar".</p>
        <x-input-error :messages="$errors->get('unit')" class="mt-1" />
    </div>

    <div>
        <x-input-label for="buy_price" value="Harga Beli per Satuan (Rp)" />
        <input id="buy_price" type="number" min="0" name="buy_price" value="{{ old('buy_price', $s?->buy_price ?: null) }}"
               placeholder="mis. 75000"
               class="mt-1 block w-full rounded-xl border-slate-200 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        <p class="mt-1 text-xs text-slate-500">Dipakai untuk menghitung pengeluaran saat bahan dibeli. Kosongkan kalau belum tahu.</p>
        <x-input-error :messages="$errors->get('buy_price')" class="mt-1" />
    </div>

    <div>
        <x-input-label for="supplier" value="Dibeli dari (toko / distributor)" />
        <input id="supplier" type="text" name="supplier" value="{{ old('supplier', $s?->supplier) }}"
               placeholder="mis. Toko Mataram ATK"
               class="mt-1 block w-full rounded-xl border-slate-200 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        <x-input-error :messages="$errors->get('supplier')" class="mt-1" />
    </div>
</div>