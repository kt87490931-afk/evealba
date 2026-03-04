<?php
/**
 * 사이드바 로그인 위젯 - Gnuboard 연동
 * - 로그인 폼(비로그인) / 회원정보+로그아웃(로그인)
 * - 오늘 방문자 수 (g5_visit / cf_visit)
 */
if (!defined('_GNUBOARD_')) exit;

global $config, $member, $is_member, $g5;

// 오늘 방문자 수 (cf_visit: "오늘:X,어제:Y,최대:Z,전체:W")
$ev_visit_today = 0;
if (!empty($config['cf_visit']) && preg_match('/오늘:(\d+)/', $config['cf_visit'], $vm)) {
    $ev_visit_today = (int)$vm[1];
}
$ev_visit_fmt = number_format($ev_visit_today);

// 로그인 후 이동 URL
$ev_login_redirect = function_exists('login_url') ? login_url(isset($urlencode) ? $urlencode : G5_URL) : '';
$ev_login_action = G5_HTTPS_BBS_URL . '/login_check.php';
?>
<div class="sidebar-widget">
  <div class="widget-title">🌸 로그인</div>
  <div class="login-visitor">오늘 방문 <strong><?php echo $ev_visit_fmt; ?></strong>명</div>
  <div class="widget-body">
<?php if ($is_member) {
    $ev_member_type = '👩이브회원';
    if (!empty($member['mb_id'])) {
        $mb_id_esc = sql_escape_string($member['mb_id']);
        $row = sql_fetch("SELECT 1 as is_biz FROM {$g5['member_table']} WHERE mb_id = '{$mb_id_esc}' AND (mb_1 = 'biz' OR mb_1 = 'business') LIMIT 1");
        if ($row && !empty($row['is_biz'])) $ev_member_type = '🏢기업회원';
    }
?>
    <div class="login-logged">
      <div class="login-logged-info">
        <strong><?php echo get_text($member['mb_nick']); ?></strong>님 접속중
        <div class="login-member-type"><?php echo $ev_member_type; ?></div>
      </div>
      <div class="login-logged-actions">
        <a href="<?php echo G5_BBS_URL; ?>/member_confirm.php?url=<?php echo urlencode(G5_BBS_URL.'/register_form.php'); ?>">마이페이지</a>
        <span class="sep">|</span>
        <a href="<?php echo G5_BBS_URL; ?>/logout.php">로그아웃</a>
      </div>
    </div>
<?php } else { ?>
    <form name="fsidebar_login" method="post" action="<?php echo $ev_login_action; ?>" autocomplete="off" class="login-form">
      <input type="hidden" name="url" value="<?php echo $ev_login_redirect; ?>">
      <input type="text" name="mb_id" placeholder="아이디" required maxlength="20">
      <input type="password" name="mb_password" placeholder="비밀번호" required maxlength="20">
      <button type="submit">로그인</button>
    </form>
    <div class="login-links">
      <a href="<?php echo (defined('G5_URL') && G5_URL) ? rtrim(G5_URL,'/').'/eve_register.php' : '/eve_register.php'; ?>">회원가입</a><span class="sep">|</span>
      <a href="<?php echo G5_BBS_URL; ?>/password_lost.php">아이디 찾기</a><span class="sep">|</span>
      <a href="<?php echo G5_BBS_URL; ?>/password_lost.php">비밀번호</a>
    </div>
<?php } ?>
  </div>
</div>
