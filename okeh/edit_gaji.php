<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $karyawan_id = $_POST['karyawan_id'];
    $bulan = $_POST['bulan'];
    $tahun = $_POST['tahun'];
    $gaji_pokok = $_POST['gaji_pokok'];
    $tunjangan = $_POST['tunjangan'];
    $potongan = $_POST['potongan'];

    $query = "UPDATE gaji SET 
                karyawan_id = '$karyawan_id',
                bulan = '$bulan',
                tahun = '$tahun',
                gaji_pokok = '$gaji_pokok',
                tunjangan = '$tunjangan',
                potongan = '$potongan'
              WHERE id = '$id'";

    if (mysqli_query($conn, $query)) {
        header("Location: gaji.php");
    } else {
        echo "Gagal mengupdate gaji: " . mysqli_error($conn);
    }
}
?>
