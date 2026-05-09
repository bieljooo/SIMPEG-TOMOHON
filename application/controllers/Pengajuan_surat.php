<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pengajuan_surat extends CI_Controller {
    const TEMPLATE_SURAT_SAKIT = 'C:\\Users\\Gabriel Pangkong\\Documents\\PROJECT MPPL\\SURAT IZIN ASN.docx';
    const KODE_TEMPLATE_SURAT_SAKIT = 'surat_keterangan_sakit';
    const PENANDATANGAN_NIP = '197704162010012004';

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Pegawai_model');
        $this->load->model('Pengajuan_surat_model');
        $this->load->model('Master_surat_model');

        if (!$this->session->userdata('logged_in') || $this->session->userdata('role') !== 'pegawai') {
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

            if (strpos($paragraph_text, $needle) === FALSE) {
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
