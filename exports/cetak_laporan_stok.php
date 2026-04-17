<?php 
include '../config/koneksi.php'; 

ensureBarangStokAwalColumn($conn);
ensureStockAdjustmentTable($conn);

$cari = isset($_GET['cari']) ? mysqli_real_escape_string($conn, $_GET['cari']) : '';
$where = !empty($cari) ? " WHERE nama_barang LIKE '%$cari%' " : "";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Stok - Maju Jaya Stationery</title>
    <link href="https://fonts.googleapis.com/css2?family=Segoe+UI:wght@400;600;700&display=swap" rel="stylesheet">
    
    <style>
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            padding: 20px; 
            color: #333; 
            background-color: #fff;
            margin: 0;
        }
        
        /* Kop Surat Lebih Elegan */
        .header { 
            text-align: center; 
            margin-bottom: 30px; 
            border-bottom: 4px solid #2c3e50; /* Garis tebal warna gelap */
            padding-bottom: 15px; 
            position: relative;
        }
        .header h1 { 
            margin: 0; 
            font-size: 28px; 
            font-weight: 700; 
            color: #2c3e50;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .header p { 
            margin: 5px 0; 
            font-size: 16px; 
            color: #7f8c8d; 
            font-weight: 400;
        }
        .header small {
            display: block;
            margin-top: 10px;
            font-size: 12px;
            color: #95a5a6;
        }

        /* Styling Tabel Lebih Lembut */
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 10px; 
            box-shadow: 0 2px 5px rgba(0,0,0,0.05); /* Bayangan halus */
        }
        th, td { 
            border: 1px solid #e0e0e0; /* Garis abu-abu terang */
            padding: 12px 10px; 
            text-align: center; 
            font-size: 13px; 
        }
        
        /* Header Tabel Warna Navbar */
        th { 
            background-color: #34495e; /* Warna gelap navbar kamu */
            color: white; 
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        
        /* Warna Selang-Seling Baris */
        tbody tr:nth-child(even) {
            background-color: #fbfcfc;
        }
        tbody tr:hover {
            background-color: #f1f4f6; /* Efek hover saat dilihat di layar */
        }

        /* Penonjolan Kolom Penting */
        .col-nama { text-align: left; font-weight: 600; color: #2c3e50; }
        .col-stok-akhir { font-weight: bold; color: #27ae60; background-color: #eafaf1; } /* Hijau tua */
        .col-masuk { color: #2980b9; } /* Biru */
        .col-keluar { color: #e67e22; } /* Oranye */

        /* Tanda Tangan */
        .footer { 
            margin-top: 60px; 
            width: 100%;
            display: flex;
            justify-content: flex-end; /* Taruh di kanan */
        }
        .ttd-box {
            text-align: center;
            width: 250px;
            font-size: 13px;
        }
        
        /* Styling untuk cetakan */
        @page {
            size: A4;
            margin: 1cm;
            margin-bottom: 1.5cm;
        }

        @media print {
            body { padding: 0; }
            .no-print { display: none !important; }
            table { box-shadow: none; }
            th { -webkit-print-color-adjust: exact; print-color-adjust: exact; } /* Pastikan background th tercetak */
            .col-stok-akhir { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="header">
        <h1>MAJU JAYA STATIONERY</h1>
        <p>Laporan Pergerakan & Saldo Stok Barang</p>
        <small>Data akurat per tanggal: <?php echo date('d F Y'); ?> | Waktu: <?php echo date('H:i'); ?> WIB</small>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th style="text-align: left; padding-left: 15px;">Nama Barang</th>
                <th width="12%">Stok Awal</th>
                <th width="12%">Barang Masuk</th>
                <th width="12%">Terjual</th>
                <th width="12%">Direturn</th>
                <th width="15%">Saldo Akhir (Ready)</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            // Gunakan query yang sama persis dengan halaman tracking_stok
            $res = mysqli_query($conn, "SELECT * FROM barang $where ORDER BY nama_barang ASC");
            
            if(mysqli_num_rows($res) > 0) {
                while($b = mysqli_fetch_array($res)){
                    $nama = $b['nama_barang'];
                    // Hitung data pendukung
                    $tambah = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(jumlah_beli) as total FROM pembelian WHERE nama_barang = '$nama'"))['total'] ?? 0;
                    $terjual = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(jumlah) as total FROM transaksi WHERE nama_barang = '$nama'"))['total'] ?? 0;
                    $direturn = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(jumlah_return) as total FROM pengembalian WHERE nama_barang = '$nama'"))['total'] ?? 0;

                    $stok_awal = getBarangStokAwal($conn, $b);
                    $stok_akhir = $stok_awal + $tambah - $terjual + $direturn;
                ?>
                <tr>
                    <td><?php echo $no++; ?></td>
                    <td class="col-nama" style="padding-left: 15px;"><?php echo $nama; ?></td>
                    <td style="background-color: #fef9e7;"><?php echo $stok_awal; ?></td>
                    <td class="col-masuk"><?php echo ($tambah > 0) ? '+'.$tambah : '-'; ?></td>
                    <td class="col-keluar"><?php echo ($terjual > 0) ? '-'.$terjual : '-'; ?></td>
                    <td style="color: #27ae60;"><?php echo ($direturn > 0) ? '+'.$direturn : '-'; ?></td>
                    <td class="col-stok-akhir"><?php echo $stok_akhir; ?></td>
                </tr>
                <?php 
                }
            } else {
                echo "<tr><td colspan='7' style='padding:30px; color:#999; font-style:italic;'>Tidak ada data barang untuk dilaporkan.</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <div class="footer">
        <div class="ttd-box">
            <p>Yogyakarta, <?php echo date('d F Y'); ?></p>
            <p style="margin-bottom: 70px;">Dicetak dan Diperiksa Oleh,</p>
            <p><b>_________________________</b></p>
            <p style="color: #7f8c8d; font-size: 11px;">( Admin Inventaris )</p>
        </div>
    </div>

    <div class="no-print" style="position: fixed; bottom: 20px; right: 20px; display: flex; gap: 10px;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #34495e; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: bold;">Cetak Ulang</button>
        <button onclick="window.close()" style="padding: 10px 20px; background: #e74c3c; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: bold;">Tutup</button>
    </div>

</body>
</html>