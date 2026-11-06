<?php

namespace App\Http\Controllers;

use App\Exports\SalesExport;
use App\Models\Sale;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->startOfMonth()->toDateString());
        $dateTo = $request->input('date_to', now()->toDateString());

        $query = Sale::with(['user', 'customer'])
            ->where('status', 'completed')
            ->whereDate('sold_at', '>=', $dateFrom)
            ->whereDate('sold_at', '<=', $dateTo);

        $sales = (clone $query)->orderByDesc('sold_at')->paginate(20)->withQueryString();

        $summary = (clone $query)->selectRaw('
            COUNT(*) as total_transactions,
            COALESCE(SUM(subtotal), 0) as total_subtotal,
            COALESCE(SUM(discount), 0) as total_discount,
            COALESCE(SUM(total), 0) as total_revenue
        ')->first();

        $paymentBreakdown = (clone $query)->selectRaw('
            payment_method,
            COUNT(*) as count,
            COALESCE(SUM(total), 0) as total
        ')->groupBy('payment_method')->get();

        return view('reports.index', compact('sales', 'summary', 'paymentBreakdown', 'dateFrom', 'dateTo'));
    }

    public function exportPdf(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->startOfMonth()->toDateString());
        $dateTo = $request->input('date_to', now()->toDateString());

        $sales = Sale::with(['user', 'customer', 'items'])
            ->where('status', 'completed')
            ->whereDate('sold_at', '>=', $dateFrom)
            ->whereDate('sold_at', '<=', $dateTo)
            ->orderBy('sold_at')
            ->get();

        $totalRevenue = $sales->sum('total');
        $totalDiscount = $sales->sum('discount');

        $pdf = Pdf::loadView('reports.pdf', compact('sales', 'dateFrom', 'dateTo', 'totalRevenue', 'totalDiscount'));
        $pdf->setPaper('a4', 'portrait');

        return $pdf->download("laporan-penjualan-{$dateFrom}-sd-{$dateTo}.pdf");
    }

    public function exportExcel(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->startOfMonth()->toDateString());
        $dateTo = $request->input('date_to', now()->toDateString());

        return Excel::download(
            new SalesExport($dateFrom, $dateTo),
            "laporan-penjualan-{$dateFrom}-sd-{$dateTo}.xlsx"
        );
    }
}
