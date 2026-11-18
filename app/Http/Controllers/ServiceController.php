<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::with('supplies')->orderBy('name')->get();

        return view('services.index', compact('services'));
    }

    public function create()
    {
        return view('services.create', ['supplyOptions' => $this->supplyOptions()]);
    }

    public function store(Request $request)
    {
        [$data, $supplies] = $this->validated($request);

        $service = Service::create($data + ['is_active' => $request->boolean('is_active')]);
        $service->supplies()->sync($supplies);

        return redirect()->route('services.index')->with('success', 'Jasa ditambahkan.');
    }

    public function edit(Service $service)
    {
        $service->load('supplies');

        return view('services.edit', [
            'service'       => $service,
            'supplyOptions' => $this->supplyOptions(),
        ]);
    }

    public function update(Request $request, Service $service)
    {
        [$data, $supplies] = $this->validated($request);

        $service->update($data + ['is_active' => $request->boolean('is_active')]);
        $service->supplies()->sync($supplies);

        return redirect()->route('services.index')->with('success', 'Jasa diperbarui.');
    }

    // Dipanggil oleh toggle switch di halaman daftar
    public function toggle(Service $service)
    {
        $service->update(['is_active' => ! $service->is_active]);

        return response()->json(['is_active' => $service->is_active]);
    }

    public function destroy(Service $service)
    {
        $service->delete();

        return back()->with('success', 'Jasa dihapus.');
    }

    // Daftar bahan internal yang boleh dipilih
    private function supplyOptions()
    {
        return Product::supplies()->orderBy('name')->get(['id', 'name', 'unit']);
    }

    private function validated(Request $request): array
    {
        // Buang baris bahan yang belum dipilih
        $request->merge([
            'supplies' => collect($request->input('supplies', []))
                ->filter(fn ($row) => ! empty($row['product_id']))
                ->values()
                ->all(),
        ]);

        $data = $request->validate([
            'name'                  => 'required|string|max:255',
            'price'                 => 'required|integer|min:0',
            'supplies'              => 'nullable|array',
            'supplies.*.product_id' => [
                'required', 'integer', 'distinct',
                Rule::exists('products', 'id')->where('is_for_sale', false), // hanya bahan internal
            ],
            'supplies.*.qty'        => 'required|integer|min:1',
        ]);

        // Bentuk yang dimengerti sync(): [id_bahan => ['qty' => jumlah]]
        $supplies = collect($data['supplies'] ?? [])
            ->mapWithKeys(fn ($row) => [(int) $row['product_id'] => ['qty' => (int) $row['qty']]])
            ->all();

        return [['name' => $data['name'], 'price' => $data['price']], $supplies];
    }
}