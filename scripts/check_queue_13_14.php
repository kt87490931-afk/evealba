<?php
$_SERVER['SERVER_PORT'] = '80';
$_SERVER['SERVER_NAME'] = 'localhost';
$_SERVER['REQUEST_URI'] = '/';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';

define('_GNUBOARD_', true);
$g5_path = ['path' => dirname(__DIR__)];
include_once dirname(__DIR__) . '/common.php';

$q = sql_query("SELECT id, jr_id, status, error_msg, created_at, processed_at FROM g5_jobs_ai_queue WHERE id >= 10 ORDER BY id", false);
while ($row = sql_fetch_array($q)) {
    $err = $row['error_msg'] ? substr($row['error_msg'], 0, 80) : '-';
    echo "ID={$row['id']} jr_id={$row['jr_id']} status={$row['status']} proc={$row['processed_at']} err={$err}\n";
}
