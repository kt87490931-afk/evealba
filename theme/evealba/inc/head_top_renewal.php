<?php
/**
 * 시안 상단 — top-notice + 급구 마퀴
 */
if (!defined('_GNUBOARD_')) exit;
$_ht_base = (defined('G5_URL') && G5_URL) ? rtrim(G5_URL, '/') : '';
?>
<div class="top-notice">
  <span>🎉 이브알바에 오신 것을 환영합니다! &nbsp; 고객센터: 1588-0000 (평일 09:00~18:00)</span>
  <div>
<?php if ($is_member) { ?>
    <a href="<?php echo G5_BBS_URL; ?>/member_confirm.php?url=<?php echo urlencode(G5_BBS_URL . '/register_form.php'); ?>">마이페이지</a>
    <a href="<?php echo G5_BBS_URL; ?>/logout.php">로그아웃</a>
<?php } else { ?>
    <a href="<?php echo G5_BBS_URL; ?>/login.php">로그인</a>
    <a href="<?php echo $_ht_base; ?>/eve_register.php">회원가입</a>
<?php } ?>
    <a href="<?php echo $_ht_base; ?>/cs.php">고객센터</a>
  </div>
</div>
<?php include G5_THEME_PATH . '/inc/marquee_bar_renewal.php'; ?>
