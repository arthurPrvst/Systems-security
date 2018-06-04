<?php
/**
 * Created by PhpStorm.
 * User: Alexis
 * Date: 24/03/2018
 * Time: 17:19
 */

    class User extends CI_Model {
        private $roles;
        private static $db;
        public function __construct(){
            parent::__construct();
            self::$db =&get_instance()->db;
        }


        // override User method
        public static function getByUsername($username) {
            $condition = "login="."'".$username."'";
            self::$db->select('*');
            self::$db->from('users');
            self::$db->where($condition);
            self::$db->limit(1);
            $query = self::$db->get();
            $result = $query->result_array();

            if (!empty($result)) {
                $privUser = new User();
                $privUser->username = $username;
                $privUser->password = $result[0]["pwd"];
                $privUser->email_addr = $result[0]["mail"];
                $privUser->initRoles();
                return $privUser;
            } else {
                return FALSE;
            }
        }

        public static function getRole($username) {
            $condition = "user_id="."'".$username."'";
            self::$db->select('role_id');
            self::$db->from('user_role');
            self::$db->where($condition);
            $query = self::$db->get();
            $result = $query->result_array();

            $minRole = 4;
            foreach ($result as $role){
                $minRole = min($minRole, $role['role_id']);
            }
            return $minRole;
        }

        // populate roles with their associated permissions
        protected function initRoles() {
            $this->roles = array();

            //$result = UsersRoles_model::get_usersRolesById($this->username);
            $condition = "user_role.user_id="."'".$this->username."'";
            self::$db->select('user_role.role_id, roles.role_name');
            self::$db->from('user_role');
            self::$db->join('roles','user_role.role_id = roles.role_id');
            self::$db->where($condition);
            $query = self::$db->get();
            $result = $query->result_array();
            foreach($result as $ro){
                $this->roles[$ro["role_name"]]= Role::getRolePerms($ro["role_id"]);
            }

           // // on lui donne les roles et ses persmissions associées.
            //while($row = $sth->fetch(PDO::FETCH_ASSOC)) {
             //   $this->roles[$row["role_name"]] = Role::getRolePerms($row["role_id"]);
            //}
        }

        // check if user has a specific privilege
        public function hasPrivilege($perm) {
            foreach ($this->roles as $role) {
                if ($role->hasPerm($perm)) {
                    return true;
                }
            }
            return false;
        }
    }