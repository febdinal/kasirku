<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TransactionController extends Controller
{
    public function index(Request $request): View
    {
        $query = Transaction::with(['customer', 'user'])
            ->latest();

        if ($request->filled('search')) {
            $query->where('invoice_number', 'like', '%'.$request->search.'%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $transactions = $query->paginate(20)->withQueryString();

        return view('transactions.index', compact('transactions'));
    }

    public function show(Transaction $transaction): View
    {
        $transaction->load(['customer', 'user', 'items.product']);

        return view('transactions.show', compact('transaction'));
    }

    public function printReceipt(Transaction $transaction): View
    {
        $transaction->load(['customer', 'user', 'items']);

        return view('transactions.receipt', compact('transaction'));
    }

    public function void(Transaction $transaction): RedirectResponse
    {
        if ($transaction->status === 'voided') {
            return back()->with('error', 'Transaksi sudah dibatalkan.');
        }

        // Kembalikan stok
        foreach ($transaction->items as $item) {
            if ($item->product) {
                $item->product->increment('stock', $item->quantity);
            }
        }

        $transaction->update(['status' => 'voided']);

        return redirect()->route('transactions.show', $transaction)
            ->with('success', 'Transaksi berhasil dibatalkan.');
    }
}
