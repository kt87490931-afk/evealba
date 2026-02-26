<?php
/**
 * 잘못 저장된 ai_content(대기열 에러 메시지) 초기화 및 큐 재등록
 * 1회 실행 후 삭제 권장
 */
$sub_menu = '910100';
require_once './_common.php';
auth_check_menu($auth, $sub_menu, 'w');

$fixed = 0;
$jr_ids = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_admin_token();
    $sql = "SELECT jr_id, jr_data, jr_subject_display, jr_nickname FROM g5_jobs_register WHERE jr_data LIKE '%대기열이 많습니다%' OR jr_data LIKE '%큐 락%' OR jr_subject_display LIKE '%대기열%'";
    $res = sql_query($sql);
    while ($row = sql_fetch_array($res)) {
        $jr_id = (int)$row['jr_id'];
        $jr_data = $row['jr_data'] ? json_decode($row['jr_data'], true) : array();
        if (!is_array($jr_data)) continue;
        $bad_subject = (strpos($row['jr_subject_display'], '대기열') !== false || strpos($row['jr_subject_display'], '큐 락') !== false);
        $bad_content = !empty($jr_data['ai_content']) && (strpos($jr_data['ai_content'], '대기열') !== false || strpos($jr_data['ai_content'], '큐 락') !== false);
        if (!$bad_subject && !$bad_content) continue;

        unset($jr_data['ai_content']);
        $nick = isset($jr_data['job_nickname']) ? trim($jr_data['job_nickname']) : $row['jr_nickname'];
        $subject_new = "[{$nick}]님의 광고글 입니다";
        $jr_data_esc = sql_escape_string(json_encode($jr_data, JSON_UNESCAPED_UNICODE));
        $subject_esc = sql_escape_string($subject_new);
        sql_query("UPDATE g5_jobs_register SET jr_data = '{$jr_data_esc}', jr_subject_display = '{$subject_esc}' WHERE jr_id = '{$jr_id}'");

        $tbq = sql_query("SHOW TABLES LIKE 'g5_jobs_ai_queue'", false);
        if (sql_num_rows($tbq)) {
            sql_query("DELETE FROM g5_jobs_ai_queue WHERE jr_id = '{$jr_id}'");
            sql_query("INSERT INTO g5_jobs_ai_queue (jr_id, status, created_at) VALUES ('{$jr_id}', 'pending', '".G5_TIME_YMDHIS."')");
        }
        $jr_ids[] = $jr_id;
        $fixed++;
    }
    $msg = $fixed ? "{$fixed}건 초기화 완료. (jr_id: " . implode(', ', $jr_ids) . ") 큐에 재등록되었습니다." : "초기화할 항목이 없습니다.";
    alert($msg, './jobs_register_list.php');
    exit;
}
$g5['title'] = 'AI 잘못 저장 데이터 초기화';
include_once './_head.php';
?>
<p>대기열 에러 메시지가 제목/내용으로 잘못 저장된 건을 초기화하고 큐에 재등록합니다.</p>
<form method="post">
<input type="hidden" name="token" value="<?php echo isset($token) ? $token : ''; ?>">
<button type="submit" class="btn btn_02">실행</button>
<a href="./jobs_register_list.php" class="btn btn_01">목록</a>
</form>
<?php include_once './_tail.php';
