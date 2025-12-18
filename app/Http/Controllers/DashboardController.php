<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Display the dashboard with analytics.
     * Admin: Full analytics
     * Cashier: Limited data (no financial info)
     */
    public function index(Request $request): Response
    {
        $user = auth()->user();
        $isAdmin = $user->role === 'admin';

        // Cashier: Return minimal data (no financial info)
        if (!$isAdmin) {
            return Inertia::render('Dashboard', [
                'stats' => null,
                'chart' => null,
                'top_items' => null,
                'recent_transactions' => null,
            ]);
        }

        // Admin: Full analytics
        $today = Carbon::today();

        // Today's stats
        $todayRevenue = Transaction::where('status', 'paid')
            ->whereDate('created_at', $today)
            ->sum('total_amount');

        $todayCount = Transaction::where('status', 'paid')
            ->whereDate('created_at', $today)
            ->count();

        // Payment method breakdown (today)
        $paymentBreakdown = Transaction::where('status', 'paid')
            ->whereDate('created_at', $today)
            ->select('payment_method', DB::raw('count(*) as count'), DB::raw('sum(total_amount) as total'))
            ->groupBy('payment_method')
            ->get()
            ->keyBy('payment_method');

        $cashStats = $paymentBreakdown->get('cash', (object)['count' => 0, 'total' => 0]);
        $qrisStats = $paymentBreakdown->get('qris', (object)['count' => 0, 'total' => 0]);
        $transferStats = $paymentBreakdown->get('transfer', (object)['count' => 0, 'total' => 0]);

        // Daily revenue for last 7 days
        $dailyRevenue = [];
        $labels = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $labels[] = $date->format('D'); // Mon, Tue, etc.
            
            $revenue = Transaction::where('status', 'paid')
                ->whereDate('created_at', $date)
                ->sum('total_amount');
            
            $dailyRevenue[] = $revenue;
        }

        // Top 5 items
        $topItems = TransactionItem::join('menus', 'transaction_items.menu_id', '=', 'menus.id')
            ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
            ->where('transactions.status', 'paid')
            ->whereDate('transactions.created_at', '>=', Carbon::today()->subDays(30))
            ->select(
                'menus.name',
                DB::raw('SUM(transaction_items.quantity) as total_qty'),
                DB::raw('SUM(transaction_items.subtotal) as total_revenue')
            )
            ->groupBy('menus.id', 'menus.name')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        // Active orders count (unpaid today)
        $activeOrdersCount = Transaction::where('status', 'unpaid')
            ->whereDate('created_at', $today)
            ->count();

        return Inertia::render('Dashboard', [
            'stats' => [
                'today_revenue' => $todayRevenue,
                'today_count' => $todayCount,
                'active_orders' => $activeOrdersCount,
                'cash' => [
                    'count' => $cashStats->count ?? 0,
                    'total' => $cashStats->total ?? 0,
                ],
                'qris' => [
                    'count' => $qrisStats->count ?? 0,
                    'total' => $qrisStats->total ?? 0,
                ],
                'transfer' => [
                    'count' => $transferStats->count ?? 0,
                    'total' => $transferStats->total ?? 0,
                ],
            ],
            'chart' => [
                'labels' => $labels,
                'data' => $dailyRevenue,
            ],
            'top_items' => $topItems,
            'recent_transactions' => Transaction::with(['user'])
                ->where('status', 'paid')
                ->latest()
                ->limit(5)
                ->get(),
        ]);
    }
}
