-- 1:1 DM 채팅 테이블 (chat_dm_ajax.php에서도 자동 생성)
CREATE TABLE IF NOT EXISTS `g5_chat_dm_room` (
  `dm_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `jr_id` INT UNSIGNED NOT NULL DEFAULT 0,
  `female_mb_id` VARCHAR(20) NOT NULL DEFAULT '',
  `biz_mb_id` VARCHAR(20) NOT NULL DEFAULT '',
  `biz_visible` TINYINT NOT NULL DEFAULT 0,
  `last_msg_preview` VARCHAR(255) NOT NULL DEFAULT '',
  `last_msg_at` DATETIME DEFAULT NULL,
  `female_unread` INT UNSIGNED NOT NULL DEFAULT 0,
  `biz_unread` INT UNSIGNED NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL,
  PRIMARY KEY (`dm_id`),
  UNIQUE KEY `uk_pair_job` (`female_mb_id`, `biz_mb_id`, `jr_id`),
  KEY `idx_female` (`female_mb_id`, `last_msg_at`),
  KEY `idx_biz` (`biz_mb_id`, `biz_visible`, `last_msg_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `g5_chat_dm_msg` (
  `msg_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `dm_id` INT UNSIGNED NOT NULL DEFAULT 0,
  `sender_mb_id` VARCHAR(20) NOT NULL DEFAULT '',
  `msg_content` TEXT NOT NULL,
  `msg_read_at` DATETIME DEFAULT NULL,
  `msg_datetime` DATETIME NOT NULL,
  PRIMARY KEY (`msg_id`),
  KEY `idx_dm` (`dm_id`, `msg_id`),
  KEY `idx_sender` (`sender_mb_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
