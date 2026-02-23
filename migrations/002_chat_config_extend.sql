-- 채팅 설정 테이블 확장 (관리자 설정 페이지용)
-- plugin/chat/install.php 실행 후 실행. 컬럼이 이미 있으면 오류 무시 가능.

ALTER TABLE `g5_chat_config` ADD COLUMN `cf_tab1_title` VARCHAR(50) NOT NULL DEFAULT '실시간채팅' AFTER `cf_box_height`;
ALTER TABLE `g5_chat_config` ADD COLUMN `cf_tab2_title` VARCHAR(50) NOT NULL DEFAULT '채팅규정' AFTER `cf_tab1_title`;
ALTER TABLE `g5_chat_config` ADD COLUMN `cf_notice_text` VARCHAR(500) NOT NULL DEFAULT '' AFTER `cf_tab2_title`;
ALTER TABLE `g5_chat_config` ADD COLUMN `cf_rule_text` TEXT AFTER `cf_notice_text`;
ALTER TABLE `g5_chat_config` ADD COLUMN `cf_position` VARCHAR(20) NOT NULL DEFAULT 'static' AFTER `cf_rule_text`;
ALTER TABLE `g5_chat_config` ADD COLUMN `cf_top` INT NOT NULL DEFAULT 0 AFTER `cf_position`;
ALTER TABLE `g5_chat_config` ADD COLUMN `cf_left` INT NOT NULL DEFAULT 0 AFTER `cf_top`;
ALTER TABLE `g5_chat_config` ADD COLUMN `cf_spam_sec` INT NOT NULL DEFAULT 2 AFTER `cf_left`;
ALTER TABLE `g5_chat_config` ADD COLUMN `cf_repeat_sec` INT NOT NULL DEFAULT 30 AFTER `cf_spam_sec`;
ALTER TABLE `g5_chat_config` ADD COLUMN `cf_report_limit` INT NOT NULL DEFAULT 5 AFTER `cf_repeat_sec`;
ALTER TABLE `g5_chat_config` ADD COLUMN `cf_autoban_min` INT NOT NULL DEFAULT 10 AFTER `cf_report_limit`;
ALTER TABLE `g5_chat_config` ADD COLUMN `cf_badwords` TEXT AFTER `cf_autoban_min`;
