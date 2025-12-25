<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;
use App\Imports\CustomersImport;
use App\Exports\CustomerTemplateExport;

class CustomerController extends Controller
{
    public function search(Request $request)
    {
        $customer = null;
        $documentNumber = $request->get('document_number');

        if ($documentNumber) {
            // Search customer by document number
            $customer = Customer::where('document_number', $documentNumber)
                ->with([
                    'district', 
                    'mandal', 
                    'sales.store.district', 
                    'sales.store.mandal', 
                    'createdBy',
                    'additionalBags' => function($q) {
                        $q->with('addedBy')->orderBy('created_at', 'desc');
                    }
                ])
                ->first();
        }

        return view('admin.customers.search', compact('customer', 'documentNumber'));
    }

    public function destroy(Customer $customer)
    {
        try {
            // Delete document photo if exists
            if ($customer->document_photo) {
                Storage::disk('public')->delete($customer->document_photo);
            }

            // Delete customer (this will cascade delete related sales if foreign key constraints are set)
            $customer->delete();

            return redirect()->route('admin.customers.search')
                ->with('success', 'Customer deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete customer: ' . $e->getMessage());
        }
    }

    public function upload(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls,csv|max:5120',
        ]);

        try {
            $import = new CustomersImport();
            Excel::import($import, $request->file('excel_file'));

            $createdCount = $import->getCreatedCount();
            $duplicates = $import->getDuplicates();

            $message = $createdCount . ' customer' . ($createdCount === 1 ? '' : 's') . ' imported successfully.';

            if (!empty($duplicates)) {
                $message .= '<br>Skipped ' . count($duplicates) . ' duplicate document number' . (count($duplicates) === 1 ? '' : 's') . ': ' . implode(', ', $duplicates);
            }

            return redirect()->route('admin.customers.search')
                ->with('success', $message);
        } catch (ValidationException $e) {
            $errors = collect($e->failures())->map(function ($failure) {
                return 'Row ' . $failure->row() . ': ' . implode(', ', $failure->errors());
            })->implode('<br>');

            return redirect()->route('admin.customers.search')
                ->with('error', 'Failed to import customers:<br>' . $errors);
        } catch (\Throwable $e) {
            return redirect()->route('admin.customers.search')
                ->with('error', 'Failed to import customers: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        $fileName = 'customer_import_template_' . now()->format('Y-m-d_H-i-s') . '.xlsx';
        return Excel::download(new CustomerTemplateExport, $fileName);
    }
}

