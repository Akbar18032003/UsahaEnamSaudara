<?php
session_start();
include '../koneksi.php';

// Cek login
if (!isset($_SESSION['pimpinan_id'])) {
  header("Location: login-pimpinan.php");
  exit;
}

$id = $_SESSION['pimpinan_id'];
$data = $conn->query("SELECT * FROM pimpinan WHERE id = $id")->fetch_assoc();

// Update Profil
if (isset($_POST['update_profil'])) {
  $nama = $_POST['nama'];
  $email = $_POST['email'];
  $username = $_POST['username'];

  $stmt = $conn->prepare("UPDATE pimpinan SET nama=?, email=?, username=? WHERE id=?");
  $stmt->bind_param("sssi", $nama, $email, $username, $id);
  $stmt->execute();

  header("Location: profil_pimpinan.php?success=profil");
  exit;
}

// Ubah Password
if (isset($_POST['ubah_password'])) {
  $pass1 = $_POST['password_baru'];
  $pass2 = $_POST['konfirmasi_password'];

  if ($pass1 === $pass2) {
    $hash = password_hash($pass1, PASSWORD_DEFAULT);
    $conn->query("UPDATE pimpinan SET password='$hash' WHERE id=$id");
    header("Location: profil_pimpinan.php?success=password");
    exit;
  } else {
    header("Location: profil_pimpinan.php?error=password");
    exit;
  }
}

$pesan = '';
if (isset($_GET['success'])) {
  if ($_GET['success'] == 'profil') $pesan = 'Profil berhasil diperbarui!';
  if ($_GET['success'] == 'password') $pesan = 'Password berhasil diubah!';
} elseif (isset($_GET['error']) && $_GET['error'] == 'password') {
  $pesan = 'Password dan konfirmasi tidak cocok!';
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Profil Pimpinan</title>
  <link rel="stylesheet" href="profil_pimpinan.css" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
  <div class="container-pimpinan">
    <div class="sidebar">
      <div class="logo">Usaha 6 Saudara</div>
      <ul class="sidebar-menu">
        <li><a href="dashboard.php">Dashboard</a></li>
        <li><a href="laporan_penjualan.php">Laporan Penjualan</a></li>
        <li><a href="transaksi.php">Data Transaksi</a></li>
        <li><a href="data_produk.php">Data Produk</a></li>
        <li><a href="kelola_akun.php">Kelola Akun Admin</a></li>
        <li><a href="profil_pimpinan.php" class="active">Profil Pimpinan</a></li>
        <li><a href="login-pimpinan.php">Logout</a></li>
      </ul>
    </div>

    <div class="main-content">
      <h1>Profil Pimpinan</h1>

      <div class="info">
  <div class="form-group">
    <label>Nama:</label>
    <input type="text" value="<?= htmlspecialchars($data['nama']); ?>" readonly>
  </div>
  <div class="form-group">
    <label>Email:</label>
    <input type="text" value="<?= htmlspecialchars($data['email']); ?>" readonly>
  </div>
  <div class="form-group">
    <label>Username:</label>
    <input type="text" value="<?= htmlspecialchars($data['username']); ?>" readonly>
  </div>
</div>

<!-- Tombol Aksi -->
<div class="actions" style="text-align:center; margin: 20px;">
  <button class="btn-edit" onclick="document.getElementById('formEdit').style.display='flex'">Edit Profil</button>
  <button class="btn-password" onclick="document.getElementById('formPassword').style.display='flex'">Ubah Password</button>
</div>

<!-- Form Edit Profil (Popup) -->
<div class="popup-form" id="formEdit" style="display:none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); justify-content: center; align-items: center; z-index: 999;">
  <form method="POST" class="form-box" style="background: white; padding: 25px 30px; border-radius: 10px; width: 100%; max-width: 400px; box-shadow: 0 0 15px rgba(0,0,0,0.3);">
    <h2>Edit Profil</h2>
    <label>Nama</label>
    <input type="text" name="nama" value="<?= htmlspecialchars($data['nama']); ?>" required>
    <label>Email</label>
    <input type="email" name="email" value="<?= htmlspecialchars($data['email']); ?>" required>
    <label>Username</label>
    <input type="text" name="username" value="<?= htmlspecialchars($data['username']); ?>" required>
    <div class="form-buttons" style="margin-top: 15px;">
      <button type="submit" name="update_profil">Simpan</button>
      <button type="button" onclick="document.getElementById('formEdit').style.display='none'">Batal</button>
    </div>
  </form>
</div>

<!-- Form Ubah Password (Popup) -->
<div class="popup-form" id="formPassword" style="display:none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); justify-content: center; align-items: center; z-index: 999;">
  <form method="POST" class="form-box" style="background: white; padding: 25px 30px; border-radius: 10px; width: 100%; max-width: 400px; box-shadow: 0 0 15px rgba(0,0,0,0.3);">
    <h2>Ubah Password</h2>
    <label>Password Baru</label>
    <input type="password" name="password_baru" required>
    <label>Konfirmasi Password</label>
    <input type="password" name="konfirmasi_password" required>
    <div class="form-buttons" style="margin-top: 15px;">
      <button type="submit" name="ubah_password">Ubah</button>
      <button type="button" onclick="document.getElementById('formPassword').style.display='none'">Batal</button>
    </div>
  </form>
</div>



  <?php if ($pesan) : ?>
  <script>
    Swal.fire({
      icon: '<?= isset($_GET['error']) ? 'error' : 'success' ?>',
      title: '<?= isset($_GET['error']) ? 'Gagal' : 'Berhasil' ?>',
      text: '<?= $pesan ?>',
      showConfirmButton: false,
      timer: 2000
    });
  </script>
  <?php endif; ?>
</body>
</html>
