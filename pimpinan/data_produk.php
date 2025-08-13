<?php
include '../koneksi.php';
session_start();

// Cek apakah pengguna sudah login
if (!isset($_SESSION['pimpinan_logged_in'])) {
  header("Location: login-pimpinan.php");
  exit();
}

// Ambil semua data produk dari database
$produk = $conn->query("SELECT * FROM produk ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Data Produk</title>
  <link rel="stylesheet" href="data_produk.css" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>
  <div class="container-pimpinan">
    <div class="sidebar">
      <div class="logo">Usaha 6 Saudara</div>
      <ul class="sidebar-menu">
        <li><a href="dashboard.php">Dashboard</a></li>
        <li><a href="laporan_penjualan.php">Laporan Penjualan</a></li>
        <li><a href="transaksi.php">Data Transaksi</a></li>
        <li><a href="data_produk.php" class="active">Data Produk</a></li>
        <li><a href="kelola_akun.php">Kelola Akun Admin</a></li>
        <li><a href="profil_pimpinan.php">Profil Pimpinan</a></li>
        <li><a href="login-pimpinan.php">Logout</a></li>
      </ul>
    </div>

    <div class="main-content">
      <h1>Data Produk</h1>

      <div class="table-box">
        <table>
          <thead>
            <tr>
              <th>No</th>
              <th>Gambar</th>
              <th>Nama Produk</th>
              <th>Spesifikasi</th>
              <th>Stok</th>
              <th>Harga Satuan</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $no = 1;
            while ($row = $produk->fetch_assoc()) :
            ?>
              <tr>
                <td><?= $no++; ?></td>
                <td><img src="../uploads/<?= htmlspecialchars($row['gambar']); ?>" alt="<?= htmlspecialchars($row['nama_produk']); ?>"></td>
                <td><?= htmlspecialchars($row['nama_produk']); ?></td>
                <td><?= htmlspecialchars($row['kategori']); ?></td>
                <td><?= htmlspecialchars($row['stok']); ?></td>
                <td>Rp <?= number_format($row['harga'], 0, ',', '.'); ?></td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</body>
</html>
