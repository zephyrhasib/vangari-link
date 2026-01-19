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
    

    public function getSellerRequests(int $sellerId): array
    {
        $rows = [];

        $sql = "SELECT
                    pr.id,
                    pr.estimated_weight,
                    pr.contact_phone,
                    pr.address,
                    pr.desired_datetime,
                    pr.seller_area,
                    pr.status,
                    pr.created_at,
                    pr.accepted_at,
                    pr.dispatched_at,
                    pr.collected_at,

                    si.name AS scrap_name,
                    si.unit AS scrap_unit,

                    u.shop_name AS buyer_shop,
                    u.phone AS buyer_phone
                FROM pickup_requests pr
                JOIN scrap_items si ON si.id = pr.scrap_item_id
                LEFT JOIN users u ON u.id = pr.buyer_id
                WHERE pr.seller_id = ?
                ORDER BY pr.created_at DESC";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) return $rows;

        $stmt->bind_param("i", $sellerId);
        $stmt->execute();

        $res = $stmt->get_result();
        if ($res) {
            while ($r = $res->fetch_assoc()) {
                $rows[] = $r;
            }
        }

        $stmt->close();
        return $rows;
    }

}
