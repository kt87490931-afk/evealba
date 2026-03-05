<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
$_SERVER['HTTP_HOST'] = 'evealba.co.kr';
$_SERVER['REQUEST_URI'] = '/';
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['SERVER_PORT'] = '443';
$_SERVER['SERVER_NAME'] = 'evealba.co.kr';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
define('G5_IS_ADMIN', 1);
chdir(__DIR__ . '/..');
include 'common.php';
$tables = array('g5_ev_matching_log', 'g5_ev_matching_config', 'g5_resume', 'g5_jobs_register');
foreach ($tables as $t) {
    $r = sql_query("SHOW TABLES LIKE '" . sql_escape_string($t) . "'", false);
    echo $t . "=" . (sql_num_rows($r) ? "YES" : "NO") . "\n";
}
if (file_exists(G5_LIB_PATH . '/ev_matching.lib.php')) echo "ev_matching.lib: OK\n";
else echo "ev_matching.lib: MISSING\n";
if (file_exists(G5_LIB_PATH . '/ev_memo.lib.php')) echo "ev_memo.lib: OK\n";
else echo "ev_memo.lib: MISSING\n";
