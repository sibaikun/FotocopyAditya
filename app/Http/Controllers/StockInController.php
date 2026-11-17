<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StockInController extends Controller
{
    // Halaman daftar riwayat
    public function index()
    {
        $movements = StockMovement::with('product')
            ->where('type', 'in')
            ->latest('date')->latest('id')
            ->paginate(15);

        $spentMonth = StockMovement::where('type', 'in')
            ->whereBetween('date', [now()->startOfMonth()->toDateString(), now()->toDateString()])
            ->sum('total_cost');

        return view('stock-in.index', compact('movements', 'spentMonth'));
    }

    // Halaman form
    public function create()
    {
        $products = Product::orderBy('name')->get()->map(fn ($p) => [
            'id'            => $p->id,
            'name'          => $p->name,
            'image'         => $p->image_url,
            'unit'          => $p->unit,
            'purchase_unit' => $p->purchase_unit,
            'per'           => $p->units_per_purchase,
            'buy_price'     => $p->buy_price,
            'supplier'      => $p->supplier,
            'stock'         => $p->stock,
            'buy_price'     => $p->buy_price,
            'buy_unit'      => $p->buy_price_unit,
            'edit_url'      => route('products.edit', $p),
        ])->values();

        $back = $this->safeBack(request('back'));

        return view('stock-in.create', compact('products', 'back'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'qty'        => 'required|integer|min:1',
            'unit_type'  => 'required|in:base,purchase',
            'date'       => 'required|date',
        ]);

        DB::transaction(function () use ($data) {
            $product = Product::lockForUpdate()->findOrFail($data['product_id']);

            $isPurchase = $data['unit_type'] === 'purchase';

            if ($isPurchase && ! $product->purchase_unit) {
                throw ValidationException::withMessages([
                    'unit_type' => 'Barang ini tidak punya satuan kardus/pak.',
                ]);
            }

            $baseQty   = $data['qty'] * ($isPurchase ? $product->units_per_purchase : 1);
            $totalCost = $product->costFor($baseQty);

            StockMovement::create([
                'product_id' => $product->id,
                'type'       => 'in',
                'qty'        => $baseQty,
                'input_qty'  => $data['qty'],
                'input_unit' => $isPurchase ? $product->purchase_unit : $product->unit,
                'total_cost' => $totalCost,
                'note'       => $product->supplier,
                'date'       => $data['date'],
            ]);

            $product->increment('stock', $baseQty);
        });

        $back = $this->safeBack($request->input('back'));

        return redirect($back ?? route('stock-in.index'))
            ->with('success', 'Barang masuk berhasil dicatat.');
    }

    // Alamat "kembali" hanya diterima kalau berupa alamat di situs ini sendiri.
    private function safeBack(?string $url): ?string
    {
        if (! $url || ! str_starts_with($url, '/') || str_starts_with($url, '//') || str_contains($url, '\\')) {
            return null;
        }

        return $url;
    }
}