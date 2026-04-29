    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <title>Edit Barang - Maju Jaya</title>
        <link rel="stylesheet" href="<?= base_url('assets/style.css') ?>">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head>
    <body>
        <?php $this->load->view('templates/navbar'); ?>

        <div style="max-width: 500px; margin: 50px auto; background: white; padding: 30px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1);">
            <h2 style="text-align: center; color: var(--primary); margin-top: 0; margin-bottom: 25px;">Edit Data Barang</h2>
            <form action="<?= site_url('barang/edit/'.$d['id_barang']) ?>" method="POST" enctype="multipart/form-data">
                
                <?php if(!empty($d['gambar'])): ?>
                <div style="text-align: center; margin-bottom: 20px;">
                    <img src="<?= base_url('../assets/uploads/'.$d['gambar']); ?>" alt="Foto Barang"style="max-height: 150px; border-radius: 8px;">
                </div>
                <?php endif; ?>

                <div style="margin-bottom: 15px;">
                    <label style="display:block; font-weight: 600; margin-bottom: 8px;">Ganti Foto (Opsional)</label>
                    <input type="file" name="gambar" accept="image/*" style="width: 100%; padding: 8px; border-radius: 5px; border: 1px dashed #aaa; background: #fafafa;">
                </div>

                <div style="margin-bottom: 15px;">
                    <label style="display:block; font-weight: 600; margin-bottom: 8px;">Nama Barang</label>
                    <input type="text" value="<?= $d['nama_barang'] ?>" disabled style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #ddd; background: #eee; cursor: not-allowed;">
                </div>
                <div style="margin-bottom: 15px;">
                    <label style="display:block; font-weight: 600; margin-bottom: 8px;">Harga (Rp)</label>
                   <input type="text" name="harga" id="harga" value="<?= number_format($d['harga'], 0, ',', '.') ?>" required style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #ddd; background: #fff;">
                </div>
                <div style="margin-bottom: 25px;">
                    <label style="display:block; font-weight: 600; margin-bottom: 8px;">Stok Saat Ini</label>
                    <input type="text" name="stok" id="stok" value="<?= number_format($d['stok_aktif'], 0, '.', ',') ?>" required style="width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #ddd; background: #fff;">
                    <small style="color: #7f8c8d; display:block; margin-top:5px;">*Ubah angka untuk menambah stok (akan tercatat di Tracking).</small>
                </div>
                
                <input type="submit" name="update" value="Simpan Perubahan" style="width: 100%; padding: 14px; background: #2980b9; color: white; border: none; border-radius: 8px; font-weight: bold; font-size: 16px; cursor: pointer;">
                <a href="<?= site_url('barang') ?>" style="display: block; text-align: center; margin-top: 15px; color: #e74c3c; text-decoration: none; font-size: 14px; font-weight:600;">Kembali ke Daftar</a>
            </form>
        </div>

        <?php if ($this->session->flashdata('sukses')): ?>
        <script>
            Swal.fire({
                title: 'Berhasil!',
                text: '<?= $this->session->flashdata('sukses') ?>',
                icon: 'success',
                confirmButtonColor: '#2980b9'
            }).then(() => {
                window.location.href = '<?= site_url('barang') ?>';
            });
        </script>
        <?php endif; ?>
       <script>
const hargaInput = document.getElementById('harga');

hargaInput.addEventListener('input', function(e) {
    let value = this.value.replace(/\D/g, '');
    this.value = new Intl.NumberFormat('id-ID').format(value);
});
</script>

<script>
document.addEventListener("DOMContentLoaded", function () {

    function formatAngka(input) {
        let angka = input.value.replace(/[^0-9]/g, '');
        input.value = angka.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
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
