<?php

namespace App\Http\Controllers\StoreManager;

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
        $store = $user->store;
        
        if (!$store) {
            return redirect()->route('store.dashboard')
                ->with('error', 'Store not found for your account.');
        }
        
        $sales = collect();
        $documentNumber = $request->get('document_number');
        
        if ($documentNumber) {
            $customer = Customer::where('document_number', $documentNumber)->first();
            
            if ($customer) {
                // Load customer with additional bags and who added them
                $customer->load([
                    'additionalBags' => function($q) {
                        $q->with('addedBy')->orderBy('created_at', 'desc');
                    }
                ]);
                
                $sales = Sale::where('customer_id', $customer->id)
                    // ->where('store_id', $store->id)
                    ->with(['customer.district', 'customer.mandal', 'store.district', 'store.mandal', 'user'])
                    ->orderBy('created_at', 'desc')
                    ->get();
            }
        }
        
        return view('store.purchase-history.index', compact('sales', 'documentNumber', 'store'));
    }
}

