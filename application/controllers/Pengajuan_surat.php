<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pengajuan_surat extends CI_Controller {
    const TEMPLATE_SURAT_SAKIT = 'C:\\Users\\Gabriel Pangkong\\Documents\\PROJECT MPPL\\SURAT IZIN ASN.docx';
    const PENANDATANGAN_NIP = '197704162010012004';

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Pegawai_model');
        $this->load->model('Pengajuan_surat_model');

        if (!$this->session->userdata('logged_in') || $this->session->userdata('role') !== 'pegawai') {
            redirect('auth');
        }
    }

    public function index()
    {
        redirect('pengajuan_surat/surat_keterangan_sakit');
    }

    public function cuti_kenaikan_pangkat()
    {
        $this->render_rekomendasi_placeholder('Usulan Kenaikan Pangkat');
    }

    public function pengajuan_cuti_tahun()
    {
        $this->render_rekomendasi_placeholder('Usulan Cuti Tahun');
    }

    public function cuti_alasan_penting()
    {
        $this->render_rekomendasi_placeholder('Usulan Alasan Penting');
    }

    public function kenaikan_gaji_berkala()
    {
        $this->render_rekomendasi_placeholder('Usulan Kenaikan Gaji Berkala');
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

        $this->load->view('templates/header_pegawai', $data);
        $this->load->view('pengajuan_surat/download_surat', $data);
        $this->load->view('templates/footer_pegawai');
    }

    public function download_surat_rekomendasi()
    {
        $this->render_rekomendasi_placeholder('Download');
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
        $this->output_surat_keterangan_sakit_pdf($id, TRUE);
    }

    public function preview_surat_keterangan_sakit($id)
    {
        $this->output_surat_keterangan_sakit_pdf($id, FALSE);
    }

    private function output_surat_keterangan_sakit_pdf($id, $is_download)
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

        $autoload_path = FCPATH . 'vendor/autoload.php';
        if (!is_file($autoload_path)) {
            show_error('Library PDF belum tersedia.', 500);
        }

        require_once $autoload_path;

        $view_data = array(
            'header_lines' => $template['header_lines'],
            'logo_data_uri' => $template['logo_data_uri'],
            'nomor_surat' => $this->build_nomor_surat($surat->tanggal_surat),
            'pegawai' => $pegawai,
            'penandatangan' => $penandatangan,
            'surat' => $surat,
            'tanggal_surat_indonesia' => $this->format_tanggal_indonesia($surat->tanggal_surat),
            'kalimat_surat' => $this->build_kalimat_surat($surat->jenis, $surat->tanggal_izin, $surat->alasan),
        );

        $html = $this->load->view('pengajuan_surat/pdf_surat_keterangan_sakit', $view_data, TRUE);

        $options = new Dompdf\Options();
        $options->set('isHtml5ParserEnabled', TRUE);
        $options->set('isRemoteEnabled', TRUE);
        $options->set('defaultFont', 'serif');

        $dompdf = new Dompdf\Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream('surat-keterangan-sakit-' . $surat->id . '.pdf', array(
            'Attachment' => (bool) $is_download,
        ));
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

    private function get_template_assets()
    {
        $default_header_lines = array(
            'PEMERINTAH KOTA TOMOHON',
            'DINAS PENANAMAN MODAL DAN',
            'PELAYANAN TERPADU SATU PINTU DAERAH',
            'Jalan Slanag Kelurahan Kolongan Satu Kecamatan Tomohon Tengah 95441',
            'Email : dpmptsp@tomohon.go.id Website : https://dpmptsp.tomohon.go.id',
        );

        if (!is_file(self::TEMPLATE_SURAT_SAKIT)) {
            return array(
                'header_lines' => $default_header_lines,
                'logo_data_uri' => NULL,
                'template_found' => FALSE,
            );
        }

        $zip = new ZipArchive();
        if ($zip->open(self::TEMPLATE_SURAT_SAKIT) !== TRUE) {
            return array(
                'header_lines' => $default_header_lines,
                'logo_data_uri' => NULL,
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
                'template_found' => FALSE,
            );
        }

        $xml = str_replace(array('</w:p>', '</w:tr>'), array("\n", "\n"), $xml);
        $text = preg_replace('/<[^>]+>/', '', $xml);
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
            'template_found' => TRUE,
        );
    }
}
