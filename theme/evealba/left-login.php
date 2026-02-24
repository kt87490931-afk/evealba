<?php
/**
 * 이브알바 left-login (318x205)
 * - ScorePoint용 링크(응원내역, 뱃지, sp_alarm) 제거
 * - 그누보드 기본 URL 사용: 마이페이지, 쪽지함
 */
if (!defined('_GNUBOARD_')) exit;

global $g5, $member, $is_member, $is_admin, $config;

// 세션/인증 변수 재확인
if (!isset($is_member) || !$is_member) {
    if (session_status() === PHP_SESSION_NONE) {
        @session_start();
    }
    if (isset($_SESSION['ss_mb_id']) && $_SESSION['ss_mb_id'] && function_exists('get_member')) {
        $member = get_member($_SESSION['ss_mb_id']);
        if ($member && isset($member['mb_id']) && $member['mb_id']) {
            $is_member = true;
            $is_admin = function_exists('is_admin') ? is_admin($member['mb_id']) : '';
        } else {
            $is_member = false;
            $is_admin = '';
            $member = array('mb_id'=>'', 'mb_level'=> 1, 'mb_name'=>'', 'mb_point'=> 0, 'mb_nick'=>'', 'mb_certify'=>'', 'mb_email'=>'', 'mb_open'=>'', 'mb_homepage'=>'', 'mb_tel'=>'', 'mb_hp'=>'', 'mb_zip1'=>'', 'mb_zip2'=>'', 'mb_addr1'=>'', 'mb_addr2'=>'', 'mb_addr3'=>'', 'mb_addr_jibeon'=>'', 'mb_signature'=>'', 'mb_profile'=>'');
        }
    } else {
        $is_member = false;
        $is_admin = '';
        $member = array('mb_id'=>'', 'mb_level'=> 1, 'mb_name'=>'', 'mb_point'=> 0, 'mb_nick'=>'', 'mb_certify'=>'', 'mb_email'=>'', 'mb_open'=>'', 'mb_homepage'=>'', 'mb_tel'=>'', 'mb_hp'=>'', 'mb_zip1'=>'', 'mb_zip2'=>'', 'mb_addr1'=>'', 'mb_addr2'=>'', 'mb_addr3'=>'', 'mb_addr_jibeon'=>'', 'mb_signature'=>'', 'mb_profile'=>'');
    }
}

if (!isset($g5) || !is_array($g5)) {
    $g5 = array(
        'member_table' => (defined('G5_TABLE_PREFIX') ? G5_TABLE_PREFIX : 'g5_').'member',
    );
}
if (!isset($config) || !is_array($config)) {
    $config = array('cf_admin' => '');
}

// 오늘 방문자수
$sp_visit_today = 0;
if (!empty($config['cf_visit']) && preg_match('/오늘:(\d+)/', $config['cf_visit'], $vm)) {
    $sp_visit_today = (int)$vm[1];
}

// 포인트 레벨
$point_level_file = defined('G5_THEME_PATH') ? (G5_THEME_PATH . '/inc/point_level.php') : (dirname(__FILE__) . '/inc/point_level.php');
if (is_file($point_level_file)) {
    include_once $point_level_file;
}

