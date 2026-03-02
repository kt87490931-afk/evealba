<?php
/**
 * 어드민 - 특수배너 처리 (검색 / 연결 / 해제)
 */
$sub_menu = '910920';
require_once './_common.php';

auth_check_menu($auth, $sub_menu, 'w');

$jr_table = 'g5_jobs_register';
$sb_table = 'g5_special_banner';

$act   = isset($_REQUEST['act']) ? preg_replace('/[^a-z_]/', '', $_REQUEST['act']) : '';
$token = isset($_REQUEST['token']) ? $_REQUEST['token'] : '';

$hero_max      = 10;
$recommend_max = 6;

// ── AJAX 검색 ──
if ($act === 'search') {
    header('Content-Type: application/json; charset=utf-8');

    $q    = isset($_GET['q']) ? trim($_GET['q']) : '';
    $type = isset($_GET['type']) ? preg_replace('/[^a-z]/', '', $_GET['type']) : 'hero';

    if (!$q) { echo '[]'; exit; }

    $q_esc = sql_real_escape_string($q);

    $already_ids = array();
    $res_existing = sql_query("SELECT sb_jr_id FROM {$sb_table} WHERE sb_type = '{$type}' AND sb_status = 'active'");
    while ($ex = sql_fetch_array($res_existing)) {
        $already_ids[] = (int)$ex['sb_jr_id'];
    }
    $not_in = '';
    if ($already_ids) {
        $not_in = " AND jr_id NOT IN (" . implode(',', $already_ids) . ") ";
    }

    $sql = "SELECT jr_id, mb_id, jr_nickname, jr_company, jr_status, jr_end_date, jr_ad_labels
            FROM {$jr_table}
            WHERE jr_status = 'ongoing'
              AND jr_payment_confirmed = 1
              {$not_in}
              AND (
                  jr_id = '{$q_esc}'
                  OR jr_company LIKE '%{$q_esc}%'
                  OR jr_nickname LIKE '%{$q_esc}%'
                  OR mb_id LIKE '%{$q_esc}%'
              )
            ORDER BY jr_id DESC
            LIMIT 20";

    $result = sql_query($sql);
    $rows = array();
    $today = date('Y-m-d');
    while ($r = sql_fetch_array($result)) {
        $remaining = '—';
        if (!empty($r['jr_end_date'])) {
            $end_ts = strtotime($r['jr_end_date']);
            $today_ts = strtotime($today);
            if ($end_ts >= $today_ts) {
                $remaining = (int)(($end_ts - $today_ts) / 86400) . '일';
            } else {
                $remaining = '마감';
            }
        }
        $rows[] = array(
            'jr_id'        => (int)$r['jr_id'],
            'mb_id'        => $r['mb_id'],
            'jr_nickname'  => $r['jr_nickname'],
            'jr_company'   => $r['jr_company'],
            'jr_status'    => $r['jr_status'],
            'jr_ad_labels' => $r['jr_ad_labels'],
            'remaining'    => $remaining,
        );
    }
    echo json_encode($rows);
    exit;
}

