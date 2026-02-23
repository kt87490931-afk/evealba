<?php
// /plugin/chat/install.php
include_once('../../common.php');
include_once('./_common.php');

// 관리자만
if (!$is_admin) die('관리자만 접근 가능합니다.');

// PHP 하위버전도 동작하는 문법만 사용
function chat_create_table($sql){
    // true: 존재해도 오류 무시 → 재실행 안전
    sql_query($sql, true);
}

/* 1) 메시지 테이블 */
chat_create_table("CREATE TABLE IF NOT EXISTS `{$g5['chat_msg_table']}` (
  `cm_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `mb_id` VARCHAR(20) NOT NULL DEFAULT '',
  `cm_nick` VARCHAR(50) NOT NULL DEFAULT '',
  `cm_icon` VARCHAR(255) NOT NULL DEFAULT '',
  `cm_content` TEXT NOT NULL,
  `cm_datetime` DATETIME NOT NULL,
  PRIMARY KEY (`cm_id`),
  KEY `mb_id` (`mb_id`),
  KEY `cm_datetime` (`cm_datetime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

/* 2) 설정 테이블 */
chat_create_table("CREATE TABLE IF NOT EXISTS `{$g5['chat_config_table']}` (
  `cf_id` TINYINT NOT NULL DEFAULT 1,
  `cf_title` VARCHAR(50) NOT NULL DEFAULT '실시간 채팅',
  `cf_freeze` TINYINT NOT NULL DEFAULT 0,
  `cf_online_count` INT NOT NULL DEFAULT 0,
  `cf_box_width` INT NOT NULL DEFAULT 320,
  `cf_box_height` INT NOT NULL DEFAULT 420,
  PRIMARY KEY (`cf_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

sql_query("INSERT INTO `{$g5['chat_config_table']}` (`cf_id`)
          VALUES (1)
          ON DUPLICATE KEY UPDATE `cf_id`=`cf_id`");

/* 3) 채팅금지 회원 */
chat_create_table("CREATE TABLE IF NOT EXISTS `{$g5['chat_ban_table']}` (
  `mb_id` VARCHAR(20) NOT NULL,
  `ban_memo` VARCHAR(255) NOT NULL DEFAULT '',
  `ban_datetime` DATETIME NOT NULL,
  PRIMARY KEY (`mb_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

/* 4) 등급 아이콘 */
chat_create_table("CREATE TABLE IF NOT EXISTS `{$g5['chat_icon_table']}` (
  `ci_level` INT NOT NULL,
  `ci_icon` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`ci_level`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

echo '<div style="padding:12px;border:1px solid #ddd;background:#fafafa">
<b>채팅 플러그인 설치 완료</b><br>
- 메시지: '.$g5['chat_msg_table'].'<br>
- 설정: '.$g5['chat_config_table'].'<br>
- 금지회원: '.$g5['chat_ban_table'].'<br>
- 등급아이콘: '.$g5['chat_icon_table'].'<br>
</div>';
