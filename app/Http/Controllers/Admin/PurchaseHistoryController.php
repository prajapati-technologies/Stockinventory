<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Customer;
use Illuminate\Http\Request;

class PurchaseHistoryController extends Controller
{
    public function index(Request $request)
    {
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
                    ->with(['customer.district', 'customer.mandal', 'store.district', 'store.mandal', 'user'])
                    ->orderBy('created_at', 'desc')
                    ->get();
            }
        }
        
        return view('admin.purchase-history.index', compact('sales', 'documentNumber'));
    }

    public function destroy(Sale $sale)
    {
        try {
            // Get customer before deleting sale
            $customer = $sale->customer;
            
            // Delete the sale
            $sale->delete();
            
            // Recalculate customer's stock_availed
            $customer->stock_availed = $customer->sales()->sum('quantity');
            $customer->save();
            
            return redirect()->route('admin.purchase-history.index', ['document_number' => $customer->document_number])
                ->with('success', 'Purchase record deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete purchase record: ' . $e->getMessage());
        }
    }
}
