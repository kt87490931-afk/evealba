<?php
/**
 * 어드민 - Migration 012 수동 실행 (쿠폰·썸네일옵션 테이블)
 * adm/run_migration_012.php
 */
$sub_menu = '100100';
require_once './_common.php';

auth_check_menu($auth, $sub_menu, 'w');

$g5['title'] = '마이그레이션 012 실행';
require_once G5_ADMIN_PATH.'/admin.head.php';

$base = dirname(dirname(__FILE__));
$sqlFile = $base . '/migrations/012_create_ev_coupon_and_thumb_paid.sql';

echo '<div class="local_desc01 local_desc">';
echo '<p>g5_ev_coupon, g5_ev_coupon_issue, g5_ev_coupon_use, g5_jobs_thumb_option_paid 테이블을 생성합니다.</p>';
echo '</div>';

if (!file_exists($sqlFile)) {
    echo '<p style="color:#c00;">migrations/012_create_ev_coupon_and_thumb_paid.sql 파일이 없습니다.</p>';
    require_once G5_ADMIN_PATH.'/admin.tail.php';
    exit;
}

$sql = file_get_contents($sqlFile);
$queries = array_filter(array_map('trim', explode(';', $sql)));
$results = array();
foreach ($queries as $q) {
    if ($q === '' || preg_match('/^\s*--/', $q)) continue;
    $r = @sql_query($q, false);
    $results[] = array(
        'ok' => ($r !== false),
        'preview' => substr(str_replace("\n", " ", $q), 0, 80) . '...'
    );
}

echo '<div class="tbl_head01 tbl_wrap"><table><tbody>';
foreach ($results as $res) {
    echo '<tr><td>' . ($res['ok'] ? '<span style="color:green;">[OK]</span>' : '<span style="color:red;">[FAIL]</span>') . '</td><td>' . htmlspecialchars($res['preview']) . '</td></tr>';
}
echo '</tbody></table></div>';

$tables = array('g5_ev_coupon', 'g5_ev_coupon_issue', 'g5_ev_coupon_use', 'g5_jobs_thumb_option_paid');
echo '<div style="margin-top:16px;"><strong>테이블 확인:</strong><ul>';
foreach ($tables as $t) {
    $chk = sql_query("SHOW TABLES LIKE '{$t}'", false);
    $exists = ($chk && sql_num_rows($chk)) ? true : false;
    echo '<li>' . $t . ': ' . ($exists ? '<span style="color:green;">존재함</span>' : '<span style="color:red;">없음</span>') . '</li>';
}
echo '</ul></div>';
echo '<p><a href="./eve_thumb_shop.php" class="btn btn_01">썸네일상점 관리로 이동</a> <a href="./eve_coupon_list.php" class="btn btn_02">쿠폰 관리로 이동</a></p>';

require_once G5_ADMIN_PATH.'/admin.tail.php';
