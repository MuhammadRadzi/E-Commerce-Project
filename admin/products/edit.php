<?php
session_start();
include '../../config/database.php';
include '../../includes/navigation.php'; // Include navigation component

if (!isset($_SESSION['role']) || $_SESSION['role'] != "admin") {
	header("location:login.php");
	exit;
}

// Validasi ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
	header("location:../index.php?pesan=error_db");
	exit;
}

$id = $_GET['id'];

// Prepared statement untuk keamanan
$stmt = $conn->prepare("SELECT * FROM barang WHERE id_barang = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
	header("location:../index.php?pesan=error_db");
	exit;
}

$d = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="id">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="Toko Komputer Online - Sedia berbagai macam perangkat hardware berkualitas.">
	<meta name="keywords" content="komputer, laptop, hardware, e-commerce">
	<title>Edit Barang - <?php echo htmlspecialchars($d['nama_barang']); ?></title>
	<link rel="stylesheet" href="../../assets/css/style.css">
</head>

<body>
	<nav>
		<h1>Admin Panel</h1>
		<ul>
			<li><a href="../index.php">Dashboard</a></li>
			<li><a href="../../auth/logout.php" style="color: #ffcccc;">Logout</a></li>
		</ul>
	</nav>

	<div class="container" style="max-width: 600px; margin-top: 2rem;">

		<?php
		// Enhanced page header with breadcrumb and back button
		page_header(
			'Edit Barang',
			['../index.php' => 'Dashboard'],
			true,
			'../index.php'
		);
		?>

		<div class="card" style="padding: 2rem;">
			<form action="../../process/product_edit.php" method="post" enctype="multipart/form-data">
				<input type="hidden" name="id_barang" value="<?php echo $d['id_barang']; ?>">

				<div style="margin-bottom: 1rem;">
					<label>Nama Barang</label>
					<input type="text" name="nama_barang" value="<?php echo htmlspecialchars($d['nama_barang']); ?>"
						required pattern="[A-Za-z0-9\s]{3,}" style="width: 100%; padding: 0.5rem;">
				</div>

				<div style="margin-bottom: 1rem;">
					<label>Kategori (Jenis)</label>
					<input type="text" name="jenis_barang" value="<?php echo htmlspecialchars($d['jenis_barang']); ?>"
						required style="width: 100%; padding: 0.5rem;" placeholder="Contoh: Laptop, Hardware">
				</div>

				<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
					<div>
						<label>Stok</label>
						<input type="number" name="stok" min="0" title="Stok tidak boleh negatif"
							value="<?php echo $d['stok']; ?>" required style="width: 100%; padding: 0.5rem;">
					</div>
					<div>
						<label>Harga</label>
						<input type="number" name="harga" value="<?php echo $d['harga']; ?>"
							required style="width: 100%; padding: 0.5rem;">
					</div>
				</div>

				<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
					<div>
						<label>Kondisi</label>
						<select name="kondisi" required style="width: 100%; padding: 0.5rem;">
							<option value="Baru" <?php echo ($d['kondisi'] == 'Baru') ? 'selected' : ''; ?>>Baru</option>
							<option value="Bekas" <?php echo ($d['kondisi'] == 'Bekas') ? 'selected' : ''; ?>>Bekas</option>
							<option value="Rusak" <?php echo ($d['kondisi'] == 'Rusak') ? 'selected' : ''; ?>>Rusak</option>
						</select>
					</div>
					<div>
						<label>Lokasi Rak</label>
						<input type="text" name="lokasi_rak" value="<?php echo htmlspecialchars($d['lokasi_rak']); ?>"
							pattern="[A-Z]-[0-9]{2}" title="Format: A-01, B-05, dll"
							style="width: 100%; padding: 0.5rem;" placeholder="Contoh: A-01">
					</div>
				</div>

				<div style="margin-bottom: 1rem;">
					<label>Foto Saat Ini:</label><br>
					<img src="../../assets/img/products/<?php echo htmlspecialchars($d['gambar']); ?>"
						alt="<?php echo htmlspecialchars($d['nama_barang']); ?>"
						width="100" style="margin-bottom: 10px; border-radius: 5px;">
					<br>
					<label>Ganti Foto (Kosongkan jika tidak ingin mengubah):</label>
					<input type="file" name="gambar" accept="image/*" style="width: 100%;">
				</div>

				<div style="display: flex; gap: 1rem;">
					<button type="button" onclick="history.back()" class="btn-buy" style="background: #64748b; flex: 1;">Batal</button>
					<button type="submit" class="btn-buy" style="background: #f59e0b; flex: 2;">Update Data</button>
				</div>
			</form>
		</div>
	</div>

	<script>
		// Auto-save warning on page leave
		let formChanged = false;
		document.querySelector('form').addEventListener('change', function() {
			formChanged = true;
		});

		window.addEventListener('beforeunload', function(e) {
			if (formChanged) {
				e.preventDefault();
				e.returnValue = 'Ada perubahan yang belum disimpan. Yakin ingin keluar?';
			}
		});

		// Reset flag on submit
		document.querySelector('form').addEventListener('submit', function() {
			formChanged = false;
		});
	</script>
</body>

</html>