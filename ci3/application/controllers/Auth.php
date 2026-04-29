<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller Auth
 * 
 * Menangani login dan logout.
 * Menggantikan: login.php + actions/login_aksi.php + actions/logout.php
 */
class Auth extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        // Load model saja — session & url sudah di-autoload di config/autoload.php
        $this->load->model('Auth_model');
    }

    /**
     * Tampilkan halaman login.
     * Menggantikan: login.php
     * 
     * Jika sudah login, langsung redirect ke halaman barang.
     */
    public function login()
    {
        // Jika sudah login, arahkan ke barang
        if ($this->session->userdata('user_id')) {
            redirect('barang');
        }

        // Tampilkan view login
        $this->load->view('auth/login');
    }

    /**
     * Proses form login (POST).
     * Menggantikan: actions/login_aksi.php
     */
    public function proses()
    {
        // Hanya menerima POST
        if ($this->input->server('REQUEST_METHOD') !== 'POST') {
            redirect('auth/login');
        }

        $username = $this->input->post('username', TRUE); // TRUE = XSS filter
        $password = $this->input->post('password');       // Plain text

        // Panggil model untuk cek ke database
        $user = $this->Auth_model->cek_login($username, $password);

        if ($user) {
            // Login berhasil: simpan data ke session CI3
            $this->session->set_userdata([
                'user_id'  => $user['id'],
                'username' => $user['username'],
                'role'     => $user['role'],
            ]);
            redirect('barang');
        } else {
            // Login gagal: simpan pesan error di flashdata (hanya muncul sekali)
            $this->session->set_flashdata('error_login', 'Username atau password salah!');
            redirect('auth/login');
        }
    }

    /**
     * Logout pengguna.
     * Menggantikan: actions/logout.php
     */
    public function logout()
    {
        $this->session->sess_destroy();
        redirect('auth/login');
    }
}
