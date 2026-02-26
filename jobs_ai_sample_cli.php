<?php
/**
 * AI 소개글 생성 샘플 (CLI - 소녀시대 데이터)
 * 사용: php jobs_ai_sample_cli.php
 */
if (php_sapi_name() !== 'cli') exit;
define('_GNUBOARD_', true);
$g5_path = ['path' => __DIR__];
include_once __DIR__ . '/common.php';
include_once G5_LIB_PATH . '/gemini_api.lib.php';

$testData = [
    'nickname' => '소녀시대',
    'title' => '소녀시대에서 언니들을 모십니다. 고용',
    'location' => '수서역 5번출구에서 81m',
    'environment' => '신규업소입니다. 인테리어 고급지고, 최신 기계로 무장했습니다.',
    'benefits' => '편하게 출근하고 편하게 퇴근하시면 됩니다',
    'details' => "친구와 함께 지원하실분, 우대합니다!!\n자세한 설명은 연락주시면 친절히 상담하겠습니다.",
];

echo "AI 소개글 생성 중...\n\n";

$result = generate_store_description_gemini($testData, 'unnie');

if (strpos($result, '오류') !== false || strpos($result, '설정') !== false || strpos($result, '대기열') !== false) {
    echo "생성 실패: " . $result . "\n";
} else {
    echo "=== AI 생성 소개글 ===\n\n";
    echo $result . "\n";
}
