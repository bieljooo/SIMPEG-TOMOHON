<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pengajuan_surat extends CI_Controller {
    const TEMPLATE_SURAT_SAKIT = 'C:\\Users\\Gabriel Pangkong\\Documents\\PROJECT MPPL\\SURAT IZIN ASN.docx';
    const TEMPLATE_SURAT_REKOMENDASI_KENAIKAN_PANGKAT = 'C:\\Users\\Gabriel Pangkong\\Documents\\PROJECT MPPL\\REKOMENDASI KENPA an. Lucky Rau (1).docx';
    const TEMPLATE_SURAT_REKOMENDASI_KENAIKAN_GAJI_BERKALA = 'C:\\Users\\Gabriel Pangkong\\Documents\\PROJECT MPPL\\USULAN KENAIKAN GAJI BERKALA (an SEK FENNY, JAMES).docx';
    const TEMPLATE_SURAT_REKOMENDASI_CUTI_TAHUNAN = 'C:\\Users\\Gabriel Pangkong\\Documents\\PROJECT MPPL\\Usulan Cuti Tahunan.docx';
    const TEMPLATE_SURAT_REKOMENDASI_CUTI_ALASAN_PENTING = 'C:\\Users\\Gabriel Pangkong\\Documents\\PROJECT MPPL\\Rekomendasi Cuti Alasan Penting.docx';
    const TEMPLATE_SURAT_REKOMENDASI_CUTI_LUAR_NEGERI = 'C:\\Users\\Gabriel Pangkong\\Documents\\PROJECT MPPL\\Permohonan Cuti  Tahunan Ke Luar Negeri.docx';
    const KODE_TEMPLATE_SURAT_SAKIT = 'surat_keterangan_sakit';
    const KODE_TEMPLATE_SURAT_REKOMENDASI_KENAIKAN_PANGKAT = 'usulan_kenaikan_pangkat';
    const KODE_TEMPLATE_SURAT_REKOMENDASI_KENAIKAN_GAJI_BERKALA = 'usulan_kenaikan_gaji_berkala';
    const KODE_TEMPLATE_SURAT_REKOMENDASI_CUTI_TAHUNAN = 'usulan_cuti_tahun';
    const KODE_TEMPLATE_SURAT_REKOMENDASI_CUTI_ALASAN_PENTING = 'usulan_alasan_penting';
    const KODE_TEMPLATE_SURAT_REKOMENDASI_CUTI_LUAR_NEGERI = 'usulan_cuti_luar_negeri';
    const PENANDATANGAN_NIP = '197704162010012004';

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Pegawai_model');
        $this->load->model('Pengajuan_surat_model');
        $this->load->model('Master_surat_model');

        if (
            !$this->session->userdata('logged_in') ||
            !in_array($this->session->userdata('role'), array('pegawai', 'kasubag', 'sek'), TRUE)
        ) {
            redirect('auth');
        }

        if ($this->db->table_exists('template_surat')) {
            $this->Master_surat_model->ensure_default_templates();
        }
    }

    public function index()
    {
        redirect('pengajuan_surat/surat_keterangan_sakit');
    }

    public function cuti_kenaikan_pangkat()
    {
        $nip = $this->session->userdata('nip');
        $data['title'] = 'Usulan Kenaikan Pangkat';
        $data['pegawai'] = $this->Pegawai_model->get_by_nip($nip);

        if (empty($data['pegawai'])) {
            show_404();
        }

        $this->load->view('templates/header_pegawai', $data);
        $this->load->view('pengajuan_surat/usulan_kenaikan_pangkat', $data);
        $this->load->view('templates/footer_pegawai');
    }

    public function pengajuan_cuti_tahun()
    {
        $nip = $this->session->userdata('nip');
        $data['title'] = 'Usulan Cuti Tahunan';
        $data['pegawai'] = $this->Pegawai_model->get_by_nip($nip);

        if (empty($data['pegawai'])) {
            show_404();
        }

        $this->load->view('templates/header_pegawai', $data);
        $this->load->view('pengajuan_surat/usulan_cuti_tahunan', $data);
        $this->load->view('templates/footer_pegawai');
    }

    public function usulan_cuti_tahunan()
    {
        $this->pengajuan_cuti_tahun();
    }

    public function cuti_alasan_penting()
    {
        $nip = $this->session->userdata('nip');
        $data['title'] = 'Usulan Cuti Alasan Penting';
        $data['pegawai'] = $this->Pegawai_model->get_by_nip($nip);

        if (empty($data['pegawai'])) {
            show_404();
        }

        $this->load->view('templates/header_pegawai', $data);
        $this->load->view('pengajuan_surat/cuti_alasan_penting', $data);
        $this->load->view('templates/footer_pegawai');
    }

    public function simpan_usulan_cuti_tahunan()
    {
        $nip = $this->session->userdata('nip');
        $pegawai = $this->Pegawai_model->get_by_nip($nip);
        $tanggal_mulai = trim((string) $this->input->post('tanggal_mulai', TRUE));
        $tanggal_selesai = trim((string) $this->input->post('tanggal_selesai', TRUE));
        $keterangan = trim((string) $this->input->post('keterangan', TRUE));

        if (empty($pegawai) || !$this->is_valid_date($tanggal_mulai) || !$this->is_valid_date($tanggal_selesai) || $keterangan === '') {
            $this->session->set_flashdata('error', 'Data cuti belum lengkap.');
            redirect('pengajuan_surat/pengajuan_cuti_tahun');
            return;
        }

        if (strtotime($tanggal_mulai) > strtotime($tanggal_selesai)) {
            $this->session->set_flashdata('error', 'Tanggal mulai tidak boleh lebih besar dari tanggal sampai dengan.');
            redirect('pengajuan_surat/pengajuan_cuti_tahun');
            return;
        }

        $data = array(
            'jenis_surat' => 'usulan_cuti_tahun',
            'nip' => $pegawai->nip,
            'nama' => $pegawai->nama,
            'pangkat' => $pegawai->pangkat_terakhir,
            'jabatan' => $pegawai->jabatan,
            'jabatan_urutan' => (int) $pegawai->jabatan_urutan,
            'sifat' => 'Penting',
            'lampiran' => '-',
            'perihal' => 'Usulan Cuti Tahunan',
            'tanggal_mulai' => $tanggal_mulai,
            'tanggal_selesai' => $tanggal_selesai,
            'keterangan' => $keterangan,
            'status' => 'pending_petugas',
        );

        $insert_id = $this->Pengajuan_surat_model->insert_surat_rekomendasi($data);

        if (!$insert_id) {
            $this->session->set_flashdata('error', 'Usulan Cuti Tahunan gagal disimpan.');
            redirect('pengajuan_surat/pengajuan_cuti_tahun');
            return;
        }

        $this->session->set_flashdata('success', 'Usulan Cuti Tahunan berhasil dikirim dan menunggu nomor surat dari petugas.');
        redirect('pengajuan_surat/proses_surat');
    }

    public function cuti_luar_negeri()
    {
        $nip = $this->session->userdata('nip');
        $data['title'] = 'Usulan Cuti Luar Negeri';
        $data['pegawai'] = $this->Pegawai_model->get_by_nip($nip);

        if (empty($data['pegawai'])) {
            show_404();
        }

        $this->load->view('templates/header_pegawai', $data);
        $this->load->view('pengajuan_surat/usulan_cuti_luar_negeri', $data);
        $this->load->view('templates/footer_pegawai');
    }

    public function kenaikan_gaji_berkala()
    {
        $nip = $this->session->userdata('nip');
        $data['title'] = 'Usulan Kenaikan Gaji Berkala';
        $data['pegawai'] = $this->Pegawai_model->get_by_nip($nip);

        if (empty($data['pegawai'])) {
            show_404();
        }

        $this->load->view('templates/header_pegawai', $data);
        $this->load->view('pengajuan_surat/usulan_kenaikan_gaji_berkala', $data);
        $this->load->view('templates/footer_pegawai');
    }

    public function proses_surat()
    {
        $nip = $this->session->userdata('nip');
        $data['title'] = 'Proses Surat';
        $data['surat_rekomendasi'] = $this->Pengajuan_surat_model->get_surat_rekomendasi_by_nip($nip);

        $this->load->view('templates/header_pegawai', $data);
        $this->load->view('pengajuan_surat/proses_surat', $data);
        $this->load->view('templates/footer_pegawai');
    }

    public function surat_keterangan_sakit()
    {
        $nip = $this->session->userdata('nip');
        $data['title'] = 'Surat Keterangan Sakit';
        $data['pegawai'] = $this->Pegawai_model->get_by_nip($nip);
        $data['penandatangan'] = $this->Pegawai_model->get_by_nip(self::PENANDATANGAN_NIP);

        if (empty($data['pegawai']) || empty($data['penandatangan'])) {
            show_404();
        }

        $this->load->view('templates/header_pegawai', $data);
        $this->load->view('pengajuan_surat/surat_keterangan_sakit', $data);
        $this->load->view('templates/footer_pegawai');
    }

    public function download_surat()
    {
        redirect('pengajuan_surat/download_surat_sakit');
    }

    public function download_surat_sakit()
    {
        $nip = $this->session->userdata('nip');
        $data['title'] = 'Download Surat Sakit';
        $data['surat_pegawai'] = $this->Pengajuan_surat_model->get_surat_pegawai_by_nip($nip);
        $template = $this->get_template_assets();
        $data['template_penandatangan'] = $this->build_template_penandatangan(
            isset($template['signer']) ? $template['signer'] : array(),
            $this->Pegawai_model->get_by_nip(self::PENANDATANGAN_NIP)
        );

        $this->load->view('templates/header_pegawai', $data);
        $this->load->view('pengajuan_surat/download_surat', $data);
        $this->load->view('templates/footer_pegawai');
    }

    public function download_surat_rekomendasi()
    {
        $nip = $this->session->userdata('nip');
        $data['title'] = 'Download Surat Rekomendasi';
        $data['surat_rekomendasi'] = $this->Pengajuan_surat_model->get_surat_rekomendasi_download_by_nip($nip);

        $this->load->view('templates/header_pegawai', $data);
        $this->load->view('pengajuan_surat/download_surat_rekomendasi', $data);
        $this->load->view('templates/footer_pegawai');
    }

    public function unduh_surat_rekomendasi($id)
    {
        $this->output_surat_rekomendasi_docx($id);
    }

    public function view_surat_rekomendasi($id)
    {
        $nip = $this->session->userdata('nip');
        $source = strtolower(trim((string) $this->input->get('source', TRUE)));
        $surat = $this->Pengajuan_surat_model->get_surat_rekomendasi_by_id($id);

        if (empty($surat) || (string) $surat->nip !== (string) $nip) {
            show_404();
        }

        $jenis_surat_labels = array(
            'usulan_kenaikan_pangkat' => 'Usulan Kenaikan Pangkat',
            'usulan_kenaikan_gaji_berkala' => 'Usulan Kenaikan Gaji Berkala',
            'usulan_cuti_tahun' => 'Usulan Cuti Tahunan',
            'usulan_alasan_penting' => 'Usulan Cuti Alasan Penting',
            'cuti_alasan_penting' => 'Usulan Cuti Alasan Penting',
            'usulan_cuti_luar_negeri' => 'Usulan Cuti Luar Negeri',
        );
        $status_labels = array(
            'pending_petugas' => 'Menunggu penomoran petugas',
            'pending_kasubag' => 'Menunggu verifikasi kasubag',
            'pending_sek' => 'Menunggu verifikasi sek',
            'pending_kadis' => 'Menunggu validasi kadis',
            'approved' => 'Selesai',
            'rejected_kasubag' => 'Ditolak kasubag',
            'rejected_sek' => 'Ditolak sek',
            'rejected_kadis' => 'Ditolak kadis',
        );

        $data['title'] = 'View Surat Rekomendasi';
        $data['surat'] = $surat;
        $data['jenis_surat_label'] = isset($jenis_surat_labels[$surat->jenis_surat])
            ? $jenis_surat_labels[$surat->jenis_surat]
            : ucwords(str_replace('_', ' ', $surat->jenis_surat));
        $data['status_label'] = isset($status_labels[$surat->status])
            ? $status_labels[$surat->status]
            : ucwords(str_replace('_', ' ', $surat->status));
        $data['back_url'] = ($source === 'download')
            ? site_url('pengajuan_surat/download_surat_rekomendasi')
            : site_url('pengajuan_surat/proses_surat');

        $this->load->view('templates/header_pegawai', $data);
        $this->load->view('pengajuan_surat/view_surat_rekomendasi', $data);
        $this->load->view('templates/footer_pegawai');
    }

    public function hapus_surat_rekomendasi($id)
    {
        $nip = $this->session->userdata('nip');
        $surat = $this->Pengajuan_surat_model->get_surat_rekomendasi_download_detail($id, $nip);

        if (empty($surat)) {
            show_404();
        }

        if (!$this->Pengajuan_surat_model->delete_surat_rekomendasi($id, $nip)) {
            $this->session->set_flashdata('error', 'Surat rekomendasi gagal dihapus.');
            redirect('pengajuan_surat/download_surat_rekomendasi');
            return;
        }

        $this->session->set_flashdata('success', 'Surat rekomendasi berhasil dihapus.');
        redirect('pengajuan_surat/download_surat_rekomendasi');
    }

    public function simpan_usulan_kenaikan_pangkat()
    {
        $nip = $this->session->userdata('nip');
        $pegawai = $this->Pegawai_model->get_by_nip($nip);

        if (empty($pegawai)) {
            show_404();
        }

        $data = array(
            'jenis_surat' => 'usulan_kenaikan_pangkat',
            'nip' => $pegawai->nip,
            'nama' => $pegawai->nama,
            'pangkat' => $pegawai->pangkat_terakhir,
            'jabatan' => $pegawai->jabatan,
            'jabatan_urutan' => (int) $pegawai->jabatan_urutan,
            'sifat' => 'Penting',
            'lampiran' => '-',
            'perihal' => 'Rekomendasi Kenaikan Pangkat',
            'template_no' => '1.',
            'template_nama' => $pegawai->nama,
            'template_nip' => $pegawai->nip,
            'status' => 'pending_petugas',
        );

        $insert_id = $this->Pengajuan_surat_model->insert_surat_rekomendasi($data);

        if (!$insert_id) {
            $this->session->set_flashdata('error', 'Usulan kenaikan pangkat gagal disimpan.');
            redirect('pengajuan_surat/cuti_kenaikan_pangkat');
            return;
        }

        $this->session->set_flashdata('success', 'Usulan kenaikan pangkat berhasil dikirim dan menunggu nomor surat dari petugas.');
        redirect('pengajuan_surat/proses_surat');
    }

    public function simpan_usulan_kenaikan_gaji_berkala()
    {
        $nip = $this->session->userdata('nip');
        $pegawai = $this->Pegawai_model->get_by_nip($nip);

        if (empty($pegawai)) {
            show_404();
        }

        $data = array(
            'jenis_surat' => 'usulan_kenaikan_gaji_berkala',
            'nip' => $pegawai->nip,
            'nama' => $pegawai->nama,
            'pangkat' => $pegawai->pangkat_terakhir,
            'jabatan' => $pegawai->jabatan,
            'jabatan_urutan' => (int) $pegawai->jabatan_urutan,
            'sifat' => 'Penting',
            'lampiran' => '-',
            'perihal' => 'Usulan Kenaikan Gaji Berkala',
            'template_no' => '1.',
            'template_nama' => $pegawai->nama,
            'template_nip' => $pegawai->nip,
            'status' => 'pending_petugas',
        );

        $insert_id = $this->Pengajuan_surat_model->insert_surat_rekomendasi($data);

        if (!$insert_id) {
            $this->session->set_flashdata('error', 'Usulan kenaikan gaji berkala gagal disimpan.');
            redirect('pengajuan_surat/kenaikan_gaji_berkala');
            return;
        }

        $this->session->set_flashdata('success', 'Usulan kenaikan gaji berkala berhasil dikirim dan menunggu nomor surat dari petugas.');
        redirect('pengajuan_surat/proses_surat');
    }

    public function simpan_cuti_alasan_penting()
    {
        $nip = $this->session->userdata('nip');
        $pegawai = $this->Pegawai_model->get_by_nip($nip);
        $tanggal_mulai = trim((string) $this->input->post('tanggal_mulai', TRUE));
        $tanggal_selesai = trim((string) $this->input->post('tanggal_selesai', TRUE));
        $keterangan = trim((string) $this->input->post('keterangan', TRUE));

        if (empty($pegawai) || !$this->is_valid_date($tanggal_mulai) || !$this->is_valid_date($tanggal_selesai) || $keterangan === '') {
            $this->session->set_flashdata('error', 'Data cuti belum lengkap.');
            redirect('pengajuan_surat/cuti_alasan_penting');
            return;
        }

        if (strtotime($tanggal_mulai) > strtotime($tanggal_selesai)) {
            $this->session->set_flashdata('error', 'Tanggal mulai tidak boleh lebih besar dari tanggal sampai dengan.');
            redirect('pengajuan_surat/cuti_alasan_penting');
            return;
        }

        $data = array(
            'jenis_surat' => 'cuti_alasan_penting',
            'nip' => $pegawai->nip,
            'nama' => $pegawai->nama,
            'pangkat' => $pegawai->pangkat_terakhir,
            'jabatan' => $pegawai->jabatan,
            'jabatan_urutan' => (int) $pegawai->jabatan_urutan,
            'sifat' => 'Penting',
            'lampiran' => '-',
            'perihal' => 'Rekomendasi Cuti Alasan Penting',
            'tanggal_mulai' => $tanggal_mulai,
            'tanggal_selesai' => $tanggal_selesai,
            'keterangan' => $keterangan,
            'status' => 'pending_petugas',
        );

        $insert_id = $this->Pengajuan_surat_model->insert_surat_rekomendasi($data);

        if (!$insert_id) {
            $this->session->set_flashdata('error', 'Cuti Alasan Penting gagal disimpan.');
            redirect('pengajuan_surat/cuti_alasan_penting');
            return;
        }

        $this->session->set_flashdata('success', 'Cuti Alasan Penting berhasil dikirim dan menunggu nomor surat dari petugas.');
        redirect('pengajuan_surat/proses_surat');
    }

    public function simpan_cuti_luar_negeri()
    {
        $nip = $this->session->userdata('nip');
        $pegawai = $this->Pegawai_model->get_by_nip($nip);
        $negara_tujuan = trim((string) $this->input->post('negara_tujuan', TRUE));
        $tanggal_mulai = trim((string) $this->input->post('tanggal_mulai', TRUE));
        $tanggal_selesai = trim((string) $this->input->post('tanggal_selesai', TRUE));
        $keterangan = trim((string) $this->input->post('keterangan', TRUE));

        if (
            empty($pegawai) ||
            $negara_tujuan === '' ||
            !$this->is_valid_date($tanggal_mulai) ||
            !$this->is_valid_date($tanggal_selesai) ||
            $keterangan === ''
        ) {
            $this->session->set_flashdata('error', 'Data cuti belum lengkap.');
            redirect('pengajuan_surat/cuti_luar_negeri');
            return;
        }

        if (strtotime($tanggal_mulai) > strtotime($tanggal_selesai)) {
            $this->session->set_flashdata('error', 'Tanggal mulai tidak boleh lebih besar dari tanggal sampai dengan.');
            redirect('pengajuan_surat/cuti_luar_negeri');
            return;
        }

        $data = array(
            'jenis_surat' => 'usulan_cuti_luar_negeri',
            'nip' => $pegawai->nip,
            'nama' => $pegawai->nama,
            'pangkat' => $pegawai->pangkat_terakhir,
            'jabatan' => $pegawai->jabatan,
            'jabatan_urutan' => (int) $pegawai->jabatan_urutan,
            'sifat' => 'Penting',
            'lampiran' => '-',
            'perihal' => 'Permohonan Cuti Tahunan Ke Luar Negeri',
            'tanggal_mulai' => $tanggal_mulai,
            'tanggal_selesai' => $tanggal_selesai,
            'keterangan' => $keterangan,
            'negara_tujuan' => $negara_tujuan,
            'status' => 'pending_petugas',
        );

        $insert_id = $this->Pengajuan_surat_model->insert_surat_rekomendasi($data);

        if (!$insert_id) {
            $this->session->set_flashdata('error', 'Usulan Cuti Luar Negeri gagal disimpan.');
            redirect('pengajuan_surat/cuti_luar_negeri');
            return;
        }

        $this->session->set_flashdata('success', 'Usulan Cuti Luar Negeri berhasil dikirim dan menunggu nomor surat dari petugas.');
        redirect('pengajuan_surat/proses_surat');
    }

    public function simpan_surat_keterangan_sakit()
    {
        $nip = $this->session->userdata('nip');
        $jenis = trim((string) $this->input->post('jenis', TRUE));
        $tanggal_surat = trim((string) $this->input->post('tanggal_surat', TRUE));
        $tanggal_izin = trim((string) $this->input->post('tanggal_izin', TRUE));
        $alasan = trim((string) $this->input->post('alasan', TRUE));
        $penandatangan_nip = trim((string) $this->input->post('penandatangan_nip', TRUE));
        $jenis_valid = array('pagi', 'sore', '1 hari');
        $pegawai = $this->Pegawai_model->get_by_nip($nip);
        $penandatangan = $this->Pegawai_model->get_by_nip($penandatangan_nip);
        $template = $this->get_template_assets();

        if (
            empty($nip) ||
            !in_array($jenis, $jenis_valid, TRUE) ||
            !$this->is_valid_date($tanggal_surat) ||
            !$this->is_valid_date($tanggal_izin) ||
            $alasan === '' ||
            empty($pegawai) ||
            $penandatangan_nip !== self::PENANDATANGAN_NIP ||
            empty($penandatangan) ||
            empty($template['template_found'])
        ) {
            $this->session->set_flashdata('error', 'Data surat belum lengkap atau template surat tidak ditemukan.');
            redirect('pengajuan_surat/surat_keterangan_sakit');
            return;
        }

        $this->Pengajuan_surat_model->insert_surat_pegawai(array(
            'nip' => $nip,
            'jenis' => $jenis,
            'tanggal_surat' => $tanggal_surat,
            'tanggal_izin' => $tanggal_izin,
            'alasan' => $alasan,
            'penandatangan_nip' => $penandatangan_nip,
        ));

        $this->session->set_flashdata('success', 'Surat Keterangan Sakit berhasil dibuat dan siap diunduh.');
        redirect('pengajuan_surat/download_surat_sakit');
    }

    public function unduh_surat_keterangan_sakit($id)
    {
        $this->output_surat_keterangan_sakit_docx($id);
    }

    public function hapus_surat_keterangan_sakit($id)
    {
        $nip = $this->session->userdata('nip');
        $surat = $this->Pengajuan_surat_model->get_surat_pegawai_detail($id, $nip);

        if (empty($surat)) {
            show_404();
        }

        if (!$this->Pengajuan_surat_model->delete_surat_pegawai($id, $nip)) {
            $this->session->set_flashdata('error', 'Surat gagal dihapus.');
            redirect('pengajuan_surat/download_surat_sakit');
            return;
        }

        $this->session->set_flashdata('success', 'Surat berhasil dihapus.');
        redirect('pengajuan_surat/download_surat_sakit');
    }

    public function preview_surat_keterangan_sakit($id)
    {
        $nip = $this->session->userdata('nip');
        $surat = $this->Pengajuan_surat_model->get_surat_pegawai_detail($id, $nip);

        if (empty($surat)) {
            show_404();
        }

        $pegawai = $this->Pegawai_model->get_by_nip($surat->nip);
        $penandatangan = $this->Pegawai_model->get_by_nip($surat->penandatangan_nip);
        $template = $this->get_template_assets();

        if (empty($pegawai) || empty($penandatangan) || empty($template['template_found'])) {
            show_error('Template atau data surat tidak lengkap.', 500);
        }

        $data = array(
            'surat' => $surat,
            'pegawai' => $pegawai,
            'penandatangan' => $this->build_template_penandatangan(
                isset($template['signer']) ? $template['signer'] : array(),
                $penandatangan
            ),
            'header_lines' => $template['header_lines'],
            'logo_data_uri' => $template['logo_data_uri'],
            'nomor_surat' => $this->build_nomor_surat($surat->tanggal_surat),
            'kalimat_surat' => $this->build_kalimat_surat($surat->jenis, $surat->tanggal_izin, $surat->alasan),
            'tanggal_surat_indonesia' => $this->format_tanggal_indonesia($surat->tanggal_surat),
        );

        $this->load->view('pengajuan_surat/pdf_surat_keterangan_sakit', $data);
    }

    private function output_surat_keterangan_sakit_docx($id)
    {
        $nip = $this->session->userdata('nip');
        $surat = $this->Pengajuan_surat_model->get_surat_pegawai_detail($id, $nip);

        if (empty($surat)) {
            show_404();
        }

        $pegawai = $this->Pegawai_model->get_by_nip($surat->nip);
        $penandatangan = $this->Pegawai_model->get_by_nip($surat->penandatangan_nip);
        $template = $this->get_template_assets();

        if (empty($pegawai) || empty($penandatangan) || empty($template['template_found'])) {
            show_error('Template atau data surat tidak lengkap.', 500);
        }

        $generated_file = $this->generate_surat_keterangan_sakit_docx(
            $surat,
            $pegawai,
            $penandatangan,
            $template['template_path'],
            isset($template['signer']) ? $template['signer'] : array()
        );

        if ($generated_file === FALSE || !is_file($generated_file)) {
            show_error('File Word surat tidak berhasil dibuat.', 500);
        }

        $filename = 'surat-keterangan-sakit-' . $surat->id . '.docx';

        if (ob_get_length()) {
            ob_end_clean();
        }

        header('Content-Description: File Transfer');
        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($generated_file));
        header('Cache-Control: max-age=0');
        header('Pragma: public');

        readfile($generated_file);
        @unlink($generated_file);
        exit;
    }

    private function generate_surat_keterangan_sakit_docx($surat, $pegawai, $penandatangan, $template_path, $template_signer = array())
    {
        $temp_base = tempnam(sys_get_temp_dir(), 'surat_sakit_');
        if ($temp_base === FALSE) {
            return FALSE;
        }

        @unlink($temp_base);
        $temp_file = $temp_base . '.docx';

        if (empty($template_path) || !is_file($template_path) || !copy($template_path, $temp_file)) {
            return FALSE;
        }

        $zip = new ZipArchive();
        if ($zip->open($temp_file) !== TRUE) {
            @unlink($temp_file);
            return FALSE;
        }

        $document_xml = $zip->getFromName('word/document.xml');
        if ($document_xml === FALSE) {
            $zip->close();
            @unlink($temp_file);
            return FALSE;
        }

        $document_xml = $this->populate_surat_keterangan_sakit_template(
            $document_xml,
            $surat,
            $pegawai,
            $this->build_template_penandatangan($template_signer, $penandatangan)
        );

        $zip->addFromString('word/document.xml', $document_xml);
        $zip->close();

        return $temp_file;
    }

    private function output_surat_rekomendasi_docx($id)
    {
        $nip = $this->session->userdata('nip');
        $surat = $this->Pengajuan_surat_model->get_surat_rekomendasi_download_detail($id, $nip);

        if (empty($surat)) {
            show_404();
        }

        $pegawai = $this->Pegawai_model->get_by_nip($surat->nip);
        $template_path = $this->get_surat_rekomendasi_template_path($surat->jenis_surat);

        if (empty($pegawai) || empty($template_path) || !is_file($template_path)) {
            show_error('Template atau data surat tidak lengkap.', 500);
        }

        $generated_file = $this->generate_surat_rekomendasi_docx($surat, $pegawai, $template_path);

        if ($generated_file === FALSE || !is_file($generated_file)) {
            show_error('File Word surat rekomendasi tidak berhasil dibuat.', 500);
        }

        $filename = $this->build_surat_rekomendasi_filename($surat->jenis_surat, $surat->id);

        if (ob_get_length()) {
            ob_end_clean();
        }

        header('Content-Description: File Transfer');
        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($generated_file));
        header('Cache-Control: max-age=0');
        header('Pragma: public');

        readfile($generated_file);
        @unlink($generated_file);
        exit;
    }

    private function generate_surat_rekomendasi_docx($surat, $pegawai, $template_path)
    {
        $temp_base = tempnam(sys_get_temp_dir(), 'surat_rekom_');
        if ($temp_base === FALSE) {
            return FALSE;
        }

        @unlink($temp_base);
        $temp_file = $temp_base . '.docx';

        if (empty($template_path) || !is_file($template_path) || !copy($template_path, $temp_file)) {
            return FALSE;
        }

        $zip = new ZipArchive();
        if ($zip->open($temp_file) !== TRUE) {
            @unlink($temp_file);
            return FALSE;
        }

        $document_xml = $zip->getFromName('word/document.xml');
        if ($document_xml === FALSE) {
            $zip->close();
            @unlink($temp_file);
            return FALSE;
        }

        $document_xml = $this->populate_surat_rekomendasi_template($document_xml, $surat, $pegawai);

        $zip->addFromString('word/document.xml', $document_xml);
        $zip->close();

        return $temp_file;
    }

    private function populate_surat_rekomendasi_template($xml, $surat, $pegawai)
    {
        if (in_array($surat->jenis_surat, array('usulan_cuti_tahun'), TRUE)) {
            return $this->populate_surat_rekomendasi_cuti_tahunan_template($xml, $surat, $pegawai);
        }

        if (in_array($surat->jenis_surat, array('cuti_alasan_penting', 'usulan_alasan_penting'), TRUE)) {
            return $this->populate_surat_rekomendasi_cuti_alasan_penting_template($xml, $surat, $pegawai);
        }

        if ($surat->jenis_surat === 'usulan_cuti_luar_negeri') {
            return $this->populate_surat_rekomendasi_cuti_luar_negeri_template($xml, $surat, $pegawai);
        }

        if ($surat->jenis_surat === 'usulan_kenaikan_gaji_berkala') {
            return $this->populate_surat_rekomendasi_kgb_template($xml, $surat, $pegawai);
        }

        $xml = $this->replace_docx_paragraph_by_contains(
            $xml,
            'Tomohon,',
            $this->build_tanggal_surat_rekomendasi_template(!empty($surat->kadis_at) ? substr($surat->kadis_at, 0, 10) : substr($surat->updated_at, 0, 10))
        );

        $dom = new DOMDocument('1.0', 'UTF-8');
        $previous_state = libxml_use_internal_errors(TRUE);
        $loaded = $dom->loadXML($xml);
        libxml_clear_errors();
        libxml_use_internal_errors($previous_state);

        if (!$loaded) {
            return $xml;
        }

        $xpath = new DOMXPath($dom);
        $xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');

        foreach ($xpath->query('//w:tr') as $row) {
            $cells = array();
            foreach ($xpath->query('./w:tc', $row) as $cell) {
                $cells[] = $cell;
            }

            if (count($cells) < 2) {
                continue;
            }

            $label = $this->normalize_docx_cell_text($xpath, $cells[0]);
            $value_cell = $cells[count($cells) - 1];

            if ($this->docx_row_label_matches($label, 'Nomor')) {
                $this->rewrite_docx_cell_text($dom, $xpath, $value_cell, (string) $surat->nomor_surat);
                continue;
            }

            if ($this->docx_row_label_matches($label, 'Sifat')) {
                $this->rewrite_docx_cell_text($dom, $xpath, $value_cell, (string) $surat->sifat);
                continue;
            }

            if ($this->docx_row_label_matches($label, 'Lampiran')) {
                $this->rewrite_docx_cell_text($dom, $xpath, $value_cell, (string) $surat->lampiran);
                continue;
            }

            if ($this->docx_row_label_matches($label, 'Perihal')) {
                $this->rewrite_docx_cell_text($dom, $xpath, $value_cell, (string) $surat->perihal);
                continue;
            }

            if ($this->docx_row_label_matches($label, 'Nama')) {
                $this->rewrite_docx_cell_text($dom, $xpath, $value_cell, $this->get_surat_rekomendasi_template_nama($surat, $pegawai));
                continue;
            }

            if ($this->docx_row_label_matches($label, 'NIP')) {
                $this->rewrite_docx_cell_text($dom, $xpath, $value_cell, $this->format_nip_template($this->get_surat_rekomendasi_template_nip($surat, $pegawai)));
                continue;
            }

            if ($this->docx_row_label_matches($label, 'Pangkat / Golongan')) {
                $this->rewrite_docx_cell_text($dom, $xpath, $value_cell, $this->format_pangkat_template($pegawai->pangkat_terakhir));
                continue;
            }

            if ($this->docx_row_label_matches($label, 'Jabatan')) {
                $this->rewrite_docx_cell_text($dom, $xpath, $value_cell, $this->format_jabatan_template($pegawai->jabatan));
                continue;
            }
        }

        return $dom->saveXML();
    }

    private function populate_surat_rekomendasi_kgb_template($xml, $surat, $pegawai)
    {
        $xml = $this->replace_docx_paragraph_by_contains(
            $xml,
            'Tomohon,',
            $this->build_tanggal_surat_rekomendasi_template(!empty($surat->kadis_at) ? substr($surat->kadis_at, 0, 10) : substr($surat->updated_at, 0, 10))
        );

        $dom = new DOMDocument('1.0', 'UTF-8');
        $previous_state = libxml_use_internal_errors(TRUE);
        $loaded = $dom->loadXML($xml);
        libxml_clear_errors();
        libxml_use_internal_errors($previous_state);

        if (!$loaded) {
            return $xml;
        }

        $xpath = new DOMXPath($dom);
        $xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');

        $table_with_pemohon = NULL;
        $pemohon_row_index = 0;
        $template_no = $this->get_surat_rekomendasi_template_no($surat);
        $template_nama = $this->get_surat_rekomendasi_template_nama($surat, $pegawai);
        $template_nip = $this->format_nip_template($this->get_surat_rekomendasi_template_nip($surat, $pegawai));

        foreach ($xpath->query('//w:tr') as $row) {
            $cells = array();
            foreach ($xpath->query('./w:tc', $row) as $cell) {
                $cells[] = $cell;
            }

            if (count($cells) < 2) {
                continue;
            }

            $label = $this->normalize_docx_cell_text($xpath, $cells[0]);
            $value_cell = $cells[count($cells) - 1];

            if ($this->docx_row_label_matches($label, 'Nomor')) {
                $this->rewrite_docx_cell_text($dom, $xpath, $value_cell, (string) $surat->nomor_surat);
                continue;
            }

            if ($this->docx_row_label_matches($label, 'Sifat')) {
                $this->rewrite_docx_cell_text($dom, $xpath, $value_cell, (string) $surat->sifat);
                continue;
            }

            if ($this->docx_row_label_matches($label, 'Lampiran')) {
                $this->rewrite_docx_cell_text($dom, $xpath, $value_cell, (string) $surat->lampiran);
                continue;
            }

            if ($this->docx_row_label_matches($label, 'Perihal')) {
                $this->rewrite_docx_cell_text($dom, $xpath, $value_cell, (string) $surat->perihal);
                continue;
            }

            if (count($cells) < 3) {
                continue;
            }

            $cell_one = $this->normalize_docx_cell_text($xpath, $cells[0]);
            $cell_two = $this->normalize_docx_cell_text($xpath, $cells[1]);
            $cell_three = $this->normalize_docx_cell_text($xpath, $cells[2]);

            if ($table_with_pemohon === NULL && preg_match('/^NO\.?$/ui', $cell_one) && preg_match('/^NAMA$/ui', $cell_two) && preg_match('/^NIP\.?$/ui', $cell_three)) {
                $table_with_pemohon = $row->parentNode;
                $pemohon_row_index = 0;
                continue;
            }

            if ($table_with_pemohon !== NULL && $row->parentNode->isSameNode($table_with_pemohon)) {
                if ($pemohon_row_index === 0) {
                    $this->rewrite_docx_cell_text($dom, $xpath, $cells[0], $template_no);
                    $this->rewrite_docx_cell_text($dom, $xpath, $cells[1], $template_nama);
                    $this->rewrite_docx_cell_text($dom, $xpath, $cells[2], $template_nip);
                } else {
                    $this->rewrite_docx_cell_text($dom, $xpath, $cells[0], '');
                    $this->rewrite_docx_cell_text($dom, $xpath, $cells[1], '');
                    $this->rewrite_docx_cell_text($dom, $xpath, $cells[2], '');
                }

                $pemohon_row_index++;
            }
        }

        return $dom->saveXML();
    }

    private function populate_surat_rekomendasi_cuti_alasan_penting_template($xml, $surat, $pegawai)
    {
        $xml = $this->replace_docx_paragraph_by_contains(
            $xml,
            'Tomohon,',
            $this->build_tanggal_surat_rekomendasi_template(!empty($surat->kadis_at) ? substr($surat->kadis_at, 0, 10) : substr($surat->updated_at, 0, 10))
        );

        $xml = $this->replace_docx_paragraph_by_contains(
            $xml,
            'Untuk mengajukan permohonan pengambilan Hak Cuti Alasan Penting selama',
            $this->build_kalimat_cuti_alasan_penting($surat)
        );

        $dom = new DOMDocument('1.0', 'UTF-8');
        $previous_state = libxml_use_internal_errors(TRUE);
        $loaded = $dom->loadXML($xml);
        libxml_clear_errors();
        libxml_use_internal_errors($previous_state);

        if (!$loaded) {
            return $xml;
        }

        $xpath = new DOMXPath($dom);
        $xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');
        $after_rekomendasi = FALSE;

        foreach ($xpath->query('//w:body/*') as $node) {
            if ($node->localName === 'p') {
                $paragraph_text = $this->normalize_docx_node_text($xpath, $node);

                if (strpos($paragraph_text, 'Dengan ini memberikan REKOMENDASI kepada :') !== FALSE) {
                    $after_rekomendasi = TRUE;
                }

                continue;
            }

            if ($node->localName !== 'tbl') {
                continue;
            }

            foreach ($xpath->query('.//w:tr', $node) as $row) {
                $cells = array();

                foreach ($xpath->query('./w:tc', $row) as $cell) {
                    $cells[] = $cell;
                }

                if (count($cells) < 2) {
                    continue;
                }

                $label = $this->normalize_docx_cell_text($xpath, $cells[0]);
                $value_cell = $cells[count($cells) - 1];

                if (!$after_rekomendasi) {
                    if ($this->docx_row_label_matches($label, 'Nomor')) {
                        $this->rewrite_docx_cell_text($dom, $xpath, $value_cell, (string) $surat->nomor_surat);
                        continue;
                    }

                    if ($this->docx_row_label_matches($label, 'Sifat')) {
                        $this->rewrite_docx_cell_text($dom, $xpath, $value_cell, (string) $surat->sifat);
                        continue;
                    }

                    if ($this->docx_row_label_matches($label, 'Lampiran')) {
                        $this->rewrite_docx_cell_text($dom, $xpath, $value_cell, (string) $surat->lampiran);
                        continue;
                    }

                    if ($this->docx_row_label_matches($label, 'Perihal')) {
                        $this->rewrite_docx_cell_text($dom, $xpath, $value_cell, (string) $surat->perihal);
                        continue;
                    }

                    continue;
                }

                if ($this->docx_row_label_matches($label, 'Nama')) {
                    $this->rewrite_docx_cell_text($dom, $xpath, $value_cell, !empty($pegawai->nama) ? $pegawai->nama : '-');
                    continue;
                }

                if ($this->docx_row_label_matches($label, 'NIP')) {
                    $this->rewrite_docx_cell_text($dom, $xpath, $value_cell, $this->format_nip_template(!empty($pegawai->nip) ? $pegawai->nip : '-'));
                    continue;
                }

                if ($this->docx_row_label_matches($label, 'Pangkat / Golongan')) {
                    $this->rewrite_docx_cell_text($dom, $xpath, $value_cell, $this->format_pangkat_template(!empty($pegawai->pangkat_terakhir) ? $pegawai->pangkat_terakhir : '-'));
                    continue;
                }

                if ($this->docx_row_label_matches($label, 'Jabatan')) {
                    $this->rewrite_docx_cell_text($dom, $xpath, $value_cell, $this->format_jabatan_template(!empty($pegawai->jabatan) ? $pegawai->jabatan : '-'));
                    continue;
                }
            }
        }

        return $dom->saveXML();
    }

    private function populate_surat_rekomendasi_cuti_tahunan_template($xml, $surat, $pegawai)
    {
        $xml = $this->replace_docx_paragraph_by_contains(
            $xml,
            'Tomohon,',
            $this->build_tanggal_surat_rekomendasi_template(!empty($surat->kadis_at) ? substr($surat->kadis_at, 0, 10) : substr($surat->updated_at, 0, 10))
        );

        $xml = $this->replace_docx_paragraph_by_contains(
            $xml,
            'Untuk mengajukan permohonan pengambilan Cuti Tahunan terhitung mulai tanggal',
            $this->build_kalimat_cuti_tahunan($surat)
        );

        $dom = new DOMDocument('1.0', 'UTF-8');
        $previous_state = libxml_use_internal_errors(TRUE);
        $loaded = $dom->loadXML($xml);
        libxml_clear_errors();
        libxml_use_internal_errors($previous_state);

        if (!$loaded) {
            return $xml;
        }

        $xpath = new DOMXPath($dom);
        $xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');
        $after_rekomendasi = FALSE;

        foreach ($xpath->query('//w:body/*') as $node) {
            if ($node->localName === 'p') {
                $paragraph_text = $this->normalize_docx_node_text($xpath, $node);

                if (strpos($paragraph_text, 'Dengan ini memberikan REKOMENDASI kepada :') !== FALSE) {
                    $after_rekomendasi = TRUE;
                }

                continue;
            }

            if ($node->localName !== 'tbl') {
                continue;
            }

            foreach ($xpath->query('.//w:tr', $node) as $row) {
                $cells = array();

                foreach ($xpath->query('./w:tc', $row) as $cell) {
                    $cells[] = $cell;
                }

                if (count($cells) < 2) {
                    continue;
                }

                $label = $this->normalize_docx_cell_text($xpath, $cells[0]);
                $value_cell = $cells[count($cells) - 1];

                if (!$after_rekomendasi) {
                    if ($this->docx_row_label_matches($label, 'Nomor')) {
                        $this->rewrite_docx_cell_text($dom, $xpath, $value_cell, (string) $surat->nomor_surat);
                        continue;
                    }

                    if ($this->docx_row_label_matches($label, 'Sifat')) {
                        $this->rewrite_docx_cell_text($dom, $xpath, $value_cell, (string) $surat->sifat);
                        continue;
                    }

                    if ($this->docx_row_label_matches($label, 'Lampiran')) {
                        $this->rewrite_docx_cell_text($dom, $xpath, $value_cell, (string) $surat->lampiran);
                        continue;
                    }

                    if ($this->docx_row_label_matches($label, 'Perihal')) {
                        $this->rewrite_docx_cell_text($dom, $xpath, $value_cell, (string) $surat->perihal);
                        continue;
                    }

                    continue;
                }

                if ($this->docx_row_label_matches($label, 'Nama')) {
                    $this->rewrite_docx_cell_text($dom, $xpath, $value_cell, !empty($pegawai->nama) ? $pegawai->nama : '-');
                    continue;
                }

                if ($this->docx_row_label_matches($label, 'NIP')) {
                    $this->rewrite_docx_cell_text($dom, $xpath, $value_cell, $this->format_nip_template(!empty($pegawai->nip) ? $pegawai->nip : '-'));
                    continue;
                }

                if ($this->docx_row_label_matches($label, 'Pangkat / Golongan')) {
                    $this->rewrite_docx_cell_text($dom, $xpath, $value_cell, $this->format_pangkat_template(!empty($pegawai->pangkat_terakhir) ? $pegawai->pangkat_terakhir : '-'));
                    continue;
                }

                if ($this->docx_row_label_matches($label, 'Jabatan')) {
                    $this->rewrite_docx_cell_text($dom, $xpath, $value_cell, $this->format_jabatan_template(!empty($pegawai->jabatan) ? $pegawai->jabatan : '-'));
                    continue;
                }
            }
        }

        return $dom->saveXML();
    }

    private function populate_surat_rekomendasi_cuti_luar_negeri_template($xml, $surat, $pegawai)
    {
        $xml = $this->replace_docx_paragraph_by_contains(
            $xml,
            'Tomohon,',
            $this->build_tanggal_surat_rekomendasi_template(!empty($surat->kadis_at) ? substr($surat->kadis_at, 0, 10) : substr($surat->updated_at, 0, 10))
        );

        $xml = $this->replace_docx_paragraph_by_contains(
            $xml,
            'Dengan ini mengajukan Permohonan Cuti Tahunan Ke Luar Negeri yaitu ke',
            $this->build_kalimat_cuti_luar_negeri($surat)
        );

        $dom = new DOMDocument('1.0', 'UTF-8');
        $previous_state = libxml_use_internal_errors(TRUE);
        $loaded = $dom->loadXML($xml);
        libxml_clear_errors();
        libxml_use_internal_errors($previous_state);

        if (!$loaded) {
            return $xml;
        }

        $xpath = new DOMXPath($dom);
        $xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');
        $identity_table_armed = FALSE;
        $identity_table_processed = FALSE;

        foreach ($xpath->query('//w:body/*') as $node) {
            if ($node->localName === 'p') {
                $paragraph_text = $this->normalize_docx_node_text($xpath, $node);

                if (strpos($paragraph_text, 'Saya yang bertanda tangan dibawah ini') !== FALSE) {
                    $identity_table_armed = TRUE;
                }

                continue;
            }

            if ($node->localName !== 'tbl') {
                continue;
            }

            $identity_table_matched = FALSE;

            foreach ($xpath->query('.//w:tr', $node) as $row) {
                $cells = array();

                foreach ($xpath->query('./w:tc', $row) as $cell) {
                    $cells[] = $cell;
                }

                if (count($cells) < 2) {
                    continue;
                }

                $label = $this->normalize_docx_cell_text($xpath, $cells[0]);
                $value_cell = $cells[count($cells) - 1];

                if ($this->docx_row_label_matches($label, 'Nomor')) {
                    $this->rewrite_docx_cell_text($dom, $xpath, $value_cell, (string) $surat->nomor_surat);
                    continue;
                }

                if ($this->docx_row_label_matches($label, 'Sifat')) {
                    $this->rewrite_docx_cell_text($dom, $xpath, $value_cell, (string) $surat->sifat);
                    continue;
                }

                if ($this->docx_row_label_matches($label, 'Lampiran')) {
                    $this->rewrite_docx_cell_text($dom, $xpath, $value_cell, (string) $surat->lampiran);
                    continue;
                }

                if ($this->docx_row_label_matches($label, 'Perihal')) {
                    $this->rewrite_docx_cell_text($dom, $xpath, $value_cell, (string) $surat->perihal);
                    continue;
                }

                if (!$identity_table_armed || $identity_table_processed) {
                    continue;
                }

                if ($this->docx_row_label_matches($label, 'Nama')) {
                    $this->rewrite_docx_cell_text($dom, $xpath, $value_cell, !empty($pegawai->nama) ? $pegawai->nama : '-');
                    $identity_table_matched = TRUE;
                    continue;
                }

                if ($this->docx_row_label_matches($label, 'NIP')) {
                    $this->rewrite_docx_cell_text($dom, $xpath, $value_cell, $this->format_nip_template(!empty($pegawai->nip) ? $pegawai->nip : '-'));
                    $identity_table_matched = TRUE;
                    continue;
                }

                if ($this->docx_row_label_matches($label, 'Pangkat / Golongan')) {
                    $this->rewrite_docx_cell_text($dom, $xpath, $value_cell, $this->format_pangkat_template(!empty($pegawai->pangkat_terakhir) ? $pegawai->pangkat_terakhir : '-'));
                    $identity_table_matched = TRUE;
                    continue;
                }

                if ($this->docx_row_label_matches($label, 'Jabatan')) {
                    $this->rewrite_docx_cell_text($dom, $xpath, $value_cell, $this->format_jabatan_template(!empty($pegawai->jabatan) ? $pegawai->jabatan : '-'));
                    $identity_table_matched = TRUE;
                    continue;
                }
            }

            if ($identity_table_armed && $identity_table_matched) {
                $identity_table_processed = TRUE;
                $identity_table_armed = FALSE;
            }
        }

        $signer = $this->Pegawai_model->get_by_nip(!empty($surat->kadis_nip) ? $surat->kadis_nip : '196708271994032006');
        $replace_pemohon_identity = FALSE;
        $replace_kadis_block = FALSE;
        $pemohon_name_done = FALSE;
        $pemohon_nip_done = FALSE;
        $kadis_name_done = FALSE;
        $kadis_rank_done = FALSE;
        $kadis_nip_done = FALSE;

        foreach ($xpath->query('//w:p') as $paragraph) {
            $paragraph_text = $this->normalize_docx_paragraph_text($xpath, $paragraph);

            if ($paragraph_text === '') {
                continue;
            }

            if (strpos($paragraph_text, 'Yang bermohon') !== FALSE) {
                $replace_pemohon_identity = TRUE;
                continue;
            }

            if ($replace_pemohon_identity && !$pemohon_name_done && !preg_match('/^NIP\.?/ui', $paragraph_text)) {
                $this->rewrite_docx_paragraph_text($dom, $xpath, $paragraph, $pegawai->nama);
                $pemohon_name_done = TRUE;
                continue;
            }

            if ($replace_pemohon_identity && $pemohon_name_done && !$pemohon_nip_done && preg_match('/^NIP\.?/ui', $paragraph_text)) {
                $this->rewrite_docx_paragraph_text($dom, $xpath, $paragraph, 'NIP. ' . $this->format_nip_template($pegawai->nip));
                $pemohon_nip_done = TRUE;
                $replace_pemohon_identity = FALSE;
                continue;
            }

            if (strpos($paragraph_text, 'Kepala Dinas Penanaman Modal dan Pelayanan Terpadu Satu Pintu Daerah') !== FALSE) {
                $replace_kadis_block = TRUE;
                $kadis_name_done = FALSE;
                $kadis_rank_done = FALSE;
                $kadis_nip_done = FALSE;
                continue;
            }

            if ($replace_kadis_block && !$kadis_name_done) {
                $this->rewrite_docx_paragraph_text($dom, $xpath, $paragraph, !empty($signer) ? $signer->nama : '-');
                $kadis_name_done = TRUE;
                continue;
            }

            if ($replace_kadis_block && $kadis_name_done && !$kadis_rank_done) {
                $this->rewrite_docx_paragraph_text($dom, $xpath, $paragraph, !empty($signer) ? $this->format_pangkat_template($signer->pangkat_terakhir) : '-');
                $kadis_rank_done = TRUE;
                continue;
            }

            if ($replace_kadis_block && $kadis_name_done && $kadis_rank_done && !$kadis_nip_done && preg_match('/^NIP\.?/ui', $paragraph_text)) {
                $this->rewrite_docx_paragraph_text($dom, $xpath, $paragraph, 'NIP. ' . (!empty($signer) ? $this->format_nip_template($signer->nip) : '-'));
                $kadis_nip_done = TRUE;
                $replace_kadis_block = FALSE;
                continue;
            }
        }

        return $dom->saveXML();
    }

    private function normalize_docx_cell_text(DOMXPath $xpath, DOMElement $cell)
    {
        $text = '';

        foreach ($xpath->query('.//w:t', $cell) as $text_node) {
            $text .= $text_node->textContent;
        }

        $text = html_entity_decode($text, ENT_QUOTES | ENT_XML1, 'UTF-8');
        $text = preg_replace('/\s+/u', ' ', trim($text));

        return trim((string) $text);
    }

    private function docx_row_label_matches($text, $label)
    {
        $patterns = array(
            'Nomor' => '/^Nomor\b/ui',
            'Sifat' => '/^Sifat\b/ui',
            'Lampiran' => '/^Lampiran\b/ui',
            'Perihal' => '/^Perihal\b/ui',
            'Nama' => '/^Nama\b/ui',
            'NIP' => '/^NIP\.?/ui',
            'Pangkat / Golongan' => '/^Pangkat\s*\/\s*Golongan\b/ui',
            'Jabatan' => '/^Jabatan\b/ui',
        );

        if (!isset($patterns[$label])) {
            return FALSE;
        }

        return (bool) preg_match($patterns[$label], $text);
    }

    private function rewrite_docx_cell_text(DOMDocument $dom, DOMXPath $xpath, DOMElement $cell, $replacement)
    {
        $paragraph = $xpath->query('./w:p', $cell)->item(0);

        if ($paragraph === NULL) {
            return;
        }

        $this->rewrite_docx_paragraph_text($dom, $xpath, $paragraph, $replacement);
    }

    private function get_surat_rekomendasi_template_path($jenis_surat)
    {
        if ($this->db->table_exists('template_surat')) {
            $template = $this->Master_surat_model->get_by_code($this->get_surat_rekomendasi_template_code($jenis_surat));

            if (!empty($template) && !empty($template->file_path)) {
                $uploaded_template_path = FCPATH . ltrim(str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $template->file_path), DIRECTORY_SEPARATOR);

                if (is_file($uploaded_template_path)) {
                    return $uploaded_template_path;
                }
            }
        }

        $default_template_path = $this->get_default_surat_rekomendasi_template_path($jenis_surat);

        if (!empty($default_template_path) && is_file($default_template_path)) {
            return $default_template_path;
        }

        return NULL;
    }

    private function get_surat_rekomendasi_template_code($jenis_surat)
    {
        $map = array(
            'usulan_kenaikan_pangkat' => self::KODE_TEMPLATE_SURAT_REKOMENDASI_KENAIKAN_PANGKAT,
            'usulan_kenaikan_gaji_berkala' => self::KODE_TEMPLATE_SURAT_REKOMENDASI_KENAIKAN_GAJI_BERKALA,
            'usulan_cuti_tahun' => self::KODE_TEMPLATE_SURAT_REKOMENDASI_CUTI_TAHUNAN,
            'usulan_alasan_penting' => self::KODE_TEMPLATE_SURAT_REKOMENDASI_CUTI_ALASAN_PENTING,
            'cuti_alasan_penting' => self::KODE_TEMPLATE_SURAT_REKOMENDASI_CUTI_ALASAN_PENTING,
            'usulan_cuti_luar_negeri' => self::KODE_TEMPLATE_SURAT_REKOMENDASI_CUTI_LUAR_NEGERI,
        );

        return isset($map[$jenis_surat]) ? $map[$jenis_surat] : self::KODE_TEMPLATE_SURAT_REKOMENDASI_KENAIKAN_PANGKAT;
    }

    private function get_default_surat_rekomendasi_template_path($jenis_surat)
    {
        $map = array(
            'usulan_kenaikan_pangkat' => self::TEMPLATE_SURAT_REKOMENDASI_KENAIKAN_PANGKAT,
            'usulan_kenaikan_gaji_berkala' => self::TEMPLATE_SURAT_REKOMENDASI_KENAIKAN_GAJI_BERKALA,
            'usulan_cuti_tahun' => self::TEMPLATE_SURAT_REKOMENDASI_CUTI_TAHUNAN,
            'usulan_alasan_penting' => self::TEMPLATE_SURAT_REKOMENDASI_CUTI_ALASAN_PENTING,
            'cuti_alasan_penting' => self::TEMPLATE_SURAT_REKOMENDASI_CUTI_ALASAN_PENTING,
            'usulan_cuti_luar_negeri' => self::TEMPLATE_SURAT_REKOMENDASI_CUTI_LUAR_NEGERI,
        );

        return isset($map[$jenis_surat]) ? $map[$jenis_surat] : self::TEMPLATE_SURAT_REKOMENDASI_KENAIKAN_PANGKAT;
    }

    private function build_surat_rekomendasi_filename($jenis_surat, $id)
    {
        $map = array(
            'usulan_kenaikan_pangkat' => 'surat-rekomendasi-kenaikan-pangkat-',
            'usulan_kenaikan_gaji_berkala' => 'surat-usulan-kenaikan-gaji-berkala-',
            'usulan_cuti_tahun' => 'surat-usulan-cuti-tahunan-',
            'usulan_alasan_penting' => 'surat-rekomendasi-cuti-alasan-penting-',
            'cuti_alasan_penting' => 'surat-rekomendasi-cuti-alasan-penting-',
            'usulan_cuti_luar_negeri' => 'surat-usulan-cuti-luar-negeri-',
        );

        $prefix = isset($map[$jenis_surat]) ? $map[$jenis_surat] : 'surat-rekomendasi-';

        return $prefix . (int) $id . '.docx';
    }

    private function get_surat_rekomendasi_template_no($surat)
    {
        $template_no = isset($surat->template_no) ? trim((string) $surat->template_no) : '';

        return $template_no !== '' ? $template_no : '1.';
    }

    private function get_surat_rekomendasi_template_nama($surat, $pegawai)
    {
        $template_nama = isset($surat->template_nama) ? trim((string) $surat->template_nama) : '';

        if ($template_nama !== '') {
            return $template_nama;
        }

        return !empty($pegawai->nama) ? $pegawai->nama : '-';
    }

    private function get_surat_rekomendasi_template_nip($surat, $pegawai)
    {
        $template_nip = isset($surat->template_nip) ? trim((string) $surat->template_nip) : '';

        if ($template_nip !== '') {
            return $template_nip;
        }

        return !empty($pegawai->nip) ? $pegawai->nip : '-';
    }

    private function render_rekomendasi_placeholder($title)
    {
        $data['title'] = $title;
        $data['placeholder_text'] = 'blum beking';

        $this->load->view('templates/header_pegawai', $data);
        $this->load->view('pengajuan_surat/placeholder', $data);
        $this->load->view('templates/footer_pegawai');
    }

    private function is_valid_date($date)
    {
        $date_time = DateTime::createFromFormat('Y-m-d', $date);

        return $date_time && $date_time->format('Y-m-d') === $date;
    }

    private function format_tanggal_indonesia($date)
    {
        $date_time = DateTime::createFromFormat('Y-m-d', $date);

        if (!$date_time) {
            return '-';
        }

        $bulan = array(
            1 => 'Januari',
            'Februari',
            'Maret',
            'April',
            'Mei',
            'Juni',
            'Juli',
            'Agustus',
            'September',
            'Oktober',
            'November',
            'Desember',
        );

        return $date_time->format('d') . ' ' . $bulan[(int) $date_time->format('n')] . ' ' . $date_time->format('Y');
    }

    private function build_kalimat_surat($jenis, $tanggal_izin, $alasan)
    {
        $prefix = 'Tidak melakukan sidik jari pada tanggal ';

        if ($jenis === 'pagi') {
            $prefix = 'Tidak melakukan sidik jari pagi pada tanggal ';
        } elseif ($jenis === 'sore') {
            $prefix = 'Tidak melakukan sidik jari sore pada tanggal ';
        }

        return $prefix . $this->format_tanggal_indonesia($tanggal_izin) . ' karena ' . rtrim($alasan, " .") . '.';
    }

    private function build_nomor_surat($tanggal_surat)
    {
        $date_time = DateTime::createFromFormat('Y-m-d', $tanggal_surat);

        if (!$date_time) {
            return '/Sket/DPMPTSPD/III/2026';
        }

        $bulan_romawi = array(
            1 => 'I',
            'II',
            'III',
            'IV',
            'V',
            'VI',
            'VII',
            'VIII',
            'IX',
            'X',
            'XI',
            'XII',
        );

        return '/Sket/DPMPTSPD/' . $bulan_romawi[(int) $date_time->format('n')] . '/' . $date_time->format('Y');
    }

    private function build_tanggal_ttd_template($tanggal_surat)
    {
        $date_time = DateTime::createFromFormat('Y-m-d', $tanggal_surat);

        if (!$date_time) {
            return 'Tomohon,      Maret 2026';
        }

        $bulan = array(
            1 => 'Januari',
            'Februari',
            'Maret',
            'April',
            'Mei',
            'Juni',
            'Juli',
            'Agustus',
            'September',
            'Oktober',
            'November',
            'Desember',
        );

        return 'Tomohon,      ' . $bulan[(int) $date_time->format('n')] . ' ' . $date_time->format('Y');
    }

    private function build_tanggal_surat_rekomendasi_template($tanggal_surat)
    {
        $date_time = DateTime::createFromFormat('Y-m-d', $tanggal_surat);

        if (!$date_time) {
            return 'Tomohon, 12 Januari 2026';
        }

        $bulan = array(
            1 => 'Januari',
            'Februari',
            'Maret',
            'April',
            'Mei',
            'Juni',
            'Juli',
            'Agustus',
            'September',
            'Oktober',
            'November',
            'Desember',
        );

        return 'Tomohon, ' . $date_time->format('d') . ' ' . $bulan[(int) $date_time->format('n')] . ' ' . $date_time->format('Y');
    }

    private function format_nip_template($nip)
    {
        $digits = preg_replace('/\D+/', '', (string) $nip);

        if (strlen($digits) === 18) {
            return substr($digits, 0, 8) . ' ' . substr($digits, 8, 6) . ' ' . substr($digits, 14, 1) . ' ' . substr($digits, 15, 3);
        }

        return (string) $nip;
    }

    private function format_pangkat_template($pangkat)
    {
        $pangkat = trim((string) $pangkat);

        if ($pangkat === '') {
            return '-';
        }

        $pangkat = str_replace(array('Tkt I', 'Tkt II'), array('Tingkat I', 'Tingkat II'), $pangkat);
        $pangkat = preg_replace('/\s*\/\s*/', ', ', $pangkat, 1);
        $pangkat = preg_replace('/\b(IV|V?I{1,3})([abcdeABCDE])\b/u', '$1/$2', $pangkat);

        return $pangkat;
    }

    private function format_jabatan_template($jabatan)
    {
        $jabatan = trim((string) $jabatan);

        if ($jabatan === '') {
            return '-';
        }

        return str_replace('Perencanaan Kepegawaian', 'Perencanaan, Kepegawaian', $jabatan);
    }

    private function replace_docx_paragraph_by_contains($xml, $needle, $replacement)
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $previous_state = libxml_use_internal_errors(TRUE);
        $loaded = $dom->loadXML($xml);
        libxml_clear_errors();
        libxml_use_internal_errors($previous_state);

        if (!$loaded) {
            return $xml;
        }

        $xpath = new DOMXPath($dom);
        $xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');

        foreach ($xpath->query('//w:p') as $paragraph) {
            $paragraph_text = '';

            foreach ($xpath->query('.//w:t', $paragraph) as $text_node) {
                $paragraph_text .= $text_node->textContent;
            }

            $paragraph_text = preg_replace('/\s+/u', ' ', trim($paragraph_text));
            $needle_normalized = preg_replace('/\s+/u', ' ', trim($needle));

            if (strpos($paragraph_text, $needle_normalized) === FALSE) {
                continue;
            }

            $this->rewrite_docx_paragraph_text($dom, $xpath, $paragraph, $replacement);
        }

        return $dom->saveXML();
    }

    private function rewrite_docx_paragraph_text(DOMDocument $dom, DOMXPath $xpath, DOMElement $paragraph, $replacement)
    {
        $sample_run = NULL;

        foreach ($xpath->query('./w:r', $paragraph) as $run) {
            if ($xpath->query('.//w:t', $run)->length > 0) {
                $sample_run = $run;
                break;
            }
        }

        if ($sample_run === NULL) {
            return;
        }

        for ($i = $paragraph->childNodes->length - 1; $i >= 0; $i--) {
            $child = $paragraph->childNodes->item($i);

            if ($child->nodeType === XML_ELEMENT_NODE && $child->localName === 'pPr') {
                continue;
            }

            $paragraph->removeChild($child);
        }

        $run = $dom->createElementNS('http://schemas.openxmlformats.org/wordprocessingml/2006/main', 'w:r');
        $run_properties = $xpath->query('./w:rPr', $sample_run)->item(0);

        if ($run_properties !== NULL) {
            $run->appendChild($run_properties->cloneNode(TRUE));
        }

        $text = $dom->createElementNS('http://schemas.openxmlformats.org/wordprocessingml/2006/main', 'w:t');
        $text->setAttributeNS('http://www.w3.org/XML/1998/namespace', 'xml:space', 'preserve');
        $text->appendChild($dom->createTextNode($replacement));
        $run->appendChild($text);
        $paragraph->appendChild($run);
    }

    private function normalize_docx_node_text(DOMXPath $xpath, DOMElement $node)
    {
        $text = '';

        foreach ($xpath->query('.//w:t', $node) as $text_node) {
            $text .= $text_node->textContent;
        }

        $text = html_entity_decode($text, ENT_QUOTES | ENT_XML1, 'UTF-8');
        $text = preg_replace('/\s+/u', ' ', trim($text));

        return trim((string) $text);
    }

    private function build_kalimat_cuti_alasan_penting($surat)
    {
        $tanggal_mulai = !empty($surat->tanggal_mulai) ? $this->format_tanggal_indonesia($surat->tanggal_mulai) : '-';
        $tanggal_selesai = !empty($surat->tanggal_selesai) ? $this->format_tanggal_indonesia($surat->tanggal_selesai) : '-';
        $keterangan = trim((string) $surat->keterangan);
        $hari = $this->get_cuti_alasan_penting_hari($surat);
        $hari_text = $this->spell_number_indonesia($hari);

        if ($keterangan === '') {
            $keterangan = '-';
        }

        return 'Untuk mengajukan permohonan pengambilan Hak Cuti Alasan Penting selama ' . $hari . ' (' . $hari_text . ') hari karena ' . rtrim($keterangan, " .,") . ', terhitung mulai tanggal ' . $tanggal_mulai . ' sampai dengan ' . $tanggal_selesai . '.';
    }

    private function build_kalimat_cuti_tahunan($surat)
    {
        $tanggal_mulai = !empty($surat->tanggal_mulai) ? $this->format_tanggal_indonesia($surat->tanggal_mulai) : '-';
        $tanggal_selesai = !empty($surat->tanggal_selesai) ? $this->format_tanggal_indonesia($surat->tanggal_selesai) : '-';

        return 'Untuk mengajukan permohonan pengambilan Cuti Tahunan terhitung mulai tanggal ' . $tanggal_mulai . ' sampai dengan ' . $tanggal_selesai . '.';
    }

    private function build_kalimat_cuti_luar_negeri($surat)
    {
        $tanggal_mulai = !empty($surat->tanggal_mulai) ? $this->format_tanggal_indonesia($surat->tanggal_mulai) : '-';
        $tanggal_selesai = !empty($surat->tanggal_selesai) ? $this->format_tanggal_indonesia($surat->tanggal_selesai) : '-';
        $negara_tujuan = trim((string) $surat->negara_tujuan);
        $keterangan = trim((string) $surat->keterangan);
        $hari = $this->get_cuti_luar_negeri_hari($surat);
        $hari_text = $this->spell_number_indonesia($hari);

        if ($negara_tujuan === '') {
            $negara_tujuan = '-';
        }

        if ($keterangan === '') {
            $keterangan = '-';
        }

        return 'Dengan ini mengajukan Permohonan Cuti Tahunan Ke Luar Negeri yaitu ke ' . $negara_tujuan . ' selama ' . $hari . ' (' . $hari_text . ') hari kerja terhitung mulai tanggal ' . $tanggal_mulai . ' sampai dengan tanggal ' . $tanggal_selesai . ', karena ' . rtrim($keterangan, " .,") . '.';
    }

    private function get_cuti_alasan_penting_hari($surat)
    {
        if (empty($surat->tanggal_mulai) || empty($surat->tanggal_selesai)) {
            return 1;
        }

        $start = DateTime::createFromFormat('Y-m-d', $surat->tanggal_mulai);
        $end = DateTime::createFromFormat('Y-m-d', $surat->tanggal_selesai);

        if (!$start || !$end) {
            return 1;
        }

        $days = (int) $start->diff($end)->days;

        return $days > 0 ? $days : 1;
    }

    private function get_cuti_luar_negeri_hari($surat)
    {
        if (empty($surat->tanggal_mulai) || empty($surat->tanggal_selesai)) {
            return 1;
        }

        $start = DateTime::createFromFormat('Y-m-d', $surat->tanggal_mulai);
        $end = DateTime::createFromFormat('Y-m-d', $surat->tanggal_selesai);

        if (!$start || !$end) {
            return 1;
        }

        $days = (int) $start->diff($end)->days + 1;

        return $days > 0 ? $days : 1;
    }

    private function spell_number_indonesia($number)
    {
        $number = (int) $number;

        if ($number < 0) {
            $number = 0;
        }

        $words = array(
            0 => 'nol',
            1 => 'satu',
            2 => 'dua',
            3 => 'tiga',
            4 => 'empat',
            5 => 'lima',
            6 => 'enam',
            7 => 'tujuh',
            8 => 'delapan',
            9 => 'sembilan',
            10 => 'sepuluh',
            11 => 'sebelas',
        );

        if ($number < 12) {
            return $words[$number];
        }

        if ($number < 20) {
            return $this->spell_number_indonesia($number - 10) . ' belas';
        }

        if ($number < 100) {
            $puluh = trim($this->spell_number_indonesia((int) ($number / 10)) . ' puluh');
            $sisa = $number % 10;

            return $sisa > 0 ? trim($puluh . ' ' . $this->spell_number_indonesia($sisa)) : $puluh;
        }

        if ($number < 200) {
            $sisa = $number - 100;

            return $sisa > 0 ? trim('seratus ' . $this->spell_number_indonesia($sisa)) : 'seratus';
        }

        if ($number < 1000) {
            $ratus = trim($this->spell_number_indonesia((int) ($number / 100)) . ' ratus');
            $sisa = $number % 100;

            return $sisa > 0 ? trim($ratus . ' ' . $this->spell_number_indonesia($sisa)) : $ratus;
        }

        if ($number < 2000) {
            $sisa = $number - 1000;

            return $sisa > 0 ? trim('seribu ' . $this->spell_number_indonesia($sisa)) : 'seribu';
        }

        if ($number < 1000000) {
            $ribu = trim($this->spell_number_indonesia((int) ($number / 1000)) . ' ribu');
            $sisa = $number % 1000;

            return $sisa > 0 ? trim($ribu . ' ' . $this->spell_number_indonesia($sisa)) : $ribu;
        }

        return (string) $number;
    }

    private function get_template_assets()
    {
        $default_header_lines = array(
            'PEMERINTAH KOTA TOMOHON',
            'DINAS PENANAMAN MODAL DAN',
            'PELAYANAN TERPADU SATU PINTU DAERAH',
            'Jalan Slanag Kelurahan Kolongan Satu Kecamatan Tomohon Tengah 95441',
            'Email : dpmptsp@tomohon.go.id Website : https://dpmptsp.tomohon.go.id',
        );

        $template_path = $this->get_surat_sakit_template_path();

        if (empty($template_path) || !is_file($template_path)) {
            return array(
                'header_lines' => $default_header_lines,
                'logo_data_uri' => NULL,
                'template_path' => NULL,
                'template_found' => FALSE,
            );
        }

        $zip = new ZipArchive();
        if ($zip->open($template_path) !== TRUE) {
            return array(
                'header_lines' => $default_header_lines,
                'logo_data_uri' => NULL,
                'signer' => array(),
                'template_path' => NULL,
                'template_found' => FALSE,
            );
        }

        $xml = $zip->getFromName('word/document.xml');
        $logo_data_uri = NULL;

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = $zip->getNameIndex($i);

            if (strpos($name, 'word/media/') !== 0) {
                continue;
            }

            $image_binary = $zip->getFromName($name);
            if ($image_binary === FALSE) {
                continue;
            }

            $image_info = @getimagesizefromstring($image_binary);
            if (empty($image_info['mime'])) {
                continue;
            }

            $logo_data_uri = 'data:' . $image_info['mime'] . ';base64,' . base64_encode($image_binary);
            break;
        }

        $zip->close();

        if ($xml === FALSE) {
            return array(
                'header_lines' => $default_header_lines,
                'logo_data_uri' => $logo_data_uri,
                'signer' => array(),
                'template_path' => $template_path,
                'template_found' => FALSE,
            );
        }

        $xml_text = str_replace(array('</w:p>', '</w:tr>'), array("\n", "\n"), $xml);
        $text = preg_replace('/<[^>]+>/', '', $xml_text);
        $text = html_entity_decode($text, ENT_QUOTES | ENT_XML1, 'UTF-8');
        $raw_lines = preg_split('/\R+/', $text);
        $lines = array();

        foreach ($raw_lines as $line) {
            $line = trim(preg_replace('/^-?\d+/', '', trim($line)));

            if ($line === '' || in_array($line, $lines, TRUE)) {
                continue;
            }

            if ($line === 'SURAT KETERANGAN') {
                break;
            }

            $lines[] = $line;

            if (count($lines) === 5) {
                break;
            }
        }

        if (count($lines) < 3) {
            $lines = $default_header_lines;
        }

        return array(
            'header_lines' => $lines,
            'logo_data_uri' => $logo_data_uri,
            'signer' => $this->extract_template_signer_from_xml($xml),
            'template_path' => $template_path,
            'template_found' => TRUE,
        );
    }

    private function get_surat_sakit_template_path()
    {
        if ($this->db->table_exists('template_surat')) {
            $template = $this->Master_surat_model->get_by_code(self::KODE_TEMPLATE_SURAT_SAKIT);

            if (!empty($template->file_path)) {
                $uploaded_template_path = FCPATH . ltrim(str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $template->file_path), DIRECTORY_SEPARATOR);

                if (is_file($uploaded_template_path)) {
                    return $uploaded_template_path;
                }
            }
        }

        if (is_file(self::TEMPLATE_SURAT_SAKIT)) {
            return self::TEMPLATE_SURAT_SAKIT;
        }

        return NULL;
    }

    private function extract_template_signer_from_xml($xml)
    {
        $signer = array(
            'nama' => '',
            'nip' => '',
            'pangkat_terakhir' => '',
            'jabatan' => '',
        );

        $dom = new DOMDocument('1.0', 'UTF-8');
        $previous_state = libxml_use_internal_errors(TRUE);
        $loaded = $dom->loadXML($xml);
        libxml_clear_errors();
        libxml_use_internal_errors($previous_state);

        if (!$loaded) {
            return $signer;
        }

        $xpath = new DOMXPath($dom);
        $xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');
        $capture = FALSE;

        foreach ($xpath->query('//w:p') as $paragraph) {
            $paragraph_text = $this->normalize_docx_paragraph_text($xpath, $paragraph);

            if ($paragraph_text === '') {
                continue;
            }

            if (strpos($paragraph_text, 'Yang bertanda tangan dibawah ini') !== FALSE) {
                $capture = TRUE;
                continue;
            }

            if (!$capture) {
                continue;
            }

            if (strpos($paragraph_text, 'Menerangkan') !== FALSE) {
                break;
            }

            if ($signer['nama'] === '' && preg_match('/^Nama\b/ui', $paragraph_text)) {
                $signer['nama'] = $this->extract_template_field_value($paragraph_text);
                continue;
            }

            if ($signer['nip'] === '' && preg_match('/^NIP\.?/ui', $paragraph_text)) {
                $signer['nip'] = $this->extract_template_field_value($paragraph_text);
                continue;
            }

            if ($signer['pangkat_terakhir'] === '' && preg_match('/^Pangkat\/Golongan\b/ui', $paragraph_text)) {
                $signer['pangkat_terakhir'] = $this->extract_template_field_value($paragraph_text);
                continue;
            }

            if ($signer['jabatan'] === '' && preg_match('/^Jabatan\b/ui', $paragraph_text)) {
                $signer['jabatan'] = $this->extract_template_field_value($paragraph_text);
                continue;
            }
        }

        return $signer;
    }

    private function normalize_docx_paragraph_text(DOMXPath $xpath, DOMElement $paragraph)
    {
        $text = '';

        foreach ($xpath->query('.//w:t', $paragraph) as $text_node) {
            $text .= $text_node->textContent;
        }

        $text = html_entity_decode($text, ENT_QUOTES | ENT_XML1, 'UTF-8');
        $text = preg_replace('/\s+/u', ' ', trim($text));

        return trim((string) $text);
    }

    private function extract_template_field_value($paragraph_text)
    {
        $parts = preg_split('/:\s*/u', $paragraph_text, 2);

        if (isset($parts[1])) {
            return trim($parts[1]);
        }

        $paragraph_text = preg_replace('/^(Nama|NIP\.?|Pangkat\/Golongan|Jabatan)\s*/ui', '', $paragraph_text);

        return trim((string) $paragraph_text);
    }

    private function build_template_penandatangan($template_signer, $fallback)
    {
        $template_signer = is_array($template_signer) ? $template_signer : array();
        $fallback = is_object($fallback) ? $fallback : new stdClass();
        $result = new stdClass();
        $result->nama = !empty($template_signer['nama']) ? $template_signer['nama'] : (!empty($fallback->nama) ? $fallback->nama : '-');
        $result->nip = !empty($template_signer['nip']) ? $template_signer['nip'] : (!empty($fallback->nip) ? $fallback->nip : '-');
        $result->pangkat_terakhir = !empty($template_signer['pangkat_terakhir']) ? $template_signer['pangkat_terakhir'] : (!empty($fallback->pangkat_terakhir) ? $fallback->pangkat_terakhir : '-');
        $result->jabatan = !empty($template_signer['jabatan']) ? $template_signer['jabatan'] : (!empty($fallback->jabatan) ? $fallback->jabatan : '-');

        return $result;
    }

    private function split_signature_jabatan_lines($jabatan)
    {
        $jabatan = trim((string) $jabatan);

        if ($jabatan === '') {
            return array('', '');
        }

        $jabatan = $this->format_jabatan_template($jabatan);

        if (strpos($jabatan, ',') !== FALSE) {
            $parts = explode(',', $jabatan, 2);

            return array(
                trim($parts[0]) . ',',
                trim($parts[1]),
            );
        }

        $words = preg_split('/\s+/u', $jabatan);

        if (count($words) <= 3) {
            return array($jabatan, '');
        }

        $split_index = (int) ceil(count($words) / 2);

        return array(
            trim(implode(' ', array_slice($words, 0, $split_index))),
            trim(implode(' ', array_slice($words, $split_index))),
        );
    }

    private function populate_surat_keterangan_sakit_template($xml, $surat, $pegawai, $signer)
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $previous_state = libxml_use_internal_errors(TRUE);
        $loaded = $dom->loadXML($xml);
        libxml_clear_errors();
        libxml_use_internal_errors($previous_state);

        if (!$loaded) {
            return $xml;
        }

        $xpath = new DOMXPath($dom);
        $xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');
        $paragraph_nodes = $xpath->query('//w:p');
        $paragraphs = array();

        foreach ($paragraph_nodes as $paragraph) {
            $paragraphs[] = $paragraph;
        }

        $section = 'before_signer';
        $date_signature_index = NULL;

        foreach ($paragraphs as $index => $paragraph) {
            $paragraph_text = $this->normalize_docx_paragraph_text($xpath, $paragraph);

            if ($paragraph_text !== '' && preg_match('/^Nomor\b/ui', $paragraph_text)) {
                $this->rewrite_docx_paragraph_text(
                    $dom,
                    $xpath,
                    $paragraph,
                    'Nomor : ' . $this->build_nomor_surat($surat->tanggal_surat)
                );
                continue;
            }

            if ($paragraph_text === '') {
                continue;
            }

            if (strpos($paragraph_text, 'Yang bertanda tangan dibawah ini') !== FALSE) {
                $section = 'signer';
                continue;
            }

            if ($section === 'signer') {
                if (strpos($paragraph_text, 'Menerangkan') !== FALSE) {
                    $section = 'pegawai';
                    continue;
                }

                if ($this->paragraph_matches_label($paragraph_text, 'Nama')) {
                    $this->rewrite_docx_paragraph_text($dom, $xpath, $paragraph, 'Nama : ' . $signer->nama);
                    continue;
                }

                if ($this->paragraph_matches_label($paragraph_text, 'NIP')) {
                    $this->rewrite_docx_paragraph_text($dom, $xpath, $paragraph, 'NIP. : ' . $this->format_nip_template($signer->nip));
                    continue;
                }

                if ($this->paragraph_matches_label($paragraph_text, 'Pangkat/Golongan')) {
                    $this->rewrite_docx_paragraph_text($dom, $xpath, $paragraph, 'Pangkat/Golongan : ' . $this->format_pangkat_template($signer->pangkat_terakhir));
                    continue;
                }

                if ($this->paragraph_matches_label($paragraph_text, 'Jabatan')) {
                    $this->rewrite_docx_paragraph_text($dom, $xpath, $paragraph, 'Jabatan : ' . $this->format_jabatan_template($signer->jabatan));
                    continue;
                }
            }

            if ($section === 'pegawai') {
                if (strpos($paragraph_text, 'Tidak melakukan sidik jari') !== FALSE) {
                    $this->rewrite_docx_paragraph_text(
                        $dom,
                        $xpath,
                        $paragraph,
                        $this->build_kalimat_surat($surat->jenis, $surat->tanggal_izin, $surat->alasan)
                    );
                    $section = 'after_pegawai';
                    continue;
                }

                if ($this->paragraph_matches_label($paragraph_text, 'Nama')) {
                    $this->rewrite_docx_paragraph_text($dom, $xpath, $paragraph, 'Nama : ' . $pegawai->nama);
                    continue;
                }

                if ($this->paragraph_matches_label($paragraph_text, 'NIP')) {
                    $this->rewrite_docx_paragraph_text($dom, $xpath, $paragraph, 'NIP. : ' . $this->format_nip_template($pegawai->nip));
                    continue;
                }

                if ($this->paragraph_matches_label($paragraph_text, 'Jabatan')) {
                    $this->rewrite_docx_paragraph_text($dom, $xpath, $paragraph, 'Jabatan : ' . $this->format_jabatan_template($pegawai->jabatan));
                    continue;
                }
            }

            if ($date_signature_index === NULL && strpos($paragraph_text, 'Tomohon,') !== FALSE) {
                $this->rewrite_docx_paragraph_text(
                    $dom,
                    $xpath,
                    $paragraph,
                    $this->build_tanggal_ttd_template($surat->tanggal_surat)
                );
                $date_signature_index = $index;
            }
        }

        if ($date_signature_index !== NULL) {
            $this->rewrite_signature_block($dom, $xpath, $paragraphs, $date_signature_index, $signer);
        }

        return $dom->saveXML();
    }

    private function paragraph_matches_label($paragraph_text, $label)
    {
        $pattern = '/^' . preg_quote($label, '/') . '(?:\b|\.|\s|:)/ui';

        return (bool) preg_match($pattern, $paragraph_text);
    }

    private function rewrite_signature_block(DOMDocument $dom, DOMXPath $xpath, array $paragraphs, $date_signature_index, $signer)
    {
        $non_empty_after_date = array();

        for ($i = $date_signature_index + 1; $i < count($paragraphs); $i++) {
            $paragraph_text = $this->normalize_docx_paragraph_text($xpath, $paragraphs[$i]);

            if ($paragraph_text === '') {
                continue;
            }

            $non_empty_after_date[] = array(
                'index' => $i,
                'text' => $paragraph_text,
            );

            if ($this->looks_like_signature_nip_line($paragraph_text)) {
                break;
            }
        }

        if (count($non_empty_after_date) < 3) {
            return;
        }

        $nip_item = array_pop($non_empty_after_date);
        $pangkat_item = array_pop($non_empty_after_date);
        $name_item = array_pop($non_empty_after_date);
        $jabatan_lines = $this->split_signature_jabatan_lines($signer->jabatan);

        foreach ($non_empty_after_date as $position => $item) {
            $replacement = isset($jabatan_lines[$position]) ? $jabatan_lines[$position] : '';
            $this->rewrite_docx_paragraph_text($dom, $xpath, $paragraphs[$item['index']], $replacement);
        }

        $this->rewrite_docx_paragraph_text($dom, $xpath, $paragraphs[$name_item['index']], $signer->nama);
        $this->rewrite_docx_paragraph_text($dom, $xpath, $paragraphs[$pangkat_item['index']], $this->format_pangkat_template($signer->pangkat_terakhir));
        $this->rewrite_docx_paragraph_text($dom, $xpath, $paragraphs[$nip_item['index']], 'NIP. ' . $this->format_nip_template($signer->nip));
    }

    private function looks_like_signature_nip_line($paragraph_text)
    {
        return (bool) preg_match('/\bNIP\b|\d{8}\s*\d{6}\s*\d\s*\d{3}/u', $paragraph_text);
    }
}
