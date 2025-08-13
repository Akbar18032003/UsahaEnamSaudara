<?php
include '../koneksi.php';
session_start();

$login_status = ''; // status yang akan dikirim ke JS

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = htmlspecialchars(trim($_POST['username']));
  $password = $_POST['password'];

  $stmt = $conn->prepare("SELECT id, nama, password FROM pimpinan WHERE username = ?");
  $stmt->bind_param("s", $username);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();
    if (password_verify($password, $row['password'])) {
  $_SESSION['pimpinan_id'] = $row['id'];
  $_SESSION['pimpinan_nama'] = $row['nama'];
  $_SESSION['pimpinan_logged_in'] = true; // <<< INI HARUS ADA
  $login_status = 'success';
    } else {
      $login_status = 'failed';
    }
  } else {
    $login_status = 'failed';
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
  <title>Login Pimpinan</title>
  <link rel="stylesheet" href="auth-pimpinan.css">
  <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
</head>
<body>
  <div class="theme-toggle">
    <button id="toggle-theme">🌙</button>
  </div>

  <div class="auth-container">
    <div class="auth-card animate-in">
      <h2>Login Pimpinan</h2>
      <form id="login-form" method="POST" action="login-pimpinan.php">
        <input type="text" name="username" placeholder="Username" required />
        <input type="password" name="password" placeholder="Password" required />
        <div class="form-extra">
        </div>
        <button type="submit">Login</button>
      </form>
    </div>
  </div>

  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="auth-pimpinan.js"></script>

  <?php if ($login_status === 'success'): ?>
    <script>
      Swal.fire({
        icon: 'success',
        title: 'Berhasil login!',
        showConfirmButton: false,
        timer: 1500
      }).then(() => {
        window.location.href = 'dashboard.php';
      });
    </script>
  <?php elseif ($login_status === 'failed'): ?>
    <script>
      Swal.fire({
        icon: 'error',
        title: 'Login gagal!',
        text: 'Username atau password salah.',
        confirmButtonText: 'Coba lagi'
      });
    </script>
  <?php endif; ?>
</body>
</html>
