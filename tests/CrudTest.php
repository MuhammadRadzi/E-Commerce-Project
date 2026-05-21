<?php
// tests/CrudTest.php
session_start();
include __DIR__ . '/../config/database.php';

echo "<h2>Unit Testing: CRUD Operations Lifecycle</h2>";
echo "<p>Menjalankan serangkaian pengujian terintegrasi untuk memverifikasi fungsionalitas CRUD pada database barang.</p>";

class CrudTest {
    public function testCompleteCrudFlow() {
        global $conn;
        
        // --- 1. TEST CREATE ---
        echo "<h3 style='color: #1e3a8a;'>1. Testing CREATE (Insert)...</h3>";
        $stmt_create = $conn->prepare("INSERT INTO barang (nama_barang, jenis_barang, stok, harga, kondisi, lokasi_rak, gambar) VALUES (?, ?, ?, ?, ?, ?, ?)");
        if (!$stmt_create) {
            echo "<p style='color:red; font-weight: bold;'>[FAILED] Gagal mempersiapkan statement INSERT: " . $conn->error . "</p>";
            return;
        }
        
        $nama = "Test Product-XYZ";
        $jenis = "Test Category";
        $stok = 12;
        $harga = 75000;
        $kondisi = "Baru";
        $lokasi = "T-99";
        $gambar = "no-image.jpg";
        
        $stmt_create->bind_param("ssissss", $nama, $jenis, $stok, $harga, $kondisi, $lokasi, $gambar);
        $create_result = $stmt_create->execute();
        
        if ($create_result) {
            $inserted_id = $conn->insert_id;
            echo "<p style='color:green; font-weight: bold;'>[PASSED] CREATE Berhasil! ID Barang Terakhir: $inserted_id</p>";
        } else {
            echo "<p style='color:red; font-weight: bold;'>[FAILED] CREATE Gagal: " . $stmt_create->error . "</p>";
            return;
        }
        
        // --- 2. TEST READ ---
        echo "<h3 style='color: #1e3a8a;'>2. Testing READ (Select)...</h3>";
        $stmt_read = $conn->prepare("SELECT * FROM barang WHERE id_barang = ?");
        $stmt_read->bind_param("i", $inserted_id);
        $stmt_read->execute();
        $read_result = $stmt_read->get_result();
        
        if ($read_result->num_rows > 0) {
            $data = $read_result->fetch_assoc();
            if ($data['nama_barang'] === $nama && (int)$data['stok'] === $stok) {
                echo "<p style='color:green; font-weight: bold;'>[PASSED] READ Berhasil! Data produk cocok dengan yang di-input.</p>";
            } else {
                echo "<p style='color:red; font-weight: bold;'>[FAILED] READ Gagal: Data tidak cocok.</p>";
            }
        } else {
            echo "<p style='color:red; font-weight: bold;'>[FAILED] READ Gagal: Produk tidak ditemukan di database.</p>";
        }
        
        // --- 3. TEST UPDATE ---
        echo "<h3 style='color: #1e3a8a;'>3. Testing UPDATE (Modify)...</h3>";
        $stmt_update = $conn->prepare("UPDATE barang SET stok = ?, harga = ? WHERE id_barang = ?");
        $stok_baru = 15;
        $harga_baru = 80000;
        $stmt_update->bind_param("iii", $stok_baru, $harga_baru, $inserted_id);
        
        if ($stmt_update->execute()) {
            // Verifikasi update
            $stmt_verify = $conn->prepare("SELECT stok, harga FROM barang WHERE id_barang = ?");
            $stmt_verify->bind_param("i", $inserted_id);
            $stmt_verify->execute();
            $verify_res = $stmt_verify->get_result()->fetch_assoc();
            
            if ((int)$verify_res['stok'] === $stok_baru && (int)$verify_res['harga'] === $harga_baru) {
                echo "<p style='color:green; font-weight: bold;'>[PASSED] UPDATE Berhasil! Stok diperbarui menjadi $stok_baru, Harga diperbarui menjadi $harga_baru.</p>";
            } else {
                echo "<p style='color:red; font-weight: bold;'>[FAILED] UPDATE Gagal: Nilai baru tidak ter-update.</p>";
            }
        } else {
            echo "<p style='color:red; font-weight: bold;'>[FAILED] UPDATE Gagal: " . $stmt_update->error . "</p>";
        }
        
        // --- 4. TEST DELETE ---
        echo "<h3 style='color: #1e3a8a;'>4. Testing DELETE (Remove)...</h3>";
        $stmt_delete = $conn->prepare("DELETE FROM barang WHERE id_barang = ?");
        $stmt_delete->bind_param("i", $inserted_id);
        
        if ($stmt_delete->execute()) {
            // Verifikasi penghapusan
            $stmt_check = $conn->prepare("SELECT id_barang FROM barang WHERE id_barang = ?");
            $stmt_check->bind_param("i", $inserted_id);
            $stmt_check->execute();
            $check_res = $stmt_check->get_result();
            
            if ($check_res->num_rows === 0) {
                echo "<p style='color:green; font-weight: bold;'>[PASSED] DELETE Berhasil! Data pengujian berhasil dibersihkan dari database.</p>";
            } else {
                echo "<p style='color:red; font-weight: bold;'>[FAILED] DELETE Gagal: Data masih tersisa di database.</p>";
            }
        } else {
            echo "<p style='color:red; font-weight: bold;'>[FAILED] DELETE Gagal: " . $stmt_delete->error . "</p>";
        }
        
        echo "<br><hr>";
        echo "<h2 style='color: green;'>[SUMMARY] Seluruh Aliran Pengujian CRUD Berhasil Dijalankan!</h2>";
    }
}

$test = new CrudTest();
$test->testCompleteCrudFlow();
?>