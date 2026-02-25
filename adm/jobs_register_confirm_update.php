<?php
/**
 * 어드민 - 채용정보등록 입금확인 처리
 * 입금확인 시 jr_payment_confirmed=1 설정 (회원 진행중 페이지에 입금확인 표시)
 */
$sub_menu = '910100';
require_once './_common.php';

auth_check_menu($auth, $sub_menu, 'w');
check_admin_token();

$jr_ids = array();
if (isset($_POST['jr_ids']) && $_POST['jr_ids'] !== '') {
    $jr_ids = array_map('intval', array_filter(explode(',', $_POST['jr_ids'])));
} elseif (isset($_GET['jr_ids']) && $_GET['jr_ids'] !== '') {
    $jr_ids = array_map('intval', array_filter(explode(',', $_GET['jr_ids'])));
} elseif (isset($_POST['chk']) && is_array($_POST['chk'])) {
    foreach ($_POST['chk'] as $v) { $id = (int)$v; if ($id) $jr_ids[] = $id; }
}
$jr_id = isset($_REQUEST['jr_id']) ? (int)$_REQUEST['jr_id'] : 0;
if ($jr_id) {
    $jr_ids = array($jr_id);
}

if (empty($jr_ids)) {
    alert('입금확인할 항목을 선택하세요.', './jobs_register_list.php');
}

$confirm_ok = 0;
$ai_errors = array();
foreach ($jr_ids as $k => $v) {
    $id = (int)(is_array($v) ? $v : $v);
    if (!$id) continue;

    $row = sql_fetch("SELECT jr_id, jr_status, jr_payment_confirmed, jr_data, jr_subject_display, jr_nickname, jr_company, jr_title, jr_ad_period FROM g5_jobs_register WHERE jr_id = '{$id}'");
    if (!$row) continue;
    if ($row['jr_payment_confirmed']) continue;
    if ($row['jr_status'] !== 'pending') continue;

    sql_query("UPDATE g5_jobs_register SET jr_payment_confirmed = 1 WHERE jr_id = '{$id}'");
    $confirm_ok++;

    // 입금확인 시 AI 소개글 자동 생성 (1회만)
    $jr_data = $row['jr_data'] ? json_decode($row['jr_data'], true) : array();
    if (is_array($jr_data) && empty($jr_data['ai_content'])) {
        if (!defined('G5_LIB_PATH') || !file_exists(G5_LIB_PATH . '/gemini_api.lib.php')) {
            $ai_errors[] = "jr_id {$id}: gemini_api.lib.php 없음 (G5_LIB_PATH=" . (defined('G5_LIB_PATH') ? G5_LIB_PATH : '미정의') . ")";
        } else {
            include_once G5_LIB_PATH . '/gemini_api.lib.php';
            $nickname = isset($jr_data['job_nickname']) ? trim($jr_data['job_nickname']) : $row['jr_nickname'];
            $title = isset($jr_data['job_title']) ? trim($jr_data['job_title']) : $row['jr_title'];
            $formData = array(
                'nickname' => $nickname,
                'title' => $title,
                'location' => isset($jr_data['desc_location']) ? trim($jr_data['desc_location']) : '',
                'environment' => isset($jr_data['desc_env']) ? trim($jr_data['desc_env']) : '',
                'benefits' => isset($jr_data['desc_benefit']) ? trim($jr_data['desc_benefit']) : '',
                'details' => trim((isset($jr_data['desc_qualify']) ? $jr_data['desc_qualify'] : '') . "\n" . (isset($jr_data['desc_extra']) ? $jr_data['desc_extra'] : '')),
            );
            $ai_tone = isset($jr_data['ai_tone']) && in_array($jr_data['ai_tone'], array('unnie', 'boss_male', 'pro')) ? $jr_data['ai_tone'] : 'unnie';
            $ai_content = generate_store_description_gemini($formData, $ai_tone);
            if ($ai_content && strpos($ai_content, '오류') === false && strpos($ai_content, '설정') === false) {
                $jr_data['ai_content'] = $ai_content;
                $first_line = strtok($ai_content, "\n");
                $ai_title = $first_line ? mb_substr(trim($first_line), 0, 80) : $row['jr_subject_display'];
                $jr_data_esc = sql_escape_string(json_encode($jr_data, JSON_UNESCAPED_UNICODE));
                $ai_title_esc = sql_escape_string($ai_title);
                sql_query("UPDATE g5_jobs_register SET jr_data = '{$jr_data_esc}', jr_subject_display = '{$ai_title_esc}' WHERE jr_id = '{$id}'");
            } else {
                $ai_errors[] = "jr_id {$id}: " . ($ai_content ?: 'API 응답 없음');
            }
        }
    }
}

$msg = $confirm_ok ? $confirm_ok . '건 입금확인 완료.' : '입금확인할 건이 없습니다.';
if (!empty($ai_errors)) {
    $msg .= ' [AI생성실패: ' . implode(' / ', array_map(function($e){ return mb_substr($e, 0, 80); }, $ai_errors)) . ']';
}
alert($msg, './jobs_register_list.php');
