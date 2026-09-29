<?php
/**
 * Royal Education Center Management System
 * Role Model
 */

class Role extends Model {
    protected $table = 'roles';
    protected $primaryKey = 'id';
    
    // Get role by name
    public function getByName($name) {
        return $this->where('name', $name)[0] ?? null;
    }
    
    // Get role with permissions
    public function getWithPermissions($id) {
        $sql = "SELECT r.*, GROUP_CONCAT(p.name) as permissions 
                FROM roles r 
                LEFT JOIN role_permissions rp ON r.id = rp.role_id 
                LEFT JOIN permissions p ON rp.permission_id = p.id 
                WHERE r.id = ? 
                GROUP BY r.id";
        return $this->db->query($sql)->bind(1, $id)->fetch();
    }
    
    // Get all roles with permission count
    public function getAllWithPermissionCount() {
        $sql = "SELECT r.*, COUNT(rp.permission_id) as permission_count 
                FROM roles r 
                LEFT JOIN role_permissions rp ON r.id = rp.role_id 
                GROUP BY r.id 
                ORDER BY r.id";
        return $this->db->query($sql)->fetchAll();
    }
    
    // Assign permission to role
    public function assignPermission($roleId, $permissionId) {
        $sql = "INSERT INTO role_permissions (role_id, permission_id) VALUES (?, ?)";
        return $this->db->query($sql)->bind(1, $roleId)->bind(2, $permissionId)->execute();
    }
    
    // Remove permission from role
    public function removePermission($roleId, $permissionId) {
        $sql = "DELETE FROM role_permissions WHERE role_id = ? AND permission_id = ?";
        return $this->db->query($sql)->bind(1, $roleId)->bind(2, $permissionId)->execute();
    }
    
    // Get role permissions
    public function getPermissions($roleId) {
        $sql = "SELECT p.* FROM permissions p 
                INNER JOIN role_permissions rp ON p.id = rp.permission_id 
                WHERE rp.role_id = ? 
                ORDER BY p.module, p.name";
        return $this->db->query($sql)->bind(1, $roleId)->fetchAll();
    }
}
