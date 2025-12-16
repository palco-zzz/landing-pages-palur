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

        $activeOrders = Transaction::with('items.menu')
            ->where('status', 'unpaid')
            ->whereDate('created_at', today())
            ->latest()
            ->get();

        return Inertia::render('Pos/Index', [
            'menus' => $menus,
            'activeOrders' => $activeOrders,
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

    /**
     * Add items to an existing transaction (Add-on order).
     */
    public function addItem(Request $request, Transaction $transaction)
    {
        // Ensure transaction is still unpaid
        if ($transaction->status !== 'unpaid') {
            return redirect()->back()->with('error', 'Transaksi sudah ditutup.');
        }

        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.menu_id' => 'required|exists:menus,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|integer|min:0',
            'items.*.subtotal' => 'required|integer|min:0',
        ]);

        try {
            DB::beginTransaction();

            // Add new items to the transaction
            foreach ($request->items as $item) {
                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'menu_id' => $item['menu_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $item['subtotal'],
                    'is_printed' => false, // New items need to be printed
                ]);
            }

            // Recalculate total
            $transaction->recalculateTotal();

            DB::commit();

            return redirect()->back()->with('success', "Tambahan pesanan untuk {$transaction->customer_name} berhasil!");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menambah pesanan: ' . $e->getMessage());
        }
    }

    /**
     * Checkout/pay a transaction.
     */
    public function checkout(Request $request, Transaction $transaction)
    {
        // Ensure transaction is still unpaid
        if ($transaction->status !== 'unpaid') {
            return redirect()->back()->with('error', 'Transaksi sudah dibayar atau dibatalkan.');
        }

        $request->validate([
            'payment_method' => 'required|in:cash,qris,transfer',
            'cash_received' => 'nullable|numeric|min:0',
        ]);

        // For cash payments, validate sufficient amount
        if ($request->payment_method === 'cash' && $request->cash_received) {
            if ($request->cash_received < $transaction->total_amount) {
                return redirect()->back()->with('error', 'Uang yang diterima kurang dari total transaksi.');
            }
        }

        $transaction->markAsPaid($request->payment_method);

        $message = "Pembayaran untuk {$transaction->customer_name} berhasil! Total: Rp " . number_format($transaction->total_amount, 0, ',', '.');

        // Add change info for cash payments
        if ($request->payment_method === 'cash' && $request->cash_received) {
            $change = $request->cash_received - $transaction->total_amount;
            $message .= " | Kembalian: Rp " . number_format($change, 0, ',', '.');
        }

        return redirect()->back()->with('success', $message);
    }
}
