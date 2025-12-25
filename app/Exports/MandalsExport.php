<?php

namespace App\Exports;

use App\Models\Mandal;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MandalsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Mandal::with('district')
            ->orderBy('district_id')
            ->orderBy('name')
            ->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Mandal Name',
            'District ID',
            'District Name',
            'Status',
            'Created At',
            'Updated At'
        ];
    }

    /**
     * @param Mandal $mandal
     * @return array
     */
    public function map($mandal): array
    {
        return [
            $mandal->id,
            $mandal->name,
            $mandal->district_id,
            $mandal->district->name ?? 'N/A',
            $mandal->is_active ? 'Active' : 'Inactive',
            $mandal->created_at->format('Y-m-d H:i:s'),
            $mandal->updated_at->format('Y-m-d H:i:s'),
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
            'B' => 25,  // Mandal Name
            'C' => 15,  // District ID
            'D' => 25,  // District Name
            'E' => 15,  // Status
            'F' => 20,  // Created At
            'G' => 20,  // Updated At
        ];
    }
}
