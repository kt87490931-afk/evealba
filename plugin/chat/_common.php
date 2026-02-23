<?php
// /plugin/chat/_common.php
if (!defined('_GNUBOARD_')) exit;

// 테이블명(프리픽스 자동)
$g5['chat_msg_table']    = G5_TABLE_PREFIX.'chat_msg';
$g5['chat_config_table'] = G5_TABLE_PREFIX.'chat_config';
$g5['chat_ban_table']    = G5_TABLE_PREFIX.'chat_ban';
$g5['chat_icon_table']   = G5_TABLE_PREFIX.'chat_icon';

/**
 * 등급별 아이콘 가져오기
 * 우선순위: chat_icon 테이블 → /data/chat_icon/{level}.png → 기본아이콘
 */
function chat_get_icon_by_level($level) {
    global $g5;

    $sql = "SELECT ci_icon FROM {$g5['chat_icon_table']} WHERE ci_level = '".intval($level)."' ";
    $row = sql_fetch($sql);
    if ($row && $row['ci_icon'])
        return $row['ci_icon'];

    $path = G5_DATA_PATH . '/chat_icon/' . intval($level) . '.png';
    $url  = G5_DATA_URL  . '/chat_icon/' . intval($level) . '.png';
    if (file_exists($path))
        return $url;

    // 기본아이콘(없으면 표시 안함)
    if (defined('G5_IMG_URL') && file_exists(G5_PATH.'/img/chat_default_icon.png')) {
        return G5_IMG_URL . '/chat_default_icon.png';
    }
    return ''; // 아이콘 미표시
}
