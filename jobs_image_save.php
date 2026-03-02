<?php
/**
 * 채용정보 업소 이미지 저장 (AJAX + multipart)
 * POST: jr_id, img_file_0~4 (file), img_caption_0~4, img_url_0~4, img_removed_0~4
 * jr_data.jr_images[] 에 [{url, caption}] 으로 저장
 */
include_once('./_common.php');

header('Content-Type: application/json; charset=utf-8');

$result = array('ok' => 0, 'msg' => '');

if (!$is_member) {
    $result['msg'] = '로그인 후 이용해 주세요.';
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
    exit;
}

$jr_id = isset($_POST['jr_id']) ? (int)$_POST['jr_id'] : 0;
if (!$jr_id) {
    $result['msg'] = '잘못된 요청입니다.';
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
    exit;
}

$mb_id_esc = sql_escape_string($member['mb_id']);
$row = sql_fetch("SELECT jr_id, jr_data FROM g5_jobs_register WHERE jr_id = '{$jr_id}' AND mb_id = '{$mb_id_esc}'");
if (!$row) {
    $result['msg'] = '권한이 없거나 데이터가 없습니다.';
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
    exit;
}

$jr_data = $row['jr_data'] ? json_decode($row['jr_data'], true) : array();
if (!is_array($jr_data)) $jr_data = array();

$upload_dir = G5_DATA_PATH . '/jobs_images/' . $jr_id;
$upload_url = G5_DATA_URL . '/jobs_images/' . $jr_id;
if (!is_dir($upload_dir)) {
    @mkdir($upload_dir, 0755, true);
}

$existing = isset($jr_data['jr_images']) && is_array($jr_data['jr_images']) ? $jr_data['jr_images'] : array();
while (count($existing) < 5) $existing[] = array('url' => '', 'caption' => '');

$allowed_ext = array('jpg', 'jpeg', 'png', 'gif', 'webp');
$max_size = 10 * 1024 * 1024;

for ($i = 0; $i < 5; $i++) {
    $removed = isset($_POST['img_removed_' . $i]) && $_POST['img_removed_' . $i] === '1';
    if ($removed) {
        if (!empty($existing[$i]['url']) && !empty($existing[$i]['_file'])) {
            $old_path = $upload_dir . '/' . $existing[$i]['_file'];
            if (file_exists($old_path)) @unlink($old_path);
        }
        $existing[$i] = array('url' => '', 'caption' => '');
        continue;
    }

    $caption = isset($_POST['img_caption_' . $i]) ? clean_xss_tags(trim((string)$_POST['img_caption_' . $i])) : '';
    $existing[$i]['caption'] = $caption;

    if (isset($_FILES['img_file_' . $i]) && $_FILES['img_file_' . $i]['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['img_file_' . $i];
        if ($file['size'] > $max_size) {
            $result['msg'] = '이미지 ' . ($i + 1) . '의 파일 크기가 10MB를 초과합니다.';
            echo json_encode($result, JSON_UNESCAPED_UNICODE);
            exit;
        }
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed_ext)) {
            $result['msg'] = '이미지 ' . ($i + 1) . ': 허용되지 않는 파일 형식입니다. (jpg, png, gif, webp)';
            echo json_encode($result, JSON_UNESCAPED_UNICODE);
            exit;
        }
        if (!empty($existing[$i]['_file'])) {
            $old_path = $upload_dir . '/' . $existing[$i]['_file'];
            if (file_exists($old_path)) @unlink($old_path);
        }
        $filename = 'img_' . $i . '_' . time() . '_' . mt_rand(1000, 9999) . '.' . $ext;
        $dest = $upload_dir . '/' . $filename;
        if (move_uploaded_file($file['tmp_name'], $dest)) {
            $existing[$i]['url'] = $upload_url . '/' . $filename;
            $existing[$i]['_file'] = $filename;
        } else {
            $result['msg'] = '이미지 ' . ($i + 1) . ' 업로드에 실패했습니다.';
            echo json_encode($result, JSON_UNESCAPED_UNICODE);
            exit;
        }
    }
}

$clean_images = array();
foreach ($existing as $img) {
    $clean_images[] = array(
        'url' => isset($img['url']) ? $img['url'] : '',
        'caption' => isset($img['caption']) ? $img['caption'] : '',
        '_file' => isset($img['_file']) ? $img['_file'] : ''
    );
}

$jr_data['jr_images'] = $clean_images;
$jr_data_esc = sql_escape_string(json_encode($jr_data, JSON_UNESCAPED_UNICODE));
sql_query("UPDATE g5_jobs_register SET jr_data = '{$jr_data_esc}' WHERE jr_id = '{$jr_id}'");

$response_images = array();
foreach ($clean_images as $img) {
    $response_images[] = array('url' => $img['url'], 'caption' => $img['caption']);
}

$result['ok'] = 1;
$result['msg'] = '이미지가 저장되었습니다.';
$result['images'] = $response_images;
echo json_encode($result, JSON_UNESCAPED_UNICODE);
