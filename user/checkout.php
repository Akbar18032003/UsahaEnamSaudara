<?php
include '../koneksi.php';

if (!isset($_GET['id'])) {
  echo "Produk tidak ditemukan.";
  exit;
}

$id = intval($_GET['id']);

// Ambil tanggal sekarang
$tanggal_sekarang = date('Y-m-d');

// Query produk dengan left join promo aktif
$query = "
  SELECT produk.*, promo.harga_diskon
  FROM produk
  LEFT JOIN promo ON produk.id = promo.produk_id 
    AND promo.tanggal_mulai <= ? AND promo.tanggal_berakhir >= ?
  WHERE produk.id = ?
";
$stmt = $conn->prepare($query);
$stmt->bind_param("ssi", $tanggal_sekarang, $tanggal_sekarang, $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
  echo "Produk tidak ditemukan.";
  exit;
}

$produk = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>Checkout - <?= htmlspecialchars($produk['nama_produk']); ?></title>
  <link rel="stylesheet" href="checkout.css" />
  <style>
    .harga-awal {
      text-decoration: line-through;
      color: #888;
      margin-right: 10px;
    }
    .harga-diskon {
      color: red;
      font-weight: bold;
      font-size: 1.2em;
    }
  </style>
</head>
<body>
<header>
  <nav class="navbar">
    <div class="logo">Usaha 6 Saudara</div>
    <ul class="nav-links">
      <li><a href="dashboard.php">Beranda</a></li>
      <li><a href="belanja.php">Belanja</a></li>
      <li><a href="promosi.php">Promosi</a></li>
      <li><a href="keranjang.php">Keranjang</a></li>
      <li><a href="pesanan.php">Pesanan</a></li>
      <li><a href="profil.php">Profil</a></li>
    </ul>
    <div class="btn-logout"><a href="login.php">Logout</a></div>
  </nav>
</header>

<div class="checkout-container">
  <h1>Checkout Produk</h1>
  <div class="produk-detail">
    <img src="../uploads/<?= htmlspecialchars($produk['gambar']); ?>" alt="<?= htmlspecialchars($produk['nama_produk']); ?>" style="max-width: 200px;">
    <h2><?= htmlspecialchars($produk['nama_produk']); ?></h2>
<p>Spesifikasi: <br><br><strong><?= htmlspecialchars($produk['kategori']); ?></strong></p>


<p class="produk-harga">
  <strong>Harga:</strong> 
  <?php if ($produk['harga_diskon'] !== null && $produk['harga_diskon'] < $produk['harga']): ?>
    <span class="harga-awal">Rp <?= number_format($produk['harga'], 0, ',', '.'); ?></span>
    <span class="harga-diskon">Rp <?= number_format($produk['harga_diskon'], 0, ',', '.'); ?></span>
  <?php else: ?>
    <strong>Rp <?= number_format($produk['harga'], 0, ',', '.'); ?></strong>
  <?php endif; ?>
</p>

<p class="produk-stok"><strong>Stok Tersisa:</strong> <?= $produk['stok']; ?> unit</p>

  </div>
  
  <form action="proses_checkout.php" method="POST">
    <input type="hidden" name="produk_id" value="<?= $produk['id']; ?>">
    <label for="jumlah">Jumlah:</label>
    <input type="number" name="jumlah" id="jumlah" value="1" min="1" max="<?= $produk['stok']; ?>" required>
    <br><br>
    <button type="submit">Lanjutkan Pembelian</button>
  </form>
</div>

<footer>
  <p>&copy; 2025 Usaha 6 Saudara</p>
</footer>
</body>
</html>
