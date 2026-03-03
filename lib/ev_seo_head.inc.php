<?php
/**
 * evealba SEO 헤드 - sp_seo_config 연동
 * ScorePoint SEO (adm/scorepoint/scorepoint_seo.php) 설정을 head 메타태그에 반영
 * 포함: lib/ev_seo_head.inc.php (head.sub.php 등에서 include)
 */
if (!defined('_GNUBOARD_')) exit;

$_ev_seo = array(
    'meta_description' => '',
    'meta_keywords' => '',
    'og_title' => '',
    'og_description' => '',
    'og_image' => '',
    'og_type' => 'website',
    'twitter_card' => 'summary_large_image',
    'google_site_verification' => '',
    'canonical_url' => '',
    'sp_schema_organization' => '',
);

// 관리자·메모 전용 페이지는 스킵
if (defined('G5_IS_ADMIN') && G5_IS_ADMIN) return;
if (defined('G5_IS_MEMO_PAGE') && G5_IS_MEMO_PAGE) return;
if (defined('G5_MEMO_POPUP') && G5_MEMO_POPUP) return;

$sp_seo_table = 'sp_seo_config';
$chk = @sql_fetch("SHOW TABLES LIKE '{$sp_seo_table}'", false);
if (is_array($chk) && count($chk) > 0) {
    $row = @sql_fetch("SELECT * FROM `{$sp_seo_table}` WHERE id = 1 LIMIT 1", false);
    if (is_array($row)) {
        $_ev_seo['meta_description'] = isset($row['sp_meta_description']) ? trim($row['sp_meta_description']) : '';
        $_ev_seo['meta_keywords'] = isset($row['sp_meta_keywords']) ? trim($row['sp_meta_keywords']) : '';
        $_ev_seo['og_title'] = isset($row['sp_og_title']) ? trim($row['sp_og_title']) : '';
        $_ev_seo['og_description'] = isset($row['sp_og_description']) ? trim($row['sp_og_description']) : '';
        $_ev_seo['og_image'] = isset($row['sp_og_image']) ? trim($row['sp_og_image']) : '';
        $_ev_seo['og_type'] = isset($row['sp_og_type']) && $row['sp_og_type'] ? $row['sp_og_type'] : 'website';
        $_ev_seo['twitter_card'] = isset($row['sp_twitter_card']) && $row['sp_twitter_card'] ? $row['sp_twitter_card'] : 'summary_large_image';
        $_ev_seo['google_site_verification'] = isset($row['sp_google_site_verification']) ? trim($row['sp_google_site_verification']) : '';
        $_ev_seo['canonical_url'] = isset($row['sp_canonical_url']) ? trim($row['sp_canonical_url']) : '';
        $_ev_seo['sp_schema_organization'] = isset($row['sp_schema_organization']) ? trim($row['sp_schema_organization']) : '';
    }
}
