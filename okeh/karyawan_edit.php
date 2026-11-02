<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $nama = $_POST['nama'];
    $tempat_lahir = $_POST['tempat_lahir'];
    $tanggal_lahir = $_POST['tanggal_lahir'];
    $alamat = $_POST['alamat'];
    $jabatan = $_POST['jabatan'];
    $status_karyawan = $_POST['status_karyawan'];

    // Cek apakah ada foto baru yang diunggah
    if (!empty($_FILES['foto']['name'])) {
        $foto = time() . "_" . $_FILES['foto']['name'];
        move_uploaded_file($_FILES['foto']['tmp_name'], "uploads/" . $foto);
        $query = "UPDATE karyawan SET nama='$nama', tempat_lahir='$tempat_lahir', tanggal_lahir='$tanggal_lahir', alamat='$alamat', 
                  jabatan='$jabatan', status_karyawan='$status_karyawan', foto='$foto' WHERE id='$id'";
    } else {
        $query = "UPDATE karyawan SET nama='$nama', tempat_lahir='$tempat_lahir', tanggal_lahir='$tanggal_lahir', alamat='$alamat', 
                  jabatan='$jabatan', status_karyawan='$status_karyawan' WHERE id='$id'";
    }

    if (mysqli_query($conn, $query)) {
        header("Location: karyawan.php?success=Karyawan berhasil diperbarui");
    } else {
        header("Location: karyawan.php?error=Gagal mengedit karyawan");
    }
}
?>
