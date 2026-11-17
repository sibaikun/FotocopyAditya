<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::orderBy('name')->get();

        return view('services.index', compact('services'));
    }

    public function create()
    {
        return view('services.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'  => 'required|string|max:255',
            'price' => 'required|integer|min:0',
        ]);

        Service::create($data + ['is_active' => $request->boolean('is_active')]);

        return redirect()->route('services.index')->with('success', 'Jasa ditambahkan.');
    }

    public function edit(Service $service)
    {
        return view('services.edit', compact('service'));
    }

    public function update(Request $request, Service $service)
    {
        $data = $request->validate([
            'name'  => 'required|string|max:255',
            'price' => 'required|integer|min:0',
        ]);

        $service->update($data + ['is_active' => $request->boolean('is_active')]);

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
}