<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Dashboard Pembeli - Penjualan Pupuk</title>
  <link rel="stylesheet" href="dashboard.css" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>
  <div class="content-wrapper">
    <header>
      <nav class="navbar">
        <div class="logo">Usaha 6 Saudara</div>
        <ul class="nav-links">
          <li><a href="dashboard.php" class="active">Beranda</a></li>
          <li><a href="belanja.php">Belanja</a></li>
          <li><a href="promosi.php">Promosi</a></li>
          <li><a href="keranjang.php">Keranjang</a></li>
          <li><a href="pesanan.php">Pesanan</a></li>
          <li><a href="profil.php">Profil</a></li>
        </ul>
        <div class="btn-logout"><a href="login.php">Logout</a></div>
      </nav>
    </header>

    <main class="dashboard">
      <section class="welcome-box">
        <h1>Selamat Datang, Pembeli!</h1>
        <p>Temukan pupuk terbaik untuk tanaman Anda.</p>
      </section>

          <section class="features">
      <a href="belanja.php" class="card">
        <h3>Belanja Pupuk</h3>
        <p>Lihat semua jenis pupuk yang tersedia dan beli langsung dari aplikasi.</p>
      </a>
      
      <a href="pesanan.php" class="card">
        <h3>Pesanan Saya</h3>
        <p>Lacak status pesanan Anda dengan mudah dan cepat.</p>
      </a>
      
      <a href="promosi.php" class="card">
        <h3>Promo & Diskon</h3>
        <p>Jangan lewatkan promo spesial untuk Anda!</p>
      </a>
</section>

    </main>
  </div>

  <footer>
  <p>&copy; 2025 Usaha 6 Saudara</p>
  </footer>
</body>
</html>
