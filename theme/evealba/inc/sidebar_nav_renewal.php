<?php
/**
 * Readdy형 좌측 세로 네비게이션 (4탭) + 하단 푸터 링크
 */
if (!defined('_GNUBOARD_')) exit;

$_nav_base = (defined('G5_URL') && G5_URL) ? rtrim(G5_URL, '/') : '';
$_nav_active = isset($nav_active) ? $nav_active : '';
if (defined('_INDEX_')) $_nav_active = 'jobs';

$_nav_items = array(
    array('key' => 'jobs', 'icon' => 'ri-home-5-line', 'label' => '구인구직', 'href' => $_nav_base . '/jobs.php', 'match' => array('jobs', '')),
    array('key' => 'community', 'icon' => 'ri-chat-3-line', 'label' => '커뮤니티', 'href' => $_nav_base . '/sudabang.php', 'match' => array('sudabang', 'used')),
    array('key' => 'notify', 'icon' => 'ri-notification-3-line', 'label' => '알림 & 채팅', 'href' => $_nav_base . '/memo_full.php', 'match' => array('memo')),
    array('key' => 'mypage', 'icon' => 'ri-user-line', 'label' => '마이페이지', 'href' => $is_member
        ? G5_BBS_URL . '/member_confirm.php?url=' . urlencode(G5_BBS_URL . '/register_form.php')
        : G5_BBS_URL . '/login.php', 'match' => array('mypage')),
);

function _ev_nav_is_active($item, $active) {
    if ($active === '' && in_array('jobs', $item['match'], true)) return true;
    return in_array($active, $item['match'], true);
}
?>
<div class="sidebar-logo-renewal">
  <a href="<?php echo G5_URL; ?>" class="sidebar-brand">EVE <span>ALBA</span></a>
</div>
<nav class="sidebar-nav-renewal" aria-label="메인 메뉴">
<?php foreach ($_nav_items as $_ni) {
    $_is_active = _ev_nav_is_active($_ni, $_nav_active);
?>
  <a href="<?php echo htmlspecialchars($_ni['href']); ?>" class="nav-item<?php echo $_is_active ? ' active' : ''; ?>">
    <i class="<?php echo $_ni['icon']; ?> nav-icon" aria-hidden="true"></i>
    <span><?php echo htmlspecialchars($_ni['label']); ?></span>
  </a>
<?php } ?>
</nav>
<div class="sidebar-login-renewal">
<?php if ($is_member) { ?>
  <div class="sidebar-user-renewal">
    <strong><?php echo get_text($member['mb_nick']); ?></strong>님<br>
    <a href="<?php echo G5_BBS_URL; ?>/logout.php" class="sidebar-logout-link">로그아웃</a>
  </div>
<?php } else { ?>
  <a href="<?php echo G5_BBS_URL; ?>/login.php" class="btn-login">로그인</a>
  <a href="<?php echo $_nav_base; ?>/eve_register.php" class="btn-join">회원이 아니신가요? <span>가입하기</span></a>
<?php } ?>
</div>
<div class="sidebar-footer-renewal">
  <a href="<?php echo $_nav_base; ?>/cs.php">회사정보</a>
  <span>|</span>
  <a href="<?php echo get_pretty_url('content', 'provision'); ?>">이용약관</a>
  <span>|</span>
  <a href="#">채용사업자 명단</a>
  <p class="sidebar-copyright">© 2026 이브알바</p>
</div>
