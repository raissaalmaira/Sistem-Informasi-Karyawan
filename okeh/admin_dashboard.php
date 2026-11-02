<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
include "config.php";

// Ambil total karyawan
$query_karyawan = "SELECT COUNT(*) AS total_karyawan FROM karyawan";
$result_karyawan = mysqli_query($conn, $query_karyawan);
$row_karyawan = mysqli_fetch_assoc($result_karyawan);
$total_karyawan = $row_karyawan['total_karyawan'];

// Ambil total gaji yang telah dibayarkan
$query_gaji = "SELECT SUM(total_gaji) AS total_gaji FROM gaji";
$result_gaji = mysqli_query($conn, $query_gaji);
$row_gaji = mysqli_fetch_assoc($result_gaji);
$total_gaji = $row_gaji['total_gaji'];

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Admin - Padang TV</title>

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
    <?php include "includes/sidebar.php"; ?>

    <div class="content-wrapper">
        <div class="content">
            <h1 class="mb-4">Dashboard Admin</h1>
            <p class="mb-4">Selamat datang di Sistem Informasi Karyawan Padang TV.</p>

            <div class="row">
                <div class="col-md-6">
                    <div class="card shadow p-3">
                        <h5><i class="fas fa-users"></i> Total Karyawan</h5>
                        <p class="fs-3"><?= $total_karyawan ?> Orang</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card shadow p-3">
                        <h5><i class="fas fa-money-bill"></i> Total Gaji Dibayar</h5>
                        <p class="fs-3">Rp <?= number_format($total_gaji,0,',','.')?></p>
                    </div>
                </div>
            </div>
        </div>

        <?php include "includes/footer.php"; ?>
    </div>
</div>

</body>
</html>
