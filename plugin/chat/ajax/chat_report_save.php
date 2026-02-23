<?php
// /plugin/chat/ajax/chat_report_save.php
include_once('../../../common.php'); // ✅ GNUBOARD 로드(경로 고정)
header('Content-Type: application/json; charset=utf-8');

if (!defined('_GNUBOARD_')) {
    echo json_encode(['ok'=>false,'msg'=>'GNUBOARD not loaded']);
    exit;
}

if (!$is_member) {
    echo json_encode(['ok'=>false,'msg'=>'로그인 후 신고 가능합니다.']);
    exit;
}

$target_id   = trim((string)($_POST['target_id'] ?? ''));
$target_nick = trim((string)($_POST['target_nick'] ?? ''));
$reason      = trim((string)($_POST['reason'] ?? ''));

if ($target_id === '' || $target_nick === '') {
    echo json_encode(['ok'=>false,'msg'=>'대상 정보가 없습니다.']);
    exit;
}

// 자기 자신 신고 방지
if ($member['mb_id'] === $target_id) {
    echo json_encode(['ok'=>false,'msg'=>'본인은 신고할 수 없습니다.']);
    exit;
}

// 테이블 자동 선택: chat_report2 우선, 없으면 chat_report
$tbl2 = $g5['table_prefix'].'chat_report2';
$tbl1 = $g5['table_prefix'].'chat_report';

$use_table = '';
$chk2 = sql_fetch("SHOW TABLES LIKE '".sql_real_escape_string($tbl2)."' ");
if ($chk2) $use_table = $tbl2;
else {
    $chk1 = sql_fetch("SHOW TABLES LIKE '".sql_real_escape_string($tbl1)."' ");
    if ($chk1) $use_table = $tbl1;
}

if ($use_table === '') {
    echo json_encode(['ok'=>false,'msg'=>'신고 테이블이 없습니다. update를 먼저 실행하세요.']);
    exit;
}

// 동일 신고자->동일 대상: 최근 5분 1회 제한
$recent = sql_fetch("
    SELECT id FROM {$use_table}
     WHERE reporter_id='".sql_real_escape_string($member['mb_id'])."'
       AND target_id='".sql_real_escape_string($target_id)."'
       AND created_at >= (NOW() - INTERVAL 5 MINUTE)
     LIMIT 1
");
if ($recent && (int)$recent['id'] > 0) {
    echo json_encode(['ok'=>false,'msg'=>'이미 최근에 신고했습니다. 잠시 후 다시 시도하세요.']);
    exit;
}

$ip = $_SERVER['REMOTE_ADDR'] ?? '';

// 신고 로그 저장
sql_query("INSERT INTO {$use_table}
    SET reporter_id='".sql_real_escape_string($member['mb_id'])."',
        reporter_nick='".sql_real_escape_string($member['mb_nick'])."',
        target_id='".sql_real_escape_string($target_id)."',
        target_nick='".sql_real_escape_string($target_nick)."',
        reason='".sql_real_escape_string($reason)."',
        ip='".sql_real_escape_string($ip)."',
        created_at=NOW()
");

// ✅ 누적 신고 카운트 증가
// 기본: g5_member.mb_3 사용 (이미 다른 용도면 여기만 바꾸면 됩니다)
$col = 'mb_3';

sql_query("
    UPDATE {$g5['member_table']}
       SET {$col} = IFNULL({$col}, '0') + 1
     WHERE mb_id='".sql_real_escape_string($target_id)."'
");

$row = sql_fetch("SELECT {$col} AS cnt FROM {$g5['member_table']} WHERE mb_id='".sql_real_escape_string($target_id)."' ");
$new_cnt = (int)($row['cnt'] ?? 0);

echo json_encode([
    'ok' => true,
    'msg' => '신고가 접수되었습니다.',
    'table' => $use_table,
    'new_count' => $new_cnt
]);
