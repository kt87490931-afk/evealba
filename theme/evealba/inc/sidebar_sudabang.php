<?php
/**
 * 이브수다방 페이지 전용 좌측 사이드바 (eve_alba_sudabang_1.html 100% 동일)
 */
if (!defined('_GNUBOARD_')) exit;
?>
<?php include G5_THEME_PATH.'/inc/sidebar_login_widget.php'; ?>
<?php include G5_THEME_PATH.'/inc/sidebar_quick_menu.php'; ?>

<?php
$_side_bbs = G5_BBS_URL;
$_side_wp  = $g5['write_prefix'];
$_side_cnt = array('night'=>0, 'couple'=>0, 'law'=>0);
foreach (array_keys($_side_cnt) as $_sb) {
    $_r = @sql_fetch("SELECT COUNT(*) as cnt FROM {$_side_wp}{$_sb} WHERE wr_is_comment=0");
    if ($_r) $_side_cnt[$_sb] = (int)$_r['cnt'];
}
$_side_best_cnt = 0;
$_rb = @sql_fetch("
    SELECT COUNT(*) as cnt FROM (
        (SELECT wr_id FROM {$_side_wp}night WHERE wr_is_comment=0 AND wr_good>=10)
        UNION ALL
        (SELECT wr_id FROM {$_side_wp}couple WHERE wr_is_comment=0 AND wr_good>=10)
    ) AS t
");
if ($_rb) $_side_best_cnt = (int)$_rb['cnt'];

$_cur_table = isset($_GET['bo_table']) ? $_GET['bo_table'] : '';
?>
<!-- 커뮤니티 메뉴 -->
<div class="sidebar-widget">
  <div class="widget-title">💬 커뮤니티</div>
  <div class="widget-body">
    <div class="side-comm-list">
      <a href="<?php echo $_side_bbs; ?>/board.php?bo_table=night" class="side-comm-item<?php echo ($_cur_table==='night') ? ' active' : ''; ?>">🌙 밤문화이야기<span class="side-comm-count"><?php echo number_format($_side_cnt['night']); ?></span></a>
      <a href="<?php echo $_side_bbs; ?>/board.php?bo_table=couple" class="side-comm-item<?php echo ($_cur_table==='couple') ? ' active' : ''; ?>">💑 같이일할단짝찾기<span class="side-comm-count"><?php echo number_format($_side_cnt['couple']); ?></span></a>
      <a href="<?php echo $_side_bbs; ?>/board.php?bo_table=law" class="side-comm-item<?php echo ($_cur_table==='law') ? ' active' : ''; ?>">⚖️ 무료법률자문<span class="side-comm-count"><?php echo number_format($_side_cnt['law']); ?></span></a>
    </div>
  </div>
</div>

<!-- 지역별 채용정보 -->
<div class="sidebar-widget">
  <div class="widget-title">📍 지역별 채용정보</div>
  <div class="widget-body">
    <div class="region-grid">
      <a href="<?php echo (defined('G5_URL') && G5_URL) ? rtrim(G5_URL,'/').'/jobs.php' : '/jobs.php'; ?>" class="region-btn">서울</a>
      <a href="<?php echo (defined('G5_URL') && G5_URL) ? rtrim(G5_URL,'/').'/jobs.php' : '/jobs.php'; ?>" class="region-btn">경기</a>
      <a href="<?php echo (defined('G5_URL') && G5_URL) ? rtrim(G5_URL,'/').'/jobs.php' : '/jobs.php'; ?>" class="region-btn">인천</a>
      <a href="<?php echo (defined('G5_URL') && G5_URL) ? rtrim(G5_URL,'/').'/jobs.php' : '/jobs.php'; ?>" class="region-btn">부산</a>
      <a href="<?php echo (defined('G5_URL') && G5_URL) ? rtrim(G5_URL,'/').'/jobs.php' : '/jobs.php'; ?>" class="region-btn">대구</a>
      <a href="<?php echo (defined('G5_URL') && G5_URL) ? rtrim(G5_URL,'/').'/jobs.php' : '/jobs.php'; ?>" class="region-btn">광주</a>
      <a href="<?php echo (defined('G5_URL') && G5_URL) ? rtrim(G5_URL,'/').'/jobs.php' : '/jobs.php'; ?>" class="region-btn">대전</a>
      <a href="<?php echo (defined('G5_URL') && G5_URL) ? rtrim(G5_URL,'/').'/jobs.php' : '/jobs.php'; ?>" class="region-btn">울산</a>
      <a href="<?php echo (defined('G5_URL') && G5_URL) ? rtrim(G5_URL,'/').'/jobs.php' : '/jobs.php'; ?>" class="region-btn">강원</a>
      <a href="<?php echo (defined('G5_URL') && G5_URL) ? rtrim(G5_URL,'/').'/jobs.php' : '/jobs.php'; ?>" class="region-btn">충청</a>
      <a href="<?php echo (defined('G5_URL') && G5_URL) ? rtrim(G5_URL,'/').'/jobs.php' : '/jobs.php'; ?>" class="region-btn">전라</a>
      <a href="<?php echo (defined('G5_URL') && G5_URL) ? rtrim(G5_URL,'/').'/jobs.php' : '/jobs.php'; ?>" class="region-btn">경상</a>
    </div>
  </div>
</div>

<!-- 광고 섹션 -->
<div class="sidebar-widget">
  <div class="widget-title">📢 광고 섹션</div>
  <div class="widget-body">
    <div class="side-section-links">
      <a href="#" class="side-section-link">▶ 우대등록채용정보<span class="badge-ad">광고신청</span></a>
      <a href="#" class="side-section-link">▶ 프리미엄채용정보<span class="badge-ad">광고신청</span></a>
      <a href="#" class="side-section-link">▶ 스페셜채용정보<span class="badge-ad">광고신청</span></a>
      <a href="#" class="side-section-link">▶ 급구채용정보<span class="badge-ad">광고신청</span></a>
      <a href="#" class="side-section-link">▶ 추천채용정보<span class="badge-ad">광고신청</span></a>
    </div>
  </div>
</div>

<!-- 추천업소 배너 -->
<div class="sidebar-widget">
  <div class="widget-title">💎 추천업소</div>
  <div class="widget-body">
    <div class="side-ad-card">
      <div class="side-ad-banner g12">동탄스카이 아이퍼블릭<br><b style="font-size:14px">60분 TC12만원</b></div>
      <div class="side-ad-info">
        <div class="side-ad-name">동탄스카이 아이퍼블릭</div>
        <div class="side-ad-wage">자유복장 · TC12만원</div>
      </div>
    </div>
    <div class="side-ad-card">
      <div class="side-ad-banner g1">일프로 &amp; 텐카페<br><b>300만 보상</b></div>
      <div class="side-ad-info">
        <div class="side-ad-name">일프로 · 텐카페</div>
        <div class="side-ad-wage">300만원 보장</div>
      </div>
    </div>
    <div class="side-ad-card">
      <div class="side-ad-banner" style="background:linear-gradient(135deg,#1A0010,#FF1B6B);font-size:18px;font-weight:900">당일<br>백만<br>UP</div>
      <div class="side-ad-info">
        <div class="side-ad-name">당일 백만원 UP 이벤트</div>
        <div class="side-ad-wage">기간 한정 특별 혜택</div>
      </div>
    </div>
  </div>
</div>

<?php include G5_THEME_PATH.'/inc/sidebar_cs_widget.php'; ?>
