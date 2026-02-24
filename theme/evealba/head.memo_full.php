<?php
/**
 * 쪽지함 전체 레이아웃 - 메인과 동일(급구·좌측사이드바·히어로배너) + breadcrumb + 단순화 상단바(유저+통계) + 탭 + 본문
 * 필요 변수(선행 설정): $memo_recv_count, $memo_unread_count, $memo_send_count, $memo_current_tab, $member_type, $member (mb_nick, mb_id)
 */
if (!defined('_GNUBOARD_')) exit;
$memo_recv_count = isset($memo_recv_count) ? (int)$memo_recv_count : 0;
$memo_unread_count = isset($memo_unread_count) ? (int)$memo_unread_count : 0;
$memo_send_count = isset($memo_send_count) ? (int)$memo_send_count : 0;
$memo_current_tab = isset($memo_current_tab) ? $memo_current_tab : 'recv';
$member_name = isset($member['mb_nick']) ? get_text($member['mb_nick']) : '';
$member_id = isset($member['mb_id']) ? $member['mb_id'] : '';
$role_icon = (isset($member_type) && strpos($member_type, '기업') !== false) ? '🏢' : '👤';
$nav_active = '';
?>
<!doctype html>
<html lang="ko">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title><?php echo isset($g5_head_title) ? $g5_head_title : '쪽지함'; ?></title>
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@300;400;500;700;900&family=Outfit:wght@300;400;700;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?php echo G5_THEME_CSS_URL ?>/default.css?ver=<?php echo G5_CSS_VER ?>">
<link rel="stylesheet" href="<?php echo G5_THEME_CSS_URL ?>/evealba.css?ver=<?php echo G5_CSS_VER ?>">
<link rel="stylesheet" href="<?php echo G5_THEME_URL ?>/css/memo_popup.css?ver=<?php echo G5_CSS_VER ?>">
<link rel="stylesheet" href="<?php echo G5_THEME_URL ?>/css/memo_full.css?ver=<?php echo G5_CSS_VER ?>">
<link rel="stylesheet" href="<?php echo G5_JS_URL ?>/font-awesome/css/font-awesome.min.css">
<script src="<?php echo G5_JS_URL ?>/jquery-1.12.4.min.js"></script>
<script src="<?php echo G5_JS_URL ?>/jquery-migrate-1.4.1.min.js"></script>
<script src="<?php echo G5_JS_URL ?>/common.js?ver=<?php echo G5_JS_VER ?>"></script>
<script>var g5_url="<?php echo G5_URL ?>"; var g5_bbs_url="<?php echo G5_BBS_URL ?>";</script>
</head>
<body class="memo-page-body">
<?php include G5_THEME_PATH.'/inc/head_top.php'; ?>

<!-- BREADCRUMB: 급구(ticker) 바로 아래, 채용정보 페이지와 동일 위치 -->
<div class="breadcrumb-bar">
  <div class="breadcrumb-inner">
    <a href="<?php echo G5_URL ?>">🏠 메인</a><span class="sep">›</span>
    <a href="<?php echo G5_BBS_URL ?>/member_confirm.php?url=<?php echo urlencode(G5_BBS_URL.'/memo.php'); ?>">마이페이지</a><span class="sep">›</span>
    <span class="current"><?php echo ($memo_current_tab==='recv') ? '📥 받은 쪽지함' : (($memo_current_tab==='unread') ? '🔔 미열람 목록' : (($memo_current_tab==='send') ? '📤 보낸 쪽지함' : '✉️ 쪽지 보내기')); ?></span>
  </div>
</div>

<!-- PAGE LAYOUT: 메인과 동일 (좌측 사이드바 + 메인) -->
<div class="page-layout">
  <?php include G5_THEME_PATH.'/inc/sidebar_main.php'; ?>
  <div class="main-area">
    <?php include G5_THEME_PATH.'/inc/ads_main_banner.php'; ?>
    <div class="memo-page-layout">
      <!-- memo-top-widget: 4칸 각 225px (회원유형 | 받은쪽지 | 미확인 | 보낸쪽지) -->
      <div class="memo-top-widget">
        <div class="memo-tw-cell memo-tw-role-cell">
          <span class="memo-tw-role"><?php echo (isset($member_type) && strpos($member_type, '기업') !== false) ? '🏢기업회원' : '👩이브회원'; ?></span>
        </div>
        <div class="memo-tw-cell memo-tw-stat"><span class="memo-tw-num"><?php echo $memo_recv_count; ?></span><span class="memo-tw-label">받은쪽지</span></div>
        <div class="memo-tw-cell memo-tw-stat"><span class="memo-tw-num orange"><?php echo $memo_unread_count; ?></span><span class="memo-tw-label">미확인</span></div>
        <div class="memo-tw-cell memo-tw-stat"><span class="memo-tw-num dark"><?php echo $memo_send_count; ?></span><span class="memo-tw-label">보낸쪽지</span></div>
      </div>
  <div class="main-area memo-main">
