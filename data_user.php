<?php 
include 'koneksi.php'; 
include 'layout/header.php'; 

// ==========================================================
// LOGIKA CRUD (CREATE, UPDATE, DELETE) DATA USER
// ==========================================================

// 1. TAMBAH USER
if (isset($_POST['tambah'])) {
    $nama = $_POST['nama'];
    $username = $_POST['username'];
    $password = md5($_POST['password']); 
    
    // Cek apakah username sudah ada
    $cek = mysqli_query($conn, "SELECT * FROM users WHERE username='$username'");
    if(mysqli_num_rows($cek) > 0) {
        echo "<script>alert('Username sudah terdaftar! Gunakan username lain.'); window.location='data_user.php';</script>";
    } else {
        mysqli_query($conn, "INSERT INTO users (nama, username, password) VALUES ('$nama', '$username', '$password')");
        echo "<script>alert('Data User berhasil ditambahkan!'); window.location='data_user.php';</script>";
    }
}

// 2. EDIT USER
if (isset($_POST['edit'])) {
    $id = $_POST['id']; // Disesuaikan dengan kolom 'id' di database
    $nama = $_POST['nama'];
    $username = $_POST['username'];
    
    // Jika password diisi baru, maka update password juga
    if (!empty($_POST['password'])) {
        $password = md5($_POST['password']);
        mysqli_query($conn, "UPDATE users SET nama='$nama', username='$username', password='$password' WHERE id='$id'");
    } else {
        mysqli_query($conn, "UPDATE users SET nama='$nama', username='$username' WHERE id='$id'");
    }
    echo "<script>alert('Data User berhasil diubah!'); window.location='data_user.php';</script>";
}

// 3. HAPUS USER
if (isset($_POST['hapus'])) {
    $id = $_POST['id']; // Disesuaikan dengan kolom 'id' di database
    mysqli_query($conn, "DELETE FROM users WHERE id='$id'");
    echo "<script>alert('Data User berhasil dihapus!'); window.location='data_user.php';</script>";
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
            <h5 class="mb-0 text-uppercase fw-bold"><i class="bi bi-people-fill me-2"></i> Data User</h5>
            <span class="text-muted text-uppercase">Admin <i class="bi bi-person-circle ms-1"></i></span>
        </div>

        <div class="card shadow-sm">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 text-uppercase">Daftar Pengguna Sistem</h6>
                <button type="button" class="btn btn-light btn-sm fw-bold text-info" data-bs-toggle="modal" data-bs-target="#modalTambah">
                    <i class="bi bi-plus-circle me-1"></i> Tambah User
                </button>
            </div>
            
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle text-center">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">No</th>
                                <th>Nama Lengkap</th>
                                <th>Username</th>
                                <th width="20%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            // Disesuaikan dengan ID di database
                            $data = mysqli_query($conn, "SELECT * FROM users ORDER BY id DESC");
                            while ($row = mysqli_fetch_assoc($data)) {
                            ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td class="text-start ps-3 fw-bold"><?= $row['nama']; ?></td>
                                <td><?= $row['username']; ?></td>
                                <td>
                                    <button type="button" class="btn btn-warning btn-sm m-1 text-dark" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $row['id']; ?>">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm m-1" data-bs-toggle="modal" data-bs-target="#modalHapus<?= $row['id']; ?>">
                                        <i class="bi bi-trash"></i> Hapus
                                    </button>
                                </td>
                            </tr>

                            <div class="modal fade" id="modalEdit<?= $row['id']; ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header bg-warning text-dark">
                                            <h5 class="modal-title">Edit Data User</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form method="POST">
                                            <div class="modal-body text-start">
                                                <input type="hidden" name="id" value="<?= $row['id']; ?>">
                                                <div class="mb-3">
                                                    <label class="form-label">Nama Lengkap</label>
                                                    <input type="text" class="form-control" name="nama" value="<?= $row['nama']; ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Username</label>
                                                    <input type="text" class="form-control" name="username" value="<?= $row['username']; ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Password Baru</label>
                                                    <input type="password" class="form-control" name="password" placeholder="Kosongkan jika tidak ingin ganti password">
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

                            <div class="modal fade" id="modalHapus<?= $row['id']; ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content border-danger">
                                        <div class="modal-header bg-danger text-white">
                                            <h5 class="modal-title">Konfirmasi Hapus</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form method="POST">
                                            <div class="modal-body text-center py-4">
                                                <input type="hidden" name="id" value="<?= $row['id']; ?>">
                                                <i class="bi bi-exclamation-triangle text-danger" style="font-size: 4rem;"></i>
                                                <h5 class="mt-3">Yakin ingin menghapus user <strong><?= $row['nama']; ?></strong>?</h5>
                                                <p class="text-muted text-sm">User yang dihapus tidak bisa login kembali.</p>
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
                <h5 class="modal-title"><i class="bi bi-person-plus-fill"></i> Tambah User Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control" name="nama" placeholder="Masukkan nama lengkap..." required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" class="form-control" name="username" placeholder="Masukkan username..." required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" class="form-control" name="password" placeholder="Masukkan password..." required>
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