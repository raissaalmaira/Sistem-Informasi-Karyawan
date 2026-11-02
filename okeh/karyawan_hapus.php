<?php
include 'config.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Hapus foto jika ada
    $query = "SELECT foto FROM karyawan WHERE id='$id'";
    $result = mysqli_query($conn, $query);
    $data = mysqli_fetch_assoc($result);
    
    if ($data['foto'] != "") {
        unlink("uploads/" . $data['foto']); // Hapus file foto dari folder uploads
    }

    // Hapus data dari database
    $query = "DELETE FROM karyawan WHERE id='$id'";
    if (mysqli_query($conn, $query)) {
        header("Location: karyawan.php?success=Karyawan berhasil dihapus");
    } else {
        header("Location: karyawan.php?error=Gagal menghapus karyawan");
    }
}
?>
