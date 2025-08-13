<?php
session_start();
include '../koneksi.php';

// Cek apakah admin sudah login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
  header("Location: login-admin.php");
  exit;
}

$admin_id = $_SESSION['admin_id'];
$successMsg = '';
$errorMsg = '';

// Ambil data admin
$query = $conn->prepare("SELECT * FROM admin WHERE id = ?");
$query->bind_param("i", $admin_id);
$query->execute();
$result = $query->get_result();
$admin = $result->fetch_assoc();

// Handle Update Profil
if (isset($_POST['update_profil'])) {
  $nama = trim($_POST['nama']);
  $email = trim($_POST['email']);

  $update = $conn->prepare("UPDATE admin SET nama = ?, email = ? WHERE id = ?");
  $update->bind_param("ssi", $nama, $email, $admin_id);

  if ($update->execute()) {
    $successMsg = "Profil berhasil diperbarui.";
    $admin['nama'] = $nama;
    $admin['email'] = $email;
  } else {
    $errorMsg = "Gagal memperbarui profil.";
  }
}

// Handle Ubah Password
if (isset($_POST['ubah_password'])) {
  $password_lama = $_POST['password_lama'];
  $password_baru = $_POST['password_baru'];
  $konfirmasi_password = $_POST['konfirmasi_password'];

  if (!password_verify($password_lama, $admin['password'])) {
    $errorMsg = "Password lama salah.";
  } elseif ($password_baru !== $konfirmasi_password) {
    $errorMsg = "Konfirmasi password tidak cocok.";
  } else {
    $password_hash = password_hash($password_baru, PASSWORD_DEFAULT);
    $update = $conn->prepare("UPDATE admin SET password = ? WHERE id = ?");
    $update->bind_param("si", $password_hash, $admin_id);

    if ($update->execute()) {
      $successMsg = "Password berhasil diubah.";
    } else {
      $errorMsg = "Gagal mengubah password.";
    }
  }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Profil Admin</title>
  <link rel="stylesheet" href="profil_admin.css">
</head>
<body>
<div class="container-admin">
  <div class="sidebar">
    <div class="logo">USAHA 6 SAUDARA</div>
    <ul class="sidebar-menu">
      <li><a href="dashboard.php">Dashboard</a></li>
      <li><a href="kelola_produk.php">Kelola Produk</a></li>
      <li><a href="kelola_pesanan.php">Kelola Pesanan</a></li>
      <li><a href="kelola_promo.php">Kelola Promo</a></li>
      <li><a href="kelola_pengguna.php">Kelola Pengguna</a></li>
      <li><a href="laporan_penjualan.php">Laporan Penjualan</a></li>
      <li><a href="profil_admin.php" class="active">Profil Admin</a></li>
      <li><a href="logout.php">Logout</a></li>
    </ul>
  </div>

  <div class="main-content">
    <h1>Profil Admin</h1>

    <!-- Informasi Profil (Read-Only) -->
<div class="card">
  <h2>Informasi Profil Admin</h2>
  <div class="form-group">
    <label>Username</label>
    <input type="text" value="<?= htmlspecialchars($admin['username']) ?>" readonly>
  </div>
  <div class="form-group">
    <label>Nama Lengkap</label>
    <input type="text" value="<?= htmlspecialchars($admin['nama']) ?>" readonly>
  </div>
  <div class="form-group">
    <label>Email</label>
    <input type="text" value="<?= htmlspecialchars($admin['email']) ?>" readonly>
  </div>
</div>


    <?php if ($successMsg): ?>
      <div class="success"><?= $successMsg ?></div>
    <?php elseif ($errorMsg): ?>
      <div class="error"><?= $errorMsg ?></div>
    <?php endif; ?>

    <!-- Form Profil -->
    <div class="card">
      <h2>Informasi Profil</h2>
      <form method="POST">
        <label>Nama Lengkap</label>
        <input type="text" name="nama" value="<?= htmlspecialchars($admin['nama']) ?>" required>

        <label>Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($admin['email']) ?>" required>

        <button type="submit" name="update_profil" class="btn-simpan">Simpan Perubahan</button>
      </form>
    </div>

    <!-- Form Ubah Password -->
    <div class="card">
      <h2>Ubah Password</h2>
      <form method="POST">
        <label>Password Lama</label>
        <input type="password" name="password_lama" required>

        <label>Password Baru</label>
        <input type="password" name="password_baru" required>

        <label>Konfirmasi Password Baru</label>
        <input type="password" name="konfirmasi_password" required>

        <button type="submit" name="ubah_password" class="btn-simpan">Ubah Password</button>
      </form>
    </div>
  </div>
</div>
</body>
</html>
