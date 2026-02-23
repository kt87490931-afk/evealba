<?php
/**
 * 인재정보 페이지 진입점
 */
include_once('./_common.php');

define('_TALENT_', true);
if (!defined('_GNUBOARD_')) exit;

if(defined('G5_THEME_PATH')) {
    require_once(G5_THEME_PATH.'/talent.php');
    return;
}

if (G5_IS_MOBILE) {
    include_once(G5_MOBILE_PATH.'/index.php');
    return;
}

include_once(G5_PATH.'/head.php');
?>
<p>인재정보 페이지입니다. 테마를 적용해 주세요.</p>
<?php
include_once(G5_PATH.'/tail.php');
?>
