<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Keranjang extends MX_Controller {

    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('user_id')) redirect('auth');
    }

    public function index() {
        $this->load->view('index');
    }

    public function tambah() {
        $id = $this->input->post('id_barang');
        $jml = $this->input->post('jumlah');

        $this->load->model('barang/Barang_model');
        $b = $this->Barang_model->get_by_id($id);

        $keranjang = $this->session->userdata('keranjang') ?? [];
        $keranjang[$id] = [
            'id'     => $id,
            'nama'   => $b['nama_barang'],
            'harga'  => $b['harga'],
            'jumlah' => $jml
        ];

        $this->session->set_userdata('keranjang', $keranjang);
        redirect('barang');
    }

    public function hapus($id) {
        $keranjang = $this->session->userdata('keranjang');
        unset($keranjang[$id]);
        $this->session->set_userdata('keranjang', $keranjang);
        redirect('keranjang');
    }

    public function checkout() {
        $keranjang = $this->session->userdata('keranjang');
        if (empty($keranjang)) redirect('barang');

        $this->load->model('barang/Barang_model');
        $inserted_ids = [];
        
        foreach ($keranjang as $item) {
            $id_trx = $this->Barang_model->generate_id_transaksi();
            $data = [
                'id_transaksi' => $id_trx,
                'nama_barang' => $item['nama'],
                'jumlah' => $item['jumlah'],
                'harga_satuan' => $item['harga'],
                'total_harga' => $item['harga'] * $item['jumlah'],
                'tanggal' => date('Y-m-d H:i:s')
            ];
            $this->db->insert('transaksi', $data);
            $inserted_ids[] = $id_trx;
        }

        $this->session->unset_userdata('keranjang');
        $this->session->set_flashdata('checkout_sukses', TRUE);
        $this->session->set_flashdata('checkout_ids', $inserted_ids);
        redirect('keranjang');
    }

    public function cetak($tipe) {
        $ids_str = $this->input->get('ids');
        if (!$ids_str) die("ID transaksi tidak ditemukan.");
        
        $ids = explode(',', $ids_str);
        $this->db->where_in('id_transaksi', $ids);
        $data['items'] = $this->db->get('transaksi')->result_array();
        
        if ($tipe === 'invoice') {
            $this->load->view('cetak_invoice', $data);
        } else {
            $this->load->view('cetak_struk', $data);
        }
    }
}
