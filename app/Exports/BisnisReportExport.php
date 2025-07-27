<?php

namespace App\Exports;

use App\Models\User;
use App\Models\Mitra;
use App\Models\FatigueActivity;
use App\Models\KeselamatanAreaKerja;
use App\Models\InspeksiKendaraan;
use App\Models\FirePreventiveManagement;
use App\Models\ProgramLingkunganHidup;
use App\Models\DevelopmentManpower;
use App\Models\ProgramKerjaKesehatan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;

use Carbon\Carbon;

class BisnisReportExport implements WithMultipleSheets
{
    protected $mitraId;
    protected $startDate;
    protected $endDate;
    public function __construct($mitraId, $startDate, $endDate)
    {

        ini_set('memory_limit', '2048M');
        set_time_limit(0);

        $this->mitraId = $mitraId;
        $this->startDate = Carbon::parse($startDate);
        $this->endDate = Carbon::parse($endDate);
    }

    public function sheets(): array
    {

        $exporter = new BisnisDetailSheet($this->mitraId, $this->startDate, $this->endDate);
        $exporter->collection(); // Pastikan dijalankan dulu

        $export = new BisnisSummarySheet($this->mitraId, $this->startDate, $this->endDate);
        $export->collection();

        return [
            'DATABASE' => new BisnisSummarySheet($this->mitraId, $this->startDate, $this->endDate),
            'Input Proker' => new BisnisDetailSheet($this->mitraId, $this->startDate, $this->endDate),
            'Draft Proker K3KOLH Mitra Kerja' => new BisnisProkerSheet(
            $this->mitraId, 
            $this->startDate, 
            $this->endDate, 
            $exporter->getAverageMonthlyData(), // Pass the calculated averages       
            ),
            'PICA' => new BisnisFailedSheet(
                // $this->mitraId, 
                // $this->startDate, 
                // $this->endDate, 
                // $exporter->getValueProker(),
                    $export,
                    $this->mitraId,
                    $this->startDate,
                    $this->endDate
            // 'Proker' => new BisnisProkerSheet($this->mitraId, $this->startDate, $this->endDate, $this->averageData),
            // 'Faied' => new BisnisFailedSheet($this->mitraId, $this->startDate, $this->endDate) 
            )
        ];


    }
}

// mengambil nilai average monthly di Bisnis Detail Shett


