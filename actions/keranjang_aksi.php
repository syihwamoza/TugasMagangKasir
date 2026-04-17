<?php
session_start();
include '../config/koneksi.php';

// 1. LOGIKA TAMBAH KE KERANJANG
if(isset($_POST['add_to_cart'])){
    $id = $_POST['id_barang'];
    $jml = $_POST['jumlah'];

    // Ambil data barang dari database berdasarkan ID
    $res = mysqli_query($conn, "SELECT * FROM barang WHERE id_barang='$id'");
    $b = mysqli_fetch_array($res);

    // Simpan ke session keranjang
    $_SESSION['keranjang'][$id] = [
        'id'     => $id,
        'nama'   => $b['nama_barang'],
        'harga'  => $b['harga'],
        'jumlah' => $jml
    ];
    
    header("Location: ../barang.php");
    exit();
}

// 2. LOGIKA HAPUS ITEM DARI KERANJANG
if(isset($_GET['hapus'])){
    $id = $_GET['hapus'];
    unset($_SESSION['keranjang'][$id]);
    
    $_SESSION['pesan'] = "Barang dihapus dari keranjang.";
    $_SESSION['warna'] = "danger";
    header("Location: ../keranjang.php");
    exit();
}

// 3. LOGIKA CHECKOUT (SIMPAN KE DATABASE)
if(isset($_POST['checkout'])){
    // Pastikan keranjang tidak kosong
    if(!empty($_SESSION['keranjang'])){
        
        foreach($_SESSION['keranjang'] as $id => $val){
            $nama   = mysqli_real_escape_string($conn, $val['nama']);
            $harga  = $val['harga'];
            $jml    = $val['jumlah'];
            $total  = $harga * $jml;
            $tgl    = date('Y-m-d H:i:s');

            // A. Simpan ke tabel transaksi
            $insert = mysqli_query($conn, "INSERT INTO transaksi (nama_barang, jumlah, harga_satuan, total_harga, tanggal) 
                                          VALUES ('$nama', '$jml', '$harga', '$total', '$tgl')");
            
            // B. Potong stok di tabel barang
            if($insert){
                // removed stok update
            }
        }

        // Kosongkan keranjang setelah berhasil simpan
        unset($_SESSION['keranjang']);

        $_SESSION['pesan'] = "Checkout Berhasil! 
        <a href='exports/cetak_invoice.php' target='_blank' style='color:#333; font-weight:bold;'>[Cetak Invoice]</a> atau 
        <a href='exports/cetak_struk.php?last=1' target='_blank' style='color:#333; font-weight:bold;'>[Cetak Struk]</a>";
        $_SESSION['warna'] = "success";
        
        header("Location: ../keranjang.php");
        exit();
    } else {
        header("Location: ../barang.php");
        exit();
    }
}

// Jika akses file tanpa tombol, kembalikan ke awal
header("Location: ../barang.php");
exit();
?>