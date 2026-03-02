<?php
include('/var/www/evealba/_common.php');

for ($id = 2; $id <= 4; $id++) {
    $row = sql_fetch("SELECT jr_id, jr_nickname, jr_company, jr_title, jr_data, jr_ad_labels FROM g5_jobs_register WHERE jr_id = {$id}");
    echo "=== jr_id={$id} ===" . PHP_EOL;
    echo "nickname={$row['jr_nickname']}" . PHP_EOL;
    echo "company={$row['jr_company']}" . PHP_EOL;
    echo "title={$row['jr_title']}" . PHP_EOL;
    echo "ad_labels={$row['jr_ad_labels']}" . PHP_EOL;
    $data = json_decode($row['jr_data'], true);
    echo "jr_data keys: " . implode(', ', array_keys($data ?: [])) . PHP_EOL;
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . PHP_EOL . PHP_EOL;
}
