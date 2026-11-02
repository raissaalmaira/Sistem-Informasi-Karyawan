<?php
session_start();
include 'config.php';

// Cek apakah karyawan sudah login
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'karyawan') {
    header("Location: login.php");
    exit();
}

// Ambil ID karyawan dari sesi login
$user_id = $_SESSION['user_id'];

// Ambil data karyawan berdasarkan ID
$query = "SELECT * FROM karyawan WHERE user_id = '$user_id'";
$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - Padang TV</title>

    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="includes/style.css">
</head>
<body>
<?php include 'includes/sidebar_karyawan.php'; ?>

<div class="container mt-4">
    <h1>Edit Profile</h1>
    <form action="profile_update.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $data['id'] ?>">

        <div class="mb-3">
            <label for="nama" class="form-label">Nama</label>
            <input type="text" name="nama" class="form-control" id="nama" value="<?= $data['nama'] ?>" required>
        </div>
        <div class="mb-3">
            <label for="tempat_lahir" class="form-label">Tempat Lahir</label>
            <input type="text" name="tempat_lahir" class="form-control" id="tempat_lahir" value="<?= $data['tempat_lahir'] ?>" required>
        </div>
        <div class="mb-3">
            <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
            <input type="date" name="tanggal_lahir" class="form-control" id="tanggal_lahir" value="<?= $data['tanggal_lahir'] ?>" required>
        </div>
        <div class="mb-3">
            <label for="alamat" class="form-label">Alamat</label>
            <textarea name="alamat" class="form-control" id="alamat" required><?= $data['alamat'] ?></textarea>
        </div>
        <div class="mb-3">
            <label for="jabatan" class="form-label">Jabatan</label>
            <input type="text" name="jabatan" class="form-control" id="jabatan" value="<?= $data['jabatan'] ?>" required>
        </div>
        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <input type="text" name="status" class="form-control" id="status" value="<?= ucfirst($data['status_karyawan']) ?>" readonly>
        </div>
        <div class="mb-3">
            <label for="foto" class="form-label">Foto</label><br>
            <img src="uploads/<?= $data['foto'] ?>" width="100" class="mb-2"><br>
            <input type="file" name="foto" class="form-control" id="foto">
        </div>
        <button type="submit" class="btn btn-success">Simpan</button>
        <a href="cetak_kartu_karyawan.php?id=<?= $data['id'] ?>" target="_blank" class="btn btn-primary">Cetak Kartu Karyawan</a>
    </form>
</div>

<?php include "includes/footer.php"; ?>
</body>
</html>
