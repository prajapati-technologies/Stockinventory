<?php

namespace App\Http\Controllers\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\Sale;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $stats = [
            'total_stores' => Store::where('district_id', $user->district_id)
                ->where('mandal_id', $user->mandal_id)
                ->count(),
            'active_stores' => Store::where('district_id', $user->district_id)
                ->where('mandal_id', $user->mandal_id)
                ->where('valid_till', '>', now())
                ->count(),
            'total_customers' => Customer::where('district_id', $user->district_id)
                ->where('mandal_id', $user->mandal_id)
                ->count(),
            'total_sales' => Sale::whereHas('store', function ($query) use ($user) {
                $query->where('district_id', $user->district_id)
                      ->where('mandal_id', $user->mandal_id);
            })->sum('quantity'),
        ];

        $stores = Store::where('district_id', $user->district_id)
            ->where('mandal_id', $user->mandal_id)
            ->with(['district', 'mandal'])
            ->get();

        return view('supervisor.dashboard', compact('stats', 'stores', 'user'));
    }
}
