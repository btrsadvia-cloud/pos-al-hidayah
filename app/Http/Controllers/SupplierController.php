<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $query = Supplier::latest();
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('name', 'like', "%{$s}%")->orWhere('contact', 'like', "%{$s}%"));
        }
        $suppliers = $query->paginate(15)->withQueryString();
        return view('suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('suppliers.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'         => ['required', 'string', 'max:100'],
            'contact'      => ['required', 'string', 'max:100'],
            'address'      => ['required', 'string'],
            'bank_account' => ['nullable', 'string', 'max:100'],
        ]);

        Supplier::create($data);
        return redirect()->route('suppliers.index')->with('success', 'Pemasok berhasil ditambahkan.');
    }

    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $data = $request->validate([
            'name'         => ['required', 'string', 'max:100'],
            'contact'      => ['required', 'string', 'max:100'],
            'address'      => ['required', 'string'],
            'bank_account' => ['nullable', 'string', 'max:100'],
        ]);

        $supplier->update($data);
        return redirect()->route('suppliers.index')->with('success', 'Pemasok berhasil diperbarui.');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();
        return redirect()->route('suppliers.index')->with('success', 'Pemasok berhasil dihapus.');
    }
}
