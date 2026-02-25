<?php
/**
 * AI(Gemini) 연동 진단 스크립트
 * 브라우저에서 adm/jobs_ai_test.php 로 접속하여 API 정상 여부 확인
 */
$sub_menu = '910100';
require_once './_common.php';

auth_check_menu($auth, $sub_menu, 'r');

header('Content-Type: text/html; charset=utf-8');
echo "<h2>AI(Gemini) 연동 진단</h2><pre>\n";

// 1. 경로 확인
$lib_path = (defined('G5_LIB_PATH') ? G5_LIB_PATH : '') . '/gemini_api.lib.php';
$extend_path = (defined('G5_PATH') ? G5_PATH : '') . '/extend/gemini_config.php';
echo "G5_LIB_PATH: " . (defined('G5_LIB_PATH') ? G5_LIB_PATH : '미정의') . "\n";
echo "gemini_api.lib.php 존재: " . (file_exists($lib_path) ? 'O' : 'X') . " ({$lib_path})\n";
echo "gemini_config.php 존재: " . (file_exists($extend_path) ? 'O' : 'X') . " ({$extend_path})\n\n";

// 2. API 호출 테스트
if (file_exists($lib_path)) {
    include_once $lib_path;
    $testData = array(
        'nickname' => '테스트업소',
        'title' => '테스트 채용공고',
        'location' => '서울 강남',
        'environment' => '깔끔한 환경',
        'benefits' => '4대보험',
        'details' => '테스트용 상세',
    );
    echo "Gemini API 호출 중... (최대 30초)\n";
    $start = microtime(true);
    $result = generate_store_description_gemini($testData, 'unnie');
    $elapsed = round((microtime(true) - $start) * 1000);
    echo "응답시간: {$elapsed}ms\n";
    echo "결과: " . (strlen($result) > 200 ? substr($result, 0, 200) . '...' : $result) . "\n";
    if (strpos($result, '오류') !== false || strpos($result, '설정') !== false) {
        echo "\n[오류] API 연동 실패 - 위 메시지 확인\n";
    } else {
        echo "\n[정상] API 연동 성공\n";
    }
} else {
    echo "gemini_api.lib.php 를 찾을 수 없습니다.\n";
}

echo "</pre><p><a href='./jobs_register_list.php'>목록으로</a></p>";
