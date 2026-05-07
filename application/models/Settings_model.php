<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Settings_model extends CI_Model {

    public function get_pegawai_account($nip)
    {
        return $this->db
            ->select('nip, nama, role, password, foto_profil, foto_posisi')
            ->where('nip', $nip)
            ->get('pegawai')
            ->row();
    }

    public function get_user_account($id)
    {
        return $this->db
            ->select('id, username, nama_lengkap, role, password, foto_profil, foto_posisi')
            ->where('id', (int) $id)
            ->get('users')
            ->row();
    }

    public function update_pegawai_account($nip, $data)
    {
        return $this->db
            ->where('nip', $nip)
            ->update('pegawai', $data);
    }

    public function update_user_account($id, $data)
    {
        return $this->db
            ->where('id', (int) $id)
            ->update('users', $data);
    }
}
