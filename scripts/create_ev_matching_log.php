<?php
define('_RUN_MIGRATION_', true);
chdir(__DIR__ . '/..');
include 'common.php';

$sql = "CREATE TABLE IF NOT EXISTS `g5_ev_matching_log` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='매칭 이력'";

$r = sql_query($sql, false);
echo $r !== false ? "OK: g5_ev_matching_log created\n" : "FAIL: " . (function_exists('sql_error') ? sql_error() : 'unknown') . "\n";
