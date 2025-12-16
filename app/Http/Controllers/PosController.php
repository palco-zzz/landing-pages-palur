<?php

namespace App\Http\Controllers;

use App\Models\Category;
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
        $menus = Menu::with('category')
            ->orderBy('name')
            ->get();

        $categories = Category::orderBy('name')->get();

        $activeOrders = Transaction::with('items.menu')
            ->where('status', 'unpaid')
            ->whereDate('created_at', today())
            ->latest()
            ->get();

        return Inertia::render('Pos/Index', [
            'menus' => $menus,
            'categories' => $categories,
            'activeOrders' => $activeOrders,
        ]);
    }

    /**
     * Format receipt data for printing.
     */
    private function formatReceipt(Transaction $transaction, array $items, string $type, array $extra = []): array
    {
        $receiptData = [
            'type' => $type,
            'title' => $type === 'kitchen' ? 'PESANAN DAPUR' : 'STRUK PEMBAYARAN',
            'subtitle' => $extra['subtitle'] ?? null,
            'store_name' => 'Bakmi Jowo Palur',
            'date' => now()->format('d/m/Y H:i'),
            'cashier' => auth()->user()->name ?? 'Kasir',
            'customer_name' => $transaction->customer_name,
            'order_number' => $transaction->uuid ? substr($transaction->uuid, 0, 8) : $transaction->id,
            'items' => collect($items)->map(function ($item) {
                return [
                    'name' => $item['menu_name'] ?? ($item['menu']['name'] ?? 'Item'),
                    'qty' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $item['subtotal'],
                ];
            })->toArray(),
            'total' => $transaction->total_amount,
        ];

        // Add payment info for customer receipts
        if ($type === 'customer') {
            $receiptData['payment_method'] = $extra['payment_method'] ?? null;
            $receiptData['cash_received'] = $extra['cash_received'] ?? null;
            $receiptData['change'] = $extra['change'] ?? null;
        }

        return $receiptData;
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

            $createdItems = [];

            // Create transaction items
            foreach ($request->items as $item) {
                $menu = Menu::find($item['menu_id']);
                $transactionItem = TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'menu_id' => $item['menu_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $item['subtotal'],
                    'is_printed' => false,
                ]);

                $createdItems[] = [
                    'menu_name' => $menu->name,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $item['subtotal'],
                ];
            }

            // Recalculate total
            $transaction->recalculateTotal();

            DB::commit();

            // Prepare print job for kitchen
            $printJob = $this->formatReceipt($transaction, $createdItems, 'kitchen');

            return redirect()->back()
                ->with('success', "Pesanan untuk {$request->customer_name} berhasil dibuat!")
                ->with('print_job', $printJob);

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

            $createdItems = [];

            // Add new items to the transaction
            foreach ($request->items as $item) {
                $menu = Menu::find($item['menu_id']);
                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'menu_id' => $item['menu_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $item['subtotal'],
                    'is_printed' => false, // New items need to be printed
                ]);

                $createdItems[] = [
                    'menu_name' => $menu->name,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $item['subtotal'],
                ];
            }

            // Recalculate total
            $transaction->recalculateTotal();

            DB::commit();

            // Prepare print job for kitchen with TAMBAHAN label
            $printJob = $this->formatReceipt($transaction, $createdItems, 'kitchen', [
                'subtitle' => '** TAMBAHAN **',
            ]);

            return redirect()->back()
                ->with('success', "Tambahan pesanan untuk {$transaction->customer_name} berhasil!")
                ->with('print_job', $printJob);

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
        $cashReceived = $request->cash_received ?? 0;
        $change = 0;

        if ($request->payment_method === 'cash' && $cashReceived) {
            if ($cashReceived < $transaction->total_amount) {
                return redirect()->back()->with('error', 'Uang yang diterima kurang dari total transaksi.');
            }
            $change = $cashReceived - $transaction->total_amount;
        }

        // Load items for receipt
        $transaction->load('items.menu');

        $allItems = $transaction->items->map(function ($item) {
            return [
                'menu_name' => $item->menu->name,
                'quantity' => $item->quantity,
                'price' => $item->price,
                'subtotal' => $item->subtotal,
            ];
        })->toArray();

        // Mark as paid
        $transaction->markAsPaid($request->payment_method);

        // Mark all items as printed
        $transaction->items()->update(['is_printed' => true]);

        $message = "Pembayaran untuk {$transaction->customer_name} berhasil! Total: Rp " . number_format($transaction->total_amount, 0, ',', '.');

        if ($request->payment_method === 'cash' && $cashReceived) {
            $message .= " | Kembalian: Rp " . number_format($change, 0, ',', '.');
        }

        // Prepare customer receipt
        $printJob = $this->formatReceipt($transaction, $allItems, 'customer', [
            'payment_method' => $request->payment_method,
            'cash_received' => $cashReceived ?: null,
            'change' => $change ?: null,
        ]);

        return redirect()->back()
            ->with('success', $message)
            ->with('print_job', $printJob);
    }

    /**
     * Void/cancel an item from an order.
     */
    public function voidItem(TransactionItem $item)
    {
        // Can only void active items
        if ($item->status !== 'active') {
            return redirect()->back()->with('error', 'Item ini sudah dibatalkan sebelumnya.');
        }

        // Load relationships
        $item->load('transaction', 'menu');
        $transaction = $item->transaction;

        // Can only void unpaid transactions
        if ($transaction->status !== 'unpaid') {
            return redirect()->back()->with('error', 'Tidak dapat membatalkan item pada transaksi yang sudah dibayar.');
        }

        // Update item status to void
        $item->update(['status' => 'void']);

        // Recalculate transaction total (only active items)
        $newTotal = $transaction->items()
            ->where('status', 'active')
            ->sum('subtotal');
        $transaction->update(['total_amount' => $newTotal]);

        // Create void ticket for kitchen
        $printJob = [
            'type' => 'void',
            'title' => 'VOID / BATAL',
            'store_name' => 'Bakmi Jowo Palur',
            'date' => now()->format('d/m/Y H:i'),
            'cashier' => auth()->user()->name ?? 'Kasir',
            'customer_name' => $transaction->customer_name,
            'order_number' => $transaction->uuid ? substr($transaction->uuid, 0, 8) : $transaction->id,
            'items' => [
                [
                    'name' => $item->menu->name ?? 'Item',
                    'qty' => $item->quantity,
                    'status' => 'DIBATALKAN',
                ],
            ],
        ];

        return redirect()->back()
            ->with('success', "Item {$item->menu->name} berhasil dibatalkan!")
            ->with('print_job', $printJob);
    }
}
