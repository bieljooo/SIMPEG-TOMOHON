<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pegawai_model extends CI_Model {

    public function get_jabatan_hierarchy_value($jabatan)
    {
        $jabatan = strtoupper(trim((string) $jabatan));

        if ($jabatan === 'KEPALA DINAS') {
            return 1;
        }

        if (strpos($jabatan, 'SEK') === 0) {
            return 2;
        }

        if (strpos($jabatan, 'KASUBAG') === 0) {
            return 3;
        }

        if (strpos($jabatan, 'KEPALA BIDANG') !== FALSE) {
            return 4;
        }

        if (strpos($jabatan, 'PENATA PERIZINAN') === 0) {
            return 5;
        }

        if (strpos($jabatan, 'ANALIS KEBIJAKAN') === 0) {
            return 6;
        }

        return 99;
    }

    public function requires_kasubag_verification($jabatan)
    {
        return $this->get_jabatan_hierarchy_value($jabatan) > 3;
    }

    private function apply_jabatan_hierarchy_order()
    {
        $this->db->order_by('pegawai.jabatan_urutan', 'ASC');
    }

    public function get_all()
    {
        $this->db->select('pegawai.*, pegawai_pribadi.tempat_lahir, pegawai_pribadi.tanggal_lahir, pegawai_pribadi.status_kawin, pegawai_pribadi.agama, pegawai_pribadi.alamat, pegawai_pribadi.no_telp, pegawai_drh.tingkat_pendidikan, pegawai_drh.jurusan, pegawai_drh.tahun_lulus, pegawai_drh.alumni');
        $this->db->from('pegawai');
        $this->db->join('pegawai_pribadi', 'pegawai_pribadi.nip = pegawai.nip', 'left');
        $this->db->join('pegawai_drh', 'pegawai_drh.nip = pegawai.nip', 'left');
        $this->apply_jabatan_hierarchy_order();
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

    public function get_all_accounts()
    {
        $this->db->select('nama, nip');
        $this->db->from('pegawai');
        $this->apply_jabatan_hierarchy_order();
        $this->db->order_by('created_at', 'ASC');
        $this->db->order_by('nip', 'ASC');
        return $this->db->get()->result();
    }

    public function get_account_by_nip($nip)
    {
        return $this->db
            ->select('nama, nip, password')
            ->from('pegawai')
            ->where('nip', $nip)
            ->get()
            ->row();
    }

    public function update_account_password($nip, $password_hash)
    {
        return $this->db
            ->where('nip', $nip)
            ->update('pegawai', array('password' => $password_hash));
    }

    public function insert($pegawai, $pribadi, $drh)
    {
        $pegawai['jabatan_urutan'] = $this->get_jabatan_hierarchy_value(isset($pegawai['jabatan']) ? $pegawai['jabatan'] : '');

        $this->db->trans_start();

        $this->db->insert('pegawai', $pegawai);
        $this->db->insert('pegawai_pribadi', $pribadi);
        $this->db->insert('pegawai_drh', $drh);

        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    public function insert_pending($data)
    {
        $data['jenis'] = isset($data['jenis']) && trim((string) $data['jenis']) !== ''
            ? strtolower(trim((string) $data['jenis']))
            : 'baru';

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
        $pegawai['jabatan_urutan'] = $this->get_jabatan_hierarchy_value(isset($pegawai['jabatan']) ? $pegawai['jabatan'] : '');

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

        $jenis = isset($pending->jenis) && trim((string) $pending->jenis) !== ''
            ? strtolower(trim((string) $pending->jenis))
            : 'baru';

        $pegawai = array(
            'nama'                => $pending->nama,
            'gol_ruang_cpns'      => $pending->gol_ruang_cpns,
            'tmt_cpns'            => $pending->tmt_cpns,
            'pangkat_terakhir'    => $pending->pangkat_terakhir,
            'jenis_kelamin'       => $pending->jenis_kelamin,
            'jabatan'             => $pending->jabatan,
            'jabatan_urutan'      => $this->get_jabatan_hierarchy_value($pending->jabatan),
            'eselon'              => $pending->eselon,
            'diklat_penjenjangan' => $pending->diklat_penjenjangan,
            'instansi_pembayar'   => $pending->instansi_pembayar,
            'keterangan'          => $pending->keterangan,
        );

        $pribadi = array(
            'tempat_lahir'   => $pending->tempat_lahir,
            'tanggal_lahir'  => $pending->tanggal_lahir,
            'status_kawin'   => $pending->status_kawin,
            'agama'          => $pending->agama,
            'alamat'         => $pending->alamat,
            'no_telp'        => $pending->no_telp,
        );

        $drh = array(
            'tingkat_pendidikan' => $pending->tingkat_pendidikan,
            'jurusan'            => $pending->jurusan,
            'tahun_lulus'        => $pending->tahun_lulus,
            'alumni'             => $pending->alumni,
        );

        $this->db->trans_start();

        if ($jenis === 'update' && $this->nip_exists($pending->nip)) {
            $this->db->where('nip', $pending->nip);
            $this->db->update('pegawai', $pegawai);

            if ($this->db->where('nip', $pending->nip)->count_all_results('pegawai_pribadi') > 0) {
                $this->db->where('nip', $pending->nip);
                $this->db->update('pegawai_pribadi', $pribadi);
            } else {
                $this->db->insert('pegawai_pribadi', array_merge(array('nip' => $pending->nip), $pribadi));
            }

            if ($this->db->where('nip', $pending->nip)->count_all_results('pegawai_drh') > 0) {
                $this->db->where('nip', $pending->nip);
                $this->db->update('pegawai_drh', $drh);
            } else {
                $this->db->insert('pegawai_drh', array_merge(array('nip' => $pending->nip), $drh));
            }
        } else {
            $this->db->insert('pegawai', array_merge(array(
                'nip'      => $pending->nip,
                'role'     => 'pegawai',
                'password' => $default_password_hash,
            ), $pegawai));
            $this->db->insert('pegawai_pribadi', array_merge(array('nip' => $pending->nip), $pribadi));
            $this->db->insert('pegawai_drh', array_merge(array('nip' => $pending->nip), $drh));
        }

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
