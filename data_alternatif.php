<?php 
include 'koneksi.php'; 
include 'layout/header.php'; 

// ==========================================================
// LOGIKA CRUD (CREATE, UPDATE, DELETE) & SIMPAN NILAI
// ==========================================================

if (isset($_POST['tambah'])) {
    $kode = $_POST['kode_alternatif'];
    $nama = $_POST['nama_alternatif'];
    
    $query = mysqli_query($conn, "INSERT INTO alternatif (kode_alternatif, nama_alternatif) VALUES ('$kode', '$nama')");
    if ($query) {
        $id_baru = mysqli_insert_id($conn);
        $kriteria = mysqli_query($conn, "SELECT id_kriteria FROM kriteria");
        while ($k = mysqli_fetch_assoc($kriteria)) {
            $id_k = $k['id_kriteria'];
            mysqli_query($conn, "INSERT INTO penilaian (id_alternatif, id_kriteria, nilai) VALUES ('$id_baru', '$id_k', 0)");
        }
        echo "<script>alert('Data berhasil ditambahkan!'); window.location='data_alternatif.php';</script>";
    }
}

if (isset($_POST['edit'])) {
    $id = $_POST['id_alternatif'];
    $kode = $_POST['kode_alternatif'];
    $nama = $_POST['nama_alternatif'];
    
    mysqli_query($conn, "UPDATE alternatif SET kode_alternatif='$kode', nama_alternatif='$nama' WHERE id_alternatif='$id'");
    echo "<script>alert('Data berhasil diubah!'); window.location='data_alternatif.php';</script>";
}

if (isset($_POST['hapus'])) {
    $id = $_POST['id_alternatif'];
    
    mysqli_query($conn, "DELETE FROM penilaian WHERE id_alternatif='$id'");
    mysqli_query($conn, "DELETE FROM alternatif WHERE id_alternatif='$id'");
    
    echo "<script>alert('Data berhasil dihapus!'); window.location='data_alternatif.php';</script>";
}

if (isset($_POST['simpan_nilai'])) {
    $id_alt = $_POST['id_alternatif'];
    $nilai_array = $_POST['nilai'];
    
    foreach ($nilai_array as $id_kriteria => $nilai_input) {
        $cek = mysqli_query($conn, "SELECT * FROM penilaian WHERE id_alternatif='$id_alt' AND id_kriteria='$id_kriteria'");
        if (mysqli_num_rows($cek) > 0) {
            mysqli_query($conn, "UPDATE penilaian SET nilai='$nilai_input' WHERE id_alternatif='$id_alt' AND id_kriteria='$id_kriteria'");
        } else {
            mysqli_query($conn, "INSERT INTO penilaian (id_alternatif, id_kriteria, nilai) VALUES ('$id_alt', '$id_kriteria', '$nilai_input')");
        }
    }
    echo "<script>alert('Nilai kriteria berhasil disimpan!'); window.location='data_alternatif.php';</script>";
}
?>

