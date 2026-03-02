<?php
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/scripts/fix_jr4_jump.php';
$base = dirname(__DIR__);
@include_once($base . '/_common.php');

sql_query("UPDATE g5_jobs_register SET jr_jump_total=900, jr_jump_remain=900 WHERE jr_id=4");
echo "jr_id=4 updated to 900\n";

sql_query("UPDATE g5_jobs_register SET jr_jump_total=900, jr_jump_remain=900 WHERE jr_id=1 AND jr_status='pending'");
echo "jr_id=1 updated to 900 (pending)\n";

$rows = array(1,2,3,4);
foreach ($rows as $id) {
    $r = sql_fetch("SELECT jr_id, jr_status, jr_jump_total, jr_jump_remain FROM g5_jobs_register WHERE jr_id = '{$id}'");
    if ($r) echo "jr_id={$r['jr_id']} status={$r['jr_status']} total={$r['jr_jump_total']} remain={$r['jr_jump_remain']}\n";
}
