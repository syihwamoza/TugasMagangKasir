<?php 
session_start();
include 'config/koneksi.php';

$id = isset($_GET['id']) ? $_GET['id'] : (isset($_POST['id_barang']) ? $_POST['id_barang'] : '');
if ($id == '') { header("Location: barang.php"); exit(); }

$result = mysqli_query($conn, "SELECT b.*, (b.stok_awal + COALESCE((SELECT SUM(jumlah_beli) FROM pembelian WHERE nama_barang = b.nama_barang), 0) - COALESCE((SELECT SUM(jumlah) FROM transaksi WHERE nama_barang = b.nama_barang), 0) + COALESCE((SELECT SUM(jumlah_return) FROM pengembalian WHERE nama_barang = b.nama_barang), 0)) AS stok_aktif FROM barang b WHERE id_barang='$id'");
$b = mysqli_fetch_array($result);

if(isset($_POST['proses'])){
    $id_barang = $_POST['id_barang'];
    $nama_barang = $b['nama_barang'];
    $harga_satuan = $b['harga'];
    $jumlah = $_POST['jumlah'];
    
    // Konversi ke int agar tidak error string * string
    $total = (int)$harga_satuan * (int)$jumlah;
    
    if($b['stok_aktif'] <= 0){
        $_SESSION['pesan'] = "Gagal! Stok habis.";
        $_SESSION['warna'] = "danger";
        header("Location: barang.php"); exit();
    } else if($jumlah <= 0 || $jumlah == ""){
        $_SESSION['pesan'] = "Gagal! Isi jumlah minimal 1.";
        $_SESSION['warna'] = "danger";
        header("Location: barang.php"); exit();
    } else if($jumlah > $b['stok_aktif']){
        $_SESSION['pesan'] = "Gagal! Stok kurang.";
        $_SESSION['warna'] = "danger";
        header("Location: barang.php"); exit();
    } else {
        $simpan = mysqli_query($conn, "INSERT INTO transaksi (nama_barang, jumlah, harga_satuan, total_harga) 
                  VALUES ('$nama_barang', '$jumlah', '$harga_satuan', '$total')");
        if($simpan){
            // removed stok update
            $_SESSION['pesan'] = "Berhasil membeli $nama_barang!";
            $_SESSION['warna'] = "success";
            header("Location: riwayat.php"); exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Beli - Maju Jaya</title>
    <link rel="stylesheet" href="assets/style.css?v=<?php echo time(); ?>">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    function validasiBeli() {
        var jumlah = document.getElementById("inputJumlah").value;
        var stok = <?php echo $b['stok_aktif']; ?>;
        if (jumlah === "" || jumlah <= 0) {
            Swal.fire({ icon: 'warning', title: 'Kosong!', text: 'Isi jumlah minimal 1 ya!', confirmButtonColor: '#2c3e50' });
            return false;
        }
        if (parseInt(jumlah) > stok) {
            Swal.fire({ icon: 'error', title: 'Stok Kurang!', text: 'Tersedia cuma ' + stok + ' unit.', confirmButtonColor: '#e74c3c' });
            return false;
        }
        return true; 
    }
    </script>
</head>
<body>
    <div class="wrapper-beli">
        <div class="card-beli">
            <h3 align="center">Checkout</h3>
            <div class="info-beli">
                <div class="info-item"><span>Barang:</span><b><?php echo $b['nama_barang']; ?></b></div>
                <div class="info-item"><span>Harga:</span><b>Rp <?php echo number_format($b['harga'], 0, ',', '.'); ?></b></div>
            </div>
            <div class="stok-label">Tersedia: <b><?php echo $b['stok_aktif']; ?></b> unit</div>
            <form method="post" onsubmit="return validasiBeli()" novalidate>
                <input type="hidden" name="id_barang" value="<?php echo $b['id_barang']; ?>">
                <label class="form-label">Jumlah Beli:</label>
                <input type="number" name="jumlah" id="inputJumlah" class="input-modern" placeholder="0" required>
                <button type="submit" name="proses" class="btn-proses-beli">Konfirmasi Pembelian</button>
            </form>
            <p align="center" style="margin-top: 20px;"><a href="barang.php" class="cancel-btn" style="color:var(--danger); text-decoration:none; font-weight:bold;">← Batal</a></p>
        </div>
    </div>
</body>
</html>