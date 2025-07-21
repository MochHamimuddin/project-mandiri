<?php

namespace App\Http\Controllers;

use App\Exports\BisnisReportExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Mitra;
use Carbon\Carbon;

class ReportController extends Controller
{
    // Menampilkan form filter
    public function index()
    {
        $mitras = Mitra::all();
        return view('reports.index', compact('mitras'));
    }

    // Proses export Excel
    public function exportBisnis()
    {
        $mitraId = request('mitra');
        $startDate = request('start_date') ?? Carbon::now()->startOfMonth();
        $endDate = request('end_date') ?? Carbon::now()->endOfMonth();

        $mitra = Mitra::findOrFail($mitraId);

        return Excel::download(
            new BisnisReportExport($mitraId, $startDate, $endDate),
            "Laporan_Bisnis_{$mitra->nama_perusahaan}_" . now()->format('Y_m_d') . ".xlsx"
        );
    }
}
