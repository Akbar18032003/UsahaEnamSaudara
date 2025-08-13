<?php
session_start();
include '../koneksi.php';

$loginStatus = null;
$errorMsg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($_POST['username']);
  $password = $_POST['password'];

  $stmt = $conn->prepare("SELECT * FROM admin WHERE username = ?");
  $stmt->bind_param("s", $username);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows === 1) {
    $admin = $result->fetch_assoc();
    if (password_verify($password, $admin['password'])) {
      $_SESSION['admin_id'] = $admin['id'];
      $_SESSION['admin_username'] = $admin['username'];
        $_SESSION['admin_logged_in'] = true; // <<< INI HARUS ADA
      $loginStatus = "success";
    } else {
      $loginStatus = "error";
      $errorMsg = "Password salah!";
    }
  } else {
    $loginStatus = "error";
    $errorMsg = "Username tidak ditemukan!";
  }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login Admin</title>
  <link rel="stylesheet" href="auth-admin.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
  <div class="theme-toggle">
    <button id="toggle-theme">🌙</button>
  </div>

  <div class="auth-container">
    <div class="auth-card animate-in">
      <h2>Login Admin</h2>
      <form id="login-form" method="POST" onsubmit="return validateForm()">
        <input type="text" id="username" name="username" placeholder="Username" required />
        <input type="password" id="password" name="password" placeholder="Password" required />
        <div class="form-extra">
        </div>
        <button type="submit">Login</button>
      </form>
    </div>
  </div>

  <script>
    function validateForm() {
      const username = document.getElementById('username').value.trim();
      const password = document.getElementById('password').value;

      if (username === "" || password === "") {
        Swal.fire({
          icon: 'warning',
          title: 'Oops!',
          text: 'Username dan Password wajib diisi.'
        });
        return false;
      }

      return true;
    }

    document.addEventListener('DOMContentLoaded', function() {
  const themeToggle = document.getElementById('toggle-theme');
  const body = document.body;
  
  // Load saved theme
  const savedTheme = localStorage.getItem('admin-theme') || 'light';
  body.setAttribute('data-theme', savedTheme);
  updateToggleIcon(savedTheme);
  
  themeToggle.addEventListener('click', function() {
    const currentTheme = body.getAttribute('data-theme');
    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
    
    body.classList.add('theme-transition');
    body.setAttribute('data-theme', newTheme);
    localStorage.setItem('admin-theme', newTheme);
    updateToggleIcon(newTheme);
    
    setTimeout(() => {
      body.classList.remove('theme-transition');
    }, 300);
  });
  
  function updateToggleIcon(theme) {
    themeToggle.textContent = theme === 'dark' ? '☀️' : '🌙';
  }
  
  // Form submission animation
  document.getElementById('login-form').addEventListener('submit', function() {
    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = 'Logging in...';
  });
});

    <?php if ($loginStatus === 'success'): ?>
      Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: 'Login berhasil. Mengarahkan ke dashboard...',
        timer: 2000,
        showConfirmButton: false
      }).then(() => {
        window.location.href = 'dashboard.php';
      });
    <?php elseif ($loginStatus === 'error'): ?>
      Swal.fire({
        icon: 'error',
        title: 'Gagal Login',
        text: '<?= $errorMsg ?>'
      });
    <?php endif; ?>
  </script>
</body>
</html>
