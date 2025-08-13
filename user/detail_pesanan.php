<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Detail Pesanan - Pupukku</title>
  <link rel="stylesheet" href="detail_pesanan.css" />
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
        <li><a href="pesanan.php" class="active">Pesanan</a></li>
        <li><a href="profil.php">Profil</a></li>
      </ul>
      <div class="btn-logout"><a href="login.php">Logout</a></div>
    </nav>

    <main class="dashboard">
      <h1>Detail Pesanan #12345</h1>

      <div class="order-detail">
        <h2>Rincian Pesanan</h2>
        <p><strong>Produk:</strong> Pupuk Organik, Pupuk Kompos</p>
        <p><strong>Alamat Pengiriman:</strong> Jl. Raya No. 123, Jakarta</p>
        <p><strong>Status Pembayaran:</strong> Bayar di Tempat (COD)</p>
        <p><strong>Status Pesanan:</strong> Selesai</p>
      </div>

      <a href="pesanan.php" class="btn-beli">Kembali ke Riwayat Pesanan</a>
    </main>
  </div>

  <footer>
    <p>&copy; 2025 Usaha 6 Saudara</p>
  </footer>
</body>
</html>
