<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Riwayat extends MX_Controller {

    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('user_id')) {
            redirect('auth');
        }
        // Hanya superadmin yang boleh akses riwayat
        if ($this->session->userdata('role') !== 'superadmin') {
            redirect('barang');
        }
        $this->load->model('Riwayat_model');
        $this->load->library('pagination');
    }

    public function index() {
        $this->Riwayat_model->auto_migrate(); // Jalankan migrasi otomatis jika ada data lama
        $cari = $this->input->get('cari', TRUE);
        $tgl_mulai = $this->input->get('tgl_mulai', TRUE);
        $tgl_selesai = $this->input->get('tgl_selesai', TRUE);
        
        $config['base_url'] = site_url('riwayat/index');
        $config['total_rows'] = $this->Riwayat_model->count_riwayat($cari, $tgl_mulai, $tgl_selesai);
        $config['per_page'] = 20;
        $config['uri_segment'] = 3;
        $config['reuse_query_string'] = TRUE;

        // Styling Pagination (Optional, for better UI)
        $config['full_tag_open'] = '<div class="pagination">';
        $config['full_tag_close'] = '</div>';
        $config['cur_tag_open'] = '<a class="page-link active">';
        $config['cur_tag_close'] = '</a>';
        $config['attributes'] = array('class' => 'page-link');

        $this->pagination->initialize($config);

        $page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
        
        $data['transaksi'] = $this->Riwayat_model->get_riwayat($config['per_page'], $page, $cari, $tgl_mulai, $tgl_selesai);
        $data['total_rows'] = $config['total_rows'];
        $data['cari'] = $cari;
        $data['tgl_mulai'] = $tgl_mulai;
        $data['tgl_selesai'] = $tgl_selesai;

        $this->load->view('index', $data);
    }

    public function return_barang() {
        $ids = $this->input->post('id_return');
        $alasan = $this->input->post('alasan_return');

        if (!empty($ids) && !empty($alasan)) {
            foreach ($ids as $id) {
                $this->Riwayat_model->proses_return($id, $alasan);
            }
            $this->session->set_flashdata('return_sukses', 'Barang berhasil diretur!');
        }

        redirect('riwayat');
    }

    public function riwayat_return() {
        $this->Riwayat_model->auto_migrate(); // Jalankan migrasi otomatis jika ada data lama
        $cari = $this->input->get('cari', TRUE);
        $tgl_mulai = $this->input->get('tgl_mulai', TRUE);
        $tgl_selesai = $this->input->get('tgl_selesai', TRUE);
        
        $config['base_url'] = site_url('riwayat/riwayat_return');
        $config['total_rows'] = $this->Riwayat_model->count_returns($cari, $tgl_mulai, $tgl_selesai);
        $config['per_page'] = 20;
        $config['uri_segment'] = 3;
        $config['reuse_query_string'] = TRUE;

        // Styling Pagination
        $config['full_tag_open'] = '<div class="pagination">';
        $config['full_tag_close'] = '</div>';
        $config['cur_tag_open'] = '<a class="page-link active">';
        $config['cur_tag_close'] = '</a>';
        $config['attributes'] = array('class' => 'page-link');

        $this->pagination->initialize($config);

        $page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
        
        $data['returns'] = $this->Riwayat_model->get_returns($config['per_page'], $page, $cari, $tgl_mulai, $tgl_selesai);
        $data['total_rows'] = $config['total_rows'];
        $data['cari'] = $cari;
        $data['tgl_mulai'] = $tgl_mulai;
        $data['tgl_selesai'] = $tgl_selesai;

        $this->load->view('riwayat_return', $data);
    }
}
