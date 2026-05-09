<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pegawai extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Pegawai_model');

        // Hanya admin/petugas/kasubag yang boleh akses
        if (!$this->session->userdata('logged_in') || $this->session->userdata('role') === 'pegawai') {
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

        if ($this->Pegawai_model->nip_exists($nip) || $this->Pegawai_model->pending_nip_exists($nip)) {
            $this->session->set_flashdata('error', 'NIP sudah terdaftar atau masih menunggu persetujuan Kasubag.');
            redirect('pegawai/tambah');
            return;
        }

        $pending = array(
            'nip'                 => $nip,
            'nama'                => $this->input->post('nama', TRUE),
            'gol_ruang_cpns'      => $this->input->post('gol_ruang_cpns', TRUE),
            'tmt_cpns'            => $this->input->post('tmt_cpns', TRUE) ?: NULL,
            'pangkat_terakhir'    => $this->input->post('pangkat_terakhir', TRUE),
            'jenis_kelamin'       => $this->input->post('jenis_kelamin', TRUE),
            'jabatan'             => $this->input->post('jabatan', TRUE),
            'eselon'              => $this->input->post('eselon', TRUE),
            'diklat_penjenjangan' => $this->input->post('diklat_penjenjangan', TRUE),
            'instansi_pembayar'   => $this->input->post('instansi_pembayar', TRUE),
            'keterangan'          => $this->input->post('keterangan', TRUE),
            'tempat_lahir'   => $this->input->post('tempat_lahir', TRUE),
            'tanggal_lahir'  => $this->input->post('tanggal_lahir', TRUE) ?: NULL,
            'status_kawin'   => $this->input->post('status_kawin', TRUE),
            'agama'          => $this->input->post('agama', TRUE),
            'alamat'         => $this->input->post('alamat', TRUE),
            'no_telp'        => $this->input->post('no_telp', TRUE),
            'tingkat_pendidikan' => $this->input->post('tingkat_pendidikan', TRUE),
            'jurusan'            => $this->input->post('jurusan', TRUE),
            'tahun_lulus'        => $this->input->post('tahun_lulus', TRUE) ?: NULL,
            'alumni'             => $this->input->post('alumni', TRUE),
        );

        if (!$this->Pegawai_model->insert_pending($pending)) {
            $this->session->set_flashdata('error', 'Data pegawai gagal diajukan. Silakan cek kembali data draft yang sudah pernah diproses.');
            redirect('pegawai/tambah');
            return;
        }

        $this->session->set_flashdata('success', 'Data pegawai berhasil diajukan dan menunggu persetujuan Kasubag.');
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

        $pegawai = array(
            'nama'                => $this->input->post('nama', TRUE),
            'gol_ruang_cpns'      => $this->input->post('gol_ruang_cpns', TRUE),
            'tmt_cpns'            => $this->input->post('tmt_cpns', TRUE) ?: NULL,
            'pangkat_terakhir'    => $this->input->post('pangkat_terakhir', TRUE),
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
        if ($this->session->userdata('role') !== 'petugas') {
            $this->session->set_flashdata('error', 'Menu Draft Verifikasi hanya dapat diakses oleh role Petugas.');
            redirect('pegawai');
            exit;
        }
    }
}
