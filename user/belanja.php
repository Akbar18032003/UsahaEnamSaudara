<?php
include '../koneksi.php';
session_start();

// Proses tambah keranjang jika ada POST
$show_alert = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['produk_id'])) {
  $id = $_POST['produk_id'];

  if (!isset($_SESSION['keranjang'])) {
    $_SESSION['keranjang'] = [];
  }

  if (isset($_SESSION['keranjang'][$id])) {
    $_SESSION['keranjang'][$id]++;
  } else {
    $_SESSION['keranjang'][$id] = 1;
  }

  $show_alert = true; // Untuk menampilkan SweetAlert
}

// Ambil tanggal sekarang untuk cek promo aktif
$tanggal_sekarang = date('Y-m-d');

// Query produk + join promo aktif (promo.tanggal_mulai <= sekarang <= promo.tanggal_berakhir)
$query = "
  SELECT produk.*, promo.harga_diskon
  FROM produk
  LEFT JOIN promo ON produk.id = promo.produk_id 
    AND promo.tanggal_mulai <= ? AND promo.tanggal_berakhir >= ?
  ORDER BY produk.id DESC
";

$stmt = $conn->prepare($query);
$stmt->bind_param("ss", $tanggal_sekarang, $tanggal_sekarang);
$stmt->execute();
$produk = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Belanja Pupuk - Pupukku</title>
  <link rel="stylesheet" href="belanja.css" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    .harga-awal {
      text-decoration: line-through;
      color: #888;
      margin-right: 8px;
    }
    .harga-diskon {
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
        <li><a href="belanja.php" class="active">Belanja</a></li>
        <li><a href="promosi.php">Promosi</a></li>
        <li><a href="keranjang.php">Keranjang</a></li>
        <li><a href="pesanan.php">Pesanan</a></li>
        <li><a href="profil.php">Profil</a></li>
      </ul>
      <div class="btn-logout"><a href="login.php">Logout</a></div>
    </nav>

    <main class="dashboard">
      <h1 style="margin-bottom: 1rem;">Belanja Pupuk</h1>
      <div class="product-grid">
        <?php while ($row = $produk->fetch_assoc()) : ?>
          <div class="product-card">
            <img src="../uploads/<?= htmlspecialchars($row['gambar']); ?>" alt="<?= htmlspecialchars($row['nama_produk']); ?>" class="gambar-produk-kecil" />
            <h3><?= htmlspecialchars($row['nama_produk']); ?></h3>

            <?php if ($row['harga_diskon'] !== null && $row['harga_diskon'] < $row['harga']): ?>
              <p>
                <span class="harga-awal">Rp <?= number_format($row['harga'], 0, ',', '.'); ?></span>
                <span class="harga-diskon">Rp <?= number_format($row['harga_diskon'], 0, ',', '.'); ?></span>
              </p>
            <?php else: ?>
              <p>Rp <?= number_format($row['harga'], 0, ',', '.'); ?></p>
            <?php endif; ?>

            <p><strong>Stok:</strong> <?= $row['stok']; ?> unit</p>

            <a href="checkout.php?id=<?= $row['id']; ?>">
              <button type="button" class="btn-beli" <?= ($row['stok'] <= 0 ? 'disabled' : '') ?>>
                <?= ($row['stok'] <= 0 ? 'Stok Habis' : 'Beli Sekarang') ?>
              </button>
            </a>

            <form action="" method="POST" style="margin-top: 0.5rem;">
              <input type="hidden" name="produk_id" value="<?= $row['id']; ?>" />
              <button type="submit" class="btn-beli" <?= ($row['stok'] <= 0 ? 'disabled' : '') ?>>
                <?= ($row['stok'] <= 0 ? 'Stok Habis' : 'Tambah ke Keranjang') ?>
              </button>
            </form>
          </div>
        <?php endwhile; ?>
      </div>
    </main>
  </div>

  <footer>
    <p>&copy; 2025 Usaha 6 Saudara</p>
  </footer>

  <?php if ($show_alert): ?>
    <script>
      Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: 'Produk berhasil ditambahkan ke keranjang!',
        showConfirmButton: false,
        timer: 1500
      }).then(() => {
        window.location.href = 'keranjang.php';
      });
    </script>
  <?php endif; ?>
</body>
</html>
