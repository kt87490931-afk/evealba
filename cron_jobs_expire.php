<?php
// 채용광고 자동 마감 크론잡
// 매일 0시 5분 실행 권장: 5 0 * * * php /var/www/evealba/cron_jobs_expire.php
// jr_end_date가 지난 ongoing 건을 ended로 변경
define('_GNUBOARD_', true);

$g5_path = __DIR__;
if (file_exists($g5_path . '/common.php')) {
    include_once($g5_path . '/common.php');
} else {
    die('common.php not found');
}

$today = date('Y-m-d');
$start = microtime(true);

$sql = "UPDATE g5_jobs_register
        SET jr_status = 'ended'
        WHERE jr_status = 'ongoing'
          AND jr_end_date IS NOT NULL
          AND jr_end_date < '{$today}'";

$result = sql_query($sql, false);
$affected = mysql_affected_rows_compat();

$duration = round((microtime(true) - $start) * 1000);

$log = array(
    'time'     => date('Y-m-d H:i:s'),
    'affected' => $affected,
    'duration' => $duration . 'ms',
    'ok'       => 1
);

if (function_exists('sql_query')) {
    $tb_check = sql_query("SHOW TABLES LIKE 'g5_jobs_health_log'", false);
    if ($tb_check && sql_num_rows($tb_check)) {
        $msg = $affected > 0 ? "{$affected}건 마감 처리" : '마감 대상 없음';
        $diag = sql_escape_string(json_encode($log, JSON_UNESCAPED_UNICODE));
        sql_query("INSERT INTO g5_jobs_health_log (jhl_task, jhl_start, jhl_end, jhl_ok, jhl_msg, jhl_diag)
                   VALUES ('cron_expire', NOW(), NOW(), 1, '{$msg}', '{$diag}')", false);
    }
}

echo json_encode($log, JSON_UNESCAPED_UNICODE) . "\n";

function mysql_affected_rows_compat() {
    if (function_exists('mysqli_affected_rows')) {
        global $g5;
        if (isset($g5['connect_db']) && $g5['connect_db']) {
            return mysqli_affected_rows($g5['connect_db']);
        }
    }
    return 0;
}
