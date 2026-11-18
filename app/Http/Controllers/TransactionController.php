<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Service;
use App\Models\StockMovement;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with('user')
            ->latest('date')->latest('id')
            ->paginate(20);

        return view('transactions.index', compact('transactions'));
    }

    public function create()
    {
        $products = Product::forSale()->where('stock', '>', 0)->orderBy('name')
            ->get(['id', 'name', 'unit', 'purchase_unit', 'units_per_purchase', 'sell_price', 'bulk_price', 'stock']);

        $services = Service::where('is_active', true)->orderBy('name')
            ->get(['id', 'name', 'price']);

        return view('transactions.create', compact('products', 'services'));
    }

public function store(Request $request)
{
    $data = $request->validate([
        'items'         => 'required|array|min:1',
        'items.*.type'  => 'required|in:product,service',
        'items.*.id'    => 'required|integer',
        'items.*.qty'   => 'required|integer|min:1',
        'items.*.unit'  => 'nullable|in:base,purchase',
        'items.*.price' => 'nullable|integer|min:0',
        'paid'          => 'required|integer|min:0',
    ]);

    $transaction = DB::transaction(function () use ($data) {
        $today = now()->toDateString();

        $seq = Transaction::whereDate('date', $today)->lockForUpdate()->count() + 1;
        $invoiceNo = 'FCA-' . now()->format('Ymd') . '-' . str_pad($seq, 3, '0', STR_PAD_LEFT);

        $transaction = Transaction::create([
            'invoice_no' => $invoiceNo,
            'date'       => $today,
            'total'      => 0,
            'paid'       => $data['paid'],
            'user_id'    => auth()->id(),
        ]);

        $total = 0;

        foreach ($data['items'] as $row) {
            $qty = (int) $row['qty'];

        if ($row['type'] === 'product') {
            $product = Product::forSale()->lockForUpdate()->findOrFail($row['id']);   // hanya barang yang dijual

            $isBulk = ($row['unit'] ?? 'base') === 'purchase';

            if ($isBulk && (! $product->purchase_unit || ! $product->bulk_price)) {
                throw ValidationException::withMessages([
                    'items' => "{$product->name} tidak dijual borongan.",
                ]);
            }

            $factor   = $isBulk ? $product->units_per_purchase : 1;
            $baseQty  = $qty * $factor;
            $price    = $isBulk ? $product->bulk_price : $product->sell_price; // harga selalu dari database
            $unitName = $isBulk ? $product->purchase_unit : $product->unit;

            if ($product->stock < $baseQty) {
                throw ValidationException::withMessages([
                    'items' => "Stok {$product->name} tidak cukup (sisa {$product->stock} {$product->unit}).",
                ]);
            }

            StockMovement::create([
                'product_id'     => $product->id,
                'type'           => 'out',
                'qty'            => $baseQty,
                'input_qty'      => $qty,
                'input_unit'     => $unitName,
                'reason'         => 'sale',
                'transaction_id' => $transaction->id,
                'note'           => 'Penjualan ' . $invoiceNo,
                'date'           => $transaction->date,
            ]);

            $product->decrement('stock', $baseQty);

            $item = [
                'product_id' => $product->id,
                'item_name'  => $product->name,
                'unit'       => $unitName,
                'base_qty'   => $baseQty,
            ];
            } else {
                $service = Service::with('supplies')->findOrFail($row['id']);
                $price   = (int) ($row['price'] ?? $service->price); // jasa boleh diubah di kasir

                // Bahan yang terpakai jasa ini ikut dikurangi (jumlah per jasa x jumlah jasa)
                foreach ($service->supplies as $supply) {
                    $used = $supply->pivot->qty * $qty;

                    $material = Product::supplies()->lockForUpdate()->find($supply->id);
                    if (! $material) {
                        continue;
                    }

                    StockMovement::create([
                        'product_id'     => $material->id,
                        'type'           => 'out',
                        'qty'            => $used,
                        'input_qty'      => $used,
                        'input_unit'     => $material->unit,
                        'reason'         => 'service',
                        'transaction_id' => $transaction->id,
                        'note'           => $service->name . ' x' . $qty,
                        'date'           => $transaction->date,
                    ]);

                    $material->decrement('stock', $used);
                }

                $item = [
                    'service_id' => $service->id,
                    'item_name'  => $service->name,
                    'unit'       => null,
                    'base_qty'   => $qty,
                ];
            }

            $subtotal = $price * $qty;
            $total   += $subtotal;

            TransactionItem::create($item + [
                'transaction_id' => $transaction->id,
                'item_type'      => $row['type'],
                'qty'            => $qty,
                'price'          => $price,
                'subtotal'       => $subtotal,
            ]);
        }

        if ($data['paid'] < $total) {
            throw ValidationException::withMessages([
                'paid' => 'Uang yang dibayar kurang dari total.',
            ]);
        }

        $transaction->update(['total' => $total]);

        return $transaction;
    });

    return redirect()->route('transactions.show', $transaction);
}

    public function show(Transaction $transaction)
    {
        $transaction->load('items', 'user');

        return view('transactions.show', compact('transaction'));
    }
}