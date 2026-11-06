<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $today = now()->toDateString();

        $todaySales = Sale::where('status', 'completed')
            ->whereDate('sold_at', $today)
            ->selectRaw('COUNT(*) as count, COALESCE(SUM(total),0) as revenue')
            ->first();

        $todayRevenue       = (int) ($todaySales->revenue ?? 0);
        $todayTransactions  = (int) ($todaySales->count ?? 0);
        $lowStockCount      = Product::whereNull('deleted_at')
            ->whereColumn('stock', '<=', 'min_stock')
            ->count();

        $data = compact('todayRevenue', 'todayTransactions', 'lowStockCount');

        if ($user->isAdmin()) {
            $topProducts = SaleItem::select('product_name', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(subtotal) as total_revenue'))
                ->whereHas('sale', fn($q) => $q->where('status', 'completed')->where('sold_at', '>=', now()->subDays(30)))
                ->groupBy('product_name')
                ->orderByDesc('total_qty')
                ->limit(5)
                ->get();

            $trend = Sale::where('status', 'completed')
                ->where('sold_at', '>=', now()->subDays(29))
                ->selectRaw('DATE(sold_at) as date, SUM(total) as revenue')
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->keyBy('date');

            $trendLabels = [];
            $trendData   = [];
            for ($i = 29; $i >= 0; $i--) {
                $d             = now()->subDays($i)->toDateString();
                $trendLabels[] = now()->subDays($i)->format('d/m');
                $trendData[]   = (int) ($trend[$d]->revenue ?? 0);
            }

            $loyalCustomers = DB::table('sales')
                ->join('customers', 'sales.customer_id', '=', 'customers.id')
                ->where('sales.status', 'completed')
                ->whereNotNull('sales.customer_id')
                ->selectRaw('customers.id, customers.name, COUNT(*) as total_transactions, SUM(sales.total) as total_spend')
                ->groupBy('customers.id', 'customers.name')
                ->orderByDesc('total_transactions')
                ->limit(5)
                ->get();

            $data = array_merge($data, compact('topProducts', 'trendLabels', 'trendData', 'loyalCustomers'));
        }

        return view('dashboard', $data);
    }
}

