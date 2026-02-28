<?php
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['REQUEST_URI'] = '/';
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['DOCUMENT_ROOT'] = '/var/www/evealba';
define('_GNUBOARD_', true);
include('/var/www/evealba/common.php');
$m = sql_fetch("SELECT mb_id, mb_nick, mb_1, mb_sex FROM g5_member WHERE mb_id='admin' LIMIT 1");
echo "Admin member data:\n";
print_r($m);
echo "\n---\n";
echo "is_admin variable: " . (isset($is_admin) ? var_export($is_admin, true) : 'NOT SET') . "\n";
echo "config cf_admin: " . (isset($config['cf_admin']) ? $config['cf_admin'] : 'NOT SET') . "\n";
