<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class StockOutController extends Controller
{
    // Halaman daftar
    public function index(Request $request)
    {
        $reasons = StockMovement::OUT_REASONS;

        $reason = array_key_exists($request->query('reason', ''), $reasons)
            ? $request->query('reason')
            : null;

        $movements = StockMovement::with('product', 'transaction')
            ->where('type', 'out')
            ->when($reason, fn ($q) => $q->where('reason', $reason))
            ->latest('date')->latest('id')
            ->paginate(15)
            ->withQueryString();

        $range = [now()->startOfMonth()->toDateString(), now()->toDateString()];

        $soldMonth = StockMovement::where('type', 'out')
            ->where('reason', 'sale')
            ->whereBetween('date', $range)
            ->sum('qty');

        $manualMonth = StockMovement::where('type', 'out')
            ->whereIn('reason', array_keys(StockMovement::manualReasons()))
            ->whereBetween('date', $range)
            ->sum('qty');

        return view('stock-out.index', compact('movements', 'reasons', 'reason', 'soldMonth', 'manualMonth'));
    }

    // Halaman form
    public function create()
    {
        $products = Product::where('stock', '>', 0)->orderBy('name')->get()->map(fn ($p) => [
            'id'            => $p->id,
            'name'          => $p->name,
            'image'         => $p->image_url,
            'unit'          => $p->unit,
            'purchase_unit' => $p->purchase_unit,
            'per'           => $p->units_per_purchase,
            'stock'         => $p->stock,
            'is_supply'     => ! $p->is_for_sale,
        ])->values();

        $reasons = StockMovement::manualReasons();

        return view('stock-out.create', compact('products', 'reasons'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'qty'        => 'required|integer|min:1',
            'unit_type'  => 'required|in:base,purchase',
            'reason'     => ['required', Rule::in(array_keys(StockMovement::manualReasons()))],
            'date'       => 'required|date',
            'note'       => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($data) {
            $product = Product::lockForUpdate()->findOrFail($data['product_id']);

            $isPurchase = $data['unit_type'] === 'purchase';

            if ($isPurchase && ! $product->purchase_unit) {
                throw ValidationException::withMessages([
                    'unit_type' => 'Barang ini tidak punya satuan kardus/pak.',
                ]);
            }

            $baseQty = $data['qty'] * ($isPurchase ? $product->units_per_purchase : 1);

            if ($product->stock < $baseQty) {
                throw ValidationException::withMessages([
                    'qty' => "Stok {$product->name} tidak cukup (sisa {$product->stock} {$product->unit}).",
                ]);
            }

            StockMovement::create([
                'product_id' => $product->id,
                'type'       => 'out',
                'qty'        => $baseQty,
                'input_qty'  => $data['qty'],
                'input_unit' => $isPurchase ? $product->purchase_unit : $product->unit,
                'reason'     => $data['reason'],
                'note'       => $data['note'] ?? null,
                'date'       => $data['date'],
            ]);

            $product->decrement('stock', $baseQty);
        });

        return redirect()->route('stock-out.index')
            ->with('success', 'Barang keluar berhasil dicatat.');
    }
}