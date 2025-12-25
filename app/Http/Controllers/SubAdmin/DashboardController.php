<?php

namespace App\Http\Controllers\SubAdmin;

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
        
        // Get accessible mandals based on level
        $accessibleMandalIds = $this->getAccessibleMandalIds($user);
        
        $stats = [
            'total_stores' => Store::where('district_id', $user->district_id)
                ->whereIn('mandal_id', $accessibleMandalIds)
                ->count(),
            'active_stores' => Store::where('district_id', $user->district_id)
                ->whereIn('mandal_id', $accessibleMandalIds)
                ->where('valid_till', '>', now())
                ->count(),
            'total_customers' => Customer::where('district_id', $user->district_id)
                ->whereIn('mandal_id', $accessibleMandalIds)
                ->count(),
            'total_sales' => Sale::whereHas('store', function ($query) use ($user, $accessibleMandalIds) {
                $query->where('district_id', $user->district_id)
                      ->whereIn('mandal_id', $accessibleMandalIds);
            })->sum('quantity'),
        ];

        $stores = Store::where('district_id', $user->district_id)
            ->whereIn('mandal_id', $accessibleMandalIds)
            ->with(['district', 'mandal'])
            ->get();

        return view('sub-admin.dashboard', compact('stats', 'stores', 'user'));
    }
    
    private function getAccessibleMandalIds($user)
    {
        if ($user->hasRole('sub_admin_level_1')) {
            // Level-1: All mandals of the district
            return \App\Models\Mandal::where('district_id', $user->district_id)
                ->where('is_active', true)
                ->pluck('id')
                ->toArray();
        } elseif ($user->hasRole('sub_admin_level_2')) {
            // Level-2: Only selected mandals
            return $user->mandal_ids ?? [];
        }
        
        return [];
    }
}
