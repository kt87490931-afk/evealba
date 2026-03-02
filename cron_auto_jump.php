<?php
/**
 * 자동 점프 크론잡 - 매 5분 실행
 * 랜덤 오프셋으로 공정성 확보, 잔여횟수 기반 간격 자동 계산
 */
@error_reporting(E_ALL);
@ini_set('display_errors', '0');

$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/cron_auto_jump.php';

$g5_path = dirname(__FILE__);
if (file_exists($g5_path.'/_common.php')) {
    ob_start();
    include_once($g5_path.'/_common.php');
    ob_end_clean();
}

if (!function_exists('sql_query')) {
    echo json_encode(array('ok'=>0,'msg'=>'DB connection failed'));
    exit;
}

$now = date('Y-m-d H:i:s');
$today = date('Y-m-d');
$start_time = microtime(true);

$tb_check = @sql_query("SHOW COLUMNS FROM g5_jobs_register LIKE 'jr_auto_jump'", false);
if (!$tb_check || !@sql_num_rows($tb_check)) {
    @sql_query("ALTER TABLE g5_jobs_register
        ADD COLUMN IF NOT EXISTS jr_jump_remain INT UNSIGNED NOT NULL DEFAULT 0,
        ADD COLUMN IF NOT EXISTS jr_jump_used INT UNSIGNED NOT NULL DEFAULT 0,
        ADD COLUMN IF NOT EXISTS jr_jump_total INT UNSIGNED NOT NULL DEFAULT 0,
        ADD COLUMN IF NOT EXISTS jr_jump_datetime DATETIME DEFAULT NULL,
        ADD COLUMN IF NOT EXISTS jr_auto_jump TINYINT UNSIGNED NOT NULL DEFAULT 0,
        ADD COLUMN IF NOT EXISTS jr_auto_jump_next DATETIME DEFAULT NULL", false);
}

$log_check = @sql_query("SHOW TABLES LIKE 'g5_jobs_jump_log'", false);
if (!$log_check || !@sql_num_rows($log_check)) {
    @sql_query("CREATE TABLE IF NOT EXISTS g5_jobs_jump_log (
        jl_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        jr_id INT UNSIGNED NOT NULL,
        mb_id VARCHAR(20) NOT NULL DEFAULT '',
        jl_type ENUM('manual','auto') NOT NULL DEFAULT 'manual',
        jl_remain_before INT UNSIGNED NOT NULL DEFAULT 0,
        jl_remain_after INT UNSIGNED NOT NULL DEFAULT 0,
        jl_datetime DATETIME NOT NULL,
        KEY idx_jr_id (jr_id),
        KEY idx_datetime (jl_datetime)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4", false);
}

$sql = "SELECT jr_id, mb_id, jr_jump_remain, jr_jump_used, jr_end_date
    FROM g5_jobs_register
    WHERE jr_auto_jump = 1
      AND jr_jump_remain > 0
      AND jr_status = 'ongoing'
      AND jr_end_date >= '{$today}'
      AND jr_auto_jump_next <= '{$now}'
    ORDER BY jr_auto_jump_next ASC
    LIMIT 100";

$result = sql_query($sql, false);
$jumped = 0;
$errors = 0;

if ($result) {
    while ($row = sql_fetch_array($result)) {
        $jr_id = (int)$row['jr_id'];
        $mb_id_esc = addslashes($row['mb_id']);
        $remain_before = (int)$row['jr_jump_remain'];
        $remain_after = $remain_before - 1;
        $used = (int)$row['jr_jump_used'] + 1;

        $upd = @sql_query("UPDATE g5_jobs_register SET
            jr_jump_remain = '{$remain_after}',
            jr_jump_used = '{$used}',
            jr_jump_datetime = '{$now}'
            WHERE jr_id = '{$jr_id}' AND jr_jump_remain > 0", false);

        if (!$upd || sql_affected_rows() === 0) {
            $errors++;
            continue;
        }

        @sql_query("INSERT INTO g5_jobs_jump_log (jr_id, mb_id, jl_type, jl_remain_before, jl_remain_after, jl_datetime)
            VALUES ('{$jr_id}', '{$mb_id_esc}', 'auto', '{$remain_before}', '{$remain_after}', '{$now}')", false);

        if ($remain_after > 0) {
            $end_date = $row['jr_end_date'];
            $days_left = max(1, (strtotime($end_date) - strtotime($today)) / 86400);
            $mins_left = $days_left * 24 * 60;
            $interval = max(10, floor($mins_left / $remain_after));
            $offset = rand(-5, 5);
            $next_ts = time() + ($interval + $offset) * 60;
            $auto_next = date('Y-m-d H:i:s', $next_ts);
            @sql_query("UPDATE g5_jobs_register SET jr_auto_jump_next = '{$auto_next}' WHERE jr_id = '{$jr_id}'", false);
        } else {
            @sql_query("UPDATE g5_jobs_register SET jr_auto_jump = 0, jr_auto_jump_next = NULL WHERE jr_id = '{$jr_id}'", false);
        }

        $jumped++;
    }
}

$elapsed = round((microtime(true) - $start_time) * 1000);

$health_check = @sql_query("SHOW TABLES LIKE 'g5_jobs_health_log'", false);
if ($health_check && @sql_num_rows($health_check)) {
    $diag = addslashes(json_encode(array(
        'jumped' => $jumped,
        'errors' => $errors,
        'elapsed_ms' => $elapsed,
    ), JSON_UNESCAPED_UNICODE));
    @sql_query("INSERT INTO g5_jobs_health_log (cron_name, last_start, last_end, last_ok, last_msg, rows_affected, diag)
        VALUES ('auto_jump', '{$now}', '".date('Y-m-d H:i:s')."', 1, 'jumped={$jumped} errors={$errors}', '{$jumped}', '{$diag}')
        ON DUPLICATE KEY UPDATE last_start='{$now}', last_end='".date('Y-m-d H:i:s')."', last_ok=1,
        last_msg='jumped={$jumped} errors={$errors}', rows_affected='{$jumped}', diag='{$diag}'", false);
}

echo json_encode(array(
    'ok' => 1,
    'jumped' => $jumped,
    'errors' => $errors,
    'elapsed_ms' => $elapsed,
), JSON_UNESCAPED_UNICODE);
