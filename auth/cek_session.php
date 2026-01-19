<?php

/**
 * ============================================
 * FILE CEK LOGIN (GUARD)
 * ============================================
 * Include file ini di awal setiap halaman yang butuh login
 * Jika belum login, user akan diarahkan ke halaman login
 */

// WAJIB! Mulai session di baris pertama
session_start();

// Cek apakah session 'login' ada dan bernilai true
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    // Jika tidak ada atau bukan true, redirect ke login
    header("Location: login.php");
    exit; // WAJIB! Hentikan eksekusi setelah redirect
}

// Jika lolos pengecekan, berarti user sudah login
// Halaman akan dilanjutkan
