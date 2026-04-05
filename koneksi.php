<?php
$host     = "127.0.0.1";
$user     = "root"; // Default user XAMPP/Laragon
$password = "";     // Default password XAMPP/Laragon biasanya kosong

// UBAH BAGIAN INI: Sesuaikan dengan nama database kamu
$dbname   = "spk_mabac"; 

$conn = mysqli_connect($host, $user, $password, $dbname);

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
?>