<?php
include '../config/koneksi.php';

if (isset($_POST['update'])) {
    $id = $_POST['id_barang'];
    $stok_tambahan = $_POST['stok_baru']; 

    // 1. Cari dulu nama barangnya berdasarkan ID supaya datanya PASTI ada
    $query_barang = mysqli_query($conn, "SELECT nama_barang FROM barang WHERE id_barang = '$id'");
    $data_barang = mysqli_fetch_assoc($query_barang);
    $nama = $data_barang['nama_barang'];

    // 2. Update stok di tabel barang
    $update_stok = true; // no longer updating static stock column

    if ($update_stok) {
        // 3. Catat ke tabel pembelian (Pakai $nama yang baru kita cari tadi)
        mysqli_query($conn, "INSERT INTO pembelian (nama_barang, jumlah_beli, tanggal_beli) 
                            VALUES ('$nama', '$stok_tambahan', NOW())");
        
        header("Location: ../barang.php?pesan=berhasil");
    } else {
        echo "Gagal update stok";
    }
}
?>