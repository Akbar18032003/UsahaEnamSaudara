<?php
$host = "localhost";
$user = "root"; // sesuaikan dengan user MySQL kamu
$pass = "";     // sesuaikan jika ada password
$db   = "pupuk";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
  die("Koneksi gagal: " . $conn->connect_error);
}
?>
