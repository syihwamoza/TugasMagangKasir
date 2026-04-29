<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Belanja Stok - Maju Jaya</title>
    <link rel="stylesheet" href="<?= base_url('assets/style.css') ?>">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <?php $this->load->view('templates/navbar'); ?>

    <div class="container" style="margin-top: 30px;">
        <h2 style="border-left: 5px solid #2980b9; padding-left: 15px;">Belanja Stok (Restock)</h2>
        <p>Gunakan halaman ini untuk menambah stok barang yang sudah terdaftar.</p>

        <table>
            <thead>
                <tr>
                    <th>Nama Barang</th>
                    <th>Supplier</th>
                    <th>Harga Satuan</th>
                    <th>Tambah Stok</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($barang as $row): ?>
                <tr>
                    <td><b><?= $row['nama_barang'] ?></b></td>
                    <form action="<?= site_url('pembelian/proses') ?>" method="POST">
                        <td>
                            <input type="hidden" name="id_barang" value="<?= $row['id_barang'] ?>">
                            <input type="text" name="supplier" placeholder="Supplier" style="width: 120px; padding: 5px;" required>
                        </td>
                        <td>
                            <input type="number" name="harga_beli" min="0" placeholder="Harga" style="width: 100px; padding: 5px;" required>
                        </td>
                        <td>
                            <input type="number" name="jumlah_beli" min="1" placeholder="Jumlah" required style="width: 70px; padding: 5px;">
                            <button type="submit" style="background: #27ae60; color: white; border: none; padding: 5px 15px; border-radius: 4px; cursor: pointer;">Tambah</button>
                        </td>
                    </form>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php if ($this->session->flashdata('pembelian_sukses')): ?>
    <script>
        Swal.fire({
            title: 'Berhasil!',
            text: '<?= $this->session->flashdata('pembelian_sukses') ?>',
            icon: 'success',
            confirmButtonColor: '#27ae60'
        });
    </script>
    <?php endif; ?>
</body>
</html>
