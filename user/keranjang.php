<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['keranjang'])) {
  $_SESSION['keranjang'] = [];
}

// Proses hapus produk dari keranjang
if (isset($_GET['hapus'])) {
  $id = $_GET['hapus'];
  unset($_SESSION['keranjang'][$id]);
  header("Location: keranjang.php");
  exit;
}

// Ambil data produk dan promo aktif dari DB
$produk_keranjang = [];
$total = 0;

if (!empty($_SESSION['keranjang'])) {
  $ids = implode(",", array_keys($_SESSION['keranjang']));
  $tanggal_sekarang = date('Y-m-d');

  $query = $conn->query("
    SELECT produk.*, promo.harga_diskon
    FROM produk
    LEFT JOIN promo ON produk.id = promo.produk_id 
      AND promo.tanggal_mulai <= '$tanggal_sekarang' 
      AND promo.tanggal_berakhir >= '$tanggal_sekarang'
    WHERE produk.id IN ($ids)
  ");

  while ($row = $query->fetch_assoc()) {
    $id = $row['id'];
    $jumlah = $_SESSION['keranjang'][$id];
    $harga_asli = $row['harga'];
    $harga_pakai = ($row['harga_diskon'] !== null && $row['harga_diskon'] < $row['harga']) 
                  ? $row['harga_diskon'] 
                  : $row['harga'];

    $row['jumlah'] = $jumlah;
    $row['harga_pakai'] = $harga_pakai;
    $row['total'] = $harga_pakai * $jumlah;
    $total += $row['total'];
    $produk_keranjang[] = $row;
  }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Keranjang Belanja - Pupukku</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="keranjang.css">
  <style>
    .btn-checkout {
      background-color: #28a745;
      color: white;
      padding: 10px 16px;
      font-size: 16px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      margin-top: 20px;
    }
    .btn-checkout:hover {
      background-color: #218838;
    }
    .harga-asli {
      text-decoration: line-through;
      color: #999;
      font-size: 0.9em;
    }
    .harga-promo {
      color: red;
      font-weight: bold;
    }
  </style>
</head>
<body>
  <div class="content-wrapper">
    <nav class="navbar">
      <div class="logo">Usaha 6 Saudara</div>
      <ul class="nav-links">
        <li><a href="dashboard.php">Beranda</a></li>
        <li><a href="belanja.php">Belanja</a></li>
        <li><a href="promosi.php">Promosi</a></li>
        <li><a href="keranjang.php" class="active">Keranjang</a></li>
        <li><a href="pesanan.php">Pesanan</a></li>
        <li><a href="profil.php">Profil</a></li>
      </ul>
      <div class="btn-logout"><a href="login.php">Logout</a></div>
    </nav>

    <main class="dashboard">
      <h1>Keranjang Belanja</h1>
      <div class="cart-container">
        <?php if (empty($produk_keranjang)) : ?>
          <p>Keranjang kamu masih kosong. Yuk belanja dulu! <a href="belanja.php">Belanja Sekarang</a></p>
        <?php else : ?>
          <table class="cart-items-table">
            <thead>
              <tr>
                <th>Produk</th>
                <th>Harga</th>
                <th>Jumlah</th>
                <th>Total</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($produk_keranjang as $produk) : ?>
                <tr>
                  <td><?= htmlspecialchars($produk['nama_produk']); ?></td>
                  <td>
                    <?php if ($produk['harga_diskon'] !== null && $produk['harga_diskon'] < $produk['harga']) : ?>
                      <span class="harga-asli">Rp <?= number_format($produk['harga'], 0, ',', '.'); ?></span><br>
                      <span class="harga-promo">Rp <?= number_format($produk['harga_diskon'], 0, ',', '.'); ?></span>
                    <?php else: ?>
                      Rp <?= number_format($produk['harga'], 0, ',', '.'); ?>
                    <?php endif; ?>
                  </td>
                  <td><?= $produk['jumlah']; ?></td>
                  <td>Rp <?= number_format($produk['total'], 0, ',', '.'); ?></td>
                  <td><a href="?hapus=<?= $produk['id']; ?>" class="btn-hapus">Hapus</a></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>

          <div class="cart-summary">
            <div class="total">
              <strong>Total Pembelian:</strong> Rp <?= number_format($total, 0, ',', '.'); ?>
            </div>

            <!-- Tombol Checkout -->
            <form action="proses_checkout.php" method="post">
              <button type="submit" class="btn-checkout">Checkout Sekarang</button>
            </form>
          </div>
        <?php endif; ?>
      </div>
    </main>
  </div>

  <footer>
    <p>&copy; 2025 Usaha 6 Saudara.</p>
  </footer>
</body>
</html>
