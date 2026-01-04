<?php

class HouseholdAccountModel
{
    private function db()
    {
        return require __DIR__ . '/../../core/db.php';
    }

    public function findSellerById($id)
    {
        $conn = $this->db();

        $sql = "SELECT id, role, name, shop_name, area, phone, email, password_hash, created_at
                FROM users
                WHERE id = ? AND role = 'seller'
                LIMIT 1";

        $stmt = $conn->prepare($sql);
        if (!$stmt) return null;

        $stmt->bind_param("i", $id);
        $stmt->execute();

        $res = $stmt->get_result();
        $row = $res ? $res->fetch_assoc() : null;

        $stmt->close();
        return $row ?: null;
    }

    public function emailOrPhoneTakenByOthers($id, $email, $phone)
    {
        $conn = $this->db();

        $sql = "SELECT id
                FROM users
                WHERE (email = ? OR phone = ?)
                  AND id <> ?
                LIMIT 1";

        $stmt = $conn->prepare($sql);
        if (!$stmt) return true;

        // ORDER: email, phone, id
        $stmt->bind_param("ssi", $email, $phone, $id);
        $stmt->execute();

        $res = $stmt->get_result();
        $taken = ($res && $res->num_rows > 0);

        $stmt->close();
        return $taken;
    }

    public function updateSellerProfile($id, $name, $area, $phone, $email)
    {
        $conn = $this->db();

        // updates ONLY these 4 columns
        $sql = "UPDATE users
                SET name = ?, area = ?, phone = ?, email = ?
                WHERE id = ? AND role = 'seller'
                LIMIT 1";

        $stmt = $conn->prepare($sql);
        if (!$stmt) return false;

        // ORDER MUST MATCH: name, area, phone, email, id
        $stmt->bind_param("ssssi", $name, $area, $phone, $email, $id);

        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function verifySellerPassword($id, $plainPassword)
    {
        $user = $this->findSellerById($id);
        if (!$user) return false;

        return password_verify($plainPassword, $user['password_hash']);
    }

    public function updateSellerPassword($id, $newPasswordHash)
    {
        $conn = $this->db();

        // updates ONLY password_hash
        $sql = "UPDATE users
                SET password_hash = ?
                WHERE id = ? AND role = 'seller'
                LIMIT 1";

        $stmt = $conn->prepare($sql);
        if (!$stmt) return false;

        // ORDER: hash, id
        $stmt->bind_param("si", $newPasswordHash, $id);

        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    public function deleteSellerAccount($id)
    {
        $conn = $this->db();

        $sql = "DELETE FROM users
                WHERE id = ? AND role = 'seller'
                LIMIT 1";

        $stmt = $conn->prepare($sql);
        if (!$stmt) return false;

        $stmt->bind_param("i", $id);

        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }
}
