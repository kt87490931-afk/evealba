<?php
/**
 * 이력서 등록 페이지 전용 좌측 사이드바 (eve_alba_resume.html 100% 동일)
 */
if (!defined('_GNUBOARD_')) exit;

$resume_register_url = (defined('G5_URL') && G5_URL) ? rtrim(G5_URL,'/').'/resume_register.php' : '/resume_register.php';
$jobs_register_url = (defined('G5_URL') && G5_URL) ? rtrim(G5_URL,'/').'/jobs_register.php' : '/jobs_register.php';
$mypage_url = G5_BBS_URL.'/member_confirm.php?url='.urlencode(G5_BBS_URL.'/register_form.php');
$resume_mypage_active = isset($resume_mypage_active) ? $resume_mypage_active : 'resume_list';
$_rmp_labels = array(
    'resume_list'=>'📄 이력서 리스트','scrap'=>'📋 채용정보 스크랩','matching'=>'👤 맞춤구인정보',
    'resume_edit'=>'⚙️ 이력서 수정','my_posts'=>'📝 내가 작성한 게시글',
    'my_comments'=>'💬 내가 작성한 댓글','bookmarks'=>'⭐ 즐겨찾기한 게시글'
);
$_my_resume_edit_url = '#';
if ($is_member) {
    $_my_rs = @sql_fetch("SELECT rs_id FROM g5_resume WHERE mb_id = '".addslashes($member['mb_id'])."' AND rs_status = 'active' LIMIT 1");
    if ($_my_rs) {
        $_tb = (defined('G5_URL') && G5_URL) ? rtrim(G5_URL,'/') : '';
        $_my_resume_edit_url = $_tb.'/talent_view.php?rs_id='.(int)$_my_rs['rs_id'];
    }
}
$_rmp_active_label = isset($_rmp_labels[$resume_mypage_active]) ? $_rmp_labels[$resume_mypage_active] : '📄 MY PAGE';
?>
<div class="sidebar-widget sidebar-mypage" id="sidebarResume">
  <div class="mypage-header">
    <span class="mypage-icon">👩</span>
    <div>
      <div class="mypage-title">MY PAGE</div>
      <div class="mypage-sub">마이페이지</div>
    </div>
  </div>
  <button type="button" class="sidebar-mobile-toggle" onclick="this.closest('.sidebar-mypage').classList.toggle('mobile-open');">
    <span class="smt-label"><?php echo $_rmp_active_label; ?></span>
    <span class="smt-arrow">▼</span>
  </button>
  <div class="side-menu-list">
    <a href="<?php echo $resume_register_url; ?>" class="side-menu-item<?php echo ($resume_mypage_active === 'resume_list') ? ' active' : ''; ?>">📄 이력서 리스트</a>
    <a href="#" class="side-menu-item<?php echo ($resume_mypage_active === 'scrap') ? ' active' : ''; ?>">📋 채용정보 스크랩</a>
    <a href="#" class="side-menu-item<?php echo ($resume_mypage_active === 'matching') ? ' active' : ''; ?>">👤 맞춤구인정보</a>
    <a href="<?php echo $_my_resume_edit_url; ?>" class="side-menu-item<?php echo ($resume_mypage_active === 'resume_edit') ? ' active' : ''; ?>">⚙️ 이력서 수정</a>
    <a href="#" class="side-menu-item<?php echo ($resume_mypage_active === 'my_posts') ? ' active' : ''; ?>">📝 내가 작성한 게시글</a>
    <a href="#" class="side-menu-item<?php echo ($resume_mypage_active === 'my_comments') ? ' active' : ''; ?>">💬 내가 작성한 댓글</a>
    <a href="#" class="side-menu-item<?php echo ($resume_mypage_active === 'bookmarks') ? ' active' : ''; ?>">⭐ 즐겨찾기한 게시글</a>
  </div>
</div>
<div class="sidebar-widget">
  <div class="widget-body" style="padding:10px;">
    <a href="<?php echo $resume_register_url; ?>" class="side-cta-btn btn-resume-reg" style="text-decoration:none;display:flex;align-items:center;justify-content:center;">📄 이력서 등록하기</a>
    <a href="<?php echo $jobs_register_url; ?>" class="side-cta-btn btn-job-scrap" style="text-decoration:none;display:flex;align-items:center;justify-content:center;">⭐ 채용정보 스크랩</a>
  </div>
</div>
<?php include G5_THEME_PATH.'/inc/sidebar_cs_widget.php'; ?>
