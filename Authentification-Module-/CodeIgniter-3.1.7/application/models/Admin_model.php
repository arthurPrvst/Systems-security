<?php
/**
 * Created by PhpStorm.
 * User: Alexis
 * Date: 14/03/2018
 * Time: 17:49
 */

class Admin_model extends CI_Model{

    private static $db;
    public function __construct(){
        self::$db =&get_instance()->db;
        $this->load->model('Login_model');

    }


    public function update_password_policy($data){

        // Remove former password_policy in db
        $this->db->empty_table('password_policy');
        // Add new password_policy in db
        $this->db->insert('password_policy', $data);
    }


    public function update_password_management($forgotten){

        // Remove former password_management in db
        $this->db->empty_table('password_management');
        // Add new password_management in db
        $this->db->insert('password_management', $forgotten);
    }


    public function get_password_policy(){

        $this->db->select('*');
        $this->db->from('password_policy');
        $this->db->limit(1);
        $query = $this->db->get();
        if($query->num_rows() == 1){ // it should have only 1 password policy
            foreach ($query->result_array() as $row)
            {
                return $row; //return user
            }
        }
        else { // login failed
            return false;
        }
    }


    public function get_password_management(){

        $this->db->select('forgotten');
        $this->db->from('password_management');
        $this->db->limit(1);
        $query = $this->db->get();
        if($query->num_rows() == 1){ // it should have only 1 password policy
            foreach ($query->result_array() as $row)
            {
                return $row; //return user
            }
        }
        else { // login failed
            return false;
        }
    }


    public function get_bruteforce_policy(){

        $this->db->select('*');
        $this->db->from('bruteforce_policy');
        $this->db->limit(1);
        $query = $this->db->get();
        if($query->num_rows() == 1){ // it should have only 1 password policy
            foreach ($query->result_array() as $row)
            {
                return $row; //return user
            }
        }
        else { // login failed
            return false;
        }
    }


    public function update_bruteforce_policy($data){
        // Remove former bruteforce_policy in db
        $this->db->empty_table('bruteforce_policy');
        // Add new bruteforce_policy in db
        $this->db->insert('bruteforce_policy', $data);

        //If banning account is set, reset of failed attempts of all user to 0
        if($data['lockingAccount']==1){
            $users = $this->Login_model->get_users();
            foreach ($users as $user => $value) {
                $data = array(
                    'login' => $value['login'],
                    'pwd'  => $value['pwd'],
                    'name'  => $value['name'],
                    'surname'  => $value['surname'],
                    'mail'  => $value['mail'],
                    'codepermanent'  => $value['codepermanent'],
                    'secret'  => $value['secret'],
                    'lastConnectionOrAttempt'  => $value['lastConnectionOrAttempt'],
                    'failedAttempt'  => 0, //Reset
                    'banned'  => $value['banned'],
                );
                $this->db->replace('users', $data);
            }
            
        }
    }
}