<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tracking extends MX_Controller {

    public function __construct() {
        parent::__construct();
        // Cek login: Hanya superadmin yang boleh masuk
        if ($this->session->userdata('role') !== 'superadmin') {
            redirect('barang');
        }
        
        // Load model dan library pagination
        $this->load->model('Tracking_model');
        $this->load->library('pagination');
    }

    /**
     * Halaman Utama Tracking Stok dengan Filter Tanggal & Pencarian
     */
    public function index() {
        // 1. Ambil data dari URL (Method GET)
        $cari   = $this->input->get('cari', TRUE);
        $dari   = $this->input->get('dari', TRUE);
        $sampai = $this->input->get('sampai', TRUE);
        
        // 2. Konfigurasi Pagination (PENTING: Pakai Query String agar ANTI-404)
        $config['base_url']             = site_url('barang/tracking/index'); 
        $config['total_rows']           = $this->Tracking_model->count_all($cari);
        $config['per_page']             = 20;
        
        // Menghindari 404 dengan memaksa pagination pakai tanda tanya (?) di URL
        $config['page_query_string']    = TRUE; 
        $config['query_string_segment'] = 'per_page';
        $config['reuse_query_string']   = TRUE; // Supaya parameter ?cari, ?dari, ?sampai tidak hilang
        
        // Styling Pagination (Biar selaras dengan CSS kamu)
        $config['full_tag_open']    = '<div class="pagination">';
        $config['full_tag_close']   = '</div>';
        $config['first_link']       = 'First';
        $config['last_link']        = 'Last';
        $config['next_link']        = '&raquo;';
        $config['prev_link']        = '&laquo;';
        $config['cur_tag_open']     = '<a class="page-link active-page">';
        $config['cur_tag_close']    = '</a>';
        $config['num_tag_open']     = '';
        $config['num_tag_close']    = '';
        $config['attributes']       = array('class' => 'page-link');

        $this->pagination->initialize($config);
        
        // 3. Ambil posisi data (offset)
        $page = ($this->input->get('per_page')) ? $this->input->get('per_page') : 0;
        
        // 4. Ambil data dari model (Mengirim parameter filter tanggal)
        $data['barang'] = $this->Tracking_model->get_tracking_data($config['per_page'], $page, $cari, $dari, $sampai);
        
        // 5. Data tambahan untuk ditampilkan kembali di form input
        $data['cari']   = $cari;
        $data['dari']   = $dari;
        $data['sampai'] = $sampai;

        // Load View
        $this->load->view('tracking_view', $data);
    }

    /**
     * Fungsi AJAX: Menampilkan tabel riwayat saat angka stok diklik
     */
    public function get_detail_ajax() {
        $nama  = $this->input->post('nama_barang');
        $jenis = $this->input->post('jenis'); // 'all', 'restock', 'sell', atau 'return'
        
        $detail = $this->Tracking_model->get_detail($nama, $jenis);
        
        if(!empty($detail)) {
            echo '<table style="width:100%; border-collapse: collapse; font-size: 13px; font-family: sans-serif;">';
            echo '<thead style="background: #34495e; color: white;">
                    <tr>
                        <th style="padding: 12px; border: 1px solid #ddd;">Waktu</th>
                        <th style="padding: 12px; border: 1px solid #ddd; text-align: left;">Keterangan</th>
                        <th style="padding: 12px; border: 1px solid #ddd;">Masuk</th>
                        <th style="padding: 12px; border: 1px solid #ddd;">Keluar</th>
                    </tr>
                  </thead>';
            echo '<tbody>';
            foreach($detail as $d) {
                $tgl = date('d/m/Y H:i', strtotime($d['tgl']));
                $masuk = ($d['masuk'] > 0) ? '<b style="color: #27ae60;">+'.$d['masuk'].'</b>' : '-';
                $keluar = ($d['keluar'] > 0) ? '<b style="color: #e74c3c;">-'.$d['keluar'].'</b>' : '-';
                
                echo "<tr>
                        <td style='padding: 10px; border: 1px solid #eee; text-align: center; color: #7f8c8d;'>$tgl</td>
                        <td style='padding: 10px; border: 1px solid #eee;'>{$d['ket']}</td>
                        <td style='padding: 10px; border: 1px solid #eee; text-align: center;'>$masuk</td>
                        <td style='padding: 10px; border: 1px solid #eee; text-align: center;'>$keluar</td>
                      </tr>";
            }
            echo '</tbody></table>';
        } else {
            echo '<div style="padding: 30px; text-align: center; color: #999; font-style: italic;">Belum ada aktivitas terekam.</div>';
        }
    }
}