class BisnisSummarySheet implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $mitraId;
    protected $startDate;
    protected $endDate;
    protected $userCount;
    protected $programFatigueData = [];
    protected $programKeselamatanData = [];
    protected $programInspeksiData = [];
    protected $programFirePreventifData = [];
    protected $programLingkunganData = [];
    protected $programDevelopmentData = [];
    protected $programKesehatanData = [];
    protected $subContData = [];

    public function __construct($mitraId, $startDate, $endDate)
    {
        $this->mitraId = $mitraId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->userCount = User::where('data_mitra_id', $mitraId)->count();
    }


    public function getProgramFatigueData()
    {
        return $this->programFatigueData;
    }

    public function getProgramKeselamatanData()
    {
        return $this->programKeselamatanData;
    }

    public function getProgramInspeksiData()
    {
        return $this->programInspeksiData;
    }

    public function getProgramFirePreventifData()
    {
        return $this->programFirePreventifData;
    }

    public function getProgramLingkunganData()
    {
        return $this->programLingkunganData;
    }

    public function getProgramDevelopmentData()
    {
        return $this->programDevelopmentData;
    }

    public function getProgramKesehatanData()
    {
        return $this->programKesehatanData;
    }

    public function getSubContData()
    {
        return $this->subContData;
    }

    public function collection()
    {
        $users = User::where('data_mitra_id', $this->mitraId)->pluck('id');
        $weeks = $this->calculateWeeks();
        $monthlyData = $this->getMonthlyData($users);
        $data = collect();

        foreach ($weeks as $week) {
            $data = $data->merge($this->getFatigueActivities($users, $week));
            $data = $data->merge($this->getKeselamatanActivities($users, $week));
            $data = $data->merge($this->getInspeksiKendaraan($users, $week));
            $data = $data->merge($this->getFirePreventive($users, $week));
            $data = $data->merge($this->getProgramLingkungan($users, $week));
            $data = $data->merge($this->getDevelopmentManpower($users, $week));
            $data = $data->merge($this->getProgramKesehatan($users, $week));
        }

        return $data->merge($monthlyData);
    }

    public function headings(): array
    {
        return [
            'ID',
            'PERIODE',
            'WEEK',
            'KODE PROGRAM',
            'NAMA PROGRAM',
            'PLAN',
            'ACT',
            'PENCAPAIAN',
            'SITE',
            'KET',
            'SUBKON',
            'SUBCONT CODE'
        ];
    }

    public function map($row): array
    {
        return [
            $row['id'] ?? '',
            $row['periode'] ?? '',
            $row['week'] ?? '',
            $row['kode_program'] ?? '',
            $row['nama_program'] ?? '',
            $row['plan'] ?? 0,
            $row['act'] ?? 0,
            $this->formatPencapaian($row['pencapaian'] ?? 0),
            $row['site'] ?? 'AGMR',
            $row['ket'] ?? 'SUBCONT',
            $row['subkon'] ?? $this->getMitraName(),
            $row['subcont_code'] ?? $this->getMitraCode()
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
            'A:L' => ['alignment' => ['vertical' => 'center']],
        ];
    }

    protected function formatPencapaian($pencapaian)
    {
        return is_numeric($pencapaian) ? $pencapaian.'%' : $pencapaian;
    }

    protected function calculateWeeks()
    {
        $weeks = [];
        $current = $this->startDate->copy();

        while ($current <= $this->endDate) {
            $weeks[] = [
                'week' => 'WEEK '.$current->weekOfMonth,
                'month_year' => $current->format('F Y'),
                'start' => $current->copy()->startOfWeek(),
                'end' => $current->copy()->endOfWeek()
            ];
            $current->addWeek();
        }

        return $weeks;
    }

    protected function getMonthlyData($users)
    {
        $monthYear = $this->startDate->format('F Y');
        $data = collect();

        // 1.5 Inspeksi SAGA
        $saga = FatigueActivity::whereIn('user_id', $users)
            ->where('activity_type', FatigueActivity::TYPE_SAGA)
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->count();

        $data->push([
            'periode' => $monthYear,
            'week' => 'Monthly',
            'kode_program' => '1.5',
            'nama_program' => 'Inspeksi/Perkunjungan Mess / Rumah Tinggal / Video Conference Karyawan Mitra yang teridentifikasi Fatigue berulang (SAGA)',
            'plan' => $this->userCount,
            'act' => $saga,
            'pencapaian' => $this->userCount > 0 ? round(($saga / $this->userCount) * 100) : 0,
        ]);

        // 3.3 Housekeeping Workshop
        $housekeeping = KeselamatanAreaKerja::whereIn('pengawas_id', $users)
            ->where('activity_type', KeselamatanAreaKerja::TYPE_HOUSEKEEPING)
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->where('is_approved', true)
            ->count();

        $data->push([
            'periode' => $monthYear,
            'week' => 'Monthly',
            'kode_program' => '3.3',
            'nama_program' => 'Penilaian Kondisi Fisik/Housekeeeping Workshop Mitra',
            'plan' => $this->userCount,
            'act' => $housekeeping,
            'pencapaian' => $this->userCount > 0 ? round(($housekeeping / $this->userCount) * 100) : 0,
        ]);

        // 4.2 Inspeksi APAR
        $apar = FirePreventiveManagement::whereIn('supervisor_id', $users)
            ->where('activity_type', FirePreventiveManagement::TYPE_INSPEKSI_APAR)
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->whereNotNull('foto_path')
            ->count();

        $data->push([
            'periode' => $monthYear,
            'week' => 'Monthly',
            'kode_program' => '4.2',
            'nama_program' => 'Inspeksi APAR Bulanan Unit dan Bangunan',
            'plan' => $this->userCount,
            'act' => $apar,
            'pencapaian' => $this->userCount > 0 ? round(($apar / $this->userCount) * 100) : 0,
        ]);

        // 5.x Training dan Pembinaan (Dengan kode pasti)
        $trainingMapping = [
            '5.1' => DevelopmentManpower::KATEGORI_AKTIVITAS[0], // SKKP/POP For GL Mitra
            '5.2' => DevelopmentManpower::KATEGORI_AKTIVITAS[1], // Training HRCP Mitra
            '5.3' => DevelopmentManpower::KATEGORI_AKTIVITAS[2], // Training Additional Plant
            '5.4' => DevelopmentManpower::KATEGORI_AKTIVITAS[3], // Review IBPR
            '5.5' => DevelopmentManpower::KATEGORI_AKTIVITAS[4], // Review SMKP
            '5.6' => DevelopmentManpower::KATEGORI_AKTIVITAS[5]  // Pembinaan Pelanggaran
        ];

        foreach ($trainingMapping as $kode => $nama) {
            $count = DevelopmentManpower::whereIn('pengawas_id', $users)
                ->where('kategori_aktivitas', $nama)
                ->whereBetween('tanggal_aktivitas', [$this->startDate, $this->endDate])
                ->count();

            $plan = ($kode === '5.6') ? $this->userCount * 4 : $this->userCount;
            $pencapaian = $plan > 0 ? round(($count / $plan) * 100) : 0;

            $data->push([
                'periode' => $monthYear,
                'week' => 'Monthly',
                'kode_program' => $kode,
                'nama_program' => $nama,
                'plan' => $plan,
                'act' => $count,
                'pencapaian' => $pencapaian,
            ]);
        }

        // 6.x Kesehatan Karyawan
        $kesehatanMapping = [
            '6.1' => ProgramKerjaKesehatan::MCU_TAHUNAN,
            '6.2' => ProgramKerjaKesehatan::PENYAKIT_KRONIS
        ];

        foreach ($kesehatanMapping as $kode => $jenis) {
            $count = ProgramKerjaKesehatan::whereIn('pengawas_id', $users)
                ->where('jenis_program', $jenis)
                ->whereBetween('tanggal_upload', [$this->startDate, $this->endDate])
                ->count();

            $data->push([
                'periode' => $monthYear,
                'week' => 'Monthly',
                'kode_program' => $kode,
                'nama_program' => $jenis === ProgramKerjaKesehatan::MCU_TAHUNAN
                    ? 'Kontrol & Monitor MCU Tahunan'
                    : 'Kontrol & Monitor Rutin Karyawan dengan Penyakit Kritis Kronis',
                'plan' => $this->userCount,
                'act' => $count,
                'pencapaian' => $this->userCount > 0 ? round(($count / $this->userCount) * 100) : 0,
            ]);
        }

        return $this->addMitraInfo($data);
    }

    //Rename sheet
    public function title(): string
    {
        return 'DATABASE';
    }

    protected function getFatigueActivities($users, $week)
    {
        $activities = FatigueActivity::whereIn('user_id', $users)
            ->whereBetween('created_at', [$week['start'], $week['end']])
            ->get()
            ->groupBy('activity_type');

        $data = [];
        $types = [
            FatigueActivity::TYPE_FTW => '1.1',
            FatigueActivity::TYPE_DFIT => '1.2',
            FatigueActivity::TYPE_FATIGUE_CHECK => '1.3',
            FatigueActivity::TYPE_WAKEUP_CALL => '1.4',
            FatigueActivity::TYPE_SAGA => '1.5',
            FatigueActivity::TYPE_SIDAK => '1.6'
        ];

        $descriptions = [
            FatigueActivity::TYPE_FTW => 'Fit to Work di Awal Shift',
            FatigueActivity::TYPE_DFIT => 'Evaluasi D Fit (Operator DT)',
            FatigueActivity::TYPE_FATIGUE_CHECK => 'Fatigue Check/Streaching Operational DT di Jam Kritis',
            FatigueActivity::TYPE_WAKEUP_CALL => 'Wake Up Call Operator A2B diluar Jam Kritis by Radio/Voice + Form',
            FatigueActivity::TYPE_SAGA => 'Inspeksi/Perkunjungan Mess / Rumah Tinggal / Video Conference Karyawan Mitra yang teridentifikasi Fatigue berulang (SAGA)',
            FatigueActivity::TYPE_SIDAK => 'Sidak Napping Driver/Operator'
        ];

        foreach ($types as $type => $kode) {
            $completed = $activities->has($type)
                ? $activities[$type]->whereNotNull('photo_path')->count()
                : 0;
            $plan = $this->userCount * 14; // 2 shift/hari x 7 hari
            $pencapaian = $plan > 0 ? round(($completed / $plan) * 100) : 0;

            $data[] = array_merge([
                'periode' => $week['month_year'],
                'week' => $week['week'],
                'kode_program' => $kode,
                'nama_program' => $descriptions[$type],
                'plan' => $plan,
                'act' => $completed,
                'pencapaian' => $pencapaian,
            ], $this->getMitraInfo());
        }
        $this->programFatigueData = $data;
        Log::debug('data fatigue: ', $data);
        return $data;
    }

    protected function getKeselamatanActivities($users, $week)
    {
        $activities = KeselamatanAreaKerja::whereIn('pengawas_id', $users)
            ->whereBetween('created_at', [$week['start'], $week['end']])
            ->get()
            ->groupBy('activity_type');

        $data = [];
        $types = [
            KeselamatanAreaKerja::TYPE_INSPEKSI_OBSERVASI => '2.1',
            KeselamatanAreaKerja::TYPE_GELAR_INSPEKSI => '3.2'
        ];

        $descriptions = [
            KeselamatanAreaKerja::TYPE_INSPEKSI_OBSERVASI => 'Inspeksi & Observasi Tematik (Mandiri & Gabungan)',
            KeselamatanAreaKerja::TYPE_GELAR_INSPEKSI => 'Gelar/Inspeksi Tools'
        ];

        foreach ($types as $type => $kode) {
            $completed = $activities->has($type)
                ? $activities[$type]->where('is_approved', true)->count()
                : 0;
            $plan = ($kode === '3.2') ? $this->userCount : $this->userCount * 7;
            $pencapaian = $plan > 0 ? round(($completed / $plan) * 100) : 0;

            $data[] = array_merge([
                'periode' => $week['month_year'],
                'week' => $week['week'],
                'kode_program' => $kode,
                'nama_program' => $descriptions[$type],
                'plan' => $plan,
                'act' => $completed,
                'pencapaian' => $pencapaian,
            ], $this->getMitraInfo());
        }
        $this->programKeselamatanData = $data;
        return $data;
    }

    protected function getInspeksiKendaraan($users, $week)
    {
        $activities = InspeksiKendaraan::whereIn('pengawas_id', $users)
            ->whereBetween('tanggal_inspeksi', [$week['start'], $week['end']])
            ->get()
            ->groupBy('jenis_inspeksi');

        $data = [];
        $types = [
            'komisioning' => '2.2',
            'evaluasi_kecepatan' => '2.3'
        ];

        $descriptions = [
            'komisioning' => 'Kelayakan kendaraan: Komisioning & re-komisioning unit',
            'evaluasi_kecepatan' => 'Evaluasi kecepatan unit wheel (non sarana)'
        ];

        foreach ($types as $type => $kode) {
            $completed = $activities->has($type) ? $activities[$type]->count() : 0;
            $plan = ($kode === '2.2') ? $this->userCount : 0;
            $pencapaian = $plan > 0 ? round(($completed / $plan) * 100) : 0;

            $data[] = array_merge([
                'periode' => $week['month_year'],
                'week' => $week['week'],
                'kode_program' => $kode,
                'nama_program' => $descriptions[$type],
                'plan' => $plan,
                'act' => $completed,
                'pencapaian' => $pencapaian,
            ], $this->getMitraInfo());
        }
        $this->programInspeksiData = $data;
        return $data;
    }

    protected function getFirePreventive($users, $week)
    {
        $activities = FirePreventiveManagement::whereIn('supervisor_id', $users)
            ->whereBetween('created_at', [$week['start'], $week['end']])
            ->get()
            ->groupBy('activity_type');

        $data = [];
        $types = [
            FirePreventiveManagement::TYPE_PENCUCIAN_UNIT => '4.1',
            FirePreventiveManagement::TYPE_INSPEKSI_APAR => '4.2'
        ];

        $descriptions = [
            FirePreventiveManagement::TYPE_PENCUCIAN_UNIT => 'Pencucian Unit terjadwal A2B & DT',
            FirePreventiveManagement::TYPE_INSPEKSI_APAR => 'Inspeksi APAR Bulanan Unit dan Bangunan'
        ];

        foreach ($types as $type => $kode) {
            $completed = $activities->has($type)
                ? $activities[$type]->whereNotNull('foto_path')->count()
                : 0;
            $plan = ($kode === '4.1') ? $this->userCount * 13 : $this->userCount;
            $pencapaian = $plan > 0 ? round(($completed / $plan) * 100) : 0;

            $data[] = array_merge([
                'periode' => $week['month_year'],
                'week' => $week['week'],
                'kode_program' => $kode,
                'nama_program' => $descriptions[$type],
                'plan' => $plan,
                'act' => $completed,
                'pencapaian' => $pencapaian,
            ], $this->getMitraInfo());
        }
        $this->programFirePreventifData=$data;
        return $data;
    }

    protected function getProgramLingkungan($users, $week)
    {
        $activities = ProgramLingkunganHidup::whereIn('pelaksana', $users)
            ->whereBetween('tanggal_kegiatan', [$week['start'], $week['end']])
            ->get()
            ->groupBy('jenis_kegiatan');

        $data = [];
        $types = [
            ProgramLingkunganHidup::TYPE_KRIDA_AREA => '7.1',
            ProgramLingkunganHidup::TYPE_PENGELOLAAN => '7.2'
        ];

        $descriptions = [
            ProgramLingkunganHidup::TYPE_KRIDA_AREA => 'Krida area office/workshop (penghijauan dan kerja bakti)',
            ProgramLingkunganHidup::TYPE_PENGELOLAAN => 'Pengelolaan lingkungan area Workshop terhadap ceceran dan tumpahan oli, fuel serta B3 saat melakukan kegiatan repair maintenance unit'
        ];

        foreach ($types as $type => $kode) {
            $completed = $activities->has($type)
                ? $activities[$type]->whereNotNull('upload_foto')->count()
                : 0;
            $plan = $this->userCount;
            $pencapaian = $plan > 0 ? round(($completed / $plan) * 100) : 0;

            $data[] = array_merge([
                'periode' => $week['month_year'],
                'week' => $week['week'],
                'kode_program' => $kode,
                'nama_program' => $descriptions[$type],
                'plan' => $plan,
                'act' => $completed,
                'pencapaian' => $pencapaian,
            ], $this->getMitraInfo());
        }
        $this->programLingkunganData=$data;
        return $data;
    }

    protected function getDevelopmentManpower($users, $week)
    {
        $activities = DevelopmentManpower::whereIn('pengawas_id', $users)
            ->whereBetween('tanggal_aktivitas', [$week['start'], $week['end']])
            ->get()
            ->groupBy('kategori_aktivitas');

        $data = [];
        $types = [
            DevelopmentManpower::KATEGORI_AKTIVITAS[0] => '5.1', // SKKP/POP For GL Mitra
            DevelopmentManpower::KATEGORI_AKTIVITAS[1] => '5.2', // Training HRCP Mitra
            DevelopmentManpower::KATEGORI_AKTIVITAS[2] => '5.3', // Training Additional Plant
            DevelopmentManpower::KATEGORI_AKTIVITAS[3] => '5.4', // Review IBPR
            DevelopmentManpower::KATEGORI_AKTIVITAS[4] => '5.5', // Review SMKP
            DevelopmentManpower::KATEGORI_AKTIVITAS[5] => '5.6'  // Pembinaan Pelanggaran
        ];

        foreach ($types as $nama => $kode) {
            $completed = $activities->has($nama)
                ? $activities[$nama]->whereNotNull('foto_aktivitas')->count()
                : 0;
            $plan = ($kode === '5.6') ? $this->userCount * 4 : $this->userCount;
            $pencapaian = $plan > 0 ? round(($completed / $plan) * 100) : 0;

            $data[] = array_merge([
                'periode' => $week['month_year'],
                'week' => $week['week'],
                'kode_program' => $kode,
                'nama_program' => $nama,
                'plan' => $plan,
                'act' => $completed,
                'pencapaian' => $pencapaian,
            ], $this->getMitraInfo());
        }
        $this->programDevelopmentData = $data;
        return $data;
    }

    protected function getProgramKesehatan($users, $week)
    {
        $activities = ProgramKerjaKesehatan::whereIn('pengawas_id', $users)
            ->whereBetween('tanggal_upload', [$week['start'], $week['end']])
            ->get()
            ->groupBy('jenis_program');

        $data = [];
        $types = [
            ProgramKerjaKesehatan::MCU_TAHUNAN => '6.1',
            ProgramKerjaKesehatan::PENYAKIT_KRONIS => '6.2'
        ];

        foreach ($types as $jenis => $kode) {
            $completed = $activities->has($jenis) ? $activities[$jenis]->count() : 0;
            $plan = $this->userCount;
            $pencapaian = $plan > 0 ? round(($completed / $plan) * 100) : 0;

            $data[] = array_merge([
                'periode' => $week['month_year'],
                'week' => $week['week'],
                'kode_program' => $kode,
                'nama_program' => $jenis === ProgramKerjaKesehatan::MCU_TAHUNAN
                    ? 'Kontrol & Monitor MCU Tahunan'
                    : 'Kontrol & Monitor Rutin Karyawan dengan Penyakit Kritis Kronis',
                'plan' => $plan,
                'act' => $completed,
                'pencapaian' => $pencapaian,
            ], $this->getMitraInfo());
        }
        $this->programKesehatanData = $data;
        return $data;
    }

    protected function getMitraName()
    {
        return Mitra::find($this->mitraId)->nama_perusahaan ?? 'N/A';
    }

    protected function getMitraCode()
    {
        return Mitra::find($this->mitraId)->kode ?? 'N/A';
    }

    protected function getMitraInfo()
    {
        $this->subContData = $this->getMitraName();
        return [
            'subkon' => $this->getMitraName(),
            'subcont_code' => $this->getMitraCode()
        ];
    }

    protected function addMitraInfo($data)
    {
        return $data->map(function ($item) {
            return array_merge($item, $this->getMitraInfo());
        });
    }
}

class BisnisDetailSheet implements FromCollection, WithHeadings, WithStyles, WithTitle
{
    protected $mitraId;
    protected $startDate;
    protected $endDate;
    protected $userCount;
    protected $averageMonthlyData = [];

    public function getAverageMonthlyData():array
    {
        return $this->averageMonthlyData;
    }

    public function __construct($mitraId, $startDate, $endDate)
    {
        $this->mitraId = $mitraId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->userCount = User::where('data_mitra_id', $mitraId)->count();
    }

