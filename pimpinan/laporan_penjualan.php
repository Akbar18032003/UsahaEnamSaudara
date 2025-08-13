<?php
session_start();

// Cek apakah pimpinan sudah login
if (!isset($_SESSION['pimpinan_logged_in']) || $_SESSION['pimpinan_logged_in'] !== true) {
  header('Location: login-pimpinan.php');
  exit();
}

// Koneksi ke database
$conn = new mysqli("localhost", "root", "", "pupuk");
if ($conn->connect_error) {
  die("Koneksi gagal: " . $conn->connect_error);
}

// Ambil filter dari GET atau default
$tanggal = isset($_GET['tanggal']) ? $_GET['tanggal'] : '';
$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : '';
$tahun = isset($_GET['tahun']) ? $_GET['tahun'] : '';

// Query dasar
$sql = "SELECT tanggal_pesan, produk_id, jumlah, total_harga FROM pesanan WHERE status = 'Selesai'";

// Tambahkan filter jika tersedia
if (!empty($tanggal)) {
  $sql .= " AND tanggal_pesan = '$tanggal'";
} elseif (!empty($bulan) && !empty($tahun)) {
  $sql .= " AND MONTH(tanggal_pesan) = '$bulan' AND YEAR(tanggal_pesan) = '$tahun'";
} elseif (!empty($tahun)) {
  $sql .= " AND YEAR(tanggal_pesan) = '$tahun'";
}

$sql .= " ORDER BY tanggal_pesan DESC";

$result = $conn->query($sql);
$data = [];
if ($result && $result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $data[] = $row;
  }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Laporan Penjualan</title>
  <link rel="stylesheet" href="laporan_penjualan.css" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>
<div class="container-pimpinan">
  <div class="sidebar">
    <div class="logo">Usaha 6 Saudara</div>
    <ul class="sidebar-menu">
      <li><a href="dashboard.php">Dashboard</a></li>
      <li><a href="laporan_penjualan.php" class="active">Laporan Penjualan</a></li>
      <li><a href="transaksi.php">Data Transaksi</a></li>
      <li><a href="data_produk.php">Data Produk</a></li>
      <li><a href="kelola_akun.php">Kelola Akun Admin</a></li>
      <li><a href="profil_pimpinan.php">Profil Pimpinan</a></li>
      <li><a href="login-pimpinan.php">Logout</a></li>
    </ul>
  </div>

  <div class="main-content">
    <h1>Laporan Penjualan</h1>

    <form method="GET" class="filter-box">

      <label for="bulan">Bulan:</label>
      <select name="bulan" id="bulan">
        <option value="">Pilih Bulan</option>
        <?php
        $bulanList = ["01" => "Januari", "02" => "Februari", "03" => "Maret", "04" => "April", "05" => "Mei", "06" => "Juni", "07" => "Juli", "08" => "Agustus", "09" => "September", "10" => "Oktober", "11" => "November", "12" => "Desember"];
        foreach ($bulanList as $val => $nama) {
          echo "<option value='$val'" . ($bulan == $val ? " selected" : "") . ">$nama</option>";
        }
        ?>
      </select>

      <label for="tahun">Tahun:</label>
      <input type="number" name="tahun" id="tahun" placeholder="2025" value="<?= htmlspecialchars($tahun) ?>">
      <button type="submit" class="btn">Filter</button>
    </form>

    <form action="export_pdf.php" method="post" style="display:inline;">
  <input type="hidden" name="bulan" value="<?= htmlspecialchars($bulan) ?>">
  <input type="hidden" name="tahun" value="<?= htmlspecialchars($tahun) ?>">
  <button type="submit" class="btn">Export PDF</button>
</form> <br><br>

    <div class="table-box">
      <table>
        <thead>
        <tr>
          <th>No</th>
          <th>Tanggal</th>
          <th>Produk</th>
          <th>Jumlah</th>
          <th>Total Harga</th>
        </tr>
        </thead>
        <tbody>
        <?php if (empty($data)) : ?>
          <tr><td colspan="5">Tidak ada data</td></tr>
        <?php else : ?>
          <?php foreach ($data as $index => $row) : ?>
            <tr>
              <td><?= $index + 1 ?></td>
              <td><?= $row['tanggal_pesan'] ?></td>
              <td><?= $row['produk_id'] ?></td>
              <td><?= $row['jumlah'] ?></td>
              <td>Rp <?= number_format($row['total_harga'], 0, ',', '.') ?></td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- jsPDF CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<script>
  async function exportPDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();

    doc.text("Laporan Penjualan", 14, 20);

    const rows = [];
    const headers = [];
    document.querySelectorAll("table thead th").forEach(th => headers.push(th.innerText));
    rows.push(headers);

    document.querySelectorAll("table tbody tr").forEach(tr => {
      const row = [];
      tr.querySelectorAll("td").forEach(td => row.push(td.innerText));
      rows.push(row);
    });

    let y = 30;
    rows.forEach((row, i) => {
      doc.text(row.join("  |  "), 14, y);
      y += 10;
    });

    doc.save("laporan-penjualan.pdf");
  }

  function exportCSV() {
    let csv = '';
    const rows = document.querySelectorAll("table tr");
    rows.forEach(row => {
      const cols = row.querySelectorAll("td, th");
      const rowData = [];
      cols.forEach(col => rowData.push('"' + col.innerText + '"'));
      csv += rowData.join(",") + "\n";
    });

    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement("a");
    a.href = url;
    a.download = "laporan-penjualan.csv";
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
  }
</script>
</body>
</html>
