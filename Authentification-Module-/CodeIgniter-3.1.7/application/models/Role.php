<?php
/**
 * Created by PhpStorm.
 * User: Alexis
 * Date: 24/03/2018
 * Time: 16:58
 */
class Role extends CI_Model
{
    protected $permissions;
    private static $db;
    public function __construct(){
        parent::__construct();
        self::$db =&get_instance()->db;
        $this->permissions=array();

    }

    // return a role object with associated permissions
    public static function getRolePerms($role_id) {
        $role = new Role();


        self::$db->select('perm_desc');
        self::$db->from('role_perm');
        self::$db->join('permissions','role_perm.perm_id = permissions.perm_id');
        self::$db->where('role_perm.role_id = '.$role_id);
        $query = self::$db->get();
        $sth = $query->result_array();

        // normalement on choppe les permissions de ce role...
        foreach($sth as $perm){
            $role->permissions[$perm["perm_desc"]] = true;
        }
        return $role;
    }

    // check if a permission is set
    public function hasPerm($permission) {
        return isset($this->permissions[$permission]);
    }
}