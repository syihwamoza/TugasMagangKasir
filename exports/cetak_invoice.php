<?php
include '../config/koneksi.php';

// 1. Ambil waktu transaksi paling baru dari database
$getLastTime = mysqli_query($conn, "SELECT tanggal FROM transaksi ORDER BY tanggal DESC LIMIT 1");
$rowTime = mysqli_fetch_assoc($getLastTime);
$lastTime = $rowTime['tanggal'];

// 2. Ambil semua barang yang dibeli pada waktu yang sama tersebut
$query = mysqli_query($conn, "SELECT * FROM transaksi WHERE tanggal = '$lastTime'");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice - Maju Jaya</title>
    <style>
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background-color: #f4f7f6;
            margin: 0;
            padding: 40px;
        }
        
        .invoice-card {
            background: #fff;
            max-width: 700px;
            margin: auto;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            position: relative;
            overflow: hidden;
        }

        .invoice-card::before {
            content: "";
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 8px;
            background: linear-gradient(to right, #27ae60, #2ecc71);
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #f1f1f1;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .brand h1 { margin: 0; color: #27ae60; font-size: 28px; letter-spacing: 1px; }
        .brand p { margin: 5px 0; color: #7f8c8d; font-size: 14px; }

        .invoice-info { text-align: right; }
        .invoice-info h2 { margin: 0; color: #34495e; font-size: 20px; }
        .invoice-info p { margin: 5px 0; color: #7f8c8d; font-size: 13px; }

        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { 
            background-color: #f9f9f9; 
            color: #34495e; 
            text-align: left; 
            padding: 15px; 
            font-size: 14px;
            border-bottom: 2px solid #eee;
        }
        td { padding: 15px; border-bottom: 1px solid #eee; color: #2c3e50; font-size: 14px; }

        .total-row td { 
            border-bottom: none; 
            padding-top: 30px; 
            font-size: 18px; 
            font-weight: bold;
            color: #27ae60;
        }

        .footer { 
            margin-top: 50px; 
            text-align: center; 
            color: #bdc3c7; 
            font-size: 12px;
            border-top: 1px solid #f1f1f1;
            padding-top: 20px;
        }

        .btn-group {
            text-align: center;
            margin-bottom: 20px;
        }

        .btn {
            padding: 12px 25px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            font-size: 14px;
            cursor: pointer;
            border: none;
            display: inline-block;
        }

        .btn-print { background: #27ae60; color: white; }
        .btn-back { background: #ecf0f1; color: #7f8c8d; margin-left: 10px; }

        @media print {
            body { background: white; padding: 0; }
            .invoice-card { box-shadow: none; max-width: 100%; padding: 20px; }
            .btn-group { display: none; }
        }
    </style>
</head>
<body>

    <div class="btn-group">
        <button onclick="window.print()" class="btn btn-print">Cetak Invoice / Simpan PDF</button>
        <a href="../riwayat.php" class="btn btn-back">Kembali</a>
    </div>

    <div class="invoice-card">
        <div class="header">
            <div class="brand">
                <h1>MAJU JAYA</h1>
                <p>Universitas Muhammadiyah Yogyakarta</p>
                <p>Bantul, D.I. Yogyakarta</p>
            </div>
            <div class="invoice-info">
                <h2>INVOICE</h2>
                <p>Tgl: <b><?php echo date('d M Y', strtotime($lastTime)); ?></b></p>
                <p>Jam: <?php echo date('H:i', strtotime($lastTime)); ?> WIB</p>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Item Deskripsi</th>
                    <th style="text-align: center;">Qty</th>
                    <th style="text-align: right;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $total_bayar = 0;
                while($row = mysqli_fetch_array($query)){ 
                    $total_bayar += $row['total_harga'];
                ?>
                <tr>
                    <td>
                        <div style="font-weight: bold;"><?php echo $row['nama_barang']; ?></div>
                        <div style="font-size: 11px; color: #95a5a6;">ID: TRX-<?php echo $row['id_transaksi']; ?></div>
                    </td>
                    <td align="center"><?php echo $row['jumlah']; ?></td>
                    <td align="right">Rp <?php echo number_format($row['total_harga'], 0, ',', '.'); ?></td>
                </tr>
                <?php } ?>
                <tr class="total-row">
                    <td colspan="2" align="right">TOTAL PEMBAYARAN</td>
                    <td align="right">Rp <?php echo number_format($total_bayar, 0, ',', '.'); ?></td>
                </tr>
            </tbody>
        </table>

        <div class="footer">
            <p>Terima kasih telah berbelanja di Maju Jaya.</p>
            <p>&copy; 2026 Maju Jaya Store - Invoice ini sah sebagai bukti pembayaran.</p>
        </div>
    </div>

</body>
</html>