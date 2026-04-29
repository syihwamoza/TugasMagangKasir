<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pembelian extends MX_Controller {

    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('user_id')) redirect('auth');
        // Fitur Belanja Stok telah dihapus - redirect ke barang
        redirect('barang');
    }

    public function index() {
        $data['barang'] = $this->Pembelian_model->get_barang();
        $this->load->view('index', $data);
    }

    public function proses() {
        $id = $this->input->post('id_barang');
        $jumlah = $this->input->post('jumlah_beli');
        $supplier = $this->input->post('supplier');
        $harga = $this->input->post('harga_beli');
        
        $barang = $this->Pembelian_model->get_barang_by_id($id);
        
        $data = [
            'nama_barang' => $barang['nama_barang'],
            'jumlah_beli' => $jumlah,
            'harga_beli' => $harga,
            'supplier' => $supplier,
            'tanggal_beli' => date('Y-m-d H:i:s')
        ];

        $this->Pembelian_model->simpan_pembelian($data);
        $this->session->set_flashdata('pembelian_sukses', 'Stok berhasil ditambahkan!');
        redirect('pembelian');
    }
}
