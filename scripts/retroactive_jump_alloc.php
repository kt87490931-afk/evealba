<?php
/**
 * 기존 ongoing 광고에 점프 횟수 소급 지급
 * jr_jump_total = 0 인 ongoing 광고에 대해 ad_labels/ad_period 기반으로 점프 부여
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/scripts/retroactive_jump_alloc.php';

$base = dirname(__DIR__);
include_once($base . '/_common.php');

echo "=== 기존 ongoing 광고 점프 소급 지급 ===\n\n";

$today = date('Y-m-d');
$sql = "SELECT jr_id, jr_ad_labels, jr_ad_period, jr_jump_total, jr_jump_remain
    FROM g5_jobs_register
    WHERE jr_status = 'ongoing' AND jr_jump_total = 0 AND jr_end_date >= '{$today}'";
$result = sql_query($sql, false);

$count = 0;
if ($result) {
    while ($row = sql_fetch_array($result)) {
        $jr_id = (int)$row['jr_id'];
        $ad_labels = trim($row['jr_ad_labels']);
        $ad_period = (int)$row['jr_ad_period'] ?: 30;

        $has_extra = false;
        $extra_types = array('우대', '프리미엄', '스페셜', '급구', '추천');
        foreach ($extra_types as $et) {
            if (strpos($ad_labels, $et) !== false) { $has_extra = true; break; }
        }

        if ($has_extra) {
            if ($ad_period >= 90) $alloc = 3200;
            elseif ($ad_period >= 60) $alloc = 1900;
            else $alloc = 900;
        } else {
            if ($ad_period >= 90) $alloc = 1200;
            elseif ($ad_period >= 60) $alloc = 700;
            else $alloc = 300;
        }

        sql_query("UPDATE g5_jobs_register SET jr_jump_total = {$alloc}, jr_jump_remain = {$alloc} WHERE jr_id = '{$jr_id}'");
        echo "jr_id={$jr_id}: labels=[{$ad_labels}] period={$ad_period}일 extra=" . ($has_extra ? 'Y' : 'N') . " → {$alloc}회 지급\n";
        $count++;
    }
}

echo "\n총 {$count}건 소급 지급 완료\n";
