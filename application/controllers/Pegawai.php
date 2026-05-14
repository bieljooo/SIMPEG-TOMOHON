<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pegawai extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Pegawai_model');
        $this->load->helper('simpeg_pegawai');

        // Hanya petugas dan kasubag yang boleh akses
        if (
            !$this->session->userdata('logged_in') ||
            !in_array($this->session->userdata('role'), array('petugas', 'kasubag'), TRUE)
        ) {
            redirect('auth');
        }
    }

    public function index()
    {
        $data['title'] = 'Data Pegawai';
        $data['pegawai'] = $this->Pegawai_model->get_all();

        $this->load->view('templates/header', $data);
        $this->load->view('pegawai/index', $data);
        $this->load->view('templates/footer');
    }

    public function tambah()
    {
        $this->guard_read_only_role();

        $data['title'] = 'Tambah Pegawai';

        $this->load->view('templates/header', $data);
        $this->load->view('pegawai/tambah', $data);
        $this->load->view('templates/footer');
    }

    public function simpan()
    {
        $this->guard_read_only_role();

        $nip = $this->input->post('nip', TRUE);
        $pangkat_terakhir = trim((string) $this->input->post('pangkat_terakhir', TRUE));

        if ($this->Pegawai_model->nip_exists($nip) || $this->Pegawai_model->pending_nip_exists($nip)) {
            $this->session->set_flashdata('error', 'NIP sudah terdaftar atau masih menunggu persetujuan Kasubag.');
            redirect('pegawai/tambah');
            return;
        }

        if (!simpeg_is_valid_pangkat_terakhir($pangkat_terakhir)) {
            $this->session->set_flashdata('error', 'Pangkat / Gol. Ruang Terakhir harus dipilih dari daftar yang tersedia.');
            redirect('pegawai/tambah');
            return;
        }

        $pegawai = array(
            'nama'                => $this->input->post('nama', TRUE),
            'gol_ruang_cpns'      => $this->input->post('gol_ruang_cpns', TRUE),
            'tmt_cpns'            => $this->input->post('tmt_cpns', TRUE) ?: NULL,
            'pangkat_terakhir'    => $pangkat_terakhir,
            'jenis_kelamin'       => $this->input->post('jenis_kelamin', TRUE),
            'jabatan'             => $this->input->post('jabatan', TRUE),
            'eselon'              => $this->input->post('eselon', TRUE),
            'diklat_penjenjangan' => $this->input->post('diklat_penjenjangan', TRUE),
            'instansi_pembayar'   => $this->input->post('instansi_pembayar', TRUE),
            'keterangan'          => $this->input->post('keterangan', TRUE),
        );

        $pribadi = array(
            'tempat_lahir'   => $this->input->post('tempat_lahir', TRUE),
            'tanggal_lahir'  => $this->input->post('tanggal_lahir', TRUE) ?: NULL,
            'status_kawin'   => $this->input->post('status_kawin', TRUE),
            'agama'          => $this->input->post('agama', TRUE),
            'alamat'         => $this->input->post('alamat', TRUE),
            'no_telp'        => $this->input->post('no_telp', TRUE),
        );

        $drh = array(
            'tingkat_pendidikan' => $this->input->post('tingkat_pendidikan', TRUE),
            'jurusan'            => $this->input->post('jurusan', TRUE),
            'tahun_lulus'        => $this->input->post('tahun_lulus', TRUE) ?: NULL,
            'alumni'             => $this->input->post('alumni', TRUE),
        );

        if ($this->Pegawai_model->requires_kasubag_verification($pegawai['jabatan'])) {
            $pending = array_merge(
                array(
                    'nip' => $nip,
                    'jenis' => 'baru',
                ),
                $pegawai,
                $pribadi,
                $drh
            );

            if (!$this->Pegawai_model->insert_pending($pending)) {
                $this->session->set_flashdata('error', 'Data pegawai gagal diajukan. Silakan cek kembali data draft yang sudah pernah diproses.');
                redirect('pegawai/tambah');
                return;
            }

            $this->session->set_flashdata('success', 'Data pegawai berhasil diajukan dan menunggu persetujuan Kasubag.');
            redirect('pegawai');
            return;
        }

        $pegawai['nip'] = $nip;
        $pegawai['role'] = 'pegawai';
        $pegawai['password'] = password_hash(DEFAULT_PEGAWAI_PASSWORD, PASSWORD_DEFAULT);

        if (!$this->Pegawai_model->insert($pegawai, array_merge(array('nip' => $nip), $pribadi), array_merge(array('nip' => $nip), $drh))) {
            $this->session->set_flashdata('error', 'Data pegawai gagal disimpan.');
            redirect('pegawai/tambah');
            return;
        }

        $this->session->set_flashdata('success', 'Data pegawai berhasil disimpan. Pegawai baru dapat login dengan password default ' . DEFAULT_PEGAWAI_PASSWORD . '.');
        redirect('pegawai');
    }

    public function draft_verifikasi()
    {
        $this->guard_draft_verification_role();

        $data['title'] = 'Draft Verifikasi';
        $data['draft_verifikasi'] = $this->Pegawai_model->get_all_processed_drafts();

        $this->load->view('templates/header', $data);
        $this->load->view('pegawai/draft_verifikasi', $data);
        $this->load->view('templates/footer');
    }

    public function akun_pegawai()
    {
        $this->guard_petugas_only_role('Menu Akun Pegawai hanya dapat diakses oleh role Petugas.');

        $data['title'] = 'Akun Pegawai';
        $data['akun_pegawai'] = $this->Pegawai_model->get_all_accounts();

        $this->load->view('templates/header', $data);
        $this->load->view('pegawai/akun_pegawai', $data);
        $this->load->view('templates/footer');
    }

    public function ubah_password_akun($nip)
    {
        $this->guard_petugas_only_role('Menu Akun Pegawai hanya dapat diakses oleh role Petugas.');

        $data['title'] = 'Update Password Akun Pegawai';
        $data['akun'] = $this->Pegawai_model->get_account_by_nip($nip);

        if (empty($data['akun'])) {
            show_404();
        }

        $this->load->view('templates/header', $data);
        $this->load->view('pegawai/ubah_password_akun', $data);
        $this->load->view('templates/footer');
    }

    public function simpan_password_akun($nip)
    {
        $this->guard_petugas_only_role('Menu Akun Pegawai hanya dapat diakses oleh role Petugas.');

        $akun = $this->Pegawai_model->get_account_by_nip($nip);

        if (empty($akun)) {
            show_404();
        }

        $new_password = trim((string) $this->input->post('new_password', TRUE));
        $confirm_password = trim((string) $this->input->post('confirm_password', TRUE));

        if ($new_password === '' || $confirm_password === '') {
            $this->session->set_flashdata('error', 'Password baru dan konfirmasi password wajib diisi.');
            redirect('pegawai/ubah_password_akun/' . $nip);
            return;
        }

        if (strlen($new_password) < 8 || !preg_match('/[A-Za-z]/', $new_password) || !preg_match('/[0-9]/', $new_password)) {
            $this->session->set_flashdata('error', 'Password baru minimal 8 karakter dan harus mengandung huruf serta angka.');
            redirect('pegawai/ubah_password_akun/' . $nip);
            return;
        }

        if ($new_password !== $confirm_password) {
            $this->session->set_flashdata('error', 'Konfirmasi password baru tidak cocok.');
            redirect('pegawai/ubah_password_akun/' . $nip);
            return;
        }

        if (!$this->Pegawai_model->update_account_password($nip, password_hash($new_password, PASSWORD_DEFAULT))) {
            $this->session->set_flashdata('error', 'Password akun pegawai gagal diperbarui.');
            redirect('pegawai/ubah_password_akun/' . $nip);
            return;
        }

        $this->session->set_flashdata('success', 'Password akun pegawai berhasil diperbarui.');
        redirect('pegawai/akun_pegawai');
    }

    public function edit($nip)
    {
        $this->guard_read_only_role();

        $data['title'] = 'Edit Pegawai';
        $data['pegawai'] = $this->Pegawai_model->get_by_nip($nip);

        if (empty($data['pegawai'])) {
            show_404();
        }

        $this->load->view('templates/header', $data);
        $this->load->view('pegawai/edit', $data);
        $this->load->view('templates/footer');
    }

    public function update($nip)
    {
        $this->guard_read_only_role();
        $existing_pegawai = $this->Pegawai_model->get_by_nip($nip);

        if (empty($existing_pegawai)) {
            show_404();
        }

        $pangkat_terakhir = trim((string) $this->input->post('pangkat_terakhir', TRUE));

        if (!simpeg_is_valid_pangkat_terakhir($pangkat_terakhir, array($existing_pegawai->pangkat_terakhir))) {
            $this->session->set_flashdata('error', 'Pangkat / Gol. Ruang Terakhir harus dipilih dari daftar yang tersedia.');
            redirect('pegawai/edit/' . $nip);
            return;
        }

        $pegawai = array(
            'nama'                => $this->input->post('nama', TRUE),
            'gol_ruang_cpns'      => $this->input->post('gol_ruang_cpns', TRUE),
            'tmt_cpns'            => $this->input->post('tmt_cpns', TRUE) ?: NULL,
            'pangkat_terakhir'    => $pangkat_terakhir,
            'jenis_kelamin'       => $this->input->post('jenis_kelamin', TRUE),
            'jabatan'             => $this->input->post('jabatan', TRUE),
            'eselon'              => $this->input->post('eselon', TRUE),
            'diklat_penjenjangan' => $this->input->post('diklat_penjenjangan', TRUE),
            'instansi_pembayar'   => $this->input->post('instansi_pembayar', TRUE),
            'keterangan'          => $this->input->post('keterangan', TRUE),
        );

        $pribadi = array(
            'tempat_lahir'   => $this->input->post('tempat_lahir', TRUE),
            'tanggal_lahir'  => $this->input->post('tanggal_lahir', TRUE) ?: NULL,
            'status_kawin'   => $this->input->post('status_kawin', TRUE),
            'agama'          => $this->input->post('agama', TRUE),
            'alamat'         => $this->input->post('alamat', TRUE),
            'no_telp'        => $this->input->post('no_telp', TRUE),
        );

        $drh = array(
            'tingkat_pendidikan' => $this->input->post('tingkat_pendidikan', TRUE),
            'jurusan'            => $this->input->post('jurusan', TRUE),
            'tahun_lulus'        => $this->input->post('tahun_lulus', TRUE) ?: NULL,
            'alumni'             => $this->input->post('alumni', TRUE),
        );

        $this->Pegawai_model->update($nip, $pegawai, $pribadi, $drh);
        $this->session->set_flashdata('success', 'Data pegawai berhasil diperbarui!');
        redirect('pegawai');
    }

    public function hapus($nip)
    {
        $this->guard_read_only_role();

        $pegawai = $this->Pegawai_model->get_by_nip($nip);
        if (empty($pegawai)) {
            show_404();
        }

        $this->Pegawai_model->delete($nip);
        $this->session->set_flashdata('success', 'Data pegawai berhasil dihapus!');
        redirect('pegawai');
    }

    public function detail($nip)
    {
        $data['title'] = 'Detail Pegawai';
        $data['pegawai'] = $this->Pegawai_model->get_by_nip($nip);

        if (empty($data['pegawai'])) {
            show_404();
        }

        $this->load->view('templates/header', $data);
        $this->load->view('pegawai/detail', $data);
        $this->load->view('templates/footer');
    }

    private function guard_read_only_role()
    {
        if ($this->session->userdata('role') === 'kasubag') {
            $this->session->set_flashdata('error', 'Role Kasubag hanya dapat melihat data pegawai.');
            redirect('pegawai');
            exit;
        }
    }

    private function guard_draft_verification_role()
    {
        $this->guard_petugas_only_role('Menu Draft Verifikasi hanya dapat diakses oleh role Petugas.');
    }

    private function guard_petugas_only_role($message)
    {
        if ($this->session->userdata('role') !== 'petugas') {
            $this->session->set_flashdata('error', $message);
            redirect('pegawai');
            exit;
        }
    }
}
