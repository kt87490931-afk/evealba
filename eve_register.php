<?php
/**
 * 회원가입 페이지 진입점 (eve_alba_register.html 기준, footer-info 제외)
 * Step1 약관동의 → Step2 회원정보입력 → Step3 가입완료
 */
include_once('./_common.php');

define('_EVE_REGISTER_', true);
if (!defined('_GNUBOARD_')) exit;

if(defined('G5_THEME_PATH')) {
    require_once(G5_THEME_PATH.'/register.php');
    return;
}

if (G5_IS_MOBILE) {
    include_once(G5_MOBILE_PATH.'/index.php');
    return;
}

include_once(G5_PATH.'/head.php');
?>
<p>회원가입 페이지입니다. 테마를 적용해 주세요.</p>
<?php
include_once(G5_PATH.'/tail.php');
?>
