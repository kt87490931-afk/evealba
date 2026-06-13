<?php
/**
 * 시안 좌측 사이드바 — 로고·메뉴·로그인·지역·고객센터
 */
if (!defined('_GNUBOARD_')) exit;

$_nav_base = (defined('G5_URL') && G5_URL) ? rtrim(G5_URL, '/') : '';
$_nav_active = isset($nav_active) ? $nav_active : '';
if (defined('_INDEX_')) $_nav_active = 'jobs';

$_nav_memo_badge = 0;
if (!empty($is_member) && !empty($member['mb_id']) && function_exists('get_memo_not_read')) {
    $_nav_memo_badge = min(99, (int)get_memo_not_read($member['mb_id']));
}

$_nav_items = array(
    array('key' => 'jobs', 'icon' => '🏠', 'label' => '구인구직', 'href' => G5_URL, 'match' => array('jobs', '')),
    array('key' => 'community', 'icon' => '💬', 'label' => '커뮤니티', 'href' => $_nav_base . '/sudabang.php', 'match' => array('sudabang', 'used')),
    array('key' => 'notify', 'icon' => '🔔', 'label' => '알림 & 채팅', 'href' => $_nav_base . '/memo_full.php', 'match' => array('memo'), 'badge' => $_nav_memo_badge),
    array('key' => 'mypage', 'icon' => '👤', 'label' => '마이페이지', 'href' => $is_member
        ? G5_BBS_URL . '/member_confirm.php?url=' . urlencode(G5_BBS_URL . '/register_form.php')
        : G5_BBS_URL . '/login.php', 'match' => array('mypage')),
);

function _ev_nav_is_active_mockup($item, $active) {
    if ($active === '' && in_array('jobs', $item['match'], true)) return true;
    return in_array($active, $item['match'], true);
}

$_regions = array('서울', '경기', '인천', '부산', '대구', '광주', '대전', '울산', '강원', '충청', '전라', '경상');
?>
<div class="sidebar-logo">
  <a href="<?php echo G5_URL; ?>" class="logo-text">eve<span>'알바</span></a>
</div>

<nav class="sidebar-nav" aria-label="메인 메뉴">
<?php foreach ($_nav_items as $_ni) {
    $_is_active = _ev_nav_is_active_mockup($_ni, $_nav_active);
?>
  <a href="<?php echo htmlspecialchars($_ni['href']); ?>" class="nav-item<?php echo $_is_active ? ' active' : ''; ?>">
    <span class="nav-icon"><?php echo $_ni['icon']; ?></span>
    <span class="nav-label"><?php echo htmlspecialchars($_ni['label']); ?></span>
<?php if (!empty($_ni['badge'])) { ?>
    <span class="nav-badge"><?php echo (int)$_ni['badge']; ?></span>
<?php } ?>
  </a>
<?php } ?>
</nav>

<div class="sidebar-auth">
<?php if ($is_member) { ?>
  <div class="sidebar-user-info" style="text-align:center;font-size:13px;margin-bottom:8px;">
    <strong><?php echo get_text($member['mb_nick']); ?></strong>님
  </div>
  <a href="<?php echo G5_BBS_URL; ?>/logout.php" class="btn-login-full">로그아웃</a>
<?php } else { ?>
  <button type="button" class="btn-login-full" onclick="location.href='<?php echo G5_BBS_URL; ?>/login.php'">로그인</button>
  <p class="sidebar-join">회원이 아니신가요? <a href="<?php echo $_nav_base; ?>/eve_register.php">가입하기</a></p>
<?php } ?>
</div>

<div class="sidebar-region">
  <h5>지역별 검색</h5>
  <div class="region-grid">
<?php foreach ($_regions as $_rg) { ?>
    <a class="region-btn" href="<?php echo $_nav_base; ?>/jobs.php?stx=<?php echo urlencode($_rg); ?>"><?php echo $_rg; ?></a>
<?php } ?>
  </div>
</div>

<div class="sidebar-cs">
  <p style="font-size:12px;color:var(--gray);margin-bottom:4px;">📞 이브알바 고객센터</p>
  <div class="cs-num">1588-0000</div>
  <div class="cs-time">평일 09:30~19:00 · 점심 12:00~13:30</div>
  <a href="#" class="btn-kakao" onclick="return false;">💛 EveAlba 카카오 채널</a>
</div>
