<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $karyawan_id = $_POST['karyawan_id'];
    $bulan = $_POST['bulan'];
    $tahun = $_POST['tahun'];
    $gaji_pokok = $_POST['gaji_pokok'];
    $tunjangan = $_POST['tunjangan'];
    $potongan = $_POST['potongan'];

    $query = "INSERT INTO gaji (karyawan_id, bulan, tahun, gaji_pokok, tunjangan, potongan) 
              VALUES ('$karyawan_id', '$bulan', '$tahun', '$gaji_pokok', '$tunjangan', '$potongan')";

    if (mysqli_query($conn, $query)) {
        header("Location: gaji.php");
    } else {
        echo "Gagal menambahkan gaji: " . mysqli_error($conn);
    }
}
?>
