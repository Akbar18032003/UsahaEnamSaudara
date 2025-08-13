<?php
include '../koneksi.php';
$id = $_GET['id'];
$query = "SELECT * FROM pesanan WHERE id = $id";
$result = $conn->query($query);
if ($row = $result->fetch_assoc()) {
  echo "<p><strong>ID:</strong> {$row['id']}</p>";
  echo "<p><strong>Tanggal:</strong> {$row['tanggal_pesan']}</p>";
  echo "<p><strong>Status:</strong> {$row['status']}</p>";
  echo "<p><strong>Total:</strong> Rp" . number_format($row['total_harga'], 0, ',', '.') . "</p>";
  // Kamu bisa tambah detail lainnya di sini
} else {
  echo "Data tidak ditemukan.";
}
$conn->close();
?>