<style>
    body { overflow-x: hidden; background-color: #f4f6f9; }
    .wrapper { display: flex; align-items: stretch; width: 100%; }
    
    /* Atur Lebar Sidebar & Konten */
    #sidebar { min-width: 250px; max-width: 250px; min-height: 100vh; background-color: #343a40; }
    #content { width: 100%; padding: 20px; min-height: 100vh; }
    
    /* Desain Card & Tabel Web */
    .card { border-radius: 8px; border: none; }
    .card-header { background-color: #17a2b8; color: white; font-weight: bold; } /* Warna teal/info */
</style>

<div class="wrapper">
    <nav id="sidebar">
        <?php include 'layout/sidebar.php'; ?>
    </nav>

    <div id="content">
        
        <div class="top-nav d-flex justify-content-between align-items-center mb-4 bg-white p-3 shadow-sm rounded">
            <h5 class="mb-0 text-uppercase fw-bold"><i class="bi bi-people-fill me-2"></i> Data Alternatif (Mahasiswa)</h5>
            <span class="text-muted text-uppercase">Admin <i class="bi bi-person-circle ms-1"></i></span>
        </div>

        <div class="card shadow-sm">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 text-uppercase">Daftar Mahasiswa Calon Penerima</h6>
                <button type="button" class="btn btn-light btn-sm fw-bold text-info" data-bs-toggle="modal" data-bs-target="#modalTambah">
                    <i class="bi bi-plus-circle me-1"></i> Tambah Data
                </button>
            </div>
            
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle text-center">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">No</th>
                                <th width="15%">Kode</th>
                                <th class="text-start ps-3">Nama Mahasiswa</th>
                                <th width="30%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            $data = mysqli_query($conn, "SELECT * FROM alternatif ORDER BY kode_alternatif ASC");
                            while ($row = mysqli_fetch_assoc($data)) {
                            ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><span class="badge bg-secondary px-3 py-2"><?= $row['kode_alternatif']; ?></span></td>
                                <td class="text-start ps-3 fw-bold"><?= $row['nama_alternatif']; ?></td>
                                <td>
                                    <button type="button" class="btn btn-success btn-sm m-1" data-bs-toggle="modal" data-bs-target="#modalNilai<?= $row['id_alternatif']; ?>">
                                        <i class="bi bi-list-check"></i> Isi Nilai
                                    </button>
                                    <button type="button" class="btn btn-warning btn-sm m-1 text-dark" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $row['id_alternatif']; ?>">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm m-1" data-bs-toggle="modal" data-bs-target="#modalHapus<?= $row['id_alternatif']; ?>">
                                        <i class="bi bi-trash"></i> Hapus
                                    </button>
                                </td>
                            </tr>

                            <div class="modal fade" id="modalNilai<?= $row['id_alternatif']; ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header bg-success text-white">
                                            <h5 class="modal-title">Input Nilai: <?= $row['nama_alternatif']; ?></h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form method="POST">
                                            <div class="modal-body text-start">
                                                <input type="hidden" name="id_alternatif" value="<?= $row['id_alternatif']; ?>">
                                                <div class="alert alert-info py-2"><small><i class="bi bi-info-circle"></i> Masukkan nilai sesuai berkas asli.</small></div>
                                                
                                                <?php
                                                $id_alt = $row['id_alternatif'];
                                                $kriteria = mysqli_query($conn, "SELECT * FROM kriteria ORDER BY kode_kriteria ASC");
                                                while ($k = mysqli_fetch_assoc($kriteria)) {
                                                    $id_k = $k['id_kriteria'];
                                                    $q_nilai = mysqli_query($conn, "SELECT nilai FROM penilaian WHERE id_alternatif='$id_alt' AND id_kriteria='$id_k'");
                                                    $dt_nilai = mysqli_fetch_assoc($q_nilai);
                                                    $nilai_sekarang = $dt_nilai ? $dt_nilai['nilai'] : 0;
                                                ?>
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold"><?= $k['kode_kriteria']; ?> - <?= $k['nama_kriteria']; ?></label>
                                                    <input type="number" step="0.01" class="form-control" name="nilai[<?= $id_k; ?>]" value="<?= $nilai_sekarang; ?>" required>
                                                </div>
                                                <?php } ?>
                                            </div>
                                            <div class="modal-footer bg-light">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                                <button type="submit" name="simpan_nilai" class="btn btn-success"><i class="bi bi-save"></i> Simpan Nilai</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <div class="modal fade" id="modalEdit<?= $row['id_alternatif']; ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header bg-warning text-dark">
                                            <h5 class="modal-title">Edit Data</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form method="POST">
                                            <div class="modal-body text-start">
                                                <input type="hidden" name="id_alternatif" value="<?= $row['id_alternatif']; ?>">
                                                <div class="mb-3">
                                                    <label class="form-label">Kode Alternatif</label>
                                                    <input type="text" class="form-control" name="kode_alternatif" value="<?= $row['kode_alternatif']; ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Nama Mahasiswa</label>
                                                    <input type="text" class="form-control" name="nama_alternatif" value="<?= $row['nama_alternatif']; ?>" required>
                                                </div>
                                            </div>
                                            <div class="modal-footer bg-light">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" name="edit" class="btn btn-warning text-dark fw-bold">Update Data</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <div class="modal fade" id="modalHapus<?= $row['id_alternatif']; ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content border-danger">
                                        <div class="modal-header bg-danger text-white">
                                            <h5 class="modal-title">Konfirmasi Hapus</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form method="POST">
                                            <div class="modal-body text-center py-4">
                                                <input type="hidden" name="id_alternatif" value="<?= $row['id_alternatif']; ?>">
                                                <i class="bi bi-exclamation-triangle text-danger" style="font-size: 4rem;"></i>
                                                <h5 class="mt-3">Yakin ingin menghapus <strong><?= $row['nama_alternatif']; ?></strong>?</h5>
                                            </div>
                                            <div class="modal-footer bg-light justify-content-center">
                                                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" name="hapus" class="btn btn-danger px-4">Ya, Hapus!</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
    </div> </div> <div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="bi bi-person-plus"></i> Tambah Mahasiswa</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Kode Alternatif</label>
                        <input type="text" class="form-control" name="kode_alternatif" placeholder="Contoh: A1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Mahasiswa</label>
                        <input type="text" class="form-control" name="nama_alternatif" placeholder="Masukkan nama..." required>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="tambah" class="btn btn-info text-white fw-bold"><i class="bi bi-save"></i> Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php 
if(file_exists('layout/footer.php')) { include 'layout/footer.php'; }
?>