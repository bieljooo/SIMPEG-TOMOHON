<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Settings extends CI_Controller {

    private $photo_positions = array(
        'left top',
        'center top',
        'right top',
        'left center',
        'center center',
        'right center',
        'left bottom',
        'center bottom',
        'right bottom',
    );

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Settings_model');

        if (!$this->session->userdata('logged_in')) {
            redirect('auth');
        }
    }

    public function index()
    {
        $account = $this->get_current_account();

        if (empty($account)) {
            show_404();
        }

        $data = array(
            'title' => 'Pengaturan',
            'account' => $account,
            'position_options' => $this->photo_positions,
            'current_position' => $this->normalize_photo_position(isset($account->foto_posisi) ? $account->foto_posisi : ''),
            'home_url' => $this->get_home_url(),
        );

        $this->render_settings($data);
    }

    public function update()
    {
        $account = $this->get_current_account();

        if (empty($account)) {
            show_404();
        }

        $source = $this->get_account_source();
        $photo_position = $this->normalize_photo_position($this->input->post('foto_posisi', TRUE));
        $remove_photo = ($this->input->post('hapus_foto', TRUE) === '1');
        $current_photo = !empty($account->foto_profil) ? $account->foto_profil : '';
        $update_data = array(
            'foto_posisi' => $photo_position,
        );

        if (!empty($_FILES['foto_profil']['name'])) {
            if ((int) $_FILES['foto_profil']['size'] > 1048576) {
                $this->session->set_flashdata('error', 'Ukuran foto profil maksimal 1 MB.');
                redirect('settings');
                return;
            }

            $upload_result = $this->upload_profile_photo('foto_profil');

            if (!$upload_result['status']) {
                $this->session->set_flashdata('error', $upload_result['message']);
                redirect('settings');
                return;
            }

            if ($current_photo !== '') {
                $this->delete_profile_photo($current_photo);
            }

            $current_photo = $upload_result['path'];
            $update_data['foto_profil'] = $current_photo;
        } elseif ($remove_photo && $current_photo !== '') {
            $this->delete_profile_photo($current_photo);
            $current_photo = NULL;
            $update_data['foto_profil'] = NULL;
        }

        $current_password = trim((string) $this->input->post('current_password', TRUE));
        $new_password = trim((string) $this->input->post('new_password', TRUE));
        $confirm_password = trim((string) $this->input->post('confirm_password', TRUE));
        $password_will_change = ($current_password !== '' || $new_password !== '' || $confirm_password !== '');

        if ($password_will_change) {
            if ($current_password === '' || $new_password === '' || $confirm_password === '') {
                $this->session->set_flashdata('error', 'Semua field password wajib diisi untuk mengubah password.');
                redirect('settings');
                return;
            }

            if (!password_verify($current_password, (string) $account->password)) {
                $this->session->set_flashdata('error', 'Password saat ini tidak sesuai.');
                redirect('settings');
                return;
            }

            if (strlen($new_password) < 8 || !preg_match('/[A-Za-z]/', $new_password) || !preg_match('/[0-9]/', $new_password)) {
                $this->session->set_flashdata('error', 'Password baru minimal 8 karakter dan harus mengandung huruf serta angka.');
                redirect('settings');
                return;
            }

            if ($new_password !== $confirm_password) {
                $this->session->set_flashdata('error', 'Konfirmasi password baru tidak cocok.');
                redirect('settings');
                return;
            }

            $update_data['password'] = password_hash($new_password, PASSWORD_DEFAULT);
        }

        if ($source === 'pegawai') {
            $updated = $this->Settings_model->update_pegawai_account($account->nip, $update_data);
        } else {
            $updated = $this->Settings_model->update_user_account($account->id, $update_data);
        }

        if (!$updated) {
            $this->session->set_flashdata('error', 'Pengaturan gagal diperbarui.');
            redirect('settings');
            return;
        }

        $this->session->set_userdata(array(
            'foto_profil' => isset($update_data['foto_profil']) ? $update_data['foto_profil'] : $current_photo,
            'foto_posisi' => $photo_position,
        ));

        $success_message = $password_will_change
            ? 'Pengaturan berhasil diperbarui dan password sudah diubah.'
            : 'Pengaturan berhasil diperbarui.';

        $this->session->set_flashdata('success', $success_message);
        redirect('settings');
    }

    private function get_account_source()
    {
        return $this->session->userdata('nip') ? 'pegawai' : 'users';
    }

    private function get_current_account()
    {
        if ($this->get_account_source() === 'pegawai') {
            return $this->Settings_model->get_pegawai_account($this->session->userdata('nip'));
        }

        return $this->Settings_model->get_user_account($this->session->userdata('user_id'));
    }

    private function render_settings($data)
    {
        if (in_array($this->session->userdata('role'), array('pegawai', 'kasubag', 'kadis', 'sek'), TRUE)) {
            $this->load->view('templates/header_pegawai', $data);
            $this->load->view('settings/index', $data);
            $this->load->view('templates/footer_pegawai');
            return;
        }

        $this->load->view('templates/header', $data);
        $this->load->view('settings/index', $data);
        $this->load->view('templates/footer');
    }

    private function get_home_url()
    {
        $role = $this->session->userdata('role');

        if (in_array($role, array('pegawai', 'kasubag', 'kadis', 'sek'), TRUE)) {
            return site_url('dashboard');
        }

        if ($role === 'petugas') {
            return site_url('dashboard_petugas');
        }

        return site_url('pegawai');
    }

    private function normalize_photo_position($value)
    {
        $value = strtolower(trim((string) $value));

        if ($value === '') {
            return '50% 50%';
        }

        if (preg_match('/^(\d{1,3}(?:\.\d+)?)%\s+(\d{1,3}(?:\.\d+)?)%$/', $value, $matches)) {
            $x = max(0, min(100, (float) $matches[1]));
            $y = max(0, min(100, (float) $matches[2]));

            return rtrim(rtrim(number_format($x, 2, '.', ''), '0'), '.') . '% ' .
                rtrim(rtrim(number_format($y, 2, '.', ''), '0'), '.') . '%';
        }

        $legacy_positions = array(
            'left top' => '0% 0%',
            'center top' => '50% 0%',
            'right top' => '100% 0%',
            'left center' => '0% 50%',
            'center center' => '50% 50%',
            'right center' => '100% 50%',
            'left bottom' => '0% 100%',
            'center bottom' => '50% 100%',
            'right bottom' => '100% 100%',
        );

        if (isset($legacy_positions[$value])) {
            return $legacy_positions[$value];
        }

        return '50% 50%';
    }

    private function upload_profile_photo($field_name)
    {
        $upload_path = FCPATH . 'assets/uploads/profile/';

        if (!is_dir($upload_path)) {
            mkdir($upload_path, 0755, TRUE);
        }

        $config = array(
            'upload_path' => $upload_path,
            'allowed_types' => 'jpg|jpeg|png|gif',
            'max_size' => 1024,
            'encrypt_name' => TRUE,
            'remove_spaces' => TRUE,
        );

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload($field_name)) {
            return array(
                'status' => FALSE,
                'message' => strip_tags($this->upload->display_errors('', '')),
            );
        }

        $uploaded = $this->upload->data();

        return array(
            'status' => TRUE,
            'path' => 'assets/uploads/profile/' . $uploaded['file_name'],
        );
    }

    private function delete_profile_photo($relative_path)
    {
        if ($relative_path === '') {
            return;
        }

        $absolute_path = FCPATH . ltrim(str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $relative_path), DIRECTORY_SEPARATOR);

        if (is_file($absolute_path)) {
            @unlink($absolute_path);
        }
    }
}
