<?php
/**
 * 이력서 등록 페이지 진입점 (eve_alba_resume.html 100% 동일)
 */
include_once('./_common.php');

define('_RESUME_REGISTER_', true);
define('_TALENT_', true);
if (!defined('_GNUBOARD_')) exit;

if ($is_guest) {
    alert('회원만 이력서를 등록할 수 있습니다.', G5_BBS_URL.'/login.php?url='.urlencode(G5_BBS_URL.'/resume_register.php'));
}

if (defined('G5_THEME_PATH')) {
    require_once(G5_THEME_PATH.'/resume_register.php');
    return;
}

if (G5_IS_MOBILE) {
    include_once(G5_MOBILE_PATH.'/index.php');
    return;
}

include_once(G5_PATH.'/head.php');
?>
<p>이력서 등록 페이지입니다. 테마를 적용해 주세요.</p>
<?php
include_once(G5_PATH.'/tail.php');
?>
