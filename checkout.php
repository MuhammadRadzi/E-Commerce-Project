<?php
session_start();
include 'koneksi.php';

// === AUTHENTICATION CHECK ===
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    header("location:login.php?pesan=belum_login");
    exit;
}

// === CHECK IF CART IS EMPTY ===
if (empty($_SESSION['keranjang'])) {
    header("location:keranjang.php");
    exit;
}

// === PROCESS CHECKOUT ===
$success = true;
$error_message = "";

// Begin transaction for data integrity
mysqli_begin_transaction($conn);

try {
    foreach ($_SESSION['keranjang'] as $id => $item) {
        $jumlah = $item['jumlah'];
        
        // Verify stock is still available
        $stmt = $conn->prepare("SELECT stok FROM barang WHERE id_barang = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        
        if (!$data) {
            throw new Exception("Barang {$item['nama']} tidak ditemukan!");
        }
        
        if ($data['stok'] < $jumlah) {
            throw new Exception("Stok {$item['nama']} tidak mencukupi! Tersedia: {$data['stok']}, diminta: {$jumlah}");
        }
        
        // Update stock (already reduced in beli.php, but double-check here)
        // Actually, since stock was already reduced in beli.php, we skip this
        // But in proper e-commerce, stock should be reserved during checkout
    }
    
    // Commit transaction
    mysqli_commit($conn);
    
    // Clear cart after successful checkout
    $_SESSION['keranjang'] = [];
    
    // Redirect to index with success message
    header("location:index.php?pesan=checkout_sukses");
    exit;
    
} catch (Exception $e) {
    // Rollback on error
    mysqli_rollback($conn);
    
    // Redirect back to cart with error
    header("location:keranjang.php?pesan=checkout_gagal&error=" . urlencode($e->getMessage()));
    exit;
}
?>