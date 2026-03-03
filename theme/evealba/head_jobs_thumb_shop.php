<?php
/**
 * 썸네일상점 페이지 전용 head
 */
if (!defined('_GNUBOARD_')) exit;

if(G5_COMMUNITY_USE === false) {
    define('G5_IS_COMMUNITY_PAGE', true);
    include_once(G5_THEME_SHOP_PATH.'/shop.head.php');
    return;
}
include_once(G5_THEME_PATH.'/head.sub.php');
if (file_exists(G5_LIB_PATH.'/ev_master.lib.php')) {
    include_once(G5_LIB_PATH.'/ev_master.lib.php');
    $ev_regions = ev_get_regions();
    $ev_region_details = ev_get_region_details();
    $ev_industries = ev_get_industries();
    $ev_jobs = ev_get_jobs();
    $ev_conveniences = ev_get_conveniences();
    if (empty($ev_region_details) || count($ev_region_details) < 100) {
        if (file_exists(G5_LIB_PATH.'/ev_region_fallback.inc.php')) {
            include_once G5_LIB_PATH.'/ev_region_fallback.inc.php';
            $ev_region_details = $ev_region_details_fallback ?? $ev_region_details;
            if (empty($ev_regions)) $ev_regions = $ev_regions_fallback ?? array();
        }
    }
    if (empty($ev_regions)) {
        $ev_regions = array(
            array('er_id'=>1,'er_name'=>'서울'), array('er_id'=>2,'er_name'=>'경기'), array('er_id'=>3,'er_name'=>'인천'),
            array('er_id'=>4,'er_name'=>'부산'), array('er_id'=>5,'er_name'=>'대구'), array('er_id'=>6,'er_name'=>'광주'),
            array('er_id'=>7,'er_name'=>'대전'), array('er_id'=>8,'er_name'=>'울산'), array('er_id'=>9,'er_name'=>'강원'),
            array('er_id'=>10,'er_name'=>'경남'), array('er_id'=>11,'er_name'=>'경북'), array('er_id'=>12,'er_name'=>'전남'),
            array('er_id'=>13,'er_name'=>'전북'), array('er_id'=>14,'er_name'=>'충남'), array('er_id'=>15,'er_name'=>'충북'),
            array('er_id'=>16,'er_name'=>'세종'), array('er_id'=>17,'er_name'=>'제주'));
    }
}
include_once(G5_LIB_PATH.'/latest.lib.php');
include_once(G5_LIB_PATH.'/outlogin.lib.php');
include_once(G5_LIB_PATH.'/visit.lib.php');
include_once(G5_LIB_PATH.'/connect.lib.php');
include_once(G5_LIB_PATH.'/popular.lib.php');
@include_once(G5_LIB_PATH.'/ev_coupon.lib.php');
@include_once(G5_LIB_PATH.'/ev_thumb_option.lib.php');

$nav_active = 'thumb_shop';
if (!isset($jobs_mypage_active)) $jobs_mypage_active = 'thumb_shop';
$g5['title'] = '썸네일상점 - '.$config['cf_title'];
include G5_THEME_PATH.'/inc/head_top.php';
?>

<!-- BREADCRUMB -->
<div class="breadcrumb-bar">
  <div class="breadcrumb-inner">
    <a href="<?php echo G5_URL ?>">🏠 메인</a>
    <span class="sep">›</span>
    <a href="<?php echo G5_BBS_URL; ?>/member_confirm.php?url=<?php echo urlencode(G5_BBS_URL.'/register_form.php'); ?>">회원정보</a>
    <span class="sep">›</span>
    <span class="current">🛒 썸네일상점</span>
  </div>
</div>

<link rel="stylesheet" href="<?php echo G5_THEME_URL; ?>/css/resume_register.css?v=<?php echo @filemtime(G5_THEME_PATH.'/css/resume_register.css'); ?>">
<!-- PAGE LAYOUT (jobs_view 편집과 동일) -->
<div class="page-layout jobs-register-page">

  <!-- 좌측 사이드바 (MY PAGE) -->
  <aside class="left-sidebar">
    <?php include G5_THEME_PATH.'/inc/sidebar_jobs_register.php'; ?>
  </aside>

  <!-- 메인 영역 -->
  <div class="main-area">
    <?php include G5_THEME_PATH.'/inc/ads_main_banner.php'; ?>
