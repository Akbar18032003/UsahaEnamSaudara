<?php
session_start();
if (!isset($_SESSION['pimpinan_logged_in']) || $_SESSION['pimpinan_logged_in'] !== true) {
    header("Location: login-pimpinan.php");
    exit();
}

// Koneksi database
$conn = new mysqli("localhost", "root", "", "pupuk");
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Query join pembeli, produk, pesanan
$sql = "SELECT 
            p.tanggal_pesan,
            pb.nama AS nama_pembeli,
            pr.nama_produk,
            p.jumlah,
            p.total_harga,
            p.status
        FROM pesanan p
        JOIN produk pr ON p.produk_id = pr.id
        JOIN pembeli pb ON p.user_id = pb.id
        ORDER BY p.tanggal_pesan DESC";

$result = $conn->query($sql);
?>


<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Data Transaksi</title>
  <link rel="stylesheet" href="transaksi.css" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>
  <div class="container-pimpinan">
    <div class="sidebar">
      <div class="logo">Usaha 6 Saudara</div>
      <ul class="sidebar-menu">
        <li><a href="dashboard.php">Dashboard</a></li>
        <li><a href="laporan_penjualan.php">Laporan Penjualan</a></li>
        <li><a href="transaksi.php" class="active">Data Transaksi</a></li>
        <li><a href="data_produk.php">Data Produk</a></li>
        <li><a href="kelola_akun.php">Kelola Akun Admin</a></li>
        <li><a href="profil_pimpinan.php">Profil Pimpinan</a></li>
        <li><a href="login-pimpinan.php">Logout</a></li>
      </ul>
    </div>

    <div class="main-content">
      <h1>Data Transaksi</h1>

      <div class="table-box">
        <table>
          <thead>
            <tr>
              <th>No</th>
              <th>Tanggal</th>
              <th>Nama Pembeli</th>
              <th>Produk</th>
              <th>Jumlah</th>
              <th>Total Harga</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $no = 1;
            if ($result && $result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$no}</td>
                        <td>{$row['tanggal_pesan']}</td>
                        <td>{$row['nama_pembeli']}</td>
                        <td>{$row['nama_produk']}</td>
                        <td>{$row['jumlah']}</td>
                        <td>Rp " . number_format($row['total_harga'], 0, ',', '.') . "</td>
                        <td>{$row['status']}</td>
                      </tr>";
                $no++;
              }
            } else {
              echo "<tr><td colspan='7' style='text-align:center;'>Tidak ada transaksi.</td></tr>";
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</body>
</html>
