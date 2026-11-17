<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $terms = preg_split('/\s+/', trim((string) $request->q), -1, PREG_SPLIT_NO_EMPTY);

        $products = Product::query()
            ->when($terms, function ($query, $terms) {
                foreach ($terms as $term) {
                    $query->where('name', 'like', '%' . $term . '%');
                }
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('products.index', compact('products'));
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        Product::create($data);

        return redirect()->route('products.index')->with('success', 'Barang ditambahkan.');
    }

    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $this->validated($request);

        if ($request->hasFile('image')) {
            // ganti gambar: hapus file lama
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');
        } elseif ($request->boolean('remove_image') && $product->image) {
            // hapus gambar tanpa menggantinya
            Storage::disk('public')->delete($product->image);
            $data['image'] = null;
        }

        $product->update($data);

        return redirect()->route('products.index')->with('success', 'Barang diperbarui.');
    }

    public function destroy(Product $product)
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return back()->with('success', 'Barang dihapus.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name'               => 'required|string|max:255',
            'image'              => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'unit'               => 'required|string|max:30',
            'sell_price'         => 'required|integer|min:0',
            'buy_price'          => 'nullable|integer|min:0',
            'buy_price_unit'     => 'nullable|in:base,purchase',
            'supplier'           => 'nullable|string|max:255',
            'has_bulk'           => 'nullable|boolean',
            'purchase_unit'      => 'required_if:has_bulk,1|nullable|string|max:30',
            'units_per_purchase' => 'required_if:has_bulk,1|nullable|integer|min:1',
            'bulk_item_price'    => 'nullable|integer|min:0',
        ]);

        $hasBulk = $request->boolean('has_bulk');
        $per     = $hasBulk ? (int) $data['units_per_purchase'] : 1;

        // Harga beli "per satuan besar" hanya masuk akal kalau barangnya punya satuan besar.
        $buyUnit = ($hasBulk && ($data['buy_price_unit'] ?? 'base') === 'purchase') ? 'purchase' : 'base';

        return [
            'name'               => $data['name'],
            'unit'               => $data['unit'],
            'sell_price'         => $data['sell_price'],
            'buy_price'          => $data['buy_price'] ?? 0,
            'buy_price_unit'     => $buyUnit,
            'supplier'           => $data['supplier'] ?? null,
            'purchase_unit'      => $hasBulk ? $data['purchase_unit'] : null,
            'units_per_purchase' => $per,
            'bulk_price'         => ($hasBulk && isset($data['bulk_item_price']))
                                        ? $data['bulk_item_price'] * $per
                                        : null,
        ];
    }
}