// ── 연결 (POST) ──
if ($act === 'connect') {
    $type  = isset($_POST['type']) ? preg_replace('/[^a-z]/', '', $_POST['type']) : '';
    $jr_id = isset($_POST['jr_id']) ? (int)$_POST['jr_id'] : 0;
    $memo  = isset($_POST['memo']) ? trim($_POST['memo']) : '';

    if (!in_array($type, array('hero', 'recommend'))) {
        alert('잘못된 배너 유형입니다.');
        exit;
    }
    if (!$jr_id) {
        alert('채용광고 ID가 필요합니다.');
        exit;
    }

    $max = $type === 'hero' ? $hero_max : $recommend_max;
    $current_cnt = (int)sql_fetch("SELECT COUNT(*) as cnt FROM {$sb_table} WHERE sb_type = '{$type}' AND sb_status = 'active'")['cnt'];
    if ($current_cnt >= $max) {
        alert('슬롯이 가득 찼습니다. (' . $current_cnt . '/' . $max . ')');
        exit;
    }

    $dup = sql_fetch("SELECT sb_id FROM {$sb_table} WHERE sb_type = '{$type}' AND sb_jr_id = {$jr_id} AND sb_status = 'active'");
    if ($dup) {
        alert('이미 연결된 채용광고입니다. (sb_id: ' . $dup['sb_id'] . ')');
        exit;
    }

    $jr = sql_fetch("SELECT jr_id, mb_id, jr_status FROM {$jr_table} WHERE jr_id = {$jr_id}");
    if (!$jr) {
        alert('채용광고를 찾을 수 없습니다. (jr_id: ' . $jr_id . ')');
        exit;
    }

    $next_pos = (int)sql_fetch("SELECT IFNULL(MAX(sb_position), 0) + 1 as np FROM {$sb_table} WHERE sb_type = '{$type}' AND sb_status = 'active'")['np'];
    $memo_esc = sql_real_escape_string($memo);

    sql_query("INSERT INTO {$sb_table} (sb_type, sb_status, sb_position, sb_jr_id, sb_mb_id, sb_memo, sb_created)
               VALUES ('{$type}', 'active', {$next_pos}, {$jr_id}, '{$jr['mb_id']}', '{$memo_esc}', NOW())");

    if ($type === 'recommend') {
        $current_labels = trim(sql_fetch("SELECT jr_ad_labels FROM {$jr_table} WHERE jr_id = {$jr_id}")['jr_ad_labels'] ?? '');
        $labels_arr = $current_labels ? array_map('trim', explode(',', $current_labels)) : array();
        if (!in_array('추천업소', $labels_arr)) {
            $labels_arr[] = '추천업소';
            $new_labels = sql_real_escape_string(implode(',', $labels_arr));
            sql_query("UPDATE {$jr_table} SET jr_ad_labels = '{$new_labels}' WHERE jr_id = {$jr_id}");
        }
    }

    $label = $type === 'hero' ? '히어로배너' : '추천업소';
    alert($label . ' 연결 완료 (jr_id: ' . $jr_id . ')', './eve_special_banner.php');
    exit;
}

// ── 해제 ──
if ($act === 'remove') {
    $sb_id = isset($_GET['sb_id']) ? (int)$_GET['sb_id'] : 0;
    if (!$sb_id) {
        alert('sb_id가 필요합니다.');
        exit;
    }

    $sb = sql_fetch("SELECT * FROM {$sb_table} WHERE sb_id = {$sb_id}");
    if (!$sb) {
        alert('해당 배너를 찾을 수 없습니다.');
        exit;
    }

    sql_query("UPDATE {$sb_table} SET sb_status = 'inactive', sb_updated = NOW() WHERE sb_id = {$sb_id}");

    if ($sb['sb_type'] === 'recommend' && $sb['sb_jr_id']) {
        $current_labels = trim(sql_fetch("SELECT jr_ad_labels FROM {$jr_table} WHERE jr_id = " . (int)$sb['sb_jr_id'])['jr_ad_labels'] ?? '');
        if ($current_labels) {
            $labels_arr = array_map('trim', explode(',', $current_labels));
            $labels_arr = array_filter($labels_arr, function($l) { return $l !== '추천업소'; });
            $new_labels = sql_real_escape_string(implode(',', array_values($labels_arr)));
            sql_query("UPDATE {$jr_table} SET jr_ad_labels = '{$new_labels}' WHERE jr_id = " . (int)$sb['sb_jr_id']);
        }
    }

    $label = $sb['sb_type'] === 'hero' ? '히어로배너' : '추천업소';
    alert($label . ' 해제 완료 (jr_id: ' . $sb['sb_jr_id'] . ')', './eve_special_banner.php');
    exit;
}

// ── 히어로배너 저장 (생성/수정) ──
if ($act === 'save_hero') {
    $sb_id    = isset($_POST['sb_id']) ? (int)$_POST['sb_id'] : 0;
    $jr_id    = isset($_POST['jr_id']) ? (int)$_POST['jr_id'] : 0;
    $link     = isset($_POST['link']) ? trim($_POST['link']) : '';
    $position = isset($_POST['position']) ? (int)$_POST['position'] : 0;
    $memo     = isset($_POST['memo']) ? trim($_POST['memo']) : '';

    $sb_data = array(
        'thumb_gradient'   => isset($_POST['thumb_gradient']) ? trim($_POST['thumb_gradient']) : '1',
        'thumb_title'      => isset($_POST['thumb_title']) ? trim($_POST['thumb_title']) : '',
        'thumb_text'       => isset($_POST['thumb_text']) ? trim($_POST['thumb_text']) : '',
        'thumb_text2'      => isset($_POST['thumb_text2']) ? trim($_POST['thumb_text2']) : '',
        'thumb_icon'       => isset($_POST['thumb_icon']) ? trim($_POST['thumb_icon']) : '',
        'thumb_motion'     => isset($_POST['thumb_motion']) ? trim($_POST['thumb_motion']) : '',
        'thumb_wave'       => isset($_POST['thumb_wave']) ? (int)$_POST['thumb_wave'] : 0,
        'thumb_text_color' => isset($_POST['thumb_text_color']) ? trim($_POST['thumb_text_color']) : '#ffffff',
        'thumb_border'     => isset($_POST['thumb_border']) ? trim($_POST['thumb_border']) : '',
        'title_size'       => isset($_POST['title_size']) ? trim($_POST['title_size']) : '30px',
        'title_align'      => isset($_POST['title_align']) ? trim($_POST['title_align']) : 'left',
        'text_size'        => isset($_POST['text_size']) ? trim($_POST['text_size']) : '14px',
        'text_color'       => isset($_POST['text_color']) ? trim($_POST['text_color']) : '#ffffff',
        'text_align'       => isset($_POST['text_align']) ? trim($_POST['text_align']) : 'left',
        'text2_size'       => isset($_POST['text2_size']) ? trim($_POST['text2_size']) : '14px',
        'text2_color'      => isset($_POST['text2_color']) ? trim($_POST['text2_color']) : '#ffffff',
        'text2_align'      => isset($_POST['text2_align']) ? trim($_POST['text2_align']) : 'left',
    );

    $mb_id = '';
    if ($jr_id) {
        $jr = sql_fetch("SELECT mb_id FROM {$jr_table} WHERE jr_id = {$jr_id}");
        if ($jr) $mb_id = $jr['mb_id'];
    }

    $data_json = sql_real_escape_string(json_encode($sb_data, JSON_UNESCAPED_UNICODE));
    $link_esc  = sql_real_escape_string($link);
    $memo_esc  = sql_real_escape_string($memo);
    $mb_id_esc = sql_real_escape_string($mb_id);

    if ($sb_id) {
        $existing = sql_fetch("SELECT sb_id FROM {$sb_table} WHERE sb_id = {$sb_id} AND sb_type = 'hero'");
        if (!$existing) {
            alert('해당 히어로배너를 찾을 수 없습니다.', './eve_special_banner.php');
            exit;
        }
        sql_query("UPDATE {$sb_table} SET
            sb_jr_id = {$jr_id},
            sb_mb_id = '{$mb_id_esc}',
            sb_position = {$position},
            sb_memo = '{$memo_esc}',
            sb_data = '{$data_json}',
            sb_link = '{$link_esc}',
            sb_updated = NOW()
            WHERE sb_id = {$sb_id}");
        alert('히어로배너가 수정되었습니다. (sb_id: ' . $sb_id . ')', './eve_special_banner.php');
    } else {
        if ($position === 0) {
            $position = (int)sql_fetch("SELECT IFNULL(MAX(sb_position), 0) + 1 as np FROM {$sb_table} WHERE sb_type = 'hero' AND sb_status = 'active'")['np'];
        }
        sql_query("INSERT INTO {$sb_table} (sb_type, sb_status, sb_position, sb_jr_id, sb_mb_id, sb_memo, sb_data, sb_link, sb_created)
                   VALUES ('hero', 'active', {$position}, {$jr_id}, '{$mb_id_esc}', '{$memo_esc}', '{$data_json}', '{$link_esc}', NOW())");
        $new_id = sql_insert_id();
        alert('히어로배너가 생성되었습니다. (sb_id: ' . $new_id . ')', './eve_special_banner.php');
    }
    exit;
}

alert('잘못된 요청입니다.');
