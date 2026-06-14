<?php
/**
 * 로그인·회원확인 페이지 리뉴얼 UI 활성화
 */
if (!defined('_GNUBOARD_')) exit;

$_ev_script = isset($_SERVER['SCRIPT_NAME']) ? basename($_SERVER['SCRIPT_NAME']) : '';

if ($_ev_script === 'login.php') {
    if (!defined('G5_IS_LOGIN_PAGE')) {
        define('G5_IS_LOGIN_PAGE', true);
    }
    if (!defined('EVEALBA_RENEWAL_UI')) {
        define('EVEALBA_RENEWAL_UI', true);
    }
}

if ($_ev_script === 'member_confirm.php') {
    if (!defined('G5_IS_MEMBER_CONFIRM_PAGE')) {
        define('G5_IS_MEMBER_CONFIRM_PAGE', true);
    }
    if (!defined('EVEALBA_RENEWAL_UI')) {
        define('EVEALBA_RENEWAL_UI', true);
    }
}
