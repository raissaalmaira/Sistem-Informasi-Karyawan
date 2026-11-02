<?php
session_start();
include 'config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username         = mysqli_real_escape_string($conn, $_POST['username']);
    $new_password     = mysqli_real_escape_string($conn, $_POST['new_password']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);

    // Cek kecocokan password
    if ($new_password !== $confirm_password) {
        $error = "Password baru dan konfirmasi tidak cocok.";
    } else {
        // Cek apakah username ada
        $res = mysqli_query($conn, "SELECT id FROM users WHERE username='$username' LIMIT 1");
        if (mysqli_num_rows($res) === 1) {
            // Update password
            mysqli_query(
                $conn,
                "UPDATE users SET password='$new_password' WHERE username='$username'"
            );
            $success = "Password berhasil direset. Silakan <a href='login.php'>login</a>.";
        } else {
            $error = "Username tidak ditemukan.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Reset Password - Padang TV</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>.btn-maroon{background-color:maroon;color:white;} .btn-maroon:hover{background-color:darkred;}</style>
</head>
<body class="bg-light">
  <div class="container vh-100 d-flex justify-content-center align-items-center">
    <div class="card p-4 shadow" style="width: 400px;">
      <h4 class="text-center mb-3">Reset Password</h4>
      <?php if($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
      <?php elseif($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
      <?php endif; ?>
      <?php if (!$success): /* sembunyi form kalau sudah sukses */ ?>
      <form method="POST">
        <div class="mb-3">
          <label>Username</label>
          <input type="text" name="username" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>Password Baru</label>
          <input type="password" name="new_password" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>Konfirmasi Password</label>
          <input type="password" name="confirm_password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-maroon w-100">Reset Password</button>
      </form>
      <?php endif; ?>
      <p class="mt-3 text-center"><a href="login.php">Kembali ke Login</a></p>
    </div>
  </div>
</body>
</html>
