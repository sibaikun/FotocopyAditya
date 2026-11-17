<x-app-layout>
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

        {{-- Judul --}}
        <div>
            <a href="{{ route('products.index') }}" class="text-sm text-slate-500 hover:text-slate-700">&larr; Kembali ke daftar</a>
            <h1 class="text-3xl font-bold text-slate-900 mt-2">Edit Barang</h1>
            <p class="text-slate-500 mt-1">Ubah data, harga, atau gambar barang ini.</p>
        </div>

        <form method="POST" action="{{ route('products.update', $product) }}" enctype="multipart/form-data"
              class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 space-y-6">
            @csrf
            @method('PUT')

            @include('products._form', ['product' => $product])

            <div class="flex items-center gap-3 pt-2">
                <button class="px-5 py-2.5 rounded-xl bg-blue-600 text-white text-sm font-semibold shadow-sm hover:bg-blue-700">
                    Perbarui
                </button>
                <a href="{{ route('products.index') }}" class="px-4 py-2.5 text-sm text-slate-600 hover:text-slate-800">Batal</a>
            </div>
        </form>
    </div>
</x-app-layout>