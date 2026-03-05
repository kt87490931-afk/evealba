-- 매칭시스템: 매칭 이력 및 설정 테이블
-- 기업회원·이브회원 AI 매칭 (매일 1쌍, 쪽지 발송)

CREATE TABLE IF NOT EXISTS `g5_ev_matching_log` (
  `mlog_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `mb_id_eve` VARCHAR(20) NOT NULL DEFAULT '' COMMENT '이브회원 mb_id',
  `mb_id_ent` VARCHAR(20) NOT NULL DEFAULT '' COMMENT '기업회원 mb_id',
  `jr_id` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '채용정보 ID',
  `rs_id` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '이력서 ID',
  `match_rate` TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '일치율 0~100',
  `matched_at` DATETIME NOT NULL COMMENT '매칭 일시',
  `memo_sent` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '쪽지 발송 여부',
  PRIMARY KEY (`mlog_id`),
  KEY `idx_mb_eve` (`mb_id_eve`),
  KEY `idx_mb_ent` (`mb_id_ent`),
  KEY `idx_matched_at` (`matched_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='매칭 이력';

CREATE TABLE IF NOT EXISTS `g5_ev_matching_config` (
  `mc_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `mc_key` VARCHAR(50) NOT NULL DEFAULT '',
  `mc_value` TEXT,
  `mc_updated` DATETIME DEFAULT NULL,
  PRIMARY KEY (`mc_id`),
  UNIQUE KEY `uk_mc_key` (`mc_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='매칭 시스템 설정';

INSERT IGNORE INTO `g5_ev_matching_config` (`mc_key`, `mc_value`, `mc_updated`) VALUES
('enabled', '0', NOW()),
('min_rate', '70', NOW()),
('re_match_days', '7', NOW()),
('min_eve_count', '10', NOW()),
('min_ent_count', '5', NOW());
