<?php
/**
 * AI 생성 콘텐츠 헬퍼 (g5_jobs_ai_content 테이블)
 * 사용자 입력(jr_data)과 AI 생성 데이터를 완전 분리
 */
if (!defined('_GNUBOARD_')) exit;

function _aic_table_exists() {
    static $exists = null;
    if ($exists !== null) return $exists;
    $r = sql_query("SHOW TABLES LIKE 'g5_jobs_ai_content'", false);
    $exists = ($r && sql_num_rows($r) > 0);
    return $exists;
}

/**
 * 활성 AI 콘텐츠 조회 (jr_id별 is_active=1인 최신 1건)
 * 없으면 null 반환 → 호출부에서 jr_data fallback 처리
 */
function aic_get_active($jr_id) {
    if (!_aic_table_exists()) return null;
    $jr_id = (int)$jr_id;
    $row = sql_fetch("SELECT id, ai_data, version, ai_tone, created_at, duration_ms
                      FROM g5_jobs_ai_content
                      WHERE jr_id = '{$jr_id}' AND is_active = 1
                      ORDER BY id DESC LIMIT 1");
    if (!$row || empty($row['ai_data'])) return null;
    $data = json_decode($row['ai_data'], true);
    if (!is_array($data)) return null;
    $data['_aic_id'] = (int)$row['id'];
    $data['_version'] = (int)$row['version'];
    $data['_ai_tone'] = $row['ai_tone'];
    $data['_created_at'] = $row['created_at'];
    $data['_duration_ms'] = (int)$row['duration_ms'];
    return $data;
}

/**
 * 새 AI 콘텐츠 저장 (기존 활성 → 비활성, 새 버전 INSERT)
 * @return int|false 새 버전 번호 또는 실패 시 false
 */
function aic_save_new($jr_id, $mb_id, $ai_data, $ai_tone = 'unnie', $duration_ms = 0) {
    if (!_aic_table_exists()) return false;
    $jr_id = (int)$jr_id;
    sql_query("UPDATE g5_jobs_ai_content SET is_active = 0 WHERE jr_id = '{$jr_id}' AND is_active = 1");
    $vrow = sql_fetch("SELECT COALESCE(MAX(version), 0) + 1 as next_ver FROM g5_jobs_ai_content WHERE jr_id = '{$jr_id}'");
    $version = isset($vrow['next_ver']) ? (int)$vrow['next_ver'] : 1;
    $mb_id_esc = sql_escape_string($mb_id);
    $tone_esc = sql_escape_string($ai_tone);
    $json_esc = sql_escape_string(json_encode($ai_data, JSON_UNESCAPED_UNICODE));
    $now = defined('G5_TIME_YMDHIS') ? G5_TIME_YMDHIS : date('Y-m-d H:i:s');
    $duration_ms = (int)$duration_ms;
    sql_query("INSERT INTO g5_jobs_ai_content (jr_id, mb_id, version, ai_tone, ai_data, is_active, created_at, duration_ms)
               VALUES ('{$jr_id}', '{$mb_id_esc}', '{$version}', '{$tone_esc}', '{$json_esc}', 1, '{$now}', '{$duration_ms}')");
    return $version;
}

/**
 * 활성 AI 콘텐츠의 특정 필드 1개 업데이트 (수동 편집용)
 */
function aic_update_field($jr_id, $key, $value) {
    return aic_update_fields($jr_id, array($key => $value));
}

/**
 * 활성 AI 콘텐츠의 여러 필드 업데이트 (수동 편집용)
 */
function aic_update_fields($jr_id, $fields) {
    if (!_aic_table_exists()) return false;
    $jr_id = (int)$jr_id;
    $row = sql_fetch("SELECT id, ai_data FROM g5_jobs_ai_content WHERE jr_id = '{$jr_id}' AND is_active = 1 ORDER BY id DESC LIMIT 1");
    if (!$row) return false;
    $data = json_decode($row['ai_data'], true);
    if (!is_array($data)) $data = array();
    foreach ($fields as $k => $v) {
        $data[$k] = $v;
    }
    $json_esc = sql_escape_string(json_encode($data, JSON_UNESCAPED_UNICODE));
    $aid = (int)$row['id'];
    sql_query("UPDATE g5_jobs_ai_content SET ai_data = '{$json_esc}' WHERE id = '{$aid}'");
    return true;
}

/**
 * 특정 버전 활성화 (관리자 복원용)
 */
function aic_activate_version($jr_id, $version) {
    if (!_aic_table_exists()) return false;
    $jr_id = (int)$jr_id;
    $version = (int)$version;
    sql_query("UPDATE g5_jobs_ai_content SET is_active = 0 WHERE jr_id = '{$jr_id}'");
    sql_query("UPDATE g5_jobs_ai_content SET is_active = 1 WHERE jr_id = '{$jr_id}' AND version = '{$version}'");
    return true;
}

/**
 * ai_content(단일 블록)를 문단별로 파싱하여 섹션 키에 매핑
 * @param string $ai_content AI가 생성한 통합 텍스트
 * @return array ai_intro, ai_location, ai_env, ai_welfare 등
 */
function aic_parse_ai_content_to_sections($ai_content) {
    $content = trim((string)$ai_content);
    if ($content === '') return array();
    $paragraphs = preg_split('/\n\s*\n+/u', $content, -1, PREG_SPLIT_NO_EMPTY);
    $paragraphs = array_map('trim', $paragraphs);
    $paragraphs = array_values(array_filter($paragraphs));
    $out = array();
    $n = count($paragraphs);
    if ($n >= 1) {
        $out['ai_intro'] = $paragraphs[0];
        $out['ai_location'] = $paragraphs[0];
        $out['ai_card1_desc'] = $paragraphs[0];
    }
    if ($n >= 2) {
        $out['ai_welfare'] = $paragraphs[1];
        $out['ai_card3_desc'] = $paragraphs[1];
    }
    if ($n >= 3) {
        $out['ai_env'] = $paragraphs[2];
        $out['ai_card2_desc'] = $paragraphs[2];
        $out['ai_qualify'] = $paragraphs[2];
    }
    if ($n >= 4) {
        $out['ai_mbti_comment'] = $paragraphs[3];
        $out['ai_extra'] = $paragraphs[3];
    }
    if ($n >= 5) {
        $out['ai_card4_desc'] = $paragraphs[4];
    }
    $out['ai_content'] = $content;
    return $out;
}

/**
 * AI 콘텐츠를 jr_data에 적용 (채용공고 페이지 표시용 fallback)
 * g5_jobs_ai_content의 활성 ai_data를 g5_jobs_register.jr_data에 병합
 * @param int $jr_id
 * @param int|null $aic_id 특정 AI 콘텐츠 ID (null이면 활성 버전 사용)
 * @return bool
 */
function aic_apply_to_jr_data($jr_id, $aic_id = null) {
    if (!_aic_table_exists()) return false;
    $jr_id = (int)$jr_id;
    $row = null;
    if ($aic_id) {
        $aid = (int)$aic_id;
        $row = sql_fetch("SELECT ai_data FROM g5_jobs_ai_content WHERE id = '{$aid}' AND jr_id = '{$jr_id}' LIMIT 1");
    } else {
        $row = sql_fetch("SELECT ai_data FROM g5_jobs_ai_content WHERE jr_id = '{$jr_id}' AND is_active = 1 ORDER BY id DESC LIMIT 1");
    }
    if (!$row || empty($row['ai_data'])) return false;
    $ai_data = json_decode($row['ai_data'], true);
    if (!is_array($ai_data)) return false;

    $has_sections = !empty($ai_data['ai_intro']) || !empty($ai_data['ai_env']) || !empty($ai_data['ai_welfare']) || !empty($ai_data['ai_location']);
    if (!$has_sections && !empty($ai_data['ai_content'])) {
        $parsed = aic_parse_ai_content_to_sections($ai_data['ai_content']);
        foreach ($parsed as $k => $v) {
            if (!isset($ai_data[$k]) || $ai_data[$k] === '') $ai_data[$k] = $v;
        }
    }

    $jr_row = sql_fetch("SELECT jr_data FROM g5_jobs_register WHERE jr_id = '{$jr_id}' LIMIT 1");
    if (!$jr_row) return false;
    $jr_data = $jr_row['jr_data'] ? json_decode($jr_row['jr_data'], true) : array();
    if (!is_array($jr_data)) $jr_data = array();

    $ai_to_desc = array('ai_location'=>'desc_location','ai_env'=>'desc_env','ai_welfare'=>'desc_benefit','ai_qualify'=>'desc_qualify','ai_extra'=>'desc_extra');
    foreach ($ai_data as $k => $v) {
        if (strpos($k, '_') === 0) continue;
        $jr_data[$k] = $v;
        if (isset($ai_to_desc[$k]) && $v !== '' && $v !== null) {
            $jr_data[$ai_to_desc[$k]] = $v;
        }
    }
    $json_esc = sql_escape_string(json_encode($jr_data, JSON_UNESCAPED_UNICODE));
    sql_query("UPDATE g5_jobs_register SET jr_data = '{$json_esc}' WHERE jr_id = '{$jr_id}'");
    return true;
}

/**
 * 버전 이력 조회 (관리자용)
 */
function aic_get_versions($jr_id) {
    if (!_aic_table_exists()) return array();
    $jr_id = (int)$jr_id;
    $result = sql_query("SELECT id, version, ai_tone, is_active, created_at, duration_ms
                         FROM g5_jobs_ai_content
                         WHERE jr_id = '{$jr_id}'
                         ORDER BY version DESC");
    $list = array();
    while ($row = sql_fetch_array($result)) {
        $list[] = $row;
    }
    return $list;
}
