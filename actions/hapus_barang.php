<?php
include '../config/koneksi.php';
$id = $_GET['id'];

$query = mysqli_query($conn, "DELETE FROM barang WHERE id_barang = '$id'");

if ($query) {
    // Balik ke halaman barang sambil bawa pesan sukses
    header("Location: ../barang.php?pesan=hapus_berhasil");
} else {
    echo "Gagal menghapus data.";
}
?>