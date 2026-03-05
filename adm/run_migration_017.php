<?php
/**
 * 어드민 - Migration 017 (OG 이미지 정리)
 * - sp_seo_config.sp_og_image 비우기 → theme/img/og_image.png 기본 사용
 * - data/seo_og/ 내 기존 OG 이미지 전부 삭제
 */
$sub_menu = '100100';
require_once './_common.php';

auth_check_menu($auth, $sub_menu, 'w');

$g5['title'] = '마이그레이션 017 - OG 이미지 정리';
require_once G5_ADMIN_PATH.'/admin.head.php';

// 1) data/seo_og/ 내 모든 파일 삭제
$seo_og_dir = defined('G5_DATA_PATH') ? (G5_DATA_PATH . '/seo_og') : '';
$deleted = 0;
if ($seo_og_dir && is_dir($seo_og_dir)) {
    $files = glob($seo_og_dir . '/*');
    foreach ($files as $f) {
        if (is_file($f)) {
            @unlink($f);
            $deleted++;
        }
    }
    echo '<p><span style="color:green;">[OK]</span> data/seo_og/ 폴더 내 기존 OG 이미지 ' . $deleted . '개 삭제됨.</p>';
} else {
    echo '<p><span style="color:gray;">data/seo_og/ 폴더 없음.</span></p>';
}

// 2) sp_seo_config.sp_og_image 비우기
$tb = 'sp_seo_config';
$chk = sql_fetch("SHOW TABLES LIKE '{$tb}'", false);

if (!$chk || !is_array($chk)) {
    echo '<p><span style="color:orange;">sp_seo_config 테이블이 없습니다.</span> theme/evealba/img/og_image.png가 기본으로 사용됩니다.</p>';
} else {
    $row = sql_fetch("SELECT sp_og_image FROM {$tb} WHERE id = 1", false);
    $prev = $row ? trim($row['sp_og_image'] ?? '') : '';
    sql_query("UPDATE {$tb} SET sp_og_image = '' WHERE id = 1", false);
    echo '<p><span style="color:green;">[OK]</span> sp_og_image를 비웠습니다. og_image.png만 OG로 사용됩니다.</p>';
    if ($prev) echo '<p class="frm_info">이전 DB값: ' . htmlspecialchars($prev) . '</p>';
}

echo '<p><a href="./scorepoint/scorepoint_seo.php" class="btn btn_01">SEO 설정으로 이동</a></p>';
require_once G5_ADMIN_PATH.'/admin.tail.php';
