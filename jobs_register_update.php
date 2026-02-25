<?php
/**
 * 채용정보등록 폼 저장 → 입금대기중으로 등록
 */
include_once('./_common.php');

if (!$is_member) {
    alert('로그인 후 이용해 주세요.', G5_BBS_URL.'/login.php?url='.urlencode(G5_URL.'/jobs_register.php'));
}

$jobs_ongoing_url = (defined('G5_URL') && G5_URL) ? rtrim(G5_URL,'/').'/jobs_ongoing.php' : '/jobs_ongoing.php';

// POST 데이터 수신
$job_data = isset($_POST['job_data']) ? stripslashes((string)$_POST['job_data']) : '';
$total_amount = isset($_POST['total_amount']) ? (int)$_POST['total_amount'] : 0;
$ad_period = isset($_POST['ad_period']) ? (int)$_POST['ad_period'] : 30;
$ad_labels = isset($_POST['ad_labels']) ? stripslashes((string)$_POST['ad_labels']) : '';
$ad_labels = trim($ad_labels);

// base64 인코딩된 JSON 또는 raw JSON 또는 폼필드 fallback
$data = null;
if (!empty($job_data)) {
    if (preg_match('/^[A-Za-z0-9+\/=]+$/', trim($job_data))) {
        $decoded = @base64_decode($job_data, true);
        if ($decoded !== false) {
            $data = json_decode($decoded, true);
        }
    }
    if (!$data || !is_array($data)) {
        $data = json_decode($job_data, true);
    }
}
if (!$data || !is_array($data)) {
    // JSON 실패 시 폼 필드에서 직접 추출 (fallback)
    $data = array(
        'job_nickname' => isset($_POST['job_nickname']) ? stripslashes((string)$_POST['job_nickname']) : '',
        'job_company' => isset($_POST['job_company']) ? stripslashes((string)$_POST['job_company']) : '',
        'job_title' => isset($_POST['job_title']) ? stripslashes((string)$_POST['job_title']) : '',
        'desc_location' => isset($_POST['desc_location']) ? stripslashes((string)$_POST['desc_location']) : '',
        'desc_env' => isset($_POST['desc_env']) ? stripslashes((string)$_POST['desc_env']) : '',
        'desc_benefit' => isset($_POST['desc_benefit']) ? stripslashes((string)$_POST['desc_benefit']) : '',
        'desc_qualify' => isset($_POST['desc_qualify']) ? stripslashes((string)$_POST['desc_qualify']) : '',
        'desc_extra' => isset($_POST['desc_extra']) ? stripslashes((string)$_POST['desc_extra']) : ''
    );
}

$nickname = isset($data['job_nickname']) ? clean_xss_tags($data['job_nickname']) : '';
$company = isset($data['job_company']) ? clean_xss_tags($data['job_company']) : '';
$title = isset($data['job_title']) ? clean_xss_tags($data['job_title']) : '';

if (empty($nickname) && empty($company)) {
    $nickname = $member['mb_nick'] ?: $member['mb_id'];
}
$display_name = $nickname ?: $company;
$subject_display = $display_name ? '['.$display_name.']님의 광고글 입니다' : '광고글 입니다';

$end_date = null;
if ($ad_period > 0) {
    $end_date = date('Y-m-d', strtotime('+'.$ad_period.' days'));
}

$jump_count = 0;
if (preg_match('/30일/', $ad_labels)) $jump_count = 300;
elseif (preg_match('/60일/', $ad_labels)) $jump_count = 700;
elseif (preg_match('/90일/', $ad_labels)) $jump_count = 1200;

$jr_table = 'g5_jobs_register';
$tb_check = sql_query("SHOW TABLES LIKE 'g5_jobs_register'", false);
if (!sql_num_rows($tb_check)) {
    $create_sql = "CREATE TABLE IF NOT EXISTS `g5_jobs_register` (
      `jr_id` int unsigned NOT NULL AUTO_INCREMENT,
      `mb_id` varchar(20) NOT NULL DEFAULT '',
      `jr_status` varchar(20) NOT NULL DEFAULT 'pending',
      `jr_payment_confirmed` tinyint NOT NULL DEFAULT 0,
      `jr_approved` tinyint NOT NULL DEFAULT 0,
      `jr_approved_datetime` datetime DEFAULT NULL,
      `jr_nickname` varchar(100) NOT NULL DEFAULT '',
      `jr_company` varchar(200) NOT NULL DEFAULT '',
      `jr_title` varchar(200) NOT NULL DEFAULT '',
      `jr_subject_display` varchar(300) NOT NULL DEFAULT '',
      `jr_data` longtext,
      `jr_ad_labels` varchar(500) NOT NULL DEFAULT '',
      `jr_ad_period` int NOT NULL DEFAULT 30,
      `jr_jump_count` int NOT NULL DEFAULT 0,
      `jr_total_amount` int NOT NULL DEFAULT 0,
      `jr_datetime` datetime NOT NULL,
      `jr_end_date` date DEFAULT NULL,
      PRIMARY KEY (`jr_id`),
      KEY `mb_id` (`mb_id`),
      KEY `jr_status` (`jr_status`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    sql_query($create_sql);
}

$job_data_to_store = (is_array($data) ? json_encode($data, JSON_UNESCAPED_UNICODE) : $job_data);
$jr_data_esc = sql_escape_string($job_data_to_store);
$jr_nick_esc = sql_escape_string($nickname);
$jr_comp_esc = sql_escape_string($company);
$jr_title_esc = sql_escape_string($title);
$jr_subj_esc = sql_escape_string($subject_display);
$mb_id_esc = sql_escape_string($member['mb_id']);
$jr_end_sql = $end_date ? "'".$end_date."'" : 'NULL';
$jr_ad_labels_esc = sql_escape_string($ad_labels);

$sql = "INSERT INTO `{$jr_table}` (mb_id, jr_status, jr_nickname, jr_company, jr_title, jr_subject_display, jr_data, jr_ad_period, jr_jump_count, jr_total_amount, jr_datetime, jr_end_date, jr_ad_labels) VALUES (
  '{$mb_id_esc}', 'pending', '{$jr_nick_esc}', '{$jr_comp_esc}', '{$jr_title_esc}', '{$jr_subj_esc}', '{$jr_data_esc}', ".(int)$ad_period.", ".(int)$jump_count.", ".(int)$total_amount.", '".G5_TIME_YMDHIS."', {$jr_end_sql}, '{$jr_ad_labels_esc}')";
sql_query($sql);

$jr_id = sql_insert_id();
if ($jr_id) {
    alert('입금대기중으로 등록되었습니다. 입금 확인 후 광고가 개재됩니다.', $jobs_ongoing_url);
} else {
    alert('저장 중 오류가 발생했습니다.', G5_URL.'/jobs_register.php');
}
