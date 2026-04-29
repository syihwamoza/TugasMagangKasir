<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Auth Controller (HMVC Module)
 * 
 * Menangani login dan logout.
 * Menggantikan: login.php + actions/login_aksi.php + actions/logout.php
 */
class Auth extends MX_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Auth_model');
    }

    /**
     * Default: tampilkan halaman login
     * URL: /auth atau /auth/index
     */
    public function index() {
        $this->login();
    }

    /**
     * Tampilkan halaman login.
     * URL: /auth/login
     */
    public function login() {
        // Jika sudah login, arahkan ke barang
        if ($this->session->userdata('user_id')) {
            redirect('barang');
        }
        $this->load->view('login_view');
    }

    /**
     * Proses form login (POST).
     * Menggantikan: actions/login_aksi.php
     * URL: /auth/proses
     */
    public function proses() {
        if ($this->input->server('REQUEST_METHOD') !== 'POST') {
            redirect('auth/login');
        }

        $username = $this->input->post('username', TRUE);
        $password = $this->input->post('password');

        $user = $this->Auth_model->cek_login($username, $password);

        if ($user) {
            $this->session->set_userdata([
                'user_id'  => $user['id'],
                'username' => $user['username'],
                'role'     => $user['role'],
            ]);
            redirect('barang');
        } else {
            $this->session->set_flashdata('error_login', 'Username atau password salah!');
            redirect('auth/login');
        }
    }

    /**
     * Logout pengguna.
     * Menggantikan: actions/logout.php
     * URL: /auth/logout
     */
    public function logout() {
        $this->session->sess_destroy();
        redirect('auth/login');
    }
}
