<?php
/**
 * ScorePoint 게시물 관리 (Gnuboard Admin)
 * - 어드민-게시판관리와 동일한 게시판 목록 기준 (g5_board, 그룹관리자 권한)
 * - 선택한 게시판의 게시물 목록: wr_id, 제목, 작성자, 작성일, 조회, 댓글, 추천, 비추천, 옵션, 신고건수
 * - 검색·정렬·페이징·원글/댓글 필터
 *
 * 경로: /adm/scorepoint/scorepoint_board_post_manage.php
 */

$sub_menu = isset($_GET['sub_menu']) ? preg_replace('/[^0-9]/', '', $_GET['sub_menu']) : '910800';
if ($sub_menu === '') {
    $sub_menu = '910800';
}

$adm_dir = dirname(__DIR__);
$adm_dir_real = @realpath($adm_dir);
if ($adm_dir_real && is_dir($adm_dir_real)) {
    $adm_dir = $adm_dir_real;
}
$old_cwd = @getcwd();
if ($adm_dir && is_dir($adm_dir)) {
    @chdir($adm_dir);
}
require_once $adm_dir . '/_common.php';
if ($old_cwd) {
    @chdir($old_cwd);
}

if (!isset($is_admin) || !$is_admin) {
    alert('관리자만 접근 가능합니다.');
}
auth_check_menu($auth, $sub_menu, 'r');

