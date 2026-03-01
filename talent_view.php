<?php
/**
 * 인재정보 상세보기 진입점
 */
include_once('./_common.php');

define('_TALENT_VIEW_', true);
if (!defined('_GNUBOARD_')) exit;

if (defined('G5_THEME_PATH')) {
    require_once(G5_THEME_PATH.'/talent_view.php');
    return;
}

include_once(G5_PATH.'/head.php');
echo '<p>인재정보 상세 페이지입니다. 테마를 적용해 주세요.</p>';
include_once(G5_PATH.'/tail.php');
