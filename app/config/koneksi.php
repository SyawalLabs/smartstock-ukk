<?php
/**
 * File Koneksi Database
 */

$host = 'localhost';
$dbname = 'smartstock_ukk';
$username = 'root';
$password = '';

// TAMBAHKAN INI: Sesuaikan dengan alamat di browser Laragon kamu
$base_url = "http://smartstock-ukk.test:8080/"; 

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}