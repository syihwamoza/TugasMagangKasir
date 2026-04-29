<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice #<?= $items[0]['id_transaksi'] ?></title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; margin: 0; padding: 40px; color: #333; line-height: 1.6; }
        .invoice-box { max-width: 800px; margin: auto; border: 1px solid #eee; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.05); }
        .header { display: flex; justify-content: space-between; margin-bottom: 40px; border-bottom: 2px solid #34495e; padding-bottom: 20px; }
        .brand { color: #34495e; }
        .brand h1 { margin: 0; font-size: 28px; letter-spacing: 1px; }
        .info { text-align: right; }
        .info h2 { margin: 0; color: #27ae60; font-size: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 30px; }
        th { background: #f8fafc; padding: 12px; border-bottom: 2px solid #eee; text-align: left; font-size: 14px; }
        td { padding: 12px; border-bottom: 1px solid #eee; font-size: 14px; }
        .total-section { margin-top: 30px; text-align: right; }
        .total-row { display: flex; justify-content: flex-end; gap: 40px; font-size: 18px; font-weight: bold; }
        .footer { margin-top: 50px; text-align: center; color: #888; font-size: 12px; border-top: 1px solid #eee; padding-top: 20px; }
        @media print {
            body { padding: 0; }
            .invoice-box { border: none; box-shadow: none; }
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="invoice-box">
        <div class="header">
            <div class="brand">
                <h1>MAJU JAYA</h1>
                <p>Alamat Toko Maju Jaya<br>Telp: 0812-3456-7890</p>
            </div>
            <div class="info">
                <h2>INVOICE</h2>
                <p>ID Trx: #<?= $items[0]['id_transaksi'] ?><br>
                Tanggal: <?= date('d/m/Y H:i', strtotime($items[0]['tanggal'])) ?></p>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Nama Barang</th>
                    <th>Harga Satuan</th>
                    <th style="text-align:center;">Jumlah</th>
                    <th style="text-align:right;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php $total = 0; foreach($items as $item): $total += $item['total_harga']; ?>
                <tr>
                    <td><?= $item['nama_barang'] ?></td>
                    <td>Rp <?= number_format($item['harga_satuan'], 0, ',', '.') ?></td>
                    <td align="center"><?= $item['jumlah'] ?></td>
                    <td align="right">Rp <?= number_format($item['total_harga'], 0, ',', '.') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="total-section">
            <div class="total-row">
                <span>TOTAL BAYAR:</span>
                <span style="color: #27ae60;">Rp <?= number_format($total, 0, ',', '.') ?></span>
            </div>
        </div>

        <div class="footer">
            <p>Terima kasih telah berbelanja di Toko Maju Jaya!<br>Barang yang sudah dibeli dapat diretur sesuai ketentuan yang berlaku.</p>
        </div>
    </div>
    
    <div class="no-print" style="text-align:center; margin-top:20px;">
        <button onclick="window.close()" style="padding:10px 20px; cursor:pointer;">Tutup Halaman</button>
    </div>
</body>
</html>
