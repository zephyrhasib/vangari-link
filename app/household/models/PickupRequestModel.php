<?php

class PickupRequestModel
{
    private mysqli $db;

    public function __construct()
    {
        $this->db = require __DIR__ . '/../../core/db.php';
    }

    public function getSellerArea(int $sellerId): ?string
    {
        $sql = "SELECT area FROM users WHERE id = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) return null;

        $stmt->bind_param("i", $sellerId);
        $stmt->execute();

        $res = $stmt->get_result();
        $row = $res ? $res->fetch_assoc() : null;

        $stmt->close();

        return $row['area'] ?? null;
    }

    public function create(
    int $sellerId,
    string $sellerArea,
    int $scrapItemId,
    float $estimatedWeight,
    string $contactPhone,
    string $address,
    string $desiredMysqlDatetime
    ): bool {

        $sql = "INSERT INTO pickup_requests
                (seller_id, seller_area, scrap_item_id, estimated_weight,
                contact_phone, address, desired_datetime, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) return false;

        $stmt->bind_param(
            "isidsss",
            $sellerId,
            $sellerArea,
            $scrapItemId,
            $estimatedWeight,
            $contactPhone,
            $address,
            $desiredMysqlDatetime
        );

        $ok = $stmt->execute();
        $stmt->close();

        return $ok;
    }


    public function getActiveScrapItems(): array
    {
        $rows = [];

        $sql = "SELECT id, name, unit
                FROM scrap_items
                WHERE is_active = 1
                ORDER BY name ASC";

        $res = $this->db->query($sql);
        if ($res) {
            while ($r = $res->fetch_assoc()) {
                $rows[] = $r;
            }
            $res->free();
        }

        return $rows;
    }

    public function getSellerInfo(int $sellerId): ?array
    {
        $sql = "SELECT phone, area FROM users WHERE id = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) return null;

        $stmt->bind_param("i", $sellerId);
        $stmt->execute();

        $res = $stmt->get_result();
        $row = $res ? $res->fetch_assoc() : null;

        $stmt->close();

        return $row ?: null;
    }

}
