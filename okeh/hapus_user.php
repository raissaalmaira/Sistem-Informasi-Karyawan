<?php
include "config.php";

if (isset($_POST['hapus_user'])) {
    $id = intval($_POST['id']);

    $query = "DELETE FROM users WHERE id=$id";
    if (mysqli_query($conn, $query)) {
        header("Location: pengelolaan_user.php");
        exit();
    } else {
        echo "Gagal menghapus user: " . mysqli_error($conn);
    }
} else {
    header("Location: pengelolaan_user.php");
    exit();
}
?>
