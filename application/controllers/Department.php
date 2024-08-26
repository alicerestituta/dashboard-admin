<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Department extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model(array('m_department', 'm_division'));
    }

    public function index()
    {
        $data['title'] = 'Department';
        $data['department'] = $this->m_department->get_department_data();
        $data['js'] = 'department';

        $this->load->view('header', $data);
        $this->load->view('department/v_department', $data);
        $this->load->view('footer', $data);
    }
    public function load_data()
    {
        $data['department'] = $this->m_department->get_department_data();
        echo json_encode($data);
    }

    public function load_division() {
        $res['data_division'] = $this->m_division->get_division_data();
        echo json_encode($res);
    }

    public function create()
    {
        if ($this->input->post('txcode') != '') {
            $division = $this->input->post('txdivision');
            $kode = $this->input->post('txcode');
            $nama = $this->input->post('txname');

            $query = $this->db->query("SELECT COUNT(*) as count FROM department WHERE departmentCode = '{$kode}'");
            $result = $query->row();

            if ($result->count > 0) {
                $res['status'] = 'error';
                $res['msg'] = "Kode {$kode} sudah terpakai";
            } else {
                $sql = "INSERT INTO department (departmentDivisionId, departmentName, departmentCode, departmentActive) VALUES ('{$division}','{$nama}','{$kode}', 1)";
                $exc = $this->db->query($sql);

                if ($exc) {
                    $res['status'] = 'success';
                    $res['msg'] = "Simpan data {$nama} berhasil";
                } else {
                    $res['status'] = 'error';
                    $res['msg'] = "Simpan data {$nama} gagal";
                }
            }
            echo json_encode($res);
        }
    }

    public function edit_table()
    {
        $id = $this->input->post('id');
        $sql = $this->db->query("SELECT * FROM department WHERE departmentId = ?", array($id));
        $result = $sql->row_array();
        if ($result > 0) {
            $res['status'] = 'ok';
            $res['data'] = $result;
            $res['msg'] = "Data {$id} sudah ada";
        } else {
            $res['status'] = 'error';
            $res['msg'] = "Data tidak ditemukan";
        }
        echo json_encode($res);
    }
    
    public function update_table()
{
    $id = $this->input->post('id');
    $division = $this->input->post('departmentDivisionId');
    $code = $this->input->post('departmentCode');
    $name = $this->input->post('departmentName');

    // Periksa apakah kode sudah digunakan oleh data lain (kecuali data yang sedang diedit)
    $this->db->where('departmentCode', $code);
    $this->db->where('departmentDelete', 0);
    $this->db->where('departmentId !=', $id); // Pastikan untuk mengabaikan ID yang sedang diedit
    $query_code = $this->db->get('department');

    if ($query_code->num_rows() > 0) {
        $res['status'] = 'error';
        $res['msg'] = "Kode sudah digunakan oleh data lain";
    } else {
        $this->db->where('departmentId', $id);
        $update_data = array(
            'departmentDivisionId' => $division,
            'departmentCode' => $code,
            'departmentName' => $name,
        );

        if ($this->db->update('department', $update_data)) {
            $res['status'] = 'success';
            $res['msg'] = "Data berhasil diperbarui";
        } else {
            $res['status'] = 'error';
            $res['msg'] = "Gagal memperbarui data";
        }
    }

    echo json_encode($res);
}

public function delete_table() {
    $id = $this->input->post("id");

    $this->db->where('employmentDepartmentId', $id);
    $this->db->where('employmentDelete !=', 1);
    $query = $this->db->get('employment');

    if ($query->num_rows() > 0) {
        $res['status'] = 'error';
        $res['msg'] = "Data department sedang digunakan di tabel employment dan tidak dapat dihapus.";
    } else {
        if ($this->m_department->delete_table($id)) {
            $res['status'] = 'success';
            $res['msg'] = "Data department berhasil dihapus.";
        } else {
            $res['status'] = 'error';
            $res['msg'] = "Gagal menghapus data department.";
        }
    }

    echo json_encode($res);
}

}