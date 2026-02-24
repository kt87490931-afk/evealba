<?php
/**
 * 중고거래 페이지 전용 head (eve_alba_used.html 기준)
 */
if (!defined('_GNUBOARD_')) exit;

// used 게시판 URL을 used.php로 연결
add_replace('get_pretty_url', function($url, $folder, $no, $query_string, $action) {
    if ($folder === 'used') {
        $base = (defined('G5_URL') && G5_URL) ? rtrim(G5_URL,'/').'/used.php' : '/used.php';
        $sep = '?';
        if ($no) { $base .= $sep.'wr_id='.$no; $sep = '&amp;'; }
        if ($query_string) {
            $qs = preg_replace('/^&amp;|^&/', '', $query_string);
            $base .= $sep . str_replace('&', '&amp;', $qs);
        }
        return $base;
    }
    return $url;
}, 10, 5);

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

$nav_active = 'used';
include G5_THEME_PATH.'/inc/head_top.php';
?>

<!-- BREADCRUMB -->
<div class="breadcrumb-bar">
  <div class="breadcrumb-inner">
    <a href="<?php echo G5_URL ?>">🏠 메인</a>
    <span>›</span>
    <a href="<?php echo (defined('G5_URL') && G5_URL) ? rtrim(G5_URL,'/').'/sudabang.php' : '/sudabang.php'; ?>">커뮤니티</a>
    <span>›</span>
    <span class="current">🛍️ 중고거래게시판</span>
  </div>
</div>

<!-- PAGE LAYOUT -->
<div class="page-layout">

  <!-- 좌측 사이드바 (중고거래용) -->
  <aside class="left-sidebar">
    <?php include G5_THEME_PATH.'/inc/sidebar_used.php'; ?>
  </aside>

  <!-- 메인 영역 -->
  <div class="main-area">
