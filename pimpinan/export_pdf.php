<?php
require_once('../tcpdf/tcpdf.php');
session_start();

// Cek login pimpinan
if (!isset($_SESSION['pimpinan_logged_in']) || $_SESSION['pimpinan_logged_in'] !== true) {
    header('Location: login-pimpinan.php');
    exit();
}

// Koneksi database
$conn = new mysqli("localhost", "root", "", "pupuk");
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Tangkap filter bulan dan tahun (jika ada)
$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : '';
$tahun = isset($_GET['tahun']) ? $_GET['tahun'] : '';

// Query data
$sql = "SELECT p.tanggal_pesan, pr.nama_produk, p.jumlah, p.total_harga 
        FROM pesanan p 
        JOIN produk pr ON p.produk_id = pr.id 
        WHERE p.status = 'Selesai'";

if (!empty($bulan) && !empty($tahun)) {
    $sql .= " AND MONTH(p.tanggal_pesan) = '$bulan' AND YEAR(p.tanggal_pesan) = '$tahun'";
} elseif (!empty($tahun)) {
    $sql .= " AND YEAR(p.tanggal_pesan) = '$tahun'";
}

$sql .= " ORDER BY p.tanggal_pesan DESC";

$result = $conn->query($sql);
$data = [];
$subtotal = 0;

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
        $subtotal += $row['total_harga']; // Tambah ke subtotal
    }
}

// Buat PDF
$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Usaha 6 Saudara');
$pdf->SetTitle('Laporan Penjualan');
$pdf->SetMargins(10, 10, 10);
$pdf->AddPage();

$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 10, 'Laporan Penjualan', 0, 1, 'C');
$pdf->Ln(5);

// Tampilkan filter
$pdf->SetFont('helvetica', '', 10);
if (!empty($bulan) || !empty($tahun)) {
    $bulanNama = [
        "01" => "Januari", "02" => "Februari", "03" => "Maret", "04" => "April",
        "05" => "Mei", "06" => "Juni", "07" => "Juli", "08" => "Agustus",
        "09" => "September", "10" => "Oktober", "11" => "November", "12" => "Desember"
    ];
    $filterTeks = "Periode: ";
    if (!empty($bulan)) {
        $filterTeks .= $bulanNama[$bulan] . " ";
    }
    if (!empty($tahun)) {
        $filterTeks .= $tahun;
    }
    $pdf->Write(0, $filterTeks);
    $pdf->Ln(5);
}

// Tabel isi data
$pdf->SetFont('helvetica', '', 10);
$tbl = '
<table border="1" cellpadding="4">
    <thead>
        <tr style="font-weight:bold; background-color:#f2f2f2;">
            <th width="20%">No</th>
            <th width="20%">Tanggal</th>
            <th width="20%">Nama Produk</th>
            <th width="20%">Jumlah</th>
            <th width="20%">Total Harga</th>
        </tr>
    </thead>
    <tbody>';

$no = 1;
if (!empty($data)) {
    foreach ($data as $row) {
        $tbl .= '<tr>
            <td align="center">' . $no++ . '</td>
            <td>' . $row['tanggal_pesan'] . '</td>
            <td>' . $row['nama_produk'] . '</td>
            <td align="center">' . $row['jumlah'] . '</td>
            <td align="right">Rp ' . number_format($row['total_harga'], 0, ',', '.') . '</td>
        </tr>';
    }

    // Tambah baris subtotal
    $tbl .= '<tr style="font-weight:bold; background-color:#f9f9f9;">
        <td colspan="4" align="right">Subtotal</td>
        <td align="right">Rp ' . number_format($subtotal, 0, ',', '.') . '</td>
    </tr>';
} else {
    $tbl .= '<tr><td colspan="5" align="center">Tidak ada data</td></tr>';
}

$tbl .= '</tbody></table>';
$pdf->writeHTML($tbl, true, false, false, false, '');

$pdf->Output('laporan_penjualan.pdf', 'I');
?>
