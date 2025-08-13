<?php
include '../koneksi.php';

// Tambah promo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_promo'])) {
  $nama = $_POST['nama_promo'];
  $produk_id = $_POST['produk_id'];
  $harga_awal = $_POST['harga_awal'];
  $harga_diskon = $_POST['harga_diskon'];
  $mulai = $_POST['tanggal_mulai'];
  $akhir = $_POST['tanggal_berakhir'];

  $gambar_name = $_FILES['gambar']['name'];
  $gambar_tmp = $_FILES['gambar']['tmp_name'];
  $upload_dir = __DIR__ . '/../uploads/';
  $gambar_path = $gambar_name;

  if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
  }

  move_uploaded_file($gambar_tmp, $upload_dir . $gambar_name);

  $stmt = $conn->prepare("INSERT INTO promo (nama_promo, produk_id, harga_awal, harga_diskon, gambar, tanggal_mulai, tanggal_berakhir) VALUES (?, ?, ?, ?, ?, ?, ?)");
  $stmt->bind_param("siissss", $nama, $produk_id, $harga_awal, $harga_diskon, $gambar_path, $mulai, $akhir);
  $stmt->execute();
  header("Location: kelola_promo.php");
  exit;
}

// Hapus promo
if (isset($_GET['hapus'])) {
  $id = $_GET['hapus'];
  $getGambar = $conn->query("SELECT gambar FROM promo WHERE id = $id")->fetch_assoc();
  if ($getGambar && file_exists(__DIR__ . '/../uploads/' . $getGambar['gambar'])) {
    unlink(__DIR__ . '/../uploads/' . $getGambar['gambar']);
  }
  $conn->query("DELETE FROM promo WHERE id = $id");
  header("Location: kelola_promo.php");
  exit;
}

// Edit Promo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_promo'])) {
  $id = $_POST['promo_id'];
  $nama = $_POST['nama_promo'];
  $produk_id = $_POST['produk_id'];
  $harga_awal = $_POST['harga_awal'];
  $harga_diskon = $_POST['harga_diskon'];
  $mulai = $_POST['tanggal_mulai'];
  $akhir = $_POST['tanggal_berakhir'];
  $gambar_path = $_POST['gambar_lama'];

  if (!empty($_FILES['gambar']['name'])) {
    $gambar_name = $_FILES['gambar']['name'];
    $gambar_tmp = $_FILES['gambar']['tmp_name'];
    $upload_dir = __DIR__ . '/../uploads/';
    if (!is_dir($upload_dir)) {
      mkdir($upload_dir, 0755, true);
    }
    move_uploaded_file($gambar_tmp, $upload_dir . $gambar_name);
    $gambar_path = $gambar_name;

    // Hapus gambar lama
    if (file_exists($upload_dir . $_POST['gambar_lama'])) {
      unlink($upload_dir . $_POST['gambar_lama']);
    }
  }

  $stmt = $conn->prepare("UPDATE promo SET nama_promo=?, produk_id=?, harga_awal=?, harga_diskon=?, gambar=?, tanggal_mulai=?, tanggal_berakhir=? WHERE id=?");
  $stmt->bind_param("siissssi", $nama, $produk_id, $harga_awal, $harga_diskon, $gambar_path, $mulai, $akhir, $id);
  $stmt->execute();
  header("Location: kelola_promo.php");
  exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Kelola Promo</title>
  <link rel="stylesheet" href="kelola_produk.css">
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
    .promo-gambar {
      max-height: 60px;
    }
  </style>
