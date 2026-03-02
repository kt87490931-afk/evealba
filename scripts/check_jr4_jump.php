<?php
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/scripts/check_jr4_jump.php';
$base = dirname(__DIR__);
@include_once($base . '/_common.php');
$rows = array(1,2,3,4);
foreach ($rows as $id) {
    $r = sql_fetch("SELECT jr_id, jr_status, jr_jump_total, jr_jump_remain, jr_jump_used, jr_ad_labels, jr_ad_period, jr_end_date FROM g5_jobs_register WHERE jr_id = '{$id}'");
    if ($r) {
        echo "jr_id={$r['jr_id']} status={$r['jr_status']} jump_total={$r['jr_jump_total']} remain={$r['jr_jump_remain']} used={$r['jr_jump_used']} labels=[{$r['jr_ad_labels']}] period={$r['jr_ad_period']}d end={$r['jr_end_date']}\n";
    }
}