    public function collection()
    {
        $users = User::where('data_mitra_id', $this->mitraId)->pluck('id');
        $weeks = $this->calculateWeeks();
        $monthlyData = $this->getMonthlyData($users);
        $weeklyData = $this->getWeeklyData($users, $weeks);

        $allPrograms = $this->getAllPrograms();
        $data = collect();

        foreach ($allPrograms as $program) {
            $row = [
                'kode_program' => $program['kode'],
                'nama_program' => $program['nama']
            ];

            // Weekly data
            foreach ($weeks as $index => $week) {
                $weekKey = 'Week '.($index+1);
                $weekData = $weeklyData[$program['kode']][$weekKey] ?? [
                    'plan' => 0,
                    'aktual' => 0,
                    'pencapaian' => 0
                ];

                $row[$weekKey.'_plan'] = $weekData['plan'];
                $row[$weekKey.'_aktual'] = $weekData['aktual'];
                $row[$weekKey.'_pencapaian'] = $weekData['pencapaian'].'%';
            }

            // Monthly data
            $monthly = $monthlyData[$program['kode']] ?? [
                'plan' => 0,
                'aktual' => 0,
                'pencapaian' => 0
            ];

            $row['monthly_plan'] = $monthly['plan'];
            $row['monthly_aktual'] = $monthly['aktual'];
            $row['monthly_pencapaian'] = $monthly['pencapaian'].'%';

            // Calculate averages
            $totalPencapaian = 0;
            $count = 0;
            foreach ($weeks as $index => $week) {
                $weekKey = 'Week '.($index+1);
                if (isset($weeklyData[$program['kode']][$weekKey])) {
                    $totalPencapaian += $weeklyData[$program['kode']][$weekKey]['pencapaian'];
                    $count++;
                }
            }

            $average = $count > 0 ? round($totalPencapaian / $count) : 0;
            $this->averageMonthlyData[$program['kode']] = $average;

            \Log::debug  (['ini data averageMonthly', $average]);

            $row['Pencapaian (%)'] = $monthly['pencapaian'].'%';
            $row['Average Monthly (%)'] = $average.'%';

            // Add placeholders
            for ($i = 1; $i <= 4; $i++) {
                $row['week'.$i.'_placeholder'] = '#UNKNOWN!';
            }



            $data->push($row);
        }
        logger()->info('Data sebelum return:', $data->toArray());
        return $data;
    }

    public function headings(): array
    {
        $headings = ['KODE PROGRAM', 'NAMA PROGRAM'];

        // Weekly headers
        for ($i = 1; $i <= 4; $i++) {
            array_push($headings,
                'Week '.$i.' Plan',
                'Week '.$i.' Aktual',
                'Week '.$i.' Pencapaian'
            );
        }

        // Monthly headers
        array_push($headings,
            'MONTHLY Plan',
            'MONTHLY Aktual',
            'MONTHLY Pencapaian',
            'Pencapaian (%)',
            'Average Monthly (%)'
        );

        // Placeholder headers
        for ($i = 1; $i <= 4; $i++) {
            array_push($headings, 'WEEK '.$i);
        }

        logger()->info('Data heading:', $headings);
        return $headings;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
            'A:Z' => ['alignment' => ['vertical' => 'center']],
        ];
    }

    //Rename sheet
    public function title(): string
    {
        return 'Input Proker';
    }

    protected function calculateWeeks()
    {
        $weeks = [];
        $current = $this->startDate->copy();

        while ($current <= $this->endDate) {
            $weeks[] = [
                'week' => 'Week '.$current->weekOfMonth,
                'start' => $current->copy()->startOfWeek(),
                'end' => $current->copy()->endOfWeek()
            ];
            $current->addWeek();
        }

        return array_slice($weeks, 0, 4);
    }

    protected function getAllPrograms()
    {
        return [
            ['kode' => '1.1', 'nama' => 'Fit to Work di Awal Shift'],
            ['kode' => '1.2', 'nama' => 'Evaluasi D Fit (Operator DT)'],
            ['kode' => '1.3', 'nama' => 'Fatigue Check/Streaching Operational DT di Jam Kritis (1x setiap shift sesuai regulasi site)'],
            ['kode' => '1.4', 'nama' => 'Wake Up Call Operator A2B diluar Jam Kritis by Radio/Voice + Form'],
            ['kode' => '1.5', 'nama' => 'Inspeksi/Perkunjungan Mess / Rumah Tinggal / Video Conference Karyawan Mitra yang teridentifikasi Fatigue berulang (SAGA)'],
            ['kode' => '1.6', 'nama' => 'Sidak Napping Driver/Operator'],
            ['kode' => '2.1', 'nama' => 'Inspeksi & Observasi Tematik (Mandiri & Gabungan). Focus item :'],
            ['kode' => '2.2', 'nama' => 'Kelayakan kendaraan : 1. Komisioning & re-komisioning unit, 2. Jadwal Maintenance & Service dan Pelaksanaanya'],
            ['kode' => '2.3', 'nama' => 'Evaluasi kecepatan unit wheel (non sarana)'],
            ['kode' => '3.1', 'nama' => 'Observasi/Pendampingan PJO'],
            ['kode' => '3.2', 'nama' => 'Gelar/Inspeksi Tools'],
            ['kode' => '3.3', 'nama' => 'Penilaian Kondisi Fisik/Housekeeeping Workshop Mitra'],
            ['kode' => '4.1', 'nama' => 'Pencucian Unit terjadwal A2B & DT'],
            ['kode' => '4.2', 'nama' => 'Inspeksi APAR Bulanan Unit dan Bangunan'],
            ['kode' => '5.1', 'nama' => 'SKKP/POP For GL Mitra'],
            ['kode' => '5.2', 'nama' => 'Training HRCP Mitra (Posisi PJO GL Produksi, GL Plant dan SHE Officer)'],
            ['kode' => '5.3', 'nama' => 'Training Additional Plant (GL Plant dan SHE Officer)'],
            ['kode' => '5.4', 'nama' => 'Review IBPR'],
            ['kode' => '5.5', 'nama' => 'Review SMKP For Mitra Kerja'],
            ['kode' => '5.6', 'nama' => 'Pembinaan Pelanggaran'],
            ['kode' => '6.1', 'nama' => 'Kontrol & Monitor MCU Tahunan'],
            ['kode' => '6.2', 'nama' => 'Kontrol & Monitor Rutin Karyawan dengan Penyakit Kritis Kronis'],
            ['kode' => '7.1', 'nama' => 'Krida area office/workshop (penghijauan dan kerja bakti)'],
            ['kode' => '7.2', 'nama' => 'Pengelolaan lingkungan area Workshop terhadap ceceran dan tumpahan oli, fuel serta B3 saat melakukan kegiatan repair maintenance unit']
        ];
    }

    protected function getWeeklyData($users, $weeks)
    {
        $weeklyData = [];

        foreach ($weeks as $index => $week) {
            $weekKey = 'Week '.($index+1);

            // Fatigue Activities
            $this->processFatigueActivities($users, $week, $weekKey, $weeklyData);

            // Keselamatan Activities
            $this->processKeselamatanActivities($users, $week, $weekKey, $weeklyData);

            // Inspeksi Kendaraan
            $this->processInspeksiKendaraan($users, $week, $weekKey, $weeklyData);

            // Fire Preventive
            $this->processFirePreventive($users, $week, $weekKey, $weeklyData);

            // Program Lingkungan
            $this->processProgramLingkungan($users, $week, $weekKey, $weeklyData);
        }

        return $weeklyData;
    }

    protected function processFatigueActivities($users, $week, $weekKey, &$weeklyData)
    {
        $types = [
            '1.1' => FatigueActivity::TYPE_FTW,
            '1.2' => FatigueActivity::TYPE_DFIT,
            '1.3' => FatigueActivity::TYPE_FATIGUE_CHECK,
            '1.4' => FatigueActivity::TYPE_WAKEUP_CALL,
            '1.5' =>  FatigueActivity::TYPE_SAGA,
            '1.6' => FatigueActivity::TYPE_SIDAK
        ];

        foreach ($types as $kode => $type) {
            $completed = FatigueActivity::whereIn('user_id', $users)
                ->where('activity_type', $type)
                ->whereBetween('created_at', [$week['start'], $week['end']])
                ->whereNotNull('photo_path')
                ->count();

            $plan = $this->userCount * 14; // 2 shift/hari x 7 hari
            $pencapaian = $plan > 0 ? round(($completed / $plan) * 100) : 0;

            $weeklyData[$kode][$weekKey] = [
                'plan' => $plan,
                'aktual' => $completed,
                'pencapaian' => $pencapaian
            ];
        }
    }

    protected function processKeselamatanActivities($users, $week, $weekKey, &$weeklyData)
    {
        $types = [
            '2.1' => KeselamatanAreaKerja::TYPE_INSPEKSI_OBSERVASI,
            '3.2' => KeselamatanAreaKerja::TYPE_GELAR_INSPEKSI
        ];

        foreach ($types as $kode => $type) {
            $completed = KeselamatanAreaKerja::whereIn('pengawas_id', $users)
                ->where('activity_type', $type)
                ->whereBetween('created_at', [$week['start'], $week['end']])
                ->where('is_approved', true)
                ->count();

            $plan = ($kode === '3.2') ? $this->userCount : $this->userCount * 7;
            $pencapaian = $plan > 0 ? round(($completed / $plan) * 100) : 0;

            $weeklyData[$kode][$weekKey] = [
                'plan' => $plan,
                'aktual' => $completed,
                'pencapaian' => $pencapaian
            ];
        }
    }

    protected function processInspeksiKendaraan($users, $week, $weekKey, &$weeklyData)
    {
        $types = [
            '2.2' => 'komisioning',
            '2.3' => 'evaluasi_kecepatan'
        ];

        foreach ($types as $kode => $type) {
            $completed = InspeksiKendaraan::whereIn('pengawas_id', $users)
                ->where('jenis_inspeksi', $type)
                ->whereBetween('tanggal_inspeksi', [$week['start'], $week['end']])
                ->count();

            $plan = ($kode === '2.2') ? $this->userCount : 0;
            $pencapaian = $plan > 0 ? round(($completed / $plan) * 100) : 0;

            $weeklyData[$kode][$weekKey] = [
                'plan' => $plan,
                'aktual' => $completed,
                'pencapaian' => $pencapaian
            ];
        }
    }

    protected function processFirePreventive($users, $week, $weekKey, &$weeklyData)
    {
        $types = [
            '4.1' => FirePreventiveManagement::TYPE_PENCUCIAN_UNIT,
            '4.2' => FirePreventiveManagement::TYPE_INSPEKSI_APAR
        ];

        foreach ($types as $kode => $type) {
            $completed = FirePreventiveManagement::whereIn('supervisor_id', $users)
                ->where('activity_type', $type)
                ->whereBetween('created_at', [$week['start'], $week['end']])
                ->whereNotNull('foto_path')
                ->count();

            $plan = ($kode === '4.1') ? $this->userCount * 13 : $this->userCount;
            $pencapaian = $plan > 0 ? round(($completed / $plan) * 100) : 0;

            $weeklyData[$kode][$weekKey] = [
                'plan' => $plan,
                'aktual' => $completed,
                'pencapaian' => $pencapaian
            ];
        }
    }

    protected function processProgramLingkungan($users, $week, $weekKey, &$weeklyData)
    {
        $types = [
            '7.1' => ProgramLingkunganHidup::TYPE_KRIDA_AREA,
            '7.2' => ProgramLingkunganHidup::TYPE_PENGELOLAAN
        ];

        foreach ($types as $kode => $type) {
            $completed = ProgramLingkunganHidup::whereIn('pelaksana', $users)
                ->where('jenis_kegiatan', $type)
                ->whereBetween('tanggal_kegiatan', [$week['start'], $week['end']])
                ->whereNotNull('upload_foto')
                ->count();

            $plan = $this->userCount;
            $pencapaian = $plan > 0 ? round(($completed / $plan) * 100) : 0;

            $weeklyData[$kode][$weekKey] = [
                'plan' => $plan,
                'aktual' => $completed,
                'pencapaian' => $pencapaian
            ];
        }
    }

    protected function getMonthlyData($users)
    {
        $monthlyData = [];

        // 1.5 Inspeksi SAGA
        $saga = FatigueActivity::whereIn('user_id', $users)
            ->where('activity_type', FatigueActivity::TYPE_SAGA)
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->count();

        $monthlyData['1.5'] = [
            'plan' => $this->userCount,
            'aktual' => $saga,
            'pencapaian' => $this->userCount > 0 ? round(($saga / $this->userCount) * 100) : 0
        ];

        // 3.3 Housekeeping Workshop
        $housekeeping = KeselamatanAreaKerja::whereIn('pengawas_id', $users)
            ->where('activity_type', KeselamatanAreaKerja::TYPE_HOUSEKEEPING)
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->where('is_approved', true)
            ->count();

        $monthlyData['3.3'] = [
            'plan' => $this->userCount,
            'aktual' => $housekeeping,
            'pencapaian' => $this->userCount > 0 ? round(($housekeeping / $this->userCount) * 100) : 0
        ];

        // 4.2 Inspeksi APAR
        $apar = FirePreventiveManagement::whereIn('supervisor_id', $users)
            ->where('activity_type', FirePreventiveManagement::TYPE_INSPEKSI_APAR)
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->whereNotNull('foto_path')
            ->count();

        $monthlyData['4.2'] = [
            'plan' => $this->userCount,
            'aktual' => $apar,
            'pencapaian' => $this->userCount > 0 ? round(($apar / $this->userCount) * 100) : 0
        ];

        // 5.x Training dan Pembinaan
        $trainingMapping = [
            '5.1' => DevelopmentManpower::KATEGORI_AKTIVITAS[0],
            '5.2' => DevelopmentManpower::KATEGORI_AKTIVITAS[1],
            '5.3' => DevelopmentManpower::KATEGORI_AKTIVITAS[2],
            '5.4' => DevelopmentManpower::KATEGORI_AKTIVITAS[3],
            '5.5' => DevelopmentManpower::KATEGORI_AKTIVITAS[4],
            '5.6' => DevelopmentManpower::KATEGORI_AKTIVITAS[5]
        ];

        foreach ($trainingMapping as $kode => $nama) {
            $count = DevelopmentManpower::whereIn('pengawas_id', $users)
                ->where('kategori_aktivitas', $nama)
                ->whereBetween('tanggal_aktivitas', [$this->startDate, $this->endDate])
                ->count();

            $plan = ($kode === '5.6') ? $this->userCount * 4 : $this->userCount;
            $pencapaian = $plan > 0 ? round(($count / $plan) * 100) : 0;

            $monthlyData[$kode] = [
                'plan' => $plan,
                'aktual' => $count,
                'pencapaian' => $pencapaian
            ];
        }

        // 6.x Kesehatan Karyawan
        $kesehatanMapping = [
            '6.1' => ProgramKerjaKesehatan::MCU_TAHUNAN,
            '6.2' => ProgramKerjaKesehatan::PENYAKIT_KRONIS
        ];

        foreach ($kesehatanMapping as $kode => $jenis) {
            $count = ProgramKerjaKesehatan::whereIn('pengawas_id', $users)
                ->where('jenis_program', $jenis)
                ->whereBetween('tanggal_upload', [$this->startDate, $this->endDate])
                ->count();

            $monthlyData[$kode] = [
                'plan' => $this->userCount,
                'aktual' => $count,
                'pencapaian' => $this->userCount > 0 ? round(($count / $this->userCount) * 100) : 0
            ];
        }

        Log::debug(['ini data dari monthly', $monthlyData]);

        return $monthlyData;
    }
}

