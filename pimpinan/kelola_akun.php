<?php
include '../koneksi.php';

// Hapus admin
if (isset($_GET['hapus'])) {
  $id = $_GET['hapus'];
  $stmt = $conn->prepare("DELETE FROM admin WHERE id = ?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  header("Location: kelola_akun.php");
  exit;
}

// Tambah admin
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['aksi']) && $_POST['aksi'] === "tambah") {
  $nama = $_POST['nama'];
  $email = $_POST['email'];
  $username = $_POST['username'];
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

  $cek = $conn->prepare("SELECT * FROM admin WHERE email = ? OR username = ?");
  $cek->bind_param("ss", $email, $username);
  $cek->execute();
  $result = $cek->get_result();

  if ($result->num_rows > 0) {
    echo "<script>alert('Email atau Username sudah digunakan!');</script>";
  } else {
    $stmt = $conn->prepare("INSERT INTO admin (nama, email, username, password) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $nama, $email, $username, $password);
    $stmt->execute();
  }
}

// Edit admin
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['aksi']) && $_POST['aksi'] === "edit") {
  $id = $_POST['id'];
  $nama = $_POST['nama'];
  $email = $_POST['email'];
  $username = $_POST['username'];

  $stmt = $conn->prepare("UPDATE admin SET nama=?, email=?, username=? WHERE id=?");
  $stmt->bind_param("sssi", $nama, $email, $username, $id);
  $stmt->execute();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Kelola Akun Admin</title>
  <link rel="stylesheet" href="kelola_akun.css" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
  <div class="container-pimpinan">
    <div class="sidebar">
      <div class="logo">Usaha 6 Saudara</div>
      <ul class="sidebar-menu">
        <li><a href="dashboard.php">Dashboard</a></li>
        <li><a href="laporan_penjualan.php">Laporan Penjualan</a></li>
        <li><a href="transaksi.php">Data Transaksi</a></li>
        <li><a href="data_produk.php">Data Produk</a></li>
        <li><a href="kelola_akun.php" class="active">Kelola Akun Admin</a></li>
        <li><a href="profil_pimpinan.php">Profil Pimpinan</a></li>
        <li><a href="login-pimpinan.php">Logout</a></li>
      </ul>
    </div>

    <div class="main-content">
      <h1>Kelola Akun Admin</h1>

      <div class="action-bar">
        <button class="btn-tambah">+ Tambah Admin</button>
      </div>

      <div class="table-box">
        <table>
          <thead>
            <tr>
              <th>No</th>
              <th>Nama</th>
              <th>Email</th>
              <th>Username</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php
              $no = 1;
              $data = $conn->query("SELECT * FROM admin");
              while ($row = $data->fetch_assoc()) {
                echo "<tr>
                        <td>$no</td>
                        <td>{$row['nama']}</td>
                        <td>{$row['email']}</td>
                        <td>{$row['username']}</td>
                        <td>
                          <button class='btn-edit' 
                            data-id='{$row['id']}' 
                            data-nama='{$row['nama']}' 
                            data-email='{$row['email']}' 
                            data-username='{$row['username']}'>✏️ Edit</button>
                          <button class='btn-hapus' data-id='{$row['id']}'>🗑️ Hapus</button>
                        </td>
                      </tr>";
                $no++;
              }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Popup Form Tambah/Edit -->
  <div class="popup-form" id="popupForm" style="display:none;">
    <div class="form-box">
      <h2 id="formTitle">Tambah Admin</h2>
      <form action="" method="POST">
        <input type="hidden" name="aksi" id="aksiInput" value="tambah">
        <input type="hidden" name="id" id="idAdmin">
        <input type="text" name="nama" id="nama" placeholder="Nama Lengkap" required>
        <input type="email" name="email" id="email" placeholder="Email" required>
        <input type="text" name="username" id="username" placeholder="Username" required>
        <input type="password" name="password" id="password" placeholder="Password">
        <div class="form-buttons">
          <button type="submit">Simpan</button>
          <button type="button" id="btnClose">Batal</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    const popupForm = document.getElementById('popupForm');
    const btnTambah = document.querySelector('.btn-tambah');
    const btnClose = document.getElementById('btnClose');
    const formTitle = document.getElementById('formTitle');
    const aksiInput = document.getElementById('aksiInput');
    const passwordInput = document.getElementById('password');
    const idInput = document.getElementById('idAdmin');
    const namaInput = document.getElementById('nama');
    const emailInput = document.getElementById('email');
    const usernameInput = document.getElementById('username');

    // Tampilkan form tambah
    btnTambah.addEventListener('click', () => {
      formTitle.textContent = 'Tambah Admin';
      aksiInput.value = 'tambah';
      idInput.value = '';
      namaInput.value = '';
      emailInput.value = '';
      usernameInput.value = '';
      passwordInput.style.display = 'block';
      popupForm.style.display = 'flex';
    });

    // Tutup popup
    btnClose.addEventListener('click', () => {
      popupForm.style.display = 'none';
    });

    popupForm.addEventListener('click', function(e) {
      if (e.target === popupForm) {
        popupForm.style.display = 'none';
      }
    });

    // Tombol Hapus
    document.querySelectorAll('.btn-hapus').forEach(btn => {
      btn.addEventListener('click', function() {
        const id = this.getAttribute('data-id');
        Swal.fire({
          title: 'Yakin ingin menghapus?',
          text: 'Data admin akan dihapus permanen.',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#d33',
          cancelButtonColor: '#aaa',
          confirmButtonText: 'Ya, hapus!'
        }).then((result) => {
          if (result.isConfirmed) {
            window.location.href = `kelola_akun.php?hapus=${id}`;
          }
        });
      });
    });

    // Tombol Edit
    document.querySelectorAll('.btn-edit').forEach(btn => {
      btn.addEventListener('click', function () {
        const id = this.getAttribute('data-id');
        const nama = this.getAttribute('data-nama');
        const email = this.getAttribute('data-email');
        const username = this.getAttribute('data-username');

        formTitle.textContent = 'Edit Admin';
        aksiInput.value = 'edit';
        idInput.value = id;
        namaInput.value = nama;
        emailInput.value = email;
        usernameInput.value = username;
        passwordInput.style.display = 'none'; // password tidak perlu saat edit
        popupForm.style.display = 'flex';
      });
    });
  </script>
</body>
</html>