</head>
<body>
  <div class="container-admin">
    <div class="sidebar">
      <div class="logo">USAHA 6 SAUDARA</div>
      <ul class="sidebar-menu">
        <li><a href="dashboard.php">Dashboard</a></li>
        <li><a href="kelola_produk.php">Kelola Produk</a></li>
        <li><a href="kelola_pesanan.php">Kelola Pesanan</a></li>
        <li><a href="kelola_promo.php" class="active">Kelola Promo</a></li>
        <li><a href="kelola_pengguna.php">Kelola Pengguna</a></li>
        <li><a href="laporan_penjualan.php">Laporan Penjualan</a></li>
        <li><a href="profil_admin.php">Profil Admin</a></li>
        <li><a href="login-admin.php">Logout</a></li>
      </ul>
    </div>

    <div class="main-content">
      <h1>Kelola Promo</h1>
      <div class="toolbar">
        <button class="btn-add" id="btnTambah">+ Tambah Promo</button>
      </div>

      <div class="table-container">
        <table border="1" cellpadding="10" cellspacing="0">
          <thead>
            <tr>
              <th>Gambar</th>
              <th>Nama Promo</th>
              <th>Produk</th>
              <th>Harga Awal</th>
              <th>Harga Diskon</th>
              <th>Tanggal Mulai</th>
              <th>Tanggal Berakhir</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php
              $promo = $conn->query("SELECT p.*, pr.nama_produk FROM promo p JOIN produk pr ON p.produk_id = pr.id ORDER BY p.tanggal_mulai DESC");
              while ($row = $promo->fetch_assoc()) {
                echo "<tr>
                  <td><img src='../uploads/{$row['gambar']}' class='promo-gambar'></td>
                  <td>{$row['nama_promo']}</td>
                  <td>{$row['nama_produk']}</td>
                  <td>Rp " . number_format($row['harga_awal'], 0, ',', '.') . "</td>
                  <td>Rp " . number_format($row['harga_diskon'], 0, ',', '.') . "</td>
                  <td>{$row['tanggal_mulai']}</td>
                  <td>{$row['tanggal_berakhir']}</td>
                  <td>
                  <a href='?hapus={$row['id']}' onclick=\"return confirm('Yakin hapus promo ini?')\">Hapus</a> |
                  <a href='#' class='btn-edit' 
                    data-id='{$row['id']}'
                    data-nama='{$row['nama_promo']}'
                    data-produk='{$row['produk_id']}'
                    data-harga_awal='{$row['harga_awal']}'
                    data-harga_diskon='{$row['harga_diskon']}'
                    data-mulai='{$row['tanggal_mulai']}'
                    data-akhir='{$row['tanggal_berakhir']}'
                    data-gambar='{$row['gambar']}'
                  >Edit</a>
                </td>

                </tr>";
              }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Popup Form Tambah Promo -->
  <div class="popup-form" id="popupForm">
    <div class="form-box">
      <h2>Tambah Promo</h2>
      <form action="" method="POST" enctype="multipart/form-data">
        <input type="text" name="nama_promo" placeholder="Nama Promo" required>

        <!-- Dropdown produk -->
        <select name="produk_id" required>
          <option value="">-- Pilih Produk --</option>
          <?php
          $produkList = $conn->query("SELECT id, nama_produk FROM produk");
          while ($p = $produkList->fetch_assoc()) {
            echo "<option value='{$p['id']}'>{$p['nama_produk']}</option>";
          }
          ?>
        </select>

        <input type="number" name="harga_awal" placeholder="Harga Awal" required>
        <input type="number" name="harga_diskon" placeholder="Harga Diskon" required>
        <input type="date" name="tanggal_mulai" required>
        <input type="date" name="tanggal_berakhir" required>
        <input type="file" name="gambar" accept="image/*" required>
        <input type="hidden" name="tambah_promo" value="1">
        <div style="text-align:right;">
          <button type="submit">Simpan</button>
          <button type="button" onclick="popupForm.style.display='none'">Batal</button>
        </div>
      </form>
    </div>
  </div>

  <div class="popup-form" id="popupEditForm">
  <div class="form-box">
    <h2>Edit Promo</h2>
    <form action="" method="POST" enctype="multipart/form-data">
      <input type="hidden" name="promo_id" id="edit_id">
      <input type="text" name="nama_promo" id="edit_nama" required>

      <select name="produk_id" id="edit_produk" required>
        <option value="">-- Pilih Produk --</option>
        <?php
        $produkList = $conn->query("SELECT id, nama_produk FROM produk");
        while ($p = $produkList->fetch_assoc()) {
          echo "<option value='{$p['id']}'>{$p['nama_produk']}</option>";
        }
        ?>
      </select>

      <input type="number" name="harga_awal" id="edit_harga_awal" required>
      <input type="number" name="harga_diskon" id="edit_harga_diskon" required>
      <input type="date" name="tanggal_mulai" id="edit_mulai" required>
      <input type="date" name="tanggal_berakhir" id="edit_akhir" required>
      <input type="file" name="gambar" accept="image/*">
      <input type="hidden" name="gambar_lama" id="edit_gambar_lama">
      <input type="hidden" name="edit_promo" value="1">

      <div style="text-align:right;">
        <button type="submit">Simpan Perubahan</button>
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

    const btnEditList = document.querySelectorAll('.btn-edit');
  const popupEditForm = document.getElementById('popupEditForm');

  btnEditList.forEach(btn => {
    btn.addEventListener('click', () => {
      document.getElementById('edit_id').value = btn.dataset.id;
      document.getElementById('edit_nama').value = btn.dataset.nama;
      document.getElementById('edit_produk').value = btn.dataset.produk;
      document.getElementById('edit_harga_awal').value = btn.dataset.harga_awal;
      document.getElementById('edit_harga_diskon').value = btn.dataset.harga_diskon;
      document.getElementById('edit_mulai').value = btn.dataset.mulai;
      document.getElementById('edit_akhir').value = btn.dataset.akhir;
      document.getElementById('edit_gambar_lama').value = btn.dataset.gambar;
      popupEditForm.style.display = 'flex';
    });
  });

  popupEditForm.addEventListener('click', function(e) {
    if (e.target === popupEditForm) {
      popupEditForm.style.display = 'none';
    }
  });
  </script>
</body>
</html>
