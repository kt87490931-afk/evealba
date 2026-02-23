<?php
// /plugin/chat/uninstall.php
include_once('../../common.php');
include_once('./_common.php');
if (!$is_admin) die('관리자만 접근 가능합니다.');

$sqls = array(
  "DROP TABLE IF EXISTS `{$g5['chat_msg_table']}`",
  "DROP TABLE IF EXISTS `{$g5['chat_config_table']}`",
  "DROP TABLE IF EXISTS `{$g5['chat_ban_table']}`",
  "DROP TABLE IF EXISTS `{$g5['chat_icon_table']}`"
);

for ($i=0; $i<count($sqls); $i++) {
    sql_query($sqls[$i], true);
}

echo '<div style="padding:12px;border:1px solid #ddd;background:#fffbe6">
채팅 플러그인 테이블 삭제 완료
</div>';