// class BisnisProkerSheet implements FromCollection, WithHeadings, WithStyles, WithEvents
// {
//     protected $mitraId;
//     protected $startDate;
//     protected $endDate;
//     protected $userCount;
//     protected $averageMonthlyData;

//     public function __construct($mitraId, $startDate, $endDate, array $averageMonthlyData)
//     {
//         $this->mitraId = $mitraId;
//         $this->startDate = $startDate;
//         $this->endDate = $endDate;
//         $this->mitraName = Mitra::find($this->mitraId)->nama_perusahaan ?? 'N/A';
//         $this->location = Mitra::find($this->mitraId)->nama_perusahaan ?? 'N/A';
//         $this->period =  $this->generatePeriodString($startDate, $endDate);
//         $this->userCount = User::where('data_mitra_id', $mitraId)->count();
//         $this->averageMonthlyData = $averageMonthlyData;
//     }

//     // generated periode
//     protected function generatePeriodString($startDate, $endDate)
//     {
//         $start = Carbon::parse($startDate);
//         $end = Carbon::parse($endDate);
        
//         if ($start->format('Y-m') === $end->format('Y-m')) {
//             return $start->translatedFormat('F Y');
//         }
        
//         if ($start->format('Y') === $end->format('Y')) {
//             return $start->translatedFormat('F') . ' - ' . $end->translatedFormat('F Y');
//         }
        
//         return $start->translatedFormat('F Y') . ' - ' . $end->translatedFormat('F Y');
//     }
//     // public function registerEvents(): array
//     // {
//     //     return [
//     //         AfterSheet::class => function(AfterSheet $event) {
//     //             $sheet = $event->sheet->getDelegate();
//     //             // 1. Tambahkan header informasi di row 1-3
//     //             $sheet->mergeCells('A1:H3');
//     //             $sheet->setCellValue('A1', 
//     //                 "Program Kerja K3KOLH Mitra Kerja SM KPP\n" .
//     //                 "Lokasi : " . $this->location . "\n" .
//     //                 "Mitra Kerja : " . $this->mitraName . "\n" .
//     //                 "Periode : " . $this->period . "\n" .
//     //                 "Pencapaian Akhir : " . $this->calculateTotalAchievement() . "%"
//     //             );

//     //             // 2. Style header
//     //             $sheet->getStyle('A1:H3')->applyFromArray([
//     //                 'font' => ['bold' => true, 'size' => 12],
//     //                 'alignment' => [
//     //                     'horizontal' => Alignment::HORIZONTAL_CENTER,
//     //                     'vertical' => Alignment::VERTICAL_CENTER,
//     //                     'wrapText' => true // Penting untuk multi-line
//     //                 ],
//     //                 'borders' => ['bottom' => ['borderStyle' => Border::BORDER_THIN]]
//     //             ]);

//     //             // 3. Geser data ke bawah
//     //             $sheet->insertNewRowBefore(4, 3); // Sisipkan 3 row kosong

//     //             // 4. Pindahkan data ke bawah header 
//     //             $dataStartRow = 4; // Mulai dari row 4
//     //             $sheet->fromArray(
//     //                 $this->collection()->toArray(), 
//     //                 null, 
//     //                 'A' . $dataStartRow,
//     //                 true
//     //             );
//     //         }
//     //     ];
//     // }

//     public function registerEvents(): array
//     {
//         return [
//             BeforeSheet::class => function(BeforeSheet $event) {
//                 // Tambahkan header sebelum data dimasukkan
//                 $event->sheet->mergeCells('A1:H3');
//                 $event->sheet->setCellValue('A1', 
//                     "Program Kerja K3KOLH Mitra Kerja SM KPP\n" .
//                     "Lokasi : " . $this->location . "\n" .
//                     "Mitra Kerja : " . $this->mitraName . "\n" .
//                     "Periode : " . $this->period . "\n" .
//                     "Pencapaian Akhir : " . $this->calculateTotalAchievement() . "%"
//                 );
    
//                 // Style header
//                 $event->sheet->getStyle('A1:H3')->applyFromArray([
//                     'font' => ['bold' => true, 'size' => 12],
//                     'alignment' => [
//                         'horizontal' => Alignment::HORIZONTAL_CENTER,
//                         'vertical' => Alignment::VERTICAL_CENTER,
//                         'wrapText' => true
//                     ]
//                 ]);
    
//                 // Geser awal data ke row 4
//                 $event->sheet->setCellValue('A4', 'No'); // Header tabel
//                 // ... lanjutkan dengan header lainnya
//             },
//             AfterSheet::class => function(AfterSheet $event) {
//                 // Auto-size kolom
//                 foreach (range('A', 'H') as $col) {
//                     $event->sheet->getColumnDimension($col)->setAutoSize(true);
//                 }
//             }
//         ];
//     }

//     protected function calculateTotalAchievement()
//     {
//         // Hitung total pencapaian dari data
//         $averages = array_filter($this->averageMonthlyData, fn($v) => is_numeric($v));
//         return count($averages) > 0 
//             ? number_format(array_sum($averages) / count($averages), 1)
//             : 0;
//     }


//     public function collection()
//     {
//         $subHeaders = $this->subHeader();
//         $allPrograms = $this->getAllPrograms();
//         $targets = $this->getTarget();
//         $frecuency = $this->getFrecuency();
//         $pic = $this->getPic();
    
//         //hardcode weights value
//         $hardcodedWeights = [
//             // Program Utama SAJA (total bobot untuk program + semua sub-nya)
//             '1' => 20,  // Total bobot untuk Fatigue Preventive + semua sub-programnya
//             '2' => 20,  // Total bobot untuk Safety Inspection + semua sub-programnya
//             '3' => 15,
//             '4' => 15,
//             '5' => 10,
//             '6' => 10,
//             '7' => 10
//         ];

//         // Gabungkan data program dengan target dan average
//         $programsWithData = [];
//         foreach ($allPrograms as $program) {
//             $target = collect($targets)->firstWhere('kode', $program['kode']);
//             $programsWithData[] = array_merge($program, [
//                 'target' => $target['target'] ?? '',
//                 'average' => $this->averageMonthlyData[$program['kode']] ?? 0
//             ]);
//         }
    
//         // Kelompokkan berdasarkan nomor utama
//         $groupedPrograms = [];
//         foreach ($programsWithData as $program) {
//             $mainNo = explode('.', $program['kode'])[0];
//             $groupedPrograms[$mainNo][] = $program;
//         }
    
//         $data = collect();
//         ksort($groupedPrograms);
    
//         foreach ($groupedPrograms as $mainNo => $programs) {
//             // Header utama
//             $mainHeader = collect($subHeaders)->firstWhere('No', $mainNo);
            
//             // Hitung bobot pencapaian sesuai rumus Excel
//             $validAverages = array_filter(array_column($programs, 'average'), function($value) {
//                 return $value !== null && $value !== 'NA';
//             });


            
//             $sum = array_sum($validAverages);
//             $count = count($validAverages);
            
//             $weightedAverage = $count > 0 ? $sum / $count : 0;
    
//             $data->push([
//                 'No' => $mainNo,
//                 'nama_program' => $mainHeader['ProgramKerja'] ?? '',
//                 'Target' => '',
//                 'Frekuensi' => '',
//                 'PIC' => '',
//                 'Bobot Penilaian' => $hardcodedWeights[$mainNo] . '%',
//                 'Pencapaian' => round($weightedAverage) . '%',
//                 'Nilai Akhir' => round(($hardcodedWeights[$mainNo] * $weightedAverage) / 100) . '%'
//             ]);
    
//             // Sub program
//             foreach ($programs as $program) {
//                 $freq = collect($frecuency)->firstWhere('kode', $program['kode']);
    
//                 $data->push([
//                     'No' => $program['kode'],
//                     'nama_program' => $program['nama'],
//                     'Target' => str_replace(["\r\n", "\n", "\r"], PHP_EOL, $program['target']),
//                     'Frekuensi' => $freq['frecuency'] ?? '',
//                     'PIC' => $pic,
//                     'Bobot Penilaian' => '',
//                     'Pencapaian' => ($program['average'] ?? 0) . '%',
//                     'Nilai Akhir' => ''
//                 ]);
//             }
//         }
    
