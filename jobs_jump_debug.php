<?php
/**
 * 점프 기능 진단 스크립트 - 브라우저에서 /jobs_jump_debug.php 접속
 * 문제 해결 후 삭제 권장
 */
header('Content-Type: application/json; charset=utf-8');
$out = array('step' => 0, 'ok' => 0, 'checks' => array());

try {
    $out['step'] = 1;
    include_once(__DIR__ . '/_common.php');
    $out['checks']['common'] = 'OK';
    $out['step'] = 2;

    if (!function_exists('sql_query')) {
        $out['checks']['sql_query'] = 'MISSING';
        echo json_encode($out, JSON_UNESCAPED_UNICODE);
        exit;
    }
    $out['checks']['sql_query'] = 'OK';
    $out['step'] = 3;

    $t = @sql_query("SHOW TABLES LIKE 'g5_jobs_register'", false);
    $out['checks']['table_register'] = ($t && @sql_num_rows($t)) ? 'EXISTS' : 'MISSING';
    $out['step'] = 4;

    $c = @sql_query("SHOW COLUMNS FROM g5_jobs_register LIKE 'jr_jump_remain'", false);
    $out['checks']['col_jump_remain'] = ($c && @sql_num_rows($c)) ? 'EXISTS' : 'MISSING';
    $out['step'] = 5;

    $lt = @sql_query("SHOW TABLES LIKE 'g5_jobs_jump_log'", false);
    $out['checks']['table_jump_log'] = ($lt && @sql_num_rows($lt)) ? 'EXISTS' : 'MISSING';
    $out['step'] = 6;

    $out['member'] = $is_member ? 'yes' : 'no';
    $out['ok'] = 1;
} catch (Throwable $e) {
    $out['error'] = $e->getMessage();
    $out['file'] = $e->getFile();
    $out['line'] = $e->getLine();
}
echo json_encode($out, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
