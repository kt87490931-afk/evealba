<?php
/**
 * Google Site Verification DB 수정 (1회 실행)
 * CLI: php fix_google_verification.php
 * 실행 후 삭제됨
 */
$base = dirname(__FILE__);
chdir($base);
$_SERVER['REQUEST_URI'] = '/';
$_SERVER['HTTP_HOST'] = 'evealba.co.kr';
$_SERVER['REQUEST_METHOD'] = 'GET';
define('G5_SET_TIME_LIMIT', 0);
include_once($base . '/common.php');
$v = 'J9HdPWo8VhI3q7vRTDx-vTxdZLhE2aHnfJzC9_nbGfY';
$e = sql_escape_string($v);
sql_query("UPDATE sp_seo_config SET sp_google_site_verification = '{$e}' WHERE id = 1");
echo "OK: sp_google_site_verification = {$v}\n";
@unlink(__FILE__);
