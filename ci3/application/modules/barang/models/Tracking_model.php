<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tracking_model extends CI_Model {

    public function get_tracking_data($limit, $start, $cari = '', $dari = '', $sampai = '') {
        if ($cari) {
            $this->db->like('nama_barang', $cari);
        }
        $this->db->order_by('nama_barang', 'ASC');
        
        $all_barang = $this->db->get('barang')->result_array();
        $filtered_data = [];

        foreach ($all_barang as $b) {
            $nama = $b['nama_barang'];
            
            // 1. Hitung mutasi (Tambah, Terjual, Retur)
            $tambah   = $this->sum_mutasi('pembelian', 'jumlah_beli', 'tanggal_beli', $nama, $dari, $sampai);
            $terjual  = $this->sum_mutasi('transaksi', 'jumlah', 'tanggal', $nama, $dari, $sampai);
            $direturn = $this->sum_mutasi('pengembalian', 'jumlah_return', 'tanggal_return', $nama, $dari, $sampai);
            
            // 2. Hitung Stok Awal (Saldo sebelum tanggal 'dari')
            $stok_awal = $this->get_stok_awal_filter($b, $dari);
            
            // 3. Hanya masukkan ke list jika ada aktivitas ATAU ada stok awal
            // Jika mau benar-benar hanya yang ada aktivitas, gunakan: if ($tambah > 0 || $terjual > 0 || $direturn > 0)
            if ($tambah > 0 || $terjual > 0 || $direturn > 0) {
                $b['tambah']   = $tambah;
                $b['terjual']  = $terjual;
                $b['direturn'] = $direturn;
                $b['stok_awal_calc'] = $stok_awal;
                $b['stok_akhir']     = $stok_awal + $tambah - $terjual + $direturn;
                
                $filtered_data[] = $b;
            }
        }

        return array_slice($filtered_data, $start, $limit);
    }

    private function sum_mutasi($tabel, $kolom, $tgl_kolom, $nama, $dari, $sampai) {
        $this->db->select_sum($kolom, 'total');
        $this->db->where('nama_barang', $nama);
        
        if ($dari) {
            $this->db->where("DATE($tgl_kolom) >=", $dari);
        }
        if ($sampai) {
            $this->db->where("DATE($tgl_kolom) <=", $sampai);
        }
        
        $res = $this->db->get($tabel)->row();
        return ($res && $res->total) ? (int)$res->total : 0;
    }

    public function count_all($cari = '') {
        if ($cari) {
            $this->db->like('nama_barang', $cari);
        }
        return $this->db->count_all_results('barang');
    }

    private function get_stok_awal_filter($b, $dari) {
        $nama = $b['nama_barang'];
        $stok_sekarang = (int)$b['stok'];

        if (!$dari) {
            // Jika tidak ada filter, stok awal dianggap stok sekarang dikurangi semua mutasi yang pernah ada
            $t = $this->sum_mutasi('pembelian', 'jumlah_beli', 'tanggal_beli', $nama, '', '');
            $j = $this->sum_mutasi('transaksi', 'jumlah', 'tanggal', $nama, '', '');
            $r = $this->sum_mutasi('pengembalian', 'jumlah_return', 'tanggal_return', $nama, '', '');
            return $stok_sekarang - $t + $j - $r;
        }

        // Hitung semua mutasi yang terjadi DARI tanggal filter sampai DETIK INI
        $t_mendatang = $this->sum_mutasi('pembelian', 'jumlah_beli', 'tanggal_beli', $nama, $dari, date('Y-m-d'));
        $j_mendatang = $this->sum_mutasi('transaksi', 'jumlah', 'tanggal', $nama, $dari, date('Y-m-d'));
        $r_mendatang = $this->sum_mutasi('pengembalian', 'jumlah_return', 'tanggal_return', $nama, $dari, date('Y-m-d'));

        // Stok Awal = Stok Sekarang - (Masuk setelah tgl filter) + (Keluar setelah tgl filter) - (Retur setelah tgl filter)
        return $stok_sekarang - $t_mendatang + $j_mendatang - $r_mendatang;
    }

    public function get_detail($nama, $jenis) {
        // Fungsi detail tetap sama agar modal kamu tidak error
        $q1 = $this->db->select("id_pembelian as id, tanggal_beli as tgl, 'Restock Barang' as ket, jumlah_beli as masuk, 0 as keluar")->where('nama_barang', $nama)->get_compiled_select('pembelian');
        $q2 = $this->db->select("id_transaksi as id, tanggal as tgl, 'Penjualan' as ket, 0 as masuk, jumlah as keluar")->where('nama_barang', $nama)->get_compiled_select('transaksi');
        $q3 = $this->db->select("id_transaksi as id, tanggal_return as tgl, 'Barang Return' as ket, jumlah_return as masuk, 0 as keluar")->where('nama_barang', $nama)->get_compiled_select('pengembalian');
        
        $query = "($q1) UNION ALL ($q2) UNION ALL ($q3) ORDER BY tgl DESC";
        $res = $this->db->query($query)->result_array();

        if ($jenis === 'restock') return array_filter($res, function($v) { return $v['masuk'] > 0 && strpos($v['ket'], 'Restock') !== false; });
        if ($jenis === 'sell') return array_filter($res, function($v) { return $v['keluar'] > 0; });
        if ($jenis === 'return') return array_filter($res, function($v) { return $v['masuk'] > 0 && strpos($v['ket'], 'Return') !== false; });
        
        return $res;
    }
}