<?php
/**
 * 알림 & 채팅 페이지 진입점
 */
include_once('./_common.php');

define('G5_IS_MEMO_PAGE', true);
define('G5_IS_CHAT_HUB', true);
if (!defined('EVEALBA_RENEWAL_UI')) {
    define('EVEALBA_RENEWAL_UI', true);
}

if (!$is_member) {
    $url = urlencode((defined('G5_URL') ? rtrim(G5_URL, '/') : '') . '/memo_full.php');
    goto_url(G5_BBS_URL . '/login.php?url=' . $url);
}

if (defined('G5_THEME_PATH') && is_file(G5_THEME_PATH . '/memo_full.php')) {
    require_once(G5_THEME_PATH . '/memo_full.php');
    return;
}

alert('테마가 적용되지 않았습니다.');
