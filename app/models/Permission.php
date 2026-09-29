<?php
/**
 * Royal Education Center Management System
 * Permission Model
 */

class Permission extends Model {
    protected $table = 'permissions';
    protected $primaryKey = 'id';
    
    // Get permission by name
    public function getByName($name) {
        return $this->where('name', $name)[0] ?? null;
    }
    
    // Get permissions by module
    public function getByModule($module) {
        return $this->where('module', $module);
    }
    
    // Get all permissions grouped by module
    public function getAllGroupedByModule() {
        $sql = "SELECT * FROM permissions ORDER BY module, name";
        $permissions = $this->db->query($sql)->fetchAll();
        
        $grouped = [];
        foreach ($permissions as $permission) {
            $grouped[$permission['module']][] = $permission;
        }
        
        return $grouped;
    }
    
    // Get all modules
    public function getAllModules() {
        $sql = "SELECT DISTINCT module FROM permissions ORDER BY module";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_COLUMN);
    }
}