//         return $data;
//     }

//     public function headings(): array
//     {
//         return ['No','Program Kerja', 'Target', 'Frekuensi', 'PIC', 'Bobot Penilaian', 'Pencapaian', 'Nilai Akhir'];

        
//     }

//     // public function registerEvents(): array
//     // {
//     //     return [
//     //         AfterSheet::class => function(AfterSheet $event) {
//     //             // Auto-size kolom A sampai Z
//     //             foreach (range('A', 'Z') as $column) {
//     //                 $event->sheet->getColumnDimension($column)->setAutoSize(true);
//     //             }
                
//     //             // Atur wrap text untuk semua cell yang membutuhkan
//     //             $event->sheet->getStyle('A1:Z1000')->getAlignment()->setWrapText(true);
                
//     //             // Atur tinggi row secara otomatis (opsional)
//     //             $event->sheet->getDefaultRowDimension()->setRowHeight(-1);
//     //         },
//     //     ];
//     // }
    
//     public function styles(Worksheet $sheet)
//     {
//         return [
//             // Header row
//             1 => [
//                 'font' => ['bold' => true],
//                 'alignment' => [
//                     'wrapText' => true,
//                     'vertical' => 'center',
//                     'horizontal' => 'center'
//                 ]
//             ],
//             // Data rows
//             'A2:Z1000' => [
//                 'alignment' => [
//                     'wrapText' => true,
//                     'vertical' => 'center',
//                     'horizontal' => 'left'
//                 ]
//             ]
//         ];
//     }

//     public function subHeader(){
//         return[
//             ['No' => '1', 'ProgramKerja' => 'Fatigue Preventive'],
//             ['No' => '2', 'ProgramKerja' => 'Traffic Management Preventive Program'],
//             ['No' => '3', 'ProgramKerja' => 'Keselamatan Area Kerja'],
//             ['No' => '4', 'ProgramKerja' => 'Fire Prevention Management Program'],
//             ['No' => '5', 'ProgramKerja' => 'Development Manpower'],
//             ['No' => '6', 'ProgramKerja' => 'Program Kerja Kesehatan'],
//             ['No' => '7', 'ProgramKerja' => 'Program Kerja Lingkungan Hidup'],
//         ];
//     }

//     protected function getTarget(){
//         return[
//             ['kode' => '1.1', 'target' => '100% All Operator/Driver\ntergantung jumlah operator yg mengisi'],
//             ['kode' => '1.2', 'target' => '100% Upload di awal shift'],
//             ['kode' => '1.3', 'target' => '100% All Driver/Shift'],
//             ['kode' => '1.4', 'target' => '100% All Driver Shift'],
//             ['kode' => '1.5', 'target' => '100% Terlaksana sesuai Plan'],
//             ['kode' => '1.6', 'target' => 'Min. 2 Opt/Driver/Minggu'],
//             ['kode' => '2.1', 'target' => 'Point 1. (2 Driver/Opt/Minggu)\nPoint 2,3,4,5 (1 x Seminggu'],
//             ['kode' => '2.2', 'target' => 'Review 1x/Minggu'],
//             ['kode' => '2.2', 'target' => 'Pelaksanaan sidak kecepatan min. 2x/minggu\nReview 1x/Minggu'],
//             ['kode' => '3.1', 'target' => '100% Terlaksana sesuai Plan'],
//             ['kode' => '3.2', 'target' => '100% Terlaksana'],
//             ['kode' => '3.3', 'target' => '100% Terlaksana'],
//             ['kode' => '4.1', 'target' => '100% Terlaksana All Unit Sesuai Jadwal'],
//             ['kode' => '4.2', 'target' => '100% All APAR'],
//             ['kode' => '5.1', 'target' => '100% Terlaksana All GL Up POP'],
//             ['kode' => '5.2', 'target' => '100% Terlaksana'],
//             ['kode' => '5.3', 'target' => '100% Terlaksana'],
//             ['kode' => '5.4', 'target' => '100% Terlaksana'],
//             ['kode' => '5.5', 'target' => '100% Terlaksana'],
//             ['kode' => '5.6', 'target' => '100% Tidak ada incident \n(LTI, Nearmis, PD, Fatality'],
//             ['kode' => '6.1', 'target' => '100% All Karyawan Mitra'],
//             ['kode' => '6.2', 'target' => '100% Terlaksana'],
//             ['kode' => '7.1', 'target' => '100% Terlaksana sesuai jadwal'],
//             ['kode' => '7.2', 'target' => 'Review 1x/Minggu'],
//         ];
        
//     }


//     protected function getFrecuency(){
//        return[
//         ['kode' => '1.1', 'frecuency' => 'Shiftly'],
//         ['kode' => '1.2', 'frecuency' => 'Daily'],
//         ['kode' => '1.3', 'frecuency' => 'Shiftly'],
//         ['kode' => '1.4', 'frecuency' => 'Shiftly'],
//         ['kode' => '1.5', 'frecuency' => 'Monthly'],
//         ['kode' => '1.6', 'frecuency' => 'Weekly'],
//         ['kode' => '2.1', 'frecuency' => 'Weekly'],
//         ['kode' => '2.2', 'frecuency' => 'Weekly'],
//         ['kode' => '2.3', 'frecuency' => 'Weekly'],
//         ['kode' => '3.1', 'frecuency' => 'Weekly'],
//         ['kode' => '3.2', 'frecuency' => 'Weekly'],
//         ['kode' => '3.3', 'frecuency' => 'Monthly'],
//         ['kode' => '4.1', 'frecuency' => 'As Plan'],
//         ['kode' => '4.2', 'frecuency' => 'Monthly'],
//         ['kode' => '5.1', 'frecuency' => 'As Plan ATMP'],
//         ['kode' => '5.2', 'frecuency' => 'Monthly'],
//         ['kode' => '5.3', 'frecuency' => 'Monthly'],
//         ['kode' => '5.4', 'frecuency' => 'Yearly'],
//         ['kode' => '5.5', 'frecuency' => 'Monthly'],
//         ['kode' => '5.6', 'frecuency' => 'By Case'],
//         ['kode' => '6.1', 'frecuency' => 'Yearly'],
//         ['kode' => '6.2', 'frecuency' => '2 x /Bulan'],
//         ['kode' => '7.1', 'frecuency' => 'Weekly'],
//         ['kode' => '7.2', 'frecuency' => 'Weekly'],
        

//        ]; 
//     }

//     protected function getAllPrograms()
//     {
//         return [
//             ['kode' => '1.1', 'nama' => 'Fit to Work di Awal Shift'],
//             ['kode' => '1.2', 'nama' => 'Evaluasi D Fit (Operator DT)'],
//             ['kode' => '1.3', 'nama' => 'Fatigue Check/Streaching Operational DT di Jam Kritis (1x setiap shift sesuai regulasi site)'],
//             ['kode' => '1.4', 'nama' => 'Wake Up Call Operator A2B diluar Jam Kritis by Radio/Voice + Form'],
//             ['kode' => '1.5', 'nama' => 'Inspeksi/Perkunjungan Mess / Rumah Tinggal / Video Conference Karyawan Mitra yang teridentifikasi Fatigue berulang (SAGA)'],
//             ['kode' => '1.6', 'nama' => 'Sidak Napping Driver/Operator'],
//             ['kode' => '2.1', 'nama' => 'Inspeksi & Observasi Tematik (Mandiri & Gabungan). Focus item :'],
//             ['kode' => '2.2', 'nama' => 'Kelayakan kendaraan : \n1. Komisioning & re-komisioning unit, \n2. Jadwal Maintenance & Service dan Pelaksanaanya'],
//             ['kode' => '2.3', 'nama' => 'Evaluasi kecepatan unit wheel (non sarana)'],
//             ['kode' => '3.1', 'nama' => 'Observasi/Pendampingan PJO'],
//             ['kode' => '3.2', 'nama' => 'Gelar/Inspeksi Tools'],
//             ['kode' => '3.3', 'nama' => 'Penilaian Kondisi Fisik/Housekeeeping Workshop Mitra'],
//             ['kode' => '4.1', 'nama' => 'Pencucian Unit terjadwal A2B & DT'],
//             ['kode' => '4.2', 'nama' => 'Inspeksi APAR Bulanan Unit dan Bangunan'],
//             ['kode' => '5.1', 'nama' => 'SKKP/POP For GL Mitra'],
//             ['kode' => '5.2', 'nama' => 'Training HRCP Mitra (Posisi PJO GL Produksi, GL Plant dan SHE Officer)'],
//             ['kode' => '5.3', 'nama' => 'Training Additional Plant (GL Plant dan SHE Officer)'],
//             ['kode' => '5.4', 'nama' => 'Review IBPR'],
//             ['kode' => '5.5', 'nama' => 'Review SMKP For Mitra Kerja'],
//             ['kode' => '5.6', 'nama' => 'Pembinaan Pelanggaran'],
//             ['kode' => '6.1', 'nama' => 'Kontrol & Monitor MCU Tahunan'],
//             ['kode' => '6.2', 'nama' => 'Kontrol & Monitor Rutin Karyawan dengan Penyakit Kritis Kronis'],
//             ['kode' => '7.1', 'nama' => 'Krida area office/workshop (penghijauan dan kerja bakti)'],
//             ['kode' => '7.2', 'nama' => 'Pengelolaan lingkungan area Workshop terhadap ceceran dan tumpahan oli, fuel serta B3 saat melakukan kegiatan repair maintenance unit']
//         ];
//     }


//     protected function calculateBobotPencapaian($rows)
//     {
//         $total = 0;
//         $count = 0;
        
//         foreach ($rows as $row) {
//             // Skip jika nilai 'Pencapaian' adalah 'NA' atau kosong
//             if (isset($row['Pencapaian']) && $row['Pencapaian'] !== 'NA' && $row['Pencapaian'] !== '') {
//                 // Hilangkan '%' dan konversi ke float
//                 $value = str_replace('%', '', $row['Pencapaian']);
//                 if (is_numeric($value)) {
//                     $total += (float)$value;
//                     $count++;
//                 }
//             }
//         }
        
//         return $count > 0 ? round($total / $count, 2) : 0;
//     }
    
//     protected function getPic(){
//         return Mitra::find($this->mitraId)->nama_perusahaan ?? 'N/A';
//     }

// }

class BisnisProkerSheet implements FromView, WithEvents, WithStyles, WithTitle
{

    protected $mitraId;
    protected $startDate;
    protected $endDate;
    protected $averageMonthlyData;
    protected $mitraName;
    protected $location;
    protected $period;
    protected $userCount;
    protected $lastRow;

    public function __construct($mitraId, $startDate, $endDate, array $averageMonthlyData = [])
    {
        $this->mitraId = $mitraId;
        $this->startDate = Carbon::parse($startDate);
        $this->endDate = Carbon::parse($endDate);
        $this->averageMonthlyData = $averageMonthlyData;

        $mitra = Mitra::find($mitraId);
        $this->mitraName = Mitra::find($this->mitraId)->nama_perusahaan ?? 'N/A';   
        $this->location = Mitra::find($this->mitraId)->alamat ?? 'N/A';
        $this->period = $this->generatePeriodString($this->startDate, $this->endDate);
        $this->userCount = User::where('data_mitra_id', $mitraId)->count();
    }

    public function view(): View
    {
        $this->lastRow = count($this->prepareCollectionData()) + 1;
        return view('exports.proker', [
            'data' => $this->prepareCollectionData(),
            'headerInfo' => [
                'location' => $this->location,
                'mitraName' => $this->mitraName,
                'period' => $this->period,
                'achievement' => $this->calculateBobotPencapaian()
            ],
            'lastRow' => $this->lastRow
        ]);
    }

