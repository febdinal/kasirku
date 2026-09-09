<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\TransactionItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function daily(Request $request): View
    {
        $date = $request->filled('date') ? $request->date : today()->toDateString();

        $transactions = Transaction::with(['customer', 'items'])
            ->whereDate('created_at', $date)
            ->where('status', 'completed')
            ->latest()
            ->get();

        $totalRevenue = $transactions->sum('total');
        $totalTransactions = $transactions->count();
        $totalItems = $transactions->sum(fn ($t) => $t->items->sum('quantity'));

        // Top produk hari ini
        $topProducts = TransactionItem::select('product_name', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(subtotal) as total_revenue'))
            ->whereHas('transaction', fn ($q) => $q->whereDate('created_at', $date)->where('status', 'completed'))
            ->groupBy('product_name')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        return view('reports.daily', compact(
            'date', 'transactions', 'totalRevenue', 'totalTransactions', 'totalItems', 'topProducts'
        ));
    }

    public function monthly(Request $request): View
    {
        $month = $request->filled('month') ? $request->month : today()->format('Y-m');
        [$year, $monthNum] = explode('-', $month);

        $dailyData = Transaction::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(total) as revenue'),
            DB::raw('COUNT(*) as transaction_count')
        )
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $monthNum)
            ->where('status', 'completed')
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get();

        $totalRevenue = (float) $dailyData->sum('revenue');
        $totalTransactions = (int) $dailyData->sum('transaction_count');

        // Total produk terjual bulan ini
        $totalItemsSold = (int) TransactionItem::whereHas('transaction', function ($q) use ($year, $monthNum) {
            $q->whereYear('created_at', $year)
                ->whereMonth('created_at', $monthNum)
                ->where('status', 'completed');
        })->sum('quantity');

        // Breakdown per metode pembayaran
        $paymentBreakdown = Transaction::select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(total) as total'))
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $monthNum)
            ->where('status', 'completed')
            ->groupBy('payment_method')
            ->get();

        // Siapkan data runtut waktu untuk semua hari dalam bulan (1 s/d hari terakhir)
        $dateObj = Carbon::createFromDate((int) $year, (int) $monthNum, 1);
        $daysInMonth = $dateObj->daysInMonth;
        $dailyMap = $dailyData->keyBy(fn ($d) => (int) Carbon::parse($d->date)->format('j'));

        $chartLabels = [];
        $chartRevenue = [];
        $chartTransactions = [];

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $chartLabels[] = str_pad($day, 2, '0', STR_PAD_LEFT);
            if ($dailyMap->has($day)) {
                $item = $dailyMap->get($day);
                $chartRevenue[] = (float) $item->revenue;
                $chartTransactions[] = (int) $item->transaction_count;
            } else {
                $chartRevenue[] = 0;
                $chartTransactions[] = 0;
            }
        }

        return view('reports.monthly', compact(
            'month', 'year', 'monthNum', 'dailyData', 'totalRevenue', 'totalTransactions',
            'totalItemsSold', 'paymentBreakdown', 'chartLabels', 'chartRevenue', 'chartTransactions'
        ));
    }

    public function profit(Request $request): View
    {
        $month = $request->filled('month') ? $request->month : today()->format('Y-m');
        [$year, $monthNum] = explode('-', $month);

        $items = TransactionItem::select(
            'product_name',
            DB::raw('SUM(quantity) as total_qty'),
            DB::raw('SUM(subtotal) as total_revenue'),
            DB::raw('SUM(cost_price * quantity) as total_cogs'),
            DB::raw('SUM(subtotal - (cost_price * quantity)) as total_profit')
        )
            ->whereHas('transaction', fn ($q) => $q
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $monthNum)
                ->where('status', 'completed'))
            ->groupBy('product_name')
            ->orderByDesc('total_profit')
            ->get();

        $totalRevenue = $items->sum('total_revenue');
        $totalCogs = $items->sum('total_cogs');
        $totalProfit = $items->sum('total_profit');
        $profitMargin = $totalRevenue > 0 ? ($totalProfit / $totalRevenue) * 100 : 0;

        return view('reports.profit', compact(
            'month', 'items', 'totalRevenue', 'totalCogs', 'totalProfit', 'profitMargin'
        ));
    }
}
