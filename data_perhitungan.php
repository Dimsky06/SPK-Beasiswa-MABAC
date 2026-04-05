<?php 
include 'koneksi.php'; 
include 'layout/header.php'; 

// ==========================================================
// AMBIL DATA DARI DATABASE UNTUK PERHITUNGAN MABAC
// ==========================================================

// 1. Ambil Data Kriteria
$q_kriteria = mysqli_query($conn, "SELECT * FROM kriteria ORDER BY kode_kriteria ASC");
$kriteria = [];
while($k = mysqli_fetch_assoc($q_kriteria)) { 
    $kriteria[$k['kode_kriteria']] = [
        'id' => $k['id_kriteria'],
        'nama' => $k['nama_kriteria'],
        'bobot' => $k['bobot'], 
        'jenis' => $k['jenis']
    ]; 
}

// 2. Ambil Data Alternatif & Nilainya (Matriks Keputusan X)
$q_alternatif = mysqli_query($conn, "SELECT * FROM alternatif ORDER BY kode_alternatif ASC");
$alternatif = [];
while($a = mysqli_fetch_assoc($q_alternatif)) {
    $id_alt = $a['id_alternatif'];
    $q_nilai = mysqli_query($conn, "SELECT k.kode_kriteria, p.nilai FROM penilaian p JOIN kriteria k ON p.id_kriteria = k.id_kriteria WHERE p.id_alternatif = '$id_alt'");
    $nilai = [];
    while($n = mysqli_fetch_assoc($q_nilai)) { 
        $nilai[$n['kode_kriteria']] = $n['nilai']; 
    }
    $alternatif[$a['kode_alternatif']] = [
        'nama' => $a['nama_alternatif'], 
        'nilai' => $nilai
    ];
}

// Cek apakah data kosong
$data_siap = (!empty($alternatif) && !empty($kriteria));
?>

