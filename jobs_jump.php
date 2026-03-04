<?php
/**
 * 수동 점프 API
 * POST: jr_id
 */
@error_reporting(0);
@ini_set('display_errors', '0');
ob_start();

$_jump_success = false;
$_jump_err_out = null;
register_shutdown_function(function () {
    global $_jump_success, $_jump_err_out;
    if ($_jump_success) return;
    if ($_jump_err_out !== null) {
        if (ob_get_level()) ob_end_clean();
        if (!headers_sent()) header('Content-Type: application/json; charset=utf-8');
        echo $_jump_err_out;
        return;
    }
    $e = error_get_last();
    if ($e && in_array($e['type'], array(E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR))) {
        $errMsg = substr($e['message'], 0, 500);
        $logDir = (defined('G5_DATA_PATH') ? G5_DATA_PATH : __DIR__) . '/log';
        if (is_dir($logDir) || @mkdir($logDir, 0755, true)) {
            @file_put_contents($logDir . '/jobs_jump_error.log', date('Y-m-d H:i:s') . ' ' . $errMsg . "\n", FILE_APPEND | LOCK_EX);
        }
        if (ob_get_level()) ob_end_clean();
        if (!headers_sent()) header('Content-Type: application/json; charset=utf-8');
        echo json_encode(array('ok' => 0, 'msg' => '서버 오류가 발생했습니다.', 'err' => $errMsg), JSON_UNESCAPED_UNICODE);
    }
});

$_jump_log_err = function ($msg) {
    $logDir = (defined('G5_DATA_PATH') ? G5_DATA_PATH : __DIR__) . '/log';
    if (is_dir($logDir) || @mkdir($logDir, 0755, true)) {
        @file_put_contents($logDir . '/jobs_jump_error.log', date('Y-m-d H:i:s') . ' ' . $msg . "\n", FILE_APPEND | LOCK_EX);
    }
};
set_exception_handler(function (Throwable $t) use ($_jump_log_err) {
    $err = $t->getMessage() . ' @ ' . basename($t->getFile()) . ':' . $t->getLine();
    if (is_callable($_jump_log_err)) $_jump_log_err($err);
    $GLOBALS['_jump_err_out'] = json_encode(array('ok' => 0, 'msg' => '예외 발생', 'err' => $err), JSON_UNESCAPED_UNICODE);
});

try {
    @chdir(__DIR__);
    $inc = __DIR__ . '/_common.php';
    if (!is_file($inc)) {
        $_jump_err_out = json_encode(array('ok' => 0, 'msg' => '_common.php 없음', 'err' => $inc), JSON_UNESCAPED_UNICODE);
        exit;
    }
    include_once($inc);
} catch (Throwable $t) {
    $_jump_err_out = json_encode(array('ok' => 0, 'msg' => '초기화 오류', 'err' => $t->getMessage() . ' @ ' . $t->getFile() . ':' . $t->getLine()), JSON_UNESCAPED_UNICODE);
    exit;
}
ob_end_clean();

if (!headers_sent()) header('Content-Type: application/json; charset=utf-8');

