CREATE TABLE IF NOT EXISTS scrap_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(60) NOT NULL UNIQUE,
  unit VARCHAR(10) NOT NULL DEFAULT 'kg',
  is_active TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;




CREATE TABLE IF NOT EXISTS dealer_prices (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  dealer_id INT NOT NULL,
  item_id INT NOT NULL,
  price_date DATE NOT NULL,
  price_per_kg DECIMAL(10,2) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  UNIQUE KEY uq_dealer_item_date (dealer_id, item_id, price_date),
  KEY idx_item_date (item_id, price_date),
  KEY idx_dealer_date (dealer_id, price_date),

  CONSTRAINT fk_dealer_prices_dealer FOREIGN KEY (dealer_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_dealer_prices_item   FOREIGN KEY (item_id)   REFERENCES scrap_items(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



CREATE TABLE IF NOT EXISTS market_prices (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  item_id INT NOT NULL,
  price_date DATE NOT NULL,
  market_price DECIMAL(10,2) NOT NULL,
  dealer_count INT NOT NULL DEFAULT 0,
  computed_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

  UNIQUE KEY uq_item_date (item_id, price_date),
  KEY idx_price_date (price_date),

  CONSTRAINT fk_market_prices_item FOREIGN KEY (item_id) REFERENCES scrap_items(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



