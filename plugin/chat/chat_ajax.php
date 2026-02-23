<?php
// /plugin/chat/chat_ajax.php
// NOTE: read-only endpoints + admin actions for chat plugin

include_once('../../common.php'); // plugin/chat 기준 두 단계 위
include_once(G5_PLUGIN_PATH.'/chat/_common.php');

// 504 방지: 최대 20초 실행 후 종료 → 워커 해제
@set_time_limit(20);

header('Content-Type: application/json; charset=utf-8');

function sp_chat_json($arr){
    echo json_encode($arr, JSON_UNESCAPED_UNICODE);
    exit;
}

$act = isset($_REQUEST['act']) ? trim($_REQUEST['act']) : '';
if ($act === '') $act = 'list';

$is_member = (isset($member) && isset($member['mb_id']) && $member['mb_id']);
$is_admin  = (isset($is_admin) && $is_admin) ? true : false;
$my_id     = $is_member ? $member['mb_id'] : '';
$my_nick   = $is_member ? $member['mb_nick'] : '손님';
$ip        = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';

$tbl_chat = isset($g5['chat_table']) ? $g5['chat_table'] : (isset($g5['chat_msg_table']) ? $g5['chat_msg_table'] : 'g5_chat');
$tbl_cfg  = isset($g5['chat_config_table']) ? $g5['chat_config_table'] : 'g5_chat_config';
$tbl_ban  = isset($g5['chat_ban_table']) ? $g5['chat_ban_table'] : 'g5_chat_ban';
$tbl_online = (defined('G5_TABLE_PREFIX') ? G5_TABLE_PREFIX : 'g5_').'chat_online';
// ✅ list/hello 등에서 사용할 cfg·online_window 공통 로드 (Undefined variable 방지)
$cfg = sp_chat_get_cfg($tbl_cfg);
$online_window = isset($cfg['cf_online_window']) ? (int)$cfg['cf_online_window'] : 300;
if ($online_window < 30) $online_window = 300;

function sp_chat_get_cfg($tbl_cfg){
    $cfg = sql_fetch(" select * from {$tbl_cfg} limit 1 ");
    if(!$cfg){
        // 기본 row가 없으면 생성(최소)
        sql_query(" insert into {$tbl_cfg} set cf_id=1 ", false);
        $cfg = sql_fetch(" select * from {$tbl_cfg} limit 1 ");
    }
    return $cfg ? $cfg : array();
}

// ✅ list/hello 등에서 사용할 cfg·online_window 공통 로드 (Undefined variable 방지)
$cfg = sp_chat_get_cfg($tbl_cfg);
$online_window = isset($cfg['cf_online_window']) ? (int)$cfg['cf_online_window'] : 300;
if ($online_window < 30) $online_window = 300;

function sp_chat_level_from_member($member){
    // ScorePoint 경험치(포인트 기반 레벨) 단일 원천: theme/scorepoint/inc/point_level.php
    $pt_level_file = (defined('G5_THEME_PATH') ? G5_THEME_PATH : (defined('G5_PATH') ? dirname(G5_PATH) . '/theme/scorepoint' : '')) . '/inc/point_level.php';
    if ($pt_level_file && is_file($pt_level_file)) {
        include_once $pt_level_file;
        if (function_exists('sp_get_point_level_by_point')) {
            $mb_point = isset($member['mb_point']) ? (int)$member['mb_point'] : 0;
            $cf_admin = isset($GLOBALS['config']['cf_admin']) ? $GLOBALS['config']['cf_admin'] : (isset($config['cf_admin']) ? $config['cf_admin'] : '');
            $is_super = ($cf_admin !== '' && isset($member['mb_id']) && $member['mb_id'] === $cf_admin);
            return (int)sp_get_point_level_by_point($mb_point, $is_super);
        }
    }
    // fallback: 그누보드 mb_level
    $lv = 1;
    if (isset($member['mb_level']) && (int)$member['mb_level'] > 0) $lv = (int)$member['mb_level'];
    if (isset($member['mb_id']) && isset($GLOBALS['is_admin']) && $GLOBALS['is_admin']) $lv = 20;
    if ($lv < 1) $lv = 1;
    if ($lv > 20) $lv = 20;
    return $lv;
}

