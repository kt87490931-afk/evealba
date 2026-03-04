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

// 발급할 쿠폰: ec_issue_type=auto, ec_auto_trigger=monthly_1st (신규) 또는 기존 쿠폰명 조회
$cols_check = array();
$cr = sql_query("SHOW COLUMNS FROM {$tb}", false);
if ($cr) while ($r = sql_fetch_array($cr)) $cols_check[] = $r['Field'];
$use_new = in_array('ec_issue_type', $cols_check) && in_array('ec_auto_trigger', $cols_check);

$ec_list = array();
if ($use_new) {
    $crs = sql_query("SELECT ec_id FROM {$tb} WHERE ec_issue_type = 'auto' AND ec_auto_trigger = 'monthly_1st' AND ec_target = 'biz' AND ec_is_active = 1", false);
    if ($crs) while ($row = sql_fetch_array($crs)) $ec_list[] = (int)$row['ec_id'];
}
if (empty($ec_list)) {
    $ec = sql_fetch("SELECT ec_id FROM {$tb} WHERE ec_name = '매월30%할인' AND ec_target = 'biz' AND ec_is_active = 1 LIMIT 1");
    if (!$ec) $ec = sql_fetch("SELECT ec_id FROM {$tb} WHERE ec_name LIKE '%30%할인%' AND ec_type = 'ad' AND ec_target = 'biz' AND ec_is_active = 1 LIMIT 1");
    if ($ec) $ec_list = array((int)$ec['ec_id']);
}
if (empty($ec_list)) exit("월간 발급 대상 쿠폰 없음\n");
$today = date('Y-m-d');
$total_issued = 0;

foreach ($ec_list as $ec_id) {
    $ec_row = sql_fetch("SELECT ec_issue_from, ec_issue_to, ec_issue_limit_per_member, ec_use_limit, ec_issue_target_scope, ec_issue_target_mb_id FROM {$tb} WHERE ec_id = '{$ec_id}'");
    if (!$ec_row) continue;
    if (!empty($ec_row['ec_issue_from']) && $today < $ec_row['ec_issue_from']) continue;
    if (!empty($ec_row['ec_issue_to']) && $today > $ec_row['ec_issue_to']) continue;

    $limit_per = (int)($ec_row['ec_issue_limit_per_member'] ?? 0);
    $use_limit = (int)($ec_row['ec_use_limit'] ?? 0);
    $scope = $ec_row['ec_issue_target_scope'] ?? 'all';
    $target_mb = trim($ec_row['ec_issue_target_mb_id'] ?? '');

    $mb_ids = array();
    if ($scope === 'individual' && $target_mb) {
        $m = sql_fetch("SELECT mb_id FROM {$g5['member_table']} WHERE mb_id = '".sql_escape_string($target_mb)."' AND mb_1 = 'biz' AND mb_7 = 'approved'");
        if ($m) $mb_ids = array($m['mb_id']);
    } else {
        $r = sql_query("SELECT mb_id FROM {$g5['member_table']} WHERE mb_1 = 'biz' AND mb_7 = 'approved'");
        while ($row = sql_fetch_array($r)) $mb_ids[] = $row['mb_id'];
    }

    $issued = 0;
    foreach ($mb_ids as $mb_id) {
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
    $total_issued += $issued;
    echo date('Y-m-d H:i:s') . " 월간쿠폰 ec_id={$ec_id} 발급 {$issued}건\n";
}

echo date('Y-m-d H:i:s') . " 월간쿠폰 발급 완료: 총 {$total_issued}건\n";
