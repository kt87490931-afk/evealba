-- 이브알바 핵심 마스터 테이블 4종 (§2.0-2, §11.2~11.7)

-- 1) 업종/직종 (1차 + 2차)
CREATE TABLE IF NOT EXISTS `g5_ev_industry` (
  `ei_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `ei_code` VARCHAR(20) NOT NULL,
  `ei_name` VARCHAR(50) NOT NULL,
  `ei_ord` INT NOT NULL DEFAULT 0,
  PRIMARY KEY (`ei_id`),
  UNIQUE KEY `ei_code` (`ei_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `g5_ev_job` (
  `ej_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `ei_id` INT UNSIGNED NOT NULL,
  `ej_code` VARCHAR(20) NOT NULL,
  `ej_name` VARCHAR(50) NOT NULL,
  `ej_ord` INT NOT NULL DEFAULT 0,
  PRIMARY KEY (`ej_id`),
  KEY `ei_id` (`ei_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2) 지역/세부지역
CREATE TABLE IF NOT EXISTS `g5_ev_region` (
  `er_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `er_code` VARCHAR(20) NOT NULL,
  `er_name` VARCHAR(50) NOT NULL,
  `er_ord` INT NOT NULL DEFAULT 0,
  PRIMARY KEY (`er_id`),
  UNIQUE KEY `er_code` (`er_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `g5_ev_region_detail` (
  `erd_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `er_id` INT UNSIGNED NOT NULL,
  `erd_code` VARCHAR(20) NOT NULL,
  `erd_name` VARCHAR(50) NOT NULL,
  `erd_ord` INT NOT NULL DEFAULT 0,
  PRIMARY KEY (`erd_id`),
  KEY `er_id` (`er_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3) 편의사항 (§11.6)
CREATE TABLE IF NOT EXISTS `g5_ev_convenience` (
  `ec_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `ec_code` VARCHAR(30) NOT NULL,
  `ec_name` VARCHAR(50) NOT NULL,
  `ec_ord` INT NOT NULL DEFAULT 0,
  PRIMARY KEY (`ec_id`),
  UNIQUE KEY `ec_code` (`ec_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4) 키워드 (§11.7)
CREATE TABLE IF NOT EXISTS `g5_ev_keyword` (
  `ek_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `ek_code` VARCHAR(30) NOT NULL,
  `ek_name` VARCHAR(50) NOT NULL,
  `ek_ord` INT NOT NULL DEFAULT 0,
  PRIMARY KEY (`ek_id`),
  UNIQUE KEY `ek_code` (`ek_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5) MBTI (§11.7-1)
CREATE TABLE IF NOT EXISTS `g5_ev_mbti` (
  `em_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `em_code` VARCHAR(4) NOT NULL,
  `em_name` VARCHAR(50) NOT NULL,
  `em_group` CHAR(2) NOT NULL DEFAULT '',
  `em_strength` VARCHAR(200) NOT NULL DEFAULT '',
  `em_ord` INT NOT NULL DEFAULT 0,
  PRIMARY KEY (`em_id`),
  UNIQUE KEY `em_code` (`em_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
