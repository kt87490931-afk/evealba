<?php
/**
 * 이력서 저장 API (INSERT / UPDATE)
 */
@error_reporting(0);
@ini_set('display_errors', '0');
ob_start();
include_once('./_common.php');
ob_end_clean();

header('Content-Type: application/json; charset=utf-8');

function rs_json($data) {
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

if (!$is_member) {
    rs_json(array('ok' => 0, 'msg' => '회원만 이력서를 등록할 수 있습니다. 로그인해 주세요.'));
}

$rs_table = 'g5_resume';
$tb_check = @sql_query("SHOW TABLES LIKE '{$rs_table}'", false);
if (!$tb_check || !@sql_num_rows($tb_check)) {
    @sql_query("CREATE TABLE IF NOT EXISTS {$rs_table} (
        rs_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        mb_id VARCHAR(20) NOT NULL DEFAULT '',
        rs_title VARCHAR(100) NOT NULL DEFAULT '',
        rs_nick VARCHAR(50) NOT NULL DEFAULT '',
        rs_gender VARCHAR(10) NOT NULL DEFAULT '',
        rs_age TINYINT UNSIGNED NOT NULL DEFAULT 0,
        rs_job1 VARCHAR(30) NOT NULL DEFAULT '',
        rs_job2 VARCHAR(30) NOT NULL DEFAULT '',
        rs_region VARCHAR(20) NOT NULL DEFAULT '',
        rs_region_detail VARCHAR(30) NOT NULL DEFAULT '',
        rs_work_region VARCHAR(20) NOT NULL DEFAULT '',
        rs_salary_type VARCHAR(10) NOT NULL DEFAULT '',
        rs_salary_amt INT UNSIGNED NOT NULL DEFAULT 0,
        rs_status VARCHAR(10) NOT NULL DEFAULT 'active',
        rs_photo VARCHAR(255) NOT NULL DEFAULT '',
        rs_data LONGTEXT,
        rs_datetime DATETIME NOT NULL,
        rs_update DATETIME DEFAULT NULL,
        KEY idx_mb_id (mb_id),
        KEY idx_status (rs_status),
        KEY idx_job1 (rs_job1),
        KEY idx_region (rs_region),
        KEY idx_datetime (rs_datetime)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4", false);
}

$mb_id_esc = addslashes($member['mb_id']);

$title      = isset($_POST['title']) ? trim($_POST['title']) : '';
$nick       = isset($_POST['nick']) ? trim($_POST['nick']) : $member['mb_nick'];
$gender     = isset($_POST['gender']) ? trim($_POST['gender']) : '';
$age        = isset($_POST['age']) ? (int)$_POST['age'] : 0;
$job1       = isset($_POST['job1']) ? trim($_POST['job1']) : '';
$job2       = isset($_POST['job2']) ? trim($_POST['job2']) : '';
$region     = isset($_POST['region']) ? trim($_POST['region']) : '';
$region_detail = isset($_POST['region_detail']) ? trim($_POST['region_detail']) : '';
$work_region = isset($_POST['work_region']) ? trim($_POST['work_region']) : '';
$salary_type = isset($_POST['salary_type']) ? trim($_POST['salary_type']) : '';
$salary_amt  = isset($_POST['salary_amt']) ? (int)preg_replace('/[^0-9]/', '', $_POST['salary_amt']) : 0;

if (!$title) rs_json(array('ok' => 0, 'msg' => '이력서 제목을 입력해 주세요.'));
if (!$age)   rs_json(array('ok' => 0, 'msg' => '나이를 선택해 주세요.'));

$all_data = array();
foreach ($_POST as $k => $v) {
    if ($k === 'photo_file') continue;
    $all_data[$k] = $v;
}

$photo_path = '';
if (isset($_FILES['photo_file']) && $_FILES['photo_file']['error'] === UPLOAD_ERR_OK) {
    $upload_dir = G5_DATA_PATH . '/resume';
    if (!is_dir($upload_dir)) @mkdir($upload_dir, 0755, true);
    $ext = strtolower(pathinfo($_FILES['photo_file']['name'], PATHINFO_EXTENSION));
    $allowed = array('jpg', 'jpeg', 'png', 'gif', 'webp');
    if (in_array($ext, $allowed)) {
        $fname = $mb_id_esc . '_' . time() . '.' . $ext;
        $dest = $upload_dir . '/' . $fname;
        if (move_uploaded_file($_FILES['photo_file']['tmp_name'], $dest)) {
            $photo_path = G5_DATA_URL . '/resume/' . $fname;
            $all_data['photo_url'] = $photo_path;
        }
    }
}

$rs_data_json = addslashes(json_encode($all_data, JSON_UNESCAPED_UNICODE));

$rs_id = isset($_POST['rs_id']) ? (int)$_POST['rs_id'] : 0;

if ($rs_id > 0) {
    $existing = sql_fetch("SELECT rs_id, mb_id, rs_photo FROM {$rs_table} WHERE rs_id = '{$rs_id}' AND mb_id = '{$mb_id_esc}'");
    if (!$existing) {
        rs_json(array('ok' => 0, 'msg' => '수정할 수 있는 이력서가 아닙니다.'));
    }
    if (!$photo_path && $existing['rs_photo']) {
        $photo_path = $existing['rs_photo'];
    }
    $sql = "UPDATE {$rs_table} SET
        rs_title = '".addslashes($title)."',
        rs_nick = '".addslashes($nick)."',
        rs_gender = '".addslashes($gender)."',
        rs_age = '{$age}',
        rs_job1 = '".addslashes($job1)."',
        rs_job2 = '".addslashes($job2)."',
        rs_region = '".addslashes($region)."',
        rs_region_detail = '".addslashes($region_detail)."',
        rs_work_region = '".addslashes($work_region)."',
        rs_salary_type = '".addslashes($salary_type)."',
        rs_salary_amt = '{$salary_amt}',
        rs_photo = '".addslashes($photo_path)."',
        rs_data = '{$rs_data_json}',
        rs_update = '".G5_TIME_YMDHIS."'
    WHERE rs_id = '{$rs_id}' AND mb_id = '{$mb_id_esc}'";
    sql_query($sql);
    rs_json(array('ok' => 1, 'rs_id' => $rs_id, 'msg' => '이력서가 수정되었습니다.'));
} else {
    $dup = sql_fetch("SELECT rs_id FROM {$rs_table} WHERE mb_id = '{$mb_id_esc}' AND rs_status = 'active'");
    if ($dup) {
        rs_json(array('ok' => 0, 'msg' => '이미 등록된 이력서가 있습니다. 기존 이력서를 수정해 주세요.', 'rs_id' => (int)$dup['rs_id']));
    }
    $sql = "INSERT INTO {$rs_table} SET
        mb_id = '{$mb_id_esc}',
        rs_title = '".addslashes($title)."',
        rs_nick = '".addslashes($nick)."',
        rs_gender = '".addslashes($gender)."',
        rs_age = '{$age}',
        rs_job1 = '".addslashes($job1)."',
        rs_job2 = '".addslashes($job2)."',
        rs_region = '".addslashes($region)."',
        rs_region_detail = '".addslashes($region_detail)."',
        rs_work_region = '".addslashes($work_region)."',
        rs_salary_type = '".addslashes($salary_type)."',
        rs_salary_amt = '{$salary_amt}',
        rs_photo = '".addslashes($photo_path)."',
        rs_data = '{$rs_data_json}',
        rs_status = 'active',
        rs_datetime = '".G5_TIME_YMDHIS."'";
    sql_query($sql);
    $new_id = sql_insert_id();
    rs_json(array('ok' => 1, 'rs_id' => (int)$new_id, 'msg' => '이력서가 등록되었습니다.'));
}
