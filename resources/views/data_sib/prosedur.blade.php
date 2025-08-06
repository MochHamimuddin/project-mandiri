{{-- resources/views/home.blade.php --}}
@extends('layout.index')

@section('content')
<div class="row mb-4">
  <div class="col-12 position-relative">
    <img src="https://www.kppmining.com/assets/images/kpp-home-banner.png" alt="KPP Banner"
         class="img-fluid rounded-3 shadow-sm w-100"
         style="max-height: 300px; object-fit: cover; filter: brightness(0.7);">

    <!-- Perubahan di sini: justify-content-start -> justify-content-end dan tambah padding-bottom -->
    <div class="position-absolute top-0 start-0 w-100 h-100 d-flex flex-column justify-content-end align-items-start p-4 pb-5">
      <h2 class="text-white mb-2 fw-bold">High Risk Activity</h2>
      <p class="text-white mb-3">Standard Operasional Prosedur</p>
      <a href="/data-sib/create" class="btn btn-success mt-2">Pengajuan SIB HRA</a>
    </div>
  </div>
</div>

<style>
  .position-relative {
    position: relative;
  }
  .position-absolute {
    position: absolute;
  }
  /* Optional: Tambahkan overlay semi-transparent untuk meningkatkan keterbacaan teks */
  .position-absolute::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 0;
    border-radius: 0.3rem;
  }
  .position-absolute > * {
    position: relative;
    z-index: 1;
  }
</style>

