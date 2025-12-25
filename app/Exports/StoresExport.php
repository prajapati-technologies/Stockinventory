<?php

namespace App\Exports;

use App\Models\Store;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StoresExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Store::with(['district', 'mandal', 'user'])->orderBy('name')->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Store Name',
            'Phone Number',
            'Address',
            'District',
            'Mandal',
            'Store Manager',
            'Valid Till',
            'Status',
            'Created At',
            'Updated At'
        ];
    }

    /**
     * @param Store $store
     * @return array
     */
    public function map($store): array
    {
        return [
            $store->id,
            $store->name,
            $store->phone_number ?? 'N/A',
            $store->address ?? 'N/A',
            $store->district->name ?? 'N/A',
            $store->mandal->name ?? 'N/A',
            $store->user->name ?? 'N/A',
            $store->valid_till ? $store->valid_till->format('Y-m-d') : 'N/A',
            $store->is_active ? 'Active' : 'Inactive',
            $store->created_at->format('Y-m-d H:i:s'),
            $store->updated_at->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1    => ['font' => ['bold' => true]],
        ];
    }

    /**
     * @return array
     */
    public function columnWidths(): array
    {
        return [
            'A' => 10,  // ID
            'B' => 25,  // Store Name
            'C' => 15,  // Phone Number
            'D' => 30,  // Address
            'E' => 20,  // District
            'F' => 20,  // Mandal
            'G' => 20,  // Store Manager
            'H' => 15,  // Valid Till
            'I' => 15,  // Status
            'J' => 20,  // Created At
            'K' => 20,  // Updated At
        ];
    }
}
