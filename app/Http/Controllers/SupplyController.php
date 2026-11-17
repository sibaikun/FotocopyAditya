<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class SupplyController extends Controller
{
    public function index()
    {
        $supplies = Product::supplies()->orderBy('name')->get();

        return view('supplies.index', compact('supplies'));
    }

    public function create()
    {
        return view('supplies.create');
    }

    public function store(Request $request)
    {
        Product::create($this->validated($request));

        return redirect()->route('supplies.index')->with('success', 'Bahan ditambahkan.');
    }

    public function edit(Product $supply)
    {
        $this->ensureSupply($supply);

        return view('supplies.edit', compact('supply'));
    }

    public function update(Request $request, Product $supply)
    {
        $this->ensureSupply($supply);

        $supply->update($this->validated($request));

        return redirect()->route('supplies.index')->with('success', 'Bahan diperbarui.');
    }

    public function destroy(Product $supply)
    {
        $this->ensureSupply($supply);

        $supply->delete();

        return back()->with('success', 'Bahan dihapus.');
    }

    // Halaman ini hanya boleh mengurus bahan, bukan barang dagangan.
    private function ensureSupply(Product $supply): void
    {
        abort_if($supply->is_for_sale, 404);
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name'      => 'required|string|max:255',
            'unit'      => 'required|string|max:30',
            'buy_price' => 'nullable|integer|min:0',
            'supplier'  => 'nullable|string|max:255',
        ]);

        return [
            'name'               => $data['name'],
            'unit'               => $data['unit'],
            'buy_price'          => $data['buy_price'] ?? 0,
            'buy_price_unit'     => 'base',
            'supplier'           => $data['supplier'] ?? null,
            'is_for_sale'        => false,
            'sell_price'         => 0,
            'bulk_price'         => null,
            'purchase_unit'      => null,
            'units_per_purchase' => 1,
        ];
    }
}