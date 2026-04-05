<?php 
include 'koneksi.php'; 
include 'layout/header.php'; 

// ==========================================================
// AMBIL TOTAL DATA UNTUK DITAMPILKAN DI DASHBOARD
// ==========================================================

// Hitung Total Alternatif
$q_alt = mysqli_query($conn, "SELECT COUNT(*) as total FROM alternatif");
$d_alt = mysqli_fetch_assoc($q_alt);
$total_alternatif = $d_alt['total'];

// Hitung Total Kriteria
$q_krit = mysqli_query($conn, "SELECT COUNT(*) as total FROM kriteria");
$d_krit = mysqli_fetch_assoc($q_krit);
$total_kriteria = $d_krit['total'];
?>

<style>
    body { overflow-x: hidden; background-color: #f4f6f9; }
    .wrapper { display: flex; align-items: stretch; width: 100%; }
    
    #sidebar { min-width: 250px; max-width: 250px; min-height: 100vh; background-color: #343a40; }
    #content { width: 100%; padding: 20px; min-height: 100vh; }
    
    /* Styling Khusus Dashboard */
    .welcome-card { background-color: #118ab2; color: white; border-radius: 10px; border: none; }
    .summary-card { border-radius: 10px; border: none; border-left: 5px solid; transition: transform 0.2s; }
    .summary-card:hover { transform: translateY(-5px); }
    .border-blue { border-left-color: #0077b6 !important; }
    .border-green { border-left-color: #06d6a0 !important; }
    .border-yellow { border-left-color: #ffd166 !important; }
    
    .icon-bg-blue { color: #cce3f0; font-size: 3rem; opacity: 0.5; }
    .icon-bg-green { color: #cbf3e8; font-size: 3rem; opacity: 0.5; }
    .icon-bg-yellow { color: #ffefc2; font-size: 3rem; opacity: 0.5; }
</style>

<div class="wrapper">
    <nav id="sidebar">
        <?php include 'layout/sidebar.php'; ?>
    </nav>

    <div id="content">
        
        <div class="top-nav d-flex justify-content-between align-items-center mb-4 bg-white p-3 shadow-sm rounded">
            <h5 class="mb-0 text-muted"><i class="bi bi-list me-2"></i> Sistem Pendukung Keputusan</h5>
            <span class="text-muted text-uppercase">Admin <i class="bi bi-person-circle ms-1"></i></span>
        </div>

        <div class="card welcome-card shadow-sm mb-4 px-3 py-4">
            <div class="d-flex align-items-center">
                <div class="me-4 ms-2 text-white">
                    <i class="bi bi-laptop" style="font-size: 3.5rem;"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-1">Selamat Datang di SPK MABAC! 👋</h4>
                    <p class="mb-0 fs-6 text-light">Sistem Pendukung Keputusan Seleksi Penerima Beasiswa menggunakan metode MABAC (Multi-Attributive Border Approximation Area Comparison).</p>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="card shadow-sm summary-card border-blue h-100 p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted fw-bold mb-1 text-uppercase" style="font-size: 0.85rem;">Total Alternatif</p>
                            <h2 class="fw-bold text-primary mb-0 d-flex align-items-baseline">
                                <?= $total_alternatif; ?> <span class="fs-6 text-muted fw-normal ms-2">Mahasiswa</span>
                            </h2>
                        </div>
                        <i class="bi bi-people-fill icon-bg-blue"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-3">
                <div class="card shadow-sm summary-card border-green h-100 p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted fw-bold mb-1 text-uppercase" style="font-size: 0.85rem;">Total Kriteria</p>
                            <h2 class="fw-bold text-success mb-0 d-flex align-items-baseline">
                                <?= $total_kriteria; ?> <span class="fs-6 text-muted fw-normal ms-2">Syarat</span>
                            </h2>
                        </div>
                        <i class="bi bi-box-seam icon-bg-green"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-3">
                <div class="card shadow-sm summary-card border-yellow h-100 p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted fw-bold mb-1 text-uppercase" style="font-size: 0.85rem;">Peringkat Terbaik</p>
                            <h5 class="fw-bold text-dark mb-2">Lihat Hasil SPK</h5>
                            <a href="hasil_akhir.php" class="btn btn-warning btn-sm text-dark fw-bold rounded-pill px-3 shadow-sm">
                                Cek Sekarang <i class="bi bi-arrow-right-circle ms-1"></i>
                            </a>
                        </div>
                        <i class="bi bi-trophy-fill icon-bg-yellow"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0 rounded">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3 text-info"><i class="bi bi-info-circle-fill me-2"></i> Tentang Metode MABAC</h6>
                <p class="text-muted" style="text-align: justify;">
                    Metode <strong>MABAC</strong> dikembangkan oleh Pamucar dan Cirovic pada tahun 2015. Inti dari metode ini adalah membandingkan alternatif dengan <strong>Area Batas Perkiraan (Border Approximation Area / G)</strong>.
                </p>
                <p class="text-muted mb-0" style="text-align: justify;">
                    Jika nilai perhitungan alternatif berada di area atas batas (nilai positif / <strong class="text-success">+</strong>), maka alternatif dikategorikan sangat layak atau mendekati ideal. Sebaliknya, jika berada di area bawah batas (nilai negatif / <strong class="text-danger">-</strong>), maka alternatif dianggap tidak layak direkomendasikan.
                </p>
            </div>
        </div>

    </div> </div> <?php 
if(file_exists('layout/footer.php')) { include 'layout/footer.php'; }
?>