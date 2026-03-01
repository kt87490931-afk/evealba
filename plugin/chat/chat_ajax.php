<?php
// /plugin/chat/chat_ajax.php — 이브알바 채팅 백엔드
@error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING & ~E_DEPRECATED);
@ini_set('display_errors', '0');

$_chat_ajax_dir = __DIR__;
$_common_path = $_chat_ajax_dir.'/../../common.php';
if (!is_file($_common_path)) {
    $_common_path = $_SERVER['DOCUMENT_ROOT'].'/common.php';
}
if (!is_file($_common_path)) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(array('ok'=>0,'msg'=>'common.php not found','path'=>$_common_path));
    exit;
}
ob_start();
include_once($_common_path);
include_once(G5_PLUGIN_PATH.'/chat/_common.php');
ob_end_clean();

@set_time_limit(20);
header('Content-Type: application/json; charset=utf-8');

// 테이블 자동 생성 (첫 실행 시)
$_chk = @sql_fetch("SELECT 1 FROM {$g5['chat_config_table']} LIMIT 1");
if (!$_chk) {
    @sql_query("CREATE TABLE IF NOT EXISTS `{$g5['chat_msg_table']}` (
      `cm_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
      `mb_id` VARCHAR(20) NOT NULL DEFAULT '',
      `cm_nick` VARCHAR(50) NOT NULL DEFAULT '',
      `cm_icon` VARCHAR(255) NOT NULL DEFAULT '',
      `cm_content` TEXT NOT NULL,
      `cm_region` VARCHAR(10) NOT NULL DEFAULT '전체',
      `cm_datetime` DATETIME NOT NULL,
      PRIMARY KEY (`cm_id`), KEY `mb_id` (`mb_id`), KEY `cm_datetime` (`cm_datetime`), KEY `idx_region` (`cm_region`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4", false);
    @sql_query("CREATE TABLE IF NOT EXISTS `{$g5['chat_config_table']}` (
      `cf_id` TINYINT NOT NULL DEFAULT 1, `cf_title` VARCHAR(50) NOT NULL DEFAULT '실시간 채팅',
      `cf_freeze` TINYINT NOT NULL DEFAULT 0, `cf_spam_sec` INT NOT NULL DEFAULT 2,
      `cf_repeat_sec` INT NOT NULL DEFAULT 30, `cf_report_limit` INT NOT NULL DEFAULT 10,
      `cf_autoban_min` INT NOT NULL DEFAULT 10, `cf_notice_text` TEXT, `cf_rule_text` TEXT,
      `cf_badwords` TEXT, `cf_online_window` INT NOT NULL DEFAULT 300,
      `cf_online_fake_add` INT NOT NULL DEFAULT 0, `cf_updated_at` DATETIME DEFAULT NULL,
      PRIMARY KEY (`cf_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4", false);
    @sql_query("INSERT INTO `{$g5['chat_config_table']}` (`cf_id`) VALUES (1) ON DUPLICATE KEY UPDATE `cf_id`=`cf_id`", false);
    @sql_query("CREATE TABLE IF NOT EXISTS `{$g5['chat_ban_table']}` (
      `mb_id` VARCHAR(20) NOT NULL, `mb_nick` VARCHAR(50) NOT NULL DEFAULT '',
      `is_active` TINYINT NOT NULL DEFAULT 1, `banned_at` DATETIME DEFAULT NULL,
      `duration_min` INT NOT NULL DEFAULT 0, `ban_until` DATETIME DEFAULT NULL,
      `reason` VARCHAR(255) NOT NULL DEFAULT '', `banned_by` VARCHAR(20) NOT NULL DEFAULT '',
      `unbanned_by` VARCHAR(20) NOT NULL DEFAULT '', `unbanned_at` DATETIME DEFAULT NULL,
      `ip_at_ban` VARCHAR(45) NOT NULL DEFAULT '', `report_count` INT NOT NULL DEFAULT 0,
      `created_at` DATETIME DEFAULT NULL, `updated_at` DATETIME DEFAULT NULL,
      PRIMARY KEY (`mb_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4", false);
    @sql_query("CREATE TABLE IF NOT EXISTS `{$g5['chat_online_table']}` (
      `visitor_key` VARCHAR(60) NOT NULL, `is_member` TINYINT NOT NULL DEFAULT 0,
      `mb_id` VARCHAR(20) NOT NULL DEFAULT '', `mb_nick` VARCHAR(50) NOT NULL DEFAULT '',
      `ip` VARCHAR(45) NOT NULL DEFAULT '', `ua` VARCHAR(200) NOT NULL DEFAULT '',
      `co_region` VARCHAR(10) NOT NULL DEFAULT '전체', `last_ping` DATETIME NOT NULL,
      PRIMARY KEY (`visitor_key`), KEY `idx_ping` (`last_ping`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4", false);
    @sql_query("CREATE TABLE IF NOT EXISTS `{$g5['chat_report_table']}` (
      `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
      `reported_nick` VARCHAR(50) NOT NULL DEFAULT '', `reporter_nick` VARCHAR(50) NOT NULL DEFAULT '',
      `reporter_id` VARCHAR(20) NOT NULL DEFAULT '', `target_id` VARCHAR(20) NOT NULL DEFAULT '',
      `cm_id` INT UNSIGNED NOT NULL DEFAULT 0, `reason` VARCHAR(255) NOT NULL DEFAULT '',
      `report_ip` VARCHAR(45) NOT NULL DEFAULT '', `ip` VARCHAR(45) NOT NULL DEFAULT '',
      `created_at` DATETIME DEFAULT NULL,
      PRIMARY KEY (`id`), KEY `idx_target` (`target_id`), KEY `idx_reporter` (`reporter_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4", false);
}
// cm_region 컬럼 누락 시 자동 추가
$_col_chk = @sql_fetch("SHOW COLUMNS FROM `{$g5['chat_msg_table']}` LIKE 'cm_region'");
if (!$_col_chk) {
    @sql_query("ALTER TABLE `{$g5['chat_msg_table']}` ADD COLUMN `cm_region` VARCHAR(10) NOT NULL DEFAULT '전체' AFTER `cm_content`", false);
    @sql_query("ALTER TABLE `{$g5['chat_msg_table']}` ADD KEY `idx_region` (`cm_region`)", false);
}
// chat_config 누락 컬럼 자동 추가 (전체)
$_cfg_cols = array(
    'cf_title'          => "VARCHAR(50) NOT NULL DEFAULT '실시간 채팅'",
    'cf_freeze'         => "TINYINT NOT NULL DEFAULT 0",
    'cf_spam_sec'       => "INT NOT NULL DEFAULT 2",
    'cf_repeat_sec'     => "INT NOT NULL DEFAULT 30",
    'cf_report_limit'   => "INT NOT NULL DEFAULT 10",
    'cf_autoban_min'    => "INT NOT NULL DEFAULT 10",
    'cf_notice_text'    => "TEXT",
    'cf_rule_text'      => "TEXT",
    'cf_badwords'       => "TEXT",
    'cf_online_window'  => "INT NOT NULL DEFAULT 300",
    'cf_online_fake_add'=> "INT NOT NULL DEFAULT 0",
    'cf_updated_at'     => "DATETIME DEFAULT NULL",
    'cf_daily_visit_target' => "INT NOT NULL DEFAULT 0",
    'cf_left_login_notice'  => "TEXT",
    'cf_left_login_ticker_speed' => "INT NOT NULL DEFAULT 30"
);
foreach ($_cfg_cols as $_cname => $_cdef) {
    $_cc = @sql_fetch("SHOW COLUMNS FROM `{$g5['chat_config_table']}` LIKE '{$_cname}'");
    if (!$_cc) {
        @sql_query("ALTER TABLE `{$g5['chat_config_table']}` ADD COLUMN `{$_cname}` {$_cdef}", false);
    }
}
// chat_ban에 id 컬럼 없으면 추가 (mb_id는 UNIQUE로 유지 → ON DUPLICATE KEY UPDATE 동작 보장)
$_ban_id = @sql_fetch("SHOW COLUMNS FROM `{$g5['chat_ban_table']}` LIKE 'id'");
if (!$_ban_id) {
    @sql_query("ALTER TABLE `{$g5['chat_ban_table']}` ADD COLUMN `id` INT UNSIGNED NOT NULL AUTO_INCREMENT FIRST, DROP PRIMARY KEY, ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `uk_mb_id` (`mb_id`)", false);
}
// chat_report에 target_nick 컬럼 없으면 추가
$_rpt_tn = @sql_fetch("SHOW COLUMNS FROM `{$g5['chat_report_table']}` LIKE 'target_nick'");
if (!$_rpt_tn) {
    @sql_query("ALTER TABLE `{$g5['chat_report_table']}` ADD COLUMN `target_nick` VARCHAR(50) NOT NULL DEFAULT '' AFTER `reported_nick`", false);
}

function eve_chat_json($arr){
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

$tbl_chat   = $g5['chat_msg_table'];
$tbl_cfg    = $g5['chat_config_table'];
$tbl_ban    = $g5['chat_ban_table'];
$tbl_online = $g5['chat_online_table'];
$tbl_report = $g5['chat_report_table'];

function eve_chat_get_cfg($tbl_cfg){
    $cfg = sql_fetch(" SELECT * FROM {$tbl_cfg} LIMIT 1 ");
    if(!$cfg){
        sql_query(" INSERT INTO {$tbl_cfg} SET cf_id=1 ", false);
        $cfg = sql_fetch(" SELECT * FROM {$tbl_cfg} LIMIT 1 ");
    }
    return $cfg ? $cfg : array();
}

$cfg = eve_chat_get_cfg($tbl_cfg);
$online_window = isset($cfg['cf_online_window']) ? (int)$cfg['cf_online_window'] : 300;
if ($online_window < 30) $online_window = 300;
$freeze = (isset($cfg['cf_freeze']) && (int)$cfg['cf_freeze'] === 1) ? 1 : 0;

// 이브알바 채팅 권한: 일반회원(여성) + 관리자
function eve_chat_can_chat($member){
    if (!$member || !isset($member['mb_id']) || !$member['mb_id']) return false;
    if (isset($GLOBALS['is_admin']) && $GLOBALS['is_admin']) return true;
    $type = isset($member['mb_1']) ? $member['mb_1'] : '';
    $sex  = isset($member['mb_sex']) ? $member['mb_sex'] : '';
    return ($type === 'normal' && $sex === 'F');
}

function eve_chat_is_banned($tbl_ban, $mb_id){
    if (!$mb_id) return false;
    $row = sql_fetch("
        SELECT * FROM {$tbl_ban}
        WHERE mb_id = '".sql_real_escape_string($mb_id)."'
          AND is_active = 1
        ORDER BY banned_at DESC LIMIT 1
    ");
    if (!$row) return false;
    if (!isset($row['ban_until']) || $row['ban_until'] === null || $row['ban_until'] === '0000-00-00 00:00:00' || $row['ban_until'] === '') {
        return true;
    }
    $until = strtotime($row['ban_until']);
    if ($until === false) return true;
    return $until > time();
}

function eve_chat_apply_expired_bans($tbl_ban){
    sql_query("
        UPDATE {$tbl_ban}
        SET is_active = 0,
            unbanned_at = IF(unbanned_at IS NULL, NOW(), unbanned_at)
        WHERE is_active = 1
          AND ban_until IS NOT NULL
          AND ban_until <> '0000-00-00 00:00:00'
          AND ban_until <= NOW()
    ", false);
}

function eve_chat_online_key($is_member, $mb_id, $ip, $ua){
    if ($is_member && $mb_id) return 'M:'.$mb_id;
    $ua = substr((string)$ua, 0, 200);
    return 'G:'.md5($ip.'|'.$ua);
}

function eve_chat_online_ping($tbl_online, $is_member, $mb_id, $mb_nick, $ip, $ua, $region){
    $ua = substr((string)$ua, 0, 200);
    $key = eve_chat_online_key($is_member, $mb_id, $ip, $ua);
    $sql = "
        INSERT INTO {$tbl_online}
        SET visitor_key = '".sql_real_escape_string($key)."',
            is_member   = ".($is_member ? 1 : 0).",
            mb_id       = '".sql_real_escape_string((string)$mb_id)."',
            mb_nick     = '".sql_real_escape_string((string)$mb_nick)."',
            ip          = '".sql_real_escape_string((string)$ip)."',
            ua          = '".sql_real_escape_string((string)$ua)."',
            co_region   = '".sql_real_escape_string((string)$region)."',
            last_ping   = NOW()
        ON DUPLICATE KEY UPDATE
            is_member = VALUES(is_member),
            mb_id     = VALUES(mb_id),
            mb_nick   = VALUES(mb_nick),
            ip        = VALUES(ip),
            ua        = VALUES(ua),
            co_region = VALUES(co_region),
            last_ping = NOW()
    ";
    sql_query($sql, false);
}

function eve_chat_online_count($tbl_online, $window_sec, $region = ''){
    $window_sec = (int)$window_sec;
    if ($window_sec < 30) $window_sec = 300;
    sql_query(" DELETE FROM {$tbl_online} WHERE last_ping < DATE_SUB(NOW(), INTERVAL ".($window_sec*3)." SECOND) ", false);

    $where = " last_ping >= DATE_SUB(NOW(), INTERVAL {$window_sec} SECOND) ";
    if ($region !== '' && $region !== '전체') {
        $where .= " AND co_region = '".sql_real_escape_string($region)."' ";
    }
    $row = sql_fetch(" SELECT COUNT(*) AS c FROM {$tbl_online} WHERE {$where} ");
    return $row && isset($row['c']) ? (int)$row['c'] : 0;
}

$req_region = isset($_REQUEST['region']) ? trim($_REQUEST['region']) : '전체';
if ($req_region === '') $req_region = '전체';

// ---------- PING ----------
if ($act === 'ping') {
    $ua = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
    eve_chat_online_ping($tbl_online, $is_member, $my_id, $my_nick, $ip, $ua, $req_region);

    $base = eve_chat_online_count($tbl_online, $online_window);
    $fake = isset($cfg['cf_online_fake_add']) ? (int)$cfg['cf_online_fake_add'] : 0;
    if ($fake < 0) $fake = 0;

    eve_chat_json(array('ok'=>1, 'online_count'=>($base + $fake)));
}

// ---------- HELLO ----------
if ($act === 'hello') {
    $mx = sql_fetch(" SELECT MAX(cm_id) AS mx FROM {$tbl_chat} ");
    $last_id = $mx && isset($mx['mx']) ? (int)$mx['mx'] : 0;

    $ua = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
    eve_chat_online_ping($tbl_online, $is_member, $my_id, $my_nick, $ip, $ua, $req_region);

    $base = eve_chat_online_count($tbl_online, $online_window);
    $fake = isset($cfg['cf_online_fake_add']) ? (int)$cfg['cf_online_fake_add'] : 0;
    if ($fake < 0) $fake = 0;

    $notice_text = isset($cfg['cf_notice_text']) ? trim($cfg['cf_notice_text']) : '';

    eve_chat_json(array(
        'ok' => 1,
        'last_id' => $last_id,
        'freeze' => $freeze,
        'online_count' => ($base + $fake),
        'can_chat' => ($is_member && eve_chat_can_chat($member)) ? 1 : 0,
        'notice_text' => $notice_text
    ));
}

// ---------- LIST ----------
if ($act === 'list') {
    $last_id = isset($_GET['last_id']) ? (int)$_GET['last_id'] : 0;

    $ua = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
    eve_chat_online_ping($tbl_online, $is_member, $my_id, $my_nick, $ip, $ua, $req_region);

    $base = eve_chat_online_count($tbl_online, $online_window);
    $fake = isset($cfg['cf_online_fake_add']) ? (int)$cfg['cf_online_fake_add'] : 0;
    if ($fake < 0) $fake = 0;

    $region_where = '';
    if ($req_region !== '' && $req_region !== '전체') {
        $region_where = " AND cm_region = '".sql_real_escape_string($req_region)."' ";
    }

    $rows = array();
    $result = sql_query("
        SELECT cm_id, mb_id, cm_nick, cm_content, cm_region, cm_datetime
        FROM {$tbl_chat}
        WHERE cm_id > {$last_id} {$region_where}
        ORDER BY cm_id ASC
        LIMIT 80
    ");
    while($r = sql_fetch_array($result)){
        $rows[] = array(
            'cm_id' => (int)$r['cm_id'],
            'mb_id' => $r['mb_id'],
            'cm_nick' => $r['cm_nick'],
            'cm_content' => $r['cm_content'],
            'cm_region' => $r['cm_region'],
            'cm_datetime' => $r['cm_datetime']
        );
    }

    eve_chat_json(array(
        'ok' => 1,
        'freeze' => $freeze,
        'online_count' => ($base + $fake),
        'list' => $rows
    ));
}

// ---------- SEND ----------
if ($act === 'send') {
    if (!$is_member) eve_chat_json(array('ok'=>0,'msg'=>'로그인 후 이용해 주세요.'));
    if (!eve_chat_can_chat($member)) eve_chat_json(array('ok'=>0,'msg'=>'일반회원(여성)만 채팅이 가능합니다.'));
    if ($freeze) eve_chat_json(array('ok'=>0,'msg'=>'채팅이 잠금 상태입니다.'));

    eve_chat_apply_expired_bans($tbl_ban);
    if (eve_chat_is_banned($tbl_ban, $my_id)) {
        eve_chat_json(array('ok'=>0,'msg'=>'채팅이 금지된 상태입니다.'));
    }

    $content = isset($_POST['content']) ? trim($_POST['content']) : '';
    $content = preg_replace("/\r\n|\r/", "\n", $content);
    if ($content === '') eve_chat_json(array('ok'=>0,'msg'=>'내용이 없습니다.'));

    $send_region = isset($_POST['region']) ? trim($_POST['region']) : '전체';
    if ($send_region === '') $send_region = '전체';

    $bad = isset($cfg['cf_badwords']) ? trim($cfg['cf_badwords']) : '';
    if ($bad !== '') {
        $parts = preg_split('/[\r\n,]+/', $bad);
        foreach($parts as $w){
            $w = trim($w);
            if ($w === '') continue;
            if (mb_stripos($content, $w) !== false){
                eve_chat_json(array('ok'=>0,'msg'=>'금칙어가 포함되어 전송이 차단되었습니다.'));
            }
        }
    }

    $spam_sec   = isset($cfg['cf_spam_sec']) ? (int)$cfg['cf_spam_sec'] : 2;
    $repeat_sec = isset($cfg['cf_repeat_sec']) ? (int)$cfg['cf_repeat_sec'] : 30;
    if ($spam_sec < 0) $spam_sec = 0;
    if ($repeat_sec < 0) $repeat_sec = 0;

    $last = sql_fetch("
        SELECT cm_content, cm_datetime FROM {$tbl_chat}
        WHERE mb_id = '".sql_real_escape_string($my_id)."'
        ORDER BY cm_id DESC LIMIT 1
    ");

    if ($last && !empty($last['cm_datetime'])) {
        $dt = strtotime($last['cm_datetime']);
        if ($dt && $spam_sec > 0 && (time() - $dt) < $spam_sec) {
            eve_chat_json(array('ok'=>0,'msg'=>"연속 전송 제한({$spam_sec}초)"));
        }
        if ($dt && $repeat_sec > 0 && isset($last['cm_content']) && $last['cm_content'] === $content && (time() - $dt) < $repeat_sec) {
            eve_chat_json(array('ok'=>0,'msg'=>"동일내용 반복 제한({$repeat_sec}초)"));
        }
    }

    $sql = "
        INSERT INTO {$tbl_chat}
        SET mb_id = '".sql_real_escape_string($my_id)."',
            cm_nick = '".sql_real_escape_string($my_nick)."',
            cm_content = '".sql_real_escape_string($content)."',
            cm_icon = '👩',
            cm_region = '".sql_real_escape_string($send_region)."',
            cm_datetime = NOW()
    ";
    $send_ok = @sql_query($sql, false);
    if (!$send_ok) {
        eve_chat_json(array('ok'=>0,'msg'=>'DB 저장 실패: '.@mysqli_error($connect_db)));
    }

    eve_chat_json(array('ok'=>1));
}

// ---------- REPORT ----------
if ($act === 'report') {
    if (!$is_member) eve_chat_json(array('ok'=>0,'msg'=>'로그인 후 이용해 주세요.'));

    $target_id = isset($_POST['target_id']) ? trim($_POST['target_id']) : '';
    $reported_nick = isset($_POST['target_nick']) ? trim($_POST['target_nick']) : '';
    $reason = isset($_POST['reason']) ? trim($_POST['reason']) : '';
    $reporter_id = $my_id;
    $reporter_nick = $my_nick;

    if ($reported_nick === '') eve_chat_json(array('ok'=>0,'msg'=>'신고 대상이 없습니다.'));
    if ($reason === '') eve_chat_json(array('ok'=>0,'msg'=>'신고 사유를 선택해주세요.'));

    $now = date('Y-m-d H:i:s');

    $dup = sql_fetch("
        SELECT id FROM {$tbl_report}
        WHERE reporter_id = '".sql_real_escape_string($reporter_id)."'
          AND reported_nick = '".sql_real_escape_string($reported_nick)."'
          AND reason = '".sql_real_escape_string($reason)."'
          AND created_at >= DATE_SUB(NOW(), INTERVAL 10 SECOND)
        LIMIT 1
    ");
    if (isset($dup['id']) && $dup['id']) {
        eve_chat_json(array('ok'=>1,'msg'=>'신고가 접수되었습니다.'));
    }

    $cm_id = isset($_POST['cm_id']) ? (int)$_POST['cm_id'] : 0;

    sql_query("
        INSERT INTO {$tbl_report}
        SET reported_nick = '".sql_real_escape_string($reported_nick)."',
            reporter_nick = '".sql_real_escape_string($reporter_nick)."',
            reporter_id   = '".sql_real_escape_string($reporter_id)."',
            target_id     = '".sql_real_escape_string($target_id)."',
            cm_id         = {$cm_id},
            reason        = '".sql_real_escape_string($reason)."',
            report_ip     = '".sql_real_escape_string($ip)."',
            ip            = '".sql_real_escape_string($ip)."',
            created_at    = '".sql_real_escape_string($now)."'
    ", false);

    // 누적신고 자동 밴
    if ($target_id !== '') {
        $cnt_row = sql_fetch(" SELECT COUNT(*) AS cnt FROM {$tbl_report} WHERE target_id = '".sql_real_escape_string($target_id)."' ");
        $report_cnt = isset($cnt_row['cnt']) ? (int)$cnt_row['cnt'] : 0;

        $ban_min = 0; $do_ban = false; $is_perm = false;
        if ($report_cnt === 10)     { $ban_min = 10;  $do_ban = true; }
        else if ($report_cnt === 20){ $ban_min = 60;  $do_ban = true; }
        else if ($report_cnt === 30){ $ban_min = 600; $do_ban = true; }
        else if ($report_cnt >= 31) { $ban_min = 0;   $do_ban = true; $is_perm = true; }

        if ($do_ban) {
            $m = sql_fetch(" SELECT mb_id, mb_nick FROM {$g5['member_table']} WHERE mb_id = '".sql_real_escape_string($target_id)."' LIMIT 1 ");
            if ($m && $m['mb_id']) {
                $ban_until = null;
                if (!$is_perm && $ban_min > 0) $ban_until = date('Y-m-d H:i:s', time() + ($ban_min * 60));
                $reason_auto = "누적신고 {$report_cnt}회 자동밴";

                sql_query("
                    INSERT INTO {$tbl_ban}
                    SET mb_id='".sql_real_escape_string($m['mb_id'])."',
                        mb_nick='".sql_real_escape_string($m['mb_nick'])."',
                        is_active=1, banned_at=NOW(), duration_min={$ban_min},
                        ban_until ".($ban_until ? "='".sql_real_escape_string($ban_until)."'" : "=NULL").",
                        reason='".sql_real_escape_string($reason_auto)."',
                        banned_by='AUTO_REPORT',
                        ip_at_ban='".sql_real_escape_string($ip)."',
                        created_at=NOW(), updated_at=NOW()
                    ON DUPLICATE KEY UPDATE
                        mb_nick=VALUES(mb_nick), is_active=1, banned_at=NOW(),
                        duration_min=VALUES(duration_min), ban_until=VALUES(ban_until),
                        reason=VALUES(reason), banned_by=VALUES(banned_by),
                        ip_at_ban=VALUES(ip_at_ban), updated_at=NOW()
                ", false);
            }
        }
    }

    eve_chat_json(array('ok'=>1,'msg'=>'신고가 접수되었습니다.'));
}

// ===== ADMIN =====
if (!$is_admin) eve_chat_json(array('ok'=>0,'msg'=>'권한이 없습니다.'));

if ($act === 'admin_freeze') {
    $val = isset($_POST['freeze']) ? ((int)$_POST['freeze'] ? 1 : 0) : 0;
    $ok = @sql_query(" UPDATE {$tbl_cfg} SET cf_freeze = {$val}, cf_updated_at = NOW() WHERE cf_id = 1 ", false);
    if (!$ok) {
        eve_chat_json(array('ok'=>0,'msg'=>'DB 오류: '.@mysqli_error($connect_db)));
    }
    eve_chat_json(array('ok'=>1));
}

if ($act === 'admin_clear') {
    $ok = @sql_query(" DELETE FROM {$tbl_chat} ", false);
    if (!$ok) {
        eve_chat_json(array('ok'=>0,'msg'=>'DB 오류: '.@mysqli_error($connect_db)));
    }
    eve_chat_json(array('ok'=>1));
}

if ($act === 'admin_notice_save') {
    $notice_text = isset($_POST['notice_text']) ? trim($_POST['notice_text']) : '';
    $badwords    = isset($_POST['badwords']) ? trim($_POST['badwords']) : '';

    $ok = @sql_query("
        UPDATE {$tbl_cfg}
        SET cf_notice_text = '".sql_real_escape_string($notice_text)."',
            cf_badwords    = '".sql_real_escape_string($badwords)."',
            cf_updated_at  = NOW()
        WHERE cf_id = 1
    ", false);
    if (!$ok) {
        eve_chat_json(array('ok'=>0,'msg'=>'DB 오류: '.@mysqli_error($connect_db)));
    }
    eve_chat_json(array('ok'=>1));
}

if ($act === 'admin_config_save') {
    $spam_sec     = isset($_POST['spam_sec']) ? (int)$_POST['spam_sec'] : 2;
    $repeat_sec   = isset($_POST['repeat_sec']) ? (int)$_POST['repeat_sec'] : 30;
    $report_limit = isset($_POST['report_limit']) ? (int)$_POST['report_limit'] : 10;
    $autoban_min  = isset($_POST['autoban_min']) ? (int)$_POST['autoban_min'] : 10;
    $online_fake_add = isset($_POST['online_fake_add']) ? max(0,(int)$_POST['online_fake_add']) : 0;
    $notice_text  = isset($_POST['notice_text']) ? trim($_POST['notice_text']) : '';
    $rule_text    = isset($_POST['rule_text']) ? trim($_POST['rule_text']) : '';
    $badwords     = isset($_POST['badwords']) ? trim($_POST['badwords']) : '';
    $daily_visit_target = isset($_POST['daily_visit_target']) ? max(0,(int)$_POST['daily_visit_target']) : 0;
    $left_login_notice = isset($_POST['left_login_notice']) ? trim($_POST['left_login_notice']) : '';
    $left_login_ticker_speed = isset($_POST['left_login_ticker_speed']) ? (int)$_POST['left_login_ticker_speed'] : 30;
    if ($left_login_ticker_speed < 10 || $left_login_ticker_speed > 45) $left_login_ticker_speed = 30;

    $ok = @sql_query("
        UPDATE {$tbl_cfg}
        SET cf_spam_sec = {$spam_sec},
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
            cf_updated_at  = NOW()
        WHERE cf_id = 1
    ", false);
    if (!$ok) {
        eve_chat_json(array('ok'=>0,'msg'=>'DB 오류: '.@mysqli_error($connect_db)));
    }

    eve_chat_json(array('ok'=>1));
}

if ($act === 'admin_ban') {
    $mb_id = isset($_POST['mb_id']) ? trim($_POST['mb_id']) : '';
    $min   = isset($_POST['min']) ? (int)$_POST['min'] : 10;
    if ($mb_id === '') eve_chat_json(array('ok'=>0,'msg'=>'mb_id가 필요합니다.'));

    $m = sql_fetch(" SELECT mb_id, mb_nick FROM {$g5['member_table']} WHERE mb_id='".sql_real_escape_string($mb_id)."' LIMIT 1 ");
    if (!$m) eve_chat_json(array('ok'=>0,'msg'=>'해당 회원을 찾지 못했습니다.'));

    $ban_until = null;
    if ($min > 0) $ban_until = date('Y-m-d H:i:s', time() + ($min*60));

    sql_query("
        INSERT INTO {$tbl_ban}
        SET mb_id='".sql_real_escape_string($m['mb_id'])."',
            mb_nick='".sql_real_escape_string($m['mb_nick'])."',
            is_active=1, banned_at=NOW(), duration_min={$min},
            ban_until ".($ban_until ? "='".sql_real_escape_string($ban_until)."'" : "=NULL").",
            reason='', banned_by='".sql_real_escape_string($my_id)."',
            ip_at_ban='".sql_real_escape_string($ip)."',
            created_at=NOW(), updated_at=NOW()
        ON DUPLICATE KEY UPDATE
            mb_nick=VALUES(mb_nick), is_active=1, banned_at=NOW(),
            duration_min=VALUES(duration_min), ban_until=VALUES(ban_until),
            banned_by=VALUES(banned_by), ip_at_ban=VALUES(ip_at_ban), updated_at=NOW()
    ", false);

    eve_chat_json(array('ok'=>1));
}

if ($act === 'admin_ban_nick') {
    $nick = isset($_POST['nick']) ? trim($_POST['nick']) : '';
    $min  = isset($_POST['min']) ? (int)$_POST['min'] : 10;
    $reason = isset($_POST['reason']) ? trim($_POST['reason']) : '';
    if ($nick === '') eve_chat_json(array('ok'=>0,'msg'=>'닉네임을 입력하세요.'));

    $m = sql_fetch(" SELECT mb_id, mb_nick FROM {$g5['member_table']} WHERE mb_nick='".sql_real_escape_string($nick)."' LIMIT 1 ");
    if (!$m) eve_chat_json(array('ok'=>0,'msg'=>'해당 닉네임 회원을 찾지 못했습니다.'));

    $ban_until = null;
    if ($min > 0) $ban_until = date('Y-m-d H:i:s', time() + ($min*60));

    sql_query("
        INSERT INTO {$tbl_ban}
        SET mb_id='".sql_real_escape_string($m['mb_id'])."',
            mb_nick='".sql_real_escape_string($m['mb_nick'])."',
            is_active=1, banned_at=NOW(), duration_min={$min},
            ban_until ".($ban_until ? "='".sql_real_escape_string($ban_until)."'" : "=NULL").",
            reason='".sql_real_escape_string($reason)."',
            banned_by='".sql_real_escape_string($my_id)."',
            ip_at_ban='".sql_real_escape_string($ip)."',
            created_at=NOW(), updated_at=NOW()
        ON DUPLICATE KEY UPDATE
            mb_nick=VALUES(mb_nick), is_active=1, banned_at=NOW(),
            duration_min=VALUES(duration_min), ban_until=VALUES(ban_until),
            reason=VALUES(reason), banned_by=VALUES(banned_by),
            ip_at_ban=VALUES(ip_at_ban), updated_at=NOW()
    ", false);

    eve_chat_json(array('ok'=>1));
}

if ($act === 'admin_unban') {
    $mb_id = isset($_POST['mb_id']) ? trim($_POST['mb_id']) : '';
    $nick  = isset($_POST['nick']) ? trim($_POST['nick']) : '';
    if ($mb_id === '' && $nick === '') eve_chat_json(array('ok'=>0,'msg'=>'해제할 대상이 없습니다.'));

    if ($mb_id === '' && $nick !== '') {
        $m = sql_fetch(" SELECT mb_id FROM {$g5['member_table']} WHERE mb_nick='".sql_real_escape_string($nick)."' LIMIT 1 ");
        if (!$m) eve_chat_json(array('ok'=>0,'msg'=>'해당 닉네임 회원을 찾지 못했습니다.'));
        $mb_id = $m['mb_id'];
    }

    sql_query("
        UPDATE {$tbl_ban}
        SET is_active = 0,
            unbanned_by = '".sql_real_escape_string($my_id)."',
            unbanned_at = NOW(), updated_at = NOW()
        WHERE mb_id = '".sql_real_escape_string($mb_id)."'
    ", false);

    eve_chat_json(array('ok'=>1));
}

if ($act === 'banlist') {
    eve_chat_apply_expired_bans($tbl_ban);
    $status = isset($_GET['status']) ? trim($_GET['status']) : 'active';
    $q = isset($_GET['q']) ? trim($_GET['q']) : '';

    $where = " WHERE 1 ";
    if ($status === 'active') $where .= " AND is_active = 1 ";
    else if ($status === 'expired') $where .= " AND is_active = 0 ";
    if ($q !== '') {
        $qq = sql_real_escape_string($q);
        $where .= " AND (mb_nick LIKE '%{$qq}%' OR mb_id LIKE '%{$qq}%') ";
    }

    $list = array();
    $rs = sql_query(" SELECT * FROM {$tbl_ban} {$where} ORDER BY banned_at DESC LIMIT 200 ");
    while($r = sql_fetch_array($rs)){
        $list[] = array(
            'mb_nick' => $r['mb_nick'], 'mb_id' => $r['mb_id'],
            'is_active' => (int)$r['is_active'], 'banned_at' => $r['banned_at'],
            'duration_min' => (int)$r['duration_min'], 'ban_until' => $r['ban_until'],
            'reason' => $r['reason'], 'banned_by' => $r['banned_by'],
            'unbanned_by' => $r['unbanned_by'], 'unbanned_at' => $r['unbanned_at'],
            'ip_at_ban' => $r['ip_at_ban'], 'report_count' => (int)$r['report_count']
        );
    }
    eve_chat_json(array('ok'=>1,'list'=>$list));
}

eve_chat_json(array('ok'=>0,'msg'=>'unknown act'));
