<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pengajuan_surat_model extends CI_Model {

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
}
