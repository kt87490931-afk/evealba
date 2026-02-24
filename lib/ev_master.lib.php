<?php
/**
 * 이브알바 마스터 데이터 로더
 * - g5_ev_region, g5_ev_region_detail, g5_ev_industry, g5_ev_job, g5_ev_convenience, g5_ev_keyword
 */
if (!defined('_GNUBOARD_')) exit;

/**
 * 마스터 테이블 존재 여부 확인
 */
function ev_master_tables_exist() {
    static $cached = null;
    if ($cached !== null) return $cached;
    $t = 'g5_ev_region';
    $res = @sql_query("SELECT 1 FROM {$t} LIMIT 1", false);
    $cached = ($res !== false);
    return $cached;
}

/**
 * 지역 목록 (1차)
 */
function ev_get_regions() {
    if (!ev_master_tables_exist()) return [];
    $sql = "SELECT er_id, er_code, er_name FROM g5_ev_region ORDER BY er_ord, er_id";
    $result = sql_query($sql);
    $list = [];
    while ($row = sql_fetch_array($result)) {
        $list[] = $row;
    }
    return $list;
}

/**
 * 세부지역 목록 (2차) - er_id 없으면 전체
 */
function ev_get_region_details($er_id = null) {
    if (!ev_master_tables_exist()) return [];
    $where = $er_id ? " WHERE er_id = " . (int)$er_id : "";
    $sql = "SELECT erd_id, er_id, erd_code, erd_name FROM g5_ev_region_detail {$where} ORDER BY erd_ord, erd_id";
    $result = sql_query($sql);
    $list = [];
    while ($row = sql_fetch_array($result)) {
        $list[] = $row;
    }
    return $list;
}

/**
 * 업종 목록 (1차)
 */
function ev_get_industries() {
    if (!ev_master_tables_exist()) return [];
    $sql = "SELECT ei_id, ei_code, ei_name FROM g5_ev_industry ORDER BY ei_ord, ei_id";
    $result = sql_query($sql);
    $list = [];
    while ($row = sql_fetch_array($result)) {
        $list[] = $row;
    }
    return $list;
}

/**
 * 직종 목록 (2차) - ei_id 없으면 전체
 */
function ev_get_jobs($ei_id = null) {
    if (!ev_master_tables_exist()) return [];
    $where = $ei_id ? " WHERE ei_id = " . (int)$ei_id : "";
    $sql = "SELECT ej_id, ei_id, ej_code, ej_name FROM g5_ev_job {$where} ORDER BY ej_ord, ej_id";
    $result = sql_query($sql);
    $list = [];
    while ($row = sql_fetch_array($result)) {
        $list[] = $row;
    }
    return $list;
}

/**
 * 편의사항 목록
 */
function ev_get_conveniences() {
    if (!ev_master_tables_exist()) return [];
    $sql = "SELECT ec_id, ec_code, ec_name FROM g5_ev_convenience ORDER BY ec_ord, ec_id";
    $result = sql_query($sql);
    $list = [];
    while ($row = sql_fetch_array($result)) {
        $list[] = $row;
    }
    return $list;
}

/**
 * 키워드 목록
 */
function ev_get_keywords() {
    if (!ev_master_tables_exist()) return [];
    $sql = "SELECT ek_id, ek_code, ek_name FROM g5_ev_keyword ORDER BY ek_ord, ek_id";
    $result = sql_query($sql);
    $list = [];
    while ($row = sql_fetch_array($result)) {
        $list[] = $row;
    }
    return $list;
}
