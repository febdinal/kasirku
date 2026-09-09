<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PosController extends Controller
{
    public function index(): View
    {
        $categories = Category::withCount(['products' => fn ($q) => $q->where('is_active', true)->where('stock', '>', 0)])->get();
        $allProductsCount = Product::where('is_active', true)->where('stock', '>', 0)->count();
        $customers = Customer::orderBy('name')->get();
        $ppnEnabled = Setting::get('ppn_enabled', '0') === '1';
        $ppnPercentage = (float) Setting::get('ppn_percentage', '0');
        $currencySymbol = Setting::get('currency_symbol', 'Rp');

        return view('pos.index', compact('categories', 'allProductsCount', 'customers', 'ppnEnabled', 'ppnPercentage', 'currencySymbol'));
    }

    public function getProducts(Request $request): JsonResponse
    {
        $query = Product::with('category')
            ->where('is_active', true)
            ->where('stock', '>', 0);

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        $products = $query->orderBy('name')->get();

        return response()->json($products);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'customer_id' => ['nullable', 'exists:customers,id'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.price' => ['required', 'numeric', 'min:0'],
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'payment_method' => ['required', 'in:cash,transfer,qris,debit,kredit'],
            'payment_amount' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $transaction = DB::transaction(function () use ($validated) {
            $ppnEnabled = Setting::get('ppn_enabled', '0') === '1';
            $ppnPercentage = (float) Setting::get('ppn_percentage', '0');

            $subtotal = 0;
            $itemsData = [];

            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                $itemSubtotal = $item['price'] * $item['quantity'];
                $subtotal += $itemSubtotal;

                $itemsData[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_sku' => $product->sku,
                    'price' => $item['price'],
                    'cost_price' => $product->cost_price,
                    'quantity' => $item['quantity'],
                    'subtotal' => $itemSubtotal,
                ];

                // Kurangi stok
                $product->decrement('stock', $item['quantity']);
            }

            $discountAmount = $validated['discount_amount'] ?? 0;
            $taxableAmount = $subtotal - $discountAmount;
            $taxAmount = $ppnEnabled ? ($taxableAmount * $ppnPercentage / 100) : 0;
            $total = $taxableAmount + $taxAmount;
            $changeAmount = $validated['payment_amount'] - $total;

            $transaction = Transaction::create([
                'invoice_number' => Transaction::generateInvoiceNumber(),
                'customer_id' => $validated['customer_id'] ?? null,
                'user_id' => auth()->id(),
                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'tax_amount' => $taxAmount,
                'total' => $total,
                'payment_amount' => $validated['payment_amount'],
                'change_amount' => max(0, $changeAmount),
                'payment_method' => $validated['payment_method'],
                'notes' => $validated['notes'] ?? null,
                'status' => 'completed',
            ]);

            $transaction->items()->createMany($itemsData);

            return $transaction;
        });

        return response()->json([
            'success' => true,
            'transaction_id' => $transaction->id,
            'invoice_number' => $transaction->invoice_number,
            'message' => 'Transaksi berhasil disimpan',
        ]);
    }
}
