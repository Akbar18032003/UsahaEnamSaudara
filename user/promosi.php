<?php
include '../koneksi.php';
$promo = $conn->query("SELECT * FROM promo ORDER BY tanggal_mulai DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Promo Belanja - Pupukku</title>
  <link rel="stylesheet" href="belanja.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>
  <div class="content-wrapper">
    <nav class="navbar">
      <div class="logo">Usaha 6 Saudara</div>
      <ul class="nav-links">
        <li><a href="dashboard.php">Beranda</a></li>
        <li><a href="belanja.php">Belanja</a></li>
        <li><a href="promosi.php" class="active">Promosi</a></li>
        <li><a href="keranjang.php">Keranjang</a></li>
        <li><a href="pesanan.php">Pesanan</a></li>
        <li><a href="profil.php">Profil</a></li>
      </ul>
      <div class="btn-logout"><a href="login.php">Logout</a></div>
    </nav>

    <main class="dashboard">
      <h1 style="margin-bottom: 1rem;">Promo Belanja</h1>
      <div class="product-grid">
        <?php while ($row = $promo->fetch_assoc()): ?>
        <div class="product-card">
  <img src="../uploads/<?= htmlspecialchars($row['gambar']) ?>" alt="<?= htmlspecialchars($row['nama_promo']) ?>" style="width:100%; height:auto;">
  <h3><?= htmlspecialchars($row['nama_promo']) ?></h3>
  <p>
    <del style="color: gray;">Rp <?= number_format($row['harga_awal'], 0, ',', '.') ?></del><br>
    <strong style="color: red;">Rp <?= number_format($row['harga_diskon'], 0, ',', '.') ?></strong>
  </p>
  <p style="font-size: 0.9rem; color: #555;">
    Berlaku: <?= date("d M Y", strtotime($row['tanggal_mulai'])) ?> - <?= date("d M Y", strtotime($row['tanggal_berakhir'])) ?>
  </p>   
  </form>
</div>

        <?php endwhile; ?>
      </div>
    </main>
  </div>

  <footer>
    <p>&copy; 2025 Usaha 6 Saudara</p>
  </footer>
</body>
</html>
