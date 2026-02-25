<?php
/**
 * 채용공고 등록 페이지 전용 좌측 사이드바
 * - MY PAGE 메뉴 5개: 채용정보등록, 진행중인 채용정보, 마감된 채용정보, 유료결제 내역, 회원정보 수정
 */
if (!defined('_GNUBOARD_')) exit;

$jobs_base_url = (defined('G5_URL') && G5_URL) ? rtrim(G5_URL,'/') : '';
$jobs_register_url = $jobs_base_url ? $jobs_base_url.'/jobs_register.php' : '/jobs_register.php';
$jobs_mem_confirm_url = G5_BBS_URL.'/member_confirm.php?url='.urlencode(G5_BBS_URL.'/register_form.php');

$jobs_mypage_active = isset($jobs_mypage_active) ? $jobs_mypage_active : 'register';
?>
<!-- 마이페이지 -->
<div class="sidebar-widget">
  <div class="mypage-header">
    <span class="mypage-icon">👑</span>
    <div>
      <div class="mypage-title">MY PAGE</div>
      <div class="mypage-sub">마이페이지</div>
    </div>
  </div>
  <div class="side-menu-list">
    <a href="<?php echo $jobs_register_url; ?>" class="side-menu-item<?php echo ($jobs_mypage_active === 'register') ? ' active' : ''; ?>">📝 채용정보등록</a>
    <a href="#" class="side-menu-item<?php echo ($jobs_mypage_active === 'ongoing') ? ' active' : ''; ?>">📋 진행중인 채용정보</a>
    <a href="#" class="side-menu-item<?php echo ($jobs_mypage_active === 'ended') ? ' active' : ''; ?>">📁 마감된 채용정보</a>
    <a href="#" class="side-menu-item<?php echo ($jobs_mypage_active === 'payment') ? ' active' : ''; ?>">💳 유료결제 내역</a>
    <a href="<?php echo $jobs_mem_confirm_url; ?>" class="side-menu-item<?php echo ($jobs_mypage_active === 'member') ? ' active' : ''; ?>">⚙️ 회원정보 수정</a>
  </div>
</div>

<!-- 사이드 CTA 버튼 -->
<div class="sidebar-widget">
  <div class="widget-body" style="padding:10px;">
    <a href="<?php echo $jobs_register_url; ?>" class="side-cta-btn btn-job-reg" style="text-decoration:none;display:flex;align-items:center;justify-content:center;">✏️ 채용공고 등록하기</a>
    <button type="button" class="side-cta-btn btn-jump">⚡ 점프옵션 구매하기</button>
  </div>
</div>

<!-- 기업회원 서비스 -->
<div class="sidebar-widget">
  <div class="widget-title">💼 기업회원 서비스</div>
  <div class="widget-body">
    <div class="biz-service-box">
      <div class="biz-service-title">🌟 프리미엄 광고 서비스</div>
      <a href="#" class="biz-service-link">💎 기업회원 서비스</a>
      <a href="#" class="biz-service-link2">💰 추가옵션 결제</a>
    </div>
  </div>
</div>

<?php include G5_THEME_PATH.'/inc/sidebar_cs_widget.php'; ?>
