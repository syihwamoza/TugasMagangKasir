<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Barang extends MX_Controller {

    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('user_id')) redirect('auth');
        $this->load->model('Barang_model');
        $this->load->library('pagination');
    }

    public function index() {
        $cari = $this->input->get('cari', TRUE);
        $config['base_url'] = site_url('barang/index');
        $config['total_rows'] = $this->Barang_model->count_all($cari);
        $config['per_page'] = 8;
        $config['reuse_query_string'] = TRUE;
        
        $this->pagination->initialize($config);
        
        $data['barang'] = $this->Barang_model->get_all($config['per_page'], $this->uri->segment(3), $cari);
        // echo "<pre>";
        // print_r($data['barang']);
        // echo "</pre>";
        // die;
        $data['total_rows'] = $config['total_rows'];
        $data['cari'] = $cari;

        $this->load->view('index', $data);
    }

    public function tambah() {
        if ($this->input->post('simpan_baru')) {
        $harga = (int) preg_replace('/[^0-9]/', '', $this->input->post('harga'));
        $stok  = (int) preg_replace('/[^0-9]/', '', $this->input->post('stok'));

        $data = [
            'nama_barang' => $this->input->post('nama_barang', TRUE),
            'harga' => $harga,
            'stok' => $stok,
            'stok_awal' => $stok
            ];
            // Upload Gambar
            if (!empty($_FILES['gambar']['name'])) {
                $config['upload_path'] = '../assets/uploads/';
                $config['allowed_types'] = 'jpg|jpeg|png|webp';
                $config['file_name'] = uniqid();
                $this->load->library('upload', $config);

                if ($this->upload->do_upload('gambar')) {
                    $data['gambar'] = $this->upload->data('file_name');
                }
            }

            $this->Barang_model->insert($data);
            $this->session->set_flashdata('sukses', 'Barang Baru Berhasil Didaftarkan!');
            redirect('barang');
        }
        $this->load->view('tambah');
    }

    public function edit($id) {
        $data['d'] = $this->Barang_model->get_by_id($id);
        
        if ($this->input->post('update')) {
                $stok_baru = (int) preg_replace('/[^0-9]/', '', $this->input->post('stok'));
            $stok_lama = $data['d']['stok_aktif'];
            
            // Logika Restock Otomatis
            if ($stok_baru > $stok_lama) {
                $selisih = $stok_baru - $stok_lama;
                $this->Barang_model->log_pembelian($data['d']['nama_barang'], $selisih);
            }

            $harga = (int) preg_replace('/[^0-9]/', '', $this->input->post('harga'));
            $update_data = [
                'harga' => $harga,
                'stok'  => $stok_baru
                ];

            // Upload Gambar baru jika ada
            if (!empty($_FILES['gambar']['name'])) {
                $config['upload_path'] = '../assets/uploads/';
                $config['allowed_types'] = 'jpg|jpeg|png|webp';
                $config['file_name'] = uniqid();
                $this->load->library('upload', $config);
                if ($this->upload->do_upload('gambar')) {
                    $update_data['gambar'] = $this->upload->data('file_name');
                }
            }

            $this->Barang_model->update($id, $update_data);
            $this->session->set_flashdata('sukses', 'Data Barang Berhasil Diupdate!');
            redirect('barang');
        }
        $this->load->view('edit', $data);
    }

    public function hapus($id) {
        $this->Barang_model->delete($id);
        redirect('barang');
    }

    public function tracking() {
    $cari   = $this->input->get('cari', TRUE);
    $dari   = $this->input->get('dari');
    $sampai = $this->input->get('sampai');

    $config['base_url'] = site_url('barang/tracking');
    $config['total_rows'] = $this->Barang_model->count_tracking($cari, $dari, $sampai);
    $config['per_page'] = 10;
    $config['reuse_query_string'] = TRUE;

    $this->pagination->initialize($config);

    $data['barang'] = $this->Barang_model->get_tracking(
        $cari,
        $dari,
        $sampai,
        $config['per_page'],
        $this->uri->segment(3)
    );

    $data['cari'] = $cari;

    $this->load->view('tracking', $data);
}
public function get_detail_ajax() {
    $nama   = $this->input->post('nama_barang');
    $jenis  = $this->input->post('jenis');
    $dari   = $this->input->post('dari');
    $sampai = $this->input->post('sampai');

    $data['detail'] = $this->Barang_model->get_detail_tracking($nama, $jenis, $dari, $sampai);

    $this->load->view('detail_tracking', $data);
    var_dump($this->input->post()); die;
}
}
