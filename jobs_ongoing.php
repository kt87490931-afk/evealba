<?php
/**
 * 진행중인 채용정보 페이지 진입점
 */
include_once('./_common.php');

define('_JOBS_ONGOING_', true);
define('_JOBS_', true);
if (!defined('_GNUBOARD_')) exit;

// 로그인 체크
if (!$is_member) {
    goto_url(G5_BBS_URL.'/login.php?url='.urlencode(G5_URL.'/jobs_ongoing.php'));
}

if(defined('G5_THEME_PATH')) {
    require_once(G5_THEME_PATH.'/jobs_ongoing.php');
    return;
}

if (G5_IS_MOBILE) {
    include_once(G5_MOBILE_PATH.'/index.php');
    return;
}

include_once(G5_PATH.'/head.php');
?>
<p>진행중인 채용정보 페이지입니다. 테마를 적용해 주세요.</p>
<?php
include_once(G5_PATH.'/tail.php');
?>
