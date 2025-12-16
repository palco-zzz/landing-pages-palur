<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Exports\TransactionExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    /**
     * Display the reports dashboard with analytics.
     */
    public function index(Request $request): Response
    {
        // Date range defaults to current month
        $startDate = $request->input('start_date')
            ? Carbon::parse($request->input('start_date'))->startOfDay()
            : Carbon::now()->startOfMonth();

        $endDate = $request->input('end_date')
            ? Carbon::parse($request->input('end_date'))->endOfDay()
            : Carbon::now()->endOfMonth();

        // Query 1: Hourly Transaction Trend (0-23 hours)
        $hourlyData = Transaction::where('status', 'paid')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(DB::raw('HOUR(created_at) as hour'), DB::raw('COUNT(*) as count'))
            ->groupBy('hour')
            ->pluck('count', 'hour')
            ->toArray();

        // Fill missing hours with 0
        $hourlyTrend = [];
        for ($i = 0; $i < 24; $i++) {
            $hourlyTrend[] = [
                'hour' => sprintf('%02d:00', $i),
                'count' => $hourlyData[$i] ?? 0,
            ];
        }

        // Query 2: Payment Method Distribution
        $paymentMethods = Transaction::where('status', 'paid')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(total_amount) as total'))
            ->groupBy('payment_method')
            ->get()
            ->map(fn ($item) => [
                'method' => $item->payment_method ?? 'Unknown',
                'count' => $item->count,
                'total' => $item->total,
            ]);

        // Query 3: Menu Performance - Top 5 Best Sellers
        $topMenus = TransactionItem::join('menus', 'transaction_items.menu_id', '=', 'menus.id')
            ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
            ->where('transactions.status', 'paid')
            ->where('transaction_items.status', 'active')
            ->whereBetween('transactions.created_at', [$startDate, $endDate])
            ->select('menus.name', DB::raw('SUM(transaction_items.quantity) as total_sold'))
            ->groupBy('menus.id', 'menus.name')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        // Query 4: Menu Performance - Bottom 5 Least Sellers
        $bottomMenus = TransactionItem::join('menus', 'transaction_items.menu_id', '=', 'menus.id')
            ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
            ->where('transactions.status', 'paid')
            ->where('transaction_items.status', 'active')
            ->whereBetween('transactions.created_at', [$startDate, $endDate])
            ->select('menus.name', DB::raw('SUM(transaction_items.quantity) as total_sold'))
            ->groupBy('menus.id', 'menus.name')
            ->orderBy('total_sold')
            ->limit(5)
            ->get();

        // Summary Stats
        $summary = [
            'total_transactions' => Transaction::where('status', 'paid')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count(),
            'total_revenue' => Transaction::where('status', 'paid')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum('total_amount'),
            'average_order' => Transaction::where('status', 'paid')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->avg('total_amount') ?? 0,
        ];

        return Inertia::render('Reports/Index', [
            'hourlyTrend' => $hourlyTrend,
            'paymentMethods' => $paymentMethods,
            'topMenus' => $topMenus,
            'bottomMenus' => $bottomMenus,
            'summary' => $summary,
            'filters' => [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
            ],
        ]);
    }

    /**
     * Export transactions as PDF.
     */
    public function exportPdf(Request $request)
    {
        $startDate = $request->input('start_date')
            ? Carbon::parse($request->input('start_date'))->startOfDay()
            : Carbon::now()->startOfMonth();

        $endDate = $request->input('end_date')
            ? Carbon::parse($request->input('end_date'))->endOfDay()
            : Carbon::now()->endOfMonth();

        $transactions = Transaction::with(['user', 'items.menu'])
            ->where('status', 'paid')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();

        $grandTotal = $transactions->sum('total_amount');

        $pdf = Pdf::loadView('reports.pdf_daily', [
            'transactions' => $transactions,
            'startDate' => $startDate->format('d M Y'),
            'endDate' => $endDate->format('d M Y'),
            'grandTotal' => $grandTotal,
        ]);

        $filename = 'laporan_' . $startDate->format('Ymd') . '_' . $endDate->format('Ymd') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Export transactions as Excel.
     */
    public function exportExcel(Request $request)
    {
        $startDate = $request->input('start_date')
            ? Carbon::parse($request->input('start_date'))->startOfDay()
            : Carbon::now()->startOfMonth();

        $endDate = $request->input('end_date')
            ? Carbon::parse($request->input('end_date'))->endOfDay()
            : Carbon::now()->endOfMonth();

        $filename = 'laporan_' . $startDate->format('Ymd') . '_' . $endDate->format('Ymd') . '.xlsx';

        return Excel::download(new TransactionExport($startDate, $endDate), $filename);
    }
}
