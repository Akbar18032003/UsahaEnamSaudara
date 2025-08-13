<?php
include '../koneksi.php';
session_start();

// Cek apakah admin sudah login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
  header("Location: login-admin.php");
  exit();
}

// Hapus pengguna jika tombol hapus diklik
if (isset($_GET['hapus'])) {
  $id_hapus = intval($_GET['hapus']);
  $stmt = $conn->prepare("DELETE FROM pembeli WHERE id = ?");
  $stmt->bind_param("i", $id_hapus);
  $stmt->execute();
  $stmt->close();
  header("Location: kelola_pengguna.php");
  exit();
}

// Ambil data pembeli dari database
$pembeli = [];
$result = $conn->query("SELECT * FROM pembeli");
while ($row = $result->fetch_assoc()) {
  $pembeli[] = $row;
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Kelola Pengguna - Admin</title>
  <link rel="stylesheet" href="kelola_pengguna.css" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
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
        <li><a href="kelola_pengguna.php" class="active">Kelola Pengguna</a></li>
        <li><a href="laporan_penjualan.php">Laporan Penjualan</a></li>
        <li><a href="profil_admin.php">Profil Admin</a></li>
        <li><a href="logout-admin.php">Logout</a></li>
      </ul>
    </div>

    <div class="main-content">
      <h1>Kelola Pengguna</h1>

      <div class="table-container">
        <table>
          <thead>
            <tr>
              <th>Nama</th>
              <th>Email</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php if (count($pembeli) > 0): ?>
              <?php foreach ($pembeli as $p): ?>
                <tr>
                  <td><?= htmlspecialchars($p['nama']) ?></td>
                  <td><?= htmlspecialchars($p['email']) ?></td>
                  <td>
                    <a href="?hapus=<?= $p['id'] ?>" onclick="return confirm('Yakin ingin menghapus pengguna ini?')">
                      <button class="btn-hapus">Hapus</button>
                    </a>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="4">Belum ada pengguna.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</body>
</html>
