@php
    $s = $service ?? null;

    // Jasa baru: aktif secara default. Kalau form gagal divalidasi, ikuti pilihan terakhir.
    $isActive = session()->hasOldInput()
        ? old('is_active') !== null
        : ($s?->is_active ?? true);
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

    <label class="flex items-start gap-3 cursor-pointer">
        <input type="checkbox" name="is_active" value="1" @checked($isActive)
               class="mt-1 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
        <span>
            <span class="block text-sm font-medium text-slate-800">Aktif</span>
            <span class="block text-xs text-slate-500">Jasa yang aktif muncul di Kasir.</span>
        </span>
    </label>
</div>