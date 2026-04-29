<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Riwayat_model extends CI_Model {

    public function get_riwayat($limit, $start, $cari = '', $tgl_mulai = '', $tgl_selesai = '') {
        $this->db->select('*');
        $this->db->from('transaksi');
        
        if ($cari) {
            $this->db->like('nama_barang', $cari);
        }
        
        if ($tgl_mulai && $tgl_selesai) {
            $this->db->where("tanggal BETWEEN '$tgl_mulai 00:00:00' AND '$tgl_selesai 23:59:59'");
        }
        
        $this->db->order_by('tanggal', 'DESC');
        $this->db->limit($limit, $start);
        
        return $this->db->get()->result_array();
    }

    public function count_riwayat($cari = '', $tgl_mulai = '', $tgl_selesai = '') {
        $this->db->from('transaksi');
        
        if ($cari) {
            $this->db->like('nama_barang', $cari);
        }
        
        if ($tgl_mulai && $tgl_selesai) {
            $this->db->where("tanggal BETWEEN '$tgl_mulai 00:00:00' AND '$tgl_selesai 23:59:59'");
        }
        
        return $this->db->count_all_results();
    }

    public function get_transaksi_by_ids($ids) {
        $this->db->where_in('id_transaksi', $ids);
        return $this->db->get('transaksi')->result_array();
    }

    public function proses_return($id_transaksi, $alasan) {
        // Ambil data transaksi
        $this->db->where('id_transaksi', $id_transaksi);
        $transaksi = $this->db->get('transaksi')->row_array();

        if ($transaksi) {
            $this->load->model('barang/Barang_model');
            // Insert ke tabel pengembalian
            $data_return = [
                'id_return' => $this->Barang_model->generate_id_return(),
                'id_transaksi' => $id_transaksi,
                'nama_barang' => $transaksi['nama_barang'],
                'jumlah_return' => $transaksi['jumlah'],
                'alasan' => $alasan,
                'tanggal_return' => date('Y-m-d H:i:s')
            ];
            $this->db->insert('pengembalian', $data_return);

            // Hapus dari transaksi
            $this->db->where('id_transaksi', $id_transaksi);
            return $this->db->delete('transaksi');
        }
        return false;
    }

    public function get_returns($limit, $start, $cari = '', $tgl_mulai = '', $tgl_selesai = '') {
        $this->db->select('*');
        $this->db->from('pengembalian');
        
        if ($cari) {
            $this->db->like('nama_barang', $cari);
        }
        
        if ($tgl_mulai && $tgl_selesai) {
            $this->db->where("tanggal_return BETWEEN '$tgl_mulai 00:00:00' AND '$tgl_selesai 23:59:59'");
        }
        
        $this->db->order_by('tanggal_return', 'DESC');
        $this->db->limit($limit, $start);
        
        return $this->db->get()->result_array();
    }

    public function count_returns($cari = '', $tgl_mulai = '', $tgl_selesai = '') {
        $this->db->from('pengembalian');
        
        if ($cari) {
            $this->db->like('nama_barang', $cari);
        }
        
        if ($tgl_mulai && $tgl_selesai) {
            $this->db->where("tanggal_return BETWEEN '$tgl_mulai 00:00:00' AND '$tgl_selesai 23:59:59'");
        }
        
        return $this->db->count_all_results();
    }

    public function auto_migrate() {
        // Cek jika sudah pernah migrasi di session ini agar tidak berat
        if ($this->session->userdata('migrated_ids')) return;

        // Migrasi Transaksi & Pengembalian
        $res = $this->db->select('id_transaksi, tanggal')->order_by('tanggal', 'ASC')->get('transaksi')->result_array();
        $counters = [];
        foreach ($res as $row) {
            if (strlen($row['id_transaksi']) > 6) continue; // Sudah format baru
            
            $prefix = date('dmy', strtotime($row['tanggal']));
            if (!isset($counters[$prefix])) $counters[$prefix] = 1;
            $new_id = $prefix . str_pad($counters[$prefix], 3, '0', STR_PAD_LEFT);
            $old_id = $row['id_transaksi'];

            $this->db->set('id_transaksi', $new_id)->where('id_transaksi', $old_id)->update('transaksi');
            $this->db->set('id_transaksi', $new_id)->where('id_transaksi', $old_id)->update('pengembalian');
            $counters[$prefix]++;
        }

        // Migrasi Pembelian
        $res = $this->db->select('id_pembelian, tanggal_beli')->order_by('tanggal_beli', 'ASC')->get('pembelian')->result_array();
        $counters_beli = [];
        foreach ($res as $row) {
            if (strlen($row['id_pembelian']) > 6) continue;
            
            $prefix = date('dmy', strtotime($row['tanggal_beli']));
            if (!isset($counters_beli[$prefix])) $counters_beli[$prefix] = 1;
            $new_id = $prefix . str_pad($counters_beli[$prefix], 3, '0', STR_PAD_LEFT);
            $old_id = $row['id_pembelian'];

            $this->db->set('id_pembelian', $new_id)->where('id_pembelian', $old_id)->update('pembelian');
            $counters_beli[$prefix]++;
        }

        // Migrasi Pengembalian (id_return)
        $res = $this->db->select('id_return, tanggal_return')->order_by('tanggal_return', 'ASC')->get('pengembalian')->result_array();
        $counters_ret = [];
        foreach ($res as $row) {
            if (strlen($row['id_return']) > 6) continue;
            
            $prefix = date('dmy', strtotime($row['tanggal_return']));
            if (!isset($counters_ret[$prefix])) $counters_ret[$prefix] = 1;
            $new_id = $prefix . str_pad($counters_ret[$prefix], 3, '0', STR_PAD_LEFT);
            $old_id = $row['id_return'];

            $this->db->set('id_return', $new_id)->where('id_return', $old_id)->update('pengembalian');
            $counters_ret[$prefix]++;
        }

        $this->session->set_userdata('migrated_ids', TRUE);
    }
}