    protected function prepareCollectionData()
    {
        // Pindahkan logika collection() Anda ke sini
        // ... kode collection() yang ada ...
        
        //     {
            $subHeaders = $this->subHeader();
                    $allPrograms = $this->getAllPrograms();
                    $targets = $this->getTarget();
                    $frecuency = $this->getFrecuency();
                    $pic = $this->getPic();
                
                    //hardcode weights value
                    $hardcodedWeights = [
                        // Program Utama SAJA (total bobot untuk program + semua sub-nya)
                        '1' => 20,  // Total bobot untuk Fatigue Preventive + semua sub-programnya
                        '2' => 20,  // Total bobot untuk Safety Inspection + semua sub-programnya
                        '3' => 15,
                        '4' => 15,
                        '5' => 10,
                        '6' => 10,
                        '7' => 10
                    ];
            
                    // Gabungkan data program dengan target dan average
                    $programsWithData = [];
                    foreach ($allPrograms as $program) {
                        $target = collect($targets)->firstWhere('kode', $program['kode']);
                        $programsWithData[] = array_merge($program, [
                            'target' => $target['target'] ?? '',
                            'average' => $this->averageMonthlyData[$program['kode']] ?? 0
                        ]);
                    }
                
                    // Kelompokkan berdasarkan nomor utama
                    $groupedPrograms = [];
                    foreach ($programsWithData as $program) {
                        $mainNo = explode('.', $program['kode'])[0];
                        $groupedPrograms[$mainNo][] = $program;
                    }
                
                    $data = collect();
                    ksort($groupedPrograms);
                
                    foreach ($groupedPrograms as $mainNo => $programs) {
                        // Header utama
                        $mainHeader = collect($subHeaders)->firstWhere('No', $mainNo);
                        
                        // Hitung bobot pencapaian sesuai rumus Excel
                        $validAverages = array_filter(array_column($programs, 'average'), function($value) {
                            return $value !== null && $value !== 'NA';
                        });
            
            
                        
                        $sum = array_sum($validAverages);
                        $count = count($validAverages);
                        
                        $weightedAverage = $count > 0 ? $sum / $count : 0;
                
                        $data->push([
                            'No' => $mainNo,
                            'nama_program' => $mainHeader['ProgramKerja'] ?? '',
                            'Target' => '',
                            'Frekuensi' => '',
                            'PIC' => '',
                            'Bobot Penilaian' => $hardcodedWeights[$mainNo] . '%',
                            'Pencapaian' => round($weightedAverage) . '%',
                            'Nilai Akhir' => round(($hardcodedWeights[$mainNo] * $weightedAverage) / 100) . '%'
                        ]);
                
                        // Sub program
                        foreach ($programs as $program) {
                            $freq = collect($frecuency)->firstWhere('kode', $program['kode']);
                
                            $data->push([
                                'No' => $program['kode'],
                                'nama_program' => $program['nama'],
                                'Target' => str_replace(["\r\n", "\n", "\r"], PHP_EOL, $program['target']),
                                'Frekuensi' => $freq['frecuency'] ?? '',
                                'PIC' => $pic,
                                'Bobot Penilaian' => '',
                                'Pencapaian' => ($program['average'] ?? 0) . '%',
                                'Nilai Akhir' => ''
                            ]);
                        }
                    }
                
                    return $data;
    }
            

                // generated periode
    protected function generatePeriodString($startDate, $endDate)
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        
        if ($start->format('Y-m') === $end->format('Y-m')) {
            return $start->translatedFormat('F Y');
        }
        
        if ($start->format('Y') === $end->format('Y')) {
            return $start->translatedFormat('F') . ' - ' . $end->translatedFormat('F Y');
        }
        
        return $start->translatedFormat('F Y') . ' - ' . $end->translatedFormat('F Y');
    }
               
    public function headings(): array
    {
        return ['No','Program Kerja', 'Target', 'Frekuensi', 'PIC', 'Bobot Penilaian', 'Pencapaian', 'Nilai Akhir'];

        
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Manipulasi Excel setelah render
                foreach (range('A', 'H') as $col) {
                    $event->sheet->getColumnDimension($col)->setAutoSize(true);
                }
                $event->sheet->getStyle('A1:H100')->getAlignment()->setWrapText(true);
            }
        ];
    }

    public function title(): string
    {
        return 'Draft Proker K3KOLH Mitra Kerja';
    }
    
    public function styles(Worksheet $sheet)
    {
        return [
            // Header row
            1 => [
                'font' => ['bold' => true],
                'alignment' => [
                    'wrapText' => true,
                    'vertical' => 'center',
                    'horizontal' => 'center'
                ]
            ],
            // Data rows
            'A2:Z1000' => [
                'alignment' => [
                    'wrapText' => true,
                    'vertical' => 'center',
                    'horizontal' => 'left'
                ]
            ],

            'A'.$this->lastRow.':H'.($this->lastRow+1) => [
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ],

        ];
    }

        public function subHeader(){
        return[
            ['No' => '1', 'ProgramKerja' => 'Fatigue Preventive'],
            ['No' => '2', 'ProgramKerja' => 'Traffic Management Preventive Program'],
            ['No' => '3', 'ProgramKerja' => 'Keselamatan Area Kerja'],
            ['No' => '4', 'ProgramKerja' => 'Fire Prevention Management Program'],
            ['No' => '5', 'ProgramKerja' => 'Development Manpower'],
            ['No' => '6', 'ProgramKerja' => 'Program Kerja Kesehatan'],
            ['No' => '7', 'ProgramKerja' => 'Program Kerja Lingkungan Hidup'],
        ];
    }

    protected function getTarget(){
        return[
            ['kode' => '1.1', 'target' => '100% All Operator/Driver tergantung jumlah operator yg mengisi'],
            ['kode' => '1.2', 'target' => '100% Upload di awal shift'],
            ['kode' => '1.3', 'target' => '100% All Driver/Shift'],
            ['kode' => '1.4', 'target' => '100% All Driver Shift'],
            ['kode' => '1.5', 'target' => '100% Terlaksana sesuai Plan'],
            ['kode' => '1.6', 'target' => 'Min. 2 Opt/Driver/Minggu'],
            ['kode' => '2.1', 'target' => 'Point 1. (2 Driver/Opt/Minggu) Point 2,3,4,5 (1 x Seminggu'],
            ['kode' => '2.2', 'target' => 'Review 1x/Minggu'],
            ['kode' => '2.3', 'target' => 'Pelaksanaan sidak kecepatan min. 2x/minggu Review 1x/Minggu'],
            ['kode' => '3.1', 'target' => '100% Terlaksana sesuai Plan'],
            ['kode' => '3.2', 'target' => '100% Terlaksana'],
            ['kode' => '3.3', 'target' => '100% Terlaksana'],
            ['kode' => '4.1', 'target' => '100% Terlaksana All Unit Sesuai Jadwal'],
            ['kode' => '4.2', 'target' => '100% All APAR'],
            ['kode' => '5.1', 'target' => '100% Terlaksana All GL Up POP'],
            ['kode' => '5.2', 'target' => '100% Terlaksana'],
            ['kode' => '5.3', 'target' => '100% Terlaksana'],
            ['kode' => '5.4', 'target' => '100% Terlaksana'],
            ['kode' => '5.5', 'target' => '100% Terlaksana'],
            ['kode' => '5.6', 'target' => '100% Tidak ada incident (LTI, Nearmis, PD, Fatality'],
            ['kode' => '6.1', 'target' => '100% All Karyawan Mitra'],
            ['kode' => '6.2', 'target' => '100% Terlaksana'],
            ['kode' => '7.1', 'target' => '100% Terlaksana sesuai jadwal'],
            ['kode' => '7.2', 'target' => 'Review 1x/Minggu'],
        ];
        
    }


    protected function getFrecuency(){
       return[
        ['kode' => '1.1', 'frecuency' => 'Shiftly'],
        ['kode' => '1.2', 'frecuency' => 'Daily'],
        ['kode' => '1.3', 'frecuency' => 'Shiftly'],
        ['kode' => '1.4', 'frecuency' => 'Shiftly'],
        ['kode' => '1.5', 'frecuency' => 'Monthly'],
        ['kode' => '1.6', 'frecuency' => 'Weekly'],
        ['kode' => '2.1', 'frecuency' => 'Weekly'],
        ['kode' => '2.2', 'frecuency' => 'Weekly'],
        ['kode' => '2.3', 'frecuency' => 'Weekly'],
        ['kode' => '3.1', 'frecuency' => 'Weekly'],
        ['kode' => '3.2', 'frecuency' => 'Weekly'],
        ['kode' => '3.3', 'frecuency' => 'Monthly'],
        ['kode' => '4.1', 'frecuency' => 'As Plan'],
        ['kode' => '4.2', 'frecuency' => 'Monthly'],
        ['kode' => '5.1', 'frecuency' => 'As Plan ATMP'],
        ['kode' => '5.2', 'frecuency' => 'Monthly'],
        ['kode' => '5.3', 'frecuency' => 'Monthly'],
        ['kode' => '5.4', 'frecuency' => 'Yearly'],
        ['kode' => '5.5', 'frecuency' => 'Monthly'],
        ['kode' => '5.6', 'frecuency' => 'By Case'],
        ['kode' => '6.1', 'frecuency' => 'Yearly'],
        ['kode' => '6.2', 'frecuency' => '2 x /Bulan'],
        ['kode' => '7.1', 'frecuency' => 'Weekly'],
        ['kode' => '7.2', 'frecuency' => 'Weekly'],
        

       ]; 
    }

    protected function getAllPrograms()
    {
        return [
            ['kode' => '1.1', 'nama' => 'Fit to Work di Awal Shift'],
            ['kode' => '1.2', 'nama' => 'Evaluasi D Fit (Operator DT)'],
            ['kode' => '1.3', 'nama' => 'Fatigue Check/Streaching Operational DT di Jam Kritis (1x setiap shift sesuai regulasi site)'],
            ['kode' => '1.4', 'nama' => 'Wake Up Call Operator A2B diluar Jam Kritis by Radio/Voice + Form'],
            ['kode' => '1.5', 'nama' => 'Inspeksi/Perkunjungan Mess / Rumah Tinggal / Video Conference Karyawan Mitra yang teridentifikasi Fatigue berulang (SAGA)'],
            ['kode' => '1.6', 'nama' => 'Sidak Napping Driver/Operator'],
            ['kode' => '2.1', 'nama' => 'Inspeksi & Observasi Tematik (Mandiri & Gabungan). Focus item:
                1. MTO/PTO
                2. Kelayakan Unit
                3. Housekeeping Cabin & Administrasi
                4. Pengecekan wheel nut
                5. Standar Buggy whip (unit mining)'],
            ['kode' => '2.2', 'nama' => 'Kelayakan kendaraan : 1. Komisioning & re-komisioning unit, 2. Jadwal Maintenance & Service dan Pelaksanaanya'],
            ['kode' => '2.3', 'nama' => 'Evaluasi kecepatan unit wheel (non sarana)'],
            ['kode' => '3.1', 'nama' => 'Observasi/Pendampingan PJO'],
            ['kode' => '3.2', 'nama' => 'Gelar/Inspeksi Tools'],
            ['kode' => '3.3', 'nama' => 'Penilaian Kondisi Fisik/Housekeeeping Workshop Mitra'],
            ['kode' => '4.1', 'nama' => 'Pencucian Unit terjadwal A2B & DT'],
            ['kode' => '4.2', 'nama' => 'Inspeksi APAR Bulanan Unit dan Bangunan'],
            ['kode' => '5.1', 'nama' => 'SKKP/POP For GL Mitra'],
            ['kode' => '5.2', 'nama' => 'Training HRCP Mitra (Posisi PJO GL Produksi, GL Plant dan SHE Officer)'],
            ['kode' => '5.3', 'nama' => 'Training Additional Plant (GL Plant dan SHE Officer)'],
            ['kode' => '5.4', 'nama' => 'Review IBPR'],
            ['kode' => '5.5', 'nama' => 'Review SMKP For Mitra Kerja'],
            ['kode' => '5.6', 'nama' => 'Pembinaan Pelanggaran'],
            ['kode' => '6.1', 'nama' => 'Kontrol & Monitor MCU Tahunan'],
            ['kode' => '6.2', 'nama' => 'Kontrol & Monitor Rutin Karyawan dengan Penyakit Kritis Kronis'],
            ['kode' => '7.1', 'nama' => 'Krida area office/workshop (penghijauan dan kerja bakti)'],
            ['kode' => '7.2', 'nama' => 'Pengelolaan lingkungan area Workshop terhadap ceceran dan tumpahan oli, fuel serta B3 saat melakukan kegiatan repair maintenance unit']
        ];
    }


    protected function calculateBobotPencapaian()
    {
        $rows = $this->prepareCollectionData()->toArray();
        $total = 0;
        $count = 0;
        
        foreach ($rows as $row) {
            // Skip jika nilai 'Pencapaian' adalah 'NA' atau kosong
            if (isset($row['Pencapaian']) && $row['Pencapaian'] !== 'NA' && $row['Pencapaian'] !== '') {
                // Hilangkan '%' dan konversi ke float
                $value = str_replace('%', '', $row['Pencapaian']);
                if (is_numeric($value)) {
                    $total += (float)$value;
                    $count++;
                }
            }
        }
        
        return $count > 0 ? round($total / $count, 2) : 0;
    }
    
    protected function getPic(){
        return Mitra::find($this->mitraId)->nama_perusahaan ?? 'N/A';
    }
}

