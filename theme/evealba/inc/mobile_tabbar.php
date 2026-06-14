<?php
/**
 * 모바일 하단 탭바 (시안 4탭 — div.tab-btn)
 */
if (!defined('_GNUBOARD_')) exit;

$_tb_base = (defined('G5_URL') && G5_URL) ? rtrim(G5_URL, '/') : '';
$_tb_active = isset($nav_active) ? $nav_active : '';
if (defined('_INDEX_')) $_tb_active = 'jobs';

$_tb_memo_badge = 0;
if (!empty($is_member) && !empty($member['mb_id'])) {
    if (is_file(G5_LIB_PATH . '/eve_chat_dm.lib.php')) {
        include_once(G5_LIB_PATH . '/eve_chat_dm.lib.php');
        $_tb_memo_badge = min(99, (int)eve_chat_dm_unread_count($member['mb_id']));
    }
}

$_tb_tabs = array(
    array('key' => 'jobs', 'icon' => '🏠', 'label' => '홈', 'href' => G5_URL, 'match' => array('jobs', '')),
    array('key' => 'community', 'icon' => '💬', 'label' => '커뮤니티', 'href' => $_tb_base . '/sudabang.php', 'match' => array('sudabang', 'used')),
    array('key' => 'notify', 'icon' => '🔔', 'label' => '알림·채팅', 'href' => $_tb_base . '/memo_full.php', 'match' => array('memo'), 'badge' => $_tb_memo_badge),
    array('key' => 'mypage', 'icon' => '👤', 'label' => defined('_EVE_REGISTER_') ? '가입' : '마이', 'href' => defined('_EVE_REGISTER_') ? $_tb_base . '/eve_register.php' : ($is_member
        ? G5_BBS_URL . '/member_confirm.php?url=' . urlencode(G5_BBS_URL . '/register_form.php')
        : G5_BBS_URL . '/login.php'), 'match' => array('mypage', 'register')),
);
?>
<nav class="mobile-tabbar" aria-label="하단 메뉴">
<?php foreach ($_tb_tabs as $_tb) {
    $_tb_on = ($_tb_active === '' && in_array('jobs', $_tb['match'], true)) || in_array($_tb_active, $_tb['match'], true);
?>
  <div class="tab-btn<?php echo $_tb_on ? ' active' : ''; ?>" data-href="<?php echo htmlspecialchars($_tb['href']); ?>"<?php echo !empty($_tb['badge']) ? ' style="position:relative;"' : ''; ?>>
    <span class="tab-icon"><?php echo $_tb['icon']; ?></span>
<?php if (!empty($_tb['badge'])) { ?>
    <span class="tab-badge"><?php echo (int)$_tb['badge']; ?></span>
<?php } ?>
    <span><?php echo htmlspecialchars($_tb['label']); ?></span>
  </div>
<?php } ?>
</nav>
