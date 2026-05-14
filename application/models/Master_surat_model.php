<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Master_surat_model extends CI_Model {

    public function ensure_default_templates()
    {
        if (!$this->db->table_exists('template_surat')) {
            return;
        }

        $defaults = array(
            array(
                'kode_template' => 'surat_keterangan_sakit',
                'nama_template' => 'Template Surat Keterangan Sakit',
                'sub_menu' => 'Surat Sakit',
                'sort_order' => 1,
            ),
            array(
                'kode_template' => 'usulan_kenaikan_pangkat',
                'nama_template' => 'Template Usulan Kenaikan Pangkat',
                'sub_menu' => 'Usulan Kenaikan Pangkat',
                'sort_order' => 2,
            ),
            array(
                'kode_template' => 'usulan_cuti_tahun',
                'nama_template' => 'Template Usulan Cuti Tahunan',
                'sub_menu' => 'Usulan Cuti Tahunan',
                'sort_order' => 3,
            ),
            array(
                'kode_template' => 'usulan_alasan_penting',
                'nama_template' => 'Template Usulan Cuti Alasan Penting',
                'sub_menu' => 'Usulan Cuti Alasan Penting',
                'sort_order' => 4,
            ),
            array(
                'kode_template' => 'usulan_kenaikan_gaji_berkala',
                'nama_template' => 'Template Usulan Kenaikan Gaji Berkala',
                'sub_menu' => 'Usulan Kenaikan Gaji Berkala',
                'sort_order' => 5,
            ),
            array(
                'kode_template' => 'usulan_cuti_luar_negeri',
                'nama_template' => 'Template Usulan Cuti Luar Negeri',
                'sub_menu' => 'Usulan Cuti Luar Negeri',
                'sort_order' => 6,
            ),
        );

        foreach ($defaults as $default) {
            $existing = $this->db
                ->where('kode_template', $default['kode_template'])
                ->get('template_surat')
                ->row();

            if (empty($existing)) {
                $this->db->insert('template_surat', $default);
                continue;
            }

            $this->db
                ->where('id', $existing->id)
                ->update('template_surat', array(
                    'nama_template' => $default['nama_template'],
                    'sub_menu' => $default['sub_menu'],
                    'sort_order' => $default['sort_order'],
                ));
        }
    }

    public function get_all()
    {
        return $this->db
            ->from('template_surat')
            ->order_by('sort_order', 'ASC')
            ->order_by('id', 'ASC')
            ->get()
            ->result();
    }

    public function get_by_id($id)
    {
        return $this->db
            ->where('id', (int) $id)
            ->get('template_surat')
            ->row();
    }

    public function get_by_code($kode_template)
    {
        return $this->db
            ->where('kode_template', (string) $kode_template)
            ->get('template_surat')
            ->row();
    }

    public function update($id, $data)
    {
        return $this->db
            ->where('id', (int) $id)
            ->update('template_surat', $data);
    }

    public function delete($id)
    {
        return $this->db
            ->where('id', (int) $id)
            ->delete('template_surat');
    }
}
