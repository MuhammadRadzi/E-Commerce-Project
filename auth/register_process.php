<?php
session_start();

// Menghubungkan dengan koneksi
include '../config/database.php';

// Menangkap data dari form
$username = input($_POST['username']);
$password = $_POST['password'];
$confirm  = $_POST['confirm_password'];

// Validasi: Password harus sama
if ($password !== $confirm) {
    header("location:../register.php?pesan=password_tidak_sama");
    exit;
}

// Validasi: Username minimal 4 karakter
if (strlen($username) < 4) {
    header("location:../register.php?pesan=username_pendek");
    exit;
}

// Mencegah SQL Injection dengan Prepared Statement untuk cek ketersediaan username
$stmt_cek = $conn->prepare("SELECT username FROM users WHERE username = ?");
$stmt_cek->bind_param("s", $username);
$stmt_cek->execute();
$result_cek = $stmt_cek->get_result();

if ($result_cek->num_rows > 0) {
    // Username sudah ada
    header("location:../register.php?pesan=username_ada");
    exit;
}

// Hashing Password (untuk keamanan tingkat tinggi)
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Masukkan ke database (role default adalah 'user')
$role = "user";
$stmt_insert = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
$stmt_insert->bind_param("sss", $username, $hashed_password, $role);

if ($stmt_insert->execute()) {
    // Berhasil daftar, kirim ke login
    header("location:../login.php?pesan=registrasi_sukses");
} else {
    // Gagal karena error database
    header("location:../register.php?pesan=error");
}

exit;
?>
