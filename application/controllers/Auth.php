<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Pegawai_model');
    }

    public function index()
    {
        // Jika sudah login, redirect sesuai role
        if ($this->session->userdata('logged_in')) {
            $this->redirect_by_role($this->session->userdata('role'));
            return;
        }

        $data['title'] = 'Login';
        $this->load->view('auth/login', $data);
    }

    public function login()
    {
        $identifier = trim((string) $this->input->post('nip', TRUE));
        $password = (string) $this->input->post('password', TRUE);

        // Prioritaskan login dari tabel users agar akun admin/petugas
        // tidak tertimpa akun pegawai jika identifier-nya bentrok.
        $this->db->where('username', $identifier);
        $this->db->where('is_active', 1);
        $admin = $this->db->get('users')->row();

        if ($admin && password_verify($password, $admin->password)) {
            $session_data = array(
                'user_id'   => $admin->id,
                'nama'      => $admin->nama_lengkap,
                'foto_profil' => property_exists($admin, 'foto_profil') ? $admin->foto_profil : NULL,
                'foto_posisi' => property_exists($admin, 'foto_posisi') && $admin->foto_posisi ? $admin->foto_posisi : 'center center',
                'role'      => $admin->role,
                'logged_in' => TRUE
            );
            $this->set_login_session($session_data);
            $this->redirect_by_role($admin->role);
            return;
        }

        // Cek di tabel pegawai
        $this->db->where('nip', $identifier);
        $user = $this->db->get('pegawai')->row();

        if ($user && password_verify($password, $user->password)) {
            $role = !empty($user->role) ? $user->role : 'pegawai';
            $session_data = array(
                'nip'       => $user->nip,
                'nama'      => $user->nama,
                'foto_profil' => property_exists($user, 'foto_profil') ? $user->foto_profil : NULL,
                'foto_posisi' => property_exists($user, 'foto_posisi') && $user->foto_posisi ? $user->foto_posisi : 'center center',
                'role'      => $role,
                'logged_in' => TRUE
            );
            $this->set_login_session($session_data);
            $this->redirect_by_role($role);
            return;
        }

        $this->session->set_flashdata('error', 'NIP/Username atau Password salah!');
        redirect('auth');
    }

    public function logout()
    {
        $this->session->sess_destroy();
        redirect('auth');
    }

    private function set_login_session($session_data)
    {
        $this->session->unset_userdata(array('user_id', 'nip', 'nama', 'role', 'logged_in', 'foto_profil', 'foto_posisi'));
        $this->session->sess_regenerate(TRUE);
        $this->session->set_userdata($session_data);
    }

    private function redirect_by_role($role)
    {
        if (in_array($role, array('pegawai', 'kasubag', 'kadis', 'sek'), TRUE)) {
            redirect('dashboard');
            return;
        }

        redirect('pegawai');
    }
}
