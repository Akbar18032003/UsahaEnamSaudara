<?php
session_start();

// Cek apakah pimpinan sudah login
if (!isset($_SESSION['pimpinan_logged_in']) || $_SESSION['pimpinan_logged_in'] !== true) {
  header('Location: login-pimpinan.php');
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

// Hitung total pendapatan
$sqlPendapatan = "SELECT SUM(total_harga) AS total_pendapatan FROM pesanan WHERE status='Selesai'";
$resultPendapatan = $conn->query($sqlPendapatan);
$totalPendapatan = 0;
if ($resultPendapatan && $row = $resultPendapatan->fetch_assoc()) {
  $totalPendapatan = $row['total_pendapatan'];
}

// Hitung jumlah pesanan masuk
$sqlPesanan = "SELECT COUNT(*) AS total_pesanan FROM pesanan";
$resultPesanan = $conn->query($sqlPesanan);
$totalPesanan = 0;
if ($resultPesanan && $row = $resultPesanan->fetch_assoc()) {
  $totalPesanan = $row['total_pesanan'];
}

// Ambil data penjualan per bulan
$dataPenjualan = [];
$sqlGrafik = "SELECT MONTH(tanggal_pesan) AS bulan, SUM(total_harga) AS total 
              FROM pesanan 
              WHERE status='Selesai' 
              GROUP BY bulan ORDER BY bulan ASC";
$resultGrafik = $conn->query($sqlGrafik);
$bulanLabel = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
$penjualanPerBulan = array_fill(1, 12, 0);

if ($resultGrafik) {
  while ($row = $resultGrafik->fetch_assoc()) {
    $penjualanPerBulan[(int)$row['bulan']] = (int)$row['total'];
  }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Dashboard Pimpinan</title>
  <link rel="stylesheet" href="dashboard.css" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
  <div class="container-pimpinan">
    <div class="sidebar">
      <div class="logo">Usaha 6 Saudara</div>
      <ul class="sidebar-menu">
        <li><a href="dashboard_pimpinan.php" class="active">Dashboard</a></li>
        <li><a href="laporan_penjualan.php">Laporan Penjualan</a></li>
        <li><a href="transaksi.php">Data Transaksi</a></li>
        <li><a href="data_produk.php">Data Produk</a></li>
        <li><a href="kelola_akun.php">Kelola Akun Admin</a></li>
        <li><a href="profil_pimpinan.php">Profil Pimpinan</a></li>
        <li><a href="login-pimpinan.php">Logout</a></li>
      </ul>
    </div>

    <div class="main-content">
      <h1>Dashboard Pimpinan</h1>
      <div class="card-container">
        <div class="card">
          <h2>Grafik Penjualan</h2>
          <canvas id="grafikPenjualan" width="400" height="200"></canvas>
        </div>
        <div class="card small">
          <h3>Total Pendapatan</h3>
          <p>Rp <?= number_format($totalPendapatan, 0, ',', '.'); ?></p>
        </div>
        <div class="card small">
          <h3>Pesanan Masuk</h3>
          <p><?= $totalPesanan; ?> Pesanan</p>
        </div>
      </div>
    </div>
  </div>

  <script>
    const ctx = document.getElementById('grafikPenjualan').getContext('2d');
    const chart = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: <?= json_encode($bulanLabel); ?>,
        datasets: [{
          label: 'Total Penjualan per Bulan',
          data: <?= json_encode(array_values($penjualanPerBulan)); ?>,
          backgroundColor: 'rgba(75, 192, 192, 0.7)',
          borderColor: 'rgba(75, 192, 192, 1)',
          borderWidth: 1
        }]
      },
      options: {
        responsive: true,
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              callback: function(value) {
                return 'Rp ' + value.toLocaleString('id-ID');
              }
            }
          }
        },
        plugins: {
          legend: {
            display: false
          }
        }
      }
    });
  </script>
</body>
</html>
