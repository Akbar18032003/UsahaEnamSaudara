<?php
include '../koneksi.php';

// Tambah produk
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_produk'])) {
  $nama = $_POST['nama_produk'];
  $kategori = $_POST['kategori'];
  $harga = $_POST['harga'];
  $stok = $_POST['stok'];

  // Upload gambar ke folder luar folder admin
  $gambar_name = $_FILES['gambar']['name'];
  $gambar_tmp = $_FILES['gambar']['tmp_name'];
  $upload_dir = __DIR__ . '/../uploads/'; // luar folder admin
  $gambar_path = $gambar_name;

  if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true); // buat folder jika belum ada
  }

  move_uploaded_file($gambar_tmp, $upload_dir . $gambar_name);

  $stmt = $conn->prepare("INSERT INTO produk (nama_produk, kategori, harga, stok, gambar) VALUES (?, ?, ?, ?, ?)");
  $stmt->bind_param("ssdis", $nama, $kategori, $harga, $stok, $gambar_path);
  $stmt->execute();
  header("Location: kelola_produk.php");
  exit;
}

// Hapus produk
if (isset($_GET['hapus'])) {
  $id = $_GET['hapus'];
  $getGambar = $conn->query("SELECT gambar FROM produk WHERE id = $id")->fetch_assoc();
  if ($getGambar && file_exists(__DIR__ . '/../uploads/' . $getGambar['gambar'])) {
  }
  $conn->query("DELETE FROM produk WHERE id = $id");
  header("Location: kelola_produk.php");
  exit;
}

// Edit Produk
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_produk'])) {
  $id = $_POST['id_produk'];
  $nama = $_POST['nama_produk'];
  $kategori = $_POST['kategori'];
  $harga = $_POST['harga'];
  $stok = $_POST['stok'];

  // Cek jika ada file gambar baru diunggah
  if ($_FILES['gambar']['name'] !== '') {
    $gambar_name = $_FILES['gambar']['name'];
    $gambar_tmp = $_FILES['gambar']['tmp_name'];
    $upload_dir = __DIR__ . '/../uploads/';
    move_uploaded_file($gambar_tmp, $upload_dir . $gambar_name);
    $conn->query("UPDATE produk SET nama_produk='$nama', kategori='$kategori', harga='$harga', stok='$stok', gambar='$gambar_name' WHERE id=$id");
  } else {
    $conn->query("UPDATE produk SET nama_produk='$nama', kategori='$kategori', harga='$harga', stok='$stok' WHERE id=$id");
  }

  header("Location: kelola_produk.php");
  exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Kelola Produk - Pupukku</title>
  <link rel="stylesheet" href="kelola_produk.css" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    .popup-form {
      position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background: rgba(0,0,0,0.5);
      display: none;
      justify-content: center;
      align-items: center;
      z-index: 1000;
    }
    .popup-form .form-box {
      background: white;
      padding: 20px;
      border-radius: 10px;
      width: 400px;
    }
    .popup-form input, .popup-form select {
      width: 100%;
      padding: 10px;
      margin: 10px 0;
    }
    .popup-form button {
      padding: 10px 20px;
      margin: 5px;
    }
    .produk-gambar {
      max-height: 60px;
    }
  </style>
