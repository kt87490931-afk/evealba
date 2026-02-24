<?php
/**
 * 쪽지함 전체 레이아웃 - eve_alba_messages.html 동일 (top-bar, header, nav, breadcrumb, sidebar + main)
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

<div class="breadcrumb-bar">
  <div class="breadcrumb-inner">
    <a href="<?php echo G5_URL ?>">🏠 메인</a><span class="sep">›</span>
    <a href="<?php echo G5_BBS_URL ?>/member_confirm.php?url=<?php echo urlencode(G5_BBS_URL.'/memo.php'); ?>">마이페이지</a><span class="sep">›</span>
    <span class="current"><?php echo ($memo_current_tab==='recv') ? '📥 받은 쪽지함' : (($memo_current_tab==='unread') ? '🔔 미열람 목록' : (($memo_current_tab==='send') ? '📤 보낸 쪽지함' : '✉️ 쪽지 보내기')); ?></span>
  </div>
</div>

<div class="page-layout memo-page-layout">
  <div class="sidebar memo-sidebar">
    <div class="profile-card">
      <div class="profile-card-header">
        <div class="profile-avatar"><?php echo $member_id ? get_member_profile_img($member_id) : '👤'; ?></div>
        <div class="profile-name"><?php echo htmlspecialchars($member_name); ?> <span>님</span></div>
        <div class="profile-greeting">즐거운 하루되세요! 🌸</div>
        <span class="profile-role"><?php echo $role_icon; ?> <?php echo isset($member_type) ? htmlspecialchars($member_type) : '일반회원'; ?></span>
      </div>
      <div class="profile-card-body">
        <div class="msg-stat-grid">
          <div class="msg-stat"><div class="msg-stat-num"><?php echo $memo_recv_count; ?></div><div class="msg-stat-label">받은쪽지</div></div>
          <div class="msg-stat"><div class="msg-stat-num orange"><?php echo $memo_unread_count; ?></div><div class="msg-stat-label">미확인</div></div>
          <div class="msg-stat"><div class="msg-stat-num dark"><?php echo $memo_send_count; ?></div><div class="msg-stat-label">보낸쪽지</div></div>
        </div>
        <a href="<?php echo G5_BBS_URL ?>/memo_form.php" class="btn-new-msg">✉️ 쪽지 보내기</a>
      </div>
    </div>
    <div class="sidebar-menu">
      <div class="sidebar-menu-title">✉️ 쪽지함</div>
      <a href="<?php echo G5_BBS_URL ?>/memo.php?kind=recv" class="sidebar-menu-item <?php echo ($memo_current_tab==='recv')?'active':''; ?>">
        <span class="sidebar-menu-icon">📥</span>받은 쪽지함<?php if ($memo_recv_count) { ?><span class="smb"><?php echo $memo_recv_count; ?></span><?php } ?>
      </a>
      <a href="<?php echo G5_BBS_URL ?>/memo.php?kind=unread" class="sidebar-menu-item <?php echo ($memo_current_tab==='unread')?'active':''; ?>">
        <span class="sidebar-menu-icon">🔔</span>미열람 목록<?php if ($memo_unread_count) { ?><span class="smb orange"><?php echo $memo_unread_count; ?></span><?php } ?>
      </a>
      <a href="<?php echo G5_BBS_URL ?>/memo.php?kind=send" class="sidebar-menu-item <?php echo ($memo_current_tab==='send')?'active':''; ?>">
        <span class="sidebar-menu-icon">📤</span>보낸 쪽지함<span class="smb gray"><?php echo $memo_send_count; ?></span>
      </a>
      <a href="<?php echo G5_BBS_URL ?>/memo_form.php" class="sidebar-menu-item <?php echo ($memo_current_tab==='form')?'active':''; ?>">
        <span class="sidebar-menu-icon">✏️</span>쪽지 보내기
      </a>
    </div>
  </div>
  <div class="main-area memo-main">
