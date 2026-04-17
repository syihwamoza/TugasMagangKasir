<?php
session_start();
include '../config/koneksi.php';

if (isset($_POST['id_return'])) {
    $pilihan = $_POST['id_return']; 
    $alasan = isset($_POST['alasan_return']) ? $_POST['alasan_return'] : 'Return';

    foreach ($pilihan as $id) {
        // Ambil data dari transaksi
        $sql = mysqli_query($conn, "SELECT * FROM transaksi WHERE id_transaksi = '$id'");
        $data = mysqli_fetch_assoc($sql);

        if ($data) {
            $nama = $data['nama_barang'];
            $jumlah = $data['jumlah'];
            $tgl = date('Y-m-d H:i:s');

            // 1. Masukkan ke tabel pengembalian
            $insert = mysqli_query($conn, "INSERT INTO pengembalian (nama_barang, jumlah_return, tanggal_return, alasan) 
                                           VALUES ('$nama', '$jumlah', '$tgl', '$alasan')");

            if ($insert) {
                // 2. Tambah stok kembali ke tabel barang
                // removed stok update

                // 3. Hapus data dari transaksi
                mysqli_query($conn, "DELETE FROM transaksi WHERE id_transaksi = '$id'");
            }
        }
    }
    // Pakai SweetAlert nanti di riwayat_return atau redirect biasa
    header("Location: ../riwayat_return.php?pesan=success");
} else {
    echo "<script>alert('Pilih barang dulu Moza!'); window.location='riwayat.php';</script>";
}
?>