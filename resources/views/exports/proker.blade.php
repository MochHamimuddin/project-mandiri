@php
    // Inisialisasi array temporary
    $tempArray = [];
    $programCounts = [];
    
    // Looping pertama untuk mengumpulkan data program utama
    foreach ($data as $row) {
        $no = $row['No'];
        
        // Cek apakah ini program utama (tanpa titik)
        if (strpos($no, '.') === false) {
            $programNumber = $no;
            $tempArray[$programNumber] = 0; // Inisialisasi counter
        }
    }
    
    // Looping kedua untuk menghitung subprogram
    foreach ($data as $row) {
        $no = $row['No'];
        
        // Cek apakah ini subprogram (ada titik)
        if (strpos($no, '.') !== false) {
            $parts = explode('.', $no);
            $programNumber = $parts[0];
            
            // Jika program utama ditemukan di array temp, increment counter
            if (isset($tempArray[$programNumber])) {
                $tempArray[$programNumber]++;
            }
        }
    }
    
    // Hasil akhir: $tempArray akan berisi [programNumber => jumlahSubprogram]
    // Contoh: [1 => 6, 2 => 3, 3 => 3, ...]
@endphp

<table>
<!-- Header Informasi -->
    <tr>
        <td colspan="8" style="text-align: center; font-weight: bold; background-color: #2A629A; color: white; padding: 20px; line-height: 1.8; height: 150px;">
            Program Kerja K3KOLH Mitra Kerja SM KPP<br>
            Lokasi : {{ $headerInfo['location'] }}<br>
            Mitra Kerja : {{ $headerInfo['mitraName'] }}<br>
            Periode : {{ $headerInfo['period'] }}<br>
            Pencapaian Akhir : {{ $headerInfo['achievement'] }}%
        </td>
    </tr>
    

    <!-- Header Tabel -->
    <tr style="">
        <th style="background-color:#000000; color:#FFFFFF; width:50px; height:40px; border: 1px solid #000; padding: 5px; text-align: center;">No</th>
        <th style="background-color:#000000; color:#FFFFFF; width:500px; height:40px; border: 1px solid #000; padding: 5px; text-align: center;">Program Kerja</th>
        <th style="background-color:#000000; color:#FFFFFF; width:250px; height:40px; border: 1px solid #000; padding: 5px; text-align: center;">Target</th>
        <th style="background-color:#000000; color:#FFFFFF; width:80px; height:40px; border: 1px solid #000; padding: 5px; text-align: center;">Frekuensi</th>
        <th style="background-color:#000000; color:#FFFFFF; width:150px; height:40px; border: 1px solid #000; padding: 5px; text-align: center;">PIC</th>
        <th style="background-color:#000000; color:#FFFFFF; width:80px; height:40px; border: 1px solid #000; padding: 5px; text-align: center;">Bobot Penilaian</th>
        <th style="background-color:#000000; color:#FFFFFF; width:80px; height:40px; border: 1px solid #000; padding: 5px; text-align: center;">Pencapaian</th>
        <th style="background-color:#000000; color:#FFFFFF; width:80px; height:40px; border: 1px solid #000; padding: 5px; text-align: center;">Nilai Akhir</th>
    </tr>
    
    <!-- Data Tabel -->
    @foreach($data as $row)

        @if(strpos($row['No'], '.') === false)

            @php
                $programNumber = $row['No'];
                $subprogramCount = $tempArray[$programNumber] ?? 0;
            @endphp

            <!-- Program Utama -->
            <tr style="">
                <td style="background-color:#90EE90; color:#000000; padding: 5px; font-weight: bold;">{{ $row['No'] }}</td>
                <td style="background-color:#90EE90; color:#000000; padding: 5px; font-weight: bold;">{{ $row['nama_program'] }}</td>
                <td style="background-color:#90EE90; color:#000000; padding: 5px;">{!! nl2br(e($row['Target'])) !!}</td>
                <td style="background-color:#90EE90; color:#000000; padding: 5px; text-align: center;">{{ $row['Frekuensi'] }}</td>
                <td style="background-color:#90EE90; color:#000000; padding: 5px;">{{ $row['PIC'] }}</td>
                <td style="border: 1px solid #000; padding: 5px; text-align: center; font-weight: bold; vertical-align: middle;" rowspan="{{ $subprogramCount + 1 }}" >{{ $row['Bobot Penilaian'] }}</td>
                <td style="border: 1px solid #000; padding: 5px; text-align: center; font-weight: bold;">{{ $row['Pencapaian'] }}</td>
                <td style="border: 1px solid #000; padding: 5px; text-align: center; font-weight: bold;
                    @php
                        $nilai = (float) str_replace('%', '', $row['Pencapaian']);
                        if ($nilai < 70) {
                            echo 'background-color: #FFFF00;'; // Kuning
                        } elseif ($nilai < 90) {
                            echo 'background-color: #90EE90;'; // Hijau Lumut
                        } else {
                            echo 'background-color: #228B22;'; // Hijau Pohon
                        }
                    @endphp
                " rowspan="{{ $subprogramCount + 1 }}">{{ $row['Nilai Akhir'] }}
                </td>
            </tr>
        @else
            <!-- Sub Program -->
            <tr>
                <td style="border: 1px solid #000; padding: 5px;">{{ $row['No'] }}</td>
                <td style="border: 1px solid #000; padding: 5px;">{{ $row['nama_program'] }}</td>
                <td style="border: 1px solid #000; padding: 5px;">{!! nl2br(e($row['Target'])) !!}</td>
                <td style="border: 1px solid #000; padding: 5px; text-align: center;">{{ $row['Frekuensi'] }}</td>
                <td style="border: 1px solid #000; padding: 5px;">{{ $row['PIC'] }}</td>
                <td style="border: 1px solid #000; padding: 5px; text-align: center;">{{ $row['Pencapaian'] }}</td>
            </tr>
        @endif
    @endforeach
</table>


<!-- Your main table content first... -->

@php
    $signatureStartRow = count($data) + 3; // Adjust based on your table structure
@endphp

<!-- Signature Section -->
<tr>
    <td colspan="10" style="height: 40px;"><!-- Vertical spacer --></td>
</tr>

<!-- First Row: Titles -->
<tr>
    <td colspan="2" align="left" style="display:flex; justify-content:between;">
        <span style="">Disusun Oleh,</span>
        <span>Disetujui Oleh,</span>
    </td>
    <td colspan="6" align="center" style="display:flex; text-align: center; justify-content:center;">Diketahui Oleh,</td>
</tr>

<!-- Second Row: Signature Lines -->
<tr>
    <td colspan="2" style="display:flex; justify-content:between;">
        <u>................................</u>
        <u>................................</u>
    </td>
    <td colspan="2" style="text-align: center; padding-top: 20px;">
        <u>................................</u>
    </td>
    <td colspan="2" style="text-align: center; padding-top: 20px;">
        <u>................................</u>
    </td>
    <td colspan="2" style="text-align: center; padding-top: 20px;">
        <u>................................</u>
    </td>
</tr>

<!-- Third Row: Positions -->
<tr>
    <td colspan="2" style="display:flex; justify-content:between;">
        <span style="">SHE Leader Mitra Kerja</span>
        <span>PJO Mitra Kerja</span>
    </td>


    <td colspan="2" style="text-align: center; padding-top: 5px;">
        SM Dept. Head/Sect. Head
    </td>
    <td colspan="2" style="text-align: center; padding-top: 5px;">
        SHE Dept. Head Site
    </td>
    <td colspan="2" style="text-align: center; padding-top: 5px;">
    Project Manager
    </td>
</tr>