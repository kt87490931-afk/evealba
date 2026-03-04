-- 쿠폰 마스터 (기업회원: 광고/썸네일 할인, 일반회원: 기프티콘)
CREATE TABLE IF NOT EXISTS `g5_ev_coupon` (
  `ec_id` int unsigned NOT NULL AUTO_INCREMENT,
  `ec_code` varchar(32) NOT NULL DEFAULT '' COMMENT '쿠폰코드',
  `ec_name` varchar(200) NOT NULL DEFAULT '',
  `ec_target` varchar(20) NOT NULL DEFAULT 'biz' COMMENT 'biz=기업회원, personal=일반회원',
  `ec_type` varchar(30) NOT NULL DEFAULT 'thumb' COMMENT 'thumb=썸네일, ad=광고, line_ad_free=줄광고무료, gift=기프티콘',
  `ec_discount_type` varchar(20) NOT NULL DEFAULT 'percent' COMMENT 'percent=율, amount=금액',
  `ec_discount_value` int NOT NULL DEFAULT 0 COMMENT '할인율(%) 또는 할인금액(원)',
  `ec_min_amount` int NOT NULL DEFAULT 0 COMMENT '최소 결제금액',
  `ec_max_discount` int NOT NULL DEFAULT 0 COMMENT '최대할인금액(percent일 때만)',
  `ec_valid_from` date DEFAULT NULL,
  `ec_valid_to` date DEFAULT NULL,
  `ec_use_limit` int NOT NULL DEFAULT 0 COMMENT '0=무제한, N=발급수제한',
  `ec_use_count` int NOT NULL DEFAULT 0,
  `ec_is_active` tinyint NOT NULL DEFAULT 1,
  `ec_created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ec_updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`ec_id`),
  UNIQUE KEY `ec_code` (`ec_code`),
  KEY `ec_target` (`ec_target`),
  KEY `ec_type` (`ec_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='이브알바 쿠폰 마스터';

-- 쿠폰 발급 (회원별 지급)
CREATE TABLE IF NOT EXISTS `g5_ev_coupon_issue` (
  `eci_id` int unsigned NOT NULL AUTO_INCREMENT,
  `ec_id` int unsigned NOT NULL,
  `mb_id` varchar(20) NOT NULL DEFAULT '',
  `eci_used` tinyint NOT NULL DEFAULT 0,
  `eci_used_at` datetime DEFAULT NULL,
  `eci_issued_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`eci_id`),
  KEY `ec_id` (`ec_id`),
  KEY `mb_id` (`mb_id`),
  KEY `eci_used` (`eci_used`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='쿠폰 발급';

-- 쿠폰 사용 로그
CREATE TABLE IF NOT EXISTS `g5_ev_coupon_use` (
  `ecu_id` int unsigned NOT NULL AUTO_INCREMENT,
  `eci_id` int unsigned NOT NULL,
  `ec_id` int unsigned NOT NULL,
  `mb_id` varchar(20) NOT NULL DEFAULT '',
  `ecu_target_type` varchar(30) NOT NULL DEFAULT 'thumb' COMMENT 'thumb, ad 등',
  `ecu_target_id` varchar(50) NOT NULL DEFAULT '' COMMENT 'jr_id, order_id 등',
  `ecu_discount_amount` int NOT NULL DEFAULT 0,
  `ecu_created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ecu_id`),
  KEY `eci_id` (`eci_id`),
  KEY `mb_id` (`mb_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='쿠폰 사용 로그';

-- 썸네일 옵션 결제 내역 (기간별 사용 가능)
CREATE TABLE IF NOT EXISTS `g5_jobs_thumb_option_paid` (
  `jtp_id` int unsigned NOT NULL AUTO_INCREMENT,
  `jr_id` int unsigned NOT NULL,
  `mb_id` varchar(20) NOT NULL DEFAULT '',
  `jtp_option_key` varchar(50) NOT NULL DEFAULT '' COMMENT 'badge, motion, wave, border, premium_color',
  `jtp_option_value` varchar(50) NOT NULL DEFAULT '' COMMENT 'beginner, gold, P1 등',
  `jtp_valid_until` date NOT NULL COMMENT '사용가능 종료일',
  `jtp_amount` int NOT NULL DEFAULT 0,
  `jtp_coupon_id` int unsigned NOT NULL DEFAULT 0,
  `jtp_coupon_discount` int NOT NULL DEFAULT 0,
  `jtp_order_id` varchar(100) NOT NULL DEFAULT '' COMMENT '결제 주문번호',
  `jtp_created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`jtp_id`),
  KEY `jr_id` (`jr_id`),
  KEY `mb_id` (`mb_id`),
  KEY `jtp_valid_until` (`jtp_valid_until`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='썸네일 옵션 결제 내역';
