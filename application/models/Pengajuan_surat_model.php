<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pengajuan_surat_model extends CI_Model {
    private $rekomendasi_table = 'surat_rekomendasi';

    public function __construct()
    {
        parent::__construct();
        $this->ensure_rekomendasi_template_fields();
    }

    public function resolve_status_after_petugas($jabatan_urutan)
    {
        $jabatan_urutan = (int) $jabatan_urutan;

        if ($jabatan_urutan === 1) {
            return 'approved';
        }

        if ($jabatan_urutan === 2) {
            return 'pending_kadis';
        }

        if ($jabatan_urutan === 3) {
            return 'pending_sek';
        }

        return 'pending_kasubag';
    }

    public function get_expected_status_for_role($role)
    {
        $map = array(
            'kasubag' => 'pending_kasubag',
            'sek' => 'pending_sek',
            'kadis' => 'pending_kadis',
        );

        return isset($map[$role]) ? $map[$role] : NULL;
    }

    public function resolve_status_after_role_approval($role)
    {
        $map = array(
            'kasubag' => 'pending_sek',
            'sek' => 'pending_kadis',
            'kadis' => 'approved',
        );

        return isset($map[$role]) ? $map[$role] : NULL;
    }

    public function insert_surat_rekomendasi($data)
    {
        $this->db->insert($this->rekomendasi_table, $data);

        return $this->db->insert_id();
    }

    public function get_all_surat_rekomendasi_masuk()
    {
        return $this->db
            ->select('id, jenis_surat, nip, nama, pangkat, jabatan, jabatan_urutan, nomor_surat, nomor_surat_at, petugas_nip, kasubag_nip, sek_nip, kadis_nip, status, rejected_by_role, rejected_by_nip, rejected_at, created_at, updated_at, sifat, lampiran, perihal, template_no, template_nama, template_nip, tanggal_mulai, tanggal_selesai, keterangan, negara_tujuan')
            ->from($this->rekomendasi_table)
            ->order_by('created_at', 'DESC')
            ->order_by('id', 'DESC')
            ->get()
            ->result();
    }

    public function get_surat_rekomendasi_by_nip($nip)
    {
        return $this->db
            ->select('id, jenis_surat, nip, nama, pangkat, jabatan, jabatan_urutan, nomor_surat, nomor_surat_at, petugas_nip, kasubag_nip, sek_nip, kadis_nip, status, rejected_by_role, rejected_by_nip, rejected_at, created_at, updated_at, sifat, lampiran, perihal, template_no, template_nama, template_nip, tanggal_mulai, tanggal_selesai, keterangan, negara_tujuan')
            ->from($this->rekomendasi_table)
            ->where('nip', $nip)
            ->order_by('created_at', 'DESC')
            ->order_by('id', 'DESC')
            ->get()
            ->result();
    }

    public function get_surat_rekomendasi_download_by_nip($nip)
    {
        return $this->db
            ->select('sr.id, sr.jenis_surat, sr.nip, sr.nama, sr.pangkat, sr.jabatan, sr.jabatan_urutan, sr.nomor_surat, sr.nomor_surat_at, sr.petugas_nip, sr.kasubag_nip, sr.kasubag_at, sr.sek_nip, sr.sek_at, sr.kadis_nip, sr.kadis_at, sr.status, sr.rejected_by_role, sr.rejected_by_nip, sr.rejected_at, sr.created_at, sr.updated_at, sr.sifat, sr.lampiran, sr.perihal, sr.template_no, sr.template_nama, sr.template_nip, sr.tanggal_mulai, sr.tanggal_selesai, sr.keterangan, sr.negara_tujuan, kadis.nama AS kadis_nama')
            ->from($this->rekomendasi_table . ' AS sr')
            ->join('pegawai AS kadis', 'kadis.nip = sr.kadis_nip', 'left')
            ->where('sr.nip', $nip)
            ->where('sr.status', 'approved')
            ->order_by('sr.kadis_at', 'DESC')
            ->order_by('sr.updated_at', 'DESC')
            ->order_by('sr.id', 'DESC')
            ->get()
            ->result();
    }

    public function get_surat_rekomendasi_by_id($id)
    {
        return $this->db
            ->select('id, jenis_surat, nip, nama, pangkat, jabatan, jabatan_urutan, nomor_surat, nomor_surat_at, petugas_nip, kasubag_nip, kasubag_at, sek_nip, sek_at, kadis_nip, kadis_at, status, rejected_by_role, rejected_by_nip, rejected_at, created_at, updated_at, sifat, lampiran, perihal, template_no, template_nama, template_nip, tanggal_mulai, tanggal_selesai, keterangan, negara_tujuan')
            ->where('id', (int) $id)
            ->get($this->rekomendasi_table)
            ->row();
    }

    public function get_surat_rekomendasi_download_detail($id, $nip)
    {
        return $this->db
            ->select('sr.id, sr.jenis_surat, sr.nip, sr.nama, sr.pangkat, sr.jabatan, sr.jabatan_urutan, sr.nomor_surat, sr.nomor_surat_at, sr.petugas_nip, sr.kasubag_nip, sr.kasubag_at, sr.sek_nip, sr.sek_at, sr.kadis_nip, sr.kadis_at, sr.status, sr.rejected_by_role, sr.rejected_by_nip, sr.rejected_at, sr.created_at, sr.updated_at, sr.sifat, sr.lampiran, sr.perihal, sr.template_no, sr.template_nama, sr.template_nip, sr.tanggal_mulai, sr.tanggal_selesai, sr.keterangan, sr.negara_tujuan, kadis.nama AS kadis_nama')
            ->from($this->rekomendasi_table . ' AS sr')
            ->join('pegawai AS kadis', 'kadis.nip = sr.kadis_nip', 'left')
            ->where('sr.id', (int) $id)
            ->where('sr.nip', $nip)
            ->where('sr.status', 'approved')
            ->get()
            ->row();
    }

    public function update_surat_rekomendasi_nomor($id, $nomor_surat, $petugas_nip, $next_status, $data_extra = array())
    {
        $update_data = array(
            'nomor_surat' => $nomor_surat,
            'nomor_surat_at' => date('Y-m-d H:i:s'),
            'petugas_nip' => $petugas_nip,
            'status' => $next_status,
        );

        if (!empty($data_extra)) {
            $update_data = array_merge($update_data, $data_extra);
        }

        return $this->db
            ->where('id', (int) $id)
            ->where('status', 'pending_petugas')
            ->update($this->rekomendasi_table, $update_data);
    }

    public function delete_surat_rekomendasi($id, $nip)
    {
        return $this->db
            ->where('id', (int) $id)
            ->where('nip', $nip)
            ->where('status', 'approved')
            ->delete($this->rekomendasi_table);
    }

    public function get_surat_rekomendasi_by_role($role)
    {
        $expected_status = $this->get_expected_status_for_role($role);

        if ($expected_status === NULL) {
            return array();
        }

        return $this->db
            ->from($this->rekomendasi_table)
            ->where('status', $expected_status)
            ->order_by('nomor_surat_at', 'DESC')
            ->order_by('created_at', 'DESC')
            ->order_by('id', 'DESC')
            ->get()
            ->result();
    }

    public function approve_surat_rekomendasi($id, $role, $actor_nip)
    {
        $surat = $this->get_surat_rekomendasi_by_id($id);
        $expected_status = $this->get_expected_status_for_role($role);
        $next_status = $this->resolve_status_after_role_approval($role);

        if (empty($surat) || $expected_status === NULL || $next_status === NULL || $surat->status !== $expected_status) {
            return FALSE;
        }

        $timestamp_column = $role . '_at';
        $nip_column = $role . '_nip';

        return $this->db
            ->where('id', (int) $id)
            ->where('status', $expected_status)
            ->update($this->rekomendasi_table, array(
                $nip_column => $actor_nip,
                $timestamp_column => date('Y-m-d H:i:s'),
                'status' => $next_status,
                'rejected_by_role' => NULL,
                'rejected_by_nip' => NULL,
                'rejected_at' => NULL,
            ));
    }

    public function reject_surat_rekomendasi($id, $role, $actor_nip)
    {
        $surat = $this->get_surat_rekomendasi_by_id($id);
        $expected_status = $this->get_expected_status_for_role($role);

        if (empty($surat) || $expected_status === NULL || $surat->status !== $expected_status) {
            return FALSE;
        }

        $timestamp_column = $role . '_at';
        $nip_column = $role . '_nip';

        return $this->db
            ->where('id', (int) $id)
            ->where('status', $expected_status)
            ->update($this->rekomendasi_table, array(
                $nip_column => $actor_nip,
                $timestamp_column => date('Y-m-d H:i:s'),
                'status' => 'rejected_' . $role,
                'rejected_by_role' => $role,
                'rejected_by_nip' => $actor_nip,
                'rejected_at' => date('Y-m-d H:i:s'),
            ));
    }

    public function insert_surat_keterangan_sakit($data)
    {
        return $this->db->insert('pengajuan_surat_sakit', $data);
    }

    public function insert_surat_pegawai($data)
    {
        $this->db->insert('surat_pegawai', $data);

        return $this->db->insert_id();
    }

    public function get_surat_pegawai_by_nip($nip)
    {
        $this->db->select('surat_pegawai.*, penandatangan.nama AS penandatangan_nama');
        $this->db->from('surat_pegawai');
        $this->db->join('pegawai AS penandatangan', 'penandatangan.nip = surat_pegawai.penandatangan_nip', 'left');
        $this->db->where('surat_pegawai.nip', $nip);
        $this->db->order_by('surat_pegawai.created_at', 'DESC');

        return $this->db->get()->result();
    }

    public function get_surat_pegawai_detail($id, $nip)
    {
        $this->db->select('surat_pegawai.*, penandatangan.nama AS penandatangan_nama');
        $this->db->from('surat_pegawai');
        $this->db->join('pegawai AS penandatangan', 'penandatangan.nip = surat_pegawai.penandatangan_nip', 'left');
        $this->db->where('surat_pegawai.id', (int) $id);
        $this->db->where('surat_pegawai.nip', $nip);

        return $this->db->get()->row();
    }

    public function delete_surat_pegawai($id, $nip)
    {
        return $this->db
            ->where('id', (int) $id)
            ->where('nip', $nip)
            ->delete('surat_pegawai');
    }

    public function get_all_surat_masuk()
    {
        $this->db->select('pengajuan_surat_sakit.*, pegawai.nama');
        $this->db->from('pengajuan_surat_sakit');
        $this->db->join('pegawai', 'pegawai.nip = pengajuan_surat_sakit.nip', 'left');
        $this->db->order_by('pengajuan_surat_sakit.created_at', 'DESC');

        return $this->db->get()->result();
    }

    public function get_surat_masuk_by_id($id)
    {
        $this->db->select('pengajuan_surat_sakit.*, pegawai.nama');
        $this->db->from('pengajuan_surat_sakit');
        $this->db->join('pegawai', 'pegawai.nip = pengajuan_surat_sakit.nip', 'left');
        $this->db->where('pengajuan_surat_sakit.id', $id);

        return $this->db->get()->row();
    }

    public function update_nomor_surat($id, $nomor_surat)
    {
        $this->db->where('id', $id);

        return $this->db->update('pengajuan_surat_sakit', array(
            'nomor_surat' => $nomor_surat,
            'nomor_surat_at' => date('Y-m-d H:i:s'),
        ));
    }

    public function delete_surat_masuk($id)
    {
        return $this->db
            ->where('id', (int) $id)
            ->delete($this->rekomendasi_table);
    }

    private function ensure_rekomendasi_template_fields()
    {
        if (!$this->db->table_exists($this->rekomendasi_table)) {
            return;
        }

        $columns = array(
            'template_no' => "ALTER TABLE `{$this->rekomendasi_table}` ADD `template_no` VARCHAR(20) COLLATE utf8mb4_general_ci DEFAULT NULL AFTER `perihal`",
            'template_nama' => "ALTER TABLE `{$this->rekomendasi_table}` ADD `template_nama` VARCHAR(200) COLLATE utf8mb4_general_ci DEFAULT NULL AFTER `template_no`",
            'template_nip' => "ALTER TABLE `{$this->rekomendasi_table}` ADD `template_nip` VARCHAR(30) COLLATE utf8mb4_general_ci DEFAULT NULL AFTER `template_nama`",
            'tanggal_mulai' => "ALTER TABLE `{$this->rekomendasi_table}` ADD `tanggal_mulai` DATE DEFAULT NULL AFTER `template_nip`",
            'tanggal_selesai' => "ALTER TABLE `{$this->rekomendasi_table}` ADD `tanggal_selesai` DATE DEFAULT NULL AFTER `tanggal_mulai`",
            'keterangan' => "ALTER TABLE `{$this->rekomendasi_table}` ADD `keterangan` TEXT COLLATE utf8mb4_general_ci NULL AFTER `tanggal_selesai`",
            'negara_tujuan' => "ALTER TABLE `{$this->rekomendasi_table}` ADD `negara_tujuan` VARCHAR(150) COLLATE utf8mb4_general_ci DEFAULT NULL AFTER `keterangan`",
        );

        foreach ($columns as $column => $sql) {
            if ($this->db->field_exists($column, $this->rekomendasi_table)) {
                continue;
            }

            $this->db->query($sql);
        }
    }
}
