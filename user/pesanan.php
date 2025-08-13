<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['id_pembeli'])) {
  echo "<script>alert('Silakan login terlebih dahulu.'); window.location.href='login.php';</script>";
  exit;
}

$id_pembeli = $_SESSION['id_pembeli'];

// Ambil data riwayat pesanan
$query = $conn->prepare("SELECT id, tanggal_pesan, total_harga, status FROM pesanan WHERE user_id = ? ORDER BY tanggal_pesan DESC");
$query->bind_param("i", $id_pembeli);
$query->execute();
$result = $query->get_result();

// Jika request AJAX untuk ambil detail pesanan
if (isset($_GET['get_detail']) && isset($_GET['id'])) {
  $id_pesanan = (int)$_GET['id'];

  $stmt = $conn->prepare("SELECT * FROM pesanan WHERE id = ? AND user_id = ?");
  $stmt->bind_param("ii", $id_pesanan, $id_pembeli);
  $stmt->execute();
  $res = $stmt->get_result();
  $pesanan = $res->fetch_assoc();

  if (!$pesanan) {
    echo "Data pesanan tidak ditemukan.";
    exit;
  }

  echo "<p><strong>Tanggal:</strong> " . date('d-m-Y', strtotime($pesanan['tanggal_pesan'])) . "</p>";
  echo "<p><strong>Status:</strong> " . ucfirst($pesanan['status']) . "</p>";
  echo "<p><strong>Total:</strong> Rp " . number_format($pesanan['total_harga'], 0, ',', '.') . "</p>";

  echo "<table border='1' cellpadding='6' cellspacing='0' width='100%'>";
  echo "<thead><tr><th>Nama Produk</th><th>Jumlah</th><th>Harga Satuan</th><th>Subtotal</th></tr></thead><tbody>";

  $items = json_decode($pesanan['items'], true);
  if (is_array($items)) {
    foreach ($items as $item) {
      $subtotal = $item['jumlah'] * $item['harga'];
echo "<tr>";
echo "<td style='text-align: center;'>" . htmlspecialchars($item['nama_produk']) . "</td>";
echo "<td style='text-align: center;'>" . $item['jumlah'] . "</td>";
echo "<td style='text-align: center;'>Rp " . number_format($item['harga'], 0, ',', '.') . "</td>";
echo "<td style='text-align: center;'>Rp " . number_format($subtotal, 0, ',', '.') . "</td>";
echo "</tr>";

    }
  } else {
    echo "<tr><td colspan='4'>Tidak ada data produk.</td></tr>";
  }

  echo "</tbody></table>";
  exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Pesanan - Pupukku</title>
  <link rel="stylesheet" href="pesanan.css" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    .modal-detail {
      display: none;
      position: fixed;
      top: 50%;
      left: 50%;
      width: 90%;
      max-width: 600px;
      transform: translate(-50%, -50%);
      background: #fff;
      padding: 20px;
      border: 2px solid #ddd;
      box-shadow: 0 0 10px rgba(0,0,0,0.2);
      z-index: 9999;
    }
    .modal-overlay {
      display: none;
      position: fixed;
      top: 0; left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0,0,0,0.5);
      z-index: 9998;
    }
    .close-modal {
      float: right;
      cursor: pointer;
      font-weight: bold;
      font-size: 20px;
    }
  </style>
</head>
<body>
  <div class="modal-overlay" onclick="closeModal()"></div>
  <div class="modal-detail" id="modalDetail">
    <span class="close-modal" onclick="closeModal()">×</span>
    <div id="detailContent">Loading...</div>
  </div>

  <div class="content-wrapper">
    <nav class="navbar">
      <div class="logo">Usaha 6 Saudara</div>
      <ul class="nav-links">
        <li><a href="dashboard.php">Beranda</a></li>
        <li><a href="belanja.php">Belanja</a></li>
        <li><a href="promosi.php">Promosi</a></li>
        <li><a href="keranjang.php">Keranjang</a></li>
        <li><a href="pesanan.php" class="active">Pesanan</a></li>
        <li><a href="profil.php">Profil</a></li>
      </ul>
      <div class="btn-logout"><a href="login.php">Logout</a></div>
    </nav>

    <main class="dashboard">
      <h1>Riwayat Pesanan</h1>
      <table class="tabel-pesanan">
        <thead>
          <tr>
            <th>Tanggal</th>
            <th>Total</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
              <tr>
                <td><?= date('d-m-Y', strtotime($row['tanggal_pesan'])) ?></td>
                <td>Rp <?= number_format($row['total_harga'], 0, ',', '.') ?></td>
                <td><?= ucfirst($row['status']) ?></td>
                <td><a href="#" onclick="showDetail(<?= $row['id'] ?>); return false;">Lihat Detail</a></td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr><td colspan="4">Belum ada pesanan.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </main>
  </div>

  <footer>
    <p>&copy; 2025 Usaha 6 Saudara</p>
  </footer>

  <script>
    function showDetail(id) {
      const overlay = document.querySelector('.modal-overlay');
      const modal = document.getElementById('modalDetail');
      const content = document.getElementById('detailContent');

      overlay.style.display = 'block';
      modal.style.display = 'block';
      content.innerHTML = 'Memuat...';

      fetch('pesanan.php?get_detail=1&id=' + id)
        .then(res => res.text())
        .then(data => {
          content.innerHTML = data;
        })
        .catch(err => {
          content.innerHTML = 'Gagal memuat data.';
        });
    }

    function closeModal() {
      document.querySelector('.modal-overlay').style.display = 'none';
      document.getElementById('modalDetail').style.display = 'none';
    }
  </script>
</body>
</html>
