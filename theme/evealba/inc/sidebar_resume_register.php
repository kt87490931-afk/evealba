<?php
/**
 * 이력서 등록 페이지 전용 좌측 사이드바 (eve_alba_resume.html 100% 동일)
 */
if (!defined('_GNUBOARD_')) exit;

$resume_register_url = (defined('G5_URL') && G5_URL) ? rtrim(G5_URL,'/').'/resume_register.php' : '/resume_register.php';
$jobs_register_url = (defined('G5_URL') && G5_URL) ? rtrim(G5_URL,'/').'/jobs_register.php' : '/jobs_register.php';
$mypage_url = G5_BBS_URL.'/member_confirm.php?url='.urlencode(G5_BBS_URL.'/register_form.php');
?>
<div class="sidebar-widget">
  <div class="mypage-header">
    <span class="mypage-icon">👩</span>
    <div>
      <div class="mypage-title">MY PAGE</div>
      <div class="mypage-sub">마이페이지</div>
    </div>
  </div>
  <div class="side-menu-list">
    <a href="<?php echo $resume_register_url; ?>" class="side-menu-item active">📄 이력서 리스트</a>
    <a href="#" class="side-menu-item">📋 채용정보 스크랩</a>
    <a href="#" class="side-menu-item">👤 맞춤구인정보</a>
    <a href="#" class="side-menu-item">⚙️ 맞춤구인 정보설정</a>
    <a href="#" class="side-menu-item">📝 내가 작성한 게시글</a>
    <a href="#" class="side-menu-item">💬 내가 작성한 댓글</a>
    <a href="#" class="side-menu-item">⭐ 즐겨찾기한 게시글</a>
  </div>
</div>
<div class="sidebar-widget">
  <div class="widget-body" style="padding:10px;">
    <a href="<?php echo $resume_register_url; ?>" class="side-cta-btn btn-resume-reg" style="text-decoration:none;display:flex;align-items:center;justify-content:center;">📄 이력서 등록하기</a>
    <a href="<?php echo $jobs_register_url; ?>" class="side-cta-btn btn-job-scrap" style="text-decoration:none;display:flex;align-items:center;justify-content:center;">⭐ 채용정보 스크랩</a>
  </div>
</div>
<?php include G5_THEME_PATH.'/inc/sidebar_cs_widget.php'; ?>
