-- schema.sql — tables for login + winners + 90-day cooldown
CREATE TABLE IF NOT EXISTS id_seq (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  stub TINYINT NULL
);

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(190) UNIQUE NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('admin','sales','rj','collection') DEFAULT 'rj',
  active TINYINT(1) DEFAULT 1,
  last_login DATETIME NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS winners (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  winner_id VARCHAR(32) UNIQUE NOT NULL,
  name VARCHAR(120) NOT NULL,
  phone_cc VARCHAR(8) DEFAULT '+973',
  phone_local VARCHAR(32) NOT NULL,
  phone_e164 VARCHAR(32) NOT NULL,
  contest_type VARCHAR(32) DEFAULT 'On-Air',
  win_date DATE NOT NULL,
  batch_id VARCHAR(64) NULL,
  seq INT NULL,
  show_id VARCHAR(32) NULL,
  created_by_email VARCHAR(190) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX (phone_e164),
  INDEX (win_date)
);

DROP TRIGGER IF EXISTS trg_winners_cooldown;
DELIMITER $$
CREATE TRIGGER trg_winners_cooldown
BEFORE INSERT ON winners
FOR EACH ROW
BEGIN
  DECLARE exists_count INT DEFAULT 0;
  SELECT COUNT(*) INTO exists_count
  FROM winners
  WHERE phone_e164 = NEW.phone_e164
    AND win_date >= DATE_SUB(NEW.win_date, INTERVAL 90 DAY);
  IF exists_count > 0 THEN
    SIGNAL SQLSTATE '45000'
      SET MESSAGE_TEXT = 'Winner in cooldown period (90 days).';
  END IF;
END$$
DELIMITER ;

-- Inventory batches (one row per voucher batch)
CREATE TABLE IF NOT EXISTS batches (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  partner VARCHAR(120) NOT NULL,
  name VARCHAR(160) NOT NULL,
  total_qty INT UNSIGNED NOT NULL,
  used_qty INT UNSIGNED NOT NULL DEFAULT 0,
  expiry_date DATE NOT NULL,
  terms TEXT NULL,
  active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Assignments (one row per winner voucher allocation)
CREATE TABLE IF NOT EXISTS assignments (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  batch_id BIGINT UNSIGNED NOT NULL,
  winner_name VARCHAR(160) NOT NULL,
  mobile VARCHAR(32) NOT NULL,
  show_code VARCHAR(80) NOT NULL,      -- e.g. "breakfast_platter"
  assigned_by BIGINT UNSIGNED NOT NULL, -- user id
  status ENUM('active','void') NOT NULL DEFAULT 'active',
  delete_reason VARCHAR(255) NULL,
  deleted_by BIGINT UNSIGNED NULL,
  assigned_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  deleted_at TIMESTAMP NULL,
  CONSTRAINT fk_assign_batch FOREIGN KEY (batch_id) REFERENCES batches(id)
) ENGINE=InnoDB;

CREATE INDEX idx_assign_batch_status ON assignments(batch_id,status);
