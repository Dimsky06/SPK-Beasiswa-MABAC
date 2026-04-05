<?php 
include 'koneksi.php'; 
include 'layout/header.php'; 

// ==========================================================
// LOGIKA CRUD (CREATE, UPDATE, DELETE) KRITERIA
// ==========================================================

// 1. TAMBAH KRITERIA
if (isset($_POST['tambah'])) {
    $kode = $_POST['kode_kriteria'];
    $nama = $_POST['nama_kriteria'];
    $bobot = $_POST['bobot'];
    $jenis = $_POST['jenis'];
    
    $query = mysqli_query($conn, "INSERT INTO kriteria (kode_kriteria, nama_kriteria, bobot, jenis) VALUES ('$kode', '$nama', '$bobot', '$jenis')");
    
    if ($query) {
        // Otomatis tambahkan nilai 0 untuk alternatif yang sudah ada
        $id_baru = mysqli_insert_id($conn);
        $alternatif = mysqli_query($conn, "SELECT id_alternatif FROM alternatif");
        while ($a = mysqli_fetch_assoc($alternatif)) {
            $id_a = $a['id_alternatif'];
            mysqli_query($conn, "INSERT INTO penilaian (id_alternatif, id_kriteria, nilai) VALUES ('$id_a', '$id_baru', 0)");
        }
        echo "<script>alert('Data Kriteria berhasil ditambahkan!'); window.location='data_kriteria.php';</script>";
    }
}

// 2. EDIT KRITERIA
if (isset($_POST['edit'])) {
    $id = $_POST['id_kriteria'];
    $kode = $_POST['kode_kriteria'];
    $nama = $_POST['nama_kriteria'];
    $bobot = $_POST['bobot'];
    $jenis = $_POST['jenis'];
    
    mysqli_query($conn, "UPDATE kriteria SET kode_kriteria='$kode', nama_kriteria='$nama', bobot='$bobot', jenis='$jenis' WHERE id_kriteria='$id'");
    echo "<script>alert('Data Kriteria berhasil diubah!'); window.location='data_kriteria.php';</script>";
}

// 3. HAPUS KRITERIA
if (isset($_POST['hapus'])) {
    $id = $_POST['id_kriteria'];
    
    mysqli_query($conn, "DELETE FROM penilaian WHERE id_kriteria='$id'");
    mysqli_query($conn, "DELETE FROM kriteria WHERE id_kriteria='$id'");
    
    echo "<script>alert('Data Kriteria berhasil dihapus!'); window.location='data_kriteria.php';</script>";
}
?>

