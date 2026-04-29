<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Auth_model
 * 
 * Model untuk mengurus data user / login.
 * Logika query diambil dari actions/login_aksi.php yang lama.
 */
class Auth_model extends CI_Model {

    /**
     * Cek username & password ke database.
     * Sama persis dengan query di login_aksi.php lama.
     * 
     * @param string $username
     * @param string $password  (plain text, sesuai data lama)
     * @return array|false  Data user jika cocok, false jika tidak
     */
    public function cek_login($username, $password)
    {
        // Pakai Query Builder CI3 (lebih aman dari SQL injection)
        $this->db->where('username', $username);
        $query = $this->db->get('users');

        if ($query->num_rows() > 0) {
            $user = $query->row_array();

            // Cek password (plain text, sesuai data lama)
            if ($user['password'] === $password) {
                return $user;
            }
        }

        return false;
    }
}
