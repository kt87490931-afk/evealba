<?php
/**
 * 어드민 - Migration 016 (쪽지관리 설정 테이블)
 * g5_ev_memo_config: 회원가입 시 자동 쪽지 on/off 및 메시지 템플릿
 */
$sub_menu = '100100';
require_once './_common.php';

auth_check_menu($auth, $sub_menu, 'w');

$g5['title'] = '마이그레이션 016 실행';
require_once G5_ADMIN_PATH.'/admin.head.php';

$tb = 'g5_ev_memo_config';
$exists = sql_num_rows(sql_query("SHOW TABLES LIKE '{$tb}'", false));

if ($exists) {
    echo '<p>g5_ev_memo_config 테이블이 이미 존재합니다.</p>';
} else {
    $sql = "CREATE TABLE IF NOT EXISTS `{$tb}` (
      emc_id TINYINT UNSIGNED NOT NULL DEFAULT 1 PRIMARY KEY,
      em_join_memo_on TINYINT(1) NOT NULL DEFAULT 0 COMMENT '회원가입 시 자동 쪽지 0:off 1:on',
      em_join_memo_general TEXT DEFAULT NULL COMMENT '일반회원 가입 시 쪽지 내용',
      em_join_memo_biz TEXT DEFAULT NULL COMMENT '기업회원 가입 시 쪽지 내용',
      em_updated DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    $ok = sql_query($sql, false);
    echo '<p>'.($ok ? '<span style="color:green;">[OK]</span> g5_ev_memo_config 테이블 생성됨' : '<span style="color:red;">[FAIL]</span>').'</p>';

    if ($ok) {
        $sql2 = "INSERT INTO `{$tb}` (emc_id, em_join_memo_on, em_join_memo_general, em_join_memo_biz)
          VALUES (1, 0, '이브알바에 가입해 주셔서 감사합니다.', '이브알바에 기업회원으로 가입해 주셔서 감사합니다. 승인 후 서비스를 이용하실 수 있습니다.')";
        $ok2 = sql_query($sql2, false);
        echo '<p>'.($ok2 ? '<span style="color:green;">[OK]</span> 기본 데이터 INSERT' : '<span style="color:orange;">[SKIP]</span> INSERT 실패 또는 이미 존재').'</p>';
    }
}

echo '<p><a href="./eve_memo_manage.php" class="btn btn_01">쪽지관리로 이동</a></p>';
require_once G5_ADMIN_PATH.'/admin.tail.php';
