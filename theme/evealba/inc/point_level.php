<?php
/**
 * 포인트 → 레벨(1~20) 단일 정의 (재정의 방지)
 * - left-login.php 등에서 include_once로 사용
 * - 경험치(회원등급 아이콘)는 보유 포인트에 따라 변동
 */
if (function_exists('sp_get_point_level_by_point')) {
    return;
}

// 포인트 레벨 구간표 (레벨 => 최소 누적 포인트)
$SP_POINT_LEVEL = array(
    1  => 0,
    2  => 300000,
    3  => 420000,
    4  => 590000,
    5  => 820000,
    6  => 1315000,
    7  => 2005000,
    8  => 2980000,
    9  => 4285000,
    10 => 6825000,
    11 => 10365000,
    12 => 15325000,
    13 => 22400000,
    14 => 34550000,
    15 => 51450000,
    16 => 80040000,
    17 => 120030000,
    18 => 179774000,
    19 => 268616000,
);

function sp_get_point_level_by_point($point, $is_super_admin = false)
{
    global $SP_POINT_LEVEL;

    if ($is_super_admin) {
        return 20;
    }

    $point = (int)$point;
    if ($point < 0) {
        $point = 0;
    }

    $level = 1;
    foreach ($SP_POINT_LEVEL as $lv => $min_point) {
        if ($point >= (int)$min_point) {
            $level = (int)$lv;
        }
    }

    if ($level < 1)  $level = 1;
    if ($level > 19) $level = 19;

    return $level;
}
