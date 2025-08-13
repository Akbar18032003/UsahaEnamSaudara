<?php
require_once('../tcpdf/tcpdf.php');
include '../koneksi.php';

date_default_timezone_set("Asia/Jakarta");
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'harian';
$tanggal_sekarang = date('Y-m-d');
$query = "";

// Tentukan query dan judul berdasarkan filter
if ($filter == 'harian') {
    $judul = "Laporan Penjualan Harian (" . date('d-m-Y') . ")";
    $query = "SELECT p.*, pr.nama_produk 
              FROM pesanan p 
              JOIN produk pr ON p.produk_id = pr.id 
              WHERE DATE(p.tanggal_pesan) = '$tanggal_sekarang' 
              AND p.status = 'Selesai'";
} elseif ($filter == 'mingguan') {
    $tanggal_minggu_lalu = date('Y-m-d', strtotime('-7 days'));
    $judul = "Laporan Penjualan Mingguan (" . date('d-m-Y', strtotime('-7 days')) . " s/d " . date('d-m-Y') . ")";
    $query = "SELECT p.*, pr.nama_produk 
              FROM pesanan p 
              JOIN produk pr ON p.produk_id = pr.id 
              WHERE DATE(p.tanggal_pesan) BETWEEN '$tanggal_minggu_lalu' AND '$tanggal_sekarang' 
              AND p.status = 'Selesai'";
} elseif ($filter == 'bulanan') {
    $bulan_ini = date('Y-m');
    $judul = "Laporan Penjualan Bulanan (" . date('F Y') . ")";
    $query = "SELECT p.*, pr.nama_produk 
              FROM pesanan p 
              JOIN produk pr ON p.produk_id = pr.id 
              WHERE DATE_FORMAT(p.tanggal_pesan, '%Y-%m') = '$bulan_ini' 
              AND p.status = 'Selesai'";
}

$result = $conn->query($query);

// Inisialisasi PDF
$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
$pdf->SetTitle($judul);
$pdf->SetMargins(15, 15, 15);
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 12);

// Header laporan
$html = '<h2 style="text-align:center;">' . $judul . '</h2>';
$html .= '
<style>
    table {
        border-collapse: collapse;
        width: 100%;
        font-size: 11px;
    }
    th, td {
        border: 1px solid #000;
        padding: 6px;
    }
    th {
        background-color: #f2f2f2;
        font-weight: bold;
        text-align: center;
    }
    td {
        vertical-align: middle;
    }
</style>';

$html .= '
<table>
    <thead>
        <tr>
            <th width="25%">Tanggal</th>
            <th width="25%">Produk</th>
            <th width="25%">Jumlah</th>
            <th width="25%">Total</th>
        </tr>
    </thead>
    <tbody>';

$totalKeseluruhan = 0;

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $tanggal = date('d-m-Y H:i', strtotime($row['tanggal_pesan']));
        $produk = htmlspecialchars($row['nama_produk']);
        $jumlah = $row['jumlah'];
        $total = number_format($row['total_harga'], 0, ',', '.');
        $totalKeseluruhan += $row['total_harga'];

        $html .= '
        <tr>
            <td>' . $tanggal . '</td>
            <td>' . $produk . '</td>
            <td align="center">' . $jumlah . '</td>
            <td>Rp ' . $total . '</td>
        </tr>';
    }

    $html .= '
    <tr>
        <td colspan="3" align="right"><strong>Total Keseluruhan</strong></td>
        <td><strong>Rp ' . number_format($totalKeseluruhan, 0, ',', '.') . '</strong></td>
    </tr>';
} else {
    $html .= '
    <tr>
        <td colspan="4" align="center">Tidak ada data penjualan.</td>
    </tr>';
}

$html .= '</tbody></table>';

// Tampilkan ke PDF
$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('Laporan_Penjualan.pdf', 'I');
?>
