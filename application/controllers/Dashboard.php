<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Pegawai_model');

        // Hanya role pegawai yang boleh akses
        if (!$this->session->userdata('logged_in') || $this->session->userdata('role') !== 'pegawai') {
            redirect('auth');
        }
    }

    public function index()
    {
        $nip = $this->session->userdata('nip');
        $data['title'] = 'Data Diri';
        $data['pegawai'] = $this->Pegawai_model->get_by_nip($nip);

        if (empty($data['pegawai'])) {
            show_404();
        }

        $this->load->view('templates/header_pegawai', $data);
        $this->load->view('dashboard/index', $data);
        $this->load->view('templates/footer_pegawai');
    }

    public function edit()
    {
        $nip = $this->session->userdata('nip');
        $data['title'] = 'Edit Data Diri';
        $data['pegawai'] = $this->Pegawai_model->get_by_nip($nip);

        if (empty($data['pegawai'])) {
            show_404();
        }

        $this->load->view('templates/header_pegawai', $data);
        $this->load->view('dashboard/edit', $data);
        $this->load->view('templates/footer_pegawai');
    }

    public function settings()
    {
        redirect('settings');
    }

    public function update()
    {
        $nip = $this->session->userdata('nip');

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

        // Update nama di session jika berubah
        $this->session->set_userdata('nama', $pegawai['nama']);

        $this->session->set_flashdata('success', 'Data diri berhasil diperbarui!');
        redirect('dashboard');
    }
}
