<?php
include "config.php";

if (isset($_POST['update_user'])) {
    $id = intval($_POST['id']);
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);

    $query = "UPDATE users SET nama='$nama', username='$username', password='$password', role='$role' WHERE id=$id";
    if (mysqli_query($conn, $query)) {
        header("Location: pengelolaan_user.php");
        exit();
    } else {
        echo "Gagal memperbarui user: " . mysqli_error($conn);
    }
} else {
    header("Location: pengelolaan_user.php");
    exit();
}
?>
