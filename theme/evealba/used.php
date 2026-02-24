<?php
/**
 * 중고거래 페이지 (eve_alba_used.html 100% 동일 구조)
 * bo_table=used 게시판을 사이드바 레이아웃으로 표시
 */
if (!defined('_USED_')) define('_USED_', true);
if (!defined('_GNUBOARD_')) exit;

// 게시판이 없으면 used 생성 권장 메시지
if (!isset($board['bo_table']) || !$board['bo_table']) {
    alert('중고거래 게시판(bo_table=used)이 존재하지 않습니다.\\n\\n관리자 > 게시판관리에서 "used" 테이블의 중고거래 게시판을 생성해 주세요.', G5_URL);
}

include_once(G5_THEME_PATH.'/inc/board_used_check.php');

$g5['title'] = (isset($g5['title']) ? $g5['title'] : $g5['board_title']).' - '.$config['cf_title'];
include_once(G5_THEME_PATH.'/head_used.php');
?>

<?php include(G5_THEME_PATH.'/used_main.php'); ?>

<?php
include_once(G5_THEME_PATH.'/tail.php');
?>
