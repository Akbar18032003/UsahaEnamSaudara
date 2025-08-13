<?php
include '../koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $nama     = htmlspecialchars(trim($_POST['nama']));
  $email    = htmlspecialchars(trim($_POST['email']));
  $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
  $alamat   = htmlspecialchars(trim($_POST['alamat']));
  $telepon  = htmlspecialchars(trim($_POST['telepon']));

  // Cek apakah email sudah terdaftar
  $cek = $conn->prepare("SELECT id FROM pembeli WHERE email = ?");
  $cek->bind_param("s", $email);
  $cek->execute();
  $cek->store_result();

  if ($cek->num_rows > 0) {
    echo "<script>alert('Email sudah terdaftar!'); window.location.href='registrasi.php';</script>";
    exit;
  } else {
    $stmt = $conn->prepare("INSERT INTO pembeli (nama, email, password, alamat, telepon) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $nama, $email, $password, $alamat, $telepon);

    if ($stmt->execute()) {
      echo "<script>alert('Registrasi berhasil! Silakan login.'); window.location.href='login.php';</script>";
      exit;
    } else {
      echo "<script>alert('Terjadi kesalahan saat registrasi.'); window.location.href='registrasi.php';</script>";
      exit;
    }
  }

  $cek->close();
  $stmt->close();
  $conn->close();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Registrasi - Pupukku</title>
  <link rel="stylesheet" href="login.css" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>
  <div class="auth-container">
    <h2>Daftar Akun Baru</h2>
    <form action="" method="POST">
      <div class="form-group">
        <label for="nama">Nama Lengkap</label>
        <input type="text" id="nama" name="nama" placeholder="Masukkan nama lengkap" required />
      </div>
      
      <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" placeholder="Masukkan email" required />
      </div>
      
      
      <div class="form-group">
    <label for="alamat">Alamat Pengiriman</label>
    <input type="text" id="alamat" name="alamat" placeholder="Masukkan alamat lengkap" required />
  </div>

      <div class="form-group">
        <label for="telepon">Nomor Telepon</label>
        <input type="tel" id="telepon" name="telepon" placeholder="Contoh: +62 812-3456-7890" required />
      </div>

      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="Masukkan password" required />
      </div>

      
      <button type="submit" class="btn-submit">Daftar</button>
      <p>Sudah punya akun? <a href="login.php">Login di sini</a></p>
    </form>
  </div>
</body>
</html>
