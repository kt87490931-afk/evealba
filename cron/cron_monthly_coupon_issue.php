<?php
/**
 * 매월 1일 전체 기업회원 대상 쿠폰 자동 발급
 * Cron: 0 0 1 * * php /var/www/evealba/cron/cron_monthly_coupon_issue.php
 * ec_id는 설정 또는 쿠폰명으로 지정 (예: 채용공고30%할인)
 */
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/cron/cron_monthly_coupon_issue.php';
$base = dirname(dirname(__FILE__));
include_once $base . '/common.php';
if (!function_exists('sql_query')) { exit("DB 연결 실패\n"); }

$tb = 'g5_ev_coupon';
$tb_issue = 'g5_ev_coupon_issue';

if (!sql_num_rows(sql_query("SHOW TABLES LIKE '{$tb}'", false))) {
    exit("g5_ev_coupon 테이블 없음\n");
}

// 발급할 쿠폰 ec_id (어드민에서 월간 발급용으로 등록한 쿠폰)
// 쿠폰명으로 조회 - '매월30%할인' 또는 설정 변경
$monthly_coupon_name = '매월30%할인';
$ec = sql_fetch("SELECT ec_id FROM {$tb} WHERE ec_name = '".sql_escape_string($monthly_coupon_name)."' AND ec_target = 'biz' AND ec_is_active = 1 LIMIT 1");
if (!$ec) {
    $ec = sql_fetch("SELECT ec_id FROM {$tb} WHERE ec_name LIKE '%30%할인%' AND ec_type = 'ad' AND ec_target = 'biz' AND ec_is_active = 1 LIMIT 1");
}
if (!$ec) {
    exit("월간 발급 대상 쿠폰 없음\n");
}

$ec_id = (int)$ec['ec_id'];
$today = date('Y-m-d');

$ec_row = sql_fetch("SELECT ec_issue_from, ec_issue_to, ec_issue_limit_per_member, ec_use_limit FROM {$tb} WHERE ec_id = '{$ec_id}'");
if ($ec_row) {
    if (!empty($ec_row['ec_issue_from']) && $today < $ec_row['ec_issue_from']) exit("발급 가능 기간 아님\n");
    if (!empty($ec_row['ec_issue_to']) && $today > $ec_row['ec_issue_to']) exit("발급 가능 기간 지남\n");
}

$r = sql_query("SELECT mb_id FROM {$g5['member_table']} WHERE mb_1 = 'biz' AND mb_7 = 'approved'");
$limit_per = (int)($ec_row['ec_issue_limit_per_member'] ?? 0);
$use_limit = (int)($ec_row['ec_use_limit'] ?? 0);
$issued = 0;

while ($row = sql_fetch_array($r)) {
    $mb_id = $row['mb_id'];
    if ($use_limit > 0) {
        $total = sql_fetch("SELECT COUNT(*) AS c FROM {$tb_issue} WHERE ec_id = '{$ec_id}'");
        if (($total['c'] ?? 0) >= $use_limit) break;
    }
    if ($limit_per > 0) {
        $cnt = sql_fetch("SELECT COUNT(*) AS c FROM {$tb_issue} WHERE ec_id = '{$ec_id}' AND mb_id = '".sql_escape_string($mb_id)."'");
        if (($cnt['c'] ?? 0) >= $limit_per) continue;
    }
    $ex = sql_fetch("SELECT eci_id FROM {$tb_issue} WHERE ec_id = '{$ec_id}' AND mb_id = '".sql_escape_string($mb_id)."' LIMIT 1");
    if ($ex) continue;

    sql_query("INSERT INTO {$tb_issue} (ec_id, mb_id) VALUES ('{$ec_id}', '".sql_escape_string($mb_id)."')", false);
    $issued++;
}

echo date('Y-m-d H:i:s') . " 월간쿠폰 발급 완료: ec_id={$ec_id}, {$issued}명\n";
