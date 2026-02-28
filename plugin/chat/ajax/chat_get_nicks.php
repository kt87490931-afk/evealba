<?php
// /plugin/chat/ajax/chat_get_nicks.php
include_once('../../../common.php');
header('Content-Type: application/json; charset=utf-8');

if (!defined('_GNUBOARD_')) {
    echo json_encode(array('ok'=>false,'msg'=>'GNUBOARD not loaded'));
    exit;
}

$mb_ids_raw = isset($_POST['mb_ids']) ? trim((string)$_POST['mb_ids']) : '';
if ($mb_ids_raw === '') {
    echo json_encode(array('ok'=>true,'map'=>array()));
    exit;
}

$parts = array_filter(array_map('trim', explode(',', $mb_ids_raw)));
$parts = array_slice($parts, 0, 50);

if (!$parts) {
    echo json_encode(array('ok'=>true,'map'=>array()));
    exit;
}

$in = array();
foreach ($parts as $id) {
    $in[] = "'".sql_real_escape_string($id)."'";
}

$sql = "SELECT mb_id, mb_nick FROM {$g5['member_table']} WHERE mb_id IN (".implode(',', $in).")";
$res = sql_query($sql);

$map = array();
while ($row = sql_fetch_array($res)) {
    $map[$row['mb_id']] = $row['mb_nick'];
}

echo json_encode(array('ok'=>true,'map'=>$map));
