<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Persetujuan_pegawai extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Pegawai_model');

        if (!$this->session->userdata('logged_in') || $this->session->userdata('role') !== 'kasubag') {
            redirect('auth');
        }
    }

    public function index()
    {
        $data['title'] = 'Persetujuan Data Pegawai';
        $data['pengajuan'] = $this->Pegawai_model->get_all_pending();

        $this->load->view('templates/header', $data);
        $this->load->view('persetujuan_pegawai', $data);
        $this->load->view('templates/footer');
    }

    public function detail($id)
    {
        $data['title'] = 'Detail Pengajuan Pegawai';
        $data['pegawai'] = $this->Pegawai_model->get_pending_by_id($id);

        if (empty($data['pegawai'])) {
            show_404();
        }

        $this->load->view('templates/header', $data);
        $this->load->view('persetujuan_pegawai_detail', $data);
        $this->load->view('templates/footer');
    }

    public function setujui($id)
    {
        $pending = $this->Pegawai_model->get_pending_by_id($id);

        if (empty($pending)) {
            show_404();
        }

        $jenis = !empty($pending->jenis) ? strtolower(trim((string) $pending->jenis)) : 'baru';

        if ($pending->status !== 'pending') {
            $this->session->set_flashdata('error', 'Data pegawai ini sudah diproses.');
            redirect('persetujuan_pegawai');
            return;
        }

        if ($jenis !== 'update' && $this->Pegawai_model->nip_exists($pending->nip)) {
            $this->session->set_flashdata('error', 'NIP ini sudah ada di data pegawai aktif.');
            redirect('persetujuan_pegawai');
            return;
        }

        $approved = $this->Pegawai_model->approve_pending(
            $id,
            $this->session->userdata('nip'),
            password_hash(DEFAULT_PEGAWAI_PASSWORD, PASSWORD_DEFAULT)
        );

        if (!$approved) {
            $this->session->set_flashdata('error', 'Persetujuan gagal diproses.');
            redirect('persetujuan_pegawai');
            return;
        }

        if ($jenis === 'update') {
            $this->session->set_flashdata('success', 'Perubahan data pegawai berhasil disetujui.');
        } else {
            $this->session->set_flashdata('success', 'Data pegawai disetujui. Pegawai baru dapat login dengan password default ' . DEFAULT_PEGAWAI_PASSWORD . '.');
        }
        redirect('persetujuan_pegawai');
    }

    public function tolak($id)
    {
        $pending = $this->Pegawai_model->get_pending_by_id($id);

        if (empty($pending)) {
            show_404();
        }

        $jenis = !empty($pending->jenis) ? strtolower(trim((string) $pending->jenis)) : 'baru';

        if ($pending->status !== 'pending') {
            $this->session->set_flashdata('error', 'Data pegawai ini sudah diproses.');
            redirect('persetujuan_pegawai');
            return;
        }

        $rejected = $this->Pegawai_model->reject_pending(
            $id,
            $this->session->userdata('nip')
        );

        if (!$rejected) {
            $this->session->set_flashdata('error', 'Penolakan data pegawai gagal diproses.');
            redirect('persetujuan_pegawai');
            return;
        }

        if ($jenis === 'update') {
            $this->session->set_flashdata('success', 'Perubahan data pegawai berhasil ditolak dan dipindahkan ke Draft Verifikasi.');
        } else {
            $this->session->set_flashdata('success', 'Data pegawai berhasil ditolak dan dipindahkan ke Draft Verifikasi.');
        }
        redirect('persetujuan_pegawai');
    }

    public function hapus($id)
    {
        $pending = $this->Pegawai_model->get_pending_by_id($id);

        if (empty($pending)) {
            show_404();
        }

        if ($pending->status !== 'pending') {
            $this->session->set_flashdata('error', 'Hanya draft yang masih pending yang bisa dihapus.');
            redirect('persetujuan_pegawai');
            return;
        }

        if (!$this->Pegawai_model->delete_pending($id)) {
            $this->session->set_flashdata('error', 'Data draft pegawai gagal dihapus.');
            redirect('persetujuan_pegawai');
            return;
        }

        $this->session->set_flashdata('success', 'Data draft pegawai berhasil dihapus.');
        redirect('persetujuan_pegawai');
    }
}
