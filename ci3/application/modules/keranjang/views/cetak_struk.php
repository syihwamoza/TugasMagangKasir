<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk #<?= $items[0]['id_transaksi'] ?></title>
    <style>
        body { font-family: 'Courier New', Courier, monospace; width: 300px; margin: 0 auto; padding: 20px; color: #000; font-size: 13px; line-height: 1.2; }
        .text-center { text-align: center; }
        .divider { border-top: 1px dashed #000; margin: 10px 0; }
        .item-row { display: flex; justify-content: space-between; margin-bottom: 5px; }
        .item-name { flex: 2; }
        .item-qty { flex: 0.5; text-align: center; }
        .item-price { flex: 1.5; text-align: right; }
        .total-row { display: flex; justify-content: space-between; font-weight: bold; font-size: 15px; margin-top: 10px; }
        @media print {
            body { width: 100%; padding: 0; margin: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="text-center">
        <h2 style="margin:0;">MAJU JAYA</h2>
        <p style="margin:5px 0;">Jl. Contoh Alamat Toko No. 123<br>Telp: 081234567890</p>
    </div>

    <div class="divider"></div>
    
    <div style="font-size: 11px;">
        ID: #<?= $items[0]['id_transaksi'] ?><br>
        Tgl: <?= date('d/m/y H:i', strtotime($items[0]['tanggal'])) ?><br>
        Kasir: <?= $this->session->userdata('username') ?>
    </div>

    <div class="divider"></div>

    <?php $total = 0; foreach($items as $item): $total += $item['total_harga']; ?>
        <div style="margin-bottom: 8px;">
            <div><?= $item['nama_barang'] ?></div>
            <div class="item-row">
                <div class="item-qty"><?= $item['jumlah'] ?> x</div>
                <div class="item-price"><?= number_format($item['harga_satuan'], 0, ',', '.') ?></div>
                <div class="item-price"><?= number_format($item['total_harga'], 0, ',', '.') ?></div>
            </div>
        </div>
    <?php endforeach; ?>

    <div class="divider"></div>

    <div class="total-row">
        <span>TOTAL</span>
        <span>Rp <?= number_format($total, 0, ',', '.') ?></span>
    </div>

    <div class="divider"></div>

    <div class="text-center" style="margin-top: 20px;">
        <p>*** TERIMA KASIH ***<br>Selamat Datang Kembali</p>
    </div>

    <div class="no-print" style="text-align:center; margin-top:20px;">
        <button onclick="window.close()">Tutup</button>
    </div>
</body>
</html>
