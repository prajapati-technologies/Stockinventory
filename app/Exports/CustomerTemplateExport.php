<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CustomerTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    /**
     * @return array
     */
    public function array(): array
    {
        return [
            ['T01030100321', 'BOYA AMMANAYYA', null, null, null, 1.06],
            ['T01030100322', 'BOYA SAANTAMMA', null, null, null, 0.375],
            ['T01030100323', 'NADIPIJANAYYA', null, null, null, 0.365],
            ['T01030100324', 'BOYA BHEEMAMMA', null, null, null, 0.375],
            ['T01030100325', 'CHINNA VENKATAYYA', null, null, null, 1.27],
            ['T01030100326', 'BAAL REDDI', null, null, null, 4.34],
        ];
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'document_number',
            'name',
            'phone',
            'district_id',
            'mandal_id',
            'total_land'
        ];
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text
            1 => ['font' => ['bold' => true]],
        ];
    }

    /**
     * @return array
     */
    public function columnWidths(): array
    {
        return [
            'A' => 25,  // document_number
            'B' => 30,  // name
            'C' => 20,  // phone
            'D' => 15,  // district_id
            'E' => 15,  // mandal_id
            'F' => 15,  // total_land
        ];
    }
}