<style>
    body { overflow-x: hidden; background-color: #f4f6f9; }
    .wrapper { display: flex; align-items: stretch; width: 100%; }
    
    #sidebar { min-width: 250px; max-width: 250px; min-height: 100vh; background-color: #343a40; }
    #content { width: 100%; padding: 20px; min-height: 100vh; }
    
    .card { border-radius: 8px; border: none; }
    .card-header { background-color: #17a2b8; color: white; font-weight: bold; }
</style>

<div class="wrapper">
    <nav id="sidebar">
        <?php include 'layout/sidebar.php'; ?>
    </nav>

    <div id="content">
        
        <div class="top-nav d-flex justify-content-between align-items-center mb-4 bg-white p-3 shadow-sm rounded">
            <h5 class="mb-0 text-uppercase fw-bold"><i class="bi bi-box me-2"></i> Data Kriteria</h5>
            <span class="text-muted text-uppercase">Admin <i class="bi bi-person-circle ms-1"></i></span>
        </div>

        <div class="card shadow-sm">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 text-uppercase">Daftar Kriteria Penilaian</h6>
                <button type="button" class="btn btn-light btn-sm fw-bold text-info" data-bs-toggle="modal" data-bs-target="#modalTambah">
                    <i class="bi bi-plus-circle me-1"></i> Tambah Kriteria
                </button>
            </div>
            
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle text-center">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">No</th>
                                <th width="15%">Kode Kriteria</th>
                                <th class="text-start ps-3">Nama Kriteria</th>
                                <th width="15%">Bobot</th>
                                <th width="15%">Jenis</th>
                                <th width="20%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            $data = mysqli_query($conn, "SELECT * FROM kriteria ORDER BY kode_kriteria ASC");
                            while ($row = mysqli_fetch_assoc($data)) {
                            ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><span class="badge bg-secondary px-3 py-2"><?= $row['kode_kriteria']; ?></span></td>
                                <td class="text-start ps-3 fw-bold"><?= $row['nama_kriteria']; ?></td>
                                <td><strong><?= $row['bobot']; ?></strong></td>
                                <td>
                                    <?php if($row['jenis'] == 'Benefit'): ?>
                                        <span class="badge bg-success">Benefit</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Cost</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-warning btn-sm m-1 text-dark" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $row['id_kriteria']; ?>">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm m-1" data-bs-toggle="modal" data-bs-target="#modalHapus<?= $row['id_kriteria']; ?>">
                                        <i class="bi bi-trash"></i> Hapus
                                    </button>
                                </td>
                            </tr>

                            <div class="modal fade" id="modalEdit<?= $row['id_kriteria']; ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header bg-warning text-dark">
                                            <h5 class="modal-title">Edit Kriteria</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form method="POST">
                                            <div class="modal-body text-start">
                                                <input type="hidden" name="id_kriteria" value="<?= $row['id_kriteria']; ?>">
                                                <div class="mb-3">
                                                    <label class="form-label">Kode Kriteria</label>
                                                    <input type="text" class="form-control" name="kode_kriteria" value="<?= $row['kode_kriteria']; ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Nama Kriteria</label>
                                                    <input type="text" class="form-control" name="nama_kriteria" value="<?= $row['nama_kriteria']; ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Bobot</label>
                                                    <input type="number" step="0.01" class="form-control" name="bobot" value="<?= $row['bobot']; ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Jenis</label>
                                                    <select class="form-select" name="jenis" required>
                                                        <option value="Benefit" <?= ($row['jenis'] == 'Benefit') ? 'selected' : ''; ?>>Benefit</option>
                                                        <option value="Cost" <?= ($row['jenis'] == 'Cost') ? 'selected' : ''; ?>>Cost</option>
                                                    </select>
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

                            <div class="modal fade" id="modalHapus<?= $row['id_kriteria']; ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content border-danger">
                                        <div class="modal-header bg-danger text-white">
                                            <h5 class="modal-title">Konfirmasi Hapus</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form method="POST">
                                            <div class="modal-body text-center py-4">
                                                <input type="hidden" name="id_kriteria" value="<?= $row['id_kriteria']; ?>">
                                                <i class="bi bi-exclamation-triangle text-danger" style="font-size: 4rem;"></i>
                                                <h5 class="mt-3">Yakin ingin menghapus kriteria <strong><?= $row['nama_kriteria']; ?></strong>?</h5>
                                                <p class="text-muted text-sm">Semua nilai penilaian terkait kriteria ini juga akan terhapus.</p>
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
        
    </div>
</div>

<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="bi bi-plus-circle"></i> Tambah Kriteria</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Kode Kriteria</label>
                        <input type="text" class="form-control" name="kode_kriteria" placeholder="Contoh: C1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Kriteria</label>
                        <input type="text" class="form-control" name="nama_kriteria" placeholder="Masukkan nama kriteria..." required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Bobot</label>
                        <input type="number" step="0.01" class="form-control" name="bobot" placeholder="Contoh: 0.25 atau 25" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jenis</label>
                        <select class="form-select" name="jenis" required>
                            <option value="" hidden>Pilih Jenis Kriteria...</option>
                            <option value="Benefit">Benefit (Keuntungan)</option>
                            <option value="Cost">Cost (Biaya)</option>
                        </select>
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