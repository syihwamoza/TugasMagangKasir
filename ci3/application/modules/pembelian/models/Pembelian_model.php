<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pembelian_model extends CI_Model {

    public function get_barang() {
        return $this->db->order_by('nama_barang', 'ASC')->get('barang')->result_array();
    }

    public function get_barang_by_id($id) {
        return $this->db->get_where('barang', ['id_barang' => $id])->row_array();
    }

    public function simpan_pembelian($data) {
        return $this->db->insert('pembelian', $data);
    }
}
