<?php
class PriceModel
{
    private mysqli $db;

    public function __construct()
    {
        $this->db = require __DIR__ . '/../../core/db.php';
    }

    public function getTodayAndYesterdayPrices(): array
    {
        $sql = "
            SELECT
              si.name AS item_name,
              tp.market_price AS today_price,
              yp.market_price AS yesterday_price
            FROM scrap_items si
            LEFT JOIN market_prices tp
              ON tp.item_id = si.id AND tp.price_date = CURDATE()
            LEFT JOIN market_prices yp
              ON yp.item_id = si.id AND yp.price_date = DATE_SUB(CURDATE(), INTERVAL 1 DAY)
            WHERE si.is_active = 1
            ORDER BY si.name ASC
        ";

        $result = $this->db->query($sql);
        if (!$result) return [];

        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
