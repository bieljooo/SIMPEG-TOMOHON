<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Surat extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Pengajuan_surat_model');

        if (
            !$this->session->userdata('logged_in') ||
            $this->session->userdata('role') !== 'petugas'
        ) {
            redirect('auth');
        }
    }

    public function index()
    {
        $surat_masuk = $this->Pengajuan_surat_model->get_all_surat_rekomendasi_masuk();

        foreach ($surat_masuk as $item) {
            $item->status_label = $this->get_status_label($item->status);
            $item->jenis_surat_label = $this->get_jenis_surat_label($item->jenis_surat);
        }

        $data['title'] = 'Surat Masuk';
        $data['surat_masuk'] = $surat_masuk;

        $this->load->view('templates/header', $data);
        $this->load->view('surat/index', $data);
        $this->load->view('templates/footer');
    }

    public function nomor($id)
    {
        $data['title'] = 'Nomor Surat';
        $data['surat'] = $this->Pengajuan_surat_model->get_surat_rekomendasi_by_id($id);

        if (empty($data['surat'])) {
            show_404();
        }

        $data['status_label'] = $this->get_status_label($data['surat']->status);
        $data['jenis_surat_label'] = $this->get_jenis_surat_label($data['surat']->jenis_surat);
        $data['default_sifat'] = !empty($data['surat']->sifat) ? $data['surat']->sifat : 'Penting';
        $data['default_lampiran'] = ($data['surat']->lampiran !== NULL && $data['surat']->lampiran !== '') ? $data['surat']->lampiran : '-';
        $data['default_perihal'] = !empty($data['surat']->perihal) ? $data['surat']->perihal : $data['jenis_surat_label'];
        $data['default_template_no'] = !empty($data['surat']->template_no) ? $data['surat']->template_no : '1.';
        $data['default_template_nama'] = !empty($data['surat']->template_nama) ? $data['surat']->template_nama : $data['surat']->nama;
        $data['default_template_nip'] = !empty($data['surat']->template_nip) ? $data['surat']->template_nip : $data['surat']->nip;
        $data['show_template_pemohon_fields'] = ($data['surat']->jenis_surat === 'usulan_kenaikan_gaji_berkala');

        $this->load->view('templates/header', $data);
        $this->load->view('surat/nomor', $data);
        $this->load->view('templates/footer');
    }

    public function update_nomor($id)
    {
        $surat = $this->Pengajuan_surat_model->get_surat_rekomendasi_by_id($id);

        if (empty($surat)) {
            show_404();
        }

        if ($surat->status !== 'pending_petugas') {
            $this->session->set_flashdata('error', 'Surat ini sudah diproses dan tidak bisa dinomori ulang.');
            redirect('surat');
            return;
        }

        $nomor_surat = trim((string) $this->input->post('nomor_surat', TRUE));
        $sifat = trim((string) $this->input->post('sifat', TRUE));
        $lampiran = trim((string) $this->input->post('lampiran', TRUE));
        $perihal = trim((string) $this->input->post('perihal', TRUE));
        $template_no = trim((string) $this->input->post('template_no', TRUE));
        $template_nama = trim((string) $this->input->post('template_nama', TRUE));
        $template_nip = trim((string) $this->input->post('template_nip', TRUE));

        if ($nomor_surat === '' || $sifat === '' || $lampiran === '' || $perihal === '') {
            $this->session->set_flashdata('error', 'Nomor, sifat, lampiran, dan perihal wajib diisi.');
            redirect('surat/nomor/' . $id);
            return;
        }

        if ($surat->jenis_surat === 'usulan_kenaikan_gaji_berkala' && ($template_no === '' || $template_nama === '' || $template_nip === '')) {
            $this->session->set_flashdata('error', 'No, nama, dan NIP pemohon wajib diisi untuk surat kenaikan gaji berkala.');
            redirect('surat/nomor/' . $id);
            return;
        }

        $template_update_data = array(
            'template_no' => !empty($surat->template_no) ? $surat->template_no : '1.',
            'template_nama' => !empty($surat->template_nama) ? $surat->template_nama : $surat->nama,
            'template_nip' => !empty($surat->template_nip) ? $surat->template_nip : $surat->nip,
        );

        if ($surat->jenis_surat === 'usulan_kenaikan_gaji_berkala') {
            $template_update_data = array(
                'template_no' => $template_no,
                'template_nama' => $template_nama,
                'template_nip' => $template_nip,
            );
        }

        $next_status = $this->Pengajuan_surat_model->resolve_status_after_petugas((int) $surat->jabatan_urutan);
        $petugas_identifier = $this->session->userdata('user_id')
            ? 'user:' . $this->session->userdata('user_id')
            : NULL;
        $updated = $this->Pengajuan_surat_model->update_surat_rekomendasi_nomor(
            $id,
            $nomor_surat,
            $petugas_identifier,
            $next_status,
            array(
                'sifat' => $sifat,
                'lampiran' => $lampiran,
                'perihal' => $perihal,
            ) + $template_update_data
        );

        if (!$updated) {
            $this->session->set_flashdata('error', 'Nomor surat gagal disimpan.');
            redirect('surat/nomor/' . $id);
            return;
        }

        $this->session->set_flashdata('success', 'Nomor surat berhasil disimpan dan surat diteruskan ke tahap berikutnya.');
        redirect('surat');
    }

    public function hapus($id)
    {
        $surat = $this->Pengajuan_surat_model->get_surat_rekomendasi_by_id($id);

        if (empty($surat)) {
            show_404();
        }

        if (!$this->Pengajuan_surat_model->delete_surat_masuk($id)) {
            $this->session->set_flashdata('error', 'Surat gagal dihapus.');
            redirect('surat');
            return;
        }

        $this->session->set_flashdata('success', 'Surat berhasil dihapus.');
        redirect('surat');
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
