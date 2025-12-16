<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class HistoryController extends Controller
{
    /**
     * Display transaction history.
     */
    public function index(Request $request): Response
    {
        $date = $request->input('date', now()->format('Y-m-d'));
        $search = $request->input('search', '');

        $transactions = Transaction::with(['items.menu', 'user'])
            ->where('status', 'paid')
            ->whereDate('created_at', $date)
            ->when($search, function ($query, $search) {
                $query->where('customer_name', 'like', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('History/Index', [
            'transactions' => $transactions,
            'filters' => [
                'date' => $date,
                'search' => $search,
            ],
        ]);
    }
}
