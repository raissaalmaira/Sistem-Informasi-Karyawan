<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST['nama'];
    $tempat_lahir = $_POST['tempat_lahir'];
    $tanggal_lahir = $_POST['tanggal_lahir'];
    $alamat = $_POST['alamat'];
    $jabatan = $_POST['jabatan'];
    $status_karyawan = $_POST['status_karyawan'];

    // Upload foto
    $foto = "";
    if (!empty($_FILES['foto']['name'])) {
        $foto = time() . "_" . $_FILES['foto']['name'];
        move_uploaded_file($_FILES['foto']['tmp_name'], "uploads/" . $foto);
    }

    // Simpan ke database
    $query = "INSERT INTO karyawan (nama, tempat_lahir, tanggal_lahir, alamat, jabatan, status_karyawan, foto) 
              VALUES ('$nama', '$tempat_lahir', '$tanggal_lahir', '$alamat', '$jabatan', '$status_karyawan', '$foto')";
    
    if (mysqli_query($conn, $query)) {
        header("Location: karyawan.php?success=Karyawan berhasil ditambahkan");
    } else {
        header("Location: karyawan.php?error=Gagal menambah karyawan");
    }
}
?>
