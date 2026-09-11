<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

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

    public function exportExcel(Request $request): Response
    {
        $periodType = $request->input('period_type', 'daily');

        $query = Transaction::with(['customer', 'items', 'user'])
            ->where('status', 'completed');

        if ($periodType === 'daily') {
            $date = $request->filled('date') ? $request->date : today()->toDateString();
            $query->whereDate('created_at', $date);
            $periodLabel = 'Harian - '.Carbon::parse($date)->translatedFormat('d F Y');
            $filename = 'laporan-penjualan-harian-'.$date.'.xls';
        } elseif ($periodType === 'weekly') {
            $startDate = $request->filled('start_date') ? $request->start_date : today()->subDays(6)->toDateString();
            $endDate = $request->filled('end_date') ? $request->end_date : today()->toDateString();
            $query->whereBetween('created_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay(),
            ]);
            $periodLabel = 'Mingguan ('.Carbon::parse($startDate)->format('d/m/Y').' - '.Carbon::parse($endDate)->format('d/m/Y').')';
            $filename = 'laporan-penjualan-mingguan-'.$startDate.'-sd-'.$endDate.'.xls';
        } elseif ($periodType === 'monthly') {
            $month = $request->filled('month') ? $request->month : today()->format('Y-m');
            [$year, $monthNum] = explode('-', $month);
            $query->whereYear('created_at', $year)->whereMonth('created_at', $monthNum);
            $periodLabel = 'Bulanan - '.Carbon::createFromDate((int) $year, (int) $monthNum, 1)->translatedFormat('F Y');
            $filename = 'laporan-penjualan-bulanan-'.$month.'.xls';
        } else {
            // custom range
            $startDate = $request->filled('start_date') ? $request->start_date : today()->startOfMonth()->toDateString();
            $endDate = $request->filled('end_date') ? $request->end_date : today()->toDateString();
            $query->whereBetween('created_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay(),
            ]);
            $periodLabel = 'Periode '.Carbon::parse($startDate)->format('d/m/Y').' s/d '.Carbon::parse($endDate)->format('d/m/Y');
            $filename = 'laporan-penjualan-periode-'.$startDate.'-sd-'.$endDate.'.xls';
        }

        $transactions = $query->latest()->get();

        $totalTransactions = $transactions->count();
        $totalSubtotal = (float) $transactions->sum('subtotal');
        $totalDiscount = (float) $transactions->sum('discount_amount');
        $totalTax = (float) $transactions->sum('tax_amount');
        $totalRevenue = (float) $transactions->sum('total');

        // Breakdown per metode pembayaran
        $paymentBreakdown = $transactions->groupBy('payment_method')->map(function ($group, $method) {
            return [
                'method' => strtoupper($method),
                'count' => $group->count(),
                'total' => (float) $group->sum('total'),
            ];
        });

        // Rekap produk terjual
        $productsSold = [];
        foreach ($transactions as $t) {
            foreach ($t->items as $item) {
                $name = $item->product_name;
                if (! isset($productsSold[$name])) {
                    $productsSold[$name] = [
                        'name' => $name,
                        'qty' => 0,
                        'total' => 0,
                    ];
                }
                $productsSold[$name]['qty'] += $item->quantity;
                $productsSold[$name]['total'] += (float) $item->subtotal;
            }
        }
        uasort($productsSold, fn ($a, $b) => $b['qty'] <=> $a['qty']);

        $storeName = Setting::get('store_name', 'VENTRA');
        $storeAddress = Setting::get('store_address', '');
        $storePhone = Setting::get('store_phone', '');
        $currency = Setting::get('currency_symbol', 'Rp');

        $html = view('reports.excel_export', compact(
            'transactions',
            'periodLabel',
            'totalTransactions',
            'totalSubtotal',
            'totalDiscount',
            'totalTax',
            'totalRevenue',
            'paymentBreakdown',
            'productsSold',
            'storeName',
            'storeAddress',
            'storePhone',
            'currency'
        ))->render();

        $output = "\xEF\xBB\xBF".$html;

        return response($output, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control' => 'max-age=0, no-cache, no-store, must-revalidate',
            'Pragma' => 'public',
        ]);
    }
}
