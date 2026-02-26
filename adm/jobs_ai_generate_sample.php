<?php
/**
 * AI 소개글 생성 샘플 (소녀시대 데이터)
 * 브라우저: adm/jobs_ai_generate_sample.php (관리자 로그인 필요)
 */
$sub_menu = '910100';
require_once './_common.php';

auth_check_menu($auth, $sub_menu, 'r');

@set_time_limit(90);
header('Content-Type: text/html; charset=utf-8');

$testData = [
    'nickname' => '소녀시대',
    'title' => '소녀시대에서 언니들을 모십니다. 고용',
    'location' => '수서역 5번출구에서 81m',
    'environment' => '신규업소입니다. 인테리어 고급지고, 최신 기계로 무장했습니다.',
    'benefits' => '편하게 출근하고 편하게 퇴근하시면 됩니다',
    'details' => "친구와 함께 지원하실분, 우대합니다!!\n자세한 설명은 연락주시면 친절히 상담하겠습니다.",
    'contact' => '01090905050',
    'sns' => '카카오:ak47, 라인:ak47, 텔레그램:ak47',
    'salary' => '급여협의',
    'region' => '11',
    'jobtype' => '룸살롱 / 아가씨',
];

include_once(G5_LIB_PATH . '/gemini_api.lib.php');

echo "<h2>AI 소개글 생성 결과</h2>";
echo "<p>입력 데이터 기준으로 생성 중...</p>";
echo "<hr>";

$start = microtime(true);
$result = generate_store_description_gemini($testData, 'unnie');
$elapsed = round((microtime(true) - $start) * 1000);

if (strpos($result, '오류') !== false || strpos($result, '설정') !== false || strpos($result, '대기열') !== false) {
    echo "<p style='color:red;'>❌ 생성 실패: " . htmlspecialchars($result) . "</p>";
    echo "<hr><h3>📝 예시 (API 할당량 회복 시 이와 비슷한 스타일로 생성됨)</h3>";
    $sample = "안녕하세요~ 💕 소녀시대에서 같이 일해줄 언니들을 찾고 있어요!\n\n"
        . "📍 수서역 5번 출구에서 81m 거리에 있는 우리 업소는 신규 오픈한 곳이에요. 인테리어도 고급스럽고 최신 설비로 준비했답니다~ ✨\n\n"
        . "💼 룸살롱 / 아가씨 직종에서 급여는 협의 가능해요. 편하게 출퇴근하시면 됩니다!\n\n"
        . "👯‍♀️ 친구와 함께 지원하시는 분 우대해요~ 같이 즐겁게 일해요!\n\n"
        . "자세한 상담은 연락 주시면 친절히 안내해 드릴게요. 연락 기다릴게요~ 💌";
    echo "<div style='background:#f8f4f8;padding:16px;border-radius:8px;white-space:pre-wrap;line-height:1.6;'>" . nl2br(htmlspecialchars($sample)) . "</div>";
} else {
    echo "<div style='background:#f8f4f8;padding:16px;border-radius:8px;white-space:pre-wrap;line-height:1.6;'>";
    echo nl2br(htmlspecialchars($result));
    echo "</div>";
    echo "<p><small>응답시간: {$elapsed}ms</small></p>";
}

echo "<hr><p><a href='./jobs_register_list.php'>목록으로</a></p>";
