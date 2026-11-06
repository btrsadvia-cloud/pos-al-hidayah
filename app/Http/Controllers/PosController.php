<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PosController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->isCashier() && ! $user->hasOpenShift()) {
            return redirect()->route('shift.index')
                ->with('warning', 'Anda harus membuka shift terlebih dahulu sebelum menggunakan POS.');
        }

        return view('pos.index');
    }

    public function checkout()
    {
        // Checkout handled via Livewire PosTerminal component
        abort(404);
    }
}

