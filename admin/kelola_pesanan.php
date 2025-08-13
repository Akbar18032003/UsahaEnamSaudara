<?php
session_start();
include '../koneksi.php';

$notif = '';

// Proses ubah status
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ubah_status'])) {
    $id = $_POST['id_pesanan'];
    $status = $_POST['status'];
    $query = "UPDATE pesanan SET status = '$status' WHERE id = $id";
    if (mysqli_query($conn, $query)) {
        header("Location: kelola_pesanan.php?status=berhasil");
        exit;
    } else {
        header("Location: kelola_pesanan.php?status=gagal");
        exit;
    }
}

// Filter status
$status_filter = isset($_GET['filter_status']) ? $_GET['filter_status'] : '';

// Query pesanan
$query = "SELECT pesanan.id, pembeli.nama, pesanan.tanggal_pesan, pesanan.metode_pembayaran, pesanan.status, pesanan.total_harga, pesanan.items, pesanan.bukti_pembayaran 
          FROM pesanan 
          JOIN pembeli ON pesanan.user_id = pembeli.id";


if (!empty($status_filter)) {
    $status_filter_escaped = mysqli_real_escape_string($conn, $status_filter);
    $query .= " WHERE pesanan.status = '$status_filter_escaped'";
}

$query .= " ORDER BY pesanan.tanggal_pesan DESC";
$result = $conn->query($query);

