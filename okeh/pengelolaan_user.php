<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
include "config.php";

// Ambil data user dari database
$query_user = "SELECT * FROM users";
$result_user = mysqli_query($conn, $query_user);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pengelolaan User - Padang TV</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="includes/style.css">
</head>
<body>
<div class="wrapper">
    <?php include "includes/sidebar.php"; ?>
    <div class="content-wrapper">
        <div class="content">
            <h1 class="mb-4">Pengelolaan User</h1>

            <!-- Tombol Tambah -->
            <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalTambah">
                <i class="fas fa-plus"></i> Tambah User
            </button>

            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama</th>
                        <th>Username</th>
                        <th>Password</th>
                        <th>Role</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php 
                $modals = []; // Untuk menyimpan semua modal edit & hapus
                while ($u = mysqli_fetch_assoc($result_user)): ?>
                    <tr>
                        <td><?= $u['id'] ?></td>
                        <td><?= htmlspecialchars($u['nama']) ?></td>
                        <td><?= htmlspecialchars($u['username']) ?></td>
                        <td>
                            <div class="input-group input-group-sm">
                                <input type="password" class="form-control" value="<?= htmlspecialchars($u['password']) ?>" id="pw<?= $u['id'] ?>" readonly>
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword(<?= $u['id'] ?>)">
                                    <i class="fas fa-eye" id="icon<?= $u['id'] ?>"></i>
                                </button>
                            </div>
                        </td>
                        <td><?= htmlspecialchars($u['role']) ?></td>
                        <td>
                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $u['id'] ?>">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#modalHapus<?= $u['id'] ?>">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>

                    <!-- Simpan Modal Edit & Hapus di variabel -->
                    <?php
                    ob_start();
                    ?>
                    <!-- Modal Edit -->
                    <div class="modal fade" id="modalEdit<?= $u['id'] ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <form method="post" action="edit_user.php" class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit User</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                    <div class="mb-3">
                                        <label>Nama</label>
                                        <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($u['nama']) ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label>Username</label>
                                        <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($u['username']) ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label>Password</label>
                                        <input type="text" name="password" class="form-control" value="<?= htmlspecialchars($u['password']) ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label>Role</label>
                                        <select name="role" class="form-control" required>
                                            <option value="admin" <?= $u['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                                            <option value="karyawan" <?= $u['role'] == 'karyawan' ? 'selected' : '' ?>>Karyawan</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" name="update_user" class="btn btn-primary">Simpan</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Modal Hapus -->
                    <div class="modal fade" id="modalHapus<?= $u['id'] ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <form method="post" action="hapus_user.php" class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Konfirmasi Hapus</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                    <p>Yakin ingin menghapus user <strong><?= htmlspecialchars($u['nama']) ?></strong>?</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" name="hapus_user" class="btn btn-danger">Hapus</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <?php
                    $modals[] = ob_get_clean();
                    ?>
                <?php endwhile; ?>
                </tbody>
            </table>

            <!-- Modal Tambah User -->
            <div class="modal fade" id="modalTambah" tabindex="-1">
                <div class="modal-dialog">
                    <form method="post" action="tambah_user.php" class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Tambah User</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label>Nama</label>
                                <input type="text" name="nama" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>Username</label>
                                <input type="text" name="username" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>Password</label>
                                <input type="text" name="password" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>Role</label>
                                <select name="role" class="form-control" required>
                                    <option value="admin">Admin</option>
                                    <option value="karyawan">Karyawan</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" name="tambah_user" class="btn btn-primary">Tambah</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tampilkan semua modal edit & hapus di sini -->
            <?php
            if (!empty($modals)) {
                foreach ($modals as $modal) {
                    echo $modal;
                }
            }
            ?>

        </div>
        <?php include "includes/footer.php"; ?>
    </div>
</div>

<script>
function togglePassword(id) {
    const input = document.getElementById('pw' + id);
    const icon = document.getElementById('icon' + id);
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
