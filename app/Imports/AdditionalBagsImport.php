<?php

namespace App\Imports;

use App\Models\Customer;
use App\Models\CustomerAdditionalBag;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Collection;

class AdditionalBagsImport implements ToCollection, WithHeadingRow, WithValidation
{
    protected array $errors = [];
    protected int $successCount = 0;
    protected int $errorCount = 0;

    /**
     * @param Collection $rows
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Handle different possible column names
            $documentNumber = isset($row['document_number']) ? trim((string) $row['document_number']) : 
                             (isset($row['document number']) ? trim((string) $row['document number']) : '');
            $additionalBags = isset($row['add_bags']) ? (int) $row['add_bags'] : 
                             (isset($row['add bags']) ? (int) $row['add bags'] : 0);
            $remarks = isset($row['remarks']) ? trim((string) $row['remarks']) : '';

            if ($documentNumber === '') {
                $this->errorCount++;
                continue;
            }

            if ($additionalBags <= 0) {
                $this->errorCount++;
                $this->errors[] = "Document {$documentNumber}: Additional bags must be greater than 0";
                continue;
            }

            $customer = Customer::where('document_number', $documentNumber)->first();

            if (!$customer) {
                $this->errorCount++;
                $this->errors[] = "Document {$documentNumber}: Customer not found";
                continue;
            }

            try {
                DB::beginTransaction();

                // Create additional bag record
                $additionalBag = new CustomerAdditionalBag();
                $additionalBag->customer_id = $customer->id;
                $additionalBag->additional_bags = $additionalBags;
                $additionalBag->remarks = $remarks;
                $additionalBag->added_by = Auth::id();
                $additionalBag->save();

                // Update customer's total stock allotted
                $customer->increment('total_stock_allotted', $additionalBags);

                DB::commit();
                $this->successCount++;
            } catch (\Exception $e) {
                DB::rollBack();
                $this->errorCount++;
                $this->errors[] = "Document {$documentNumber}: " . $e->getMessage();
            }
        }
    }

    public function rules(): array
    {
        return [
            'document_number' => 'required|string',
            'add_bags' => 'required|integer|min:1',
            'remarks' => 'nullable|string|max:500',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'document_number.required' => 'Document number is required.',
            'add_bags.required' => 'Add bags is required.',
            'add_bags.integer' => 'Add bags must be a number.',
            'add_bags.min' => 'Add bags must be at least 1.',
        ];
    }


    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getSuccessCount(): int
    {
        return $this->successCount;
    }

    public function getErrorCount(): int
    {
        return $this->errorCount;
    }
}

