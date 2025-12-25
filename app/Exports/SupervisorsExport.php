<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SupervisorsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return User::role('supervisor')->with(['district', 'mandal'])->orderBy('name')->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Phone Number',
            'Email',
            'District',
            'Mandal',
            'Status',
            'Created At',
            'Updated At'
        ];
    }

    /**
     * @param User $supervisor
     * @return array
     */
    public function map($supervisor): array
    {
        return [
            $supervisor->id,
            $supervisor->name,
            $supervisor->phone_number ?? 'N/A',
            $supervisor->email ?? 'N/A',
            $supervisor->district->name ?? 'N/A',
            $supervisor->mandal->name ?? 'N/A',
            $supervisor->is_active ? 'Active' : 'Inactive',
            $supervisor->created_at->format('Y-m-d H:i:s'),
            $supervisor->updated_at->format('Y-m-d H:i:s'),
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
            'B' => 25,  // Name
            'C' => 15,  // Phone Number
            'D' => 30,  // Email
            'E' => 20,  // District
            'F' => 20,  // Mandal
            'G' => 15,  // Status
            'H' => 20,  // Created At
            'I' => 20,  // Updated At
        ];
    }
}
