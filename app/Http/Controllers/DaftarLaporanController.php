<?php

namespace App\Http\Controllers;

use App\Models\DevelopmentManpower;
use App\Models\FatigueActivity;
use App\Models\FirePreventiveManagement;
use App\Models\InspeksiKendaraan;
use App\Models\KeselamatanAreaKerja;
use App\Models\ProgramKerjaKesehatan;
use App\Models\ProgramLingkunganHidup;

use Illuminate\Http\Request;

class DaftarLaporanController extends Controller
{



    public function index (){

        $KATEGORI_INSPEKSI = [
            'komisioning',
            'perawatan',
            'evaluasi_kecepatan'
        ];
    
        $KATEGORI_FATIGUE = [
            'ftw',
            'dfit',
            'fatigue_check',
            'wakeup_call',
            'saga',
            'sidak'
        ];

        $KATEGORI_KESELAMATAN = [
            'inspeksi',
            'gelar',
            'housekeeping'
        ];

        $kategoriDevelopment = DevelopmentManpower::KATEGORI_AKTIVITAS;
        $kategoriFatigue = $KATEGORI_FATIGUE;
        $kategoriFire = [FirePreventiveManagement::TYPE_PENCUCIAN_UNIT, FirePreventiveManagement::TYPE_INSPEKSI_APAR];
        $kategoriInspeksi = $KATEGORI_INSPEKSI;
        $kategoriKeselamatan = $KATEGORI_KESELAMATAN;
        $kategoriKesehatan = [ProgramKerjaKesehatan::MCU_TAHUNAN, ProgramKerjaKesehatan::PENYAKIT_KRONIS];
        $kategoriLingkungan = [ProgramLingkunganHidup::TYPE_KRIDA_AREA, ProgramLingkunganHidup::TYPE_PENGELOLAAN];


            $resultDev = $this->calculateCompletion(
                DevelopmentManpower::class,
                'kategori_aktivitas',
                DevelopmentManpower::KATEGORI_AKTIVITAS
            );

            $completionValueDev = $resultDev['completion'];


            $resultFire = $this->calculateCompletion(
                FirePreventiveManagement::class,
                'activity_type',
                [
                    FirePreventiveManagement::TYPE_PENCUCIAN_UNIT,
                    FirePreventiveManagement::TYPE_INSPEKSI_APAR
                ]
            );

            $completionValueFire = $resultFire['completion'];

            $resultInspeksi = $this->calculateCompletion(
                InspeksiKendaraan::class,
                'jenis_inspeksi',
                $kategoriInspeksi
            );

            $completionValueInspeksi = $resultInspeksi['completion'];   
        
            $resultKeselamatan = $this->calculateCompletion(
                KeselamatanAreaKerja::class,
                'activity_type',
                $KATEGORI_KESELAMATAN
            );

            $completionValueKeselamatan = $resultKeselamatan['completion'];
        
            $resultKesehatan = $this->calculateCompletion(
                ProgramKerjaKesehatan::class,
                'jenis_program',
                [
                    ProgramKerjaKesehatan::MCU_TAHUNAN, 
                    ProgramKerjaKesehatan::PENYAKIT_KRONIS
                ]
            );

            $completionValueKesehatan = $resultKesehatan['completion'];

            $resultLingkungan = $this->calculateCompletion(
                ProgramLingkunganHidup::class,
                'jenis_kegiatan',
                [
                    ProgramLingkunganHidup::TYPE_KRIDA_AREA, 
                    ProgramLingkunganHidup::TYPE_PENGELOLAAN
                ]
            );

            $completionValueLingkungan = $resultLingkungan['completion'];

            $resultFatigue = $this->calculateCompletion(
                FatigueActivity::class,
                'activity_type',
                $kategoriFatigue
            );

            $completionValueFatigue = $resultFatigue['completion'];


            // menghitung average dr complete
            $allCompletions = [
                $completionValueDev,
                $completionValueFire,
                $completionValueInspeksi,
                $completionValueKeselamatan,
                $completionValueKesehatan,
                $completionValueLingkungan,
                $completionValueFatigue
            ];

            $averageCompletion = count($allCompletions) > 0 
            ? round(array_sum($allCompletions) / count($allCompletions))
            : 0;


            // menghitung yang missing
            $results = [
                'DevelopmentManpower' => $resultDev,
                'FirePreventiveManagement' => $resultFire,
                'InspeksiKendaraan' => $resultInspeksi,
                'KeselamatanAreaKerja' => $resultKeselamatan,
                'ProgramKerjaKesehatan' => $resultKesehatan,
                'ProgramLingkunganHidup' => $resultLingkungan,
                'FatigueActivity' => $resultFatigue
            ];
            
            // Hitung total missing dari semua model
            $totalMissing = 0;
            $missingDetails = [];
            
            foreach ($results as $modelName => $result) {
                $missingCount = count($result['missing']);
                $totalMissing += $missingCount;
                
                // Simpan detail missing per model (opsional)
                $missingDetails[$modelName] = [
                    'count' => $missingCount,
                    'items' => $result['missing']
                ];
            }

        $reportData = [
            [
                // 'total' => DevelopmentManpower::count(),
                'total' => DevelopmentManpower::count(),
                'name' => 'Manpower Development',
                'route' => 'development-manpower.dashboard',
                'color' => 'info',
                'icon' => 'bi-people-fill',
                'desc' => 'Employee training and development',
                'completion' => $resultDev['completion'],
                // 'recent' => DevelopmentManpower::latest()->take(5)->get(),
                // 'pending' => DevelopmentManpower::where('status', 'pending')->count()
            ],
            [
                'total' => FatigueActivity::count(),
                'name' => 'Fatigue Preventive',
                'route' => 'fatigue-preventive.dashboard',
                'color' => 'primary',
                'icon' => 'bi-activity',
                'desc' => 'Monitor and prevent worker fatigue',
                'completion' => $resultFatigue['completion']
                // 'recent' => FatigueActivity::latest()->take(5)->get(),
                // 'high_risk' => FatigueActivity::where('risk_level', 'high')->count()
            ],
            [
                'total' => FirePreventiveManagement::count(),
                'name' => 'Fire Prevention',
                'route' => 'fire-preventive.dashboard',
                'color' => 'danger',
                'icon' => 'bi-fire',
                'desc' => 'Fire prevention and management',
                'completion' => $resultFire['completion']
                // 'recent' => FirePreventiveManagement::latest()->take(5)->get(),
                // 'expired' => FirePreventiveManagement::where('expiry_date', '<', now())->count()
            ],
            [
                'total' => InspeksiKendaraan::count(),
                'name' => 'Traffic Management',
                'route' => 'inspeksi.dashboard',
                'color' => 'success',
                'icon' => 'bi-check-circle',
                'desc' => 'Vehicle and traffic safety management',
                'completion' => $resultInspeksi['completion']
                // 'recent' => InspeksiKendaraan::latest()->take(5)->get(),
                // 'need_attention' => InspeksiKendaraan::where('status', 'need_repair')->count()
            ],
            [
                'total' => KeselamatanAreaKerja::count(),
                'name' => 'Workplace Safety',
                'route' => 'keselamatan.dashboard',
                'color' => 'warning',
                'icon' => 'bi-shield-check',
                'desc' => 'Work area safety programs',
                'completion' => $resultKeselamatan['completion']
                // 'recent' => KeselamatanAreaKerja::latest()->take(5)->get(),
                // 'open_issues' => KeselamatanAreaKerja::where('is_closed', false)->count()
            ],
            [
                'total' => ProgramKerjaKesehatan::count(),
                'name' => 'Health Program',
                'route' => 'program-kesehatan.dashboard',
                'color' => 'secondary',
                'icon' => 'bi-heart-pulse',
                'desc' => 'Employee health initiatives',
                'completion' => $resultKesehatan['completion']
                // 'recent' => ProgramKerjaKesehatan::latest()->take(5)->get(),
                // 'active' => ProgramKerjaKesehatan::where('is_active', true)->count()
            ],
            [
                'total' => ProgramLingkunganHidup::count(),
                'name' => 'Environment Program',
                'route' => 'program-lingkungan.dashboard',
                'color' => 'dark',
                'icon' => 'bi-tree-fill',
                'desc' => 'Environmental management',
                'completion' => $resultLingkungan['completion']
                // 'recent' => ProgramLingkunganHidup::latest()->take(5)->get(),
                // 'ongoing' => ProgramLingkunganHidup::where('status', 'ongoing')->count()
            ],
        ];

        $summary = [
            [
                'total_all' => DevelopmentManpower::count() + FatigueActivity::count() + 
                              FirePreventiveManagement::count() + InspeksiKendaraan::count() + 
                              KeselamatanAreaKerja::count() + ProgramKerjaKesehatan::count() + 
                              ProgramLingkunganHidup::count(),
                'average' => $averageCompletion,
                'missing' => $totalMissing,
                'total_program' => count($reportData)
                // 'last_updated' => now()->format('d F Y H:i')
            ]
        ];

        // dd($reportData);

        return view('daftarlaporan.daftarpengguna', compact('reportData', 'summary'));
    }





    protected function calculateCompletion($model, $fieldName, $kategoriConstants)
    {
        $totalKategori = count($kategoriConstants);
        $existingKategori = $model::select($fieldName)
            ->distinct()
            ->pluck($fieldName)
            ->toArray();
    
        $matchedKategori = array_intersect($kategoriConstants, $existingKategori);
        $completion = ($totalKategori > 0) ? round(count($matchedKategori) / $totalKategori * 100) : 0;
    
        return [
            'total_kategori' => $totalKategori,
            'terisi' => count($matchedKategori),
            'completion' => $completion,
            'missing' => array_diff($kategoriConstants, $existingKategori)
        ];
    }
}
