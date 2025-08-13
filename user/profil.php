<?php
session_start();
require '../koneksi.php'; // koneksi ke database

// Cek apakah user sudah login
if (!isset($_SESSION['id_pembeli'])) {
  header("Location: login.php");
  exit;
}

$id_pembeli = $_SESSION['id_pembeli'];

// Ambil data dari database
$query = $conn->prepare("SELECT * FROM pembeli WHERE id = ?");
$query->bind_param("i", $id_pembeli);
$query->execute();
$result = $query->get_result();
$data = $result->fetch_assoc();

// Update profil jika form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $nama = htmlspecialchars($_POST['nama']);
  $email = htmlspecialchars($_POST['email']);
  $alamat = htmlspecialchars($_POST['alamat']);
  $telepon = htmlspecialchars($_POST['telepon']);

  $update = $conn->prepare("UPDATE pembeli SET nama = ?, email = ?, alamat = ?, telepon = ? WHERE id = ?");
  $update->bind_param("ssssi", $nama, $email, $alamat, $telepon, $id_pembeli);

  if ($update->execute()) {
    echo "<script>alert('Profil berhasil diperbarui!'); window.location.href='profil.php';</script>";
    exit;
  } else {
    echo "<script>alert('Gagal memperbarui profil.');</script>";
  }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Profil - Pupukku</title>
  <link rel="stylesheet" href="profil.css" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>
  <div class="content-wrapper">
    <nav class="navbar">
      <div class="logo">Usaha 6 Saudara</div>
      <ul class="nav-links">
        <li><a href="dashboard.php">Beranda</a></li>
        <li><a href="belanja.php">Belanja</a></li>
        <li><a href="promosi.php">Promosi</a></li>
        <li><a href="keranjang.php">Keranjang</a></li>
        <li><a href="pesanan.php">Pesanan</a></li>
        <li><a href="profil.php" class="active">Profil</a></li>
      </ul>
      <div class="btn-logout"><a href="login.php">Logout</a></div>
    </nav>

    <main class="dashboard">
      <h1>Profil Saya</h1>

      <div class="profil-container">
        <div class="profil-info">
          <h2>Informasi Pribadi</h2>
          <p><strong>Nama:</strong> <?= htmlspecialchars($data['nama']) ?></p>
          <p><strong>Email:</strong> <?= htmlspecialchars($data['email']) ?></p>
          <p><strong>Alamat Pengiriman:</strong> <?= htmlspecialchars($data['alamat']) ?></p>
          <p><strong>Nomor Telepon:</strong> <?= htmlspecialchars($data['telepon']) ?></p>
        </div>

        <div class="profil-update">
          <h2>Ubah Profil</h2>
          <form action="profil.php" method="POST">
            <div class="form-group">
              <label for="nama">Nama Lengkap</label>
              <input type="text" id="nama" name="nama" value="<?= htmlspecialchars($data['nama']) ?>" required />
            </div>

            <div class="form-group">
              <label for="email">Email</label>
              <input type="email" id="email" name="email" value="<?= htmlspecialchars($data['email']) ?>" required />
            </div>

            <div class="form-group">
              <label for="alamat">Alamat Pengiriman</label>
              <textarea id="alamat" name="alamat" required><?= htmlspecialchars($data['alamat']) ?></textarea>
            </div>

            <div class="form-group">
              <label for="telepon">Nomor Telepon</label>
              <input type="tel" id="telepon" name="telepon" value="<?= htmlspecialchars($data['telepon']) ?>" required />
            </div>

            <button type="submit" class="btn-submit">Simpan Perubahan</button>
          </form>
        </div>
      </div>
    </main>
  </div>

  <footer>
    <p>&copy; 2025 Usaha 6 Saudara</p>
  </footer>
</body>
</html>
