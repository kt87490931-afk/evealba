<?php
/**
 * 중고거래 메인 영역 - 게시판 목록/보기
 * board_head, list 또는 view, board_tail 포함
 */
if (!defined('_GNUBOARD_') || !defined('_USED_')) exit;

include_once(G5_BBS_PATH.'/board_head.php');

// 게시물 아이디가 있으면 글보기
if (isset($wr_id) && $wr_id) {
    include_once(G5_BBS_PATH.'/view.php');
}

// wr_id 없으면 목록
if ($member['mb_level'] >= $board['bo_list_level'] && $board['bo_use_list_view'] || empty($wr_id)) {
    include_once(G5_BBS_PATH.'/list.php');
}

include_once(G5_BBS_PATH.'/board_tail.php');

echo "\n<!-- 사용스킨 : ".(G5_IS_MOBILE ? $board['bo_mobile_skin'] : $board['bo_skin'])." -->\n";
