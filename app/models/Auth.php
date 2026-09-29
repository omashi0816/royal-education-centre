<?php
/**
 * Royal Education Center Management System
 * Auth Model
 */

class Auth extends Model {
    protected $table = 'users';
    
    // Find user by username or email
    public function findUser($identifier) {
        $sql = "SELECT u.*, r.name as role_name 
                FROM users u 
                INNER JOIN roles r ON u.role_id = r.id 
                WHERE u.username = ? OR u.email = ? 
                LIMIT 1";
        return $this->db->query($sql)->bind(1, $identifier)->bind(2, $identifier)->fetch();
    }
    
    // Find user by ID
    public function findUserById($id) {
        $sql = "SELECT u.*, r.name as role_name 
                FROM users u 
                INNER JOIN roles r ON u.role_id = r.id 
                WHERE u.id = ? 
                LIMIT 1";
        return $this->db->query($sql)->bind(1, $id)->fetch();
    }
    
    // Verify user credentials
    public function verifyCredentials($identifier, $password) {
        $user = $this->findUser($identifier);
        
        if (!$user) {
            return ['success' => false, 'message' => 'Invalid credentials'];
        }
        
        // Check if account is locked
        if ($user['locked_until'] && new DateTime($user['locked_until']) > new DateTime()) {
            $remaining = (new DateTime($user['locked_until']))->diff(new DateTime())->i;
            return ['success' => false, 'message' => "Account locked. Try again in $remaining minutes."];
        }
        
        // Check account status
        if ($user['status'] !== 'active') {
            return ['success' => false, 'message' => 'Account is ' . $user['status']];
        }
        
        // Verify password
        if (!Security::verifyPassword($password, $user['password_hash'])) {
            // Increment login attempts
            $this->incrementLoginAttempts($user['id']);
            return ['success' => false, 'message' => 'Invalid credentials'];
        }
        
        // Check if password needs rehash
        if (Security::needsRehash($user['password_hash'])) {
            $this->updatePassword($user['id'], $password);
        }
        
        // Reset login attempts on successful login
        $this->resetLoginAttempts($user['id']);
        
        return ['success' => true, 'user' => $user];
    }
    
    // Increment login attempts
    private function incrementLoginAttempts($userId) {
        $sql = "UPDATE users SET login_attempts = login_attempts + 1 WHERE id = ?";
        $this->db->query($sql)->bind(1, $userId)->execute();
        
        // Lock account if max attempts reached
        $sql = "SELECT login_attempts FROM users WHERE id = ?";
        $attempts = $this->db->query($sql)->bind(1, $userId)->fetchColumn();
        
        if ($attempts >= MAX_LOGIN_ATTEMPTS) {
            $lockUntil = date('Y-m-d H:i:s', strtotime('+' . LOGIN_LOCKOUT_TIME . ' minutes'));
            $sql = "UPDATE users SET locked_until = ? WHERE id = ?";
            $this->db->query($sql)->bind(1, $lockUntil)->bind(2, $userId)->execute();
        }
    }
    
    // Reset login attempts
    private function resetLoginAttempts($userId) {
        $sql = "UPDATE users SET login_attempts = 0, locked_until = NULL, last_login = CURRENT_TIMESTAMP WHERE id = ?";
        $this->db->query($sql)->bind(1, $userId)->execute();
    }
    
    // Update password
    private function updatePassword($userId, $password) {
        $hash = Security::hashPassword($password);
        $sql = "UPDATE users SET password_hash = ? WHERE id = ?";
        $this->db->query($sql)->bind(1, $hash)->bind(2, $userId)->execute();
    }
    
    // Create new user
    public function createUser($data) {
        // Hash password
        $data['password_hash'] = Security::hashPassword($data['password']);
        unset($data['password']);
        
        $sql = "INSERT INTO users (role_id, username, email, password_hash, full_name, phone, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $this->db->query($sql);
        $this->db->bind(1, $data['role_id']);
        $this->db->bind(2, $data['username']);
        $this->db->bind(3, $data['email']);
        $this->db->bind(4, $data['password_hash']);
        $this->db->bind(5, $data['full_name']);
        $this->db->bind(6, $data['phone'] ?? null);
        $this->db->bind(7, $data['status'] ?? 'active');
        
        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    // Create a user and the profile required by the selected operational role.
    public function createUserWithProfile($data, $roleName) {
        $nameParts = preg_split('/\s+/', trim($data['full_name']), 2);
        $firstName = $nameParts[0];
        $lastName = $nameParts[1] ?? '';

        $this->beginTransaction();

        try {
            $userId = $this->createUser($data);
            if (!$userId) {
                throw new Exception('Unable to create user account.');
            }

            if ($roleName === 'student') {
                $profileCreated = $this->createProfile('students', [
                    'user_id' => $userId,
                    'student_code' => generateCode('S', 'students', 'student_code', 4),
                    'first_name' => $firstName,
                    'last_name' => $lastName
                ]);
            } elseif ($roleName === 'teacher') {
                $profileCreated = $this->createProfile('teachers', [
                    'user_id' => $userId,
                    'teacher_code' => generateCode('T', 'teachers', 'teacher_code', 3),
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'phone' => $data['phone'] ?? null
                ]);
            } elseif (in_array($roleName, ['receptionist', 'cashier'], true)) {
                $profileCreated = $this->createProfile('staff', [
                    'user_id' => $userId,
                    'staff_code' => generateCode('ST', 'staff', 'staff_code', 3),
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'designation' => ucfirst($roleName),
                    'phone' => $data['phone'] ?? null
                ]);
            } else {
                $profileCreated = true;
            }

            if (!$profileCreated) {
                throw new Exception('Unable to create the user profile.');
            }

            $this->commit();
            return $userId;
        } catch (Exception $e) {
            $this->rollback();
            return false;
        }
    }

    private function createProfile($table, $data) {
        $columns = array_keys($data);
        $this->db->query("INSERT INTO $table (" . implode(', ', $columns) . ") VALUES (" . implode(', ', array_fill(0, count($columns), '?')) . ")");
        $index = 1;
        foreach ($data as $value) {
            $this->db->bind($index++, $value);
        }
        return $this->db->execute();
    }
    
    // Check if username exists
    public function usernameExists($username, $excludeId = null) {
        $sql = "SELECT COUNT(*) FROM users WHERE username = ?";
        if ($excludeId) {
            $sql .= " AND id != ?";
        }
        
        $this->db->query($sql)->bind(1, $username);
        if ($excludeId) {
            $this->db->bind(2, $excludeId);
        }
        
        return $this->db->fetchColumn() > 0;
    }
    
    // Check if email exists
    public function emailExists($email, $excludeId = null) {
        $sql = "SELECT COUNT(*) FROM users WHERE email = ?";
        if ($excludeId) {
            $sql .= " AND id != ?";
        }
        
        $this->db->query($sql)->bind(1, $email);
        if ($excludeId) {
            $this->db->bind(2, $excludeId);
        }
        
        return $this->db->fetchColumn() > 0;
    }
}
