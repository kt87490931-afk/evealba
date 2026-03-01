<?php
/**
 * 인재정보 상세보기 (테마 래퍼)
 */
if (!defined('_TALENT_VIEW_')) define('_TALENT_VIEW_', true);
if (!defined('_GNUBOARD_')) exit;

$rs_id = isset($_GET['rs_id']) ? (int)$_GET['rs_id'] : 0;
$rs_row = null;
$rs_data = array();

if ($rs_id > 0) {
    $tb_check = @sql_query("SHOW TABLES LIKE 'g5_resume'", false);
    if ($tb_check && @sql_num_rows($tb_check)) {
        $rs_row = sql_fetch("SELECT * FROM g5_resume WHERE rs_id = '{$rs_id}' AND rs_status = 'active'");
        if ($rs_row && $rs_row['rs_data']) {
            $rs_data = @json_decode($rs_row['rs_data'], true);
            if (!is_array($rs_data)) $rs_data = array();
        }
    }
}

$g5['title'] = ($rs_row ? htmlspecialchars($rs_row['rs_title']) : '인재정보').' - '.$config['cf_title'];
include_once(G5_THEME_PATH.'/head_talent.php');

if (!$rs_row) {
    echo '<div style="text-align:center;padding:60px 0;"><p style="font-size:18px;color:#999;">존재하지 않는 이력서이거나 삭제된 이력서입니다.</p>';
    echo '<a href="'.(defined('G5_URL')?rtrim(G5_URL,'/').'/talent.php':'/talent.php').'" style="display:inline-block;margin-top:20px;padding:10px 24px;background:var(--hot-pink,#FF1B6B);color:#fff;border-radius:20px;text-decoration:none;">📋 목록으로</a></div>';
} else {
    include(G5_THEME_PATH.'/talent_view_main.php');
}

include_once(G5_THEME_PATH.'/tail.php');
