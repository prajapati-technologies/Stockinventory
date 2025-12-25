<?php

namespace App\Http\Controllers\StoreManager;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $store = $user->store;

        if (!$store) {
            return redirect()->route('login')->with('error', 'No store assigned to your account.');
        }

        // Check if store is valid
        if (!$store->isValid()) {
            return view('store.expired', compact('store'));
        }

        $stats = [
            'total_sales_today' => Sale::where('store_id', $store->id)
                ->whereDate('created_at', today())
                ->sum('quantity'),
            'total_sales_month' => Sale::where('store_id', $store->id)
                ->whereMonth('created_at', now()->month)
                ->sum('quantity'),
            'total_sales' => Sale::where('store_id', $store->id)
                ->sum('quantity'),
            'total_customers' => Sale::where('store_id', $store->id)
                ->distinct('customer_id')
                ->count('customer_id'),
        ];

        $recentSales = Sale::where('store_id', $store->id)
            ->with('customer')
            ->latest()
            ->take(10)
            ->get();

        return view('store.dashboard', compact('store', 'stats', 'recentSales'));
    }
}
