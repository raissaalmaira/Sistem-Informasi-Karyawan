<?php
include 'config.php';
session_start();

// Ambil user_id dari sesi login
$user_id = $_SESSION['user_id'];

// Ambil data karyawan berdasarkan user_id
$query_karyawan = "SELECT * FROM karyawan WHERE user_id = '$user_id'";
$result_karyawan = mysqli_query($conn, $query_karyawan);
$data_karyawan = mysqli_fetch_assoc($result_karyawan);
$karyawan_id = $data_karyawan['id'];

// Ambil semua data gaji berdasarkan karyawan_id
$query_gaji = "SELECT * FROM gaji WHERE karyawan_id = '$karyawan_id' ORDER BY tahun DESC, bulan DESC";
$result_gaji = mysqli_query($conn, $query_gaji);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Cetak Gaji - Padang TV</title>
    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="includes/style.css">
    
</head>
<body>
<?php include "includes/sidebar_karyawan.php"; ?>

<div class="container mt-4">
    <h1>Riwayat Slip Gaji Karyawan</h1>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No</th>
                <th>Bulan</th>
                <th>Tahun</th>
                <th>Gaji Pokok</th>
                <th>Tunjangan</th>
                <th>Potongan</th>
                <th>Total Gaji</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            while ($data_gaji = mysqli_fetch_assoc($result_gaji)) {
                echo "<tr>";
                echo "<td>" . $no++ . "</td>";
                echo "<td>" . $data_gaji['bulan'] . "</td>";
                echo "<td>" . $data_gaji['tahun'] . "</td>";
                echo "<td>Rp " . number_format($data_gaji['gaji_pokok'], 2, ',', '.') . "</td>";
                echo "<td>Rp " . number_format($data_gaji['tunjangan'], 2, ',', '.') . "</td>";
                echo "<td>Rp " . number_format($data_gaji['potongan'], 2, ',', '.') . "</td>";
                echo "<td>Rp " . number_format($data_gaji['total_gaji'], 2, ',', '.') . "</td>";
                echo "<td><a href='cetak_slip_gaji.php?id=" . $data_gaji['id'] . "' target='_blank' class='btn btn-primary'><i class='fas fa-print'></i> Cetak</a></td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<?php include "includes/footer.php"; ?>
</body>
</html>
