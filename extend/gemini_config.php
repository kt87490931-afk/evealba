<?php
/**
 * Gemini API 설정 (이브알바 AI 소개글 생성)
 * 보안: API 키는 환경변수 GEMINI_API_KEY 로 우선 사용 가능
 */
if (!defined('_GNUBOARD_')) exit;

// API 키: 1) 환경변수 GEMINI_API_KEY (getenv 또는 $_SERVER), 2) extend/gemini_api_key.env 파일
// 새 키 발급: https://aistudio.google.com/apikey
$gemini_api_key = trim((string) (getenv('GEMINI_API_KEY') ?: ($_SERVER['GEMINI_API_KEY'] ?? '')));
if ($gemini_api_key === '') {
    $ext_dir = defined('G5_EXTEND_PATH') ? rtrim(G5_EXTEND_PATH, '/') : (defined('G5_PATH') ? G5_PATH . '/extend' : __DIR__);
    $key_file = $ext_dir . '/gemini_api_key.env';
    if (file_exists($key_file) && is_readable($key_file)) {
        $gemini_api_key = trim((string) file_get_contents($key_file));
    } else {
        $gemini_api_key = '';
    }
}
$gemini_model = 'gemini-3-flash-preview';

// 톤별 역할: jobs_register_main 선택값 → jr_data.ai_tone → jobs_ai_queue_process → gemini_api.lib
// AI소개글 종합정리 순서: 닉네임·상호, 연락처, SNS, 채용제목·고용형태, 급여조건, 근무지역, 업종/직종(고정) → 편의사항, 키워드, 선호 MBTI, 업소위치, 근무환경, 혜택, 자격, 추가상세(생성)
$gemini_roles = [
    'unnie' => [
        'name' => '친근한 언니 톤',
        'prompt' => "너는 20~30대 여성 구직자들에게 친근하게 말하는 다정한 언니 사장님이야.\n\n"
            . "[중요] [고정 참고 데이터]는 화면에 그대로 표시되므로 참고만 하고, 생성 글에 포함하지 마라.\n"
            . "[작성할 데이터]를 바탕으로 AI소개글 종합정리 순서대로, 총 1,500자 내외의 업소소개글을 작성해줘.\n\n"
            . "말투: ~해요, ~답니다 등 해요체. 부드럽고 편안한 언니 느낌.\n"
            . "특징: 이모지를 적극 활용 (각 단락마다 2~3개 이상). 가독성 좋게 단락 나누기.\n\n"
    ],
    'boss_male' => [
        'name' => '세심한 남사장님 톤',
        'prompt' => "너는 여성 구직자들에게 세심하고 다정하게 말하는 남성 사장님이야.\n\n"
            . "[중요] [고정 참고 데이터]는 화면에 그대로 표시되므로 참고만 하고, 생성 글에 포함하지 마라.\n"
            . "[작성할 데이터]를 바탕으로 AI소개글 종합정리 순서대로, 총 1,500자 내외의 업소소개글을 작성해줘.\n\n"
            . "말투: 정중하고 다정한 ~합니다 체. 배려심 있고 믿음직한 느낌.\n"
            . "특징: 이모지를 적극 활용 (각 단락마다 2~3개 이상). 가독성 좋게 단락 나누기.\n\n"
    ],
    'pro' => [
        'name' => '전문가 톤',
        'prompt' => "너는 구인광고 전문 카피라이터야.\n\n"
            . "[중요] [고정 참고 데이터]는 화면에 그대로 표시되므로 참고만 하고, 생성 글에 포함하지 마라.\n"
            . "[작성할 데이터]를 바탕으로 AI소개글 종합정리 순서대로, 총 1,500자 내외의 업소소개글을 작성해줘.\n\n"
            . "말투: 간결하고 전문적인 문체. 핵심 정보를 명확히 전달.\n"
            . "특징: 이모지를 적극 활용 (각 단락마다 2~3개 이상). 가독성 좋게 단락 나누기.\n\n"
    ],
];