<style>
    body { overflow-x: hidden; background-color: #f4f6f9; }
    .wrapper { display: flex; align-items: stretch; width: 100%; }
    #sidebar { min-width: 250px; max-width: 250px; min-height: 100vh; background-color: #343a40; }
    #content { width: 100%; padding: 20px; min-height: 100vh; }
    .card { border-radius: 8px; border: none; margin-bottom: 20px; }
    .card-header { background-color: #17a2b8; color: white; font-weight: bold; }
    .table th { background-color: #e9ecef !important; vertical-align: middle; }
</style>

<div class="wrapper">
    <nav id="sidebar">
        <?php include 'layout/sidebar.php'; ?>
    </nav>

    <div id="content">
        <div class="top-nav d-flex justify-content-between align-items-center mb-4 bg-white p-3 shadow-sm rounded">
            <h5 class="mb-0 text-uppercase fw-bold"><i class="bi bi-calculator me-2"></i> Data Perhitungan MABAC</h5>
            <span class="text-muted text-uppercase">Admin <i class="bi bi-person-circle ms-1"></i></span>
        </div>

        <?php if(!$data_siap): ?>
            <div class="alert alert-warning shadow-sm"><i class="bi bi-exclamation-triangle-fill me-2"></i> Data Kriteria atau Alternatif belum lengkap untuk melakukan perhitungan.</div>
        <?php else: ?>

        <div class="card shadow-sm">
            <div class="card-header py-3">Langkah 1: Matriks Keputusan (X)</div>
            <div class="card-body table-responsive">
                <table class="table table-bordered table-striped text-center align-middle">
                    <thead>
                        <tr>
                            <th rowspan="2" width="15%">Alternatif</th>
                            <th colspan="<?= count($kriteria); ?>">Kriteria</th>
                        </tr>
                        <tr>
                            <?php foreach($kriteria as $kode_k => $k): ?>
                                <th><?= $kode_k; ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($alternatif as $kode_a => $alt): ?>
                        <tr>
                            <td class="fw-bold"><?= $kode_a; ?> - <?= $alt['nama']; ?></td>
                            <?php foreach($kriteria as $kode_k => $k): ?>
                                <td><?= isset($alt['nilai'][$kode_k]) ? $alt['nilai'][$kode_k] : 0; ?></td>
                            <?php endforeach; ?>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php
        // Persiapan cari Min dan Max tiap kriteria
        $minMax = [];
        foreach ($kriteria as $kode_k => $k) {
            $kolom_nilai = array_column(array_column($alternatif, 'nilai'), $kode_k);
            if(!empty($kolom_nilai)) { 
                $minMax[$kode_k] = ['max' => max($kolom_nilai), 'min' => min($kolom_nilai)]; 
            } else {
                $minMax[$kode_k] = ['max' => 0, 'min' => 0];
            }
        }
        ?>

        <div class="card shadow-sm">
            <div class="card-header py-3">Langkah 2: Normalisasi Matriks (N)</div>
            <div class="card-body table-responsive">
                <table class="table table-bordered table-striped text-center align-middle">
                    <thead>
                        <tr>
                            <th>Alternatif</th>
                            <?php foreach($kriteria as $kode_k => $k): ?>
                                <th><?= $kode_k; ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $matriks_N = [];
                        foreach($alternatif as $kode_a => $alt): 
                        ?>
                        <tr>
                            <td class="fw-bold"><?= $kode_a; ?></td>
                            <?php 
                            foreach($kriteria as $kode_k => $k): 
                                $x = isset($alt['nilai'][$kode_k]) ? $alt['nilai'][$kode_k] : 0;
                                $max = $minMax[$kode_k]['max'];
                                $min = $minMax[$kode_k]['min'];
                                $pembagi = ($max - $min) == 0 ? 1 : ($max - $min); // Hindari division by zero
                                
                                // Rumus Normalisasi MABAC
                                if ($k['jenis'] == 'Benefit') {
                                    $n = ($x - $min) / $pembagi;
                                } else {
                                    $n = ($max - $x) / $pembagi;
                                }
                                $matriks_N[$kode_a][$kode_k] = $n;
                            ?>
                                <td><?= round($n, 4); ?></td>
                            <?php endforeach; ?>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header py-3">Langkah 3: Matriks Tertimbang (V)</div>
            <div class="card-body table-responsive">
                <table class="table table-bordered table-striped text-center align-middle">
                    <thead>
                        <tr>
                            <th>Alternatif</th>
                            <?php foreach($kriteria as $kode_k => $k): ?>
                                <th><?= $kode_k; ?><br><small>(W=<?= $k['bobot']; ?>)</small></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $matriks_V = [];
                        foreach($alternatif as $kode_a => $alt): 
                        ?>
                        <tr>
                            <td class="fw-bold"><?= $kode_a; ?></td>
                            <?php 
                            foreach($kriteria as $kode_k => $k): 
                                $n = $matriks_N[$kode_a][$kode_k];
                                $w = $k['bobot'];
                                // Rumus V = W * (N + 1)
                                $v = $w * ($n + 1);
                                $matriks_V[$kode_a][$kode_k] = $v;
                            ?>
                                <td><?= round($v, 4); ?></td>
                            <?php endforeach; ?>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header py-3">Langkah 4: Matriks Area Batas Perkiraan (G)</div>
            <div class="card-body table-responsive">
                <table class="table table-bordered text-center align-middle">
                    <thead class="table-light">
                        <tr>
                            <?php foreach($kriteria as $kode_k => $k): ?>
                                <th>Batas <?= $kode_k; ?> (G)</th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <?php 
                            $matriks_G = [];
                            $jumlah_alternatif = count($alternatif);
                            foreach($kriteria as $kode_k => $k): 
                                $perkalian_v = 1;
                                foreach ($alternatif as $kode_a => $alt) { 
                                    $perkalian_v *= $matriks_V[$kode_a][$kode_k]; 
                                }
                                // Rumus G = Akar pangkat M dari (V1 * V2 * ... * Vm)
                                $g = pow($perkalian_v, 1 / $jumlah_alternatif);
                                $matriks_G[$kode_k] = $g;
                            ?>
                                <td class="fw-bold text-success"><?= round($g, 4); ?></td>
                            <?php endforeach; ?>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card shadow-sm mb-5">
            <div class="card-header py-3">Langkah 5 & 6: Jarak dari Batas (Q) dan Skor Akhir (S)</div>
            <div class="card-body table-responsive">
                <table class="table table-bordered table-striped text-center align-middle">
                    <thead>
                        <tr>
                            <th>Alternatif</th>
                            <?php foreach($kriteria as $kode_k => $k): ?>
                                <th><?= $kode_k; ?></th>
                            <?php endforeach; ?>
                            <th class="bg-warning text-dark">Skor Akhir (S)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        foreach($alternatif as $kode_a => $alt): 
                            $skor_S = 0;
                        ?>
                        <tr>
                            <td class="fw-bold"><?= $kode_a; ?></td>
                            <?php 
                            foreach($kriteria as $kode_k => $k): 
                                $v = $matriks_V[$kode_a][$kode_k];
                                $g = $matriks_G[$kode_k];
                                // Rumus Q = V - G
                                $q = $v - $g;
                                $skor_S += $q;
                            ?>
                                <td><?= round($q, 4); ?></td>
                            <?php endforeach; ?>
                            <td class="bg-warning text-dark fw-bold"><?= round($skor_S, 4); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php endif; // Akhir kondisi $data_siap ?>

    </div>
</div>

<?php 
if(file_exists('layout/footer.php')) { include 'layout/footer.php'; }
?>