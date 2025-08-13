<?php
session_start();
include '../koneksi.php';

$user_id = $_SESSION['id_pembeli'] ?? 0;
if (!$user_id) {
    echo "<script>alert('Silakan login terlebih dahulu.'); window.location.href='login.php';</script>";
    exit;
}

$items = [];
$total = 0;

// Ambil dari post jika langsung beli
if (isset($_POST['produk_id']) && isset($_POST['jumlah'])) {
    $produk_id = intval($_POST['produk_id']);
    $jumlah = intval($_POST['jumlah']);

    $stmt = $conn->prepare("SELECT pr.*, p.harga_diskon 
        FROM produk pr 
        LEFT JOIN promo p ON pr.id = p.produk_id 
            AND p.tanggal_mulai <= CURDATE() AND p.tanggal_berakhir >= CURDATE()
        WHERE pr.id = ?");
    $stmt->bind_param("i", $produk_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo "<script>alert('Produk tidak ditemukan.'); window.location.href='belanja.php';</script>";
        exit;
    }

    $produk = $result->fetch_assoc();
    if ($produk['stok'] < $jumlah) {
        echo "<script>alert('Stok tidak mencukupi.'); window.location.href='belanja.php';</script>";
        exit;
    }

    $harga_final = $produk['harga_diskon'] ?? $produk['harga'];

    $items = [[
        'produk_id' => $produk_id,
        'nama_produk' => $produk['nama_produk'],
        'jumlah' => $jumlah,
        'harga' => $harga_final
    ]];
    $total = $harga_final * $jumlah;
} elseif (!empty($_SESSION['keranjang'])) {
    foreach ($_SESSION['keranjang'] as $id_produk => $jumlah) {
        $stmt = $conn->prepare("SELECT pr.*, p.harga_diskon 
            FROM produk pr 
            LEFT JOIN promo p ON pr.id = p.produk_id 
                AND p.tanggal_mulai <= CURDATE() AND p.tanggal_berakhir >= CURDATE()
            WHERE pr.id = ?");
        $stmt->bind_param("i", $id_produk);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 0) continue;

        $produk = $result->fetch_assoc();
        if ($produk['stok'] < $jumlah) {
            echo "<script>alert('Stok produk {$produk['nama_produk']} tidak mencukupi.'); window.location.href='keranjang.php';</script>";
            exit;
        }

        $harga_final = $produk['harga_diskon'] ?? $produk['harga'];

        $items[] = [
            'produk_id' => $id_produk,
            'nama_produk' => $produk['nama_produk'],
            'jumlah' => $jumlah,
            'harga' => $harga_final
        ];
        $total += $harga_final * $jumlah;
    }

    if (empty($items)) {
        echo "<script>alert('Keranjang kosong atau produk tidak ditemukan.'); window.location.href='keranjang.php';</script>";
        exit;
    }
} else {
    echo "<script>alert('Data produk tidak valid.'); window.location.href='belanja.php';</script>";
    exit;
}

if (isset($_POST['bayar'])) {
    $metode = $_POST['metode'] ?? 'Transfer Bank';

    $cek_user = $conn->prepare("SELECT id FROM pembeli WHERE id = ?");
    $cek_user->bind_param("i", $user_id);
    $cek_user->execute();
    $hasil_user = $cek_user->get_result();
    if ($hasil_user->num_rows === 0) {
        echo "<script>alert('Akun tidak valid.'); window.location.href='belanja.php';</script>";
        exit;
    }

    $bukti_nama = null;
    if ($metode === "Transfer Bank" && isset($_FILES['bukti']) && $_FILES['bukti']['error'] === 0) {
        $folder = "../uploads/";
        if (!is_dir($folder)) mkdir($folder, 0777, true);
        $bukti_nama = uniqid() . '_' . basename($_FILES['bukti']['name']);
        move_uploaded_file($_FILES['bukti']['tmp_name'], $folder . $bukti_nama);
    }

    $items_json = json_encode($items);
    $jumlah_total = array_sum(array_column($items, 'jumlah'));
    $produk_id_input = $items[0]['produk_id'];

    $query = $conn->prepare("INSERT INTO pesanan 
        (user_id, produk_id, items, jumlah, total_harga, metode_pembayaran, bukti_pembayaran, tanggal_pesan, status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), 'Menunggu Konfirmasi')");
    $query->bind_param("iisidss", $user_id, $produk_id_input, $items_json, $jumlah_total, $total, $metode, $bukti_nama);

    if ($query->execute()) {
        foreach ($items as $item) {
            $update = $conn->prepare("UPDATE produk SET stok = stok - ? WHERE id = ?");
            $update->bind_param("ii", $item['jumlah'], $item['produk_id']);
            $update->execute();
        }

        unset($_SESSION['keranjang']);
        echo "<script>alert('Pesanan berhasil dibuat!'); window.location.href='pesanan.php';</script>";
        exit;
    } else {
        echo "<script>alert('Terjadi kesalahan saat menyimpan pesanan.');</script>";
    }
}
?>


<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Checkout</title>
  <link rel="stylesheet" href="proses_checkout.css">
  <style>
    #rekening-box {
      display: none;
      margin-top: 15px;
      background-color: #f7f7f7;
      padding: 12px;
      border-radius: 6px;
    }
    #bukti-upload {
      margin-top: 10px;
    }
    button[disabled] {
      background-color: #ccc;
      cursor: not-allowed;
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

  <div class="checkout-box">
    <h2>Konfirmasi Pembayaran</h2>

    <div class="produk-info">
      <?php foreach ($items as $item): ?>
        <div class="item-produk">
          <p><strong>Nama Produk:</strong> <?= htmlspecialchars($item['nama_produk']) ?></p>
          <p><strong>Harga Satuan:</strong> Rp<?= number_format($item['harga'], 0, ',', '.') ?></p>
          <p><strong>Jumlah:</strong> <?= $item['jumlah'] ?></p>
          <hr>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="total">
      <p><strong>Total Pembayaran:</strong> Rp<?= number_format($total, 0, ',', '.') ?></p>
    </div>

    <form method="post" action="" enctype="multipart/form-data" id="formCheckout">
      <label for="metode">Metode Pembayaran:</label>
      <select name="metode" id="metode" onchange="toggleRekening()" required>
        <option value="Transfer Bank">Transfer Bank</option>
        <option value="COD">Bayar di Tempat (COD)</option>
      </select>

      <div id="rekening-box">
        <p>Transfer ke rekening:</p>
        <p><strong>Bank BRI - 1234 5678 9012 3456</strong><br>a.n. <strong>Usaha 6 Saudara</strong></p>

        <div id="bukti-upload">
          <label for="bukti">Upload Bukti Transfer:</label><br>
          <input type="file" name="bukti" id="bukti" accept="image/*">
        </div>
      </div>

      <?php if (isset($_POST['produk_id']) && isset($_POST['jumlah'])): ?>
  <input type="hidden" name="produk_id" value="<?= htmlspecialchars($_POST['produk_id']) ?>">
  <input type="hidden" name="jumlah" value="<?= htmlspecialchars($_POST['jumlah']) ?>">
<?php endif; ?>


      <button type="submit" name="bayar" id="btnBayar" disabled>Bayar Sekarang</button>
    </form>
  </div>

  <footer>
    <p>&copy; 2025 Usaha 6 Saudara</p>
  </footer>

  <script>
  function toggleRekening() {
    const metode = document.getElementById("metode").value;
    const rekeningBox = document.getElementById("rekening-box");
    const buktiInput = document.getElementById("bukti");
    const btnBayar = document.getElementById("btnBayar");

    if (metode === "Transfer Bank") {
      rekeningBox.style.display = "block";
      btnBayar.disabled = !buktiInput.value;
    } else {
      rekeningBox.style.display = "none";
      btnBayar.disabled = false;
    }
  }

  document.getElementById("metode").addEventListener("change", toggleRekening);

  document.getElementById("bukti").addEventListener("change", function () {
    const btnBayar = document.getElementById("btnBayar");
    const metode = document.getElementById("metode").value;
    btnBayar.disabled = metode === "Transfer Bank" && this.files.length === 0;
  });

  window.onload = toggleRekening;
  </script>
</body>
</html>
