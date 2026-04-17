<?php 
session_start(); 
include 'config/koneksi.php'; 
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Keranjang Belanja - Maju Jaya</title>
    <link rel="stylesheet" href="assets/style.css?v=<?php echo time(); ?>">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <a href="barang.php" class="nav-brand">MAJU JAYA</a>
            <div class="nav-menu">
                <a href="barang.php" class="nav-item">Belanja</a>
                <a href="keranjang.php" class="nav-item active">Keranjang (<?php echo isset($_SESSION['keranjang']) ? count($_SESSION['keranjang']) : 0; ?>)</a>
                <a href="riwayat.php" class="nav-item">Riwayat Transaksi</a>
                <a href="riwayat_return.php" class="nav-item">Riwayat Retur</a>
                <a href="tracking_stok.php" class="nav-item">Tracking Stok</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <h2>Keranjang Belanja</h2>
        
        <?php if(isset($_SESSION['pesan'])): ?>
            <div class="alert alert-<?php echo $_SESSION['warna']; ?>">
                <?php echo $_SESSION['pesan']; unset($_SESSION['pesan']); unset($_SESSION['warna']); ?>
            </div>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>Nama Barang</th>
                    <th>Harga</th>
                    <th>Jumlah</th>
                    <th>Subtotal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $total_bayar = 0;
                if(!empty($_SESSION['keranjang'])):
                    foreach($_SESSION['keranjang'] as $id => $item): 
                        $subtotal = $item['harga'] * $item['jumlah'];
                        $total_bayar += $subtotal;
                ?>
                <tr>
                    <td style="text-align: center;"><?php echo $item['nama']; ?></td>
                    <td align="center">Rp <?php echo number_format($item['harga'], 0, ',', '.'); ?></td>
                    <td align="center"><?php echo $item['jumlah']; ?></td>
                    <td align="center">Rp <?php echo number_format($subtotal, 0, ',', '.'); ?></td>
                    <td align="center">
                        <a href="keranjang_aksi.php?hapus=<?php echo $id; ?>" class="btn-hapus" style="text-decoration:none; padding: 5px 10px;">Batal</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="3" align="right"><b>Total Bayar:</b></td>
                    <td colspan="2" align="center"><b>Rp <?php echo number_format($total_bayar, 0, ',', '.'); ?></b></td>
                </tr>
                <?php else: ?>
                    <tr><td colspan="5" align="center" style="padding: 30px; color: #999;">Keranjang masih kosong.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
        
        <div style="margin-top: 30px; display: flex; gap: 10px; justify-content: center;">
            <a href="barang.php" class="btn-reset-modern" style="text-decoration:none; display: flex; align-items: center;">Lanjut Belanja</a>
            <?php if(!empty($_SESSION['keranjang'])): ?>
                <form action="actions/keranjang_aksi.php" method="POST">
                    <button type="submit" name="checkout" class="btn-beli" style="height: 45px; padding: 0 30px;">Proses Checkout</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>