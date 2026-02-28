<?php
// 채팅 테이블 설치 스크립트 (CLI 전용)
$_SERVER['HTTP_HOST'] = '188.166.179.115';
$_SERVER['REQUEST_URI'] = '/scripts/install_chat_tables.php';
$_SERVER['SCRIPT_NAME'] = '/scripts/install_chat_tables.php';
$_SERVER['DOCUMENT_ROOT'] = '/var/www/evealba';

include_once(__DIR__ . '/../common.php');
include_once(__DIR__ . '/../plugin/chat/_common.php');

function ct($sql){ sql_query($sql, true); }

ct("CREATE TABLE IF NOT EXISTS `{$g5['chat_msg_table']}` (
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

ct("CREATE TABLE IF NOT EXISTS `{$g5['chat_config_table']}` (
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

ct("CREATE TABLE IF NOT EXISTS `{$g5['chat_ban_table']}` (
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

ct("CREATE TABLE IF NOT EXISTS `{$g5['chat_online_table']}` (
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

ct("CREATE TABLE IF NOT EXISTS `{$g5['chat_report_table']}` (
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

echo "ALL CHAT TABLES CREATED OK\n";
echo "- {$g5['chat_msg_table']}\n";
echo "- {$g5['chat_config_table']}\n";
echo "- {$g5['chat_ban_table']}\n";
echo "- {$g5['chat_online_table']}\n";
echo "- {$g5['chat_report_table']}\n";
