<?php
/**
 * AI 소개글 생성 대기열 워커 (크론 실행)
 * 무료 한도(분당 15회) 준수를 위해 한 번에 3건, 20초 간격 처리
 * 사용법: php jobs_ai_queue_process.php [--limit=N]
 * 크론 예: (매2분) 0-59/2 * * * * cd /var/www/evealba && php jobs_ai_queue_process.php --limit=3
 */
if (php_sapi_name() === 'cli' && empty($_SERVER['SCRIPT_FILENAME'])) {
    $_SERVER['SCRIPT_FILENAME'] = __FILE__;
}
$limit = 3;
foreach ($argv ?? [] as $a) {
    if (preg_match('/^--limit=(\d+)$/', $a, $m)) {
        $limit = (int)$m[1];
        break;
    }
}

define('_GNUBOARD_', true);
$g5_path = ['path' => __DIR__];
include_once __DIR__ . '/common.php';

$log_dir = defined('G5_DATA_PATH') ? G5_DATA_PATH . '/log' : __DIR__ . '/data/log';
if (!is_dir($log_dir)) @mkdir($log_dir, 0755, true);
$log_file = $log_dir . '/gemini_ai_queue.log';

function _queue_log($msg) {
    global $log_file;
    $line = date('Y-m-d H:i:s') . ' ' . $msg . "\n";
    @file_put_contents($log_file, $line, FILE_APPEND | LOCK_EX);
}

