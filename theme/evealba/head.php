<?php
if (!defined('_GNUBOARD_')) exit;

if (G5_IS_MOBILE) {
    include_once(G5_THEME_MOBILE_PATH.'/head.php');
    return;
}

if(G5_COMMUNITY_USE === false) {
    define('G5_IS_COMMUNITY_PAGE', true);
    include_once(G5_THEME_SHOP_PATH.'/shop.head.php');
    return;
}
include_once(G5_THEME_PATH.'/head.sub.php');
include_once(G5_LIB_PATH.'/latest.lib.php');
include_once(G5_LIB_PATH.'/outlogin.lib.php');
include_once(G5_LIB_PATH.'/poll.lib.php');
include_once(G5_LIB_PATH.'/visit.lib.php');
include_once(G5_LIB_PATH.'/connect.lib.php');
include_once(G5_LIB_PATH.'/popular.lib.php');
?>

<?php if(defined('_INDEX_')) { include G5_BBS_PATH.'/newwin.inc.php'; } ?>

<?php include G5_THEME_PATH.'/inc/head_top.php'; ?>

<!-- PAGE LAYOUT -->
<div class="page-layout">

  <!-- 좌측 사이드바 -->
  <aside class="left-sidebar">
    <div class="sidebar-widget">
      <div class="widget-title">🌸 로그인</div>
      <div class="login-visitor">오늘 방문 <strong>24,153</strong>명</div>
      <div class="widget-body">
        <div class="login-form">
          <input type="text" placeholder="아이디">
          <input type="password" placeholder="비밀번호">
          <button>로그인</button>
        </div>
        <div class="login-links">
          <a href="<?php echo G5_BBS_URL ?>/register.php">회원가입</a><span class="sep">|</span>
          <a href="<?php echo G5_BBS_URL ?>/password_lost.php">아이디 찾기</a><span class="sep">|</span>
          <a href="<?php echo G5_BBS_URL ?>/password_lost.php">비밀번호</a>
        </div>
      </div>
    </div>
    <div class="sidebar-widget">
      <div class="widget-title">⚡ 빠른 메뉴</div>
      <div class="widget-body">
        <div class="quick-links">
          <a href="#" class="quick-link-btn"><span class="ql-icon">📋</span>채용공고 등록</a>
          <a href="#" class="quick-link-btn"><span class="ql-icon">👩</span>이력서 등록</a>
          <a href="#" class="quick-link-btn"><span class="ql-icon">📍</span>지역별 채용</a>
          <a href="#" class="quick-link-btn"><span class="ql-icon">💬</span>수다방</a>
        </div>
      </div>
    </div>
    <div class="sidebar-widget">
      <div class="widget-title">📍 지역별 검색</div>
      <div class="widget-body">
        <div class="region-grid">
          <a href="#" class="region-btn">서울</a>
          <a href="#" class="region-btn">경기</a>
          <a href="#" class="region-btn">인천</a>
          <a href="#" class="region-btn">부산</a>
          <a href="#" class="region-btn">대구</a>
          <a href="#" class="region-btn">광주</a>
          <a href="#" class="region-btn">대전</a>
          <a href="#" class="region-btn">울산</a>
          <a href="#" class="region-btn">강원</a>
          <a href="#" class="region-btn">충청</a>
          <a href="#" class="region-btn">전라</a>
          <a href="#" class="region-btn">경상</a>
        </div>
      </div>
    </div>
  </aside>

  <!-- 메인 영역 (index.php에서 채움) -->
  <div class="main-area">
