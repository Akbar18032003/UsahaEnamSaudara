<?php
session_start();

// Cek apakah admin sudah login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
  header('Location: login-admin.php');
  exit();
}

// Koneksi ke database
$host = "localhost";
$user = "root";
$pass = "";
$db = "pupuk";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
  die("Koneksi gagal: " . $conn->connect_error);
}

// Ambil 3 pesanan terbaru dan nama pembeli
$pesananTerbaru = [];
$sql = "SELECT pesanan.id AS pesanan_id, pesanan.jumlah, pesanan.tanggal_pesan, pembeli.nama AS nama_pembeli
        FROM pesanan
        JOIN pembeli ON pesanan.user_id = pembeli.id
        ORDER BY pesanan.tanggal_pesan DESC
        LIMIT 3";

$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $pesananTerbaru[] = $row;
  }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Dashboard Admin - Pupukku</title>
  <link rel="stylesheet" href="dashboard.css" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>

  <button class="sidebar-toggle" id="sidebarToggle">&#9776;</button>

  <div class="container-admin">
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
      <div class="logo"><strong>USAHA 6 SAUDARA</strong></div>
      <ul class="sidebar-menu">
        <li><a href="dashboard.php" class="active">Dashboard</a></li>
        <li><a href="kelola_produk.php">Kelola Produk</a></li>
        <li><a href="kelola_pesanan.php">Kelola Pesanan</a></li>
        <li><a href="kelola_promo.php">Kelola Promo</a></li>
        <li><a href="kelola_pengguna.php">Kelola Pengguna</a></li>
        <li><a href="laporan_penjualan.php">Laporan Penjualan</a></li>
        <li><a href="profil_admin.php">Profil Admin</a></li>
        <li><a href="logout-admin.php">Logout</a></li>
      </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
      <h1>Dashboard Admin</h1>

      <!-- Statistik Penjualan -->
      <section class="stats">
        <div class="stat-box">
          <h3>Pesanan Terbaru</h3>
          <ul>
            <?php if (count($pesananTerbaru) > 0): ?>
              <?php foreach ($pesananTerbaru as $pesanan): ?>
                <li><?= $pesanan['jumlah']; ?> produk oleh <?= htmlspecialchars($pesanan['nama_pembeli']); ?></li>
              <?php endforeach; ?>
            <?php else: ?>
              <li>Tidak ada pesanan terbaru.</li>
            <?php endif; ?>
          </ul>
        </div>
      </section>

      <!-- Fitur Pengelolaan -->
      <section class="features">
        <h3>Fitur Pengelolaan</h3>
        <div class="feature-list">
          <div class="feature-box">
            <h4>Kelola Produk</h4>
            <p>Tambah, edit, atau hapus produk pupuk.</p>
            <a href="kelola_produk.php">Kelola Produk</a>
          </div>
          <div class="feature-box">
            <h4>Kelola Pesanan</h4>
            <p>Lihat dan proses pesanan dari pembeli.</p>
            <a href="kelola_pesanan.php">Kelola Pesanan</a>
          </div>
          <div class="feature-box">
            <h4>Kelola Promo</h4>
            <p>Tambah promo atau diskon untuk produk pupuk.</p>
            <a href="kelola_promo.php">Kelola Promo</a>
          </div>
          <div class="feature-box">
            <h4>Kelola Pengguna</h4>
            <p>Lihat dan kelola akun pembeli.</p>
            <a href="kelola_pengguna.php">Kelola Pengguna</a>
          </div>
          <div class="feature-box">
            <h4>Laporan Penjualan</h4>
            <p>Lihat laporan penjualan harian/mingguan/bulanan.</p>
            <a href="laporan_penjualan.php">Laporan Penjualan</a>
          </div>
          <div class="feature-box">
            <h4>Profil</h4>
            <p>Profil dari admin</p>
            <a href="profil_admin.php">Profil Admin</a>
          </div>
        </div>
      </section>
    </div>
  </div>

  <script>
    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('sidebarToggle');
    toggleBtn.addEventListener('click', () => {
      sidebar.classList.toggle('active');
    });
  </script>

</body>
</html>
