<?php

namespace App\Http\Controllers\StoreManager;

use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DistrictsExport;
use App\Exports\MandalsExport;

class DistrictMandalController extends Controller
{
    public function exportDistricts()
    {
        return Excel::download(new DistrictsExport, 'districts_' . date('Y-m-d_H-i-s') . '.xlsx');
    }

    public function exportMandals()
    {
        return Excel::download(new MandalsExport, 'mandals_' . date('Y-m-d_H-i-s') . '.xlsx');
    }
}