// g5_board_report 테이블 없으면 생성 (신고건수 표시용)
$tbl_report = (defined('G5_TABLE_PREFIX') ? G5_TABLE_PREFIX : 'g5_') . 'board_report';
sql_query("
    CREATE TABLE IF NOT EXISTS `{$tbl_report}` (
        `br_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `bo_table` VARCHAR(50) NOT NULL DEFAULT '',
        `wr_id` INT UNSIGNED NOT NULL DEFAULT 0,
        `reporter_mb_id` VARCHAR(20) NOT NULL DEFAULT '',
        `reporter_nick` VARCHAR(255) NOT NULL DEFAULT '',
        `reason` VARCHAR(100) NOT NULL DEFAULT '',
        `detail` VARCHAR(500) NOT NULL DEFAULT '',
        `report_ip` VARCHAR(255) NOT NULL DEFAULT '',
        `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`br_id`),
        KEY `bo_wr` (`bo_table`, `wr_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
", false);

// g5_board_report_config (신고 누적 임계: N회 이상이면 해당 글 비공개)
$tbl_report_cfg = (defined('G5_TABLE_PREFIX') ? G5_TABLE_PREFIX : 'g5_') . 'board_report_config';
sql_query("
    CREATE TABLE IF NOT EXISTS `{$tbl_report_cfg}` (
        `id` TINYINT UNSIGNED NOT NULL DEFAULT 1,
        `report_hide_limit` INT UNSIGNED NOT NULL DEFAULT 5,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
", false);
$report_cfg = sql_fetch(" SELECT report_hide_limit FROM {$tbl_report_cfg} WHERE id = 1 LIMIT 1 ", false);
if (!$report_cfg || !isset($report_cfg['report_hide_limit'])) {
    sql_query(" INSERT INTO {$tbl_report_cfg} (id, report_hide_limit) VALUES (1, 5) ON DUPLICATE KEY UPDATE report_hide_limit = report_hide_limit ", false);
    $report_cfg = array('report_hide_limit' => 5);
}
$report_hide_limit = (int)($report_cfg['report_hide_limit'] ?? 5);
if ($report_hide_limit < 1) {
    $report_hide_limit = 5;
}

// g5_board_report_exempt: 비공개 해제(면제) — 해당 글은 신고 수와 관계없이 비공개 처리하지 않음
$tbl_exempt = (defined('G5_TABLE_PREFIX') ? G5_TABLE_PREFIX : 'g5_') . 'board_report_exempt';
sql_query("
    CREATE TABLE IF NOT EXISTS `{$tbl_exempt}` (
        `bo_table` VARCHAR(50) NOT NULL DEFAULT '',
        `wr_id` INT UNSIGNED NOT NULL DEFAULT 0,
        `exempt_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`bo_table`, `wr_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
", false);

// 비공개 해제(면제) 처리 (GET: report_exempt=1&bo_table=xxx&wr_id=yyy)
if (isset($_GET['report_exempt']) && (int)$_GET['report_exempt'] === 1 && isset($_GET['bo_table']) && isset($_GET['wr_id'])) {
    auth_check_menu($auth, $sub_menu, 'w');
    $exempt_bo = preg_replace('/[^a-zA-Z0-9_]/', '', trim($_GET['bo_table']));
    $exempt_wr = (int)$_GET['wr_id'];
    $chk = sql_fetch(" SELECT 1 FROM {$g5['board_table']} WHERE bo_table = '" . sql_real_escape_string($exempt_bo) . "' LIMIT 1 ", false);
    if ($exempt_bo !== '' && $exempt_wr > 0 && $chk) {
        sql_query(" INSERT INTO {$tbl_exempt} (bo_table, wr_id, exempt_at) VALUES ('" . sql_real_escape_string($exempt_bo) . "', {$exempt_wr}, NOW()) ON DUPLICATE KEY UPDATE exempt_at = NOW() ", false);
    }
    $redir_bo = isset($_GET['return_bo']) ? urlencode(trim($_GET['return_bo'])) : (isset($_GET['bo_table']) ? urlencode(trim($_GET['bo_table'])) : 'all');
    goto_url($_SERVER['SCRIPT_NAME'] . '?sub_menu=' . $sub_menu . '&bo_table=' . $redir_bo . '&page=' . (isset($_GET['page']) ? (int)$_GET['page'] : 1));
}

// 신고 누적 설정 저장 (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['report_config_save'])) {
    auth_check_menu($auth, $sub_menu, 'w');
    $new_limit = isset($_POST['report_hide_limit']) ? (int)$_POST['report_hide_limit'] : 5;
    if ($new_limit < 1) {
        $new_limit = 5;
    }
    sql_query(" INSERT INTO {$tbl_report_cfg} (id, report_hide_limit) VALUES (1, {$new_limit}) ON DUPLICATE KEY UPDATE report_hide_limit = {$new_limit} ", false);
    $redirect_bo = isset($_GET['bo_table']) && $_GET['bo_table'] !== '' ? '&bo_table=' . urlencode($_GET['bo_table']) : '&bo_table=all';
goto_url($_SERVER['SCRIPT_NAME'] . '?sub_menu=' . $sub_menu . $redirect_bo);
}

// 게시판 목록 (board_list와 동일: super는 전체, 아니면 gr_admin인 그룹만)
$sql_common_boards = " FROM {$g5['board_table']} a ";
$sql_search_boards = " WHERE 1=1 ";
if ($is_admin != 'super') {
    $sql_common_boards .= " , {$g5['group_table']} b ";
    $sql_search_boards .= " AND (a.gr_id = b.gr_id AND b.gr_admin = '" . sql_real_escape_string($member['mb_id']) . "') ";
}
$sql_boards = " SELECT a.bo_table, a.bo_subject " . $sql_common_boards . $sql_search_boards . " ORDER BY a.gr_id, a.bo_table ";
$res_boards = sql_query($sql_boards);
$board_list = array();
while ($row = sql_fetch_array($res_boards)) {
    $board_list[$row['bo_table']] = $row['bo_subject'];
}

$bo_table = isset($_GET['bo_table']) ? trim($_GET['bo_table']) : '';
$view_reported = false;
if ($bo_table === 'all' || $bo_table === '') {
    $bo_table = 'all';
    $view_all = true;
} elseif ($bo_table === 'reported') {
    $bo_table = 'reported';
    $view_all = true;
    $view_reported = true;
} else {
    $bo_table = preg_replace('/[^a-zA-Z0-9_]/', '', $bo_table);
    if (!isset($board_list[$bo_table])) {
        $bo_table = 'all';
        $view_all = true;
    } else {
        $view_all = false;
    }
}

$sfl = isset($_REQUEST['sfl']) ? preg_replace('/[^a-zA-Z0-9_]/', '', $_REQUEST['sfl']) : 'wr_datetime';
$sst_allow = array('wr_id', 'wr_datetime', 'wr_subject', 'wr_name', 'wr_hit', 'wr_comment', 'wr_good', 'wr_nogood', 'report_cnt');
if (!in_array($sfl, $sst_allow, true)) {
    $sfl = 'wr_datetime';
}
$sod = isset($_REQUEST['sod']) ? (strtolower($_REQUEST['sod']) === 'asc' ? 'asc' : 'desc') : 'desc';
$stx = isset($_REQUEST['stx']) ? trim($_REQUEST['stx']) : '';
$filter_comment = isset($_REQUEST['filter_comment']) ? $_REQUEST['filter_comment'] : '';
if (!in_array($filter_comment, array('', '0', '1'), true)) {
    $filter_comment = '';
}
$fr_date = isset($_REQUEST['fr_date']) ? trim($_REQUEST['fr_date']) : '';
$to_date = isset($_REQUEST['to_date']) ? trim($_REQUEST['to_date']) : '';
$page = isset($_REQUEST['page']) ? (int)$_REQUEST['page'] : 1;
if ($page < 1) {
    $page = 1;
}
$rows_allow = array(15, 20, 50);
$rows_request = isset($_REQUEST['rows']) ? (int)$_REQUEST['rows'] : 0;
if (!in_array($rows_request, $rows_allow, true)) {
    $rows_request = 0;
}
if ($rows_request > 0) {
    $rows = $rows_request;
} else {
    $rows = isset($config['cf_page_rows']) ? (int)$config['cf_page_rows'] : 20;
    if (!in_array($rows, $rows_allow, true)) {
        $rows = 20;
    }
}

$total_count = 0;
$today_count = 0;
$result = false;
$board_subject = '';
$write_table = '';
$today_start = date('Y-m-d') . ' 00:00:00';
$today_end = date('Y-m-d') . ' 23:59:59';

// 분류 버튼(전체/신고)에 표시할 건수 (항상 계산, 실패 시 보드별 합산 폴백)
$count_all = 0;
$count_reported = 0;
if (count($board_list) > 0) {
    $union_parts_nav = array();
    foreach ($board_list as $bt => $subject) {
        $write_tbl = $g5['write_prefix'] . $bt;
        $bt_esc = sql_real_escape_string($bt);
        $subject_esc = sql_real_escape_string($subject);
        $union_parts_nav[] = "(SELECT '{$bt_esc}' AS bo_table, '{$subject_esc}' AS bo_subject, w.wr_id FROM {$write_tbl} w)";
    }
    $union_sql_nav = implode(' UNION ALL ', $union_parts_nav);
    $row_all = @sql_fetch(" SELECT COUNT(*) AS cnt FROM ({$union_sql_nav}) u ", false);
    $count_all = ($row_all && isset($row_all['cnt'])) ? (int)$row_all['cnt'] : 0;
    $row_rep = @sql_fetch(" SELECT COUNT(*) AS cnt FROM ({$union_sql_nav}) u LEFT JOIN (SELECT bo_table, wr_id, COUNT(*) AS report_cnt FROM {$tbl_report} GROUP BY bo_table, wr_id) r ON r.bo_table = u.bo_table AND r.wr_id = u.wr_id WHERE IFNULL(r.report_cnt, 0) > 0 ", false);
    $count_reported = ($row_rep && isset($row_rep['cnt'])) ? (int)$row_rep['cnt'] : 0;
    // UNION 실패 시 보드별 합산으로 건수 표시 (제로 회귀: 항상 건수 노출)
    if ($count_all === 0 && count($union_parts_nav) > 0) {
        foreach ($board_list as $bt => $subject) {
            $write_tbl = $g5['write_prefix'] . $bt;
            $r = @sql_fetch(" SELECT COUNT(*) AS cnt FROM {$write_tbl} ", false);
            if ($r && isset($r['cnt'])) {
                $count_all += (int)$r['cnt'];
            }
        }
    }
    if ($count_reported === 0 && count($union_parts_nav) > 0) {
        $count_reported = 0;
        foreach ($board_list as $bt => $subject) {
            $write_tbl = $g5['write_prefix'] . $bt;
            $bt_esc = sql_real_escape_string($bt);
            $r = @sql_fetch(" SELECT COUNT(*) AS cnt FROM {$write_tbl} w INNER JOIN (SELECT wr_id FROM {$tbl_report} WHERE bo_table = '{$bt_esc}' GROUP BY wr_id) r ON r.wr_id = w.wr_id ", false);
            if ($r && isset($r['cnt'])) {
                $count_reported += (int)$r['cnt'];
            }
        }
    }
}

// [신고] 탭일 때 검색폼-테이블 사이 통계: 신고게시글수, 비공개게시글수, 해제된게시글수
$count_hidden_posts = 0;
$count_exempt_posts = 0;
if ($view_reported && count($board_list) > 0) {
    $bo_in_list = array_map(function ($bt) { return "'" . sql_real_escape_string($bt) . "'"; }, array_keys($board_list));
    $bo_in_sql = implode(',', $bo_in_list);
    $limit_val = (int)$report_hide_limit;
    if ($limit_val < 1) {
        $limit_val = 5;
    }
    // 비공개게시글수: 동일 신고사유가 임계 이상이고, 면제(해제) 테이블에 없는 (bo_table, wr_id)
    $row_h = @sql_fetch("
        SELECT COUNT(*) AS cnt FROM (
            SELECT br.bo_table, br.wr_id
            FROM (
                SELECT bo_table, wr_id FROM {$tbl_report}
                WHERE bo_table IN ({$bo_in_sql})
                GROUP BY bo_table, wr_id, reason
                HAVING COUNT(*) >= {$limit_val}
            ) br
            LEFT JOIN {$tbl_exempt} ex ON ex.bo_table = br.bo_table AND ex.wr_id = br.wr_id
            WHERE ex.wr_id IS NULL
            GROUP BY br.bo_table, br.wr_id
        ) u
    ", false);
    $count_hidden_posts = ($row_h && isset($row_h['cnt'])) ? (int)$row_h['cnt'] : 0;
    // 해제된게시글수: 면제 테이블에 있으면서 신고가 1건 이상인 (bo_table, wr_id)
    $row_e = @sql_fetch("
        SELECT COUNT(DISTINCT ex.bo_table, ex.wr_id) AS cnt
        FROM {$tbl_exempt} ex
        INNER JOIN {$tbl_report} r ON r.bo_table = ex.bo_table AND r.wr_id = ex.wr_id
        WHERE ex.bo_table IN ({$bo_in_sql})
    ", false);
    $count_exempt_posts = ($row_e && isset($row_e['cnt'])) ? (int)$row_e['cnt'] : 0;
}

// 전체 보기: 모든 게시판 게시물 UNION + 신고건수
if ($view_all && count($board_list) > 0) {
    $union_parts = array();
    foreach ($board_list as $bt => $subject) {
        $write_tbl = $g5['write_prefix'] . $bt;
        $bt_esc = sql_real_escape_string($bt);
        $subject_esc = sql_real_escape_string($subject);
        $union_parts[] = "(SELECT '{$bt_esc}' AS bo_table, '{$subject_esc}' AS bo_subject, w.wr_id, w.wr_num, w.wr_reply, w.wr_parent, w.wr_is_comment, w.wr_comment, w.wr_option, w.wr_subject, w.wr_content, w.wr_hit, w.wr_good, w.wr_nogood, w.mb_id, w.wr_name, w.wr_datetime, w.wr_last, w.wr_ip, w.ca_name FROM {$write_tbl} w)";
    }
    $union_sql = implode(' UNION ALL ', $union_parts);

    $sql_search = " 1=1 ";
    if ($stx !== '') {
        $stx_esc = sql_real_escape_string($stx);
        if ($sfl === 'wr_subject') {
            $sql_search .= " AND u.wr_subject LIKE '%{$stx_esc}%' ";
        } elseif ($sfl === 'wr_name') {
            $sql_search .= " AND u.wr_name LIKE '%{$stx_esc}%' ";
        } elseif ($sfl === 'wr_id') {
            $sql_search .= " AND u.wr_id = '" . (int)$stx . "' ";
        } else {
            $sql_search .= " AND (u.wr_subject LIKE '%{$stx_esc}%' OR u.wr_name LIKE '%{$stx_esc}%' OR u.wr_content LIKE '%{$stx_esc}%' OR u.mb_id LIKE '%{$stx_esc}%') ";
        }
    }
    if ($filter_comment === '0') {
        $sql_search .= " AND u.wr_is_comment = 0 ";
    } elseif ($filter_comment === '1') {
        $sql_search .= " AND u.wr_is_comment = 1 ";
    }
    if ($fr_date !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $fr_date)) {
        $sql_search .= " AND u.wr_datetime >= '" . sql_real_escape_string($fr_date) . " 00:00:00' ";
    }
    if ($to_date !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $to_date)) {
        $sql_search .= " AND u.wr_datetime <= '" . sql_real_escape_string($to_date) . " 23:59:59' ";
    }
    if ($view_reported) {
        $sql_search .= " AND IFNULL(r.report_cnt, 0) > 0 ";
    }

    if ($view_reported) {
        $sql_count = " SELECT COUNT(*) AS cnt FROM ({$union_sql}) u LEFT JOIN (SELECT bo_table, wr_id, COUNT(*) AS report_cnt FROM {$tbl_report} GROUP BY bo_table, wr_id) r ON r.bo_table = u.bo_table AND r.wr_id = u.wr_id WHERE {$sql_search} ";
    } else {
        $sql_count = " SELECT COUNT(*) AS cnt FROM ({$union_sql}) u WHERE {$sql_search} ";
    }
    $row_cnt = @sql_fetch($sql_count, false);
    $total_count = ($row_cnt && isset($row_cnt['cnt'])) ? (int)$row_cnt['cnt'] : 0;

    // 오늘 작성된 게시글수 (현재 분류 기준)
    $sql_today_where = $sql_search . " AND u.wr_datetime >= '" . sql_real_escape_string($today_start) . "' AND u.wr_datetime <= '" . sql_real_escape_string($today_end) . "' ";
    if ($view_reported) {
        $sql_today = " SELECT COUNT(*) AS cnt FROM ({$union_sql}) u LEFT JOIN (SELECT bo_table, wr_id, COUNT(*) AS report_cnt FROM {$tbl_report} GROUP BY bo_table, wr_id) r ON r.bo_table = u.bo_table AND r.wr_id = u.wr_id WHERE {$sql_today_where} ";
    } else {
        $sql_today = " SELECT COUNT(*) AS cnt FROM ({$union_sql}) u WHERE {$sql_today_where} ";
    }
    $row_today = @sql_fetch($sql_today, false);
    $today_count = ($row_today && isset($row_today['cnt'])) ? (int)$row_today['cnt'] : 0;

    // [전체] 탭: UNION 카운트 실패 시 보드별 합산으로 작성일-컬럼 사이 통계 정상 표시
    if (!$view_reported && $total_count === 0 && count($board_list) > 0) {
        $total_count = 0;
        $today_count = 0;
        $search_w = " 1=1 ";
        if ($stx !== '') {
            $stx_esc = sql_real_escape_string($stx);
            if ($sfl === 'wr_subject') {
                $search_w .= " AND w.wr_subject LIKE '%{$stx_esc}%' ";
            } elseif ($sfl === 'wr_name') {
                $search_w .= " AND w.wr_name LIKE '%{$stx_esc}%' ";
            } elseif ($sfl === 'wr_id') {
                $search_w .= " AND w.wr_id = '" . (int)$stx . "' ";
            } else {
                $search_w .= " AND (w.wr_subject LIKE '%{$stx_esc}%' OR w.wr_name LIKE '%{$stx_esc}%' OR w.wr_content LIKE '%{$stx_esc}%' OR w.mb_id LIKE '%{$stx_esc}%') ";
            }
        }
        if ($filter_comment === '0') {
            $search_w .= " AND w.wr_is_comment = 0 ";
        } elseif ($filter_comment === '1') {
            $search_w .= " AND w.wr_is_comment = 1 ";
        }
        if ($fr_date !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $fr_date)) {
            $search_w .= " AND w.wr_datetime >= '" . sql_real_escape_string($fr_date) . " 00:00:00' ";
        }
        if ($to_date !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $to_date)) {
            $search_w .= " AND w.wr_datetime <= '" . sql_real_escape_string($to_date) . " 23:59:59' ";
        }
        foreach ($board_list as $bt => $subject) {
            $write_tbl = $g5['write_prefix'] . $bt;
            $r = @sql_fetch(" SELECT COUNT(*) AS cnt FROM {$write_tbl} w WHERE {$search_w} ", false);
            if ($r && isset($r['cnt'])) {
                $total_count += (int)$r['cnt'];
            }
            $today_w = $search_w . " AND w.wr_datetime >= '" . sql_real_escape_string($today_start) . "' AND w.wr_datetime <= '" . sql_real_escape_string($today_end) . "' ";
            $rt = @sql_fetch(" SELECT COUNT(*) AS cnt FROM {$write_tbl} w WHERE {$today_w} ", false);
            if ($rt && isset($rt['cnt'])) {
                $today_count += (int)$rt['cnt'];
            }
        }
    }

    $total_page = $total_count > 0 ? (int)ceil($total_count / $rows) : 1;
    if ($page > $total_page) {
        $page = $total_page;
    }
    $from_record = ($page - 1) * $rows;

    $order_col = $sfl;
    if ($order_col === 'report_cnt') {
        $order_col = 'report_cnt';
    } elseif ($order_col === 'wr_name') {
        $order_col = 'u.wr_datetime';
    } else {
        $order_col = 'u.' . $order_col;
    }
    $report_join = " LEFT JOIN (SELECT bo_table, wr_id, COUNT(*) AS report_cnt FROM {$tbl_report} GROUP BY bo_table, wr_id) r ON r.bo_table = u.bo_table AND r.wr_id = u.wr_id ";
    $tbl_exempt = (defined('G5_TABLE_PREFIX') ? G5_TABLE_PREFIX : 'g5_') . 'board_report_exempt';
    $exempt_join = " LEFT JOIN {$tbl_exempt} e ON e.bo_table = u.bo_table AND e.wr_id = u.wr_id ";
    $sql = "
        SELECT u.bo_table, u.bo_subject, u.wr_id, u.wr_num, u.wr_reply, u.wr_parent, u.wr_is_comment, u.wr_comment,
               u.wr_option, u.wr_subject, u.wr_content, u.wr_hit, u.wr_good, u.wr_nogood,
               u.mb_id, u.wr_name, u.wr_datetime, u.wr_last, u.wr_ip, u.ca_name,
               IFNULL(r.report_cnt, 0) AS report_cnt,
               (e.wr_id IS NOT NULL) AS is_exempt
        FROM ({$union_sql}) u
        {$report_join}
        {$exempt_join}
        WHERE {$sql_search}
        ORDER BY {$order_col} {$sod}, u.wr_id {$sod}
        LIMIT {$from_record}, {$rows}
    ";
    $result = sql_query($sql);
} elseif (!$view_all && $bo_table !== '') {
    $write_table = $g5['write_prefix'] . $bo_table;
    $board_subject = $board_list[$bo_table];

    $sql_search = " 1=1 ";
    if ($stx !== '') {
        $stx_esc = sql_real_escape_string($stx);
        if ($sfl === 'wr_subject') {
            $sql_search .= " AND w.wr_subject LIKE '%{$stx_esc}%' ";
        } elseif ($sfl === 'wr_name') {
            $sql_search .= " AND w.wr_name LIKE '%{$stx_esc}%' ";
        } elseif ($sfl === 'wr_id') {
            $sql_search .= " AND w.wr_id = '" . (int)$stx . "' ";
        } else {
            $sql_search .= " AND (w.wr_subject LIKE '%{$stx_esc}%' OR w.wr_name LIKE '%{$stx_esc}%' OR w.wr_content LIKE '%{$stx_esc}%' OR w.mb_id LIKE '%{$stx_esc}%') ";
        }
    }
    if ($filter_comment === '0') {
        $sql_search .= " AND w.wr_is_comment = 0 ";
    } elseif ($filter_comment === '1') {
        $sql_search .= " AND w.wr_is_comment = 1 ";
    }
    if ($fr_date !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $fr_date)) {
        $sql_search .= " AND w.wr_datetime >= '" . sql_real_escape_string($fr_date) . " 00:00:00' ";
    }
    if ($to_date !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $to_date)) {
        $sql_search .= " AND w.wr_datetime <= '" . sql_real_escape_string($to_date) . " 23:59:59' ";
    }

    $bo_esc = sql_real_escape_string($bo_table);
    $sql_count = " SELECT COUNT(*) AS cnt FROM {$write_table} w WHERE {$sql_search} ";
    $row_cnt = sql_fetch($sql_count);
    $total_count = (int)($row_cnt['cnt'] ?? 0);

    // 오늘 작성된 게시글수 (해당 게시판 기준)
    $sql_today = " SELECT COUNT(*) AS cnt FROM {$write_table} w WHERE {$sql_search} AND w.wr_datetime >= '" . sql_real_escape_string($today_start) . "' AND w.wr_datetime <= '" . sql_real_escape_string($today_end) . "' ";
    $row_today = sql_fetch($sql_today);
    $today_count = (int)($row_today['cnt'] ?? 0);

    $total_page = $total_count > 0 ? (int)ceil($total_count / $rows) : 1;
    if ($page > $total_page) {
        $page = $total_page;
    }
    $from_record = ($page - 1) * $rows;

    $order_col = $sfl;
    if ($order_col === 'report_cnt') {
        $order_by_col = 'report_cnt';
    } elseif ($order_col === 'wr_name') {
        $order_by_col = 'w.wr_datetime';
    } else {
        $order_by_col = 'w.' . $order_col;
    }
    $tbl_exempt = (defined('G5_TABLE_PREFIX') ? G5_TABLE_PREFIX : 'g5_') . 'board_report_exempt';
    $sql = "
        SELECT w.wr_id, w.wr_num, w.wr_reply, w.wr_parent, w.wr_is_comment, w.wr_comment,
               w.wr_option, w.wr_subject, w.wr_content, w.wr_hit, w.wr_good, w.wr_nogood,
               w.mb_id, w.wr_name, w.wr_datetime, w.wr_last, w.wr_ip, w.ca_name,
               IFNULL(r.report_cnt, 0) AS report_cnt,
               (e.wr_id IS NOT NULL) AS is_exempt
        FROM {$write_table} w
        LEFT JOIN (
            SELECT wr_id, COUNT(*) AS report_cnt FROM {$tbl_report}
            WHERE bo_table = '{$bo_esc}' GROUP BY wr_id
        ) r ON r.wr_id = w.wr_id
        LEFT JOIN {$tbl_exempt} e ON e.bo_table = '{$bo_esc}' AND e.wr_id = w.wr_id
        WHERE {$sql_search}
        ORDER BY {$order_by_col} {$sod}, w.wr_id {$sod}
        LIMIT {$from_record}, {$rows}
    ";
    $result = sql_query($sql);
}

$listall = '<a href="' . $_SERVER['SCRIPT_NAME'] . '?sub_menu=' . $sub_menu . '" class="ov_listall">전체목록</a>';
$g5['title'] = '게시물 관리';
require_once G5_ADMIN_PATH . '/admin.head.php';

$qstr_base = 'sub_menu=' . $sub_menu . '&bo_table=' . urlencode($bo_table) . '&sfl=' . urlencode($sfl) . '&sod=' . urlencode($sod) . '&stx=' . urlencode($stx) . '&filter_comment=' . urlencode($filter_comment) . '&fr_date=' . urlencode($fr_date) . '&to_date=' . urlencode($to_date) . '&rows=' . (int)$rows;
$qstr_no_bo = trim(preg_replace('/&?bo_table=[^&]*/', '', $qstr_base), '&');
// 컬럼 클릭 시 오름/내림차순 정렬 링크 (sfl=컬럼, sod=asc|desc)
function sp_post_sort_link($col, $label, $qstr_base, $sfl, $sod) {
    $new_sod = ($sfl === $col && $sod === 'desc') ? 'asc' : 'desc';
    $q = preg_replace('/&?sfl=[^&]*/', '', $qstr_base);
    $q = preg_replace('/&?sod=[^&]*/', '', $q);
    $q = trim($q, '&');
    $url = $_SERVER['SCRIPT_NAME'] . '?' . $q . ($q ? '&' : '') . 'sfl=' . urlencode($col) . '&sod=' . $new_sod . '&page=1';
    $arrow = ($sfl === $col) ? ($sod === 'asc' ? ' ↑' : ' ↓') : '';
    return '<a href="' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . $arrow . '</a>';
}
?>

<style>
.sp-post-form tbody th,
.sp-post-form tbody td { text-align: left !important; }
.sp-post-form tbody td.td_num { text-align: right !important; }
.sp-post-nav-btn { display: inline-flex; align-items: center; justify-content: center; padding: 6px 12px; line-height: 1.4; min-height: 2em; box-sizing: border-box; }
.sp-post-form thead th a { text-decoration: none; }
.sp-post-form thead th a:hover { text-decoration: underline; }
</style>

<div class="local_ov01 local_ov">
    <?php echo $listall; ?>
    <span class="btn_ov01"><span class="ov_txt">총 게시물 </span><span class="ov_num"><?php echo number_format($total_count); ?> 건</span></span>
    <?php if (!$view_all && $board_subject !== ''): ?>
    <span class="btn_ov01"><span class="ov_txt">게시판 </span><span class="ov_num"><?php echo htmlspecialchars($board_subject, ENT_QUOTES, 'UTF-8'); ?> (<?php echo htmlspecialchars($bo_table, ENT_QUOTES, 'UTF-8'); ?>)</span></span>
    <?php endif; ?>
    <span class="btn_ov01" style="margin-left:12px;">
        <label for="rows_select" class="sound_only">페이지당 행 수</label>
        <select id="rows_select" class="frm_input" style="padding:4px 8px;min-width:60px;">
            <?php foreach ($rows_allow as $r): ?>
            <option value="<?php echo $r; ?>" <?php echo $rows === $r ? 'selected' : ''; ?>><?php echo $r; ?>개씩</option>
            <?php endforeach; ?>
        </select>
    </span>
</div>
<script>
(function(){
    var sel = document.getElementById('rows_select');
    if (!sel) return;
    var baseQ = '<?php echo addslashes(preg_replace('/&rows=\d+/', '', $qstr_base) . '&rows='); ?>';
    sel.addEventListener('change', function(){
        var rows = this.value;
        location.href = '<?php echo $_SERVER['SCRIPT_NAME']; ?>?' + baseQ + rows + '&page=1';
    });
})();
</script>

<?php if (count($board_list) > 0): ?>
<section class="sp-post-board-nav" style="margin:12px 0;padding:12px;background:#f9f9f9;border:1px solid #e0e0e0;border-radius:4px;">
    <h3 class="sound_only">게시판별 분류</h3>
    <div style="display:flex;flex-wrap:wrap;align-items:center;gap:8px;">
        <a href="?<?php echo $qstr_no_bo; ?><?php echo $qstr_no_bo !== '' ? '&' : ''; ?>bo_table=all" class="btn <?php echo ($view_all && !$view_reported) ? 'btn_02' : 'btn_03'; ?> sp-post-nav-btn">전체</a>
        <a href="?<?php echo $qstr_no_bo; ?><?php echo $qstr_no_bo !== '' ? '&' : ''; ?>bo_table=reported" class="btn <?php echo $view_reported ? 'btn_02' : 'btn_03'; ?> sp-post-nav-btn">신고</a>
        <?php foreach ($board_list as $bt => $subject): ?>
        <a href="?<?php echo $qstr_no_bo; ?><?php echo $qstr_no_bo !== '' ? '&' : ''; ?>bo_table=<?php echo urlencode($bt); ?>" class="btn <?php echo (!$view_all && $bo_table === $bt) ? 'btn_02' : 'btn_03'; ?> sp-post-nav-btn"><?php echo htmlspecialchars($subject, ENT_QUOTES, 'UTF-8'); ?></a>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<form name="freport_config" method="post" action="">
    <input type="hidden" name="report_config_save" value="1">
    <div class="tbl_head01 tbl_wrap" style="margin-top:12px;">
        <table>
            <caption class="sound_only">신고 누적 설정</caption>
            <colgroup><col style="width:180px;"><col></colgroup>
            <tbody>
                <tr>
                    <th scope="row">신고 누적 비공개 임계</th>
                    <td>
                        <input type="number" name="report_hide_limit" value="<?php echo (int)$report_hide_limit; ?>" min="1" max="999" class="frm_input" style="width:80px;"> 회 이상 — <strong>동일 신고사유</strong>가 이 횟수 이상일 때만 해당 게시물 비공개 (예: 10회면 "욕설 10회" 또는 "광고 10회" 등 같은 사유만 10회 이상)
                        <button type="submit" class="btn btn_02">저장</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</form>

<?php if (count($board_list) === 0): ?>
<p class="frm_info">관리 권한이 있는 게시판이 없습니다.</p>
<?php else: ?>

<form name="fsearch" id="fsearch" class="local_sch01 local_sch" method="get">
    <input type="hidden" name="sub_menu" value="<?php echo $sub_menu; ?>">
    <input type="hidden" name="bo_table" value="<?php echo htmlspecialchars($bo_table, ENT_QUOTES, 'UTF-8'); ?>">
    <input type="hidden" name="rows" value="<?php echo (int)$rows; ?>">
    <label for="sfl" class="sound_only">검색대상</label>
    <select name="sfl" id="sfl">
        <option value="wr_datetime" <?php echo $sfl === 'wr_datetime' ? 'selected' : ''; ?>>작성일</option>
        <option value="wr_subject" <?php echo $sfl === 'wr_subject' ? 'selected' : ''; ?>>제목</option>
        <option value="wr_name" <?php echo $sfl === 'wr_name' ? 'selected' : ''; ?>>작성자</option>
        <option value="wr_id" <?php echo $sfl === 'wr_id' ? 'selected' : ''; ?>>글번호</option>
    </select>
    <label for="stx" class="sound_only">검색어</label>
    <input type="text" name="stx" id="stx" value="<?php echo htmlspecialchars($stx, ENT_QUOTES, 'UTF-8'); ?>" class="frm_input">
    <label for="filter_comment">원글/댓글</label>
    <select name="filter_comment" id="filter_comment">
        <option value="" <?php echo $filter_comment === '' ? 'selected' : ''; ?>>전체</option>
        <option value="0" <?php echo $filter_comment === '0' ? 'selected' : ''; ?>>원글만</option>
        <option value="1" <?php echo $filter_comment === '1' ? 'selected' : ''; ?>>댓글만</option>
    </select>
    <label for="fr_date">시작일</label>
    <input type="text" name="fr_date" id="fr_date" value="<?php echo htmlspecialchars($fr_date, ENT_QUOTES, 'UTF-8'); ?>" class="frm_input" size="10" placeholder="YYYY-MM-DD">
    <label for="to_date">종료일</label>
    <input type="text" name="to_date" id="to_date" value="<?php echo htmlspecialchars($to_date, ENT_QUOTES, 'UTF-8'); ?>" class="frm_input" size="10" placeholder="YYYY-MM-DD">
    <input type="submit" value="검색" class="btn_submit">
</form>

<?php // 작성일 폼과 컬럼 사이: [전체/게시판] 게시글수·오늘 작성 / [신고] 신고·비공개·해제된 건수 ?>
<div class="sp-post-stats local_ov01" style="margin:12px 0;padding:10px 14px;background:#f5f5f5;border:1px solid #e8e8e8;border-radius:4px;">
    <?php if ($view_reported): ?>
    <span class="btn_ov01"><span class="ov_txt">신고게시글수 </span><span class="ov_num"><?php echo number_format($total_count); ?> 건</span></span>
    <span class="btn_ov01" style="margin-left:12px;"><span class="ov_txt">비공개게시글수 </span><span class="ov_num"><?php echo number_format($count_hidden_posts); ?> 건</span></span>
    <span class="btn_ov01" style="margin-left:12px;"><span class="ov_txt">해제된게시글수 </span><span class="ov_num"><?php echo number_format($count_exempt_posts); ?> 건</span></span>
    <?php else: ?>
    <span class="btn_ov01"><span class="ov_txt"><?php echo $view_all ? '전체' : '해당 게시판'; ?> 게시글수 </span><span class="ov_num"><?php echo number_format($total_count); ?> 건</span></span>
    <span class="btn_ov01" style="margin-left:12px;"><span class="ov_txt">오늘 작성된 게시글수 </span><span class="ov_num"><?php echo number_format($today_count); ?> 건</span></span>
    <?php if (!$view_all && $board_subject !== ''): ?>
    <span class="btn_ov01" style="margin-left:12px;"><span class="ov_txt">게시판 </span><span class="ov_num"><?php echo htmlspecialchars($board_subject, ENT_QUOTES, 'UTF-8'); ?> (<?php echo htmlspecialchars($bo_table, ENT_QUOTES, 'UTF-8'); ?>)</span></span>
    <?php endif; ?>
    <?php endif; ?>
</div>

<div class="tbl_head01 tbl_wrap sp-post-form" style="margin-top:16px;">
    <table>
        <caption class="sound_only">게시물 목록</caption>
        <thead>
            <tr>
                <?php if ($view_all): ?><th scope="col">게시판</th><?php endif; ?>
                <th scope="col"><?php echo sp_post_sort_link('wr_id', '글번호', $qstr_base, $sfl, $sod); ?></th>
                <th scope="col">구분</th>
                <th scope="col"><?php echo sp_post_sort_link('wr_subject', '제목', $qstr_base, $sfl, $sod); ?></th>
                <th scope="col"><?php echo sp_post_sort_link('wr_name', '작성자', $qstr_base, $sfl, $sod); ?></th>
                <th scope="col">회원ID</th>
                <th scope="col"><?php echo sp_post_sort_link('wr_datetime', '작성일', $qstr_base, $sfl, $sod); ?></th>
                <th scope="col"><?php echo sp_post_sort_link('wr_hit', '조회', $qstr_base, $sfl, $sod); ?></th>
                <th scope="col"><?php echo sp_post_sort_link('wr_comment', '댓글', $qstr_base, $sfl, $sod); ?></th>
                <th scope="col"><?php echo sp_post_sort_link('wr_good', '추천', $qstr_base, $sfl, $sod); ?></th>
                <th scope="col"><?php echo sp_post_sort_link('wr_nogood', '비추', $qstr_base, $sfl, $sod); ?></th>
                <th scope="col">옵션</th>
                <th scope="col"><?php echo sp_post_sort_link('report_cnt', '신고건수', $qstr_base, $sfl, $sod); ?></th>
                <th scope="col">관리</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $colspan_extra = $view_all ? 1 : 0;
            if ($result) {
                $i = 0;
                while ($row = sql_fetch_array($result)) {
                    $bg = 'bg' . ($i % 2);
                    $is_comment = (int)($row['wr_is_comment'] ?? 0);
                    $opt = isset($row['wr_option']) ? $row['wr_option'] : '';
                    $report_cnt = (int)($row['report_cnt'] ?? 0);
                    $is_exempt = !empty($row['is_exempt']);
                    $row_bo_table = isset($row['bo_table']) ? $row['bo_table'] : $bo_table;
                    $view_url = G5_BBS_URL . '/board.php?bo_table=' . urlencode($row_bo_table) . '&wr_id=' . (int)$row['wr_id'];
                    $show_exempt_btn = ($report_cnt > 0);
                    $subject_show = get_text(cut_str($row['wr_subject'], 80));
                    if ($is_comment) {
                        $subject_show = '└ ' . $subject_show;
                    }
            ?>
            <tr class="<?php echo $bg; ?>">
                <?php if ($view_all): ?><td class="td_left"><?php echo htmlspecialchars($row['bo_subject'] ?? $board_list[$row_bo_table] ?? $row_bo_table, ENT_QUOTES, 'UTF-8'); ?></td><?php endif; ?>
                <td class="td_num"><?php echo (int)$row['wr_id']; ?></td>
                <td class="td_left"><?php echo $is_comment ? '댓글' : '원글'; ?></td>
                <td class="td_left"><?php echo htmlspecialchars($subject_show, ENT_QUOTES, 'UTF-8'); ?></td>
                <td class="td_left"><?php echo htmlspecialchars(get_text($row['wr_name']), ENT_QUOTES, 'UTF-8'); ?></td>
                <td class="td_left"><?php echo htmlspecialchars($row['mb_id'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                <td class="td_datetime"><?php echo htmlspecialchars($row['wr_datetime'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                <td class="td_num"><?php echo number_format((int)($row['wr_hit'] ?? 0)); ?></td>
                <td class="td_num"><?php echo number_format((int)($row['wr_comment'] ?? 0)); ?></td>
                <td class="td_num"><?php echo number_format((int)($row['wr_good'] ?? 0)); ?></td>
                <td class="td_num"><?php echo number_format((int)($row['wr_nogood'] ?? 0)); ?></td>
                <td class="td_left"><?php echo htmlspecialchars($opt, ENT_QUOTES, 'UTF-8'); ?></td>
                <td class="td_num"><?php echo $report_cnt > 0 ? '<strong>' . number_format($report_cnt) . '</strong>' : '0'; ?></td>
                <td class="td_left">
                    <a href="<?php echo $view_url; ?>" target="_blank" rel="noopener" class="btn btn_03">보기</a>
                    <?php if ($report_cnt > 0): ?>
                    <a href="<?php echo G5_ADMIN_URL; ?>/scorepoint/scorepoint_board_report_log.php?sub_menu=<?php echo $sub_menu; ?>&amp;bo_table=<?php echo urlencode($row_bo_table); ?>&amp;wr_id=<?php echo (int)$row['wr_id']; ?>" target="_blank" rel="noopener" class="btn btn_03">신고내용</a>
                    <?php endif; ?>
                    <a href="<?php echo G5_BBS_URL; ?>/delete.php?bo_table=<?php echo urlencode($row_bo_table); ?>&amp;wr_id=<?php echo (int)$row['wr_id']; ?>" class="btn btn_02" onclick="return confirm('이 게시물을 삭제하시겠습니까?');">삭제</a>
                    <?php if ($show_exempt_btn): ?>
                    <?php if ($is_exempt): ?>
                    <span class="btn btn_03" style="cursor:default;">해제됨</span>
                    <?php else: ?>
                    <a href="<?php echo $_SERVER['SCRIPT_NAME']; ?>?sub_menu=<?php echo $sub_menu; ?>&amp;bo_table=<?php echo urlencode($row_bo_table); ?>&amp;return_bo=<?php echo urlencode($bo_table); ?>&amp;report_exempt=1&amp;wr_id=<?php echo (int)$row['wr_id']; ?>&amp;page=<?php echo (int)$page; ?>" class="btn btn_02" onclick="return confirm('이 게시물의 비공개를 해제하시겠습니까? (신고 기록은 유지됩니다.)');">비공개 해제</a>
                    <?php endif; ?>
                    <?php endif; ?>
                </td>
            </tr>
            <?php
                    $i++;
                }
                if ($i === 0) {
                    echo '<tr><td colspan="' . (13 + $colspan_extra) . '" class="empty_table">게시물이 없습니다.</td></tr>';
                }
            } else {
                echo '<tr><td colspan="' . (13 + $colspan_extra) . '" class="empty_table">게시물을 불러올 수 없습니다.</td></tr>';
            }
            ?>
        </tbody>
    </table>
</div>

<?php if ($total_count > 0): ?>
<nav class="pg_wrap">
    <?php
    $paging_url = $_SERVER['SCRIPT_NAME'] . '?' . $qstr_base . '&page=';
    echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, $paging_url);
    ?>
</nav>
<?php endif; ?>

<?php endif; ?>

<?php
require_once G5_ADMIN_PATH . '/admin.tail.php';
?>
