<!DOCTYPE html>
<html lang="id">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Daftar Akun Baru - LapakPC</title>
	<link rel="stylesheet" href="assets/css/style.css">
	<style>
		body {
			display: flex;
			justify-content: center;
			align-items: center;
			min-height: 100vh;
		}
		.login-box {
			background: white;
			padding: 2rem;
			border-radius: 8px;
			box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
			width: 100%;
			max-width: 400px;
		}
		.form-group { margin-bottom: 1rem; }
		.form-group label { display: block; margin-bottom: 0.5rem; }
		.form-group input {
			width: 100%;
			padding: 0.75rem;
			border: 1px solid #ccc;
			border-radius: 4px;
		}
		.btn-submit {
			width: 100%;
			padding: 0.75rem;
			background: var(--primary-color);
			color: white;
			border: none;
			border-radius: 4px;
			cursor: pointer;
		}
		.btn-submit:hover { filter: brightness(1.1); }
		.alert { color: red; margin-bottom: 1rem; text-align: center; font-size: 0.9rem; }
	</style>
</head>
<body>
	<div class="login-box">
		<h2 style="text-align: center; margin-bottom: 1.5rem;">Buat Akun Baru</h2>

		<?php
		if (isset($_GET['pesan'])) {
			if ($_GET['pesan'] == "password_tidak_sama") {
				echo "<div class='alert'>Konfirmasi password tidak cocok!</div>";
			} else if ($_GET['pesan'] == "username_ada") {
				echo "<div class='alert'>Username sudah digunakan!</div>";
			} else if ($_GET['pesan'] == "username_pendek") {
				echo "<div class='alert'>Username minimal 4 karakter!</div>";
			} else if ($_GET['pesan'] == "error") {
				echo "<div class='alert'>Terjadi kesalahan, coba lagi nanti.</div>";
			}
		}
		?>

		<form action="auth/register_process.php" method="post" id="regForm">
			<div class="form-group">
				<label>Username</label>
				<input type="text" name="username" required placeholder="Masukkan username Anda" minlength="4">
			</div>
			<div class="form-group">
				<label>Password</label>
				<input type="password" name="password" required placeholder="Masukkan password (minimal 6 karakter)" minlength="6">
			</div>
			<div class="form-group">
				<label>Konfirmasi Password</label>
				<input type="password" name="confirm_password" required placeholder="Konfirmasi password">
			</div>
			<button type="submit" class="btn-submit" id="btnReg">Daftar Sekarang</button>
			
			<p style="margin-top: 1.5rem; text-align: center; font-size: 0.9rem;">
				Sudah punya akun? <a href="login.php" style="text-decoration: none; color: var(--primary-color); font-weight: bold;">Login di sini</a>
			</p>
		</form>
	</div>

	<script>
		document.getElementById('regForm').addEventListener('submit', function(e) {
			const pass = this.querySelector('input[name="password"]').value;
			const confirm = this.querySelector('input[name="confirm_password"]').value;
			
			if (pass !== confirm) {
				e.preventDefault();
				alert('Konfirmasi password tidak sesuai!');
				return;
			}

			const btn = document.getElementById('btnReg');
			btn.innerHTML = "Memproses...";
			btn.disabled = true;
			btn.style.opacity = "0.7";
		});
	</script>
</body>
</html>
