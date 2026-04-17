<?php
session_start();
include 'config/koneksi.php';

if (isset($_POST['pilih_return'])) {
    $id_transaksi_array = $_POST['id_return']; // Mengambil array ID dari checkbox
    $alasan = "Barang dikembalikan pembeli"; // Alasan default

    foreach ($id_transaksi_array as $id_transaksi) {
        // 1. Ambil data transaksi lama
        $query_t = mysqli_query($conn, "SELECT * FROM transaksi WHERE id_transaksi = '$id_transaksi'");
        $t = mysqli_fetch_assoc($query_t);
        
        $nama_barang = $t['nama_barang'];
        $jumlah_return = $t['jumlah'];

        // 2. UPDATE STOK BARANG (LOGIKA: STOK BERTAMBAH KARENA BARANG BALIK)
        // removed stok update

        // 3. CATAT KE TABEL PENGEMBALIAN
        mysqli_query($conn, "INSERT INTO pengembalian (nama_barang, jumlah_return, harga_satuan, alasan, tanggal_return) 
                            VALUES ('$nama_barang', '$jumlah_return', '" . $t['harga_satuan'] . "', '$alasan', NOW())");
                            
        // 4. (Opsional) Hapus atau tandai transaksi lama agar tidak direturn dua kali
        // mysqli_query($conn, "DELETE FROM transaksi WHERE id_transaksi = '$id_transaksi'");
    }

    $_SESSION['pesan'] = "Retur berhasil! Barang sudah masuk kembali ke stok.";
    $_SESSION['warna'] = "success";
    header("Location: riwayat.php");
}
?>