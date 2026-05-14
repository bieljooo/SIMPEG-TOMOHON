<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Surat_pimpinan extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Pengajuan_surat_model');

        if (
            !$this->session->userdata('logged_in') ||
            !in_array($this->session->userdata('role'), array('kasubag', 'sek', 'kadis'), TRUE)
        ) {
            redirect('auth');
        }
    }

    public function validasi_surat()
    {
        $this->guard_role('kadis');
        $this->render_queue_page('kadis', 'Validasi Surat', 'Validasi');
    }

    public function verifikasi_surat()
    {
        $role = $this->session->userdata('role');

        if (!in_array($role, array('kasubag', 'sek'), TRUE)) {
            redirect('dashboard');
            return;
        }

        $this->render_queue_page($role, 'Verifikasi Surat', 'Setujui');
    }

    public function detail($id)
    {
        $surat = $this->Pengajuan_surat_model->get_surat_rekomendasi_by_id($id);

        if (empty($surat)) {
            show_404();
        }

        $data = array(
            'title' => 'Detail Surat',
            'surat' => $surat,
            'status_label' => $this->get_status_label($surat->status),
            'jenis_surat_label' => $this->get_jenis_surat_label($surat->jenis_surat),
            'current_role' => $this->session->userdata('role'),
        );

        $this->load->view('templates/header_pegawai', $data);
        $this->load->view('surat_pimpinan/detail', $data);
        $this->load->view('templates/footer_pegawai');
    }

    public function setujui($id)
    {
        $role = $this->session->userdata('role');

        if (!in_array($role, array('kasubag', 'sek', 'kadis'), TRUE)) {
            redirect('dashboard');
            return;
        }

        $approved = $this->Pengajuan_surat_model->approve_surat_rekomendasi(
            $id,
            $role,
            $this->session->userdata('nip')
        );

        if (!$approved) {
            $this->session->set_flashdata('error', 'Surat gagal diproses atau statusnya sudah berubah.');
            redirect($this->get_redirect_path_by_role($role));
            return;
        }

        $this->session->set_flashdata('success', 'Surat berhasil diproses.');
        redirect($this->get_redirect_path_by_role($role));
    }

    public function tolak($id)
    {
        $role = $this->session->userdata('role');

        if (!in_array($role, array('kasubag', 'sek', 'kadis'), TRUE)) {
            redirect('dashboard');
            return;
        }

        $rejected = $this->Pengajuan_surat_model->reject_surat_rekomendasi(
            $id,
            $role,
            $this->session->userdata('nip')
        );

        if (!$rejected) {
            $this->session->set_flashdata('error', 'Penolakan surat gagal diproses atau statusnya sudah berubah.');
            redirect($this->get_redirect_path_by_role($role));
            return;
        }

        $this->session->set_flashdata('success', 'Surat berhasil ditolak.');
        redirect($this->get_redirect_path_by_role($role));
    }

    private function render_queue_page($role, $title, $primary_action_label)
    {
        $surat_list = $this->Pengajuan_surat_model->get_surat_rekomendasi_by_role($role);

        foreach ($surat_list as $item) {
            $item->status_label = $this->get_status_label($item->status);
            $item->jenis_surat_label = $this->get_jenis_surat_label($item->jenis_surat);
        }

        $data = array(
            'title' => $title,
            'surat_list' => $surat_list,
            'current_role' => $role,
            'primary_action_label' => $primary_action_label,
        );

        $this->load->view('templates/header_pegawai', $data);
        $this->load->view('surat_pimpinan/index', $data);
        $this->load->view('templates/footer_pegawai');
    }

    private function guard_role($role)
    {
        if ($this->session->userdata('role') !== $role) {
            redirect('dashboard');
            exit;
        }
    }

    private function get_redirect_path_by_role($role)
    {
        return ($role === 'kadis')
            ? 'surat_pimpinan/validasi_surat'
            : 'surat_pimpinan/verifikasi_surat';
    }

    private function get_jenis_surat_label($jenis_surat)
    {
        $map = array(
            'usulan_kenaikan_pangkat' => 'Usulan Kenaikan Pangkat',
            'usulan_kenaikan_gaji_berkala' => 'Usulan Kenaikan Gaji Berkala',
            'usulan_cuti_tahun' => 'Usulan Cuti Tahunan',
            'usulan_alasan_penting' => 'Usulan Cuti Alasan Penting',
            'cuti_alasan_penting' => 'Usulan Cuti Alasan Penting',
            'usulan_cuti_luar_negeri' => 'Usulan Cuti Luar Negeri',
        );

        return isset($map[$jenis_surat]) ? $map[$jenis_surat] : ucfirst(str_replace('_', ' ', (string) $jenis_surat));
    }

    private function get_status_label($status)
    {
        $map = array(
            'pending_petugas' => 'Menunggu penomoran petugas',
            'pending_kasubag' => 'Menunggu verifikasi kasubag',
            'pending_sek' => 'Menunggu verifikasi sek',
            'pending_kadis' => 'Menunggu validasi kadis',
            'approved' => 'Selesai',
            'rejected_kasubag' => 'Ditolak kasubag',
            'rejected_sek' => 'Ditolak sek',
            'rejected_kadis' => 'Ditolak kadis',
        );

        return isset($map[$status]) ? $map[$status] : ucfirst(str_replace('_', ' ', (string) $status));
    }
}
