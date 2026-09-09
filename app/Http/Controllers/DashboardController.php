<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $today = today();

        $todayRevenue = Transaction::whereDate('created_at', $today)
            ->where('status', 'completed')
            ->sum('total');

        $todayTransactions = Transaction::whereDate('created_at', $today)
            ->where('status', 'completed')
            ->count();

        $monthRevenue = Transaction::whereMonth('created_at', $today->month)
            ->whereYear('created_at', $today->year)
            ->where('status', 'completed')
            ->sum('total');

        $totalProducts = Product::where('is_active', true)->count();
        $lowStockProducts = Product::where('is_active', true)->where('stock', '<=', 5)->count();

        // Data grafik 7 hari terakhir
        $chartLabels = [];
        $chartData = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = today()->subDays($i);
            $chartLabels[] = $date->format('d M');
            $chartData[] = Transaction::whereDate('created_at', $date)
                ->where('status', 'completed')
                ->sum('total');
        }

        $recentTransactions = Transaction::with(['customer', 'user'])
            ->latest()
            ->limit(5)
            ->get();

        return view('dashboard.index', compact(
            'todayRevenue',
            'todayTransactions',
            'monthRevenue',
            'totalProducts',
            'lowStockProducts',
            'chartLabels',
            'chartData',
            'recentTransactions'
        ));
    }
}
