<?php
$host = "localhost:3309"; 
$user = "root";
$pass = ""; 
$db   = "toko_maju_jaya";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi ke database gagal: " . mysqli_connect_error());
}

function ensureBarangStokAwalColumn($conn) {
    $result = mysqli_query($conn, "SHOW COLUMNS FROM barang LIKE 'stok_awal'");
    if ($result && mysqli_num_rows($result) == 0) {
        mysqli_query($conn, "ALTER TABLE barang ADD stok_awal INT NOT NULL DEFAULT 0 AFTER stok");
        mysqli_query($conn, "UPDATE barang b
            LEFT JOIN (SELECT nama_barang, SUM(jumlah_beli) AS total_beli FROM pembelian GROUP BY nama_barang) p ON p.nama_barang = b.nama_barang
            LEFT JOIN (SELECT nama_barang, SUM(jumlah) AS total_terjual FROM transaksi GROUP BY nama_barang) t ON t.nama_barang = b.nama_barang
            LEFT JOIN (SELECT nama_barang, SUM(jumlah_return) AS total_return FROM pengembalian GROUP BY nama_barang) r ON r.nama_barang = b.nama_barang
            SET b.stok_awal = b.stok - COALESCE(p.total_beli,0) + COALESCE(t.total_terjual,0) - COALESCE(r.total_return,0)");
    }
}

function getBarangStokAwal($conn, $barangData) {
    if (isset($barangData['stok_awal']) && $barangData['stok_awal'] !== null) {
        return (int)$barangData['stok_awal'];
    }

    $nama = mysqli_real_escape_string($conn, $barangData['nama_barang']);
    $stok_akhir = (int)$barangData['stok'];
    $tambah = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(jumlah_beli) AS total FROM pembelian WHERE nama_barang = '$nama'"))['total'] ?? 0;
    $terjual = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(jumlah) AS total FROM transaksi WHERE nama_barang = '$nama'"))['total'] ?? 0;
    $direturn = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(jumlah_return) AS total FROM pengembalian WHERE nama_barang = '$nama'"))['total'] ?? 0;

    return $stok_akhir - $tambah + $terjual - $direturn;
}

function ensureStockAdjustmentTable($conn) {
    mysqli_query($conn, "CREATE TABLE IF NOT EXISTS stok_penyesuaian (
        id_penyesuaian INT AUTO_INCREMENT PRIMARY KEY,
        nama_barang VARCHAR(100) NOT NULL,
        jumlah INT NOT NULL,
        keterangan VARCHAR(255) DEFAULT 'Penyesuaian Stok Manual',
        tanggal DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
}

function getTotalStockAdjustment($conn, $barangData) {
    $nama = mysqli_real_escape_string($conn, $barangData['nama_barang']);
    $explicit = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(jumlah) as total FROM stok_penyesuaian WHERE nama_barang = '$nama'"))['total'] ?? 0;

    if (!isset($barangData['stok']) || !isset($barangData['stok_awal'])) {
        return (int)$explicit;
    }

    $currentStock = (int)$barangData['stok'];
    $stokAwal = getBarangStokAwal($conn, $barangData);
    $tambah = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(jumlah_beli) AS total FROM pembelian WHERE nama_barang = '$nama'"))['total'] ?? 0;
    $terjual = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(jumlah) AS total FROM transaksi WHERE nama_barang = '$nama'"))['total'] ?? 0;
    $direturn = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(jumlah_return) AS total FROM pengembalian WHERE nama_barang = '$nama'"))['total'] ?? 0;

    $expectedStock = $stokAwal + $tambah - $terjual + $direturn + (int)$explicit;
    $residual = $currentStock - $expectedStock;

    return (int)$explicit + $residual;
}

if (basename($_SERVER['SCRIPT_NAME']) !== 'login.php' && basename($_SERVER['SCRIPT_NAME']) !== 'login_aksi.php' && basename($_SERVER['SCRIPT_NAME']) !== 'logout.php') {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit;
    }

    $kasir_allowed = ['barang.php', 'keranjang.php', 'keranjang_aksi.php', 'cetak_struk.php', 'cetak_invoice.php'];
    if ($_SESSION['role'] === 'kasir' && !in_array(basename($_SERVER['SCRIPT_NAME']), $kasir_allowed)) {
        echo "<script>alert('Akses Ditolak! Kasir hanya bisa mengakses fitur penjualan.'); window.location.href='barang.php';</script>";
        exit;
    }
}

?>