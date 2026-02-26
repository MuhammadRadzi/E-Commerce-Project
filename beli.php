<?php
session_start();
include 'config/database.php';

// === AUTHENTICATION CHECK ===
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    header("location:login.php?pesan=belum_login");
    exit;
}

// === VALIDATE ID ===
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<script>alert('Invalid request!'); window.location='index.php';</script>";
    exit;
}

$id = (int)$_GET['id'];

// === GET PRODUCT DATA ===
$stmt = $conn->prepare("SELECT * FROM barang WHERE id_barang = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if (!$data || $data['stok'] <= 0) {
    echo "<script>alert('Barang tidak tersedia!'); window.location='index.php';</script>";
    exit;
}

// === INITIALIZE CART ===
if (!isset($_SESSION['keranjang'])) {
    $_SESSION['keranjang'] = [];
}

// ============================================================
// === FORM PROCESSING ===
// ============================================================
if (isset($_POST['proses_beli'])) {
    $jumlah_beli = (int)$_POST['jumlah'];

    // Validate quantity
    if ($jumlah_beli <= 0 || $jumlah_beli > $data['stok']) {
        echo "<script>alert('Jumlah tidak valid!'); window.location='beli.php?id=$id';</script>";
        exit;
    }

    // PERUBAHAN UTAMA: TIDAK KURANGI STOK DI SINI!
    // Stok akan dikurangi saat checkout, bukan saat add to cart

    // Add to cart (with accumulation)
    if (isset($_SESSION['keranjang'][$id])) {
        // Cek apakah total quantity tidak melebihi stok
        $total_quantity = $_SESSION['keranjang'][$id]['jumlah'] + $jumlah_beli;
        if ($total_quantity > $data['stok']) {
            echo "<script>alert('Total quantity di keranjang akan melebihi stok yang tersedia!'); window.location='beli.php?id=$id';</script>";
            exit;
        }
        $_SESSION['keranjang'][$id]['jumlah'] += $jumlah_beli;
    } else {
        $_SESSION['keranjang'][$id] = [
            'nama' => $data['nama_barang'],
            'harga' => $data['harga'],
            'jumlah' => $jumlah_beli
        ];
    }

    // Redirect to cart
    header("location:keranjang.php?pesan=berhasil_beli");
    exit;
}
// === END OF FORM PROCESSING ===

// === LOAD NAVIGATION (AFTER PROCESSING) ===
include 'includes/navigation.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beli <?php echo htmlspecialchars($data['nama_barang']); ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <nav>
        <h1>E-Commerce Project</h1>
        <ul>
            <li><a href="index.php">Katalog</a></li>
            <li>
                <a href="keranjang.php" style="position: relative;">
                    Keranjang
                    <?php if (!empty($_SESSION['keranjang'])): ?>
                        <span style="background: red; color: white; border-radius: 50%; padding: 2px 6px; font-size: 10px; position: absolute; top: -10px; right: -15px;">
                            <?php echo count($_SESSION['keranjang']); ?>
                        </span>
                    <?php endif; ?>
                </a>
            </li>
        </ul>
    </nav>

    <div class="container" style="max-width: 500px;">
        <?php
        page_header(
            'Konfirmasi Pembelian',
            ['index.php' => 'Katalog'],
            true
        );
        ?>

        <div class="card" style="padding: 2rem;">
            <div style="margin-bottom: 1.5rem;">
                <?php if ($data['gambar'] != 'no-image.jpg' && !empty($data['gambar'])): ?>
                    <img src="assets/img/products/<?php echo htmlspecialchars($data['gambar']); ?>"
                        alt="<?php echo htmlspecialchars($data['nama_barang']); ?>"
                        style="width: 100%; height: 200px; object-fit: cover; border-radius: 8px; margin-bottom: 1rem;">
                <?php endif; ?>

                <h2 style="margin-bottom: 0.5rem;"><?php echo htmlspecialchars($data['nama_barang']); ?></h2>
                <p style="font-size: 1.5rem; color: var(--primary-color); font-weight: bold;">
                    <?php echo formatRupiah($data['harga']); ?>
                </p>

                <div style="display: inline-block; padding: 0.375rem 0.75rem; background: #d1fae5; color: #065f46; border-radius: 4px; font-size: 0.875rem; font-weight: 500; margin-top: 0.5rem;">
                    Tersedia: <?php echo $data['stok']; ?> unit
                </div>
            </div>

            <hr style="margin: 1.5rem 0; border: none; border-top: 1px solid #e2e8f0;">

            <form method="POST" action="" id="purchaseForm">
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Jumlah Pembelian:</label>
                    <input type="number" name="jumlah" id="jumlahInput" value="1" min="1" max="<?php echo $data['stok']; ?>" required
                        style="width: 100%; padding: 0.75rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 1rem;">
                    <small style="color: #64748b; margin-top: 0.25rem; display: block;">Maksimal: <?php echo $data['stok']; ?> unit</small>
                </div>

                <div style="background: #f8fafc; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                        <span style="color: #64748b;">Harga Satuan:</span>
                        <span style="font-weight: 600;"><?php echo formatRupiah($data['harga']); ?></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                        <span style="color: #64748b;">Jumlah:</span>
                        <span style="font-weight: 600;" id="displayJumlah">1</span>
                    </div>
                    <hr style="margin: 0.75rem 0; border: none; border-top: 1px dashed #cbd5e1;">
                    <div style="display: flex; justify-content: space-between;">
                        <span style="font-weight: 700; font-size: 1.125rem;">Total:</span>
                        <span style="font-weight: 700; font-size: 1.125rem; color: var(--primary-color);" id="displayTotal"><?php echo formatRupiah($data['harga']); ?></span>
                    </div>
                </div>

                <div style="display: flex; gap: 1rem;">
                    <button type="button" onclick="history.back()" class="btn-buy" style="background: #64748b; flex: 1;">Batal</button>
                    <button type="submit" name="proses_beli" value="1" class="btn-buy" style="flex: 2;">
                        Masukkan ke Keranjang
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const jumlahInput = document.getElementById('jumlahInput');
        const hargaSatuan = <?php echo $data['harga']; ?>;
        const stokMax = <?php echo $data['stok']; ?>;

        // Real-time calculation
        jumlahInput.addEventListener('input', function() {
            let jumlah = parseInt(this.value) || 1;
            
            if (jumlah > stokMax) {
                jumlah = stokMax;
                this.value = stokMax;
            }
            
            if (jumlah < 1) {
                jumlah = 1;
                this.value = 1;
            }
            
            const total = hargaSatuan * jumlah;
            document.getElementById('displayJumlah').textContent = jumlah;
            document.getElementById('displayTotal').textContent = 'Rp ' + total.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        });
        
    </script>
</body>
</html>