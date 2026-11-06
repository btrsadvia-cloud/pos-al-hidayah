<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Shift;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    public function index()
    {
        $user         = auth()->user();
        $currentShift = $user->currentShift();
        $shifts       = Shift::where('user_id', $user->id)
            ->latest('opened_at')
            ->paginate(10);

        return view('shift.index', compact('currentShift', 'shifts'));
    }

    public function open(Request $request)
    {
        $user = auth()->user();

        if ($user->hasOpenShift()) {
            return back()->withErrors(['shift' => 'Anda sudah memiliki shift yang terbuka.']);
        }

        $request->validate([
            'opening_cash' => ['required', 'numeric', 'min:0'],
        ], [
            'opening_cash.required' => 'Kas awal wajib diisi.',
            'opening_cash.min'      => 'Kas awal tidak boleh negatif.',
        ]);

        Shift::create([
            'user_id'      => $user->id,
            'opened_at'    => now(),
            'opening_cash' => (int) $request->opening_cash,
        ]);

        return redirect()->route('shift.index')->with('success', 'Shift berhasil dibuka.');
    }

    public function close(Request $request)
    {
        $user  = auth()->user();
        $shift = $user->currentShift();

        if (! $shift) {
            return back()->withErrors(['shift' => 'Tidak ada shift yang sedang terbuka.']);
        }

        $request->validate([
            'counted_cash' => ['required', 'numeric', 'min:0'],
        ], [
            'counted_cash.required' => 'Kas hasil hitung wajib diisi.',
            'counted_cash.min'      => 'Kas tidak boleh negatif.',
        ]);

        $cashSalesTotal = Sale::where('shift_id', $shift->id)
            ->where('status', 'completed')
            ->where('payment_method', 'cash')
            ->sum('total');

        $expectedCash = (int) $shift->opening_cash + (int) $cashSalesTotal;
        $countedCash  = (int) $request->counted_cash;
        $difference   = $countedCash - $expectedCash;

        $shift->update([
            'closed_at'     => now(),
            'expected_cash' => $expectedCash,
            'counted_cash'  => $countedCash,
            'difference'    => $difference,
        ]);

        $selisihLabel = $difference === 0
            ? 'pas'
            : ($difference > 0 ? 'lebih ' . format_rupiah($difference) : 'kurang ' . format_rupiah(abs($difference)));

        return redirect()->route('shift.index')
            ->with('success', "Shift berhasil ditutup. Selisih kas: {$selisihLabel}.");
    }
}

