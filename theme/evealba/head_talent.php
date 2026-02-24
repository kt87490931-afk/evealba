<?php
/**
 * 인재정보 페이지 전용 head (eve_alba_talent.html 100% 동일)
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
    $ev_industries = ev_get_industries();
    $ev_jobs = ev_get_jobs();
    if (empty($ev_regions)) {
        $ev_regions = array(
            array('er_id'=>1,'er_name'=>'서울'), array('er_id'=>2,'er_name'=>'경기'), array('er_id'=>3,'er_name'=>'인천'),
            array('er_id'=>4,'er_name'=>'부산'), array('er_id'=>5,'er_name'=>'대구'), array('er_id'=>6,'er_name'=>'광주'),
            array('er_id'=>7,'er_name'=>'대전'), array('er_id'=>8,'er_name'=>'울산'), array('er_id'=>9,'er_name'=>'강원'),
            array('er_id'=>10,'er_name'=>'충청'), array('er_id'=>11,'er_name'=>'전라'), array('er_id'=>12,'er_name'=>'경상'),
            array('er_id'=>13,'er_name'=>'제주'));
    }
    if (empty($ev_industries)) {
        $ev_industries = array(
            array('ei_id'=>1,'ei_name'=>'룸싸롱'), array('ei_id'=>2,'ei_name'=>'노래주점'),
            array('ei_id'=>3,'ei_name'=>'마사지'), array('ei_id'=>4,'ei_name'=>'기타'));
    }
    if (empty($ev_jobs)) {
        $ev_jobs = array(
            array('ej_id'=>1,'ei_id'=>1,'ej_name'=>'아가씨'), array('ej_id'=>2,'ei_id'=>1,'ej_name'=>'초미씨'),
            array('ej_id'=>3,'ei_id'=>1,'ej_name'=>'미씨'), array('ej_id'=>4,'ei_id'=>1,'ej_name'=>'TC'));
    }
} else {
    $ev_regions = $ev_industries = $ev_jobs = [];
}
include_once(G5_LIB_PATH.'/latest.lib.php');
include_once(G5_LIB_PATH.'/outlogin.lib.php');
include_once(G5_LIB_PATH.'/poll.lib.php');
include_once(G5_LIB_PATH.'/visit.lib.php');
include_once(G5_LIB_PATH.'/connect.lib.php');
include_once(G5_LIB_PATH.'/popular.lib.php');

$nav_active = 'talent';
include G5_THEME_PATH.'/inc/head_top.php';
?>

<!-- BREADCRUMB -->
<div class="breadcrumb-bar">
  <div class="breadcrumb-inner">
    <a href="<?php echo G5_URL ?>">🏠 메인</a>
    <span>›</span>
    <span class="current">👑 인재정보 리스트</span>
  </div>
</div>

<!-- PAGE LAYOUT -->
<div class="page-layout">

  <!-- 좌측 사이드바 (인재정보용) -->
  <aside class="left-sidebar">
    <?php include G5_THEME_PATH.'/inc/sidebar_talent.php'; ?>
  </aside>

  <!-- 메인 영역 -->
  <div class="main-area">
