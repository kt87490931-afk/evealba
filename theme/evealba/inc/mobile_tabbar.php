<?php
/**
 * 모바일 하단 탭바 (Readdy 4탭)
 */
if (!defined('_GNUBOARD_')) exit;

$_tb_base = (defined('G5_URL') && G5_URL) ? rtrim(G5_URL, '/') : '';
$_tb_active = isset($nav_active) ? $nav_active : '';
if (defined('_INDEX_')) $_tb_active = 'jobs';

$_tb_memo_badge = 0;
if (!empty($is_member) && !empty($member['mb_id']) && function_exists('get_memo_not_read')) {
    $_tb_memo_badge = min(99, (int)get_memo_not_read($member['mb_id']));
}

$_tb_tabs = array(
    array('key' => 'jobs', 'icon' => 'ri-home-5-line', 'label' => '홈', 'href' => G5_URL, 'match' => array('jobs', '')),
    array('key' => 'community', 'icon' => 'ri-chat-3-line', 'label' => '커뮤니티', 'href' => $_tb_base . '/sudabang.php', 'match' => array('sudabang', 'used')),
    array('key' => 'notify', 'icon' => 'ri-notification-3-line', 'label' => '알림', 'href' => $_tb_base . '/memo_full.php', 'match' => array('memo'), 'badge' => $_tb_memo_badge),
    array('key' => 'mypage', 'icon' => 'ri-user-line', 'label' => '마이', 'href' => $is_member
        ? G5_BBS_URL . '/member_confirm.php?url=' . urlencode(G5_BBS_URL . '/register_form.php')
        : G5_BBS_URL . '/login.php', 'match' => array('mypage')),
);
?>
<nav class="mobile-tabbar" aria-label="하단 메뉴">
<?php foreach ($_tb_tabs as $_tb) {
    $_tb_on = ($_tb_active === '' && in_array('jobs', $_tb['match'], true)) || in_array($_tb_active, $_tb['match'], true);
?>
  <a href="<?php echo htmlspecialchars($_tb['href']); ?>" class="tab-item<?php echo $_tb_on ? ' active' : ''; ?>">
    <i class="<?php echo $_tb['icon']; ?> tab-icon" aria-hidden="true"></i>
    <span><?php echo htmlspecialchars($_tb['label']); ?></span>
<?php if (!empty($_tb['badge'])) { ?>
    <span class="badge-count"><?php echo (int)$_tb['badge']; ?></span>
<?php } ?>
  </a>
<?php } ?>
</nav>
