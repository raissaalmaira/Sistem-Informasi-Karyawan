<?php
include "config.php";

if (isset($_POST['tambah_user'])) {
    $nama     = mysqli_real_escape_string($conn, $_POST['nama']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $role     = mysqli_real_escape_string($conn, $_POST['role']);

    // 1) Insert ke tabel users
    $sqlUser = "INSERT INTO users (nama, username, password, role)
                VALUES ('$nama', '$username', '$password', '$role')";
    if (mysqli_query($conn, $sqlUser)) {
        // 2) Ambil ID user yang baru dibuat
        $user_id = mysqli_insert_id($conn);

        // 3) Jika role = karyawan, insert juga ke karyawan
        if ($role === 'karyawan') {
            $sqlKar = "INSERT INTO karyawan 
                (nama, tempat_lahir, tanggal_lahir, alamat, jabatan, status_karyawan, foto, user_id)
             VALUES 
                ('$nama', '', '', '', '', 'aktif', '', $user_id)";
            mysqli_query($conn, $sqlKar);
        }

        header("Location: pengelolaan_user.php");
        exit();
    } else {
        echo "Gagal menambahkan user: " . mysqli_error($conn);
    }
} else {
    header("Location: pengelolaan_user.php");
    exit();
}
?>
