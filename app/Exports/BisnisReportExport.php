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
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class BisnisReportExport implements WithMultipleSheets
{
    protected $mitraId;
    protected $startDate;
    protected $endDate;

    public function __construct($mitraId, $startDate, $endDate)
    {
        $this->mitraId = $mitraId;
        $this->startDate = Carbon::parse($startDate);
        $this->endDate = Carbon::parse($endDate);
    }

    public function sheets(): array
    {
        return [
            'Summary' => new BisnisSummarySheet($this->mitraId, $this->startDate, $this->endDate),
            'Detail' => new BisnisDetailSheet($this->mitraId, $this->startDate, $this->endDate),
        ];
    }
}

class BisnisSummarySheet implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $mitraId;
    protected $startDate;
    protected $endDate;
    protected $userCount;

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
            FatigueActivity::TYPE_SIDAK => '1.6'
        ];

        $descriptions = [
            FatigueActivity::TYPE_FTW => 'Fit to Work di Awal Shift',
            FatigueActivity::TYPE_DFIT => 'Evaluasi D Fit (Operator DT)',
            FatigueActivity::TYPE_FATIGUE_CHECK => 'Fatigue Check/Streaching Operational DT di Jam Kritis',
            FatigueActivity::TYPE_WAKEUP_CALL => 'Wake Up Call Operator A2B diluar Jam Kritis by Radio/Voice + Form',
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

class BisnisDetailSheet implements FromCollection, WithHeadings, WithStyles
{
    protected $mitraId;
    protected $startDate;
    protected $endDate;
    protected $userCount;

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

            $row['pencapaian_total'] = $monthly['pencapaian'].'%';
            $row['average_monthly'] = $average.'%';

            // Add placeholders
            for ($i = 1; $i <= 4; $i++) {
                $row['week'.$i.'_placeholder'] = '#UNKNOWN!';
            }

            $data->push($row);
        }

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

        return $headings;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
            'A:Z' => ['alignment' => ['vertical' => 'center']],
        ];
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

        return $monthlyData;
    }
}
