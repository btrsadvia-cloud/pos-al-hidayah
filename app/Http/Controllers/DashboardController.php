<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Shift;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $today = now()->toDateString();
        $yesterday = now()->subDay()->toDateString();

        // 1. Sales metrics today vs yesterday
        $todaySales = Sale::where('status', 'completed')
            ->whereDate('sold_at', $today)
            ->selectRaw('COUNT(*) as count, COALESCE(SUM(total),0) as revenue')
            ->first();

        $yesterdaySales = Sale::where('status', 'completed')
            ->whereDate('sold_at', $yesterday)
            ->selectRaw('COUNT(*) as count, COALESCE(SUM(total),0) as revenue')
            ->first();

        $todayRevenue      = (int) ($todaySales->revenue ?? 0);
        $todayTransactions = (int) ($todaySales->count ?? 0);
        $yesterdayRevenue  = (int) ($yesterdaySales->revenue ?? 0);

        // Revenue comparison percentage
        $revenueDiffPct = 0;
        if ($yesterdayRevenue > 0) {
            $revenueDiffPct = round((($todayRevenue - $yesterdayRevenue) / $yesterdayRevenue) * 100);
        }

        // 2. Inventory & Customer metrics
        $totalProductsCount = Product::whereNull('deleted_at')->count();
        $lowStockCount      = Product::whereNull('deleted_at')
            ->whereColumn('stock', '<=', 'min_stock')
            ->count();
        $totalCustomersCount = Customer::count();

        // 3. Payment Methods Breakdown Today (Tunai vs QRIS vs Transfer)
        $paymentBreakdown = Sale::where('status', 'completed')
            ->whereDate('sold_at', $today)
            ->select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(total) as total'))
            ->groupBy('payment_method')
            ->get()
            ->keyBy('payment_method');

        $cashTotal     = (int) ($paymentBreakdown['cash']->total ?? 0);
        $cashCount     = (int) ($paymentBreakdown['cash']->count ?? 0);
        $qrisTotal     = (int) ($paymentBreakdown['qris']->total ?? 0);
        $qrisCount     = (int) ($paymentBreakdown['qris']->count ?? 0);
        $transferTotal = (int) ($paymentBreakdown['transfer']->total ?? 0);
        $transferCount = (int) ($paymentBreakdown['transfer']->count ?? 0);

        // 4. Recent Transactions Today (Latest 6)
        $recentSales = Sale::with(['customer', 'user', 'items'])
            ->where('status', 'completed')
            ->orderByDesc('id')
            ->limit(6)
            ->get();

        // 5. Active Shift Status
        $activeShift = Shift::with('user')
            ->whereNull('closed_at')
            ->latest('opened_at')
            ->first();

        $data = compact(
            'todayRevenue',
            'todayTransactions',
            'yesterdayRevenue',
            'revenueDiffPct',
            'totalProductsCount',
            'lowStockCount',
            'totalCustomersCount',
            'cashTotal',
            'cashCount',
            'qrisTotal',
            'qrisCount',
            'transferTotal',
            'transferCount',
            'recentSales',
            'activeShift'
        );

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
