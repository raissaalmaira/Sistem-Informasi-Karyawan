<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'karyawan') {
    header("Location: login.php");
    exit();
}
include "config.php";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Karyawan - Padang TV</title>

    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="includes/style.css">
</head>
<body>

<div class="wrapper">
    <?php include "includes/sidebar_karyawan.php"; ?>

    <div class="content-wrapper">
        <div class="content">
            <h1 class="mb-4">Dashboard Karyawan</h1>
            <p class="mb-4">Selamat datang, <?= $_SESSION['role'] ?>! Anda berada di halaman Dashboard Karyawan.</p>
            <div class="card shadow p-3">
                <h5><i class="fas fa-info-circle"></i> Informasi Perusahaan</h5>
                <p>Padang TV adalah stasiun televisi lokal yang berfokus pada penyampaian berita dan informasi terkini di Sumatera Barat. Kami berkomitmen untuk memberikan layanan terbaik dan informasi yang akurat bagi masyarakat.</p>
                <p>Terima kasih telah menjadi bagian dari keluarga besar Padang TV!</p>
            </div>
        </div>

        <?php include "includes/footer.php"; ?>
    </div>
</div>

</body>
</html>
