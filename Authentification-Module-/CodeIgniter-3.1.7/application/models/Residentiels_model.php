<?php
/**
 * Created by PhpStorm.
 * User: Alexis
 * Date: 14/03/2018
 * Time: 17:49
 */

class Residentiels_model extends CI_Model{
    public function __construct(){
        $this->load->database();
    }

    public function get_residentiels(){
        $query = $this->db->get('clientsresidentiels');
        return $query->result_array();
    }
}