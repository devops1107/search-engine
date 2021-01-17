<?php

namespace spark\models;

use spark\models\UserModel;

/**
 * RoleModel
 *
 * Manages user roles, duh.
 *
 * @package spark
 */
class RoleModel extends Model
{
    /**
     * Quick reference to Admin Role ID
     *
     * @var integer
     */
    const TYPE_ADMIN = 1;

    /**
     * Quick reference to Mod Role ID
     *
     * @var integer
     */
    const TYPE_MOD   = 2;

    /**
     * Quick reference to User Role ID
     *
     * @var integer
     */
    const TYPE_USER  = 3;

    /**
     * Sorting rules
     *
     * @var array
     */
    protected $sortRules = [
        'protected' => ['is_protected' => 'DESC'],
        'custom' => ['is_protected' => 'ASC'],
        'newest' => ['created_at' => 'DESC'],
        'oldest' => ['created_at' => 'ASC'],
        'a2z'    => ['role_name'  => 'ASC'],
        'z2a'    => ['role_name'  => 'DESC'],
    ];


    protected $autoTimestamp = true;

    /**
     * Table name
     *
     * @var string
     */
    protected static $table = 'roles';

    /**
     * Query key
     *
     * @var string
     */
    protected $queryKey = 'role_id';

    /**
     * List of role specific permissions
     *
     * @var array
     */
    protected $rolePerms = [];

    /**
     * List of all permissions
     *
     * @var array
     */
    protected $permissions;


    /**
     * Returns all available permissions as an perm_id => perm_desc formatted array
     *
     * First call is cached in a variable
     *
     * @return array
     */
    public function getAllPermissions()
    {
        if (is_array($this->permissions)) {
            return $this->permissions;
        }

        $db = app()->db;
        $table = Model::getPrefix('permissions');
        $sql = $db->select()->from($table);
        $stmt = $sql->execute();
        $perms = [];

        foreach ($stmt->fetchAll() as $row) {
            $perms[$row['perm_id']] = $row['perm_desc'];
        }

        $this->permissions = $perms;

        return $this->permissions;
    }

    /**
     * Add a new permission
     *
     * @param string $name
     */
    public function addPermission($name)
    {
        $permID = $this->getPermission($name);

        if ((int) $permID) {
            return $permID;
        }
        
        $db = app()->db;
        $table = Model::getPrefix('permissions');
        return $db->insert(['perm_desc' => $name])
                   ->into($table)
                   ->execute();
    }

    public function getPermission($name)
    {
        $db = app()->db;
        $table = Model::getPrefix('permissions');
        $perm = $db->select(['perm_id'])
                   ->from($table)
                   ->where('perm_desc', '=', $name)
                   ->execute()
                   ->fetch();

        if (isset($perm['perm_id'])) {
            return $perm['perm_id'];
        }

        return false;
    }

    /**
     * Get permissions list for a specific role as perm_name => perm_id format
     *
     * SQL query is performed once in a request for a role id, the latter will serve the cached result
     *
     * @param  integer $roleID
     * @return array
     */
    public function getRolePerms($roleID)
    {
        // Why waste SQL queries?
        if (isset($this->rolePerms[$roleID])) {
            return $this->rolePerms[$roleID];
        }

        $table1 = Model::getPrefix('role_perm');
        $table2 = Model::getPrefix('permissions');

        $sql = "SELECT t2.perm_desc, t2.perm_id FROM {$table1} as t1
                JOIN {$table2} as t2 ON t1.perm_id = t2.perm_id
                WHERE t1.role_id = :role_id";
        $sth = app()->db->prepare($sql);
        $sth->execute([":role_id" => $roleID]);

        $perms = [];

        while ($row = $sth->fetch()) {
            $perms[$row["perm_desc"]] = $row['perm_id'];
        }

        $this->rolePerms[$roleID] = $perms;

        return $perms;
    }

    /**
     * Insert a new role permission association
     *
     * @param  integer $roleID
     * @param  integer $permID
     * @return boolean
     */
    public function insertPerm($roleID, $permID)
    {
        $db = app()->db;
        $table = Model::getPrefix('role_perm');

        $sql = $db->insert(['role_id', 'perm_id'])->into($table)->values([$roleID, $permID]);
        $sql->execute();

        return true;
    }

    /**
     * Delete a role perm association
     *
     * @param  integer $roleID
     * @param  integer $permID
     * @return boolean
     */
    public function deletePerm($roleID, $permID)
    {
        $db = app()->db;
        $table = Model::getPrefix('role_perm');
        return $db->delete()
                ->from($table)
                ->where('role_id', '=', $roleID)
                ->where('perm_id', '=', $permID)
                ->execute();
    }

    /**
     * Delete all permissions for a role ID from the role=>perm association table
     *
     * @param  integer $roleID
     * @return boolean
     */
    public function deleteRolePerms($roleID)
    {
        $db = app()->db;
        $table = Model::getPrefix('role_perm');
        return (bool) $db->delete()
                ->from($table)
                ->where('role_id', '=', $roleID)
                ->execute();
    }

    /**
     * Delete a role, and all permission associations
     *
     * @param   $roleID
     * @return  boolean
     */
    public function deleteRole($roleID)
    {
        $db = app()->db;
        $table1 = Model::getPrefix('roles');
        $table2 = Model::getPrefix('role_perm');

        $sql = "DELETE t1, t2 FROM {$table1} as t1
        JOIN {$table2} as t2 on t1.role_id = t2.role_id
        WHERE t1.role_id = :role_id";

        $sql = $db->prepare($sql);
        $sql->bindParam(":role_id", $roleID, \PDO::PARAM_INT);

        return $sql->execute();
    }

    public function getUsersCountUnderRole($roleID)
    {
        static $userModel;
        if (!$userModel) {
            $userModel = new UserModel;
        }

        $filters = [];
        $filters['where'][] = [$this->queryKey, '=', $roleID];

        return $userModel->countRows(null, $filters);
    }
}
