<?php 
// 1. Panggil Header
include 'layout/header.php'; 
?>

<style>
    body { overflow-x: hidden; background-color: #f4f6f9; }
    .wrapper { display: flex; align-items: stretch; width: 100%; }
    
    /* Atur Lebar Sidebar & Konten */
    #sidebar { min-width: 250px; max-width: 250px; min-height: 100vh; background-color: #343a40; }
    #content { width: 100%; padding: 20px; min-height: 100vh; }
    
    /* Desain Card & Tabel Web */
    .card { border-radius: 8px; border: none; }
    .card-header { background-color: #5bc0de; color: white; font-weight: bold; }
    
    /* =========================================
       FITUR PRINT KE PDF (SUPER RAPI - A4)
       ========================================= */
    @media print {
        @page { size: A4 portrait; margin: 1.5cm; } /* Ukuran paten A4 */
        
        body { 
            background-color: white !important; 
            -webkit-print-color-adjust: exact !important; /* Memaksa warna tabel/badge keluar di PDF */
            print-color-adjust: exact !important;
            color: black !important;
        }
        
        /* Sembunyikan semua elemen UI Web */
        #sidebar, .top-nav, .btn, .aksi-kolom { display: none !important; }
        
        /* Lebarkan area konten */
        #content { margin: 0 !important; padding: 0 !important; width: 100% !important; }
        .wrapper { display: block !important; }
        
        /* Bersihkan Card */
        .card { box-shadow: none !important; border: none !important; margin-top: 20px !important; }
        .card-header { display: none !important; } /* Sembunyikan header biru tabel saat diprint */
        .card-body { padding: 0 !important; }

        /* Styling Khusus Tabel Laporan PDF */
        table { border-collapse: collapse !important; width: 100% !important; margin-top: 20px !important; }
        th, td { border: 1px solid #000 !important; padding: 12px 8px !important; font-size: 14pt !important; color: black !important; }
        thead th { background-color: #e9ecef !important; text-align: center !important; font-weight: bold !important; }
        
        /* Header Laporan (Kop) */
        .print-header { margin-bottom: 30px !important; }
        .print-header h4 { font-size: 18pt !important; font-weight: bold !important; margin-bottom: 5px !important; }
        .print-header h5 { font-size: 16pt !important; margin-bottom: 5px !important; }
        .print-header p { font-size: 14pt !important; margin-bottom: 10px !important; }
        .print-header hr { border-top: 3px solid black !important; border-bottom: 1px solid black !important; padding: 1px 0 !important; }
    }
</style>

<div class="wrapper">
    <nav id="sidebar">
        <?php include 'layout/sidebar.php'; ?>
    </nav>

    <div id="content">
        
        <div class="top-nav d-flex justify-content-between align-items-center mb-4 bg-white p-3 shadow-sm rounded">
            <h5 class="mb-0 text-uppercase fw-bold"><i class="bi bi-grid-fill me-2"></i> Data Hasil Akhir</h5>
            <span class="text-muted text-uppercase">Admin <i class="bi bi-person-circle ms-1"></i></span>
        </div>

        <div class="d-none d-print-block text-center print-header">
            <h4>LAPORAN HASIL SELEKSI PENERIMA BEASISWA</h4>
            <h5>SISTEM PENDUKUNG KEPUTUSAN METODE MABAC</h5>
            <p>Kelompok 9</p>
            <hr>
        </div>

        <div class="card shadow-sm">
            <div class="card-header py-3 text-center">
                <h6 class="m-0 text-uppercase">Daftar Peringkat Penerima Beasiswa</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle text-center">
                        <thead class="table-light">
                            <tr>
                                <th width="10%">Peringkat</th>
                                <th width="15%">Kode Alternatif</th>
                                <th width="30%">Nama Alternatif</th>
                                <th width="15%">Skor Akhir (S)</th>
                                <th width="15%">Rekomendasi</th>
                                <th width="15%" class="aksi-kolom">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            include 'koneksi.php';

                            $q_kriteria = mysqli_query($conn, "SELECT * FROM kriteria");
                            $kriteria = [];
                            while($k = mysqli_fetch_assoc($q_kriteria)) { 
                                $kriteria[$k['kode_kriteria']] = ['bobot' => $k['bobot'], 'jenis' => $k['jenis']]; 
                            }

                            $q_alternatif = mysqli_query($conn, "SELECT * FROM alternatif");
                            $alternatif = [];
                            while($a = mysqli_fetch_assoc($q_alternatif)) {
                                $id_alt = $a['id_alternatif'];
                                $q_nilai = mysqli_query($conn, "SELECT k.kode_kriteria, p.nilai FROM penilaian p JOIN kriteria k ON p.id_kriteria = k.id_kriteria WHERE p.id_alternatif = '$id_alt'");
                                $nilai = [];
                                while($n = mysqli_fetch_assoc($q_nilai)) { $nilai[$n['kode_kriteria']] = $n['nilai']; }
                                $alternatif[$a['kode_alternatif']] = ['nama' => $a['nama_alternatif'], 'nilai' => $nilai];
                            }

                            if (!empty($alternatif) && !empty($kriteria)) {
                                $minMax = [];
                                foreach ($kriteria as $kode_k => $k) {
                                    $kolom_nilai = array_column(array_column($alternatif, 'nilai'), $kode_k);
                                    if(!empty($kolom_nilai)) { $minMax[$kode_k] = ['max' => max($kolom_nilai), 'min' => min($kolom_nilai)]; }
                                }

                                $matriks_V = [];
                                foreach ($alternatif as $kode_a => $alt) {
                                    foreach ($kriteria as $kode_k => $k) {
                                        $x = $alt['nilai'][$kode_k] ?? 0;
                                        $max = $minMax[$kode_k]['max'] ?? 0;
                                        $min = $minMax[$kode_k]['min'] ?? 0;
                                        $pembagi = ($max - $min) == 0 ? 1 : ($max - $min);
                                        $n = ($k['jenis'] == 'Benefit') ? ($x - $min) / $pembagi : ($max - $x) / $pembagi;
                                        $matriks_V[$kode_a][$kode_k] = $k['bobot'] * ($n + 1);
                                    }
                                }

                                $matriks_G = [];
                                $jumlah_alternatif = count($alternatif);
                                foreach ($kriteria as $kode_k => $k) {
                                    $perkalian_v = 1;
                                    foreach ($alternatif as $kode_a => $alt) { $perkalian_v *= $matriks_V[$kode_a][$kode_k]; }
                                    $matriks_G[$kode_k] = pow($perkalian_v, 1 / $jumlah_alternatif);
                                }

                                $hasil_akhir = [];
                                foreach ($alternatif as $kode_a => $alt) {
                                    $skor_S = 0;
                                    foreach ($kriteria as $kode_k => $k) { $skor_S += ($matriks_V[$kode_a][$kode_k] - $matriks_G[$kode_k]); }
                                    $hasil_akhir[] = ['kode' => $kode_a, 'nama' => $alt['nama'], 'skor' => round($skor_S, 4)];
                                }

                                usort($hasil_akhir, function($a, $b) { return $b['skor'] <=> $a['skor']; });

                                $no = 1;
                                foreach ($hasil_akhir as $hasil): 
                                ?>
                                    <tr>
                                        <td><strong><?= $no++; ?></strong></td>
                                        <td><?= $hasil['kode']; ?></td>
                                        <td class="text-start px-3"><?= $hasil['nama']; ?></td>
                                        <td><strong><?= $hasil['skor']; ?></strong></td>
                                        <td>
                                            <?php if($hasil['skor'] > 0): ?>
                                                <span class="badge bg-success text-white px-3 py-2 rounded-pill">Layak</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger text-white px-3 py-2 rounded-pill">Tidak Layak</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="aksi-kolom">
                                            <a href="#" class="btn btn-sm btn-info text-white"><i class="bi bi-eye"></i> Detail</a>
                                        </td>
                                    </tr>
                                <?php 
                                endforeach; 
                            } else {
                                echo "<tr><td colspan='6' class='text-center text-danger py-4'>Data Penilaian belum lengkap.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-4 top-nav">
                    <button onclick="window.print()" class="btn btn-danger px-4 py-2 text-white fw-bold shadow-sm">
                        <i class="bi bi-file-pdf me-2"></i> Export ke PDF
                    </button>
                </div>

            </div>
        </div>
    </div>
</div>

<?php 
if(file_exists('layout/footer.php')) { include 'layout/footer.php'; }
?>