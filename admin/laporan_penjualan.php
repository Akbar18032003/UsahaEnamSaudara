<?php
session_start();
include '../koneksi.php';

date_default_timezone_set("Asia/Jakarta");

$filter = isset($_GET['filter']) ? $_GET['filter'] : 'harian';
$tanggal_sekarang = date('Y-m-d');
$query = "";

if ($filter == 'harian') {
    $query = "SELECT p.*, pr.nama_produk AS nama_produk, pb.nama AS nama_pembeli 
              FROM pesanan p 
              JOIN produk pr ON p.produk_id = pr.id 
              JOIN pembeli pb ON p.user_id = pb.id
              WHERE DATE(p.tanggal_pesan) = '$tanggal_sekarang' 
              AND p.status = 'Selesai'";
} elseif ($filter == 'mingguan') {
    $tanggal_minggu_lalu = date('Y-m-d', strtotime('-7 days'));
    $query = "SELECT p.*, pr.nama_produk AS nama_produk, pb.nama AS nama_pembeli 
              FROM pesanan p 
              JOIN produk pr ON p.produk_id = pr.id 
              JOIN pembeli pb ON p.user_id = pb.id
              WHERE DATE(p.tanggal_pesan) BETWEEN '$tanggal_minggu_lalu' AND '$tanggal_sekarang' 
              AND p.status = 'Selesai'";
} elseif ($filter == 'bulanan') {
    $bulan_ini = date('Y-m');
    $query = "SELECT p.*, pr.nama_produk AS nama_produk, pb.nama AS nama_pembeli 
              FROM pesanan p 
              JOIN produk pr ON p.produk_id = pr.id 
              JOIN pembeli pb ON p.user_id = pb.id
              WHERE DATE_FORMAT(p.tanggal_pesan, '%Y-%m') = '$bulan_ini' 
              AND p.status = 'Selesai'";
}

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Laporan Penjualan - Admin</title>
  <link rel="stylesheet" href="laporan_penjualan.css" />
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
        <li><a href="kelola_pengguna.php">Kelola Pengguna</a></li>
        <li><a href="laporan_penjualan.php" class="active">Laporan Penjualan</a></li>
        <li><a href="profil_admin.php">Profil Admin</a></li>
        <li><a href="login-admin.php">Logout</a></li>
      </ul>
    </div>

    <div class="main-content">
      <h1>Laporan Penjualan</h1>

      <div class="filter-bar">
        <form method="get" style="display: flex; align-items: center; gap: 10px;">
          <label for="filter">Filter:</label>
          <select id="filter" name="filter" onchange="this.form.submit()">
            <option value="harian" <?= $filter == 'harian' ? 'selected' : '' ?>>Harian</option>
            <option value="mingguan" <?= $filter == 'mingguan' ? 'selected' : '' ?>>Mingguan</option>
            <option value="bulanan" <?= $filter == 'bulanan' ? 'selected' : '' ?>>Bulanan</option>
          </select>
<a href="export_laporan_pdf.php?filter=<?= $filter ?>" class="btn-download" target="_blank">📥 Unduh Laporan</a>
        </form>
      </div>

      <div class="table-container">
        <table>
          <thead>
            <tr>
              <th>Tanggal</th>
              <th>Nama Pembeli</th>
              <th>Produk</th>
              <th>Jumlah</th>
              <th>Total</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
              <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                  <td><?= date('d-m-Y H:i', strtotime($row['tanggal_pesan'])) ?></td>
                  <td><?= htmlspecialchars($row['nama_pembeli']) ?></td>
                  <td><?= htmlspecialchars($row['nama_produk']) ?></td>
                  <td><?= htmlspecialchars($row['jumlah']) ?></td>
                  <td>Rp <?= number_format($row['total_harga'], 0, ',', '.') ?></td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr><td colspan="5">Tidak ada data penjualan.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</body>
</html>
