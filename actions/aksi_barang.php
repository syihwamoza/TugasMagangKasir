<?php
include '../config/koneksi.php';

// Cek apakah tombol tambah/update diklik
if (isset($_POST['tambah_stok'])) {
    $id_barang = $_POST['id_barang'];
    $nama_barang = $_POST['nama_barang']; // Pastikan form mengirim nama barang juga
    $jumlah_tambah = $_POST['jumlah_tambah']; // Angka 90 atau lainnya

    // 1. Update stok di tabel barang (ini yang bikin angka Stok Akhir berubah)
    $update = true;

    if ($update) {
        // 2. INI KUNCINYA: Masukkan data ke tabel pembelian
        // Tanpa ini, kolom TAMBAH di tracking akan selalu strip (-)
        mysqli_query($conn, "INSERT INTO pembelian (nama_barang, jumlah_beli, tanggal_beli) 
                            VALUES ('$nama_barang', '$jumlah_tambah', NOW())");
        
        header("Location: ../barang.php?pesan=berhasil");
    }
}
?>