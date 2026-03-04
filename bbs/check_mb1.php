<?php
/**
 * 임시: test03 mb_1 확인용 (확인 후 삭제)
 */
include_once('./_common.php');
if (!$is_admin) die('관리자만');
header('Content-Type: text/plain; charset=utf-8');
$id = isset($_GET['id']) ? preg_replace('/[^a-zA-Z0-9_]/', '', $_GET['id']) : 'test03';
$row = sql_fetch("SELECT mb_id, mb_1, mb_7, mb_nick FROM {$g5['member_table']} WHERE mb_id = '" . sql_escape_string($id) . "'");
echo "mb_id: " . ($row['mb_id'] ?? 'NOT FOUND') . "\n";
echo "mb_1: " . ($row['mb_1'] ?? 'NULL') . " (raw: " . var_export($row['mb_1'] ?? null, true) . ")\n";
echo "mb_7: " . ($row['mb_7'] ?? '') . "\n";
$is_biz = !empty($row['mb_1']) && in_array($row['mb_1'], ['biz', 'business'], true);
echo "is_biz: " . ($is_biz ? 'YES' : 'NO') . "\n";
