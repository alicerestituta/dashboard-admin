<?php
class M_model extends CI_Model {
    
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function insert_bank($data) {
        return $this->db->insert('bank', $data);
    }

    public function get_all_banks() {
        $query = $this->db->get('bank');
        return $query->result();
    }
}