$box_width  = 318;
$box_height = 205;
?>
<div class="ev-login-box<?php echo $is_member ? '' : ' ev-login-guest'; ?>">
<?php if ($is_member) { ?>
    <?php
    $mb_point     = isset($member['mb_point']) ? (int)$member['mb_point'] : 0;
    $row = sql_fetch("SELECT mb_point FROM {$g5['member_table']} WHERE mb_id = '".sql_real_escape_string($member['mb_id'])."' LIMIT 1");
    if ($row !== null) {
        $mb_point = (int)($row['mb_point'] ?? 0);
    }
    $mb_point_fmt = number_format($mb_point);
    $is_super_admin = (isset($config['cf_admin']) && $member['mb_id'] === $config['cf_admin']);
    $pt_level = function_exists('sp_get_point_level_by_point') ? sp_get_point_level_by_point($mb_point, $is_super_admin) : (int)$member['mb_level'];
    $icon_index = $pt_level;
    $army_path = (defined('G5_THEME_PATH') ? G5_THEME_PATH : dirname(__FILE__)) . '/img/army/army_' . $icon_index . '.gif';
    $army_url  = (defined('G5_THEME_URL') ? G5_THEME_URL : G5_URL.'/theme/evealba') . '/img/army/army_' . $icon_index . '.gif';
    $mb_icon = (is_file($army_path)) ? '<img src="'.$army_url.'" alt="LV'.$pt_level.'" />' : '<span class="ev-level-badge">LV'.$pt_level.'</span>';
    $level_text = 'LV'.$pt_level;

    $link_mypage = G5_BBS_URL.'/member_confirm.php?url='.urlencode(G5_BBS_URL.'/register_form.php');
    $link_memo   = G5_BBS_URL.'/memo.php';
    $memo_not_read = function_exists('get_memo_not_read') ? get_memo_not_read($member['mb_id']) : 0;
    $memo_badge_count = min(99, $memo_not_read);
    ?>
    <div class="ev-login-top">
        <span class="ev-login-top-left">
            <span>보안접속 <b>ON</b></span>
            <span class="ev-sep">|</span>
            <span>IP보안 <b>ON</b></span>
        </span>
        <span class="ev-login-top-right">오늘 방문자수 <?php echo number_format($sp_visit_today); ?></span>
    </div>
    <div class="ev-login-main ev-login-main-logged">
        <div class="ev-login-header-row">
            <div class="ev-login-nick">
                <strong class="ev-login-nick-text"><?php echo get_text($member['mb_nick']); ?></strong>
                <span>님 접속중</span>
            </div>
            <a href="<?php echo G5_BBS_URL; ?>/logout.php" class="ev-logout-link">로그아웃</a>
        </div>
        <div class="ev-login-body">
            <div class="ev-login-grade">
                <div class="ev-login-grade-icon"><?php echo $mb_icon; ?></div>
                <div class="ev-login-level"><?php echo $level_text; ?></div>
            </div>
            <div class="ev-login-info">
                <div class="ev-login-point-row">
                    <span>보유포인트</span>
                    <span class="ev-login-point-num"><?php echo $mb_point_fmt; ?></span>
                    <span>P</span>
                </div>
                <div class="ev-login-menu-row">
                    <a href="<?php echo $link_mypage; ?>">마이페이지</a>
                    <span>|</span>
                    <a href="<?php echo $link_memo; ?>">쪽지함<?php if ($memo_badge_count > 0) { ?><span class="ev-memo-badge">+<?php echo $memo_badge_count; ?></span><?php } ?></a>
                </div>
            </div>
        </div>
    </div>
<?php } else { ?>
    <div class="ev-login-top">
        <span class="ev-login-top-left">
            <span>보안접속 <b>ON</b></span>
            <span class="ev-sep">|</span>
            <span>IP보안 <b>ON</b></span>
        </span>
        <span class="ev-login-top-right">오늘 방문자수 <?php echo number_format($sp_visit_today); ?></span>
    </div>
    <a href="<?php echo G5_BBS_URL; ?>/login.php" class="ev-login-main">
        <div class="ev-login-logo">
            <div class="ev-login-logo-main">이브알바</div>
            <div class="ev-login-logo-sub">여성 구인구직</div>
        </div>
        <div class="ev-login-text">로그인</div>
    </a>
    <div class="ev-login-links">
        <div class="ev-login-links-left">
            <a href="<?php echo (defined('G5_URL') && G5_URL) ? rtrim(G5_URL,'/').'/eve_register.php' : '/eve_register.php'; ?>">회원가입</a>
            <a href="<?php echo G5_BBS_URL; ?>/password_lost.php">아이디/비밀번호 찾기</a>
        </div>
    </div>
<?php } ?>
</div>
<style>
.ev-login-box{ background:#5c3d7a; border:0; border-radius:12px; overflow:hidden; box-sizing:border-box; font-family:'맑은 고딕', system-ui, sans-serif; width:318px; height:205px; max-height:205px; overflow:hidden; }
.ev-login-top{ height:38px; display:flex; align-items:center; justify-content:space-between; padding:0 10px; color:#fff; font-size:11px; box-sizing:border-box; }
.ev-login-top-left{ display:inline-flex; align-items:center; }
.ev-login-top-right{ margin-left:auto; white-space:nowrap; font-weight:700; }
.ev-login-top b{ font-weight:900; }
.ev-login-top .ev-sep{ opacity:.7; margin:0 6px; }
.ev-login-main{ display:block; background:#fff; box-sizing:border-box; height:calc(205px - 38px); padding:6px 10px; text-decoration:none; color:#111; }
.ev-login-guest .ev-login-main{ height:100px; }
.ev-login-header-row{ display:flex; align-items:center; justify-content:space-between; margin-bottom:6px; }
.ev-login-nick{ font-size:12px; color:#222; }
.ev-login-nick-text{ color:#5c3d7a; font-weight:900; }
.ev-logout-link{ font-size:11px; color:#fff; background:#5c3d7a; padding:4px 8px; border-radius:10px; text-decoration:none; line-height:1; }
.ev-logout-link:hover{ opacity:.9; }
.ev-login-body{ display:flex; }
.ev-login-grade{ width:58px; text-align:center; margin-right:8px; }
.ev-login-grade-icon{ width:38px; height:38px; margin:0 auto 3px; display:flex; align-items:center; justify-content:center; }
.ev-login-grade-icon img{ max-width:100%; max-height:100%; }
.ev-level-badge{ display:inline-block; width:32px; height:32px; line-height:32px; text-align:center; background:#5c3d7a; color:#fff; font-size:11px; font-weight:900; border-radius:8px; }
.ev-login-level{ font-size:11px; color:#222; font-weight:800; }
.ev-login-info{ flex:1; font-size:11px; }
.ev-login-point-row{ margin-bottom:3px; color:#111; }
.ev-login-point-num{ font-weight:900; color:#5c3d7a; margin:0 2px; }
.ev-login-menu-row{ font-size:11px; white-space:nowrap; }
.ev-login-menu-row a{ color:#111; text-decoration:none; font-weight:800; }
.ev-login-menu-row a:hover{ color:#5c3d7a; }
.ev-login-menu-row span{ margin:0 4px; color:#9aa7b5; }
.ev-memo-badge{ color:#e74c3c !important; margin-left:2px; font-size:11px; font-weight:900; }
.ev-login-logo{ text-align:center; margin-top:2px; }
.ev-login-logo-main{ font-size:15px; font-weight:900; letter-spacing:.3px; color:#5c3d7a; line-height:1.1; }
.ev-login-logo-sub{ font-size:10px; font-weight:700; color:#7a5c8a; margin-top:1px; }
.ev-login-text{ text-align:center; margin-top:4px; font-size:12px; font-weight:900; color:#5c3d7a; }
.ev-login-links{ background:#fff; padding:5px 10px; display:flex; justify-content:space-between; align-items:center; font-size:11px; box-sizing:border-box; min-height:36px; border-radius:0 0 12px 12px; }
.ev-login-links-left a{ margin-right:6px; color:#111; text-decoration:none; font-weight:800; }
.ev-login-links-left a:hover{ color:#5c3d7a; }
@media (max-width: 768px){ .ev-login-box{ width:100% !important; height:auto !important; min-height:0 !important; } .ev-login-main{ height:auto !important; min-height:120px; } }
</style>
