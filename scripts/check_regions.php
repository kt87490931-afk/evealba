<?php
include('/var/www/evealba/_common.php');

$r = sql_query("SELECT er_id, er_name FROM g5_eve_region ORDER BY er_id LIMIT 20");
echo "=== Regions ===" . PHP_EOL;
while ($row = sql_fetch_array($r)) {
    echo "er_id={$row['er_id']} => {$row['er_name']}" . PHP_EOL;
}

$r2 = sql_query("SELECT erd_id, er_id, erd_name FROM g5_eve_region_detail ORDER BY erd_id LIMIT 50");
echo PHP_EOL . "=== Region Details ===" . PHP_EOL;
while ($row = sql_fetch_array($r2)) {
    echo "erd_id={$row['erd_id']} (er_id={$row['er_id']}) => {$row['erd_name']}" . PHP_EOL;
}
