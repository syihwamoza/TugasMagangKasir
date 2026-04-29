<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Auth_model
 * 
 * Model untuk login. Query diambil dari actions/login_aksi.php lama.
 */
class Auth_model extends CI_Model {

    /**
     * Cek username & password ke database.
     * 
     * @param string $username
     * @param string $password (plain text, sesuai data lama)
     * @return array|false
     */
    public function cek_login($username, $password)
    {
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
