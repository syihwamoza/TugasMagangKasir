<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Barang_model extends CI_Model {

    public function get_all($limit, $start, $cari = '') {
        if ($cari) {
            $this->db->like('nama_barang', $cari);
        }
        
        // Logika stok aktif legacy: (stok_awal + beli - jual + retur)
        $sql_stok = "(stok_awal + 
                    COALESCE((SELECT SUM(jumlah_beli) FROM pembelian WHERE nama_barang = barang.nama_barang), 0) - 
                    COALESCE((SELECT SUM(jumlah) FROM transaksi WHERE nama_barang = barang.nama_barang), 0) + 
                    COALESCE((SELECT SUM(jumlah_return) FROM pengembalian WHERE nama_barang = barang.nama_barang), 0))";
        
        $this->db->select("*, $sql_stok AS stok_aktif");
        $this->db->order_by('id_barang', 'DESC');
        return $this->db->get('barang', $limit, $start)->result_array();
    }

    public function count_all($cari = '') {
        if ($cari) {
            $this->db->like('nama_barang', $cari);
        }
        return $this->db->count_all_results('barang');
    }

    public function get_by_id($id) {
        $sql_stok = "(stok_awal + 
                    COALESCE((SELECT SUM(jumlah_beli) FROM pembelian WHERE nama_barang = barang.nama_barang), 0) - 
                    COALESCE((SELECT SUM(jumlah) FROM transaksi WHERE nama_barang = barang.nama_barang), 0) + 
                    COALESCE((SELECT SUM(jumlah_return) FROM pengembalian WHERE nama_barang = barang.nama_barang), 0))";
        
        $this->db->select("*, $sql_stok AS stok_aktif");
        return $this->db->get_where('barang', ['id_barang' => $id])->row_array();
    }

    public function insert($data) {
        return $this->db->insert('barang', $data);
    }

    public function update($id, $data) {
        $this->db->where('id_barang', $id);
        return $this->db->update('barang', $data);
    }

    public function delete($id) {
        return $this->db->delete('barang', ['id_barang' => $id]);
    }

    // Generate ID pembelian format DDMMYYXXX (misal: 240426001)
    private function generate_id_pembelian() {
        $prefix = date('dmy'); // e.g., "240426"
        // Hitung berapa record yang sudah ada hari ini di tabel pembelian
        $this->db->like('id_pembelian', $prefix, 'after');
        $count = $this->db->count_all_results('pembelian');
        $seq = str_pad($count + 1, 3, '0', STR_PAD_LEFT); // 001, 002, ...
        return $prefix . $seq; // e.g., "240426001"
    }

    // Generate ID transaksi format DDMMYYXXX (misal: 240426001)
    public function generate_id_transaksi() {
        $prefix = date('dmy');
        // Hitung berapa record yang sudah ada hari ini di tabel transaksi
        $this->db->like('id_transaksi', $prefix, 'after');
        $count = $this->db->count_all_results('transaksi');
        $seq = str_pad($count + 1, 3, '0', STR_PAD_LEFT);
        return $prefix . $seq;
    }

    // Generate ID return format DDMMYYXXX (misal: 240426001)
    public function generate_id_return() {
        $prefix = date('dmy');
        // Hitung berapa record yang sudah ada hari ini di tabel pengembalian
        $this->db->like('id_return', $prefix, 'after');
        $count = $this->db->count_all_results('pengembalian');
        $seq = str_pad($count + 1, 3, '0', STR_PAD_LEFT);
        return $prefix . $seq;
    }

    // Logika Tracking Stok (Beli/Restock)
    public function log_pembelian($nama, $jumlah) {
        return $this->db->insert('pembelian', [
            'id_pembelian'  => $this->generate_id_pembelian(),
            'nama_barang'   => $nama,
            'jumlah_beli'   => $jumlah,
            'tanggal_beli'  => date('Y-m-d H:i:s')
        ]);
    }

    public function get_detail_tracking($nama, $jenis, $dari, $sampai)
{
    $this->db->where('nama_barang', $nama);

    if ($jenis != 'all') {
        $this->db->where('jenis', $jenis);
    }

    if ($dari && $sampai) {
        $this->db->where('tanggal >=', $dari . ' 00:00:00');
        $this->db->where('tanggal <=', $sampai . ' 23:59:59');
    }

    return $this->db->get('tracking')->result_array();
}
public function count_tracking($cari = null, $dari = null, $sampai = null)
{
    if ($cari) {
        $this->db->like('nama_barang', $cari);
    }

    if ($dari && $sampai) {
        $this->db->where('DATE(tanggal) >=', $dari);
        $this->db->where('DATE(tanggal) <=', $sampai);
    }

    return $this->db->count_all_results('tracking');
}
}
