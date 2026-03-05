<?php
define('_RUN_MIGRATION_', true);
chdir(__DIR__ . '/..');
include 'common.php';
$r = sql_query("SHOW TABLES LIKE 'g5_ev_matching%'", false);
$n = sql_num_rows($r);
echo "Tables found: $n\n";
while ($row = sql_fetch_array($r)) {
    echo implode(' ', $row) . "\n";
}
