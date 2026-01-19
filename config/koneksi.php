<?php

/**
 * KONEKSI DATABASE
 * File ini menghubungkan PHP dengan MySQL
 * Di-include di setiap halaman yang butuh database
 */

// ============================================
// KONFIGURASI DATABASE
// ============================================
$host     = "localhost";    // Server database
$username = "root";         // Username MySQL
$password = "";             // Password MySQL (kosong untuk Laragon)
$database = "smartstock_ukk"; // Nama database

// ============================================
// MEMBUAT KONEKSI
// ============================================
$koneksi = mysqli_connect($host, $username, $password, $database);

// ============================================
// CEK KONEKSI
// ============================================
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
