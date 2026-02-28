<?php
// /plugin/chat/uninstall.php — 이브알바 채팅 테이블 삭제
include_once('../../common.php');
include_once('./_common.php');
if (!$is_admin) die('관리자만 접근 가능합니다.');

$sqls = array(
  "DROP TABLE IF EXISTS `{$g5['chat_msg_table']}`",
  "DROP TABLE IF EXISTS `{$g5['chat_config_table']}`",
  "DROP TABLE IF EXISTS `{$g5['chat_ban_table']}`",
  "DROP TABLE IF EXISTS `{$g5['chat_online_table']}`",
  "DROP TABLE IF EXISTS `{$g5['chat_report_table']}`"
);

for ($i=0; $i<count($sqls); $i++) {
    sql_query($sqls[$i], true);
}

echo '<div style="padding:16px;border:1px solid #f0c0d0;background:#fff0f5;border-radius:12px;font-family:sans-serif;">
<b style="color:#C90050;">🌸 이브알바 채팅 플러그인 테이블 삭제 완료</b>
</div>';
