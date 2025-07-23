<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UserResultsExport;
use App\Exports\CalonKandidatExport;
use Barryvdh\DomPDF\Facade\Pdf;


class HomeController extends Controller
{
    public function index()
    {
        $filePath = public_path('txt/pengumuman.txt');
        $announcement = file_get_contents($filePath);

        return view('announcement', compact('announcement'));
    }

    public function exportExcel()
    {
        $export = new UserResultsExport();
        $export->setAddPercentage(true); // Set a flag to add "%"
        return Excel::download($export, 'Hasil_Psikotes.xlsx');
    }

    public function exportExcelCalonKandidat()
    {
        $export = new CalonKandidatExport();
        $export->setAddPercentage(true); // Set a flag to add "%"
        return Excel::download($export, 'Calon_Kandidat_Psikotes.xlsx');
    }

    public function exportPDF()
    {
        $export = new UserResultsExport();
        $export->setAddPercentage(true); // Set a flag to add "%"
    
        $pdf = PDF::loadView('exports.user_results_pdf', compact('export'));
        $pdfFileName = 'Hasil_Psikotes.pdf';
    
        return $pdf->stream($pdfFileName);
    }
}
