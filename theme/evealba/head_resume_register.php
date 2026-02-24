<?php
/**
 * 이력서 등록 페이지 전용 head (eve_alba_resume.html 100% 동일)
 * - breadcrumb, page-layout, sidebar_resume_register, main-area
 */
if (!defined('_GNUBOARD_')) exit;

if (G5_COMMUNITY_USE === false) {
    define('G5_IS_COMMUNITY_PAGE', true);
    include_once(G5_THEME_SHOP_PATH.'/shop.head.php');
    return;
}
include_once(G5_THEME_PATH.'/head.sub.php');
include_once(G5_LIB_PATH.'/latest.lib.php');
include_once(G5_LIB_PATH.'/outlogin.lib.php');
include_once(G5_LIB_PATH.'/visit.lib.php');
include_once(G5_LIB_PATH.'/connect.lib.php');
include_once(G5_LIB_PATH.'/popular.lib.php');

$nav_active = 'talent';
include G5_THEME_PATH.'/inc/head_top.php';

$resume_register_url = (defined('G5_URL') && G5_URL) ? rtrim(G5_URL,'/').'/resume_register.php' : '/resume_register.php';
$talent_url = (defined('G5_URL') && G5_URL) ? rtrim(G5_URL,'/').'/talent.php' : '/talent.php';
$mypage_url = G5_BBS_URL.'/member_confirm.php?url='.urlencode(G5_BBS_URL.'/register_form.php');
?>

<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@300;400;500;700;900&family=Outfit:wght@300;400;700;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?php echo G5_THEME_URL; ?>/css/resume_register.css?v=<?php echo @filemtime(G5_THEME_PATH.'/css/resume_register.css'); ?>">

<!-- BREADCRUMB -->
<div class="breadcrumb-bar">
  <div class="breadcrumb-inner">
    <a href="<?php echo G5_URL ?>">🏠 메인</a>
    <span class="sep">›</span>
    <a href="<?php echo $mypage_url; ?>">마이페이지</a>
    <span class="sep">›</span>
    <a href="<?php echo $talent_url; ?>">인재정보</a>
    <span class="sep">›</span>
    <span class="current">📄 이력서 등록</span>
  </div>
</div>

<!-- PAGE LAYOUT -->
<div class="page-layout resume-register-page">

  <!-- 좌측 사이드바 (이력서 등록용) -->
  <aside class="left-sidebar">
    <?php include G5_THEME_PATH.'/inc/sidebar_resume_register.php'; ?>
  </aside>

  <!-- 메인 영역 -->
  <div class="main-area">
