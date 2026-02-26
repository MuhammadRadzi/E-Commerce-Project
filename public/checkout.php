<?php
session_start();
include '../config/database.php';

// === AUTHENTICATION CHECK ===
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    header("location:../login.php?pesan=belum_login");
    exit;
}

// === CHECK IF CART IS EMPTY ===
if (empty($_SESSION['keranjang'])) {
    header("location:../keranjang.php");
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
        
        // Verify stock is still available (with FOR UPDATE lock)
        $stmt = $conn->prepare("SELECT stok FROM barang WHERE id_barang = ? FOR UPDATE");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        
        if (!$data) {
            throw new Exception("Barang {$item['nama']} tidak ditemukan!");
        }
        
        // VALIDASI STOK SEBELUM DIKURANGI
        if ($data['stok'] < $jumlah) {
            throw new Exception("Stok {$item['nama']} tidak mencukupi! Tersedia: {$data['stok']}, diminta: {$jumlah}");
        }
        
        // KURANGI STOK DI SINI (SAAT CHECKOUT)
        $stok_baru = $data['stok'] - $jumlah;
        $stmt_update = $conn->prepare("UPDATE barang SET stok = ? WHERE id_barang = ?");
        $stmt_update->bind_param("ii", $stok_baru, $id);
        
        if (!$stmt_update->execute()) {
            throw new Exception("Gagal mengurangi stok untuk barang {$item['nama']}");
        }
    }
    
    // Commit transaction
    mysqli_commit($conn);
    
    // Clear cart after successful checkout
    $_SESSION['keranjang'] = [];
    
    // Redirect to index with success message
    header("location:../index.php?pesan=checkout_sukses");
    exit;
    
} catch (Exception $e) {
    // Rollback on error
    mysqli_rollback($conn);
    
    // Redirect back to cart with error
    header("location:../keranjang.php?pesan=checkout_gagal&error=" . urlencode($e->getMessage()));
    exit;
}
?>