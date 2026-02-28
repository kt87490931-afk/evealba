<?php
include_once('./_common.php');
if (!defined('_GNUBOARD_')) exit;

@include_once(G5_PATH.'/extend/jobs_list_helper.php');

header('Content-Type: application/xml; charset=UTF-8');

$host = 'http://' . $_SERVER['HTTP_HOST'];

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

echo '<url><loc>' . $host . '/</loc><changefreq>daily</changefreq><priority>1.0</priority></url>' . "\n";
echo '<url><loc>' . $host . '/jobs.php</loc><changefreq>daily</changefreq><priority>0.9</priority></url>' . "\n";
echo '<url><loc>' . $host . '/jobs_region.php</loc><changefreq>daily</changefreq><priority>0.8</priority></url>' . "\n";

$tb_check = sql_query("SHOW TABLES LIKE 'g5_jobs_register'", false);
if ($tb_check && sql_num_rows($tb_check)) {
    $result = sql_query("SELECT * FROM g5_jobs_register WHERE jr_status = 'ongoing' AND jr_approved = 1 AND jr_end_date >= CURDATE() ORDER BY jr_id DESC", false);
    if ($result) {
        while ($row = sql_fetch_array($result)) {
            $url = $host . _jlh_clean_url($row);
            $lastmod = date('Y-m-d', strtotime($row['jr_datetime']));
            echo '<url><loc>' . htmlspecialchars($url) . '</loc><lastmod>' . $lastmod . '</lastmod><changefreq>weekly</changefreq><priority>0.7</priority></url>' . "\n";
        }
    }
}

echo '</urlset>' . "\n";
