<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Surat extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Pengajuan_surat_model');

        if (
            !$this->session->userdata('logged_in') ||
            in_array($this->session->userdata('role'), array('pegawai', 'kasubag'), TRUE)
        ) {
            redirect('auth');
        }
    }

    public function index()
    {
        $data['title'] = 'Surat Masuk';
        $data['surat_masuk'] = array();

        $this->load->view('templates/header', $data);
        $this->load->view('surat/index', $data);
        $this->load->view('templates/footer');
    }

    public function nomor($id)
    {
        $this->session->set_flashdata('info', 'Surat sakit tidak masuk lagi ke daftar surat petugas.');
        redirect('surat');
    }

    public function update_nomor($id)
    {
        $this->session->set_flashdata('info', 'Surat sakit tidak masuk lagi ke daftar surat petugas.');
        redirect('surat');
    }
}
