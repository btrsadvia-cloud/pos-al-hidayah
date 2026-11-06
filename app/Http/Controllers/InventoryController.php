<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $query = StockMovement::with(['product', 'user', 'supplier'])->latest('movement_date')->latest('id');

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->filled('product')) {
            $p = $request->product;
            $query->whereHas('product', fn($q) => $q->where('name', 'like', "%{$p}%")->orWhere('barcode', 'like', "%{$p}%"));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('movement_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('movement_date', '<=', $request->date_to);
        }

        $movements = $query->paginate(20)->withQueryString();
        $products  = Product::orderBy('name')->get();

        return view('inventory.index', compact('movements', 'products'));
    }

    public function stockInForm()
    {
        $products  = Product::orderBy('name')->get();
        $suppliers = Supplier::orderBy('name')->get();
        return view('inventory.stock-in', compact('products', 'suppliers'));
    }

    public function storeStockIn(Request $request)
    {
        $data = $request->validate([
            'product_id'    => ['required', 'exists:products,id'],
            'supplier_id'   => ['required', 'exists:suppliers,id'],
            'quantity'      => ['required', 'integer', 'min:1'],
            'unit_cost'     => ['required', 'integer', 'min:0'],
            'reason'        => ['nullable', 'string', 'max:255'],
            'movement_date' => ['required', 'date'],
        ], [
            'product_id.required'  => 'Produk wajib dipilih.',
            'supplier_id.required' => 'Pemasok wajib dipilih.',
            'quantity.required'    => 'Jumlah stok wajib diisi.',
            'unit_cost.required'   => 'Harga beli per satuan (HPP) wajib diisi.',
        ]);

        DB::transaction(function () use ($data) {
            $product = Product::lockForUpdate()->findOrFail($data['product_id']);
            $product->increment('stock', $data['quantity']);

            $movement = StockMovement::create([
                'product_id'    => $product->id,
                'type'          => 'in',
                'quantity'      => $data['quantity'],
                'unit_cost'     => $data['unit_cost'],
                'supplier_id'   => $data['supplier_id'],
                'reason'        => $data['reason'] ?: 'Pembelian dari Pemasok',
                'user_id'       => auth()->id(),
                'movement_date' => $data['movement_date'],
            ]);

            AuditLog::record(
                action: 'Stok Masuk',
                subject: $movement,
                newValues: ['product' => $product->name, 'qty' => $data['quantity'], 'cost' => $data['unit_cost']]
            );
        });

        return redirect()->route('inventory.index')->with('success', 'Stok masuk berhasil dicatat.');
    }

    public function stockOutForm()
    {
        $products = Product::where('stock', '>', 0)->orderBy('name')->get();
        return view('inventory.stock-out', compact('products'));
    }

    public function storeStockOut(Request $request)
    {
        $data = $request->validate([
            'product_id'    => ['required', 'exists:products,id'],
            'quantity'      => ['required', 'integer', 'min:1'],
            'reason'        => ['required', 'string', 'max:255'],
            'movement_date' => ['required', 'date'],
        ], [
            'product_id.required' => 'Produk wajib dipilih.',
            'quantity.required'   => 'Jumlah stok wajib diisi.',
            'reason.required'     => 'Alasan stok keluar wajib diisi (rusak, hilang, retur, dll).',
        ]);

        DB::transaction(function () use ($data) {
            $product = Product::lockForUpdate()->findOrFail($data['product_id']);

            if ($product->stock < $data['quantity']) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'quantity' => "Stok produk '{$product->name}' tidak mencukupi (sisa: {$product->stock}).",
                ]);
            }

            $product->decrement('stock', $data['quantity']);

            $movement = StockMovement::create([
                'product_id'    => $product->id,
                'type'          => 'out',
                'quantity'      => $data['quantity'],
                'unit_cost'     => $product->latestCost(),
                'supplier_id'   => null,
                'reason'        => $data['reason'],
                'user_id'       => auth()->id(),
                'movement_date' => $data['movement_date'],
            ]);

            AuditLog::record(
                action: 'Stok Keluar Manual',
                subject: $movement,
                newValues: ['product' => $product->name, 'qty' => $data['quantity'], 'reason' => $data['reason']]
            );
        });

        return redirect()->route('inventory.index')->with('success', 'Stok keluar berhasil dicatat.');
    }

    public function stockCard(Product $product)
    {
        $movements = StockMovement::where('product_id', $product->id)
            ->with(['user', 'supplier'])
            ->orderBy('movement_date')
            ->orderBy('id')
            ->get();

        // Calculate running balance
        $balance = 0;
        $cardRows = $movements->map(function ($m) use (&$balance) {
            if ($m->type === 'in') {
                $balance += $m->quantity;
            } else {
                $balance -= $m->quantity;
            }
            $m->running_balance = $balance;
            return $m;
        });

        return view('inventory.card', compact('product', 'cardRows'));
    }
}
