<?php
session_start();
include '../koneksi.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $email = htmlspecialchars(trim($_POST['email']));
  $password = $_POST['password'];

  $stmt = $conn->prepare("SELECT id, nama, password FROM pembeli WHERE email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $stmt->store_result();

  if ($stmt->num_rows > 0) {
    $stmt->bind_result($id, $nama, $hashed_password);
    $stmt->fetch();

    if (password_verify($password, $hashed_password)) {
      $_SESSION['id_pembeli'] = $id;
      $_SESSION['nama_pembeli'] = $nama;
      header("Location: dashboard.php");
      exit;
    } else {
      $error = "Password salah!";
    }
  } else {
    $error = "Email tidak ditemukan!";
  }

  $stmt->close();
  $conn->close();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login - Pupukku</title>
  <link rel="stylesheet" href="login.css" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>
  <div class="auth-container">
    <h2>Login Akun Anda</h2>
    <form action="" method="POST">
      <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" placeholder="Masukkan email" required />
      </div>
      
      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="Masukkan password" required />
      </div>
      
      <button type="submit" class="btn-submit">Login</button>
      
      <p>Belum punya akun? <a href="registrasi.php">Daftar sekarang</a></p>
    </form>
  </div>

  <?php if (!empty($error)) : ?>
    <div class="modal-overlay" id="modalError">
      <div class="modal">
        <h3>Login Gagal</h3>
        <p><?= $error; ?></p>
        <button onclick="document.getElementById('modalError').style.display='none'">Tutup</button>
      </div>
    </div>
  <?php endif; ?>
</body>
</html>
