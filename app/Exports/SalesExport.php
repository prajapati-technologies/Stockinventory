<?php

namespace App\Exports;

use App\Models\Sale;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SalesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $sales;

    public function __construct($sales)
    {
        $this->sales = $sales;
    }

    public function collection()
    {
        return $this->sales;
    }

    public function headings(): array
    {
        return [
            'Document Number',
            'Customer Name',
            'Phone Number',
            'Land (Acres)',
            'Store Name',
            'Quantity Sold',
            'Balance After Sale',
            'Sale Date',
            'Sale Time',
            'District',
            'Mandal'
        ];
    }

    public function map($sale): array
    {
        return [
            $sale->customer->document_number,
            $sale->customer->name ?? 'N/A',
            $sale->customer->phone ?? 'N/A',
            $sale->customer->total_land,
            $sale->store->name,
            $sale->quantity,
            $sale->balance_after,
            $sale->created_at->format('d-m-Y'),
            $sale->created_at->format('h:i A'),
            $sale->customer->district->name,
            $sale->customer->mandal->name,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1    => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20, // Document Number
            'B' => 25, // Customer Name
            'C' => 15, // Phone Number
            'D' => 12, // Land
            'E' => 25, // Store Name
            'F' => 15, // Quantity
            'G' => 18, // Balance After
            'H' => 12, // Sale Date
            'I' => 12, // Sale Time
            'J' => 20, // District
            'K' => 20, // Mandal
        ];
    }
}
