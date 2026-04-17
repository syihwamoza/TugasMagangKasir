<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
$koneksi = mysqli_connect("localhost", "root", "", "toko_maju_jaya");

if (isset($_POST['simpan_barang'])) {
    $nama_barang = $_POST['nama_barang'];
    $harga       = $_POST['harga'];
    $stok_input  = $_POST['stok_awal'];

    
    
    $query = "INSERT INTO stok_barang (nama_barang, harga, stok_awal, tambah, terjual, direturn, stok_akhir) 
              VALUES ('$nama_barang', '$harga', '$stok_input', 0, 0, 0, '$stok_input')";

    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Data Berhasil Disimpan!'); window.location='barang.php';</script>";
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
}
?>