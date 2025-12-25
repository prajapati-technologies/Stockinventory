<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\User;
use App\Models\Customer;
use App\Models\Sale;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_stores' => Store::count(),
            'active_stores' => Store::where('is_active', true)->whereDate('valid_till', '>=', now())->count(),
            'total_supervisors' => User::role('supervisor')->count(),
            'total_customers' => Customer::count(),
            'total_sales' => Sale::sum('quantity'),
        ];

        $recentStores = Store::with(['district', 'mandal'])
            ->latest()
            ->take(5)
            ->get();

        $expiringSoon = Store::where('valid_till', '<=', now()->addDays(30))
            ->where('valid_till', '>=', now())
            ->with(['district', 'mandal'])
            ->get();

        return view('admin.dashboard', compact('stats', 'recentStores', 'expiringSoon'));
    }
}
