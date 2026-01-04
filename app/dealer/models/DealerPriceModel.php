<?php
class DealerPriceModel
{
    private mysqli $db;

    public function __construct()
    {
        $this->db = require __DIR__ . '/../../core/db.php';
    }

    public function hasSubmittedToday(int $dealerId): bool
    {
        $sql = "SELECT 1 FROM dealer_prices WHERE dealer_id = ? AND price_date = CURDATE() LIMIT 1";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) return false;

        $stmt->bind_param("i", $dealerId);
        $stmt->execute();
        $res = $stmt->get_result();
        $exists = $res && $res->num_rows > 0;
        $stmt->close();

        return $exists;
    }

    public function getManagePriceRows(int $dealerId): array
    {
        $sql = "
            SELECT
              si.id AS item_id,
              si.name AS item_name,

              t.market_price AS today_market,
              y.market_price AS yesterday_market,

              a7.avg7_market AS avg7_market,
              a30.avg30_market AS avg30_market,

              dp.price_per_kg AS your_today_price

            FROM scrap_items si

            LEFT JOIN market_prices t
              ON t.item_id = si.id
             AND t.price_date = CURDATE()

            LEFT JOIN market_prices y
              ON y.item_id = si.id
             AND y.price_date = DATE_SUB(CURDATE(), INTERVAL 1 DAY)

            LEFT JOIN (
              SELECT item_id, AVG(market_price) AS avg7_market
              FROM market_prices
              WHERE price_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
                AND price_date < CURDATE()
              GROUP BY item_id
            ) a7 ON a7.item_id = si.id

            LEFT JOIN (
              SELECT item_id, AVG(market_price) AS avg30_market
              FROM market_prices
              WHERE price_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                AND price_date < CURDATE()
              GROUP BY item_id
            ) a30 ON a30.item_id = si.id

            LEFT JOIN dealer_prices dp
              ON dp.item_id = si.id
             AND dp.dealer_id = ?
             AND dp.price_date = CURDATE()

            WHERE si.is_active = 1
            ORDER BY si.name ASC
        ";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) return [];

        $stmt->bind_param("i", $dealerId);
        $stmt->execute();
        $res = $stmt->get_result();
        $rows = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
        $stmt->close();

        return $rows;
    }

    public function insertDealerPricesOnce(int $dealerId, array $pricesByItemId): bool
    {
        if ($this->hasSubmittedToday($dealerId)) {
        return false;
    }

    $this->db->begin_transaction();

    try {
        $sql = "INSERT INTO dealer_prices (dealer_id, item_id, price_date, price_per_kg)
                VALUES (?, ?, CURDATE(), ?)";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            $this->db->rollback();
            return false;
        }

        foreach ($pricesByItemId as $itemId => $price) {
            $itemId = (int)$itemId;
            $price = (float)$price;

            $stmt->bind_param("iid", $dealerId, $itemId, $price);

            if (!$stmt->execute()) {
                $stmt->close();
                $this->db->rollback();
                return false;
            }
        }

        $stmt->close();
        $this->db->commit();
        return true;

      } catch (Throwable $e) {
          $this->db->rollback();
          return false;
      }
    }
}
