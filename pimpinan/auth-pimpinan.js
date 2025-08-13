// Dark mode toggle
const toggle = document.getElementById('toggle-theme');
toggle.addEventListener('click', () => {
  document.body.classList.toggle('dark');
  toggle.textContent = document.body.classList.contains('dark') ? '☀️' : '🌙';
});

// Contoh validasi login
document.getElementById('toggle-theme').addEventListener('click', () => {
  document.body.classList.toggle('dark');
});

function validateForm() {
  const nama = document.getElementById('nama').value.trim();
  const email = document.getElementById('email').value.trim();
  const username = document.getElementById('username').value.trim();
  const password = document.getElementById('password').value;

  if (nama === "" || email === "" || username === "" || password === "") {
    Swal.fire({
      icon: 'warning',
      title: 'Oops...',
      text: 'Semua field wajib diisi!'
    });
    return false;
  }

  if (password.length < 6) {
    Swal.fire({
      icon: 'warning',
      title: 'Password terlalu pendek',
      text: 'Password minimal 6 karakter.'
    });
    return false;
  }

  return true;
}

