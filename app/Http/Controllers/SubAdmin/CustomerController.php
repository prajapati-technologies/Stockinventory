<?php

namespace App\Http\Controllers\SubAdmin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
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

        return view('sub-admin.customers.search', compact('customer', 'documentNumber'));
    }

    public function create()
    {
        return view('sub-admin.customers.upload');
    }

    public function store(Request $request)
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

            return redirect()->route('sub-admin.customers.upload-form')
                ->with('success', $message);
        } catch (ValidationException $e) {
            $errors = collect($e->failures())->map(function ($failure) {
                return 'Row ' . $failure->row() . ': ' . implode(', ', $failure->errors());
            })->implode('<br>');

            return redirect()->route('sub-admin.customers.upload-form')
                ->with('error', 'Failed to import customers:<br>' . $errors);
        } catch (\Throwable $e) {
            return redirect()->route('sub-admin.customers.upload-form')
                ->with('error', 'Failed to import customers: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        $fileName = 'customer_import_template_' . now()->format('Y-m-d_H-i-s') . '.xlsx';
        return Excel::download(new CustomerTemplateExport, $fileName);
    }
}