// Ambil data produk
$produkList = [];
$produkResult = $conn->query("SELECT id, nama_produk, harga FROM produk");
while ($p = $produkResult->fetch_assoc()) {
    $produkList[$p['id']] = $p;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Kelola Pesanan - Pupukku</title>
  <link rel="stylesheet" href="kelola_pesanan.css" />
</head>
<body>

<div class="sidebar" id="sidebar">
  <div class="logo">USAHA 6 SAUDARA</div>
  <ul class="sidebar-menu">
    <li><a href="dashboard.php">Dashboard</a></li>
    <li><a href="kelola_produk.php">Kelola Produk</a></li>
    <li><a href="kelola_pesanan.php" class="active">Kelola Pesanan</a></li>
    <li><a href="kelola_promo.php">Kelola Promo</a></li>
    <li><a href="kelola_pengguna.php">Kelola Pengguna</a></li>
    <li><a href="laporan_penjualan.php">Laporan Penjualan</a></li>
    <li><a href="profil_admin.php">Profil Admin</a></li>
    <li><a href="login-admin.php">Logout</a></li>
  </ul>
</div>

<div class="main-content">
  <h1>Kelola Pesanan</h1>

  <!-- Filter Status -->
  <form class="filter-form" method="get">
    <label for="filter_status">Filter Status: </label>
    <select name="filter_status" id="filter_status">
      <option value="">Semua</option>
      <option value="Diproses" <?= ($status_filter == 'Diproses') ? 'selected' : '' ?>>Diproses</option>
      <option value="Dikirim" <?= ($status_filter == 'Dikirim') ? 'selected' : '' ?>>Dikirim</option>
      <option value="Selesai" <?= ($status_filter == 'Selesai') ? 'selected' : '' ?>>Selesai</option>
      <option value="Dibatalkan" <?= ($status_filter == 'Dibatalkan') ? 'selected' : '' ?>>Dibatalkan</option>
    </select>
    <button type="submit">Tampilkan</button>
  </form>

  <div class="table-container">
    <table>
      <thead>
        <tr>
          <th>Nama Pembeli</th>
          <th>Tanggal</th>
          <th>Status</th>
          <th>Metode Pembayaran</th>
          <th>Total</th>
          <th>Bukti Pembayaran</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php while($row = $result->fetch_assoc()): ?>
          <tr>
            <td><?= htmlspecialchars($row['nama']) ?></td>
            <td><?= date('d-m-Y', strtotime($row['tanggal_pesan'])) ?></td>
            <td><?= $row['status'] ?></td>
            <td><?= htmlspecialchars($row['metode_pembayaran']) ?></td>
            <td>Rp<?= number_format($row['total_harga'], 0, ',', '.') ?></td>
            <td>
              <?php if (!empty($row['bukti_pembayaran'])): ?>
                <a href="../uploads/<?= htmlspecialchars($row['bukti_pembayaran']) ?>" target="_blank" class="btn-bukti">Lihat</a>
              <?php else: ?>
                <span style="color: red;">---</span>
              <?php endif; ?>
            </td>
            <td>
              <a href="#" class="btn-detail" onclick='showDetail(<?= json_encode($row["items"]) ?>)'>Detail</a>
              <a href="#" class="btn-ubah" onclick='showUbahStatus(<?= $row["id"] ?>, "<?= $row["status"] ?>")'>Ubah Status</a>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Modal Detail -->
<div id="modalDetail" class="modal">
  <div class="modal-content">
    <span class="close" onclick="tutupModal('modalDetail')">&times;</span>
    <h3>Detail Pesanan</h3>
    <div id="isiDetail"></div>
  </div>
</div>

<!-- Modal Ubah Status -->
<div id="modalUbah" class="modal">
  <div class="modal-content">
    <span class="close" onclick="tutupModal('modalUbah')">&times;</span>
    <h3>Ubah Status Pesanan</h3>
    <form method="post">
      <input type="hidden" name="id_pesanan" id="idPesanan">
      <select name="status" id="status" required>
        <option value="">-- Pilih --</option>
        <option value="Diproses">Diproses</option>
        <option value="Dikirim">Dikirim</option>
        <option value="Selesai">Selesai</option>
        <option value="Dibatalkan">Dibatalkan</option>
      </select>
      <input type="hidden" name="ubah_status" value="1">
      <br><br>
      <button type="submit">Simpan</button>
    </form>
  </div>
</div>

<!-- SweetAlert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
<?php if (isset($_GET['status']) && $_GET['status'] == 'berhasil'): ?>
Swal.fire({
  icon: 'success',
  title: 'Berhasil!',
  text: 'Status pesanan berhasil diubah.',
  timer: 2000,
  showConfirmButton: false
});
<?php elseif (isset($_GET['status']) && $_GET['status'] == 'gagal'): ?>
Swal.fire({
  icon: 'error',
  title: 'Gagal!',
  text: 'Status pesanan gagal diubah.',
  timer: 2500,
  showConfirmButton: false
});
<?php endif; ?>
</script>

<script>
const produkData = <?= json_encode($produkList) ?>;

function showDetail(itemsJson) {
  const items = JSON.parse(itemsJson);
  let html = '<table class="detail-table"><thead><tr><th>Produk</th><th>Jumlah</th><th>Harga</th><th>Subtotal</th></tr></thead><tbody>';
  let total = 0;

  items.forEach(item => {
    const produk = produkData[item.produk_id];
    if (produk) {
      const subtotal = produk.harga * item.jumlah;
      total += subtotal;
      html += `<tr>
        <td>${produk.nama_produk}</td>
        <td>${item.jumlah}</td>
        <td>Rp ${produk.harga.toLocaleString('id-ID')}</td>
        <td>Rp ${subtotal.toLocaleString('id-ID')}</td>
      </tr>`;
    }
  });

  html += `<tr><td colspan="3"><strong>Total</strong></td><td><strong>Rp ${total.toLocaleString('id-ID')}</strong></td></tr>`;
  html += '</tbody></table>';

  document.getElementById('isiDetail').innerHTML = html;
  document.getElementById('modalDetail').style.display = 'block';
}

function showUbahStatus(id, status) {
  document.getElementById('idPesanan').value = id;
  document.getElementById('status').value = status;
  document.getElementById('modalUbah').style.display = 'block';
}

function tutupModal(id) {
  document.getElementById(id).style.display = 'none';
}
</script>

</body>
</html>

<?php $conn->close(); ?>
