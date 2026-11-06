<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function index()
    {
        $units = Unit::latest()->paginate(20);
        return view('units.index', compact('units'));
    }

    public function create()
    {
        return view('units.create');
    }

    public function store(Request $request)
    {
        $request->validate(['name' => ['required', 'string', 'max:50', 'unique:units,name']],
            ['name.required' => 'Nama satuan wajib diisi.', 'name.unique' => 'Nama satuan sudah ada.']);

        Unit::create(['name' => $request->name]);
        return redirect()->route('units.index')->with('success', 'Satuan berhasil ditambahkan.');
    }

    public function edit(Unit $unit)
    {
        return view('units.edit', compact('unit'));
    }

    public function update(Request $request, Unit $unit)
    {
        $request->validate(['name' => ['required', 'string', 'max:50', 'unique:units,name,' . $unit->id]],
            ['name.required' => 'Nama satuan wajib diisi.', 'name.unique' => 'Nama satuan sudah ada.']);

        $unit->update(['name' => $request->name]);
        return redirect()->route('units.index')->with('success', 'Satuan berhasil diperbarui.');
    }

    public function destroy(Unit $unit)
    {
        $unit->delete();
        return redirect()->route('units.index')->with('success', 'Satuan berhasil dihapus.');
    }
}
