<?php
class Settings extends Model {
    protected $table = 'settings';
    protected $primaryKey = 'id';
    
    public function getAll() {
        return $this->db->query("SELECT * FROM settings ORDER BY setting_group, setting_key")->fetchAll();
    }
    
    public function getByGroup($group) {
        return $this->where('setting_group', $group);
    }
    
    public function get($key, $default = null) {
        $row = $this->where('setting_key', $key);
        return $row ? $row[0]['setting_value'] : $default;
    }
    
    public function set($key, $value, $group = 'general') {
        $sql = "INSERT INTO settings (setting_key, setting_value, setting_group) 
                VALUES (?, ?, ?) 
                ON DUPLICATE KEY UPDATE setting_value = ?, updated_at = CURRENT_TIMESTAMP";
        return $this->db->query($sql)
            ->bind(1, $key)->bind(2, $value)->bind(3, $group)->bind(4, $value)
            ->execute();
    }
}
