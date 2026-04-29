<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Barang - Maju Jaya</title>
    <link rel="stylesheet" href="<?= base_url('assets/style.css') ?>">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <?php $this->load->view('templates/navbar'); ?>

    <div style="max-width: 450px; margin: 50px auto; background: white; padding: 30px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1);">
        <h2 style="text-align: center; color:var(--primary); margin-bottom: 25px;">Katalog Baru</h2>
        
        <form action="<?= site_url('barang/tambah') ?>" method="POST" enctype="multipart/form-data">
            <div style="margin-bottom: 15px;">
                <label style="display:block; font-weight: 600; margin-bottom: 8px;">Foto Produk (Opsional)</label>
                <input type="file" name="gambar" accept="image/*" style="width: 100%; padding: 8px; border-radius: 5px; border: 1px dashed #aaa; background: #fafafa;">
            </div>
            <div style="margin-bottom: 15px;">
                <label style="display:block; font-weight: 600; margin-bottom: 8px;">Nama Barang</label>
                <input type="text" name="nama_barang" id="nama_barang" required style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #ddd; background: #fff;">
            </div>
            <div style="margin-bottom: 15px;">
                <label style="display:block; font-weight: 600; margin-bottom: 8px;">Harga (Rp)</label>
                <input type="text" name="harga" id="harga" required style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #ddd; background: #fff;">
            </div>
            <div style="margin-bottom: 25px;">
                <label style="display:block; font-weight: 600; margin-bottom: 8px;">Stok Awal</label>
                <input type="text" name="stok" id="stok" required style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #ddd; background: #fff;">
            </div>
            <input type="submit" name="simpan_baru" value="Simpan ke Etalase" style="width: 100%; padding: 14px; background: #27ae60; color: white; border: none; border-radius: 8px; font-weight: bold; font-size: 16px; cursor: pointer;">
        </form>
    </div>

    <?php if ($this->session->flashdata('sukses')): ?>
    <script>
        Swal.fire({
            title: 'Berhasil!',
            text: '<?= $this->session->flashdata('sukses') ?>',
            icon: 'success',
            confirmButtonColor: '#2c3e50'
        }).then(() => {
            window.location.href = '<?= site_url('barang') ?>';
        });
    </script>
    <?php endif; ?>

  <script>
function formatAngka(input) {
    let angka = input.value.replace(/[^0-9]/g, '');
    let formatted = angka.replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    input.value = formatted;
}

const harga = document.getElementById('harga');
const stok = document.getElementById('stok');

if (harga) {
    harga.addEventListener('input', function() {
        formatAngka(this);
    });
}

if (stok) {
    stok.addEventListener('input', function() {
        formatAngka(this);
    });
}
</script>

<script>
document.addEventListener("DOMContentLoaded", function () {

    function formatAngka(input) {
        let angka = input.value.replace(/[^0-9]/g, '');
        input.value = angka.replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

    const harga = document.getElementById('harga');
    const stok  = document.getElementById('stok');

    [harga, stok].forEach(function(el) {
        if (el) {
            el.addEventListener('input', function() {
                formatAngka(this);
            });
        }
    });

});
</script>
</body>
</html>
