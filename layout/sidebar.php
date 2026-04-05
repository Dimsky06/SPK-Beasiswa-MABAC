<?php
// Trik agar menu biru otomatis pindah: Ambil nama file yang sedang dibuka
$current_page = basename($_SERVER['PHP_SELF']);
?>

<style>
    .sidebar { width: 250px; background-color: #343a40; min-height: 100vh; color: #fff; transition: all 0.3s; }
    .sidebar .sidebar-header { padding: 20px; background-color: #212529; text-align: center; }
    .sidebar .sidebar-header img { max-width: 80px; margin-bottom: 10px; }
    .sidebar ul.components { padding: 20px 0; border-bottom: 1px solid #47748b; }
    .sidebar ul li a { padding: 10px 20px; font-size: 1.1em; display: block; color: #d1d8e0; text-decoration: none; }
    .sidebar ul li a:hover, .sidebar ul li.active > a { color: #fff; background: #17a2b8; }
    .sidebar ul li a i { margin-right: 10px; }
</style>

<nav class="sidebar">
    <div class="sidebar-header">
        <img src="assets/img/logo_kelompok.png" alt="Logo Kelompok 9" class="img-fluid rounded-circle">
        <h5>SPK MABAC</h5>
        <small>Kelompok 9</small>
    </div>
    
    <ul class="list-unstyled components">
        <li class="<?= ($current_page == 'index.php') ? 'active' : ''; ?>">
            <a href="index.php"><i class="bi bi-house-door"></i> Dashboard</a>
        </li>
        <li class="<?= ($current_page == 'data_alternatif.php') ? 'active' : ''; ?>">
            <a href="data_alternatif.php"><i class="bi bi-people"></i> Data Alternatif</a>
        </li>
        <li class="<?= ($current_page == 'data_kriteria.php') ? 'active' : ''; ?>">
            <a href="data_kriteria.php"><i class="bi bi-box"></i> Data Kriteria</a>
        </li>
        <li class="<?= ($current_page == 'data_perhitungan.php') ? 'active' : ''; ?>">
            <a href="data_perhitungan.php"><i class="bi bi-calculator"></i> Data Perhitungan</a>
        </li>
        <li class="<?= ($current_page == 'hasil_akhir.php') ? 'active' : ''; ?>">
            <a href="hasil_akhir.php"><i class="bi bi-bar-chart-line"></i> Data Hasil Akhir</a>
        </li>
        <li class="<?= ($current_page == 'data_user.php') ? 'active' : ''; ?>">
            <a href="data_user.php"><i class="bi bi-person-badge"></i> Data User</a>
        </li>
    </ul>
</nav>

<div class="main-content">
    <div class="top-navbar">
        <h5 class="mb-0 text-secondary"><i class="bi bi-list"></i> Sistem Pendukung Keputusan</h5>
        <div class="user-profile text-secondary">
            <i class="bi bi-person-circle fs-4"></i> <span class="ms-2">Admin</span>
        </div>
    </div>