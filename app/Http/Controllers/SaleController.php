<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\User;
use App\Services\PosService;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Sale::with(['user', 'customer', 'shift'])->latest('sold_at');

        // Cashiers can only see their own sales
        if ($user->isCashier()) {
            $query->where('user_id', $user->id);
        } elseif ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('sold_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('sold_at', '<=', $request->date_to);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('invoice_number', 'like', "%{$s}%")
                  ->orWhereHas('customer', fn($cq) => $cq->where('name', 'like', "%{$s}%"));
            });
        }

        $sales = $query->paginate(15)->withQueryString();
        $cashiers = User::where('level', 2)->orderBy('name')->get();

        return view('sales.index', compact('sales', 'cashiers'));
    }

    public function show(Sale $sale)
    {
        $user = auth()->user();
        if ($user->isCashier() && $sale->user_id !== $user->id) {
            abort(403, 'Anda hanya dapat melihat transaksi Anda sendiri.');
        }

        $sale->load(['items.product', 'customer', 'user', 'shift']);
        return view('sales.show', compact('sale'));
    }

    public function receipt(Sale $sale)
    {
        $user = auth()->user();
        if ($user->isCashier() && $sale->user_id !== $user->id) {
            abort(403, 'Anda hanya dapat melihat struk transaksi Anda sendiri.');
        }

        $sale->load(['items', 'customer', 'user']);
        return view('sales.receipt', compact('sale'));
    }

    public function cancel(Request $request, Sale $sale, PosService $posService)
    {
        $request->validate([
            'reason' => ['required', 'string', 'max:255'],
        ], [
            'reason.required' => 'Alasan pembatalan wajib diisi.',
        ]);

        try {
            $posService->cancelSale($sale, auth()->user(), $request->reason);
            return redirect()->route('sales.show', $sale)->with('success', 'Transaksi berhasil dibatalkan dan stok telah dikembalikan.');
        } catch (\Exception $e) {
            return back()->withErrors(['cancel' => $e->getMessage()]);
        }
    }
}
