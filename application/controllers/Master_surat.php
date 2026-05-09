<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Master_surat extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Master_surat_model');

        if (
            !$this->session->userdata('logged_in') ||
            $this->session->userdata('role') !== 'petugas'
        ) {
            redirect('auth');
        }

        $this->Master_surat_model->ensure_default_templates();
    }

    public function index()
    {
        redirect('master_surat/template_surat');
    }

    public function template_surat()
    {
        $data['title'] = 'Template Surat';
        $data['templates'] = $this->Master_surat_model->get_all();

        $this->load->view('templates/header', $data);
        $this->load->view('master_surat/template_surat', $data);
        $this->load->view('templates/footer');
    }

    public function upload($id)
    {
        $template = $this->Master_surat_model->get_by_id($id);

        if (empty($template)) {
            show_404();
        }

        if ($this->input->method(TRUE) === 'POST') {
            $uploaded = $this->upload_template_file('template_file');

            if (!$uploaded['status']) {
                $this->session->set_flashdata('error', $uploaded['message']);
                redirect('master_surat/upload/' . $template->id);
                return;
            }

            $update_data = array(
                'file_path' => $uploaded['file_path'],
                'file_original_name' => $uploaded['original_name'],
                'file_mime' => $uploaded['file_mime'],
                'file_size' => $uploaded['file_size'],
            );

            if (!$this->Master_surat_model->update($template->id, $update_data)) {
                $this->delete_template_file($uploaded['file_path']);
                $this->session->set_flashdata('error', 'Template surat gagal diupload.');
                redirect('master_surat/upload/' . $template->id);
                return;
            }

            if (!empty($template->file_path) && $template->file_path !== $uploaded['file_path']) {
                $this->delete_template_file($template->file_path);
            }

            $this->session->set_flashdata('success', 'Template surat berhasil diupload.');
            redirect('master_surat/template_surat');
            return;
        }

        $data['title'] = 'Upload Template Surat';
        $data['template'] = $template;
        $data['form_mode'] = 'upload';

        $this->load->view('templates/header', $data);
        $this->load->view('master_surat/template_form', $data);
        $this->load->view('templates/footer');
    }

    public function edit($id)
    {
        $template = $this->Master_surat_model->get_by_id($id);

        if (empty($template)) {
            show_404();
        }

        if ($this->input->method(TRUE) === 'POST') {
            $nama_template = trim((string) $this->input->post('nama_template', TRUE));

            if ($nama_template === '') {
                $this->session->set_flashdata('error', 'Nama template wajib diisi.');
                redirect('master_surat/edit/' . $template->id);
                return;
            }

            $update_data = array(
                'nama_template' => $nama_template,
            );
            $uploaded_path = NULL;

            if (!empty($_FILES['template_file']['name'])) {
                $uploaded = $this->upload_template_file('template_file');

                if (!$uploaded['status']) {
                    $this->session->set_flashdata('error', $uploaded['message']);
                    redirect('master_surat/edit/' . $template->id);
                    return;
                }

                $update_data['file_path'] = $uploaded['file_path'];
                $update_data['file_original_name'] = $uploaded['original_name'];
                $update_data['file_mime'] = $uploaded['file_mime'];
                $update_data['file_size'] = $uploaded['file_size'];
                $uploaded_path = $uploaded['file_path'];
            }

            if (!$this->Master_surat_model->update($template->id, $update_data)) {
                if ($uploaded_path !== NULL) {
                    $this->delete_template_file($uploaded_path);
                }

                $this->session->set_flashdata('error', 'Template surat gagal diperbarui.');
                redirect('master_surat/edit/' . $template->id);
                return;
            }

            if ($uploaded_path !== NULL && !empty($template->file_path) && $template->file_path !== $uploaded_path) {
                $this->delete_template_file($template->file_path);
            }

            $this->session->set_flashdata('success', 'Template surat berhasil diperbarui.');
            redirect('master_surat/template_surat');
            return;
        }

        $data['title'] = 'Edit Template Surat';
        $data['template'] = $template;
        $data['form_mode'] = 'edit';

        $this->load->view('templates/header', $data);
        $this->load->view('master_surat/template_form', $data);
        $this->load->view('templates/footer');
    }

    public function delete($id)
    {
        $template = $this->Master_surat_model->get_by_id($id);

        if (empty($template)) {
            show_404();
        }

        if (!$this->Master_surat_model->delete($template->id)) {
            $this->session->set_flashdata('error', 'Template surat gagal dihapus.');
            redirect('master_surat/template_surat');
            return;
        }

        if (!empty($template->file_path)) {
            $this->delete_template_file($template->file_path);
        }

        $this->session->set_flashdata('success', 'Template surat berhasil dihapus.');
        redirect('master_surat/template_surat');
    }

    private function upload_template_file($field_name)
    {
        $upload_path = FCPATH . 'assets/uploads/template_surat/';

        if (!is_dir($upload_path)) {
            mkdir($upload_path, 0755, TRUE);
        }

        $config = array(
            'upload_path' => $upload_path,
            'allowed_types' => 'doc|docx',
            'max_size' => 5120,
            'encrypt_name' => TRUE,
            'remove_spaces' => TRUE,
        );

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload($field_name)) {
            return array(
                'status' => FALSE,
                'message' => strip_tags($this->upload->display_errors('', '')),
            );
        }

        $uploaded = $this->upload->data();

        return array(
            'status' => TRUE,
            'file_path' => 'assets/uploads/template_surat/' . $uploaded['file_name'],
            'original_name' => $uploaded['client_name'],
            'file_mime' => $uploaded['file_type'],
            'file_size' => (int) $uploaded['file_size'],
        );
    }

    private function delete_template_file($relative_path)
    {
        if ($relative_path === '') {
            return;
        }

        $absolute_path = FCPATH . ltrim(str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $relative_path), DIRECTORY_SEPARATOR);

        if (is_file($absolute_path)) {
            @unlink($absolute_path);
        }
    }
}
