<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];

    $query = "DELETE FROM gaji WHERE id = '$id'";

    if (mysqli_query($conn, $query)) {
        header("Location: gaji.php");
    } else {
        echo "Gagal menghapus gaji: " . mysqli_error($conn);
    }
}
?>
