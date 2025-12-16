<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class PosController extends Controller
{
    /**
     * Display the POS interface.
     */
    public function index(): Response
    {
        $menus = Menu::where('is_available', true)
            ->orderBy('category')
            ->orderBy('name')
            ->get();

        return Inertia::render('Pos/Index', [
            'menus' => $menus,
        ]);
    }

    /**
     * Store a new order/transaction.
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.menu_id' => 'required|exists:menus,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|integer|min:0',
            'items.*.subtotal' => 'required|integer|min:0',
        ]);

        try {
            DB::beginTransaction();

            // Create the transaction
            $transaction = Transaction::create([
                'user_id' => auth()->id(),
                'customer_name' => $request->customer_name,
                'status' => 'unpaid',
                'total_amount' => 0,
            ]);

            // Create transaction items
            foreach ($request->items as $item) {
                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'menu_id' => $item['menu_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $item['subtotal'],
                    'is_printed' => false,
                ]);
            }

            // Recalculate total
            $transaction->recalculateTotal();

            DB::commit();

            return redirect()->back()->with('success', "Pesanan untuk {$request->customer_name} berhasil dibuat!");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal membuat pesanan: ' . $e->getMessage());
        }
    }

    /**
     * Get unpaid orders for the current session.
     */
    public function unpaidOrders(): Response
    {
        $orders = Transaction::with('items.menu')
            ->where('user_id', auth()->id())
            ->where('status', 'unpaid')
            ->whereDate('created_at', today())
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Pos/UnpaidOrders', [
            'orders' => $orders,
        ]);
    }

    /**
     * Mark an order as paid.
     */
    public function pay(Request $request, Transaction $transaction)
    {
        $request->validate([
            'payment_method' => 'required|in:cash,qris,transfer',
        ]);

        $transaction->markAsPaid($request->payment_method);

        return redirect()->back()->with('success', "Pembayaran untuk {$transaction->customer_name} berhasil!");
    }

    /**
     * Cancel an order.
     */
    public function cancel(Transaction $transaction)
    {
        $transaction->markAsCancelled();

        return redirect()->back()->with('success', "Pesanan untuk {$transaction->customer_name} dibatalkan.");
    }
}
