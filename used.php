<?php
/**
 * 중고거래 페이지 진입점
 * bo_table=used 게시판을 사이드바 레이아웃으로 표시
 */
$_GET['bo_table'] = 'used';
define('_USED_', true);

include_once('./_common.php');

if (!defined('_GNUBOARD_')) exit;

if(defined('G5_THEME_PATH')) {
    require_once(G5_THEME_PATH.'/used.php');
    return;
}

if (G5_IS_MOBILE) {
    include_once(G5_MOBILE_PATH.'/index.php');
    return;
}

include_once(G5_PATH.'/head.php');
?>
<p>중고거래 페이지입니다. 테마를 적용해 주세요.</p>
<?php
include_once(G5_PATH.'/tail.php');
?>