function _jump_json($data) {
    global $_jump_success;
    $_jump_success = true;
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

try {
if (!$is_member) {
    _jump_json(array('ok' => 0, 'msg' => '로그인이 필요합니다.'));
}

if (empty($member) || empty($member['mb_id'])) {
    _jump_json(array('ok' => 0, 'msg' => '회원 정보를 확인할 수 없습니다.'));
}

$jr_id = isset($_POST['jr_id']) ? (int)$_POST['jr_id'] : 0;
if (!$jr_id) {
    _jump_json(array('ok' => 0, 'msg' => '광고 ID가 필요합니다.'));
}

$mb_id_esc = addslashes($member['mb_id']);

// 점프 컬럼/테이블 부트스트랩 (미마이그레이션 서버 대응)
$col_check = @sql_query("SHOW COLUMNS FROM g5_jobs_register LIKE 'jr_jump_remain'", false);
if (!$col_check || !@sql_num_rows($col_check)) {
    $add_cols = array(
        'jr_jump_remain INT UNSIGNED NOT NULL DEFAULT 0',
        'jr_jump_used INT UNSIGNED NOT NULL DEFAULT 0',
        'jr_jump_total INT UNSIGNED NOT NULL DEFAULT 0',
        'jr_jump_datetime DATETIME DEFAULT NULL',
        'jr_auto_jump TINYINT UNSIGNED NOT NULL DEFAULT 0',
        'jr_auto_jump_next DATETIME DEFAULT NULL'
    );
    foreach ($add_cols as $def) {
        $col_name = preg_replace('/\s+.*/', '', $def);
        $c = @sql_query("SHOW COLUMNS FROM g5_jobs_register LIKE '{$col_name}'", false);
        if (!$c || !@sql_num_rows($c)) {
            @sql_query("ALTER TABLE g5_jobs_register ADD COLUMN {$def}", false);
        }
    }
}
$log_tb = @sql_query("SHOW TABLES LIKE 'g5_jobs_jump_log'", false);
if (!$log_tb || !@sql_num_rows($log_tb)) {
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

$row = sql_fetch("SELECT jr_id, mb_id, jr_status, jr_jump_remain, jr_jump_used, jr_jump_total,
    jr_jump_datetime, jr_end_date, jr_auto_jump, jr_auto_jump_next
    FROM g5_jobs_register WHERE jr_id = '{$jr_id}'");

if (!$row) {
    _jump_json(array('ok' => 0, 'msg' => '존재하지 않는 광고입니다.'));
}
if ($row['mb_id'] !== $member['mb_id']) {
    _jump_json(array('ok' => 0, 'msg' => '본인의 광고만 점프할 수 있습니다.'));
}
if ($row['jr_status'] !== 'ongoing') {
    _jump_json(array('ok' => 0, 'msg' => '진행중인 광고만 점프할 수 있습니다.'));
}

$remain = (int)$row['jr_jump_remain'];
if ($remain <= 0) {
    _jump_json(array('ok' => 0, 'msg' => '잔여 점프 횟수가 없습니다. 추가 구매해 주세요.', 'remain' => 0));
}

$now = date('Y-m-d H:i:s');
$remain_before = $remain;
$remain_after = $remain - 1;
$used = (int)$row['jr_jump_used'] + 1;

sql_query("UPDATE g5_jobs_register SET
    jr_jump_remain = '{$remain_after}',
    jr_jump_used = '{$used}',
    jr_jump_datetime = '{$now}'
    WHERE jr_id = '{$jr_id}' AND jr_jump_remain > 0");

if (sql_affected_rows() === 0) {
    _jump_json(array('ok' => 0, 'msg' => '점프 처리에 실패했습니다. 다시 시도해 주세요.'));
}

@sql_query("INSERT INTO g5_jobs_jump_log (jr_id, mb_id, jl_type, jl_remain_before, jl_remain_after, jl_datetime)
    VALUES ('{$jr_id}', '{$mb_id_esc}', 'manual', '{$remain_before}', '{$remain_after}', '{$now}')", false);

$auto_next = '';
if ((int)$row['jr_auto_jump'] === 1 && $remain_after > 0) {
    $end_date = $row['jr_end_date'];
    $days_remain = max(1, (strtotime($end_date) - strtotime(date('Y-m-d'))) / 86400);
    $mins_remain = $days_remain * 24 * 60;
    $interval = max(10, floor($mins_remain / $remain_after));
    $offset = rand(-5, 5);
    $next_ts = time() + ($interval + $offset) * 60;
    $auto_next = date('Y-m-d H:i:s', $next_ts);
    sql_query("UPDATE g5_jobs_register SET jr_auto_jump_next = '{$auto_next}' WHERE jr_id = '{$jr_id}'");
}

_jump_json(array(
    'ok' => 1,
    'msg' => '점프 완료!',
    'remain' => $remain_after,
    'used' => $used,
    'total' => (int)$row['jr_jump_total'],
    'jump_datetime' => $now,
    'auto_next' => $auto_next
));
} catch (Throwable $t) {
    _jump_json(array('ok' => 0, 'msg' => '처리 중 오류', 'err' => $t->getMessage() . ' @ ' . basename($t->getFile()) . ':' . $t->getLine()));
}