class BisnisFailedSheet implements FromCollection, WithHeadings, WithStyles, WithTitle
{
    protected $mitraId;
    protected $startDate;
    protected $endDate;
    protected $userCount;
    protected $summarySheet;
    protected $failedItems = [];

    // public function getValueProker():array
    // {
    //     return $this->valueProker;
    // }

    public function __construct(BisnisSummarySheet $summarySheet, $mitraId, $startDate, $endDate)
    {



        $this->mitraId = $mitraId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->userCount = User::where('data_mitra_id', $mitraId)->count();

        $this->summarySheet = $summarySheet;
        
        // dd($this->summarySheet->getProgramFatigueData());
        Log::debug(['data fatigue dari Summary:' => $summarySheet->getProgramFatigueData()]);
        Log::debug(['data fire dari Summary:' => $summarySheet->getProgramFirePreventifData()]);
        Log::debug(['data lingkungan dari Summary:' => $summarySheet->getProgramLingkunganData()]);
        Log::debug(['data kesehatan dari Summary:' => $summarySheet->getProgramKesehatanData()]);
        Log::debug(['data inspeksi dari Summary:' => $summarySheet->getProgramInspeksiData()]);
        Log::debug(['data develoopment dari Summary:' => $summarySheet->getProgramDevelopmentData()]);
        Log::debug(['data keselamatan dari Summary:' => $summarySheet->getProgramKeselamatanData()]);
    
    }

    // public function getAllProgramData() 
    // {
    //     return [
    //         'fatigue' => $this->programFatigueData ?? [],
    //         'kesehatan' => $this->programKesehatanData ?? [],
    //         'keselamatan' => $this->programKeselamatanData ?? [],
    //         'lingkungan' => $this->programLingkunganData ?? [],
    //         'inspeksi' => $this->programInspeksiData ?? [],
    //         'fire' => $this->programFirePreventifData ?? [],
    //         'development' => $this->programDevelopmentData ?? [],
    //         'subcont' => $this->subContData ?? [],
    //         // ... tambahkan semua data program lainnya ...
    //     ];
    // }

    // Di dalam BisnisFailedSheet:
    // public function collection()
    // {
    //     // Ambil semua data dari summary sheet
    //     $allData = $this->summaryData->getAllProgramData();
        
    //     // Gabungkan semua array data
    //     $combinedData = array_merge(
    //         $allData['fatigue'],
    //         $allData['kesehatan'],
    //         $allData['keselamatan'],
    //         $allData['inspeksi'],
    //         $allData['lingkungan'],
    //         $allData['fire'],
    //         $allData['development'],
    //         // ... tambahkan semua dataset lainnya ...
    //     );
        
    //     return collect($combinedData);
    // }

    // public function getAllUnderachievedData()
    // {
    //     $allData = array_merge(
    //         $this->summarySheet->getProgramFatigueData() ?? [],
    //         $this->summarySheet->getProgramKeselamatanData() ?? [],
    //         $this->summarySheet->getProgramFirePreventifData() ?? [],
    //         $this->summarySheet->getProgramInspeksiData() ?? [],
    //         $this->summarySheet->getProgramLingkunganData() ?? [],
    //         $this->summarySheet->getProgramDevelopmentData() ?? [],
    //         $this->summarySheet->getProgramKesehatanData() ?? [],
    //         $this->summarySheet->getSubContData() ?? [],
    //         // ... tambahkan semua data program lainnya ...
    //     );

    //     // Filter hanya yang pencapaian < 100
    //     return array_filter($allData, function($item) {
    //         return $item['pencapaian'] < 100;
    //     });
    // }

    // public function collection()
    // {

    //     $underachievedData = array_filter(
    //         array_merge(
    //             $this->summarySheet->getProgramFatigueData() ?? [],
    //             $this->summarySheet->getProgramKeselamatanData() ?? [],
    //             $this->summarySheet->getProgramFirePreventifData() ?? [],
    //             $this->summarySheet->getProgramInspeksiData() ?? [],
    //             $this->summarySheet->getProgramLingkunganData() ?? [],
    //             $this->summarySheet->getProgramDevelopmentData() ?? [],
    //             $this->summarySheet->getProgramKesehatanData() ?? [],
    //             // $this->$summarySheet->getSubContData() ?? [],
    //             // ... tambahkan data lainnya ...
    //         ),
    //         function($item) {
    //             return isset($item['pencapaian']) && $item['pencapaian'] < 100;
    //         }
    //     );
    //     // dd($this->summarySheet->getProgramFatigueData());
    //     // dd([$underachievedData]);
    //     // dd($underachievedData[0]);
    //     Log::debug($underachievedData);
    //     return collect($underachievedData);
    // }

    public function collection()
{
    $underachievedData = array_filter(
        array_merge(
            $this->summarySheet->getProgramFatigueData() ?? [],
            $this->summarySheet->getProgramKeselamatanData() ?? [],
            $this->summarySheet->getProgramFirePreventifData() ?? [],
            $this->summarySheet->getProgramInspeksiData() ?? [],
            $this->summarySheet->getProgramLingkunganData() ?? [],
            $this->summarySheet->getProgramDevelopmentData() ?? [],
            $this->summarySheet->getProgramKesehatanData() ?? [],
        ),
        function($item) {
            return isset($item['pencapaian']) && $item['pencapaian'] < 100;
        }
    );

    // Transform data sesuai urutan header
    return collect($underachievedData)->map(function ($item) {
        return [
            $item['kode_program'] ?? '', // Kode Program
            $item['nama_program'] ?? '', // Program Tidak Tercapai
            $item['site'] ?? 'AGMR',         // Site
            $item['subkon'] ?? '',      // Subcont
            $item['periode'] ?? '',      // Periode
            $this->getProblemDescription($item['kode_program'] ?? '', $item['pencapaian'] ?? 0), // Problem
            $item['wsbh'] ?? '',        // WSBH
            $item['wah'] ?? '',         // WAH
            $this->getCorrectiveAction($item['kode_program'] ?? '', $item['pencapaian'] ?? 0), // Corrective Action
            ($item['pencapaian'] ?? 0) . '%', // Score Before Corrective Action
            $item['score_after'] ?? ''   // Score After Corrective Action
        ];
    });

    Log::debug('Transformed Data:', collect($underachievedData)->first());
}


    public function headings(): array
    {
        return [
            'Kode Program',
            'Program Tidak Tercapai',
            'Site',
            'Subcont',
            'Periode',
            'Problem',
            'WSBH',
            'WAH',
            'Corrective Action',
            'Score Before Corrective Action',
            'Score After Corrective Action'
        ];
    }

    // public function map($item): array
    // {
    //     \Log::debug('Mapping item:', ['item' => $item]);

    //     $indexedItem = array_values($item);
    //     \Log::debug('Array values:', ['values' => $values]);


    //    $result = [
    //         'Kode Program' => $item['kode_program'] ?? $values[2] ?? 'N/A',
    //         'Program Tidak Tercapai' => $item['nama_program'] ?? $values[3] ?? 'N/A',
    //         'Site' => ($item['subcont_code'] ?? $values[8] ?? 'N/A') === 'N/A' ? 'AMGR' : $item['subcont_code'] ?? $values[8],
    //         'Subcont' => $item['subkon'] ?? $values[7] ?? 'N/A',
    //         'Periode' => $item['periode'] ?? $values[0] ?? 'N/A',
    //         'Problem' => $this->getProblemDescription($item['kode_program'] ?? $values[2] ?? '', $item['pencapaian'] ?? $values[6] ?? 0),
    //         'WSBH' => '',
    //         'WAH' => '',
    //         'Corrective Action' => $this->getRecommendation($item['kode_program'] ?? $values[2] ?? '', $item['pencapaian'] ?? $values[6] ?? 0),
    //         'Score Before Corrective Action' => ($item['pencapaian'] ?? $values[6] ?? 0) . '%',
    //         'Score After Corrective Action' => ''
    //     ];

        
    //     \Log::debug('Mapping result:', ['result' => $result]);
    //     return $result;
    // }

    // public function map($item): array
    // {
    //     // Definisikan urutan key yang diinginkan
    //     $orderedKeys = ['periode', 'week', 'kode_program', 'nama_program', 'plan', 'act', 'pencapaian', 'subkon', 'subcont_code'];
        
    //     // Konversi ke array terindeks dengan urutan spesifik
    //     $indexedItem = [];
    //     foreach ($orderedKeys as $key) {
    //         $indexedItem[] = $item[$key] ?? null;
    //     }
    
    //     return [
    //         'Kode Program' => $indexedItem[2] ?? 'N/A', // kode_program
    //         'Program Tidak Tercapai' => $indexedItem[3] ?? 'N/A', // nama_program
    //         'Site' => ($indexedItem[8] ?? 'N/A') === 'N/A' ? 'AMGR' : $indexedItem[8], // subcont_code
    //         'Subcont' => $indexedItem[7] ?? 'N/A', // subkon
    //         'Periode' => $indexedItem[0] ?? 'N/A', // periode
    //         'Problem' => $this->getProblemDescription($indexedItem[2] ?? '', $indexedItem[6] ?? 0), // kode_program dan pencapaian
    //         'WSBH' => '',
    //         'WAH' => '',
    //         'Corrective Action' => $this->getRecommendation($indexedItem[2] ?? '', $indexedItem[6] ?? 0), // kode_program dan pencapaian
    //         'Score Before Corrective Action' => ($indexedItem[6] ?? 0) . '%', // pencapaian
    //         'Score After Corrective Action' => ''
    //     ];
    // }
    // public function map($item): array
    // {
    //     // Definisikan mapping sesuai urutan headings
    //     $mappedData = [
    //         'Kode Program' => $item['kode_program'] ?? 'N/A',
    //         'Program Tidak Tercapai' => $item['nama_program'] ?? 'N/A',
    //         'Site' => $item['subcont_code'] === 'N/A' ? 'AMGR' : ($item['subcont_code'] ?? 'AMGR'),
    //         'Subcont' => $item['subkon'] ?? 'N/A',
    //         'Periode' => $item['periode'] ?? 'N/A',
    //         'Problem' => $this->getProblemDescription($item['kode_program'] ?? '', $item['pencapaian'] ?? 0),
    //         'WSBH' => $item['wsbh'] ?? '',
    //         'WAH' => $item['wah'] ?? '',
    //         'Corrective Action' => $this->getRecommendation($item['kode_program'] ?? '', $item['pencapaian'] ?? 0),
    //         'Score Before Corrective Action' => ($item['pencapaian'] ?? 0) . '%',
    //         'Score After Corrective Action' => $item['score_after'] ?? ''
    //     ];

