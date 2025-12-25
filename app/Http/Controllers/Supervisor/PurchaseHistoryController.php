<?php

namespace App\Http\Controllers\Supervisor;

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
        
        $sales = collect();
        $documentNumber = $request->get('document_number');
        
        if ($documentNumber) {
            $customer = Customer::where('document_number', $documentNumber)
                // ->where('district_id', $user->district_id)
                // ->where('mandal_id', $user->mandal_id)
                ->first();
            
            if ($customer) {
                // Load customer with additional bags and who added them
                $customer->load([
                    'additionalBags' => function($q) {
                        $q->with('addedBy')->orderBy('created_at', 'desc');
                    }
                ]);
                
                $sales = Sale::where('customer_id', $customer->id)
                    // ->whereHas('store', function ($query) use ($user) {
                    //     $query->where('district_id', $user->district_id)
                    //           ->where('mandal_id', $user->mandal_id);
                    // })
                    ->with(['customer.district', 'customer.mandal', 'store.district', 'store.mandal', 'user'])
                    ->orderBy('created_at', 'desc')
                    ->get();
            }
        }
        
        return view('supervisor.purchase-history.index', compact('sales', 'documentNumber'));
    }
}

