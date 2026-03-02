<?php
/**
 * 이력서 등록 페이지 전용 head (eve_alba_resume.html 100% 동일)
 * - breadcrumb, page-layout, sidebar_resume_register, main-area
 */
if (!defined('_GNUBOARD_')) exit;

$_edit_rs_id = isset($_GET['rs_id']) ? (int)$_GET['rs_id'] : 0;
$_is_edit_mode = false;
$rs_row = null;
$rs_data = array();
if ($_edit_rs_id > 0 && $is_member) {
    $_rs_tb = @sql_query("SHOW TABLES LIKE 'g5_resume'", false);
    if ($_rs_tb && @sql_num_rows($_rs_tb)) {
        $rs_row = sql_fetch("SELECT * FROM g5_resume WHERE rs_id = '{$_edit_rs_id}' AND mb_id = '".addslashes($member['mb_id'])."' AND rs_status = 'active'");
        if ($rs_row) {
            $_is_edit_mode = true;
            $rs_data = @json_decode($rs_row['rs_data'], true);
            if (!is_array($rs_data)) $rs_data = array();
            $g5['title'] = '이력서 수정 - '.$config['cf_title'];
        }
    }
}

if (G5_COMMUNITY_USE === false) {
    define('G5_IS_COMMUNITY_PAGE', true);
    include_once(G5_THEME_SHOP_PATH.'/shop.head.php');
    return;
}
include_once(G5_THEME_PATH.'/head.sub.php');
if (file_exists(G5_LIB_PATH.'/ev_master.lib.php')) {
    include_once(G5_LIB_PATH.'/ev_master.lib.php');
    $ev_regions = ev_get_regions();
    $ev_region_details = ev_get_region_details();
    if (empty($ev_region_details) || count($ev_region_details) < 50) {
        if (file_exists(G5_LIB_PATH.'/ev_region_fallback.inc.php')) {
            include_once G5_LIB_PATH.'/ev_region_fallback.inc.php';
            $ev_region_details = $ev_region_details_fallback;
            if (empty($ev_regions)) $ev_regions = $ev_regions_fallback;
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
} else {
    $ev_regions = array();
    $ev_region_details = array();
}
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
    <span class="current">📄 <?php echo $_is_edit_mode ? '이력서 수정' : '이력서 등록'; ?></span>
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
    <?php include G5_THEME_PATH.'/inc/ads_main_banner.php'; ?>
