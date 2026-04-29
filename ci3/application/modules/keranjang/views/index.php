<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Keranjang Belanja - Maju Jaya</title>
    <link rel="stylesheet" href="<?= base_url('assets/style.css') ?>">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <?php $this->load->view('templates/navbar'); ?>

    <div class="container">
        <h2>Keranjang Belanja</h2>

        <table class="table">
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
                $total = 0;
                $cart = $this->session->userdata('keranjang') ?? [];
                if(!empty($cart)):
                    foreach($cart as $id => $item): 
                        $sub = $item['harga'] * $item['jumlah'];
                        $total += $sub;
                ?>
                <tr>
                    <td align="center"><?= $item['nama'] ?></td>
                    <td align="center">Rp <?= number_format($item['harga'], 0, ',', '.') ?></td>
                    <td align="center"><?= $item['jumlah'] ?></td>
                    <td align="center">Rp <?= number_format($sub, 0, ',', '.') ?></td>
                    <td align="center">
                        <a href="<?= site_url('keranjang/hapus/'.$id) ?>" class="btn btn-danger">Batal</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="3" align="right"><b>Total Bayar:</b></td>
                    <td colspan="2" align="center"><b>Rp <?= number_format($total, 0, ',', '.') ?></b></td>
                </tr>
                <?php else: ?>
                    <tr><td colspan="5" align="center" style="padding: 30px; color: #999;">Keranjang masih kosong.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
        
        <div style="margin-top: 30px; display: flex; gap: 10px; justify-content: center;">
            <a href="<?= site_url('barang') ?>" class="btn">Lanjut Belanja</a>
            <?php if(!empty($cart)): ?>
                <a href="<?= site_url('keranjang/checkout') ?>" class="btn btn-primary" style="background:#27ae60;">Proses Checkout</a>
            <?php endif; ?>
        </div>
    </div>

    <?php if($this->session->flashdata('checkout_sukses')): ?>
    <script>
        <?php $ids = implode(',', $this->session->flashdata('checkout_ids') ?? []); ?>
        Swal.fire({
            title: 'Checkout Berhasil!',
            text: 'Transaksi telah disimpan. Ingin cetak bukti pembayaran?',
            icon: 'success',
            showCancelButton: true,
            showDenyButton: true,
            confirmButtonText: '📄 Cetak Invoice (A4)',
            denyButtonText: '🧾 Cetak Struk (Thermal)',
            cancelButtonText: 'Nanti Saja',
            confirmButtonColor: '#2980b9',
            denyButtonColor: '#27ae60',
        }).then((result) => {
            if (result.isConfirmed) {
                window.open('<?= site_url("keranjang/cetak/invoice?ids=".$ids) ?>', '_blank');
            } else if (result.isDenied) {
                window.open('<?= site_url("keranjang/cetak/struk?ids=".$ids) ?>', '_blank');
            }
        });
    </script>
    <?php endif; ?>
</body>
</html>
