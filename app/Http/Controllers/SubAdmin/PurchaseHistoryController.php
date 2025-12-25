<?php

namespace App\Http\Controllers\SubAdmin;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PurchaseHistoryController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Get accessible mandals based on level
        $accessibleMandalIds = $this->getAccessibleMandalIds($user);
        
        $sales = collect();
        $documentNumber = $request->get('document_number');
        
        if ($documentNumber) {
            $customer = Customer::where('document_number', $documentNumber)
                // ->where('district_id', $user->district_id)
                // ->whereIn('mandal_id', $accessibleMandalIds)
                ->first();
            
            if ($customer) {
                // Load customer with additional bags and who added them
                $customer->load([
                    'additionalBags' => function($q) {
                        $q->with('addedBy')->orderBy('created_at', 'desc');
                    }
                ]);
                
                $sales = Sale::where('customer_id', $customer->id)
                    // ->whereHas('store', function ($query) use ($user, $accessibleMandalIds) {
                    //     $query->where('district_id', $user->district_id)
                    //           ->whereIn('mandal_id', $accessibleMandalIds);
                    // })
                    ->with(['customer.district', 'customer.mandal', 'store.district', 'store.mandal', 'user'])
                    ->orderBy('created_at', 'desc')
                    ->get();
            }
        }
        
        return view('sub-admin.purchase-history.index', compact('sales', 'documentNumber'));
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