    //     // Validasi: Pastikan jumlah kolom match dengan headings
    //     if (count($mappedData) !== count($this->headings())) {
    //         Log::error('Jumlah kolom tidak sesuai', [
    //             'mapped' => array_keys($mappedData),
    //             'headings' => $this->headings()
    //         ]);
    //     }

    //     return $mappedData;
    // }

    // public function map($item): array
    // {
    //     // Handle error message
    //     if (isset($item['error'])) {
    //         return array_fill_keys($this->headings(), $item['error']);
    //     }

    //     // Define all possible fields
    //     $fieldMapping = [
    //         'Kode Program' => $item['kode_program'] ?? 'N/A',
    //         'Program Tidak Tercapai' => $item['nama_program'] ?? 'N/A',
    //         'Site' => ($item['subcont_code'] ?? 'N/A') === 'N/A' ? 'AMGR' : $item['subcont_code'],
    //         'Subcont' => $item['subkon'] ?? 'N/A',
    //         'Periode' => $item['periode'] ?? 'N/A',
    //         'Problem' => $this->getProblemDescription($item['kode_program'] ?? '', $item['pencapaian'] ?? 0),
    //         'WSBH' => $item['wsbh'] ?? '',
    //         'WAH' => $item['wah'] ?? '',
    //         'Corrective Action' => $this->getRecommendation($item['kode_program'] ?? '', $item['pencapaian'] ?? 0),
    //         'Score Before Corrective Action' => ($item['pencapaian'] ?? 0) . '%',
    //         'Score After Corrective Action' => $item['score_after'] ?? ''
    //     ];

    //     // Ensure order matches headings
    //     $orderedData = [];
    //     foreach ($this->headings() as $header) {
    //         $orderedData[$header] = $fieldMapping[$header] ?? '';
    //     }
    //     Log::debug('data yg di order', $orderedData);
    //     return $orderedData;
    // }


    // public function styles(Worksheet $sheet)
    // {
    //     // Warna biru untuk kolom tertentu
    //     $blueColumns = ['B', 'C', 'D']; // Kolom B (Program Tidak Tercapai), C (Site), D (Subcont)
    //     $blueStyle = [
    //         'fill' => [
    //             'fillType' => Fill::FILL_SOLID,
    //             'startColor' => ['argb' => Color::COLOR_BLUE],
    //         ],
    //         'font' => [
    //             'color' => ['argb' => Color::COLOR_WHITE],
    //         ]
    //     ];
    
    //     // Warna kuning untuk kolom lainnya
    //     $yellowColumns = ['A', 'E', 'F', 'G', 'H', 'I', 'J', 'K'];
    //     $yellowStyle = [
    //         'fill' => [
    //             'fillType' => Fill::FILL_SOLID,
    //             'startColor' => ['argb' => Color::COLOR_YELLOW],
    //         ]
    //     ];
    
    //     // // Terapkan style untuk header (baris 1)
    //     foreach ($blueColumns as $col) {
    //         $sheet->getStyle($col.'1')->applyFromArray($blueStyle);
    //     }
        
    //     foreach ($yellowColumns as $col) {
    //         $sheet->getStyle($col.'1')->applyFromArray($yellowStyle);
    //     }
    
    //     // Style default untuk seluruh worksheet
    //     return [
    //         1 => ['font' => ['bold' => true]], // Header bold
    //         'A:K' => [
    //             'alignment' => [
    //                 'vertical' => 'center',
    //                 'horizontal' => 'center'
    //             ],
    //             'borders' => [
    //                 'allBorders' => [
    //                     'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
    //                 ]
    //             ]
    //         ],
    //     ];
    // }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
            'A:L' => ['alignment' => ['vertical' => 'center']],
        ];
    }

    public function title(): string
    {
        return 'PICA';
    }

    protected function getProblemDescription($kodeProgram, $pencapaian)
    {
        $problemMap = [
            // Program Fatigue (1.x)
            '1.1' => 'Tidak mencapai target 100% FTW harian untuk semua operator/driver',
            '1.2' => 'Evaluasi D Fit tidak diupload tepat waktu di awal shift',
            '1.3' => 'Pengecekan fatigue tidak dilakukan sesuai jadwal shift kritis',
            '1.4' => 'Wake up call tidak dilakukan secara konsisten di luar jam kritis',
            '1.5' => 'Kunjungan monitoring fatigue berulang tidak sesuai rencana',
            '1.6' => 'Frekuensi sidak napping tidak memenuhi target minimal 2x/minggu',

            // Program Traffic Management (2.x)
            '2.1' => 'Observasi tematik tidak mencapai target frekuensi mingguan',
            '2.2' => 'Maintenance kendaraan tidak sesuai jadwal yang ditetapkan',
            '2.3' => 'Tidak ada monitoring kecepatan unit wheel secara berkala',

            // Program Keselamatan (3.x)
            '3.1' => 'Pendampingan PJO tidak terlaksana 100% sesuai rencana',
            '3.2' => 'Inspeksi tools tidak dilakukan secara menyeluruh',
            '3.3' => 'Kondisi housekeeping workshop di bawah standar',

            // Program Fire Prevention (4.x)
            '4.1' => 'Pencucian unit tidak sesuai jadwal yang ditentukan',
            '4.2' => 'Inspeksi APAR bulanan tidak mencakup semua unit',

            // Program Development (5.x)
            '5.1' => 'Proses sertifikasi GL mitra tertunda',
            '5.2' => 'Training HRCP tidak dilaksanakan tepat waktu',
            '5.3' => 'Training additional plant belum komprehensif',
            '5.4' => 'Review IBPR tidak dilakukan tahunan',
            '5.5' => 'Evaluasi SMKP tidak sesuai jadwal bulanan',
            '5.6' => 'Pembinaan pelanggaran tidak konsisten',

            // Program Kesehatan (6.x)
            '6.1' => 'MCU tahunan tidak mencakup 100% karyawan',
            '6.2' => 'Monitoring karyawan dengan penyakit kronis tidak rutin',

            // Program Lingkungan (7.x)
            '7.1' => 'Kegiatan penghijauan tidak sesuai jadwal mingguan',
            '7.2' => 'Terdapat ceceran oli/fuel/B3 yang tidak tertangani'

        ];
        
        // Jika kode program tidak ada di mapping, gunakan deskripsi generik
        return $problemMap[$kodeProgram] 
            ?? ($pencapaian < 70 
                ? 'Program tidak mencapai target minimum (70%)' 
                : 'Program belum mencapai target 100%');
    }

    protected function getCorrectiveAction($kodeProgram)
    {
        $actionMap = [
            // Program Fatigue (1.x)
            '1.1' => '1. Memperketat monitoring FTW harian dengan checklist digital
    2. Memberikan coaching khusus untuk operator/driver yang sering tidak memenuhi FTW
    3. Menerapkan reward & punishment system untuk compliance FTW',
            
            '1.2' => '1. Membuat reminder otomatis di awal shift
    2. Menunjuk PIC khusus untuk upload evaluasi D Fit
    3. Memasukkan compliance upload dalam KPI supervisor',
            
            '1.3' => '1. Membuat jadwal shift kritis yang lebih visible
    2. Melakukan cross-check pencatatan pengecekan fatigue
    3. Memasang alarm reminder untuk pengecekan fatigue',
            
            '1.4' => '1. Membuat log wake up call yang diverifikasi supervisor
    2. Melakukan random check via phone recording
    3. Menyediakan backup caller untuk tiap shift',
            
            '1.5' => '1. Membuat tracker kunjungan real-time
    2. Menjadwalkan ulang kunjungan yang tertunda dalam 24 jam
    3. Melaporkan hasil kunjungan dalam daily meeting',
            
            '1.6' => '1. Membuat jadwal sidak yang terjadwal
    2. Melibatkan manajemen dalam sidak mendadak
    3. Dokumentasikan bukti sidak di dashboard digital',
    
            // Program Traffic Management (2.x)
            '2.1' => '1. Membuat kalender observasi tematik
    2. Menugaskan safety officer khusus untuk observasi
    3. Membuat laporan tematik mingguan',
            
            '2.2' => '1. Implementasi sistem reminder maintenance
    2. Membuat checklist maintenance yang lebih detail
    3. Audit compliance maintenance bulanan',
            
            '2.3' => '1. Pasang GPS tracker untuk monitoring kecepatan
    2. Laporan pelanggaran kecepatan harian
    3. Training defensive driving khusus',
    
            // Program Keselamatan (3.x)
            '3.1' => '1. Membuat matrix pendampingan PJO
    2. Evaluasi kompetensi PJO bulanan
    3. Simulasi lapangan dengan scenario based training',
            
            '3.2' => '1. Standardisasi checklist inspeksi tools
    2. Kalibrasi tools secara berkala
    3. Sistem tagging tools yang sudah diinspeksi',
            
            '3.3' => '1. 5S implementation di workshop
    2. Daily housekeeping audit
    3. Zoning responsibility area',
    
            // Program Fire Prevention (4.x)
            '4.1' => '1. Digital tracking pencucian unit
    2. Sistem approval setelah pencucian
    3. CCTV monitoring area cuci',
            
            '4.2' => '1. Mobile app untuk inspeksi APAR
    2. Tagging QR code di tiap APAR
    3. Laporan kondisi APAR real-time',
    
            // Program Development (5.x)
            '5.1' => '1. Membuat project plan sertifikasi
    2. Audit gap analysis GL mitra
    3. Monthly progress review dengan manajemen',
            
            '5.2' => '1. Menyusun training matrix HRCP
    2. E-learning module pre-training
    3. Mandatory training sebelum deadline',
            
            '5.3' => '1. Training needs assessment
    2. Modul training plant specific
    3. Practical evaluation setelah training',
            
            '5.4' => '1. Jadwal review IBPR tahunan
    2. Pembentukan tim review multidepartemen
    3. Update dokumen maksimal 1 bulan setelah review',
            
            '5.5' => '1. Integrasi kalender evaluasi SMKP
    2. Template evaluasi yang terstandarisasi
    3. Action item tracking system',
            
            '5.6' => '1. Database pelanggaran terpusat
    2. Pembinaan berjenjang
    3. Konsekuensi yang konsisten',
    
            // Program Kesehatan (6.x)
            '6.1' => '1. Scheduling system MCU online
    2. Partner dengan klinik tambahan
    3. Tracking compliance per departemen',
            
            '6.2' => '1. Registry karyawan dengan kondisi khusus
    2. Medical buddy system
    3. Konseling kesehatan berkala',
    
            // Program Lingkungan (7.x)
            '7.1' => '1. Kalender kegiatan penghijauan
    2. Penugasan green team
    3. Dokumentasi progress bulanan',
            
            '7.2' => '1. Spill response protocol
    2. Spill kit di area kritis
    3. Pelatihan penanganan B3'
        ];
    
        return $actionMap[$kodeProgram] ?? '1. Analisis akar masalah\n2. Buat action plan perbaikan\n3. Monitoring implementasi';
    }

}