<?php
class ManageRequestModel
{

    private mysqli $db;

    public function __construct()
    {
        $this->db = require __DIR__ . '/../../core/db.php';
    }

    public function getBuyerInfo(int $buyerId): ?array
    {
        $sql = "SELECT area, shop_name, phone FROM users WHERE id = ? AND role='buyer' LIMIT 1";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) return null;

        $stmt->bind_param("i", $buyerId);
        $stmt->execute();

        $res = $stmt->get_result();
        $row = $res ? $res->fetch_assoc() : null;

        $stmt->close();
        return $row ?: null;
    }


    public function getPendingRequestsByArea(string $area): array
    {
        $rows = [];

        $sql = "SELECT
                    pr.id,
                    pr.estimated_weight,
                    pr.address,
                    pr.desired_datetime,
                    pr.seller_area,
                    pr.status,
                    si.name AS scrap_name,
                    si.unit AS scrap_unit
                FROM pickup_requests pr
                JOIN scrap_items si ON si.id = pr.scrap_item_id
                WHERE pr.status = 'pending'
                  AND pr.seller_area = ?
                ORDER BY pr.created_at ASC";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) return $rows;

        $stmt->bind_param("s", $area);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res) {
            while ($r = $res->fetch_assoc()) $rows[] = $r;
        }

        $stmt->close();
        return $rows;
    }


    public function getMyAcceptedRequests(int $buyerId): array
    {
        $rows = [];

        $sql = "SELECT
                    pr.id,
                    pr.estimated_weight,
                    pr.address,
                    pr.desired_datetime,
                    pr.status,

                    si.name AS scrap_name,
                    si.unit AS scrap_unit,

                    u.name  AS seller_name,
                    u.phone AS seller_phone,
                    u.email AS seller_email
                FROM pickup_requests pr
                JOIN scrap_items si ON si.id = pr.scrap_item_id
                JOIN users u ON u.id = pr.seller_id
                WHERE pr.buyer_id = ?
                  AND pr.status IN ('accepted','dispatched')
                ORDER BY pr.accepted_at DESC";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) return $rows;

        $stmt->bind_param("i", $buyerId);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res) {
            while ($r = $res->fetch_assoc()) $rows[] = $r;
        }

        $stmt->close();
        return $rows;
    }

    
    public function acceptRequest(int $requestId, int $buyerId): bool
    {
        $sql = "UPDATE pickup_requests
                SET buyer_id = ?, status = 'accepted', accepted_at = NOW()
                WHERE id = ? AND status = 'pending'";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) return false;

        $stmt->bind_param("ii", $buyerId, $requestId);
        $stmt->execute();

        $ok = ($stmt->affected_rows === 1);
        $stmt->close();

        return $ok;
    }

    public function markDispatched(int $requestId, int $buyerId): bool
    {
        $sql = "UPDATE pickup_requests
                SET status = 'dispatched', dispatched_at = NOW()
                WHERE id = ? AND buyer_id = ? AND status = 'accepted'";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) return false;

        $stmt->bind_param("ii", $requestId, $buyerId);
        $stmt->execute();

        $ok = ($stmt->affected_rows === 1);
        $stmt->close();

        return $ok;
    }

    public function markCollected(int $requestId, int $buyerId): bool
    {
        $sql = "UPDATE pickup_requests
                SET status = 'collected', collected_at = NOW()
                WHERE id = ? AND buyer_id = ? AND status = 'dispatched'";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) return false;

        $stmt->bind_param("ii", $requestId, $buyerId);
        $stmt->execute();

        $ok = ($stmt->affected_rows === 1);
        $stmt->close();

        return $ok;
    }


}