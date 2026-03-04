<?php
/**
 * 어드민 - Migration 012 (쿠폰·썸네일옵션 테이블 생성)
 * g5_ev_coupon, g5_ev_coupon_issue, g5_ev_coupon_use, g5_jobs_thumb_option_paid
 */
$sub_menu = '100100';
require_once './_common.php';

auth_check_menu($auth, $sub_menu, 'w');

$g5['title'] = '마이그레이션 012 실행';
require_once G5_ADMIN_PATH.'/admin.head.php';

$run = isset($_GET['run']) || isset($_POST['run']);

$queries = array(
    'g5_ev_coupon' => "CREATE TABLE IF NOT EXISTS `g5_ev_coupon` (
  `ec_id` int unsigned NOT NULL AUTO_INCREMENT,
  `ec_code` varchar(32) NOT NULL DEFAULT '' COMMENT '쿠폰코드',
  `ec_name` varchar(200) NOT NULL DEFAULT '',
  `ec_target` varchar(20) NOT NULL DEFAULT 'biz' COMMENT 'biz=기업회원, personal=일반회원',
  `ec_type` varchar(30) NOT NULL DEFAULT 'thumb' COMMENT 'thumb=썸네일, ad=광고, line_ad_free=줄광고무료, gift=기프티콘',
  `ec_discount_type` varchar(20) NOT NULL DEFAULT 'percent' COMMENT 'percent=율, amount=금액',
  `ec_discount_value` int NOT NULL DEFAULT 0 COMMENT '할인율(%) 또는 할인금액(원)',
  `ec_min_amount` int NOT NULL DEFAULT 0 COMMENT '최소 결제금액',
  `ec_max_discount` int NOT NULL DEFAULT 0 COMMENT '최대할인금액(percent일 때만)',
  `ec_valid_from` date DEFAULT NULL,
  `ec_valid_to` date DEFAULT NULL,
  `ec_use_limit` int NOT NULL DEFAULT 0 COMMENT '0=무제한, N=발급수제한',
  `ec_use_count` int NOT NULL DEFAULT 0,
  `ec_is_active` tinyint NOT NULL DEFAULT 1,
  `ec_created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ec_updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`ec_id`),
  UNIQUE KEY `ec_code` (`ec_code`),
  KEY `ec_target` (`ec_target`),
  KEY `ec_type` (`ec_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='이브알바 쿠폰 마스터'",
    'g5_ev_coupon_issue' => "CREATE TABLE IF NOT EXISTS `g5_ev_coupon_issue` (
  `eci_id` int unsigned NOT NULL AUTO_INCREMENT,
  `ec_id` int unsigned NOT NULL,
  `mb_id` varchar(20) NOT NULL DEFAULT '',
  `eci_used` tinyint NOT NULL DEFAULT 0,
  `eci_used_at` datetime DEFAULT NULL,
  `eci_issued_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`eci_id`),
  KEY `ec_id` (`ec_id`),
  KEY `mb_id` (`mb_id`),
  KEY `eci_used` (`eci_used`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='쿠폰 발급'",
    'g5_ev_coupon_use' => "CREATE TABLE IF NOT EXISTS `g5_ev_coupon_use` (
  `ecu_id` int unsigned NOT NULL AUTO_INCREMENT,
  `eci_id` int unsigned NOT NULL,
  `ec_id` int unsigned NOT NULL,
  `mb_id` varchar(20) NOT NULL DEFAULT '',
  `ecu_target_type` varchar(30) NOT NULL DEFAULT 'thumb' COMMENT 'thumb, ad 등',
  `ecu_target_id` varchar(50) NOT NULL DEFAULT '' COMMENT 'jr_id, order_id 등',
  `ecu_discount_amount` int NOT NULL DEFAULT 0,
  `ecu_created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ecu_id`),
  KEY `eci_id` (`eci_id`),
  KEY `mb_id` (`mb_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='쿠폰 사용 로그'",
    'g5_jobs_thumb_option_paid' => "CREATE TABLE IF NOT EXISTS `g5_jobs_thumb_option_paid` (
  `jtp_id` int unsigned NOT NULL AUTO_INCREMENT,
  `jr_id` int unsigned NOT NULL,
  `mb_id` varchar(20) NOT NULL DEFAULT '',
  `jtp_option_key` varchar(50) NOT NULL DEFAULT '' COMMENT 'badge, motion, wave, border, premium_color',
  `jtp_option_value` varchar(50) NOT NULL DEFAULT '' COMMENT 'beginner, gold, P1 등',
  `jtp_valid_until` date NOT NULL COMMENT '사용가능 종료일',
  `jtp_amount` int NOT NULL DEFAULT 0,
  `jtp_coupon_id` int unsigned NOT NULL DEFAULT 0,
  `jtp_coupon_discount` int NOT NULL DEFAULT 0,
  `jtp_order_id` varchar(100) NOT NULL DEFAULT '' COMMENT '결제 주문번호',
  `jtp_created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`jtp_id`),
  KEY `jr_id` (`jr_id`),
  KEY `mb_id` (`mb_id`),
  KEY `jtp_valid_until` (`jtp_valid_until`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='썸네일 옵션 결제 내역'"
);

echo '<div class="local_desc01 local_desc">';
echo '<p>g5_ev_coupon, g5_ev_coupon_issue, g5_ev_coupon_use, g5_jobs_thumb_option_paid 테이블을 생성합니다.</p>';
echo '<p>아래 <strong>마이그레이션 실행</strong> 버튼을 클릭하세요.</p>';
echo '</div>';

if ($run) {
    echo '<div class="tbl_head01 tbl_wrap" style="margin-top:16px;"><table><tbody>';
    foreach ($queries as $name => $sql) {
        $r = @sql_query($sql, false);
        $ok = ($r !== false);
        $err = '';
        if (!$ok && function_exists('sql_error_info')) {
            $err = sql_error_info();
        }
        echo '<tr>';
        echo '<td>' . ($ok ? '<span style="color:green;">[OK]</span>' : '<span style="color:red;">[FAIL]</span>') . '</td>';
        echo '<td>' . htmlspecialchars($name) . '</td>';
        if ($err) echo '<td style="color:red;font-size:12px;">' . htmlspecialchars($err) . '</td>';
        echo '</tr>';
    }
    echo '</tbody></table></div>';
}

$tables = array('g5_ev_coupon', 'g5_ev_coupon_issue', 'g5_ev_coupon_use', 'g5_jobs_thumb_option_paid');
echo '<div style="margin-top:16px;"><strong>테이블 확인:</strong><ul>';
foreach ($tables as $t) {
    $chk = sql_query("SHOW TABLES LIKE '{$t}'", false);
    $exists = ($chk && sql_num_rows($chk)) ? true : false;
    echo '<li>' . $t . ': ' . ($exists ? '<span style="color:green;">존재함</span>' : '<span style="color:red;">없음</span>') . '</li>';
}
echo '</ul></div>';

echo '<div style="margin-top:20px;">';
echo '<form method="post" style="display:inline;">';
echo '<input type="hidden" name="run" value="1">';
echo get_admin_token();
echo '<button type="submit" class="btn btn_01">마이그레이션 012 실행 (테이블 생성)</button>';
echo '</form> ';
echo '<a href="./eve_thumb_shop.php" class="btn btn_02">썸네일상점 관리로 이동</a> ';
echo '<a href="./eve_coupon_list.php" class="btn btn_02">쿠폰 관리로 이동</a>';
echo '</div>';

require_once G5_ADMIN_PATH.'/admin.tail.php';
