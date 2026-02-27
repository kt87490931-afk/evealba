<?php
include_once('./_common.php');

define('_JOBS_VIEW_', true);
define('_JOBS_', true);
if (!defined('_GNUBOARD_')) exit;

/* 비회원도 채용정보 열람 가능 */

if(defined('G5_THEME_PATH')) {
    require_once(G5_THEME_PATH.'/jobs_view.php');
    return;
}

include_once(G5_PATH.'/head.php');
?>
<p>채용정보 상세 페이지입니다.</p>
<?php
include_once(G5_PATH.'/tail.php');
?>
