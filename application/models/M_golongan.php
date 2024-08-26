<?php
class m_golongan extends CI_Model {
    
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function insert_golongan($data) {
        return $this->db->insert('levelgroup', $data);
    }

    public function get_golongan_data() {
        $sql = "SELECT * FROM levelgroup WHERE levelgroupDelete=0 order by levelgroupId desc";
        $query = $this->db->query($sql);
        return $query->result();
    }

    public function delete_table($id) {
        $sql = "UPDATE department SET departmentDelete = 1 WHERE departmentId = '$id'";
        return $this->db->query($sql, array($id));
    }
}