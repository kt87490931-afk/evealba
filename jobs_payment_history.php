<?php
include_once('./_common.php');

define('_JOBS_PAYMENT_', true);
define('_JOBS_', true);
if (!defined('_GNUBOARD_')) exit;

if (!$is_member) {
    goto_url(G5_BBS_URL.'/login.php?url='.urlencode(G5_URL.'/jobs_payment_history.php'));
}

if(defined('G5_THEME_PATH')) {
    require_once(G5_THEME_PATH.'/jobs_payment.php');
    return;
}

include_once(G5_PATH.'/head.php');
?>
<p>유료결제 내역 페이지입니다.</p>
<?php include_once(G5_PATH.'/tail.php'); ?>
