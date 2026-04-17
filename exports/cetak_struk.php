<?php
session_start();
include '../config/koneksi.php';

// Cek parameter id atau last
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = mysqli_query($conn, "SELECT * FROM transaksi WHERE id_transaksi = '$id'");
    $data = mysqli_fetch_array($query);

    if (!$data) {
        echo "Data transaksi tidak ditemukan.";
        exit;
    }
} elseif (isset($_GET['last'])) {
    // Ambil waktu transaksi terakhir
    $lastTimeRes = mysqli_query($conn, "SELECT tanggal FROM transaksi ORDER BY tanggal DESC LIMIT 1");
    $lastTimeData = mysqli_fetch_assoc($lastTimeRes);

    if (!$lastTimeData) {
        echo "Tidak ada data transaksi.";
        exit;
    }

    $lastTime = $lastTimeData['tanggal'];
    $query = mysqli_query($conn, "SELECT * FROM transaksi WHERE tanggal = '$lastTime'");
    $data = mysqli_fetch_array($query);

    if (!$data) {
        echo "Data transaksi tidak ditemukan.";
        exit;
    }
} else {
    header("Location: ../barang.php");
    exit;
}

// Jika menggunakan last, $data berisi transaksi pertama pada grup terakhir.
$nota_id = $data['id_transaksi'];
$nota_time = $data['tanggal'];
$items = [];
$total_harga = 0;

if (isset($_GET['last'])) {
    mysqli_data_seek($query, 0);
    while($row = mysqli_fetch_assoc($query)) {
        $items[] = $row;
        $total_harga += $row['total_harga'];
    }
} else {
    $items[] = $data;
    $total_harga = $data['total_harga'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk Pembayaran #<?php echo $nota_id; ?></title>
    <style>
        body { font-family: 'Courier New', Courier, monospace; width: 300px; margin: 20px auto; padding: 10px; border: 1px solid #000; }
        .text-center { text-align: center; }
        .line { border-bottom: 1px dashed #000; margin: 10px 0; }
        table { width: 100%; font-size: 12px; }
        .btn-kembali { display: block; text-align: center; margin-top: 20px; text-decoration: none; color: blue; font-size: 12px; }
        
        /* Hilangkan tombol saat dicetak */
        @media print {
            .btn-kembali { display: none; }
            body { border: none; margin: 0; }
        }
    </style>
</head>
<body>

    <div class="text-center">
        <h3 style="margin:0;">MAJU JAYA</h3>
        <p style="font-size:10px;">Jl. Tamantirto, Kasihan, Bantul</p>
    </div>

    <div class="line"></div>

    <table>
        <tr>
            <td>No. Nota</td>
            <td>: #<?php echo $nota_id; ?></td>
        </tr>
        <tr>
            <td>Tanggal</td>
            <td>: <?php echo date('d/m/Y H:i', strtotime($data['tanggal'])); ?></td>
        </tr>
    </table>

    <div class="line"></div>

    <table>
        <?php foreach($items as $item): ?>
        <tr>
            <td colspan="2"><?php echo htmlspecialchars($item['nama_barang']); ?></td>
        </tr>
        <tr>
            <td><?php echo $item['jumlah']; ?> x Rp <?php echo number_format($item['harga_satuan'], 0, ',', '.'); ?></td>
            <td style="text-align: right;">Rp <?php echo number_format($item['total_harga'], 0, ',', '.'); ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

    <div class="line"></div>

    <table style="font-weight: bold;">
        <tr>
            <td>TOTAL</td>
            <td style="text-align: right;">Rp <?php echo number_format($total_harga, 0, ',', '.'); ?></td>
        </tr>
    </table>

    <div class="line"></div>

    <div class="text-center" style="font-size: 10px;">
        <p>Terima Kasih Telah Berbelanja!</p>
        <p>Barang yang sudah dibeli tidak dapat ditukar.</p>
    </div>

    <script>
        window.print();
    </script>

    <a href="../barang.php" class="btn-kembali"> Kembali Belanja</a>

</body>
</html>