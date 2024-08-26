<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Employee extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model(array('m_employee', 'm_kantor', 'm_bank', 'm_bpjs', 'm_insurance'));
    }

    public function index()
    {
        $data['title'] = 'Employee';
        $data['employee'] = $this->m_employee->get_employee_data();
        $data['js'] = 'employee';

        $this->load->view('header', $data);
        $this->load->view('employee/v_employee', $data);
        $this->load->view('footer', $data);
    }
    public function load_data()
    {
        $data['employee'] = $this->m_employee->get_employee_data();
        echo json_encode($data);
    }

    function valid_info()
    {
        $nama = $this->input->post('val');
        $x = $this->input->post('x');
        $result = null;

        if ($nama != '') {
            if ($x == 1) {
                $query_nama = $this->db->query("SELECT COUNT(*) as count FROM employee WHERE employeeName = '{$nama}'");
                $result = $query_nama->row();
                $ket = 'nama';
            } else if ($x == 2) {
                $query_kode = $this->db->query("SELECT COUNT(*) as count FROM employee WHERE employeeCode = '{$nama}'");
                $result = $query_kode->row();
                $ket = 'code';
            } else if ($x == 3) {
                $query_email = $this->db->query("SELECT COUNT(*) as count FROM employee WHERE employeeEmail = '{$nama}'");
                $result = $query_email->row();
                $ket = 'email';
            } else if ($x == 4) {
                $query_ktp = $this->db->query("SELECT COUNT(*) as count FROM employee WHERE employeeKtp = '{$nama}'");
                $result = $query_ktp->row();
                $ket = 'no KTP';
            } else if ($x == 5) {
                $query_rek = $this->db->query("SELECT COUNT(*) as count FROM employee WHERE employeeBill = '{$nama}'");
                $result = $query_rek->row();
                $ket = 'no rekening';
            } else if ($x == 6) {
                $query_bpjs = $this->db->query("SELECT COUNT(*) as count FROM employee WHERE employeeBpjsNo = '{$nama}'");
                $result = $query_bpjs->row();
                $ket = 'no BPJS';
            } else if ($x == 7) {
                $query_npwp = $this->db->query("SELECT COUNT(*) as count FROM employee WHERE employeeNpwp = '{$nama}'");
                $result = $query_npwp->row();
                $ket = 'no NPWP';
            }
        }

        if ($result && $result->count > 0) {
            $res['status'] = 'error';
            $res['msg'] = $ket . " {$nama} sudah terpakai";
            echo json_encode($res);
        }
    }

    function load_cabang()
    {
        $res['data_kantor'] = $this->m_kantor->get_kantor_data();
        $res['data_bank'] = $this->m_bank->get_bank_data();
        $res['data_bpjs'] = $this->m_bpjs->get_bpjs_data();
        $res['data_insurance'] = $this->m_insurance->get_insurance_data();
        echo json_encode($res);
    }

    public function create()
    {
        $res['cabang'] = $this->input->post('cabang');
        $res['bank'] = $this->input->post('bank');
        $res['nama'] = $this->input->post('nama');
        $res['namalkp'] = $this->input->post('namalkp');
        $res['kode'] = $this->input->post('kode');
        $res['email'] = $this->input->post('email');
        $res['ktp'] = $this->input->post('ktp');
        $res['gender'] = $this->input->post('gender');
        $res['golongan'] = $this->input->post('golongan');
        $res['namaibu'] = $this->input->post('namaibu');
        $res['agama'] = $this->input->post('agama');
        $res['kebangsaan'] = $this->input->post('kebangsaan');
        $res['rekening'] = $this->input->post('rekening');
        $res['gaji'] = $this->input->post('gaji');
        $res['bpjs'] = $this->input->post('bpjs');
        $res['nobpjs'] = $this->input->post('nobpjs');
        $res['npwp'] = $this->input->post('npwp');
        $res['foto'] = $_FILES['foto']['name'];
        $res['tglmasuk'] = date('Y-m-d', strtotime($this->input->post('tglmasuk')));
        $res['tglaktif'] = $this->input->post('tglaktif');
        $res['tglkeluar'] = date('Y-m-d', strtotime($this->input->post('tglkeluar')));
        $alamat = json_decode($this->input->post('alamat'), true);
        $asuransi = json_decode($this->input->post('asuransi'), true);
        $kontak = json_decode($this->input->post('kontak'), true);
        $pendidikan = json_decode($this->input->post('pendidikan'), true);
        $path = $this->upload('foto', 'img-' . time());

        $employee_data = array(
            'employeeClientId' => 0,
            'employeeBranchId' => $res['cabang'],
            'employeeBankId' => $res['bank'],
            'employeeName' => $res['nama'],
            'employeeFullname' => $res['namalkp'],
            'employeeCode' => $res['kode'],
            'employeeEmail' => $res['email'],
            'employeeKtp' => $res['ktp'],
            'employeeGender' => $res['gender'],
            'employeeBlood' => $res['golongan'],
            'employeeMother' => $res['namaibu'],
            'employeeReligion' => $res['agama'],
            'employeeNation' => $res['kebangsaan'],
            'employeeBill' => $res['rekening'],
            'employeeSalaryType' => $res['gaji'],
            'employeeBpjsId' => $res['bpjs'],
            'employeeBpjsNo' => $res['nobpjs'],
            'employeeNpwp' => $res['npwp'],
            'employeePhoto' => $path,
            'employeeInDate' => $res['tglmasuk'],
            'employeeActiveDate' => $res['tglaktif'],
            'employeeOutDate' => $res['tglkeluar'],
            'employeeActive' => 1
        );

        $this->db->insert('employee', $employee_data);
        $employee_id = $this->db->insert_id();

        foreach ($alamat as $addr) {
            $addr['empladdressId'] = $employee_id;
            $this->db->insert('empladdress', $addr);
        }

        foreach ($asuransi as $ins) {
            $ins['emplinsuranceId'] = $employee_id;
            $this->db->insert('emplinsurance', $ins);
        }

        foreach ($kontak as $con) {
            $con['emplcontactId'] = $employee_id;
            $this->db->insert('emplcontact', $con);
        }

        foreach ($pendidikan as $edu) {
            $edu['empleduId'] = $employee_id;
            $this->db->insert('empledu', $edu);
        }

        echo json_encode(array('status' => 'success', 'employee_id' => $employee_id));
    }

    // public function update_table()
    // {
    //     $id = $this->input->post('id');
    //     $insuranceCode = $this->input->post('insuranceCode');
    //     $insuranceName = $this->input->post('insuranceName');
    //     $insuranceNo = $this->input->post('InsuranceNo');
    //     $insuranceType = $this->input->post('InsuranceType');
    //     $insuranceTotalBill = $this->input->post('insuranceTotalBill');
    //     $insuranceSalaryCut = $this->input->post('InsuranceSalaryCut');
    //     $insuranceCompPersen = $this->input->post('insuranceCompPersen');
    //     $insuranceEmplPersen = $this->input->post('insuranceEmplPersen');

    //     // Periksa apakah kode sudah digunakan oleh data lain (kecuali data yang sedang diedit)
    //     $this->db->where('insuranceCode', $insuranceCode);
    //     $this->db->where('insuranceDelete', 0);
    //     $this->db->where('insuranceId !=', $id); // Pastikan untuk mengabaikan ID yang sedang diedit
    //     $query_code = $this->db->get('insurance');

    //     if ($query_code->num_rows() > 0) {
    //         $res['status'] = 'error';
    //         $res['msg'] = "Kode sudah digunakan oleh data lain";
    //     } else {
    //         $this->db->where('insuranceId', $id);
    //         $update_data = array(
    //             'insuranceCode' => $insuranceCode,
    //             'insuranceName' => $insuranceName,
    //             'insuranceNo' => $insuranceNo,
    //             'insuranceType' => $insuranceType,
    //             'insuranceTotalBill' => $insuranceTotalBill,
    //             'insuranceSalaryCut' => $insuranceSalaryCut,
    //             'insuranceCompPersen' => $insuranceCompPersen,
    //             'insuranceEmplPersen' => $insuranceEmplPersen
    //         );

    //         if ($this->db->update('insurance', $update_data)) {
    //             $res['status'] = 'success';
    //             $res['msg'] = "Data berhasil diperbarui";
    //         } else {
    //             $res['status'] = 'error';
    //             $res['msg'] = "Gagal memperbarui data";
    //         }
    //     }

    //     echo json_encode($res);
    // }

    public function edit_table()
    {
        $id = $this->input->post('id');
        $employee = $this->db->query("SELECT * FROM employee WHERE employeeId = ?", array($id))->row_array();
        $address = $this->db->query("SELECT * FROM empladdress WHERE empladdressDelete=0 and empladdressEmployeeId = ?", array($id))->result_array();
        $insurance = $this->db->query("SELECT * FROM emplinsurance WHERE emplinsuranceEmployeeId = ?", array($id))->result_array();
        $contact = $this->db->query("SELECT * FROM emplcontact WHERE emplcontactEmployeeId = ?", array($id))->result_array();
        $education = $this->db->query("SELECT * FROM empledu WHERE empleduEmployeeId = ?", array($id))->result_array();

        if ($employee) {
            $res['status'] = 'ok';
            $res['data'] = [
                'employee' => $employee,
                'address' => $address ?: null,
                'insurance' => $insurance ?: null,
                'contact' => $contact ?: null,
                'education' => $education ?: null,
            ];
            $res['msg'] = "Data {$id} found";
        } else {
            $res['status'] = 'error';
            $res['msg'] = "Code {$id} not found";
        }

        echo json_encode($res);
    }

    public function update_employee()
    {
        $id = $this->input->post('id');
        $alamat = json_decode($this->input->post('empladdress'), true);
        $asuransi = json_decode($this->input->post('emplinsuranse'), true);
        $kontak = json_decode($this->input->post('emplcontact'), true);
        $pendidikan = json_decode($this->input->post('empledu'), true);

        $employeeData = array(
            'employeeBranchId' => $this->input->post('employeeBranchId'),
            'employeeBankId' => $this->input->post('employeeBankId'),
            'employeeName' => $this->input->post('employeeName'),
            'employeeFullname' => $this->input->post('employeeFullname'),
            'employeeCode' => $this->input->post('employeeCode'),
            'employeeEmail' => $this->input->post('employeeEmail'),
            'employeeKtp' => $this->input->post('employeeKtp'),
            'employeeGender' => $this->input->post('employeeGender'),
            'employeeBlood' => $this->input->post('employeeBlood'),
            'employeeMother' => $this->input->post('employeeMother'),
            'employeeReligion' => $this->input->post('employeeReligion'),
            'employeeNation' => $this->input->post('employeeNation'),
            'employeeBill' => $this->input->post('employeeBill'),
            'employeeSalaryType' => $this->input->post('employeeSalaryType'),
            'employeeBpjsId' => $this->input->post('employeeBpjsId'),
            'employeeBpjsNo' => $this->input->post('employeeBpjsNo'),
            'employeeNpwp' => $this->input->post('employeeNpwp'),
            'employeeInDate' => $this->input->post('employeeInDate'),
            'employeeActiveDate' => $this->input->post('employeeActiveDate'),
            'employeeOutDate' => $this->input->post('employeeOutDate'),
        );

        $this->db->where('employeeId', $id);
        if ($this->db->update('employee', $employeeData)) {
            $employee_id = $id;

            // Update address
            if (is_array($alamat)) {
                $this->db->where('empladdressEmployeeId', $employee_id);
                $this->db->update('empladdress', array('empladdressDelete' => 1)); // Update column if exists

                foreach ($alamat as $addr) {
                    $col = array(
                        'empladdressEmployeeId' => $employee_id,
                        'empladdressJalan' => $addr['empladdressJalan'],
                        'empladdressKecamatan' => $addr['empladdressKecamatan'],
                        'empladdressKelurahan' => $addr['empladdressKelurahan'],
                        'empladdressKota' => $addr['empladdressKota'],
                        'empladdressPhone' => $addr['empladdressPhone'],
                        'empladdressProvinsi' => $addr['empladdressProvinsi']
                    );
                    $this->db->insert('empladdress', $col);
                }
            }

            // Update insurance
            if (is_array($asuransi)) {
                $this->db->where('emplinsuranceEmployeeId', $employee_id);
                $this->db->update('emplinsurance', array('emplinsuranceDelete' => 1)); // Update column if exists

                foreach ($asuransi as $ins) {
                    $col = array(
                        'emplinsuranceEmployeeId' => $employee_id,
                        'emplinsuranceBpjsId' => $ins['emplinsuranceBpjsId'],
                        'emplinsuranceNo' => $ins['emplinsuranceNo']
                    );
                    $this->db->insert('emplinsurance', $col);
                }
            }

            // Update contact
            if (is_array($kontak)) {
                $this->db->where('emplcontactEmployeeId', $employee_id);
                $this->db->update('emplcontact', array('emplcontactDelete' => 1)); // Update column if exists

                foreach ($kontak as $con) {
                    $col = array(
                        'emplcontactEmployeeId' => $employee_id,
                        'emplcontactName' => $con['emplcontactName'],
                        'emplcontactAddress' => $con['emplcontactAddress'],
                        'emplcontactProfesion' => $con['emplcontactProfesion'],
                        'emplcontactHubungan' => $con['emplcontactHubungan'],
                        'emplcontactPhone' => $con['emplcontactPhone']
                    );
                    $this->db->insert('emplcontact', $col);
                }
            }

            // Update education
            if (is_array($pendidikan)) {
                $this->db->where('empleduEmployeeId', $employee_id);
                $this->db->update('empledu', array('empleduDelete' => 1)); // Update column if exists

                foreach ($pendidikan as $edu) {
                    $col = array(
                        'empleduEmployeeId' => $employee_id,
                        'empleduJenjang' => $edu['empleduJenjang'],
                        'empleduInstansi' => $edu['empleduInstansi'],
                        'empleduJurusan' => $edu['empleduJurusan'],
                        'empleduTahunlulus' => $edu['empleduTahunlulus']
                    );
                    $this->db->insert('empledu', $col);
                }
            }

            $res['status'] = 'success';
            $res['msg'] = "Data berhasil diperbarui";
        } else {
            $res['status'] = 'error';
            $res['msg'] = "Gagal memperbarui data";
        }

        echo json_encode($res);
    }


    public function delete()
    {
        $id = $this->input->post('id');
        if ($this->m_employee->delete_employee($id)) {
            $res['status'] = 'success';
            $res['msg'] = "Data kantor berhasil dihapus!";
        } else {
            $res['status'] = 'error';
            $res['msg'] = "Gagal menghapus data kantor.";
        }
        echo json_encode($res);
    }

    function upload($field, $filename)
    {
        $this->load->library('upload');
        $config['upload_path'] = './uploads/employee';
        if (!is_dir($config['upload_path'])) {
            mkdir($config['upload_path'], 0777, TRUE);
        }
        $config['allowed_types'] = '*';
        $config['file_name'] = $filename;
        $path = substr($config['upload_path'], 1);
        $this->upload->initialize($config);
        if ($this->upload->do_upload($field)) {
            $this->img_resize('.' . $path . '/' . $this->upload->data('file_name'));
            return $path . '/' . $this->upload->data('file_name');
        } else {
            return null;
        }
    }

    function img_resize($file)
    {
        $config['image_library']  = 'gd2';
        $config['source_image']   = $file;
        $config['create_thumb']   = FALSE;
        $config['quality']        = '70%';
        $config['width']          = '315';
        $config['height']         = '1';
        $config['maintain_ratio'] = TRUE;
        $config['master_dim']     = 'width';
        $config['new_image']      = $file;
        $this->load->library('image_lib', $config);
        $done = $this->image_lib->resize();
        $this->image_lib->clear();
        return $done;
    }

    // function import_excel()
    // {
    //     if (isset($_FILES["file"])) {
    //         //-- upload file excel ke folder temporary
    //         $rand             = rand();
    //         $path_part    =    pathinfo($_FILES["file"]["name"]);
    //         $ext                =    $path_part["extension"];
    //         $filename        =    "inject_" . $rand . "." . $ext;

    //         $config['upload_path']       = "tmp/xls/";
    //         $config['allowed_types']     = '*';
    //         $config['file_name']             = $filename;
    //         $this->load->library('upload', $config);
    //         $this->upload->do_upload('file');

    //         //-- lokasi file temporary
    //         $path_file     = "tmp/xls/" . $filename;

    //         error_reporting(E_ALL);
    //         $this->load->library(array('PHPExcel', 'PHPExcel/IOFactory'));
    //         try {
    //             $inputFileType  = IOFactory::identify($path_file);
    //             $objReader      = IOFactory::createReader($inputFileType);
    //             $objPHPExcel    = $objReader->load($path_file);
    //         } catch (Exception $e) {
    //             die('Error loading file "' . pathinfo($path_file, PATHINFO_BASENAME) . '": ' . $e->getMessage());
    //         }
    //         $sheet          = $objPHPExcel->getSheet(0);
    //         $highestRow     = $sheet->getHighestRow();
    //         $highestColumn  = $sheet->getHighestColumn();

    //         for ($row = 1; $row <= $highestRow; $row++) {
    //             $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
    //             if ($rowData) {
    //                 //  echo $rowData[0][0].".".$rowData[0][1].".".$rowData[0][2]. "<br>";

    //                 $data_excel = array(
    //                     'employeeClientId' => 0,
    //                     'employeeBranchId' => $rowData[0][0],
    //                     'employeeBankId' => $rowData[0][1],
    //                 //     'employeeCode' => $rowData[0][3],
    //                 //     'employeeName' => $rowData[0][4],
    //                 //     'employeeFullname' => $rowData[0][5],
    //                 //     'employeeEmail' => $rowData[0][6],
    //                 //     'employeeKtp' => $rowData[0][7],
    //                 //     'employeeGender' => $rowData[0][8],
    //                 //     'employeeBlood' => $rowData[0][9],
    //                 //     'employeeMother' => $rowData[0][10],
    //                 //     'employeeReligion' =>$rowData[0][11],
    //                 //     'employeeNation' => $rowData[0][12],
    //                 //     'employeeBill' => $rowData[0][13],
    //                 //     'employeeInDate' => $rowData[0][14],
    //                 //     'employeeActiveDate' => $rowData[0][15],
    //                 //     'employeeNpwp' => $rowData[0][16],
    //                 //     // 'empladdressJalan' => $rowData[0][17],
    //                 //     // 'empladdressKelurahan' => $rowData[0][18],
    //                 //     // 'empladdressKecamatan' => $rowData[0][19],
    //                 //     // 'empladdressKota' => $rowData[0][20],
    //                 //     // 'empladdressProvinsi' => $rowData[0][21],
    //                 //     // 'emplcontactPhone' => $rowData[0][22],
    //                 //     // 'empladdressProvinsi' => $rowData[0][21],
    //                 //     // 'employeeBpjsId' => $rowData[0][22],
    //                 //     // 'employeeBpjsNo' => $rowData[0][23],
    //                 //     // 'emplcontactName' => $rowData[0][24],
    //                 //     // 'emplcontactAddress' => $rowData[0][25],
    //                 //     // 'emplcontactProfesion' => $rowData[0][26],
    //                 //     // 'emplcontactHubungan' => $rowData[0][27],
    //                 //     // 'emplcontactPhone' => $rowData[0][28],
    //                 //     // 'empleduJenjang' => $rowData[0][29],
    //                 //     // 'empleduInstansi' => $rowData[0][30],
    //                 //     // 'empleduJurusan' => $rowData[0][31],
    //                 //     // 'empleduTahunLulus' => $rowData[0][32],
    //                 //     'employeeActive' => 1
    //                 );

    //                 $this->db->insert('employee', $data_excel);
    //             }
    //         }
    //     }
    // }

    function import_excel()
    {
        if (isset($_FILES["file"])) {
            $rand = rand();
            $path_part = pathinfo($_FILES["file"]["name"]);
            $ext = $path_part["extension"];
            $filename = "inject_" . $rand . "." . $ext;

            $config['upload_path'] = "tmp/xls/";
            $config['allowed_types'] = '*';
            $config['file_name'] = $filename;
            $this->load->library('upload', $config);
            $this->upload->do_upload('file');

            $path_file = "tmp/xls/" . $filename;
            error_reporting(0);
            $this->load->library(array('PHPExcel', 'PHPExcel/IOFactory'));
            try {
                $inputFileType  = IOFactory::identify($path_file);
                $objReader      = IOFactory::createReader($inputFileType);
                $objPHPExcel    = $objReader->load($path_file);
            } catch (Exception $e) {
                die('Error loading file "' . pathinfo($path_file, PATHINFO_BASENAME) . '": ' . $e->getMessage());
            }
            $sheet          = $objPHPExcel->getSheet(0);
            $highestRow     = $sheet->getHighestRow();
            $highestColumn  = $sheet->getHighestColumn();

            for ($row = 2; $row <= $highestRow; $row++) {
                $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
                if ($rowData) {
                    //   echo $rowData[0][0].".".$rowData[0][1].".".$rowData[0][2]. "<br>";
                    $tglin = $this->tgl_excel($rowData[0][13]);
                    $tglout = $this->tgl_excel($rowData[0][14]);
                    $sql = "SELECT branchId FROM branch WHERE branchName = '{$rowData[0][0]}' AND branchDelete = 0";
                    $branchId = $this->db->query($sql)->row()->branchId;
                    $sql = "SELECT bankId FROM bank WHERE bankName = '{$rowData[0][1]}' AND bankDelete = 0";
                    $bankId = $this->db->query($sql)->row()->bankId;
                    $sql = "SELECT bpjsId FROM bpjs WHERE bpjsName = '{$rowData[0][17]}' AND bpjsDelete = 0";
                    $bpjsId = $this->db->query($sql)->row()->bpjsId;
                    $dataEmployee = array(
                        'employeeClientId' => 0,
                        'employeeBranchId' => $branchId,
                        'employeeBankId' => $bankId,
                        'employeeBpjsId' => $rowData[0][17],
                        // 'employeeBpjsNo' => 
                        'employeeCode' => $rowData[0][2],
                        'employeeName' => $rowData[0][3],
                        'employeeFullname' => $rowData[0][4],
                        'employeeEmail' => $rowData[0][5],
                        'employeeKtp' => $rowData[0][6],
                        'employeeGender' => $rowData[0][7],
                        'employeeBlood' => $rowData[0][8],
                        'employeeMother' => $rowData[0][9],
                        'employeeReligion' => $rowData[0][10],
                        'employeeNation' => $rowData[0][11],
                        'employeeBill' => $rowData[0][12],
                        // 'employeeSalaryType' => $rowData[0][17],
                        // 'employeeInDate' => date('Y-m-d', strtotime($tglin)),
                        // 'employeeActiveDate' => date('Y-m-d', strtotime($tglout)),
                        'employeeNpwp' => $rowData[0][15],
                        'employeeActive' => 1,
                    );
                    $this->db->insert('employee', $dataEmployee);
                    $employee_id = $this->db->insert_id();

                     //-- Impor Alamat
                     $sheet2         = $objPHPExcel->getSheet(1);
                     $highestRow2    = $sheet2->getHighestRow();
                     $highestColumn2  = $sheet2->getHighestColumn();
 
                     for ($row2 = 2; $row2 <= $highestRow2; $row2++) {
                         $rowData2 = $sheet2->rangeToArray('A' . $row2 . ':' . $highestColumn2 . $row2, NULL, TRUE, FALSE);
                         if ($rowData2) {
                             if ($rowData2[0][0] == $rowData[0][2]) {
                                 $dataAlamat = array(
                                    'empladdressEmployeeId' => $employee_id,
                                    'empladdressJalan' => $rowData2[0][1],
                                    'empladdressKecamatan' => $rowData2[0][2],
                                    'empladdressKelurahan' => $rowData2[0][3],
                                    'empladdressKota' => $rowData2[0][4],
                                    'empladdressPhone' => $rowData2[0][5],
                                    'empladdressProvinsi' => $rowData2[0][6],
                                 );
                                 $this->db->insert('empladdress', $dataAlamat);
                             }
                         }
                     }
                    
                    //-- Impor Asuransi
                    $sheet3          = $objPHPExcel->getSheet(2);
                    $highestRow3    = $sheet3->getHighestRow();
                    $highestColumn3  = $sheet3->getHighestColumn();

                    for ($row3 = 2; $row3 <= $highestRow3; $row3++) {
                        $rowData3 = $sheet3->rangeToArray('A' . $row3 . ':' . $highestColumn3 . $row3, NULL, TRUE, FALSE);
                        if ($rowData3) {
                            if ($rowData3[0][0] == $rowData[0][2]) {
                                $dataAsuransi = array(
                                    'emplinsuranceEmployeeId' => $employee_id,
                                    'emplinsuranceBpjsId' => $rowData3[0][1],
                                    'emplinsuranceNo' =>  $rowData3[0][2],
                                );
                                $this->db->insert('emplinsurance', $dataAsuransi);
                            }
                        }
                    }

                    //-- Impor Kontak
                    $sheet4          = $objPHPExcel->getSheet(3);
                    $highestRow4    = $sheet4->getHighestRow();
                    $highestColumn4  = $sheet4->getHighestColumn();

                    for ($row4 = 2; $row4 <= $highestRow4; $row4++) {
                        $rowData4 = $sheet4->rangeToArray('A' . $row4 . ':' . $highestColumn4 . $row4, NULL, TRUE, FALSE);
                        if ($rowData4) {
                            if ($rowData4[0][0] == $rowData[0][2]) {
                                $dataKontak = array(
                                   'emplcontactEmployeeId' => $employee_id,
                                    'emplcontactName' => $rowData4[0][1],
                                    'emplcontactAddress' => $rowData4[0][2],
                                    'emplcontactProfesion' => $rowData4[0][3],
                                    'emplcontactHubungan' => $rowData4[0][4],
                                    'emplcontactPhone' => $rowData4[0][5],
                                );
                                $this->db->insert('emplcontact', $dataKontak);
                            }
                        }
                    }

                    //-- Impor Pendidikan
                    $sheet5          = $objPHPExcel->getSheet(4);
                    $highestRow5    = $sheet5->getHighestRow();
                    $highestColumn5  = $sheet5->getHighestColumn();

                    for ($row5 = 2; $row5 <= $highestRow5; $row5++) {
                        $rowData5 = $sheet5->rangeToArray('A' . $row5 . ':' . $highestColumn5 . $row5, NULL, TRUE, FALSE);
                        if ($rowData5) {
                            if ($rowData5[0][0] == $rowData[0][2]) {
                                $dataPendidikan = array(
                                    'empleduEmployeeId' => $employee_id,
                                    'empleduJenjang' => $rowData5[0][1],
                                    'empleduInstansi' =>  $rowData5[0][2],
                                    'empleduJurusan' =>  $rowData5[0][3],
                                    'empleduTahunlulus' =>  $rowData5[0][4],
                                );
                                $this->db->insert('empledu', $dataPendidikan);
                            }
                        }
                    }
                }
                
            }
        }
    }

    function tgl_excel($tg)
    {
        $unix_date = ($tg - 25569) * 86400;
        $tg = 25569 + ($unix_date / 86400);
        $unix_date = ($tg - 25569) * 86400;
        $tgl = gmdate("Y-m-d", $unix_date);
        return $tgl;
    }

    function exportExcel()
    {
        $sql = "SELECT branchName, employeeCode, employeeName, bankName, bpjsName FROM employee 
        JOIN branch ON branchId = employeeBranchId 
        JOIN bank ON bankId = employeeBankId 
        JOIN bpjs ON bpjsId = employeeBpjsId 
        ORDER BY employeeCode, employeeName";
        $res['data'] = $this->db->query($sql)->result_array();
        $res['filename'] = 'dataPegawai-'.date('Y-m-d_H-i-s');
        $output = $this->load->view("employee/v_export_excel", $res, true);
        echo $output;
    }
}
