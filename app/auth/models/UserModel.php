<?php
 
class UserModel
{
    private function db()
    {
        return require __DIR__ . '/../../core/db.php';
    }
 
    public function findByEmailOrPhone($value)
    {
        $conn = $this->db();
 
        $sql = "SELECT * FROM users WHERE email = ? OR phone = ? LIMIT 1";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            return null;
        }
 
        $stmt->bind_param("ss", $value, $value);
        $stmt->execute();
 
        $result = $stmt->get_result();
        $row = $result ? $result->fetch_assoc() : null;
 
        $stmt->close();
        return $row ?: null;
    }
 
    public function existsByEmailOrPhone($email, $phone)
    {
        $conn = $this->db();
 
        $sql = "SELECT id FROM users WHERE email = ? OR phone = ? LIMIT 1";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            return false;
        }
 
        $stmt->bind_param("ss", $email, $phone);
        $stmt->execute();
 
        $result = $stmt->get_result();
        $exists = ($result && $result->num_rows > 0);
 
        $stmt->close();
        return $exists;
    }
 
    public function createSeller($name, $area, $phone, $email, $passwordHash)
    {
        $conn = $this->db();
 
        $sql = "INSERT INTO users (role, name, shop_name, area, phone, email, password_hash)
                VALUES ('seller', ?, NULL, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            return false;
        }
 
        $stmt->bind_param("sssss", $name, $area, $phone, $email, $passwordHash);
 
        $ok = $stmt->execute();
        $stmt->close();
 
        return $ok;
    }
 
    public function createBuyer($ownerName, $shopName, $area, $phone, $email, $passwordHash)
    {
        $conn = $this->db();
 
        $sql = "INSERT INTO users (role, name, shop_name, area, phone, email, password_hash)
                VALUES ('buyer', ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            return false;
        }
 
        $stmt->bind_param("ssssss", $ownerName, $shopName, $area, $phone, $email, $passwordHash);
 
        $ok = $stmt->execute();
        $stmt->close();
 
        return $ok;
    }
}