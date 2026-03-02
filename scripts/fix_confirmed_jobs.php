<?php
include('/var/www/evealba/_common.php');

echo "=== Current Status ===" . PHP_EOL;
$r = sql_query("SELECT jr_id, jr_status, jr_payment_confirmed, jr_approved, jr_end_date, jr_ad_labels, jr_ad_period FROM g5_jobs_register ORDER BY jr_id");
while($row = sql_fetch_array($r)) {
    echo "jr_id={$row['jr_id']} | status={$row['jr_status']} | confirmed={$row['jr_payment_confirmed']} | approved={$row['jr_approved']} | end={$row['jr_end_date']} | labels={$row['jr_ad_labels']}" . PHP_EOL;
}

echo PHP_EOL . "=== Fixing confirmed but not ongoing ===" . PHP_EOL;
$fix = sql_query("SELECT jr_id, jr_ad_period FROM g5_jobs_register WHERE jr_payment_confirmed = 1 AND jr_status != 'ongoing'");
while($row = sql_fetch_array($fix)) {
    $period = (int)$row['jr_ad_period'] ?: 30;
    $end = date('Y-m-d', strtotime("+{$period} days"));
    sql_query("UPDATE g5_jobs_register SET jr_status = 'ongoing', jr_approved = 1, jr_approved_datetime = NOW(), jr_end_date = '{$end}' WHERE jr_id = " . (int)$row['jr_id']);
    echo "Fixed jr_id={$row['jr_id']} -> ongoing, end={$end}" . PHP_EOL;
}

echo PHP_EOL . "=== After Fix ===" . PHP_EOL;
$r2 = sql_query("SELECT jr_id, jr_status, jr_payment_confirmed, jr_approved, jr_end_date, jr_ad_labels FROM g5_jobs_register ORDER BY jr_id");
while($row = sql_fetch_array($r2)) {
    echo "jr_id={$row['jr_id']} | status={$row['jr_status']} | confirmed={$row['jr_payment_confirmed']} | approved={$row['jr_approved']} | end={$row['jr_end_date']} | labels={$row['jr_ad_labels']}" . PHP_EOL;
}
