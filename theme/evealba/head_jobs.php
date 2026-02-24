<?php
/**
 * 채용정보 페이지 전용 head
 * - head_top (nav_active=jobs), breadcrumb, page-layout, sidebar_jobs, main-area
 */
if (!defined('_GNUBOARD_')) exit;

/* evealba 반응형 테마: 모바일에서도 동일 레이아웃 사용 (evealba.css 반응형) */
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
    // DB 비어있으면 정적 폴백
    if (empty($ev_regions)) {
        $ev_regions = array(
            array('er_id'=>1,'er_name'=>'서울'), array('er_id'=>2,'er_name'=>'경기'), array('er_id'=>3,'er_name'=>'인천'),
            array('er_id'=>4,'er_name'=>'부산'), array('er_id'=>5,'er_name'=>'대구'), array('er_id'=>6,'er_name'=>'광주'),
            array('er_id'=>7,'er_name'=>'대전'), array('er_id'=>8,'er_name'=>'울산'), array('er_id'=>9,'er_name'=>'강원'),
            array('er_id'=>10,'er_name'=>'경남'), array('er_id'=>11,'er_name'=>'경북'), array('er_id'=>12,'er_name'=>'전남'),
            array('er_id'=>13,'er_name'=>'전북'), array('er_id'=>14,'er_name'=>'충남'), array('er_id'=>15,'er_name'=>'충북'),
            array('er_id'=>16,'er_name'=>'세종'), array('er_id'=>17,'er_name'=>'제주'));
    }
    if (empty($ev_region_details)) {
        $ev_region_details = array(
            array('erd_id'=>1,'er_id'=>1,'erd_name'=>'강남구'), array('erd_id'=>2,'er_id'=>1,'erd_name'=>'강동구'),
            array('erd_id'=>3,'er_id'=>1,'erd_name'=>'강북구'), array('erd_id'=>4,'er_id'=>1,'erd_name'=>'강서구'),
            array('erd_id'=>5,'er_id'=>1,'erd_name'=>'관악구'), array('erd_id'=>6,'er_id'=>1,'erd_name'=>'광진구'),
            array('erd_id'=>7,'er_id'=>1,'erd_name'=>'구로구'), array('erd_id'=>8,'er_id'=>1,'erd_name'=>'금천구'),
            array('erd_id'=>9,'er_id'=>1,'erd_name'=>'노원구'), array('erd_id'=>10,'er_id'=>1,'erd_name'=>'도봉구'),
            array('erd_id'=>11,'er_id'=>1,'erd_name'=>'동대문구'), array('erd_id'=>12,'er_id'=>1,'erd_name'=>'동작구'),
            array('erd_id'=>13,'er_id'=>1,'erd_name'=>'마포구'), array('erd_id'=>14,'er_id'=>1,'erd_name'=>'서대문구'),
            array('erd_id'=>15,'er_id'=>1,'erd_name'=>'서초구'), array('erd_id'=>16,'er_id'=>1,'erd_name'=>'성동구'),
            array('erd_id'=>17,'er_id'=>1,'erd_name'=>'성북구'), array('erd_id'=>18,'er_id'=>1,'erd_name'=>'송파구'),
            array('erd_id'=>19,'er_id'=>1,'erd_name'=>'양천구'), array('erd_id'=>20,'er_id'=>1,'erd_name'=>'영등포구'),
            array('erd_id'=>21,'er_id'=>1,'erd_name'=>'용산구'), array('erd_id'=>22,'er_id'=>1,'erd_name'=>'은평구'),
            array('erd_id'=>23,'er_id'=>1,'erd_name'=>'종로구'), array('erd_id'=>24,'er_id'=>1,'erd_name'=>'중구'),
            array('erd_id'=>25,'er_id'=>1,'erd_name'=>'중랑구'), array('erd_id'=>26,'er_id'=>1,'erd_name'=>'기타'));
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
    if (empty($ev_conveniences)) {
        $ev_conveniences = array(
            array('ec_id'=>1,'ec_name'=>'선불가능'), array('ec_id'=>2,'ec_name'=>'순번확실'),
            array('ec_id'=>3,'ec_name'=>'원룸제공'), array('ec_id'=>4,'ec_name'=>'만근비지원'),
            array('ec_id'=>5,'ec_name'=>'성형지원'), array('ec_id'=>6,'ec_name'=>'출퇴근지원'),
            array('ec_id'=>7,'ec_name'=>'식사제공'), array('ec_id'=>8,'ec_name'=>'팁별도'),
            array('ec_id'=>9,'ec_name'=>'인센티브'), array('ec_id'=>10,'ec_name'=>'갯수보장'),
            array('ec_id'=>11,'ec_name'=>'초이스없음'), array('ec_id'=>12,'ec_name'=>'당일지급'));
    }
} else {
    $ev_regions = $ev_region_details = $ev_industries = $ev_jobs = $ev_conveniences = [];
}
include_once(G5_LIB_PATH.'/latest.lib.php');
include_once(G5_LIB_PATH.'/outlogin.lib.php');
include_once(G5_LIB_PATH.'/poll.lib.php');
include_once(G5_LIB_PATH.'/visit.lib.php');
include_once(G5_LIB_PATH.'/connect.lib.php');
include_once(G5_LIB_PATH.'/popular.lib.php');

$nav_active = 'jobs';
include G5_THEME_PATH.'/inc/head_top.php';
?>

<!-- BREADCRUMB -->
<div class="breadcrumb-bar">
  <div class="breadcrumb-inner">
    <a href="<?php echo G5_URL ?>">🏠 메인</a>
    <span>›</span>
    <span class="current">📋 채용정보 리스트 (전체)</span>
  </div>
</div>

<!-- PAGE LAYOUT -->
<div class="page-layout">

  <!-- 좌측 사이드바 (채용정보용) -->
  <aside class="left-sidebar">
    <?php include G5_THEME_PATH.'/inc/sidebar_jobs.php'; ?>
  </aside>

  <!-- 메인 영역 -->
  <div class="main-area">
