<?php 
session_start();
include 'config/koneksi.php'; 

if (isset($_POST['proses_tambah'])) {
    $id = $_POST['id_barang'];
    $jumlah = $_POST['jumlah_beli'];
    $supplier = mysqli_real_escape_string($conn, $_POST['supplier'] ?? '');
    $harga = $_POST['harga_beli'] ?? 0;
    
    // Ambil data barang
    $cek = mysqli_query($conn, "SELECT nama_barang FROM barang WHERE id_barang = '$id'");
    $d = mysqli_fetch_assoc($cek);
    $nama = $d['nama_barang'];

    // 1. Update stok utama (Stok Akhir bertambah)
    // removed stok update

    // 2. CATAT KE PEMBELIAN (Agar masuk kolom TAMBAH di tracking)
    mysqli_query($conn, "INSERT INTO pembelian (nama_barang, jumlah_beli, harga_beli, supplier, tanggal_beli) 
                        VALUES ('$nama', '$jumlah', '$harga', '$supplier', NOW())");

    echo "<script>alert('Berhasil menambah stok! Data masuk ke kolom TAMBAH.'); window.location='belanja_supplier.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Belanja Stok - Maju Jaya</title>
    <link rel="stylesheet" href="assets/style.css?v=<?php echo time(); ?>">
</head>
<body>
    <?php include 'components/navbar.php'; ?>

    <div class="container" style="margin-top: 30px;">
        <h2 style="border-left: 5px solid #2980b9; padding-left: 15px;">Belanja Stok (Restock)</h2>
        <p>Gunakan halaman ini untuk menambah stok barang yang sudah terdaftar.</p>

        <table>
            <thead>
                <tr>
                    <th>Nama Barang</th>
                    <th>Stok Sekarang</th>
                    <th>Supplier</th>
                    <th>Harga Satuan</th>
                    <th>Tambah Stok</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $res = mysqli_query($conn, "SELECT * FROM barang ORDER BY nama_barang ASC");
                while($row = mysqli_fetch_array($res)){
                ?>
                <tr>
                    <td><b><?php echo $row['nama_barang']; ?></b></td>
                    <td align="center"><?php echo $row['stok']; ?> unit</td>
                    <td>
                        <form method="POST" style="display: flex; gap: 8px; justify-content: center; align-items: center;">
                            <input type="hidden" name="id_barang" value="<?php echo $row['id_barang']; ?>">
                            <input type="text" name="supplier" placeholder="Supplier" style="width: 120px; padding: 5px;" required>
                            <input type="number" name="harga_beli" min="0" placeholder="Harga" style="width: 100px; padding: 5px;" required>
                            <input type="number" name="jumlah_beli" min="1" placeholder="Jumlah" required style="width: 70px; padding: 5px;">
                            <button type="submit" name="proses_tambah" style="background: #27ae60; color: white; border: none; padding: 5px 15px; border-radius: 4px; cursor: pointer; white-space: nowrap;">Tambah</button>
                        </form>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>