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

// Cek jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form
    $id = $_POST['id'];
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $tempat_lahir = mysqli_real_escape_string($conn, $_POST['tempat_lahir']);
    $tanggal_lahir = $_POST['tanggal_lahir'];
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $jabatan = mysqli_real_escape_string($conn, $_POST['jabatan']);
    
    // Menangani foto jika diupload
    $foto = $_FILES['foto']['name'];
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($foto);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Periksa apakah file gambar valid
    if ($foto) {
        // Cek apakah file gambar adalah gambar nyata
        $check = getimagesize($_FILES['foto']['tmp_name']);
        if ($check === false) {
            $uploadOk = 0;
            echo "File yang diupload bukan gambar.";
        }

        // Cek ukuran file (maks 2MB)
        if ($_FILES['foto']['size'] > 2000000) {
            $uploadOk = 0;
            echo "Ukuran file terlalu besar.";
        }

        // Cek format gambar
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
            $uploadOk = 0;
            echo "Hanya file JPG, JPEG, PNG & GIF yang diperbolehkan.";
        }

        // Jika semua cek lolos, upload file
        if ($uploadOk === 1) {
            if (!move_uploaded_file($_FILES['foto']['tmp_name'], $target_file)) {
                echo "Terjadi kesalahan saat mengupload gambar.";
                $uploadOk = 0;
            }
        }
    }

    // Jika tidak ada masalah dengan upload foto atau tidak ada foto baru
    if ($uploadOk === 1) {
        if ($foto) {
            // Update data karyawan dengan foto baru
            $query = "UPDATE karyawan SET 
                    nama = '$nama', 
                    tempat_lahir = '$tempat_lahir', 
                    tanggal_lahir = '$tanggal_lahir', 
                    alamat = '$alamat', 
                    jabatan = '$jabatan', 
                    foto = '$foto'
                    WHERE id = '$id' AND user_id = '$user_id'";
        } else {
            // Update data tanpa foto baru
            $query = "UPDATE karyawan SET 
                    nama = '$nama', 
                    tempat_lahir = '$tempat_lahir', 
                    tanggal_lahir = '$tanggal_lahir', 
                    alamat = '$alamat', 
                    jabatan = '$jabatan'
                    WHERE id = '$id' AND user_id = '$user_id'";
        }

        // Eksekusi query
        if (mysqli_query($conn, $query)) {
            // Redirect ke halaman profil setelah update
            header("Location: edit_profile.php");
            exit();
        } else {
            echo "Gagal mengupdate profil: " . mysqli_error($conn);
        }
    }
}
?>
