<?php
/**
 * /plugin/chat/chat_dm_ajax.php — 1:1 DM 채팅 API
 */
@error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING & ~E_DEPRECATED);
@ini_set('display_errors', '0');

$_dm_dir = __DIR__;
$_common_path = $_dm_dir . '/../../common.php';
if (!is_file($_common_path)) {
    $_common_path = $_SERVER['DOCUMENT_ROOT'] . '/common.php';
}
if (!is_file($_common_path)) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(array('ok' => 0, 'msg' => 'common.php not found'), JSON_UNESCAPED_UNICODE);
    exit;
}

ob_start();
include_once($_common_path);
include_once(G5_PLUGIN_PATH . '/chat/_common.php');
include_once(G5_LIB_PATH . '/eve_chat_dm.lib.php');
ob_end_clean();

@set_time_limit(20);
header('Content-Type: application/json; charset=utf-8');

if (isset($connect_db)) {
    @mysqli_set_charset($connect_db, 'utf8mb4');
}

$__t0 = microtime(true);

function eve_dm_json($arr, $http_status = 200)
{
    global $__t0;
    if (!is_array($arr)) {
        $arr = array('ok' => 0, 'msg' => 'invalid response');
    }
    $arr['response_time_ms'] = (int)round((microtime(true) - $__t0) * 1000);
    if (!isset($arr['http_status'])) {
        $arr['http_status'] = $http_status;
    }
    http_response_code($http_status);
    echo json_encode($arr, JSON_UNESCAPED_UNICODE);
    exit;
}

eve_chat_dm_ensure_tables();

$act = isset($_REQUEST['act']) ? trim($_REQUEST['act']) : '';
if ($act === '') {
    $act = 'rooms';
}

$is_member = !empty($member['mb_id']);
$my_id = $is_member ? $member['mb_id'] : '';
$tbl_room = $g5['chat_dm_room_table'];
$tbl_msg  = $g5['chat_dm_msg_table'];
$tbl_cfg  = $g5['chat_config_table'];

$cfg = sql_fetch("SELECT * FROM {$tbl_cfg} LIMIT 1");
if (!$cfg) {
    $cfg = array();
}

function eve_dm_check_badwords($content, $cfg)
{
    $bad = isset($cfg['cf_badwords']) ? trim($cfg['cf_badwords']) : '';
    if ($bad === '') {
        return '';
    }
    $parts = preg_split('/[\r\n,]+/', $bad);
    foreach ($parts as $w) {
        $w = trim($w);
        if ($w === '') {
            continue;
        }
        if (mb_stripos($content, $w) !== false) {
            return '금칙어가 포함되어 전송이 차단되었습니다.';
        }
    }
    return '';
}

