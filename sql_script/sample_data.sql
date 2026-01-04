INSERT INTO scrap_items (name) VALUES
('Paper'), ('Plastic'), ('Iron'), ('Glass'), ('Aluminum')
ON DUPLICATE KEY UPDATE name = name;

INSERT INTO market_prices (item_id, price_date, market_price, dealer_count)
SELECT id, CURDATE(),
      CASE name
        WHEN 'Paper' THEN 12.00
        WHEN 'Plastic' THEN 18.00
        WHEN 'Iron' THEN 55.00
        WHEN 'Glass' THEN 6.00
        WHEN 'Aluminum' THEN 160.00
        ELSE 10.00
      END,
      3
FROM scrap_items
ON DUPLICATE KEY UPDATE market_price = VALUES(market_price), dealer_count = VALUES(dealer_count);

INSERT INTO market_prices (item_id, price_date, market_price, dealer_count)
SELECT id, DATE_SUB(CURDATE(), INTERVAL 1 DAY),
      CASE name
        WHEN 'Paper' THEN 11.50
        WHEN 'Plastic' THEN 17.50
        WHEN 'Iron' THEN 54.00
        WHEN 'Glass' THEN 6.00
        WHEN 'Aluminum' THEN 158.00
        ELSE 10.00
      END,
      3
FROM scrap_items
ON DUPLICATE KEY UPDATE market_price = VALUES(market_price), dealer_count = VALUES(dealer_count);