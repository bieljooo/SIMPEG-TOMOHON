<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pegawai_model extends CI_Model {

    public function get_all()
    {
        $this->db->select('pegawai.*, pegawai_pribadi.tempat_lahir, pegawai_pribadi.tanggal_lahir, pegawai_pribadi.status_kawin, pegawai_pribadi.agama, pegawai_pribadi.alamat, pegawai_pribadi.no_telp, pegawai_drh.tingkat_pendidikan, pegawai_drh.jurusan, pegawai_drh.tahun_lulus, pegawai_drh.alumni');
        $this->db->from('pegawai');
        $this->db->join('pegawai_pribadi', 'pegawai_pribadi.nip = pegawai.nip', 'left');
        $this->db->join('pegawai_drh', 'pegawai_drh.nip = pegawai.nip', 'left');
        $this->db->order_by('pegawai.created_at', 'ASC');
        $this->db->order_by('pegawai.nip', 'ASC');
        return $this->db->get()->result();
    }

    public function get_by_nip($nip)
    {
        $this->db->select('pegawai.*, pegawai_pribadi.tempat_lahir, pegawai_pribadi.tanggal_lahir, pegawai_pribadi.status_kawin, pegawai_pribadi.agama, pegawai_pribadi.alamat, pegawai_pribadi.no_telp, pegawai_drh.tingkat_pendidikan, pegawai_drh.jurusan, pegawai_drh.tahun_lulus, pegawai_drh.alumni');
        $this->db->from('pegawai');
        $this->db->join('pegawai_pribadi', 'pegawai_pribadi.nip = pegawai.nip', 'left');
        $this->db->join('pegawai_drh', 'pegawai_drh.nip = pegawai.nip', 'left');
        $this->db->where('pegawai.nip', $nip);
        return $this->db->get()->row();
    }

    public function insert($pegawai, $pribadi, $drh)
    {
        $this->db->trans_start();

        $this->db->insert('pegawai', $pegawai);
        $this->db->insert('pegawai_pribadi', $pribadi);
        $this->db->insert('pegawai_drh', $drh);

        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    public function insert_pending($data)
    {
        $existing = $this->db
            ->where('nip', $data['nip'])
            ->get('pegawai_pending')
            ->row();

        if (empty($existing)) {
            return $this->db->insert('pegawai_pending', $data);
        }

        if ($existing->status === 'pending') {
            return FALSE;
        }

        $data['status'] = 'pending';
        $data['approved_by'] = NULL;
        $data['approved_at'] = NULL;
        $data['created_at'] = date('Y-m-d H:i:s');

        return $this->db
            ->where('id', $existing->id)
            ->update('pegawai_pending', $data);
    }

    public function update($nip, $pegawai, $pribadi, $drh)
    {
        $this->db->trans_start();

        $this->db->where('nip', $nip);
        $this->db->update('pegawai', $pegawai);

        $this->db->where('nip', $nip);
        $this->db->update('pegawai_pribadi', $pribadi);

        $this->db->where('nip', $nip);
        $this->db->update('pegawai_drh', $drh);

        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    public function delete($nip)
    {
        $this->db->trans_start();

        $this->db->where('nip', $nip);
        $this->db->delete('pegawai_drh');

        $this->db->where('nip', $nip);
        $this->db->delete('pegawai_pribadi');

        $this->db->where('nip', $nip);
        $this->db->delete('pegawai');

        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    public function count_all()
    {
        return $this->db->count_all('pegawai');
    }

    public function count_by_gender($jenis_kelamin)
    {
        return (int) $this->db
            ->where('jenis_kelamin', $jenis_kelamin)
            ->count_all_results('pegawai');
    }

    public function nip_exists($nip)
    {
        return $this->db->where('nip', $nip)->count_all_results('pegawai') > 0;
    }

    public function pending_nip_exists($nip)
    {
        return $this->db
            ->where('nip', $nip)
            ->where('status', 'pending')
            ->count_all_results('pegawai_pending') > 0;
    }

    public function get_all_pending()
    {
        $this->db->from('pegawai_pending');
        $this->db->where('status', 'pending');
        $this->db->order_by('created_at', 'DESC');

        return $this->db->get()->result();
    }

    public function get_all_processed_drafts()
    {
        return $this->db
            ->from('pegawai_pending')
            ->where_in('status', array('approved', 'rejected'))
            ->order_by('approved_at', 'DESC')
            ->order_by('updated_at', 'DESC')
            ->get()
            ->result();
    }

    public function get_pending_by_id($id)
    {
        return $this->db
            ->where('id', $id)
            ->get('pegawai_pending')
            ->row();
    }

    public function approve_pending($id, $approved_by, $default_password_hash)
    {
        $pending = $this->get_pending_by_id($id);

        if (empty($pending) || $pending->status !== 'pending') {
            return FALSE;
        }

        $pegawai = array(
            'nip'                 => $pending->nip,
            'nama'                => $pending->nama,
            'gol_ruang_cpns'      => $pending->gol_ruang_cpns,
            'tmt_cpns'            => $pending->tmt_cpns,
            'pangkat_terakhir'    => $pending->pangkat_terakhir,
            'jenis_kelamin'       => $pending->jenis_kelamin,
            'jabatan'             => $pending->jabatan,
            'eselon'              => $pending->eselon,
            'diklat_penjenjangan' => $pending->diklat_penjenjangan,
            'instansi_pembayar'   => $pending->instansi_pembayar,
            'keterangan'          => $pending->keterangan,
            'role'                => 'pegawai',
            'password'            => $default_password_hash,
        );

        $pribadi = array(
            'nip'            => $pending->nip,
            'tempat_lahir'   => $pending->tempat_lahir,
            'tanggal_lahir'  => $pending->tanggal_lahir,
            'status_kawin'   => $pending->status_kawin,
            'agama'          => $pending->agama,
            'alamat'         => $pending->alamat,
            'no_telp'        => $pending->no_telp,
        );

        $drh = array(
            'nip'                => $pending->nip,
            'tingkat_pendidikan' => $pending->tingkat_pendidikan,
            'jurusan'            => $pending->jurusan,
            'tahun_lulus'        => $pending->tahun_lulus,
            'alumni'             => $pending->alumni,
        );

        $this->db->trans_start();

        $this->db->insert('pegawai', $pegawai);
        $this->db->insert('pegawai_pribadi', $pribadi);
        $this->db->insert('pegawai_drh', $drh);

        $this->db->where('id', $id);
        $this->db->update('pegawai_pending', array(
            'status' => 'approved',
            'approved_by' => $approved_by,
            'approved_at' => date('Y-m-d H:i:s'),
        ));

        $this->db->trans_complete();

        return $this->db->trans_status();
    }

    public function reject_pending($id, $approved_by)
    {
        $pending = $this->get_pending_by_id($id);

        if (empty($pending) || $pending->status !== 'pending') {
            return FALSE;
        }

        return $this->db
            ->where('id', $id)
            ->update('pegawai_pending', array(
                'status' => 'rejected',
                'approved_by' => $approved_by,
                'approved_at' => date('Y-m-d H:i:s'),
            ));
    }

    public function delete_pending($id)
    {
        return $this->db
            ->where('id', $id)
            ->delete('pegawai_pending');
    }
}