$tb_check = sql_query("SHOW TABLES LIKE 'g5_jobs_ai_queue'", false);
if (!sql_num_rows($tb_check)) {
    $create_sql = "CREATE TABLE IF NOT EXISTS `g5_jobs_ai_queue` (
      `id` int unsigned NOT NULL AUTO_INCREMENT,
      `jr_id` int unsigned NOT NULL,
      `status` varchar(20) NOT NULL DEFAULT 'pending',
      `retry_count` int NOT NULL DEFAULT 0,
      `error_msg` varchar(500) NOT NULL DEFAULT '',
      `created_at` datetime NOT NULL,
      `processed_at` datetime DEFAULT NULL,
      PRIMARY KEY (`id`),
      KEY `status` (`status`),
      KEY `jr_id` (`jr_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    sql_query($create_sql);
    _queue_log("TABLE_CREATED g5_jobs_ai_queue");
}

if (!file_exists(G5_LIB_PATH . '/gemini_api.lib.php')) {
    _queue_log("ERROR gemini_api.lib.php not found");
    exit(1);
}
include_once G5_LIB_PATH . '/gemini_api.lib.php';

$t_start = microtime(true);
$processed = 0;
for ($i = 0; $i < $limit; $i++) {
    $row = sql_fetch("SELECT id, jr_id FROM g5_jobs_ai_queue WHERE status = 'pending' ORDER BY id ASC LIMIT 1");
    if (!$row) break;

    $qid = (int)$row['id'];
    $jr_id = (int)$row['jr_id'];
    sql_query("UPDATE g5_jobs_ai_queue SET status = 'processing' WHERE id = '{$qid}'");

    $jr = sql_fetch("SELECT jr_id, jr_data, jr_subject_display, jr_nickname, jr_company, jr_title FROM g5_jobs_register WHERE jr_id = '{$jr_id}'");
    if (!$jr) {
        sql_query("UPDATE g5_jobs_ai_queue SET status = 'failed', error_msg = 'jr not found', processed_at = '".G5_TIME_YMDHIS."' WHERE id = '{$qid}'");
        _queue_log("SKIP id={$qid} jr_id={$jr_id} jr_not_found");
        continue;
    }

    $jr_data = $jr['jr_data'] ? json_decode($jr['jr_data'], true) : array();
    if (!is_array($jr_data) || !empty($jr_data['ai_content']) || !empty($jr_data['ai_intro'])) {
        sql_query("UPDATE g5_jobs_ai_queue SET status = 'done', processed_at = '".G5_TIME_YMDHIS."' WHERE id = '{$qid}'");
        _queue_log("SKIP id={$qid} jr_id={$jr_id} already_has_ai");
        continue;
    }

    $salary_disp = '';
    if (!empty($jr_data['job_salary_type'])) {
        $st = $jr_data['job_salary_type'];
        $sa = isset($jr_data['job_salary_amt']) ? preg_replace('/[^0-9]/', '', $jr_data['job_salary_amt']) : '';
        $salary_disp = ($st === '급여협의') ? '급여협의' : $st . ($sa ? ' ' . number_format((int)$sa) . '원' : '');
    }
    $sns_parts = array();
    if (!empty($jr_data['job_kakao'])) $sns_parts[] = '카카오:' . $jr_data['job_kakao'];
    if (!empty($jr_data['job_line'])) $sns_parts[] = '라인:' . $jr_data['job_line'];
    if (!empty($jr_data['job_telegram'])) $sns_parts[] = '텔레그램:' . $jr_data['job_telegram'];
    $region_disp = '';
    if (!empty($jr_data['job_work_region_1'])) {
        $r1 = isset($jr_data['job_work_region_1']) ? trim($jr_data['job_work_region_1']) : '';
        $d1 = isset($jr_data['job_work_region_detail_1']) ? trim($jr_data['job_work_region_detail_1']) : '';
        $region_disp = $r1 . ($d1 ? ' ' . $d1 : '');
    }
    $job1 = isset($jr_data['job_job1']) ? trim($jr_data['job_job1']) : '';
    $job2 = isset($jr_data['job_job2']) ? trim($jr_data['job_job2']) : '';
    $jobtype_disp = trim(implode(' / ', array_filter(array($job1, $job2))));
    $formData = array(
        'nickname' => isset($jr_data['job_nickname']) ? trim($jr_data['job_nickname']) : $jr['jr_nickname'],
        'title' => isset($jr_data['job_title']) ? trim($jr_data['job_title']) : $jr['jr_title'],
        'location' => isset($jr_data['desc_location']) ? trim($jr_data['desc_location']) : '',
        'environment' => isset($jr_data['desc_env']) ? trim($jr_data['desc_env']) : '',
        'benefits' => isset($jr_data['desc_benefit']) ? trim($jr_data['desc_benefit']) : '',
        'details' => trim((isset($jr_data['desc_qualify']) ? $jr_data['desc_qualify'] : '') . "\n" . (isset($jr_data['desc_extra']) ? $jr_data['desc_extra'] : '')),
        'contact' => isset($jr_data['job_contact']) ? trim($jr_data['job_contact']) : '',
        'sns' => implode(', ', $sns_parts),
        'salary' => $salary_disp,
        'region' => $region_disp,
        'jobtype' => $jobtype_disp,
    );
    $ai_tone = isset($jr_data['ai_tone']) && in_array($jr_data['ai_tone'], array('unnie', 'boss_male', 'pro')) ? $jr_data['ai_tone'] : 'unnie';

    $sections = generate_store_description_gemini_sections($formData, $ai_tone);
    $use_sections = is_array($sections) && !isset($sections['error']);
    $ai_title = $jr['jr_subject_display'];
    $save_ok = false;
    $err_short = '';

    if ($use_sections) {
        $jr_data['ai_intro'] = $sections['ai_intro'];
        $jr_data['ai_location'] = $sections['ai_location'];
        $jr_data['ai_env'] = $sections['ai_env'];
        $jr_data['ai_benefit'] = $sections['ai_benefit'];
        $jr_data['ai_wrapup'] = $sections['ai_wrapup'];
        $first_line = strtok($sections['ai_intro'], "\n");
        $ai_title = $first_line ? mb_substr(trim($first_line), 0, 80) : $jr['jr_subject_display'];
        $save_ok = true;
    } else {
        $ai_content = generate_store_description_gemini($formData, $ai_tone);
        $is_err = (strpos($ai_content, '오류') !== false || strpos($ai_content, '설정') !== false || strpos($ai_content, '대기열') !== false || strpos($ai_content, '큐 락') !== false);
        if ($ai_content && !$is_err) {
            $jr_data['ai_content'] = $ai_content;
            $first_line = strtok($ai_content, "\n");
            $ai_title = $first_line ? mb_substr(trim($first_line), 0, 80) : $jr['jr_subject_display'];
            $save_ok = true;
        } else {
            $err_short = mb_substr(isset($sections['error']) ? $sections['error'] : ($ai_content ?: 'API 응답 없음'), 0, 200);
        }
    }

    if ($save_ok) {
        $jr_data_esc = sql_escape_string(json_encode($jr_data, JSON_UNESCAPED_UNICODE));
        $ai_title_esc = sql_escape_string($ai_title);
        sql_query("UPDATE g5_jobs_register SET jr_data = '{$jr_data_esc}', jr_subject_display = '{$ai_title_esc}' WHERE jr_id = '{$jr_id}'");
        sql_query("UPDATE g5_jobs_ai_queue SET status = 'done', processed_at = '".G5_TIME_YMDHIS."' WHERE id = '{$qid}'");
        _queue_log("OK id={$qid} jr_id={$jr_id} sections=" . ($use_sections ? '1' : '0'));
        $processed++;
    } else {
        $ret_row = sql_fetch("SELECT retry_count FROM g5_jobs_ai_queue WHERE id = '{$qid}'");
        $retry = isset($ret_row['retry_count']) ? (int)$ret_row['retry_count'] : 0;
        $err_short = mb_substr($err_short ?: 'API 응답 없음', 0, 200);
        $err_esc = sql_escape_string($err_short);
        $is_retryable = (strpos($err_short, '429') !== false || stripos($err_short, 'quota') !== false || stripos($err_short, 'RESOURCE_EXHAUSTED') !== false || strpos($err_short, '대기열') !== false || strpos($err_short, '큐 락') !== false);
        if ($retry < 3 && $is_retryable) {
            sql_query("UPDATE g5_jobs_ai_queue SET status = 'pending', retry_count = retry_count + 1, error_msg = '{$err_esc}' WHERE id = '{$qid}'");
            _queue_log("RETRY id={$qid} jr_id={$jr_id} retry=".($retry+1)." msg={$err_short}");
        } else {
            sql_query("UPDATE g5_jobs_ai_queue SET status = 'failed', error_msg = '{$err_esc}', processed_at = '".G5_TIME_YMDHIS."' WHERE id = '{$qid}'");
            _queue_log("FAIL id={$qid} jr_id={$jr_id} msg={$err_short}");
        }
    }

    // 무료 한도(분당 15회) 준수: 건당 20초 간격
    sleep(20);
}

$t_end = microtime(true);
$duration_ms = (int)(($t_end - $t_start) * 1000);
$diag = json_encode([
    'processed' => $processed,
    'limit' => $limit,
    'duration_ms' => $duration_ms,
    'ok' => 1,
], JSON_UNESCAPED_UNICODE);
_queue_log("HEALTH start=" . date('Y-m-d H:i:s', (int)$t_start) . " end=" . date('Y-m-d H:i:s', (int)$t_end) . " duration_ms={$duration_ms} ok=1 msg=done diag={$diag}");
echo "Processed: {$processed}\n";
