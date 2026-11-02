<?php
include 'config.php';

// Ambil data karyawan
$query = "SELECT * FROM karyawan";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Karyawan - Padang TV</title>

    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
        <!-- Custom CSS -->
    <link rel="stylesheet" href="includes/style.css">
</head>
<body>
<?php include 'includes/sidebar.php'; ?>

<div class="container mt-4">
    <h1>Data Karyawan</h1>
    <div class="d-flex justify-content-start gap-2 mb-3">
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahKaryawanModal">
        <i class="fa fa-plus"></i> Tambah Karyawan
    </button>
    <a href="karyawan_export_pdf.php" class="btn btn-danger">
        <i class="fa fa-file-pdf"></i> 
    </a>
    <a href="karyawan_export_excel.php" class="btn btn-success">
        <i class="fa fa-file-excel"></i> 
    </a>
</div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Foto</th>
                <th>Nama</th>
                <th>Tempat/Tanggal Lahir</th>
                <th>Alamat</th>
                <th>Jabatan</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><img src="uploads/<?= $row['foto'] ?>" width="50"></td>
                    <td><?= $row['nama'] ?></td>
                    <td><?= $row['tempat_lahir'], ',', $row['tanggal_lahir']?></td>
                    <td><?= $row['alamat'] ?></td>
                    <td><?= $row['jabatan'] ?></td>
                    <td><?= ucfirst($row['status_karyawan']) ?></td>
                    <td>
                        <button class="btn btn-warning btn-sm editBtn"
                                data-id="<?= $row['id'] ?>"
                                data-nama="<?= $row['nama'] ?>"
                                data-tempat="<?= $row['tempat_lahir'] ?>"
                                data-tanggal="<?= $row['tanggal_lahir'] ?>"
                                data-alamat="<?= $row['alamat'] ?>"
                                data-jabatan="<?= $row['jabatan'] ?>"
                                data-status="<?= $row['status_karyawan'] ?>"
                                data-bs-toggle="modal" data-bs-target="#editKaryawanModal">
                            <i class="fa fa-edit"></i>
                        </button>
                        <button class="btn btn-danger btn-sm deleteBtn"
                                data-id="<?= $row['id'] ?>"
                                data-nama="<?= $row['nama'] ?>"
                                data-bs-toggle="modal" data-bs-target="#hapusKaryawanModal">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- Modal Tambah Karyawan -->
<div class="modal fade" id="tambahKaryawanModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Karyawan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="karyawan_tambah.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="text" name="nama" class="form-control mb-2" placeholder="Nama" required>
                    <input type="text" name="tempat_lahir" class="form-control mb-2" placeholder="Tempat Lahir" required>
                    <input type="date" name="tanggal_lahir" class="form-control mb-2" required>
                    <textarea name="alamat" class="form-control mb-2" placeholder="Alamat" required></textarea>
                    <input type="text" name="jabatan" class="form-control mb-2" placeholder="Jabatan" required>
                    <select name="status_karyawan" class="form-control mb-2">
                        <option value="aktif">Aktif</option>
                        <option value="tidak aktif">Tidak Aktif</option>
                    </select>
                    <input type="file" name="foto" class="form-control">
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Simpan</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Karyawan -->
<div class="modal fade" id="editKaryawanModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Karyawan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="karyawan_edit.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="id" id="edit-id">
                    <input type="text" name="nama" id="edit-nama" class="form-control mb-2" required>
                    <input type="text" name="tempat_lahir" id="edit-tempat" class="form-control mb-2" required>
                    <input type="date" name="tanggal_lahir" id="edit-tanggal" class="form-control mb-2" required>
                    <textarea name="alamat" id="edit-alamat" class="form-control mb-2" required></textarea>
                    <input type="text" name="jabatan" id="edit-jabatan" class="form-control mb-2" required>
                    <select name="status_karyawan" id="edit-status" class="form-control mb-2">
                        <option value="aktif">Aktif</option>
                        <option value="tidak aktif">Tidak Aktif</option>
                    </select>
                    <input type="file" name="foto" class="form-control">
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Update</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Hapus Karyawan -->
<div class="modal fade" id="hapusKaryawanModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Hapus Karyawan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Yakin ingin menghapus <strong id="hapus-nama"></strong>?</p>
            </div>
            <div class="modal-footer">
                <a href="#" id="confirmHapus" class="btn btn-danger">Hapus</a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            </div>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.editBtn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.getElementById('edit-id').value = this.dataset.id;
        document.getElementById('edit-nama').value = this.dataset.nama;
        document.getElementById('edit-tempat').value = this.dataset.tempat;
        document.getElementById('edit-tanggal').value = this.dataset.tanggal;
        document.getElementById('edit-alamat').value = this.dataset.alamat;
        document.getElementById('edit-jabatan').value = this.dataset.jabatan;
        document.getElementById('edit-status').value = this.dataset.status;
    });
});
document.querySelectorAll('.deleteBtn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.getElementById('hapus-nama').innerText = this.dataset.nama;
        document.getElementById('confirmHapus').href = "karyawan_hapus.php?id=" + this.dataset.id;
    });
});
</script>

<?php include "includes/footer.php"; ?>
</body>
</html>
