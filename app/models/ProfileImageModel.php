<?php

class ProfileImageModel
{
    private mysqli $db;

    public function __construct()
    {
        $this->db = require __DIR__ . '/../core/db.php';
    }

    public function getByUserId(int $userId): ?string
    {
        $sql = "SELECT image_path FROM user_profile_images WHERE user_id = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) return null;

        $stmt->bind_param("i", $userId);
        $stmt->execute();

        $res = $stmt->get_result();
        $row = $res ? $res->fetch_assoc() : null;

        $stmt->close();

        return $row['image_path'] ?? null;
    }

    public function upsert(int $userId, string $imagePath): bool
    {
        // Insert if not exists, else update (simple)
        $existing = $this->getByUserId($userId);

        if ($existing) {
            $sql = "UPDATE user_profile_images
                    SET image_path = ?, uploaded_at = NOW()
                    WHERE user_id = ?";
            $stmt = $this->db->prepare($sql);
            if (!$stmt) return false;

            $stmt->bind_param("si", $imagePath, $userId);
            $ok = $stmt->execute();
            $stmt->close();
            return $ok;
        }

        $sql = "INSERT INTO user_profile_images (user_id, image_path)
                VALUES (?, ?)";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) return false;

        $stmt->bind_param("is", $userId, $imagePath);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }
}
