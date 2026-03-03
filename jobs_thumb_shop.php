<?php
/**
 * 썸네일상점 페이지 진입점
 * - 썸네일 옵션 디자인 및 구매
 */
include_once('./_common.php');

define('_JOBS_', true);
define('_THUMB_SHOP_', true);
if (!defined('_GNUBOARD_')) exit;

if(defined('G5_THEME_PATH')) {
    require_once(G5_THEME_PATH.'/jobs_thumb_shop.php');
    return;
}

include_once(G5_PATH.'/head.php');
?>
<p>썸네일상점 페이지입니다. 테마를 적용해 주세요.</p>
<?php
include_once(G5_PATH.'/tail.php');
?>
