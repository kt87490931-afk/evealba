<?php
/**
 * 어드민 - Migration 017 (OG 이미지 기본값 사용)
 * sp_seo_config.sp_og_image를 비워서 theme/img/og_image.png(새 v4 이미지)가 사용되도록 함
 */
$sub_menu = '100100';
require_once './_common.php';

auth_check_menu($auth, $sub_menu, 'w');

$g5['title'] = '마이그레이션 017 - OG 이미지 기본값 적용';
require_once G5_ADMIN_PATH.'/admin.head.php';

$tb = 'sp_seo_config';
$chk = sql_fetch("SHOW TABLES LIKE '{$tb}'", false);

if (!$chk || !is_array($chk)) {
    echo '<p><span style="color:orange;">sp_seo_config 테이블이 없습니다.</span> SEO 설정 없이 theme/img/og_image.png가 기본으로 사용됩니다.</p>';
} else {
    $row = sql_fetch("SELECT sp_og_image FROM {$tb} WHERE id = 1", false);
    $prev = $row ? trim($row['sp_og_image'] ?? '') : '';
    sql_query("UPDATE {$tb} SET sp_og_image = '' WHERE id = 1", false);
    echo '<p><span style="color:green;">[OK]</span> sp_og_image를 비웠습니다. 기본 이미지(theme/evealba/img/og_image.png)가 OG로 사용됩니다.</p>';
    if ($prev) echo '<p class="frm_info">이전 값: ' . htmlspecialchars($prev) . '</p>';
}

echo '<p><a href="./scorepoint/scorepoint_seo.php" class="btn btn_01">SEO 설정으로 이동</a></p>';
require_once G5_ADMIN_PATH.'/admin.tail.php';
