<?php
/**
 * Created by PhpStorm.
 * User: Alexis
 * Date: 14/03/2018
 * Time: 17:49
 */

class Login_model extends CI_Model{
    private static $db;
    public function __construct(){
        self::$db =&get_instance()->db;
    }

    // récupération de tous les utilisateurs
    public function get_users(){
        $query = $this->db->get('users');
        return $query->result_array();
    }


    // requête pour le login
    public function get_user($data){

        $condition = "login="."'".$data['username']."'";
        $this->db->select('*');
        $this->db->from('users');
        $this->db->where($condition);
        $this->db->limit(1);
        $query = $this->db->get();
        if($query->num_rows() == 1){ // login success
            foreach ($query->result_array() as $row)
            {
                return $row; //return user
            }
        }
        else { // login failed
            return false;
        }
    }


    // requête pour le login
    static function get_userByUsername($username){

        $condition = "login="."'".$username."'";
        self::$db->select('*');
        self::$db->from('users');
        self::$db->where($condition);
        self::$db->limit(1);
        $query = self::$db->get();
        if($query->num_rows() == 1){ // login success
            foreach ($query->result_array() as $row)
            {
                return $row; //return user
            }
        }
        else { // login failed
            return FALSE;
        }
    }


    // ajout d'un nouvel utilisateur
    public function registration_insert($data, $role) {

        // Empêche 2 mêmes noms d'utilisateur, ajoute l'utilisateur
        $condition = "login =" . "'" . $data['login'] . "'";
        $this->db->select('*');
        $this->db->from('users');
        $this->db->where($condition);
        $this->db->limit(1);
        $query = $this->db->get();
        if ($query->num_rows() == 0) {
            $this->db->insert('users', $data);
            if ($this->db->affected_rows() <= 0) {
                return false;
            }
        }
        $datas = array(
            'role_id' => $role,
            'user_id' => $data['login'],
        );
        // maintenant on doit ajouter à user_role
        $this->db->insert('user_role', $datas);
    }


    public function update_password($user, $pwd){
        $data = array(
            'pwd' => $pwd,
        );
        $this->db->where('login',$user);
        $this->db->update('users',$data);
    }


    public function check_user_mail($user, $mail){
        $condition = "login="."'".$user."' and mail ="."'".$mail."'";
        $this->db->select('*');
        $this->db->from('users');
        $this->db->where($condition);
        $this->db->limit(1);
        $query = $this->db->get();
        if($query->num_rows() == 1) { // success
            return true;
        }
        else { // failure
            return false;
        }
    }


    public function check_user_secret($user, $secret){
        $condition = "login="."'".$user."' and secret ="."'".$secret."'";
        $this->db->select('*');
        $this->db->from('users');
        $this->db->where($condition);
        $this->db->limit(1);
        $query = $this->db->get();
        if($query->num_rows() == 1) { // success
            return true;
        }
        else { // failure
            return false;
        }
    }


    public function updateTimestampAttempt($username, $time) {
        $data = array(
            'lastConnectionOrAttempt' => $time
        );
        $this->db->where('login',$username);
        $this->db->update('users',$data);
    }


    public function incrementFailedAttempt($username, $userObject, $lockingBoolean){
        $data = array(
            'failedAttempt' => $userObject['failedAttempt']+1,
            'banned' => FALSE,
        );
        //If 3 failed attempts, and admin specified in settings that accoutn should be banned
        if($data['failedAttempt']>=3 && $lockingBoolean){
            $data['banned'] = TRUE;
        }
        $this->db->where('login',$username);
        $this->db->update('users',$data);
    }
}