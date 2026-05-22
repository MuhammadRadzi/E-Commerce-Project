-- database.sql
-- Setup Database untuk LapakPC (E-Commerce Perangkat Keras Komputer)

CREATE DATABASE IF NOT EXISTS db_ecommerce;
USE db_ecommerce;

-- 1. Membuat tabel 'barang'
CREATE TABLE IF NOT EXISTS barang (
    id_barang INT PRIMARY KEY AUTO_INCREMENT,
    nama_barang VARCHAR(255) NOT NULL,
    jenis_barang VARCHAR(100) NOT NULL,
    stok INT NOT NULL DEFAULT 0,
    harga DECIMAL(10,2) NOT NULL,
    kondisi ENUM('Baru', 'Bekas', 'Rusak') NOT NULL,
    lokasi_rak VARCHAR(10) DEFAULT NULL,
    gambar VARCHAR(255) DEFAULT 'no-image.jpg',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Membuat tabel 'users'
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Seeding data admin default (Password: admin123)
INSERT INTO users (username, password, role) VALUES 
('admin', '$2y$12$fOgM7iVi75yGsxwaSMbti.HNbW/EiZ36Fvr3pZm.VGyCzNWZYwjgC', 'admin')
ON DUPLICATE KEY UPDATE username=username;

-- 4. Seeding data user default (Password: user123)
INSERT INTO users (username, password, role) VALUES 
('user', '$2y$12$VqWX7ncFtn44eCMCjiPE0OFZzBJ70W.H.BUdQe.7oC0BrwokKqz3.', 'user')
ON DUPLICATE KEY UPDATE username=username;

-- 5. Seeding data barang awal
INSERT INTO barang (nama_barang, jenis_barang, stok, harga, kondisi, lokasi_rak, gambar) VALUES
('Processor AMD Ryzen 5 5600X', 'Processor', 15, 2300000.00, 'Baru', 'A-01', 'no-image.jpg'),
('Nvidia RTX 4060 Ti 8GB', 'VGA Card', 8, 6500000.00, 'Baru', 'A-02', 'no-image.jpg'),
('RAM Corsair Vengeance LPX DDR4 16GB', 'RAM', 25, 650000.00, 'Baru', 'B-01', 'no-image.jpg'),
('SSD Samsung 980 Pro NVMe 1TB', 'Storage', 12, 1450000.00, 'Baru', 'B-02', 'no-image.jpg'),
('Power Supply Corsair RM750x Gold', 'Power Supply', 5, 1850000.00, 'Baru', 'C-01', 'no-image.jpg');