</head>
<body>
  <div class="container-admin">
    <div class="sidebar" id="sidebar">
      <div class="logo">USAHA 6 SAUDARA</div>
      <ul class="sidebar-menu">
        <li><a href="dashboard.php">Dashboard</a></li>
        <li><a href="kelola_produk.php" class="active">Kelola Produk</a></li>
        <li><a href="kelola_pesanan.php">Kelola Pesanan</a></li>
        <li><a href="kelola_promo.php">Kelola Promo</a></li>
        <li><a href="kelola_pengguna.php">Kelola Pengguna</a></li>
        <li><a href="laporan_penjualan.php">Laporan Penjualan</a></li>
        <li><a href="profil_admin.php">Profil Admin</a></li>
        <li><a href="login-admin.php">Logout</a></li>
      </ul>
    </div>

    <div class="main-content">
      <h1>Kelola Produk</h1>

      <div class="toolbar">
        <button class="btn-add" id="btnTambah">+ Tambah Produk</button>
      </div>

      <div class="table-container">
        <table border="1" cellpadding="10" cellspacing="0">
          <thead>
            <tr>
              <th>Gambar</th>
              <th>Nama Produk</th>
              <th>Spesifikasi</th>
              <th>Harga</th>
              <th>Stok</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php
              $produk = $conn->query("SELECT * FROM produk");
             while ($row = $produk->fetch_assoc()) {
  echo "<tr>
    <td><img src='../uploads/{$row['gambar']}' class='produk-gambar'></td>
    <td>{$row['nama_produk']}</td>
    <td>{$row['kategori']}</td>
    <td>Rp " . number_format($row['harga'], 0, ',', '.') . "</td>
    <td>{$row['stok']}</td>
    <td>
      <a href='#' class='btn-edit' 
         data-id='{$row['id']}' 
         data-nama='{$row['nama_produk']}' 
         data-kategori='{$row['kategori']}' 
         data-harga='{$row['harga']}' 
         data-stok='{$row['stok']}' 
         data-gambar='{$row['gambar']}'>Edit</a> <br><br>
      <a href='?hapus={$row['id']}' onclick=\"return confirm('Yakin hapus produk ini?')\">Hapus</a>
    </td>
  </tr>";
}
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Popup Form Tambah Produk -->
  <div class="popup-form" id="popupForm">
    <div class="form-box">
      <h2>Tambah Produk</h2>
      <form action="" method="POST" enctype="multipart/form-data">
        <input type="text" name="nama_produk" placeholder="Nama Produk" required>
        <input type="text" name="kategori" placeholder="Kategori" required>
        <input type="number" name="harga" placeholder="Harga" required>
        <input type="number" name="stok" placeholder="Stok" required>
        <input type="file" name="gambar" accept="image/*" required>
        <input type="hidden" name="tambah_produk" value="1">
        <div style="text-align:right;">
          <button type="submit">Simpan</button>
          <button type="button" onclick="popupForm.style.display='none'">Batal</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Popup Form Edit Produk -->
<div class="popup-form" id="popupEditForm">
  <div class="form-box">
    <h2>Edit Produk</h2>
    <form action="" method="POST" enctype="multipart/form-data">
      <input type="hidden" name="id_produk" id="edit_id">
      <input type="text" name="nama_produk" id="edit_nama" placeholder="Nama Produk" required>
      <input type="text" name="kategori" id="edit_kategori" placeholder="Kategori" required>
      <input type="number" name="harga" id="edit_harga" placeholder="Harga" required>
      <input type="number" name="stok" id="edit_stok" placeholder="Stok" required>
      <input type="file" name="gambar" accept="image/*">
      <input type="hidden" name="edit_produk" value="1">
      <div style="text-align:right;">
        <button type="submit">Update</button>
        <button type="button" onclick="popupEditForm.style.display='none'">Batal</button>
      </div>
    </form>
  </div>
</div>


  <script>
    const btnTambah = document.getElementById('btnTambah');
    const popupForm = document.getElementById('popupForm');

    btnTambah.addEventListener('click', () => {
      popupForm.style.display = 'flex';
    });

    popupForm.addEventListener('click', function(e) {
      if (e.target === popupForm) {
        popupForm.style.display = 'none';
      }
    });

    document.querySelectorAll('.btn-edit').forEach(btn => {
  btn.addEventListener('click', () => {
    document.getElementById('edit_id').value = btn.dataset.id;
    document.getElementById('edit_nama').value = btn.dataset.nama;
    document.getElementById('edit_kategori').value = btn.dataset.kategori;
    document.getElementById('edit_harga').value = btn.dataset.harga;
    document.getElementById('edit_stok').value = btn.dataset.stok;
    popupEditForm.style.display = 'flex';
  });
});

const popupEditForm = document.getElementById('popupEditForm');
popupEditForm.addEventListener('click', function(e) {
  if (e.target === popupEditForm) {
    popupEditForm.style.display = 'none';
  }
});

  </script>
</body>
</html>