<div class="container my-5">
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="card-title text-primary mb-4 border-bottom pb-2">Prosedur High Risk Activity</h2>

            <div class="accordion" id="proceduresAccordion">
                <!-- Dumping & Loading -->
                <div class="card mb-3">
                    <div class="card-header bg-light" id="headingOne">
                        <h3 class="mb-0">
                            <button class="btn btn-link text-info font-weight-bold w-100 text-left d-flex justify-content-between align-items-center" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                <span><i class="fas fa-clipboard-check mr-2"></i>Prosedur Dumping & Loading HRA</span>
                                <i class="fas fa-chevron-down"></i>
                            </button>
                        </h3>
                    </div>
                    <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#proceduresAccordion">
                        <div class="card-body">
                            <p>Dumping di disposal High Risk (parameter: air kedalam ≥ 1 meter, lumpur, dan ketinggian ≥ 5 meter).</p>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">Memastikan area dumping telah diperiksa/diinspeksi dan dinyatakan aman</li>
                                <li class="list-group-item">Menyediakan semua Alat Pelindung Diri yang akan digunakan.</li>
                                <li class="list-group-item">Menyiapkan alat keselamatan lainnya dan telah dinyatakan aman.</li>
                                <li class="list-group-item">Melakukan pemeriksaan alat bantu lainnya dan dinyatakan aman.</li>
                                <li class="list-group-item">Menyediakan alat radio komunikasi dan berfungsi dengan baik.</li>
                                <li class="list-group-item">Memastikan semua karyawan yang terlibat sudah terlatih dan mengerti Instruksi Kerja dumping disposal high risk.</li>
                                <li class="list-group-item">Memastikan terdapat pengawas yang melakukan pengawasan di lokasi kerja.</li>
                                <li class="list-group-item">mengubah semua unit/alat yang digunakan sudah lulus commissioning.</li>
                                <li class="list-group-item">Memastikan semua unit / alat yang digunakan dilakukan P2H dan dinyatakan aman.</li>
                                <li class="list-group-item">Memastikan semua operator memiliki SIMPER / sertifikat kompetensi sesuai dengan pengoperasian unit yang dioperasikan.</li>
                                <li class="list-group-item">Memastikan Area dumping high risk telah bebas dari bahaya-bahaya lainnya yang tidak terkait dengan pekeriaan</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Peledakan -->
                <div class="card mb-3">
                    <div class="card-header bg-light" id="headingTwo">
                        <h3 class="mb-0">
                            <button class="btn btn-link text-info font-weight-bold w-100 text-left d-flex justify-content-between align-items-center collapsed" type="button" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                <span><i class="fas fa-tools mr-2"></i>Prosedur Aktifitas Peledakan</span>
                                <i class="fas fa-chevron-down"></i>
                            </button>
                        </h3>
                    </div>
                    <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#proceduresAccordion">
                        <div class="card-body">
                            <p>Aktifitas peledakan (parameter: sleep blast, dekat pemukiman / masyarakat < 500 meter, dan dekat alat < 300 meter, dekat area rawan longsor, dan peledakan di area terdapat gas metan / batuan panas).</p>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">Memastikan telah terdapat SOP/JSA pekerjaan aktifitas peledakan High Risk.</li>
                                <li class="list-group-item">Memastikan area peledakan telah diperiksa/diinspeksi dan dinyatakan aman.</li>
                                <li class="list-group-item">Menyediakan semua Alat Pelindung Diri yang akan digunakan.</li>
                                <li class="list-group-item">Menyiapkan alat keselamatan lainnya dan telah dinyatakan aman.</li>
                                <li class="list-group-item">Melakukan pemeriksaan alat bantu lainnya dan dinyatakan aman.</li>
                                <li class="list-group-item">Menyediakan alat radio komunikasi dan berfungsi dengan baik.</li>
                                <li class="list-group-item">Memastikan semua karyawan yang terlibat sudah terlatih dan mengerti Instruksi Kerja peledakan high risk.</li>
                                <li class="list-group-item">Memastikan pekerja dengan keahlian khusus peledakan memiliki sertifikat kompetensi.</li>
                                <li class="list-group-item">mengubah semua unit/alat yang digunakan sudah lulus commissioning.</li>
                                <li class="list-group-item">Memastikan semua unit / alat yang digunakan dilakukan P2H dan dinyatakan aman.</li>
                                <li class="list-group-item">Memastikan semua operator memiliki SIMPER / sertifikat kompetensi sesuai dengan pengoperasian unit yang dioperasikan.</li>
                                <li class="list-group-item">Memastikan Area peledakan high risk telah bebas dari bahaya-bahaya lainnya yang tidak terkait dengan pekerjaan peledakan.</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Ketinggian -->
                <div class="card mb-3">
                    <div class="card-header bg-light" id="headingThree">
                        <h3 class="mb-0">
                            <button class="btn btn-link text-info font-weight-bold w-100 text-left d-flex justify-content-between align-items-center collapsed" type="button" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                <span><i class="fas fa-shield-alt mr-2"></i>Prosedur Bekerja di Ketinggian</span>
                                <i class="fas fa-chevron-down"></i>
                            </button>
                        </h3>
                    </div>
                    <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#proceduresAccordion">
                        <div class="card-body">
                            <p>Bekerja di ketinggian ≥ 1.8 meter</p>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">Memastikan telah terdapat SOP/JSA pekerjaan aktifitas bekerja diketinggian.</li>
                                <li class="list-group-item">Memastikan area kerja telah diperiksa/diinspeksi dan dinyatakan aman.</li>
                                <li class="list-group-item">Menyediakan semua Alat Pelindung Diri yang akan digunakan.</li>
                                <li class="list-group-item">Menyiapkan alat keselamatan lainnya dan telah dinyatakan aman.</li>
                                <li class="list-group-item">Melakukan pemeriksaan alat bantu lainnya dan dinyatakan aman.</li>
                                <li class="list-group-item">Menyediakan alat radio komunikasi dan berfungsi dengan baik.</li>
                                <li class="list-group-item">Memastikan semua karyawan yang terlibat sudah terlatih dan mengerti Instruksi pekerjaan ketinggian.</li>
                                <li class="list-group-item">Memastikan pekerja dengan keahlian khusus untuk naik di ketinggian telah memiliki sertifikat-kompetensi bekeja di ketinggian.</li>
                                <li class="list-group-item">Memastikan terdapat pengawas yang melakukan pengawasan di lokasi kerja.</li>
                                <li class="list-group-item">Memastikan Area pekerjaan di ketinggian telah bebas dari bahaya-bahaya lainnya yang tidak terkait dengan pekerjaan ketinggian.</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Dekat Air -->
                <div class="card mb-3">
                    <div class="card-header bg-light" id="headingFour">
                        <h3 class="mb-0">
                            <button class="btn btn-link text-info font-weight-bold w-100 text-left d-flex justify-content-between align-items-center collapsed" type="button" data-toggle="collapse" data-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                <span><i class="fas fa-shield-alt mr-2"></i>Prosedur Bekerja di Dekat Air</span>
                                <i class="fas fa-chevron-down"></i>
                            </button>
                        </h3>
                    </div>
                    <div id="collapseFour" class="collapse" aria-labelledby="headingFour" data-parent="#proceduresAccordion">
                        <div class="card-body">
                            <p>Bekerja di dekat air (kedalaman ≥ 1 meter) dan area sump terkait install dan uninstall pompa/pipa/walkway.</p>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">Ternyata telah terdapat SOP / JSA yang bekerja di dekat air.</li>
                                <li class="list-group-item">Memastikan akses jalan dan area kerja telah diperiksa/diinspeksi dan dinyatakan aman.</li>
                                <li class="list-group-item">Menyediakan semua Alat Pelindung Diri yang akan digunakan.</li>
                                <li class="list-group-item">Menyiapkan alat keselamatan lainnya dan telah dinyatakan aman.</li>
                                <li class="list-group-item">Melakukan pemeriksaan alat bantu lainnya dan dinyatakan aman.</li>
                                <li class="list-group-item">Menyediakan alat radio komunikasi dan berfungsi dengan baik.</li>
                                <li class="list-group-item">Memastikan semua karyawan yang terlibat sudah terlatih dan mengerti Instruksi Kerja bekerja didekat air.</li>
                                <li class="list-group-item">Memastikan terdapat pengawas yang melakukan pengawasan di lokasi kerja.</li>
                                <li class="list-group-item">Memastikan area pekerjaan telah bebas dari bahaya-bahaya lainnya yang tidak terkait dengan pekerjaan didekat air.</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Kelistrikan -->
                <div class="card mb-3">
                    <div class="card-header bg-light" id="headingFive">
                        <h3 class="mb-0">
                            <button class="btn btn-link text-info font-weight-bold w-100 text-left d-flex justify-content-between align-items-center collapsed" type="button" data-toggle="collapse" data-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                                <span><i class="fas fa-shield-alt mr-2"></i>Prosedur Bekerja Kelistrikan >380 V</span>
                                <i class="fas fa-chevron-down"></i>
                            </button>
                        </h3>
                    </div>
                    <div id="collapseFive" class="collapse" aria-labelledby="headingFive" data-parent="#proceduresAccordion">
                        <div class="card-body">
                            <p>Bekerja dengan instalasi listrik ≥ 380 V.</p>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">Memastikan telah terdapat SOP / JSA pekerjaan listrik.</li>
                                <li class="list-group-item">Memastikan area kerja telah diperiksa/diinspeksi dan dinyatakan aman.</li>
                                <li class="list-group-item">Menyediakan semua Alat Pelindung Diri yang akan digunakan.</li>
                                <li class="list-group-item">Menyiapkan alat keselamatan lainnya dan telah dinyatakan aman.</li>
                                <li class="list-group-item">Melakukan pemeriksaan alat bantu lainnya dan dinyatakan aman.</li>
                                <li class="list-group-item">Menyediakan alat radio komunikasi dan berfungsi dengan baik.</li>
                                <li class="list-group-item">Memastikan semua karyawan yang terlibat sudah terlatih dan mengerti Instruksi Kerja pekerjaan instalasi listrik.</li>
                                <li class="list-group-item">Memastikan pekerja dengan keahlian khusus yang menangani kelistrikan memiliki sertifikat kompetensi yang sesuai.</li>
                                <li class="list-group-item">Memastikan terdapat pengawas yang melakukan pengawasan di lokasi kerja.</li>
                                <li class="list-group-item">Memastikan Area kerja telah bebas dari bahaya-bahaya lainnya yang tidak terkait dengan pekerjaan kelistrikan.</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Pengangkatan/Lifting -->
                <div class="card mb-3">
                    <div class="card-header bg-light" id="headingSix">
                        <h3 class="mb-0">
                            <button class="btn btn-link text-info font-weight-bold w-100 text-left d-flex justify-content-between align-items-center collapsed" type="button" data-toggle="collapse" data-target="#collapseSix" aria-expanded="false" aria-controls="collapseSix">
                                <span><i class="fas fa-shield-alt mr-2"></i>Prosedur Pengangkatan/Lifting</span>
                                <i class="fas fa-chevron-down"></i>
                            </button>
                        </h3>
                    </div>
                    <div id="collapseSix" class="collapse" aria-labelledby="headingSix" data-parent="#proceduresAccordion">
                        <div class="card-body">
                            <p>Pengangkatan (lifting) (parameter: beban 25 ton, di dekat atas air, mengangkat manusia/mahluk hidup ≥ 5 meter, pengangkatan menggunakan 2 crane, beban dimensi besar (misal ponton pompa))</p>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">Pemilihan telah terdapat SOP/JSA pekerjaan pengangkatan.</li>
                                <li class="list-group-item">Memastikan area kerja telah diperiksa/diinspeksi dan dinyatakan aman.</li>
                                <li class="list-group-item">Menyediakan semua Alat Pelindung Diri yang akan digunakan.</li>
                                <li class="list-group-item">Menyiapkan alat keselamatan lainnya dan telah dinyatakan aman.</li>
                                <li class="list-group-item">Melakukan pemeriksaan alat bantu lainnya dan dinyatakan aman.</li>
                                <li class="list-group-item">Melakukan alat radio komunikasi berfungsi dengan baik.</li>
                                <li class="list-group-item">Memastikan semua karyawan yang terlibat sudah terlatih dan mengerti Instruksi Kerja pengangkatan.</li>
                                <li class="list-group-item">Memastikan pekerja dengan keahlian khusus memandu sebagai rigger telah memiliki sertifikat kompetensi Rigger.</li>
                                <li class="list-group-item">Memastikan terdapat pengawas yang melakukan pengawasan di lokasi kerja.</li>
                                <li class="list-group-item">Memastikan semua unit/alat yang digunakan sudah lulus commissioning.</li>
                                <li class="list-group-item">Memastikan semua unit / alat yang digunakan dilakukan P2H dan dinyatakan aman.</li>
                                <li class="list-group-item">Memastikan operator alat angkat memiliki SIMPER dan SIO sesuai dengan pengoperasian unit yang dioperasikan.</li>
                                <li class="list-group-item">Memastikan Area kerja telah bebas dari bahaya-bahaya lainnya yang tidak terkait dengan pekerjaan pengangkatan.</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Ruang Terbatas -->
                <div class="card mb-3">
                    <div class="card-header bg-light" id="headingSeven">
                        <h3 class="mb-0">
                            <button class="btn btn-link text-info font-weight-bold w-100 text-left d-flex justify-content-between align-items-center collapsed" type="button" data-toggle="collapse" data-target="#collapseSeven" aria-expanded="false" aria-controls="collapseSeven">
                                <span><i class="fas fa-shield-alt mr-2"></i>Prosedur Bekerja di Ruang Terbatas</span>
                                <i class="fas fa-chevron-down"></i>
                            </button>
                        </h3>
                    </div>
                    <div id="collapseSeven" class="collapse" aria-labelledby="headingSeven" data-parent="#proceduresAccordion">
                        <div class="card-body">
                            <p>Bekerja di ruang terbatas (confined space)</p>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">Memastikan telah terdapat SOP / JSA yang bekerja di ruang terbatas.</li>
                                <li class="list-group-item">Memastikan area kerja telah diperiksa/diinspeksi dan dinyatakan aman.</li>
                                <li class="list-group-item">Menyediakan semua Alat Pelindung Diri yang akan digunakan.</li>
                                <li class="list-group-item">Menyiapkan alat keselamatan lainnya dan telah dinyatakan aman.</li>
                                <li class="list-group-item">Melakukan pemeriksaan alat bantu lainnya dan dinyatakan aman.</li>
                                <li class="list-group-item">Menyediakan alat radio komunikasi dan berfungsi dengan baik.</li>
                                <li class="list-group-item">Memastikan semua karyawan yang terlibat sudah terlatih dan mengerti Instruksi Kerja bekerja di ruang terbatas.</li>
                                <li class="list-group-item">Memastikan pekerja dengan keahlian khusus (Welder) dan lainnya telah memiliki sertifikat kompetensi yang sesuai.</li>
                                <li class="list-group-item">Memastikan terdapat pengawas yang melakukan pengawasan di lokasi kerja.</li>
                                <li class="list-group-item">Memastikan telah dilakukan pengukuran gas dengan gas detector dan dinyatakan masuk dalam batas aman (Combustable < 5 LEL, Oksigen 19,5% 23,5%, Hydrogen Sulphide < 10 ppm, CO < 30 ppm.</li>
                                <li class="list-group-item">Memastikan Area kerja telah bebas dari bahaya-bahaya lainnya yang tidak terkait dengan pekerjaan di ruang terbatas.</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Tebing Rawan Longsor -->
                <div class="card mb-3">
                    <div class="card-header bg-light" id="headingEight">
                        <h3 class="mb-0">
                            <button class="btn btn-link text-info font-weight-bold w-100 text-left d-flex justify-content-between align-items-center collapsed" type="button" data-toggle="collapse" data-target="#collapseEight" aria-expanded="false" aria-controls="collapseEight">
                                <span><i class="fas fa-shield-alt mr-2"></i>Prosedur Bekerja di Dekat/Bawah Tebing Rawan Longsor (FK < 1.3)</span>
                                <i class="fas fa-chevron-down"></i>
                            </button>
                        </h3>
                    </div>
                    <div id="collapseEight" class="collapse" aria-labelledby="headingEight" data-parent="#proceduresAccordion">
                        <div class="card-body">
                            <p>Bekerja di dekat tebing / lereng yang rawan longsor (FK < 1,3).</p>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">Memastikan telah terdapat SOP / JSA bekerja di tebing dekat.</li>
                                <li class="list-group-item">Memastikan area kerja telah diperiksa/diinspeksi dan dinyatakan aman.</li>
                                <li class="list-group-item">Menyediakan semua Alat Pelindung Diri yang akan digunakan.</li>
                                <li class="list-group-item">Menyiapkan alat keselamatan lainnya dan telah dinyatakan aman.</li>
                                <li class="list-group-item">Melakukan pemeriksaan alat bantu lainnya dan dinyatakan aman.</li>
                                <li class="list-group-item">Menyediakan alat radio komunikasi dan berfungsi dengan baik.</li>
                                <li class="list-group-item">Memastikan semua karyawan yang terlibat sudah terlatih dan mengerti Instruksi Kerja bekerja di dekat tebing.</li>
                                <li class="list-group-item">Memastikan terdapat pengawas yang melakukan pengawasan di lokasi kerja.</li>
                                <li class="list-group-item">Memastikan Area kerja telah bebas dari bahaya-bahaya lainnya yang tidak terkait dengan pekerjaan di dekat tebing.</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Tyre OHT -->
                <div class="card mb-3">
                    <div class="card-header bg-light" id="headingNine">
                        <h3 class="mb-0">
                            <button class="btn btn-link text-info font-weight-bold w-100 text-left d-flex justify-content-between align-items-center collapsed" type="button" data-toggle="collapse" data-target="#collapseNine" aria-expanded="false" aria-controls="collapseNine">
                                <span><i class="fas fa-shield-alt mr-2"></i>Prosedur Pelepasan dan Pemasangan Tyre OHT di Jalan Tambang</span>
                                <i class="fas fa-chevron-down"></i>
                            </button>
                        </h3>
                    </div>
                    <div id="collapseNine" class="collapse" aria-labelledby="headingNine" data-parent="#proceduresAccordion">
                        <div class="card-body">
                            <p>Penggantian tyre unit HD/Articulated di jalur aktif tambang/hauling yang tidak bisa dibawa ke Workshop.</p>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">Memastikan telah terdapat SOP / JSA pekerjaan penggantian ban Berisiko Tinggi.</li>
                                <li class="list-group-item">Memastikan area kerja telah diperiksa dan dinyatakan aman Menyediakan semua Alat Pelindung Diri yang akan digunakan</li>
                                <li class="list-group-item">Menyiapkan alat keselamatan lainnya dan telah dinyatakan aman.</li>
                                <li class="list-group-item">Melakukan pemeriksaan alat bantu lainnya dan dinyatakan aman.</li>
                                <li class="list-group-item">Menyediakan alat radio komunikasi dan berfungsi dengan baik.</li>
                                <li class="list-group-item">Memastikan semua karyawan yang terlibat sudah terlatih dan mengerti Instruksi Kerja penggantian tyre unit.</li>
                                <li class="list-group-item">Memastikan terdapat pengawas yang melakukan pengawasan di lokasi kerja.</li>
                                <li class="list-group-item">Memastikan Area kerja telah bebas dari bahaya-bahaya lainnya yang tidak terkait dengan pekerjaan penggantian tyre unit HD/Articulated.</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Eksplorasi Area Kritis -->
                <div class="card mb-3">
                    <div class="card-header bg-light" id="headingTen">
                        <h3 class="mb-0">
                            <button class="btn btn-link text-info font-weight-bold w-100 text-left d-flex justify-content-between align-items-center collapsed" type="button" data-toggle="collapse" data-target="#collapseTen" aria-expanded="false" aria-controls="collapseTen">
                                <span><i class="fas fa-shield-alt mr-2"></i>Prosedur Pekerjaan Eksplorasi Area Kritis</span>
                                <i class="fas fa-chevron-down"></i>
                            </button>
                        </h3>
                    </div>
                    <div id="collapseTen" class="collapse" aria-labelledby="headingTen" data-parent="#proceduresAccordion">
                        <div class="card-body">
                            <p>Pekerjaan eksplorasi di area kritis (Area dekat tebing, area terpencil, area dekat lalulintas unit, area dekat sungai, area didekat pohon rawan tumbang).</p>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">Memastikan telah terdapat SOP / JSA pekerjaan eksplorasi.</li>
                                <li class="list-group-item">Memastikan area kerja telah diperiksa/diinspeksi dan dinyatakan aman.</li>
                                <li class="list-group-item">Menyediakan semua Alat Pelindung Diri yang akan digunakan.</li>
                                <li class="list-group-item">Menyiapkan alat keselamatan lainnya dan telah dinyatakan aman.</li>
                                <li class="list-group-item">Melakukan pemeriksaan alat bantu lainnya dan dinyatakan aman.</li>
                                <li class="list-group-item">Menyediakan alat radio komunikasi dan berfungsi dengan baik.</li>
                                <li class="list-group-item">Memastikan semua karyawan yang terlibat sudah terlatih dan mengerti Instruksi Kerja pekerjaan eksplorasi.</li>
                                <li class="list-group-item">Memastikan pekerja dengan keahlian khusus (operator drilling) dan lainnya telah memiliki kompetensi yang sesuai.</li>
                                <li class="list-group-item">Memastikan terdapat pengawas yang melakukan pengawasan di lokasi kerja.</li>
                                <li class="list-group-item">Memastikan Area kerja telah bebas dari bahaya-bahaya lainnya yang tidak terkait dengan pekeriaan eksplorasi.</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Land Clearing -->
                <div class="card mb-3">
                    <div class="card-header bg-light" id="headingEleven">
                        <h3 class="mb-0">
                            <button class="btn btn-link text-info font-weight-bold w-100 text-left d-flex justify-content-between align-items-center collapsed" type="button" data-toggle="collapse" data-target="#collapseEleven" aria-expanded="false" aria-controls="collapseEleven">
                                <span><i class="fas fa-shield-alt mr-2"></i>Prosedur Aktifitas Land Clearing</span>
                                <i class="fas fa-chevron-down"></i>
                            </button>
                        </h3>
                    </div>
                    <div id="collapseEleven" class="collapse" aria-labelledby="headingEleven" data-parent="#proceduresAccordion">
                        <div class="card-body">
                            <p>Aktifitas Land Clearing (brushing, cutting, grubbing) (parameter hutan original dan kemiringan >45°) atau di luar area land clearing (parameter: dekat bangunan, Office, Workshop, Warehouse, Mess)</p>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">Memastikan telah terdapat SOP / JSA pekerjaan cutting pohon di area High Risk.</li>
                                <li class="list-group-item">Memastikan area kerja telah diperiksa/diinspeksi dan dinyatakan aman.</li>
                                <li class="list-group-item">Menyediakan semua Alat Pelindung Diri yang akan digunakan.</li>
                                <li class="list-group-item">Menyiapkan alat keselamatan lainnya dan telah dinyatakan aman.</li>
                                <li class="list-group-item">Melakukan pemeriksaan alat bantu lainnya dan dinyatakan aman.</li>
                                <li class="list-group-item">Menyediakan alat radio komunikasi dan berfungsi dengan baik.</li>
                                <li class="list-group-item">Memastikan semua karyawan yang terlibat sudah terlatih dan mengerti Instruksi Kerja pekerjaan cutting pohon.</li>
                                <li class="list-group-item">Memastikan pekerja dengan keahlian khusus (Chainsaw man) dan telah memiliki sertifikat kompetensi yang sesuai.</li>
                                <li class="list-group-item">Memastikan terdapat pengawas yang melakukan pengawasan di lokasi kerja.</li>
                                <li class="list-group-item">Memastikan Area kerja telah bebas dari bahaya-bahaya lainnya yang tidak terkait dengan pekerjaan cutting pohon.</li>
                                <li class="list-group-item">Aktifitas cutting pohon dilakukan jika pohon mrmiliki diameter >20 cm. Untuk pohon dengan lebar diameter <20 cm dapat dilakukan menggunakan metode brushing.</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Maintenance Conveyor -->
                <div class="card mb-3">
                    <div class="card-header bg-light" id="headingTwelve">
                        <h3 class="mb-0">
                            <button class="btn btn-link text-info font-weight-bold w-100 text-left d-flex justify-content-between align-items-center collapsed" type="button" data-toggle="collapse" data-target="#collapseTwelve" aria-expanded="false" aria-controls="collapseTwelve">
                                <span><i class="fas fa-shield-alt mr-2"></i>Prosedur Maintenance Conveyor</span>
                                <i class="fas fa-chevron-down"></i>
                            </button>
                        </h3>
                    </div>
                    <div id="collapseTwelve" class="collapse" aria-labelledby="headingTwelve" data-parent="#proceduresAccordion">
                        <div class="card-body">
                            <p>Maintenance Konveyor (parameter install dan uninstall conveyor belt, head drum dam crusher).</p>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">Melihat telah terdapat SOP / JSA pekerjaan maintenance conveyor.</li>
                                <li class="list-group-item">Memastikan area kerja telah diperiksa/diinspeksi dan dinyatakan aman.</li>
                                <li class="list-group-item">Menyediakan semua Alat Pelindung Diri yang akan digunakan</li>
                                <li class="list-group-item">Menyiapkan alat keselamatan lainnya dan telah dinyatakan aman</li>
                                <li class="list-group-item">Melakukan pemeriksaan alat bantu lainnya dan dinyatakan aman.</li>
                                <li class="list-group-item">Menyediakan alat radio komunikasi dan berfungsi dengan baik.</li>
                                <li class="list-group-item">Memastikan semua karyawan yang terlibat sudah terlatih dan mengerti Instruksi Kerja pekerjaan maintenance conveyor dan Instruksi Kerja isolasi energi.</li>
                                <li class="list-group-item">Memastikan terdapat pengawas yang melakukan pengawasan di lokasi kerja.</li>
                                <li class="list-group-item">Memastikan Area kerja telah bebas dari bahaya-bahaya lainnya yang tidak terkait dengan pekerjaan maintenance conveyor.</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Penggalian Bangunan -->
                <div class="card mb-3">
                    <div class="card-header bg-light" id="headingThirteen">
                        <h3 class="mb-0">
                            <button class="btn btn-link text-info font-weight-bold w-100 text-left d-flex justify-content-between align-items-center collapsed" type="button" data-toggle="collapse" data-target="#collapseThirteen" aria-expanded="false" aria-controls="collapseThirteen">
                                <span><i class="fas fa-shield-alt mr-2"></i>Prosedur Penggalian/Gangguan di Sekitar Bangunan</span>
                                <i class="fas fa-chevron-down"></i>
                            </button>
                        </h3>
                    </div>
                    <div id="collapseThirteen" class="collapse" aria-labelledby="headingThirteen" data-parent="#proceduresAccordion">
                        <div class="card-body">
                            <p>Penggalian / gangguan tanah disekitar bangunan (parameter: Office, Mess, Workshop, Warehouse dan bangunan lainnya yang terdapat instalasi air, listrik, jaringan komunikasi dan gas).</p>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">Memastikan telah terdapat SOP / JSA pekerjaan penggalian / gangguan tanah disekitar bangunan.</li>
                                <li class="list-group-item">Memastikan terdapat peta jalur kabel listrik, jalur pepipaan dan lainnya di area lokasi pekerjaan dan disampaikan oleh PIC kelistrikan / perpipaan kepada pengawas dan operator A2B.</li>
                                <li class="list-group-item">Memastikan area kerja telah diperiksa/diinspeksi dan dinyatakan aman dari jalur kabel listrik, pipa air dan lainnya.</li>
                                <li class="list-group-item">Menyediakan semua Alat Pelindung Diri yang akan digunakan.</li>
                                <li class="list-group-item">Menyiapkan alat keselamatan lainnya dan telah dinyatakan aman.</li>
                                <li class="list-group-item">Melakukan pemeriksaan alat bantu lainnya dan dinyatakan aman.</li>
                                <li class="list-group-item">Menyediakan alat radio komunikasi dan berfungsi dengan baik.</li>
                                <li class="list-group-item">Memastikan semua karyawan yang terlibat sudah terlatih dan mengerti Instruksi Kerja pekerjaan penggalian.</li>
                                <li class="list-group-item">Memastikan terdapat pengawas yang melakukan pengawasan di lokasi kerja.</li>
                                <li class="list-group-item">Memastikan semua unit/alat yang digunakan sudah lulus commissioning.</li>
                                <li class="list-group-item">Memastikan semua unit / alat yang digunakan dilakukan P2H dan dinyatakan aman.</li>
                                <li class="list-group-item">Memastikan operator A2B memiliki SIMPER sesuai dengan unit yang dioperasikan</li>
                                <li class="list-group-item">Memastikan Area kerja telah bebas dari bahaya-bahaya lainnya yang tidak terkait dengan pekerjaan penggalian</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Pengelasan -->
                <div class="card mb-3">
                    <div class="card-header bg-light" id="headingFourteen">
                        <h3 class="mb-0">
                            <button class="btn btn-link text-info font-weight-bold w-100 text-left d-flex justify-content-between align-items-center collapsed" type="button" data-toggle="collapse" data-target="#collapseFourteen" aria-expanded="false" aria-controls="collapseFourteen">
                                <span><i class="fas fa-shield-alt mr-2"></i>Prosedur Aktifitas Pengelasan Bahan Mudah Terbakar</span>
                                <i class="fas fa-chevron-down"></i>
                            </button>
                        </h3>
                    </div>
                    <div id="collapseFourteen" class="collapse" aria-labelledby="headingFourteen" data-parent="#proceduresAccordion">
                        <div class="card-body">
                            <p>Melakukan pengelasan (parameter: pengelasan yang terdapat atau dekat bahan y mudah meledak/terbakar atau pekerjaan yang diluar area workshop yang tidak diranc untuk pekerjaan panas).</p>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">Memastikan telah terdapat SOP / JSA pekerjaan pengelasan.</li>
                                <li class="list-group-item">Memastikan area kerja telah diperiksa/diinspeksi dan dinyatakan aman.</li>
                                <li class="list-group-item">Menyediakan semua Alat Pelindung Diri yang akan digunakan.</li>
                                <li class="list-group-item">Menyiapkan alat keselamatan lainnya dan telah dinyatakan aman.</li>
                                <li class="list-group-item">Melakukan pemeriksaan alat bantu lainnya dan dinyatakan aman.</li>
                                <li class="list-group-item">Menyediakan alat radio komunikasi dan berfungsi dengan baik.</li>
                                <li class="list-group-item">Memastikan semua karyawan yang terlibat sudah terlatih dan mengerti Instruksi Kerja pekerjaan pengelasan.</li>
                                <li class="list-group-item">Memastikan pekerja dengan keahlian khusus (Welder) dan lainnya telah memiliki sertifikat kompetensi yang sesuai.</li>
                                <li class="list-group-item">Memastikan terdapat pengawas yang melakukan pengawasan di lokasi kerja.</li>
                                <li class="list-group-item">Memastikan Area kerja telah bebas dari bahaya-bahaya lainnya yang tidak terkait dengan pekerjaan pengelasan</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .banner {
        background: linear-gradient(135deg, #1e5799 0%, #207cca 51%, #2989d8 100%);
        padding: 3rem 0;
    }
    .card-header {
        cursor: pointer;
        transition: background-color 0.3s ease;
    }
    .card-header:hover {
        background-color: #f8f9fa !important;
    }
    .btn-link {
        text-decoration: none;
        color: #17a2b8;
    }
    .btn-link:hover {
        text-decoration: none;
        color: #138496;
    }
    .btn-link.collapsed .fa-chevron-down {
        transform: rotate(0deg);
        transition: transform 0.3s ease;
    }
    .btn-link .fa-chevron-down {
        transform: rotate(180deg);
        transition: transform 0.3s ease;
    }
    .list-group-item {
        border-left: 3px solid transparent;
        transition: all 0.3s ease;
    }
    .list-group-item:hover {
        border-left-color: #17a2b8;
        background-color: #f8f9fa;
    }
    @media (max-width: 768px) {
        .banner {
            padding: 2rem 0;
        }
        .banner h1 {
            font-size: 2rem;
        }
        .banner p {
            font-size: 1rem;
        }
        .card-title {
            font-size: 1.5rem;
        }
        .btn-link {
            font-size: 1rem;
            padding: 0.75rem 0.5rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        // Add smooth transition to accordion collapse
        $('.collapse').on('show.bs.collapse', function() {
            $(this).parent().find('.card-header').addClass('bg-white');
        });
        $('.collapse').on('hide.bs.collapse', function() {
            $(this).parent().find('.card-header').removeClass('bg-white');
        });

        // Make list items more interactive
        $('.list-group-item').hover(
            function() {
                $(this).addClass('shadow-sm');
            },
            function() {
                $(this).removeClass('shadow-sm');
            }
        );
    });
</script>
@endpush