function sp_chat_get_icon_url($member){
    // ScorePoint 군인 아이콘(army_1.gif ~ army_20.gif) 사용
    $lv = sp_chat_level_from_member($member);
    if (defined('G5_THEME_URL')) {
        $u = G5_THEME_URL . '/img/army/army_' . $lv . '.gif';
        return $u;
    }
    if (defined('G5_URL')) {
        return G5_URL . '/theme/scorepoint/img/army/army_' . $lv . '.gif';
    }
    return '';
}

function sp_chat_is_banned($tbl_ban, $mb_id){
    if (!$mb_id) return false;
    $row = sql_fetch("
        select * from {$tbl_ban}
        where mb_id = '".sql_real_escape_string($mb_id)."'
          and is_active = 1
        order by banned_at desc
        limit 1
    ");
    if (!$row) return false;

    // 영구(ban_until NULL) 또는 미래면 밴중
    if (!isset($row['ban_until']) || $row['ban_until'] === null || $row['ban_until'] === '0000-00-00 00:00:00' || $row['ban_until'] === '') {
        return true;
    }
    $until = strtotime($row['ban_until']);
    if ($until === false) return true;
    return $until > time();
}

function sp_chat_apply_expired_bans($tbl_ban){
    // 만료된 active 밴 자동 비활성화 (가벼운 정리)
    sql_query("
        update {$tbl_ban}
        set is_active = 0,
            unbanned_at = if(unbanned_at is null, now(), unbanned_at)
        where is_active = 1
          and ban_until is not null
          and ban_until <> '0000-00-00 00:00:00'
          and ban_until <= now()
    ", false);
}

$cfg = sp_chat_get_cfg($tbl_cfg);
$freeze = (isset($cfg['cf_freeze']) && (int)$cfg['cf_freeze'] === 1) ? 1 : 0;
function sp_chat_online_key($is_member, $mb_id, $ip, $ua){
    if ($is_member && $mb_id) return 'M:'.$mb_id;
    $ua = substr((string)$ua, 0, 200);
    return 'G:'.md5($ip.'|'.$ua);
}

function sp_chat_online_ping($tbl_online, $is_member, $mb_id, $mb_nick, $ip, $ua){
    $ua = substr((string)$ua, 0, 200);
    $key = sp_chat_online_key($is_member, $mb_id, $ip, $ua);

    $sql = "
        insert into {$tbl_online}
        set visitor_key = '".sql_real_escape_string($key)."',
            is_member   = ".($is_member ? 1 : 0).",
            mb_id       = '".sql_real_escape_string((string)$mb_id)."',
            mb_nick     = '".sql_real_escape_string((string)$mb_nick)."',
            ip          = '".sql_real_escape_string((string)$ip)."',
            ua          = '".sql_real_escape_string((string)$ua)."',
            last_ping   = now()
        on duplicate key update
            is_member = values(is_member),
            mb_id     = values(mb_id),
            mb_nick   = values(mb_nick),
            ip        = values(ip),
            ua        = values(ua),
            last_ping = now()
    ";
    sql_query($sql, false);
}

function sp_chat_online_count($tbl_online, $window_sec){
    $window_sec = (int)$window_sec;
    if ($window_sec < 30) $window_sec = 300;

    // 오래된 데이터 정리(가벼운 청소)
    sql_query(" delete from {$tbl_online} where last_ping < date_sub(now(), interval ".($window_sec*3)." second) ", false);

    $row = sql_fetch(" select count(*) as c from {$tbl_online} where last_ping >= date_sub(now(), interval {$window_sec} second) ");
    return $row && isset($row['c']) ? (int)$row['c'] : 0;
}

// ---------- PING ----------
if ($act === 'ping') {
    $ua = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
    sp_chat_online_ping($tbl_online, $is_member, $my_id, $my_nick, $ip, $ua);

    $online_window = isset($cfg['cf_online_window']) ? (int)$cfg['cf_online_window'] : 300;
    if ($online_window < 30) $online_window = 300;

    $base = sp_chat_online_count($tbl_online, $online_window);
    $fake = isset($cfg['cf_online_fake_add']) ? (int)$cfg['cf_online_fake_add'] : 0;
    if ($fake < 0) $fake = 0;

    sp_chat_json(array('ok'=>1, 'online_count'=>($base + $fake)));
}

// ---------- HELLO (새 접속 시점 기준 last_id 제공: "방금 접속한 유저는 아무것도 안 보이게") ----------
if ($act === 'hello') {
    $mx = sql_fetch(" select max(cm_id) as mx from {$tbl_chat} ");
    $last_id = $mx && isset($mx['mx']) ? (int)$mx['mx'] : 0;

        $ua = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
    sp_chat_online_ping($tbl_online, $is_member, $my_id, $my_nick, $ip, $ua);

    $base = sp_chat_online_count($tbl_online, $online_window);
    $fake = isset($cfg['cf_online_fake_add']) ? (int)$cfg['cf_online_fake_add'] : 0;
    if ($fake < 0) $fake = 0;

    $online_count = $base + $fake;


    sp_chat_json(array(
        'ok' => 1,
        'last_id' => $last_id,
        'freeze' => $freeze,
        'online_count' => $online_count
    ));
}

// ---------- LIST ----------
if ($act === 'list') {
    $last_id = isset($_GET['last_id']) ? (int)$_GET['last_id'] : 0;

        $ua = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
    sp_chat_online_ping($tbl_online, $is_member, $my_id, $my_nick, $ip, $ua);

    $base = sp_chat_online_count($tbl_online, $online_window);
    $fake = isset($cfg['cf_online_fake_add']) ? (int)$cfg['cf_online_fake_add'] : 0;
    if ($fake < 0) $fake = 0;

    $online_count = $base + $fake;

    $rows = array();
    $result = sql_query("
        select cm_id, mb_id, cm_nick, cm_content, cm_icon, cm_datetime
        from {$tbl_chat}
        where cm_id > {$last_id}
        order by cm_id asc
        limit 80
    ");
    while($r = sql_fetch_array($result)){
        $rows[] = array(
            'cm_id' => (int)$r['cm_id'],
            'mb_id' => $r['mb_id'],
            'cm_nick' => $r['cm_nick'],
            'cm_content' => $r['cm_content'],
            'cm_icon' => $r['cm_icon'],
            'cm_datetime' => $r['cm_datetime']
        );
    }

    sp_chat_json(array(
        'ok' => 1,
        'freeze' => $freeze,
        'online_count' => $online_count,
        'list' => $rows
    ));
}

// ---------- SEND ----------
if ($act === 'send') {
    if (!$is_member) sp_chat_json(array('ok'=>0,'msg'=>'회원만 채팅이 가능합니다.'));
    if ($freeze) sp_chat_json(array('ok'=>0,'msg'=>'채팅이 잠금 상태입니다.'));

    sp_chat_apply_expired_bans($tbl_ban);
    if (sp_chat_is_banned($tbl_ban, $my_id)) {
        sp_chat_json(array('ok'=>0,'msg'=>'채팅이 금지된 상태입니다.'));
    }

    $content = isset($_POST['content']) ? trim($_POST['content']) : '';
    $content = preg_replace("/\r\n|\r/", "\n", $content);
    if ($content === '') sp_chat_json(array('ok'=>0,'msg'=>'내용이 없습니다.'));

    // 금칙어 차단 (줄바꿈/콤마 구분)
    $bad = isset($cfg['cf_badwords']) ? trim($cfg['cf_badwords']) : '';
    if ($bad !== '') {
        $parts = preg_split('/[\r\n,]+/', $bad);
        foreach($parts as $w){
            $w = trim($w);
            if ($w === '') continue;
            if (mb_stripos($content, $w) !== false){
                sp_chat_json(array('ok'=>0,'msg'=>'금칙어가 포함되어 전송이 차단되었습니다.'));
            }
        }
    }

        // 도배/반복 제한
    $spam_sec   = isset($cfg['cf_spam_sec']) ? (int)$cfg['cf_spam_sec'] : 2;
    $repeat_sec = isset($cfg['cf_repeat_sec']) ? (int)$cfg['cf_repeat_sec'] : 30;
    if ($spam_sec < 0) $spam_sec = 0;
    if ($repeat_sec < 0) $repeat_sec = 0;

    $last = sql_fetch("
        SELECT cm_content, cm_datetime
        FROM {$tbl_chat}
        WHERE mb_id = '".sql_real_escape_string($my_id)."'
        ORDER BY cm_id DESC
        LIMIT 1
    ");

    if ($last && !empty($last['cm_datetime'])) {
        $dt = strtotime($last['cm_datetime']);
        if ($dt && $spam_sec > 0 && (time() - $dt) < $spam_sec) {
            sp_chat_json(array('ok'=>0,'msg'=>"연속 전송 제한({$spam_sec}초)"));
        }

        if ($dt && $repeat_sec > 0 && isset($last['cm_content']) && $last['cm_content'] === $content && (time() - $dt) < $repeat_sec) {
            sp_chat_json(array('ok'=>0,'msg'=>"동일내용 반복 제한({$repeat_sec}초)"));
        }
    }


    $icon = sp_chat_get_icon_url($GLOBALS['member']);
    $sql = "
        insert into {$tbl_chat}
        set mb_id = '".sql_real_escape_string($my_id)."',
            cm_nick = '".sql_real_escape_string($my_nick)."',
            cm_content = '".sql_real_escape_string($content)."',
            cm_icon = '".sql_real_escape_string($icon)."',
            cm_datetime = now()
    ";
    sql_query($sql);

    sp_chat_json(array('ok'=>1));
}

// ----- 이하 ADMIN -----
if (!$is_admin) sp_chat_json(array('ok'=>0,'msg'=>'권한이 없습니다.'));

if ($act === 'admin_freeze') {
    $val = isset($_POST['freeze']) ? (int)$_POST['freeze'] : 0;
    $val = $val ? 1 : 0;
    sql_query(" update {$tbl_cfg} set cf_freeze = {$val}, cf_updated_at = now() where cf_id = 1 ", false);
    sp_chat_json(array('ok'=>1));
}

if ($act === 'admin_clear') {
    // 전체 채팅 비우기
    sql_query(" delete from {$tbl_chat} ", false);
    sp_chat_json(array('ok'=>1));
}

if ($act === 'admin_config_save') {
    $spam_sec     = isset($_POST['spam_sec']) ? (int)$_POST['spam_sec'] : 2;
    $repeat_sec   = isset($_POST['repeat_sec']) ? (int)$_POST['repeat_sec'] : 30;
    $report_limit = isset($_POST['report_limit']) ? (int)$_POST['report_limit'] : 10;
    $autoban_min  = isset($_POST['autoban_min']) ? (int)$_POST['autoban_min'] : 10;
    $online_fake_add = isset($_POST['online_fake_add']) ? (int)$_POST['online_fake_add'] : 0;
    if ($online_fake_add < 0) $online_fake_add = 0;

    $daily_visit_target = isset($_POST['daily_visit_target']) ? (int)$_POST['daily_visit_target'] : 0;
    if ($daily_visit_target < 0) $daily_visit_target = 0;

    $notice_text  = isset($_POST['notice_text']) ? trim($_POST['notice_text']) : '';
    $rule_text    = isset($_POST['rule_text']) ? trim($_POST['rule_text']) : '';
    $badwords     = isset($_POST['badwords']) ? trim($_POST['badwords']) : '';
    $left_login_notice = isset($_POST['left_login_notice']) ? trim($_POST['left_login_notice']) : '';
    $left_login_ticker_speed = isset($_POST['left_login_ticker_speed']) ? (int)$_POST['left_login_ticker_speed'] : 30;
    if ($left_login_ticker_speed < 10) $left_login_ticker_speed = 10;
    if ($left_login_ticker_speed > 45) $left_login_ticker_speed = 45;

    // 매일 방문자수(목표) 컬럼 없으면 추가 (하위 호환)
    $col_check = sql_fetch(" SHOW COLUMNS FROM {$tbl_cfg} LIKE 'cf_daily_visit_target' ", false);
    if (!$col_check) {
        sql_query(" ALTER TABLE {$tbl_cfg} ADD COLUMN cf_daily_visit_target INT UNSIGNED NOT NULL DEFAULT 0 ", false);
    }
    // 좌측 로그인박스 하단 공지글 컬럼 없으면 추가 (하위 호환)
    $col_left = sql_fetch(" SHOW COLUMNS FROM {$tbl_cfg} LIKE 'cf_left_login_notice' ", false);
    if (!$col_left) {
        sql_query(" ALTER TABLE {$tbl_cfg} ADD COLUMN cf_left_login_notice TEXT ", false);
    }
    $col_speed = sql_fetch(" SHOW COLUMNS FROM {$tbl_cfg} LIKE 'cf_left_login_ticker_speed' ", false);
    if (!$col_speed) {
        sql_query(" ALTER TABLE {$tbl_cfg} ADD COLUMN cf_left_login_ticker_speed INT UNSIGNED NOT NULL DEFAULT 30 ", false);
    }

    sql_query("
        update {$tbl_cfg}
        set cf_spam_sec = {$spam_sec},
            cf_repeat_sec = {$repeat_sec},
            cf_report_limit = {$report_limit},
            cf_autoban_min = {$autoban_min},
            cf_notice_text = '".sql_real_escape_string($notice_text)."',
            cf_rule_text   = '".sql_real_escape_string($rule_text)."',
            cf_badwords    = '".sql_real_escape_string($badwords)."',
            cf_online_fake_add = {$online_fake_add},
            cf_daily_visit_target = {$daily_visit_target},
            cf_left_login_notice = '".sql_real_escape_string($left_login_notice)."',
            cf_left_login_ticker_speed = {$left_login_ticker_speed},
            cf_updated_at  = now()
        where cf_id = 1
    ", false);

    sp_chat_json(array('ok'=>1));
}

// (레거시) mb_id 밴
if ($act === 'admin_ban') {
    $mb_id = isset($_POST['mb_id']) ? trim($_POST['mb_id']) : '';
    $min   = isset($_POST['min']) ? (int)$_POST['min'] : 10;
    if ($mb_id === '') sp_chat_json(array('ok'=>0,'msg'=>'mb_id가 필요합니다.'));

    $m = sql_fetch(" select mb_id, mb_nick from {$g5['member_table']} where mb_id='".sql_real_escape_string($mb_id)."' limit 1 ");
    if (!$m) sp_chat_json(array('ok'=>0,'msg'=>'해당 회원을 찾지 못했습니다.'));

    $ban_until = null;
    if ($min > 0) {
        $ban_until = date('Y-m-d H:i:s', time() + ($min*60));
    }

    $sql = "
        insert into {$tbl_ban}
        set mb_id='".sql_real_escape_string($m['mb_id'])."',
            mb_nick='".sql_real_escape_string($m['mb_nick'])."',
            is_active=1,
            banned_at=now(),
            duration_min={$min},
            ban_until ".($ban_until ? "='".sql_real_escape_string($ban_until)."'" : "=NULL").",
            reason='',
            banned_by='".sql_real_escape_string($my_id)."',
            ip_at_ban='".sql_real_escape_string($ip)."',
            created_at=now(),
            updated_at=now()
        on duplicate key update
            mb_nick=values(mb_nick),
            is_active=1,
            banned_at=now(),
            duration_min=values(duration_min),
            ban_until=values(ban_until),
            banned_by=values(banned_by),
            ip_at_ban=values(ip_at_ban),
            updated_at=now()
    ";
    sql_query($sql, false);

    sp_chat_json(array('ok'=>1));
}

// (신규) 닉네임 밴 + 사유
if ($act === 'admin_ban_nick') {
    $nick = isset($_POST['nick']) ? trim($_POST['nick']) : '';
    $min  = isset($_POST['min']) ? (int)$_POST['min'] : 10;
    $reason = isset($_POST['reason']) ? trim($_POST['reason']) : '';

    if ($nick === '') sp_chat_json(array('ok'=>0,'msg'=>'닉네임을 입력하세요.'));

    $m = sql_fetch(" select mb_id, mb_nick from {$g5['member_table']} where mb_nick='".sql_real_escape_string($nick)."' limit 1 ");
    if (!$m) sp_chat_json(array('ok'=>0,'msg'=>'해당 닉네임 회원을 찾지 못했습니다.'));

    $ban_until = null;
    if ($min > 0) $ban_until = date('Y-m-d H:i:s', time() + ($min*60));

    $sql = "
        insert into {$tbl_ban}
        set mb_id='".sql_real_escape_string($m['mb_id'])."',
            mb_nick='".sql_real_escape_string($m['mb_nick'])."',
            is_active=1,
            banned_at=now(),
            duration_min={$min},
            ban_until ".($ban_until ? "='".sql_real_escape_string($ban_until)."'" : "=NULL").",
            reason='".sql_real_escape_string($reason)."',
            banned_by='".sql_real_escape_string($my_id)."',
            ip_at_ban='".sql_real_escape_string($ip)."',
            created_at=now(),
            updated_at=now()
        on duplicate key update
            mb_nick=values(mb_nick),
            is_active=1,
            banned_at=now(),
            duration_min=values(duration_min),
            ban_until=values(ban_until),
            reason=values(reason),
            banned_by=values(banned_by),
            ip_at_ban=values(ip_at_ban),
            updated_at=now()
    ";
    sql_query($sql, false);

    sp_chat_json(array('ok'=>1));
}

// unban (mb_id 또는 nick)
if ($act === 'admin_unban') {
    $mb_id = isset($_POST['mb_id']) ? trim($_POST['mb_id']) : '';
    $nick  = isset($_POST['nick']) ? trim($_POST['nick']) : '';

    if ($mb_id === '' && $nick === '') sp_chat_json(array('ok'=>0,'msg'=>'해제할 대상이 없습니다.'));

    if ($mb_id === '' && $nick !== '') {
        $m = sql_fetch(" select mb_id from {$g5['member_table']} where mb_nick='".sql_real_escape_string($nick)."' limit 1 ");
        if (!$m) sp_chat_json(array('ok'=>0,'msg'=>'해당 닉네임 회원을 찾지 못했습니다.'));
        $mb_id = $m['mb_id'];
    }

    sql_query("
        update {$tbl_ban}
        set is_active = 0,
            unbanned_by = '".sql_real_escape_string($my_id)."',
            unbanned_at = now(),
            updated_at = now()
        where mb_id = '".sql_real_escape_string($mb_id)."'
    ", false);

    sp_chat_json(array('ok'=>1));
}

// banlist 조회 (필터/검색)
if ($act === 'banlist') {
    sp_chat_apply_expired_bans($tbl_ban);

    $status = isset($_GET['status']) ? trim($_GET['status']) : 'active'; // active|expired|all
    $q = isset($_GET['q']) ? trim($_GET['q']) : '';

    $where = " where 1 ";
    if ($status === 'active') $where .= " and is_active = 1 ";
    else if ($status === 'expired') $where .= " and is_active = 0 ";

    if ($q !== '') {
        $qq = sql_real_escape_string($q);
        $where .= " and (mb_nick like '%{$qq}%' or mb_id like '%{$qq}%') ";
    }

    $list = array();
    $rs = sql_query(" select * from {$tbl_ban} {$where} order by banned_at desc limit 200 ");
    while($r = sql_fetch_array($rs)){
        $list[] = array(
            'mb_nick' => $r['mb_nick'],
            'mb_id' => $r['mb_id'],
            'is_active' => (int)$r['is_active'],
            'banned_at' => $r['banned_at'],
            'duration_min' => (int)$r['duration_min'],
            'ban_until' => $r['ban_until'],
            'reason' => $r['reason'],
            'banned_by' => $r['banned_by'],
            'unbanned_by' => $r['unbanned_by'],
            'unbanned_at' => $r['unbanned_at'],
            'ip_at_ban' => $r['ip_at_ban'],
            'report_count' => (int)$r['report_count']
        );
    }

    sp_chat_json(array('ok'=>1,'list'=>$list));
}

// =========================================================
// ✅ 신고 저장 (닉네임 클릭 메뉴 → 신고하기)
//  - chat_box.php 에서 chat_ajax.php?act=report 로 POST
//  - g5_chat_report 컬럼에 맞춰 INSERT
// =========================================================
if ($act === 'report') {
    // 신고 접수 (채팅창에서 신고하기)
    // 호환 파라미터: target_id/target_nick, mb_id/mbid, nick/reported_nick 등
    $target_id = trim($_POST['target_id'] ?? $_POST['mb_id'] ?? $_POST['mbid'] ?? $_POST['target'] ?? '');
    $reported_nick = trim($_POST['reported_nick'] ?? $_POST['target_nick'] ?? $_POST['nick'] ?? $_POST['targetNick'] ?? $_POST['target_nick'] ?? '');
    $reason = trim($_POST['reason'] ?? $_POST['report_reason'] ?? '');

    // reporter: 로그인 회원 기준
    $reporter_id = isset($member['mb_id']) ? trim($member['mb_id']) : '';
    $reporter_nick = isset($member['mb_nick']) ? trim($member['mb_nick']) : '';

    // 방어: 최소한 닉네임은 있어야 기록
    if ($reported_nick === '') {
        sp_chat_json(array('ok' => 0, 'msg' => '신고 대상이 없습니다.'));
    }
    if ($reason === '') {
        sp_chat_json(array('ok' => 0, 'msg' => '신고 사유를 선택해주세요.'));
    }

    // 같은 사람이 같은 대상/사유로 너무 연속 신고 방지(10초)
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    $now = date('Y-m-d H:i:s');

    // 테이블 존재/컬럼을 신뢰(스키마는 g5_chat_report: reported_nick/reporter_nick/reporter_id/target_id/cm_id/reason/report_ip/ip/created_at)
    $tbl = 'g5_chat_report';

    $dup_sql = "SELECT id FROM {$tbl}
                WHERE reporter_id = '" . sql_real_escape_string($reporter_id) . "'
                  AND reported_nick = '" . sql_real_escape_string($reported_nick) . "'
                  AND reason = '" . sql_real_escape_string($reason) . "'
                  AND created_at >= DATE_SUB(NOW(), INTERVAL 10 SECOND)
                ORDER BY id DESC LIMIT 1";
    $dup = sql_fetch($dup_sql);
    if (isset($dup['id']) && $dup['id']) {
        sp_chat_json(array('ok' => 1, 'msg' => '신고가 접수되었습니다.'));
    }

    $cm_id = intval($_POST['cm_id'] ?? 0);

    $sql = "INSERT INTO {$tbl}
            SET reported_nick = '" . sql_real_escape_string($reported_nick) . "',
                reporter_nick = '" . sql_real_escape_string($reporter_nick) . "',
                reporter_id   = '" . sql_real_escape_string($reporter_id) . "',
                target_id     = '" . sql_real_escape_string($target_id) . "',
                cm_id         = {$cm_id},
                reason        = '" . sql_real_escape_string($reason) . "',
                report_ip     = '" . sql_real_escape_string($ip) . "',
                ip            = '" . sql_real_escape_string($ip) . "',
                created_at    = '" . sql_real_escape_string($now) . "'";

    $res = sql_query($sql, false);
    if (!$res) {
        sp_chat_json(array('ok' => 0, 'msg' => 'DB 저장 실패(신고).'));
    }

        // ✅ 누적신고 자동 밴 (10/20/30/31 도달 순간 1회)
    if ($target_id !== '') {
        $target_id_esc = sql_real_escape_string($target_id);

        // 누적 신고 수(채팅 신고 테이블 기준)
        $cnt_row = sql_fetch(" select count(*) as cnt from g5_chat_report where target_id = '{$target_id_esc}' ");
        $report_cnt = (int)($cnt_row['cnt'] ?? 0);

        $ban_min = 0;
        $do_ban = false;
        $is_perm = false;

        if ($report_cnt === 10) { $ban_min = 10;  $do_ban = true; }
        else if ($report_cnt === 20) { $ban_min = 60;  $do_ban = true; }
        else if ($report_cnt === 30) { $ban_min = 600; $do_ban = true; }
        else if ($report_cnt >= 31)  { $ban_min = 0;   $do_ban = true; $is_perm = true; }

        if ($do_ban) {
            // 대상 회원 정보(닉네임)
            $m = sql_fetch(" select mb_id, mb_nick from {$g5['member_table']} where mb_id = '{$target_id_esc}' limit 1 ");
            if ($m && $m['mb_id']) {

                // 이미 활성 밴이 있으면(특히 영구/더 긴 밴) 중복 적용 방지
                $cur = sql_fetch(" select duration_min, ban_until from {$tbl_ban} where mb_id = '{$target_id_esc}' and is_active = 1 order by banned_at desc limit 1 ");
                $cur_is_perm = false;
                $cur_min = -1;
                if ($cur) {
                    $cur_min = (int)($cur['duration_min'] ?? 0);
                    if (!isset($cur['ban_until']) || $cur['ban_until'] === null || $cur['ban_until'] === '' || $cur['ban_until'] === '0000-00-00 00:00:00') {
                        $cur_is_perm = true;
                    }
                }

                $need_apply = true;
                if ($is_perm) {
                    if ($cur_is_perm) $need_apply = false; // 이미 영구
                } else {
                    if ($cur_is_perm) $need_apply = false; // 이미 영구가 더 강함
                    else if ($cur_min >= $ban_min) $need_apply = false; // 이미 더 길거나 동일
                }

                if ($need_apply) {
                    $ban_until = null;
                    if (!$is_perm && $ban_min > 0) {
                        $ban_until = date('Y-m-d H:i:s', time() + ($ban_min * 60));
                    }

                    $reason_auto = "누적신고 {$report_cnt}회 자동밴";

                    $sql_ban = "
                        insert into {$tbl_ban}
                        set mb_id='".sql_real_escape_string($m['mb_id'])."',
                            mb_nick='".sql_real_escape_string($m['mb_nick'])."',
                            is_active=1,
                            banned_at=now(),
                            duration_min={$ban_min},
                            ban_until ".($ban_until ? "='".sql_real_escape_string($ban_until)."'" : "=NULL").",
                            reason='".sql_real_escape_string($reason_auto)."',
                            banned_by='AUTO_REPORT',
                            ip_at_ban='".sql_real_escape_string($ip)."',
                            created_at=now(),
                            updated_at=now()
                        on duplicate key update
                            mb_nick=values(mb_nick),
                            is_active=1,
                            banned_at=now(),
                            duration_min=values(duration_min),
                            ban_until=values(ban_until),
                            reason=values(reason),
                            banned_by=values(banned_by),
                            ip_at_ban=values(ip_at_ban),
                            updated_at=now()
                    ";
                    sql_query($sql_ban, false);
                }
            }
        }
    }

    // 신고 뱃지 조건 체크 (신고자 기준)
    if ($reporter_id !== '') {
        $badge_inc = defined('G5_THEME_PATH') ? (G5_THEME_PATH . '/inc/badge.php') : (defined('G5_PATH') ? G5_PATH . '/theme/scorepoint/inc/badge.php' : '');
        if ($badge_inc !== '' && is_file($badge_inc)) {
            include_once $badge_inc;
            if (function_exists('sp_badge_count_reports') && function_exists('sp_badge_check_and_grant')) {
                $report_count = sp_badge_count_reports($reporter_id);
                sp_badge_check_and_grant($reporter_id, 'report', array('report_count' => $report_count));
            }
        }
    }

    sp_chat_json(array('ok' => 1, 'msg' => '신고가 접수되었습니다.'));

}

sp_chat_json(array('ok'=>0,'msg'=>'unknown act'));
