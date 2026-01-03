
DELIMITER $$

DROP PROCEDURE IF EXISTS sp_compute_market_prices_today $$

CREATE PROCEDURE sp_compute_market_prices_today(IN p_cap_percent DECIMAL(5,2))
BEGIN
  DECLARE v_today DATE;
  SET v_today = CURDATE();

  INSERT INTO market_prices (item_id, price_date, market_price, dealer_count, computed_at)
  SELECT
    si.id AS item_id,
    v_today AS price_date,

    CASE
      WHEN raw.raw_avg IS NULL THEN lastp.last_price
      ELSE
        CASE
          WHEN baseline.base_price IS NOT NULL THEN
            LEAST(
              baseline.base_price * (1 + p_cap_percent/100),
              GREATEST(baseline.base_price * (1 - p_cap_percent/100), raw.raw_avg)
            )
          ELSE raw.raw_avg
        END
    END AS final_price,

    IFNULL(raw.dealer_count, 0) AS dealer_count,
    NOW() AS computed_at

  FROM scrap_items si

  LEFT JOIN (
    SELECT item_id, AVG(price_per_kg) AS raw_avg, COUNT(*) AS dealer_count
    FROM dealer_prices
    WHERE price_date = v_today
    GROUP BY item_id
  ) raw ON raw.item_id = si.id

  LEFT JOIN (
    SELECT mp1.item_id, mp1.market_price AS last_price
    FROM market_prices mp1
    JOIN (
      SELECT item_id, MAX(price_date) AS max_date
      FROM market_prices
      WHERE price_date < v_today
      GROUP BY item_id
    ) x ON x.item_id = mp1.item_id AND x.max_date = mp1.price_date
  ) lastp ON lastp.item_id = si.id


  LEFT JOIN (
    SELECT
      si2.id AS item_id,
      COALESCE(avg7.avg7_price, last2.last_price) AS base_price
    FROM scrap_items si2
    LEFT JOIN (
      SELECT item_id, AVG(market_price) AS avg7_price
      FROM market_prices
      WHERE price_date >= DATE_SUB(v_today, INTERVAL 7 DAY)
        AND price_date < v_today
      GROUP BY item_id
    ) avg7 ON avg7.item_id = si2.id
    LEFT JOIN (
      SELECT mp2.item_id, mp2.market_price AS last_price
      FROM market_prices mp2
      JOIN (
        SELECT item_id, MAX(price_date) AS max_date
        FROM market_prices
        WHERE price_date < v_today
        GROUP BY item_id
      ) y ON y.item_id = mp2.item_id AND y.max_date = mp2.price_date
    ) last2 ON last2.item_id = si2.id
  ) baseline ON baseline.item_id = si.id

  WHERE si.is_active = 1

  ON DUPLICATE KEY UPDATE
    market_price = VALUES(market_price),
    dealer_count = VALUES(dealer_count),
    computed_at = VALUES(computed_at);

END $$

DELIMITER ;






DROP EVENT IF EXISTS ev_compute_market_prices_5min;

CREATE EVENT ev_compute_market_prices_5min
ON SCHEDULE EVERY 5 MINUTE
DO
  CALL sp_compute_market_prices_today(10.00);



CALL sp_compute_market_prices_today(10.00);