<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Golongan extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model(array('m_golongan'));
    }

    public function index()
    {
        $data['title'] = 'Golongan';
        $data['levelgroup'] = $this->m_golongan->get_golongan_data();
        $data['js'] = 'golongan';

        $this->load->view('header', $data);
        $this->load->view('golongan/v_golongan', $data);
        $this->load->view('footer', $data);
    }

    public function load_data()
    {
        $data['levelgroup'] = $this->m_golongan->get_golongan_data();
        echo json_encode($data);
    }


    public function create()
    {
        if ($this->input->post('txkode') != '') {
            $kode = $this->input->post('txkode');
            $nama = $this->input->post('txnama');
            $nominal = $this->input->post('txnominal');
            $total_hari = $this->input->post('txtotalhari');
            $nominal_perhari = $this->input->post('txnominalperhari');
            $setengah = $this->input->post('txsetengah');
            $persen = $this->input->post('txpersen');
            $pokok = $this->input->post('txpokok');

            $query = $this->db->query("SELECT COUNT(*) as count FROM levelgroup WHERE levelgroupCode = '{$kode}'");
            $result = $query->row();

            if ($result->count > 0) {
                $res['status'] = 'error';
                $res['msg'] = "Kode {$kode} sudah terpakai";
            } else {
               $sql = "INSERT INTO levelgroup (levelgroupCode, levelgroupName, levelgroupAmount, levelgroupDivide, levelgroupNominal, levelgroupHalfDay, levelgroupHalfPercent, levelgroupHalfAmount, levelgroupActive) VALUES 
                ('{$kode}','{$nama}', '{$nominal_perhari}' ,'{$total_hari}','{$nominal}' ,'{$setengah}' ,'{$persen}' ,'{$pokok}', 1)";
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
        $sql = $this->db->query("SELECT * FROM employment WHERE employmentId = ?", array($id));
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
    $department = $this->input->post('employmentDepartmentId');
    $atasan = $this->input->post('employmentParentEmploymentId');
    $kode = $this->input->post('employmentCode');
    $nama = $this->input->post('employmentName');

    $this->db->where('employmentCode', $kode);
    $this->db->where('employmentDelete', 0);
    $this->db->where('employmentId !=', $id); 
    $query_code = $this->db->get('employment');


    if ($query_code->num_rows() > 0) {
        $res['status'] = 'error';
        $res['msg'] = "Kode sudah digunakan oleh data lain";
    } else {
        $this->db->where('employmentId', $id);
        $update_data = array(
            'employmentDepartmentId' => $department,
            'employmentParentEmploymentId' => $atasan,
            'employmentCode' => $kode,
            'employmentName' => $nama,
        );

        if ($this->db->update('employment', $update_data)) {
            $res['status'] = 'success';
            $res['msg'] = "Data berhasil diperbarui";
        } else {
            $res['status'] = 'error';
            $res['msg'] = "Gagal memperbarui data";
        }
    }

    echo json_encode($res);
}

public function delete_table()
{
    $id = $this->input->post("id");
    $this->db->where('employmentParentEmploymentId', $id);
    $this->db->where('employmentDelete !=', 1);
    $query = $this->db->get('employment');

    if ($query->num_rows() > 0) {
        $res['status'] = 'error';
        $res['msg'] = "Data  sudah digunakan sebagai atasan";
    } else {
        if ($this->m_employment->delete_table($id)) {
            $res['status'] = 'success';
            $res['msg'] = "Data  berhasil di hapus";
        } else {
            $res['status'] = 'error';
            $res['msg'] = 'Gagal menghapus data';
        }
    }
    echo json_encode($res);
}

}