function eve_dm_get_room($dm_id, $my_id)
{
    global $tbl_room;

    $dm_id = (int)$dm_id;
    if ($dm_id < 1 || !$my_id) {
        return null;
    }

    $room = sql_fetch("
        SELECT * FROM {$tbl_room}
        WHERE dm_id = '{$dm_id}'
          AND (female_mb_id = '" . sql_real_escape_string($my_id) . "'
               OR (biz_mb_id = '" . sql_real_escape_string($my_id) . "' AND biz_visible = 1))
        LIMIT 1
    ");

    return $room ?: null;
}

function eve_dm_can_send($room, $my_id)
{
    $role = eve_chat_dm_room_member($room, $my_id);
    if ($role === 'female') {
        return true;
    }
    if ($role === 'biz') {
        return ((int)$room['biz_visible'] === 1);
    }
    return false;
}

if ($act === 'unread_count') {
    if (!$is_member) {
        eve_dm_json(array('ok' => 1, 'count' => 0, 'result_count' => 0));
    }
    $cnt = eve_chat_dm_unread_count($my_id);
    eve_dm_json(array('ok' => 1, 'count' => $cnt, 'result_count' => $cnt));
}

if ($act === 'rooms') {
    if (!$is_member) {
        eve_dm_json(array('ok' => 0, 'msg' => '로그인 후 이용해 주세요.', 'http_status' => 401), 401);
    }

    $rows = array();
    if (eve_member_is_female_normal($member)) {
        $res = sql_query("
            SELECT * FROM {$tbl_room}
            WHERE female_mb_id = '" . sql_real_escape_string($my_id) . "'
            ORDER BY IFNULL(last_msg_at, created_at) DESC
            LIMIT 100
        ");
    } elseif (eve_member_is_biz($member)) {
        $res = sql_query("
            SELECT * FROM {$tbl_room}
            WHERE biz_mb_id = '" . sql_real_escape_string($my_id) . "'
              AND biz_visible = 1
            ORDER BY IFNULL(last_msg_at, created_at) DESC
            LIMIT 100
        ");
    } else {
        eve_dm_json(array('ok' => 0, 'msg' => '1:1 채팅 이용 권한이 없습니다.'));
    }

    while ($r = sql_fetch_array($res)) {
        $rows[] = eve_chat_dm_format_room($r, $my_id);
    }

    eve_dm_json(array(
        'ok' => 1,
        'rooms' => $rows,
        'result_count' => count($rows),
        'is_female' => eve_member_is_female_normal($member) ? 1 : 0,
        'is_biz' => eve_member_is_biz($member) ? 1 : 0,
    ));
}

if ($act === 'open') {
    if (!$is_member) {
        eve_dm_json(array('ok' => 0, 'msg' => '로그인 후 이용해 주세요.', 'http_status' => 401), 401);
    }
    if (!eve_member_is_female_normal($member)) {
        eve_dm_json(array('ok' => 0, 'msg' => '일반회원(여성)만 채팅을 시작할 수 있습니다.'));
    }

    $jr_id = isset($_REQUEST['jr_id']) ? (int)$_REQUEST['jr_id'] : 0;
    if ($jr_id < 1) {
        eve_dm_json(array('ok' => 0, 'msg' => '채용공고 정보가 없습니다.'));
    }

    $jr = sql_fetch("
        SELECT jr_id, mb_id, jr_nickname, jr_company, jr_title, jr_status
        FROM g5_jobs_register
        WHERE jr_id = '{$jr_id}'
        LIMIT 1
    ");
    if (!$jr || $jr['jr_status'] !== 'ongoing') {
        eve_dm_json(array('ok' => 0, 'msg' => '유효하지 않은 채용공고입니다.'));
    }

    $biz_mb_id = $jr['mb_id'];
    if ($biz_mb_id === $my_id) {
        eve_dm_json(array('ok' => 0, 'msg' => '본인 공고에는 채팅할 수 없습니다.'));
    }

    $biz = sql_fetch("SELECT mb_id, mb_nick, mb_1 FROM {$g5['member_table']} WHERE mb_id = '" . sql_real_escape_string($biz_mb_id) . "' LIMIT 1");
    if (!$biz || !eve_member_is_biz($biz)) {
        eve_dm_json(array('ok' => 0, 'msg' => '기업회원 공고만 채팅할 수 있습니다.'));
    }

    $existing = sql_fetch("
        SELECT * FROM {$tbl_room}
        WHERE female_mb_id = '" . sql_real_escape_string($my_id) . "'
          AND biz_mb_id = '" . sql_real_escape_string($biz_mb_id) . "'
          AND jr_id = '{$jr_id}'
        LIMIT 1
    ");

    if ($existing) {
        $room = $existing;
    } else {
        sql_query("
            INSERT INTO {$tbl_room}
            SET jr_id = '{$jr_id}',
                female_mb_id = '" . sql_real_escape_string($my_id) . "',
                biz_mb_id = '" . sql_real_escape_string($biz_mb_id) . "',
                biz_visible = 0,
                created_at = NOW()
        ");
        $dm_id = sql_insert_id();
        $room = sql_fetch("SELECT * FROM {$tbl_room} WHERE dm_id = '" . (int)$dm_id . "' LIMIT 1");
    }

    eve_dm_json(array(
        'ok' => 1,
        'room' => eve_chat_dm_format_room($room, $my_id),
        'rows_affected' => 1,
    ));
}

if ($act === 'messages') {
    if (!$is_member) {
        eve_dm_json(array('ok' => 0, 'msg' => '로그인 후 이용해 주세요.', 'http_status' => 401), 401);
    }

    $dm_id = isset($_REQUEST['dm_id']) ? (int)$_REQUEST['dm_id'] : 0;
    $last_id = isset($_REQUEST['last_id']) ? (int)$_REQUEST['last_id'] : 0;

    $room = eve_dm_get_room($dm_id, $my_id);
    if (!$room) {
        eve_dm_json(array('ok' => 0, 'msg' => '채팅방을 찾을 수 없습니다.'));
    }

    $rows = array();
    $res = sql_query("
        SELECT msg_id, dm_id, sender_mb_id, msg_content, msg_read_at, msg_datetime
        FROM {$tbl_msg}
        WHERE dm_id = '" . (int)$dm_id . "'
          AND msg_id > '{$last_id}'
        ORDER BY msg_id ASC
        LIMIT 100
    ");
    while ($r = sql_fetch_array($res)) {
        $rows[] = array(
            'msg_id' => (int)$r['msg_id'],
            'sender_mb_id' => $r['sender_mb_id'],
            'mine' => ($r['sender_mb_id'] === $my_id) ? 1 : 0,
            'content' => $r['msg_content'],
            'read_at' => $r['msg_read_at'],
            'datetime' => $r['msg_datetime'],
        );
    }

    eve_dm_json(array(
        'ok' => 1,
        'room' => eve_chat_dm_format_room($room, $my_id),
        'list' => $rows,
        'result_count' => count($rows),
        'can_send' => eve_dm_can_send($room, $my_id) ? 1 : 0,
    ));
}

if ($act === 'read') {
    if (!$is_member) {
        eve_dm_json(array('ok' => 0, 'msg' => '로그인 후 이용해 주세요.'));
    }

    $dm_id = isset($_REQUEST['dm_id']) ? (int)$_REQUEST['dm_id'] : 0;
    $room = eve_dm_get_room($dm_id, $my_id);
    if (!$room) {
        eve_dm_json(array('ok' => 0, 'msg' => '채팅방을 찾을 수 없습니다.'));
    }

    $role = eve_chat_dm_room_member($room, $my_id);
    $other_id = ($role === 'female') ? $room['biz_mb_id'] : $room['female_mb_id'];

    sql_query("
        UPDATE {$tbl_msg}
        SET msg_read_at = NOW()
        WHERE dm_id = '" . (int)$dm_id . "'
          AND sender_mb_id = '" . sql_real_escape_string($other_id) . "'
          AND (msg_read_at IS NULL OR msg_read_at = '0000-00-00 00:00:00')
    ", false);

    if ($role === 'female') {
        sql_query("UPDATE {$tbl_room} SET female_unread = 0 WHERE dm_id = '" . (int)$dm_id . "'", false);
    } elseif ($role === 'biz') {
        sql_query("UPDATE {$tbl_room} SET biz_unread = 0 WHERE dm_id = '" . (int)$dm_id . "'", false);
    }

    eve_dm_json(array('ok' => 1, 'rows_affected' => 1));
}

if ($act === 'send') {
    if (!$is_member) {
        eve_dm_json(array('ok' => 0, 'msg' => '로그인 후 이용해 주세요.'));
    }

    $dm_id = isset($_POST['dm_id']) ? (int)$_POST['dm_id'] : 0;
    $content = isset($_POST['content']) ? trim($_POST['content']) : '';
    $content = preg_replace("/\r\n|\r/", "\n", $content);

    if ($content === '') {
        eve_dm_json(array('ok' => 0, 'msg' => '내용이 없습니다.'));
    }

    $room = sql_fetch("SELECT * FROM {$tbl_room} WHERE dm_id = '" . (int)$dm_id . "' LIMIT 1");
    if (!$room) {
        eve_dm_json(array('ok' => 0, 'msg' => '채팅방을 찾을 수 없습니다.'));
    }

    $role = eve_chat_dm_room_member($room, $my_id);
    if ($role === '') {
        eve_dm_json(array('ok' => 0, 'msg' => '접근 권한이 없습니다.'));
    }

    if ($role === 'biz' && (int)$room['biz_visible'] !== 1) {
        eve_dm_json(array('ok' => 0, 'msg' => '먼저 메시지를 보낼 수 없습니다. 일반회원의 첫 메시지를 기다려 주세요.'));
    }

    if ($role === 'female' && !eve_member_is_female_normal($member)) {
        eve_dm_json(array('ok' => 0, 'msg' => '일반회원(여성)만 메시지를 보낼 수 있습니다.'));
    }

    $bad_msg = eve_dm_check_badwords($content, $cfg);
    if ($bad_msg !== '') {
        eve_dm_json(array('ok' => 0, 'msg' => $bad_msg, 'last_error' => 'badword'));
    }

    $spam_sec = isset($cfg['cf_spam_sec']) ? (int)$cfg['cf_spam_sec'] : 2;
    if ($spam_sec < 0) {
        $spam_sec = 0;
    }
    $last = sql_fetch("
        SELECT msg_datetime FROM {$tbl_msg}
        WHERE sender_mb_id = '" . sql_real_escape_string($my_id) . "'
        ORDER BY msg_id DESC LIMIT 1
    ");
    if ($last && !empty($last['msg_datetime']) && $spam_sec > 0) {
        $dt = strtotime($last['msg_datetime']);
        if ($dt && (time() - $dt) < $spam_sec) {
            eve_dm_json(array('ok' => 0, 'msg' => "연속 전송 제한({$spam_sec}초)"));
        }
    }

    $preview = mb_substr(strip_tags($content), 0, 80, 'UTF-8');

    sql_query("
        INSERT INTO {$tbl_msg}
        SET dm_id = '" . (int)$dm_id . "',
            sender_mb_id = '" . sql_real_escape_string($my_id) . "',
            msg_content = '" . sql_real_escape_string($content) . "',
            msg_datetime = NOW()
    ");
    $msg_id = sql_insert_id();
    if (!$msg_id) {
        eve_dm_json(array('ok' => 0, 'msg' => 'DB 저장 실패', 'last_error' => 'insert_fail'));
    }

    $set_sql = "
        last_msg_preview = '" . sql_real_escape_string($preview) . "',
        last_msg_at = NOW()
    ";

    if ($role === 'female') {
        $set_sql .= ", biz_unread = biz_unread + 1, biz_visible = 1";
    } else {
        $set_sql .= ", female_unread = female_unread + 1";
    }

    sql_query("UPDATE {$tbl_room} SET {$set_sql} WHERE dm_id = '" . (int)$dm_id . "'", false);

    eve_dm_json(array(
        'ok' => 1,
        'msg_id' => (int)$msg_id,
        'rows_affected' => 1,
    ));
}

if ($act === 'notifications') {
    if (!$is_member) {
        eve_dm_json(array('ok' => 1, 'list' => array(), 'result_count' => 0));
    }

    $list = array();

    if (eve_member_is_female_normal($member) || eve_member_is_biz($member)) {
        $where = eve_member_is_female_normal($member)
            ? "female_mb_id = '" . sql_real_escape_string($my_id) . "'"
            : "biz_mb_id = '" . sql_real_escape_string($my_id) . "' AND biz_visible = 1";

        $res = sql_query("
            SELECT r.*, m.msg_content, m.msg_datetime, m.sender_mb_id, m.msg_read_at
            FROM {$tbl_room} r
            INNER JOIN {$tbl_msg} m ON m.dm_id = r.dm_id
            WHERE {$where}
            ORDER BY m.msg_id DESC
            LIMIT 30
        ");
        while ($r = sql_fetch_array($res)) {
            $fmt = eve_chat_dm_format_room($r, $my_id);
            $is_unread = ($r['sender_mb_id'] !== $my_id && (empty($r['msg_read_at']) || $r['msg_read_at'] === '0000-00-00 00:00:00'));
            $list[] = array(
                'type' => 'chat',
                'dm_id' => (int)$r['dm_id'],
                'title' => $fmt['other_nick'],
                'desc' => mb_substr(strip_tags($r['msg_content']), 0, 120, 'UTF-8'),
                'time' => $r['msg_datetime'],
                'unread' => $is_unread ? 1 : 0,
                'job_label' => $fmt['job_label'],
            );
        }
    }

    eve_dm_json(array('ok' => 1, 'list' => $list, 'result_count' => count($list)));
}

eve_dm_json(array('ok' => 0, 'msg' => 'unknown act', 'last_error' => 'bad_act'));
