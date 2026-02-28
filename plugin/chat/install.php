<?php
// /plugin/chat/install.php — 이브알바 채팅 DB 설치
include_once('../../common.php');
include_once('./_common.php');

if (!$is_admin) die('관리자만 접근 가능합니다.');

function chat_create_table($sql){
    sql_query($sql, true);
}

// 1) 메시지 테이블
chat_create_table("CREATE TABLE IF NOT EXISTS `{$g5['chat_msg_table']}` (
  `cm_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `mb_id` VARCHAR(20) NOT NULL DEFAULT '',
  `cm_nick` VARCHAR(50) NOT NULL DEFAULT '',
  `cm_icon` VARCHAR(255) NOT NULL DEFAULT '',
  `cm_content` TEXT NOT NULL,
  `cm_region` VARCHAR(10) NOT NULL DEFAULT '전체',
  `cm_datetime` DATETIME NOT NULL,
  PRIMARY KEY (`cm_id`),
  KEY `mb_id` (`mb_id`),
  KEY `cm_datetime` (`cm_datetime`),
  KEY `idx_region` (`cm_region`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// cm_region 컬럼이 없으면 추가 (기존 테이블 호환)
$col = sql_fetch("SHOW COLUMNS FROM `{$g5['chat_msg_table']}` LIKE 'cm_region'", false);
if (!$col) {
    sql_query("ALTER TABLE `{$g5['chat_msg_table']}` ADD COLUMN `cm_region` VARCHAR(10) NOT NULL DEFAULT '전체' AFTER `cm_content`", false);
    sql_query("ALTER TABLE `{$g5['chat_msg_table']}` ADD KEY `idx_region` (`cm_region`)", false);
}

// 2) 설정 테이블
chat_create_table("CREATE TABLE IF NOT EXISTS `{$g5['chat_config_table']}` (
  `cf_id` TINYINT NOT NULL DEFAULT 1,
  `cf_title` VARCHAR(50) NOT NULL DEFAULT '실시간 채팅',
  `cf_freeze` TINYINT NOT NULL DEFAULT 0,
  `cf_spam_sec` INT NOT NULL DEFAULT 2,
  `cf_repeat_sec` INT NOT NULL DEFAULT 30,
  `cf_report_limit` INT NOT NULL DEFAULT 10,
  `cf_autoban_min` INT NOT NULL DEFAULT 10,
  `cf_notice_text` TEXT,
  `cf_rule_text` TEXT,
  `cf_badwords` TEXT,
  `cf_online_window` INT NOT NULL DEFAULT 300,
  `cf_online_fake_add` INT NOT NULL DEFAULT 0,
  `cf_updated_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`cf_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

sql_query("INSERT INTO `{$g5['chat_config_table']}` (`cf_id`) VALUES (1) ON DUPLICATE KEY UPDATE `cf_id`=`cf_id`");

// 3) 채팅금지
chat_create_table("CREATE TABLE IF NOT EXISTS `{$g5['chat_ban_table']}` (
  `mb_id` VARCHAR(20) NOT NULL,
  `mb_nick` VARCHAR(50) NOT NULL DEFAULT '',
  `is_active` TINYINT NOT NULL DEFAULT 1,
  `banned_at` DATETIME DEFAULT NULL,
  `duration_min` INT NOT NULL DEFAULT 0,
  `ban_until` DATETIME DEFAULT NULL,
  `reason` VARCHAR(255) NOT NULL DEFAULT '',
  `banned_by` VARCHAR(20) NOT NULL DEFAULT '',
  `unbanned_by` VARCHAR(20) NOT NULL DEFAULT '',
  `unbanned_at` DATETIME DEFAULT NULL,
  `ip_at_ban` VARCHAR(45) NOT NULL DEFAULT '',
  `report_count` INT NOT NULL DEFAULT 0,
  `created_at` DATETIME DEFAULT NULL,
  `updated_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`mb_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// 4) 접속자 추적
chat_create_table("CREATE TABLE IF NOT EXISTS `{$g5['chat_online_table']}` (
  `visitor_key` VARCHAR(60) NOT NULL,
  `is_member` TINYINT NOT NULL DEFAULT 0,
  `mb_id` VARCHAR(20) NOT NULL DEFAULT '',
  `mb_nick` VARCHAR(50) NOT NULL DEFAULT '',
  `ip` VARCHAR(45) NOT NULL DEFAULT '',
  `ua` VARCHAR(200) NOT NULL DEFAULT '',
  `co_region` VARCHAR(10) NOT NULL DEFAULT '전체',
  `last_ping` DATETIME NOT NULL,
  PRIMARY KEY (`visitor_key`),
  KEY `idx_ping` (`last_ping`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// 5) 신고 테이블
chat_create_table("CREATE TABLE IF NOT EXISTS `{$g5['chat_report_table']}` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `reported_nick` VARCHAR(50) NOT NULL DEFAULT '',
  `reporter_nick` VARCHAR(50) NOT NULL DEFAULT '',
  `reporter_id` VARCHAR(20) NOT NULL DEFAULT '',
  `target_id` VARCHAR(20) NOT NULL DEFAULT '',
  `cm_id` INT UNSIGNED NOT NULL DEFAULT 0,
  `reason` VARCHAR(255) NOT NULL DEFAULT '',
  `report_ip` VARCHAR(45) NOT NULL DEFAULT '',
  `ip` VARCHAR(45) NOT NULL DEFAULT '',
  `created_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_target` (`target_id`),
  KEY `idx_reporter` (`reporter_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

echo '<div style="padding:16px;border:1px solid #f0c0d0;background:#fff0f5;border-radius:12px;font-family:sans-serif;">
<b style="color:#C90050;">🌸 이브알바 채팅 플러그인 설치 완료</b><br><br>
- 메시지: '.$g5['chat_msg_table'].' (cm_region 포함)<br>
- 설정: '.$g5['chat_config_table'].'<br>
- 금지회원: '.$g5['chat_ban_table'].'<br>
- 접속자: '.$g5['chat_online_table'].' (co_region 포함)<br>
- 신고: '.$g5['chat_report_table'].'<br>
</div>';
