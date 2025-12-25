<?php

namespace App\Imports;

use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class CustomersImport implements ToModel, WithHeadingRow, WithValidation
{
    protected array $createdDocuments = [];

    protected array $duplicates = [];

    protected int $createdCount = 0;

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $documentNumber = isset($row['document_number']) ? trim((string) $row['document_number']) : '';

        if ($documentNumber === '') {
            return null;
        }

        if ($this->isDuplicate($documentNumber)) {
            if (!in_array($documentNumber, $this->duplicates, true)) {
                $this->duplicates[] = $documentNumber;
            }

            return null;
        }

        $totalStockAllotted = Customer::calculateStockAllocation($row['total_land'], $row['district_id'] ?? null);

        $customer = Customer::create([
            'document_number' => $documentNumber,
            'name' => $row['name'] ?? null,
            'phone' => $row['phone'] ?? null,
            'district_id' => $row['district_id'],
            'mandal_id' => $row['mandal_id'],
            'total_land' => $row['total_land'],
            'total_stock_allotted' => $totalStockAllotted,
            'stock_availed' => 0,
            'created_by' => Auth::id(),
        ]);

        $this->createdDocuments[] = $documentNumber;
        $this->createdCount++;

        return $customer;
    }

    public function rules(): array
    {
        return [
            'document_number' => 'required|string',
            'district_id' => 'required|exists:districts,id',
            'mandal_id' => 'required|exists:mandals,id',
            'total_land' => 'required|numeric|min:0',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'document_number.required' => 'Document number is required.',
            'document_number.unique' => 'The document number :input has already been taken.',
            'district_id.required' => 'District ID is required.',
            'district_id.exists' => 'District ID does not exist.',
            'mandal_id.required' => 'Mandal ID is required.',
            'mandal_id.exists' => 'Mandal ID does not exist.',
            'total_land.required' => 'Total land is required.',
            'total_land.numeric' => 'Total land must be a number.',
            'total_land.min' => 'Total land must be at least 0.',
        ];
    }

    public function getDuplicates(): array
    {
        return $this->duplicates;
    }

    public function getCreatedCount(): int
    {
        return $this->createdCount;
    }

    protected function isDuplicate(string $documentNumber): bool
    {
        if (in_array($documentNumber, $this->createdDocuments, true)) {
            return true;
        }

        return Customer::where('document_number', $documentNumber)->exists();
    }
}
