<?php
/**
 * Gemini API를 이용한 업소 소개글 자동 생성
 * 역할(톤)별 프롬프트 지원: unnie, boss_male, pro
 * 한 번에 1,500자 내외 다이렉트 생성 (Tier 1 + Gemini 3 Flash)
 */
if (!defined('_GNUBOARD_')) exit;

if (!function_exists('_gemini_ai_debug_log')) {
    function _gemini_ai_debug_log($msg) {
        $log_dir = defined('G5_DATA_PATH') ? G5_DATA_PATH . '/log' : (dirname(__DIR__) . '/data/log');
        if (!is_dir($log_dir)) @mkdir($log_dir, 0755, true);
        $log_file = $log_dir . '/gemini_ai_debug.log';
        @file_put_contents($log_file, date('Y-m-d H:i:s') . ' ' . $msg . "\n", FILE_APPEND | LOCK_EX);
    }
}

if (!function_exists('generate_store_description_gemini')) {
    function generate_store_description_gemini($data, $role_id = 'unnie', $return_json = false) {
        @set_time_limit(180);

        $config_loaded = false;
        $config_path = '';
        $lib_dir = dirname(__FILE__);
        $project_root = dirname($lib_dir);

        $candidates = array();
        if (defined('G5_EXTEND_PATH') && file_exists(G5_EXTEND_PATH.'/gemini_config.php')) {
            $candidates[] = G5_EXTEND_PATH.'/gemini_config.php';
        }
        if (defined('G5_PATH') && file_exists(G5_PATH.'/extend/gemini_config.php')) {
            $candidates[] = G5_PATH.'/extend/gemini_config.php';
        }
        $candidates[] = $project_root . '/extend/gemini_config.php';

        foreach ($candidates as $p) {
            if (file_exists($p)) {
                $config_path = $p;
                include $p;
                $config_loaded = true;
                if (function_exists('_gemini_ai_debug_log')) _gemini_ai_debug_log("CONFIG_LOADED: " . $p);
                break;
            }
        }

        if (!$config_loaded) {
            if (function_exists('_gemini_ai_debug_log')) {
                _gemini_ai_debug_log("CONFIG_NOT_FOUND G5_EXTEND_PATH=" . (defined('G5_EXTEND_PATH') ? G5_EXTEND_PATH : 'undef') . " G5_PATH=" . (defined('G5_PATH') ? G5_PATH : 'undef'));
            }
            return '설정 파일을 찾을 수 없습니다.';
        }

        $api_key = isset($gemini_api_key) ? trim($gemini_api_key) : '';
        if (empty($api_key)) {
            if (function_exists('_gemini_ai_debug_log')) {
                _gemini_ai_debug_log("API_KEY_EMPTY config_path={$config_path} isset=" . (isset($gemini_api_key) ? '1' : '0'));
            }
            return 'API 키가 설정되지 않았습니다.';
        }
        if (function_exists('_gemini_ai_debug_log')) {
            _gemini_ai_debug_log("API_KEY_OK len=" . strlen($api_key) . " config=" . basename($config_path));
        }

        $role_id = in_array($role_id, ['unnie', 'boss_male', 'pro']) ? $role_id : 'unnie';
        $role = isset($gemini_roles[$role_id]) ? $gemini_roles[$role_id] : $gemini_roles['unnie'];
        $base_prompt = isset($role['prompt']) ? $role['prompt'] : $gemini_roles['unnie']['prompt'];

        $nickname = isset($data['nickname']) ? trim($data['nickname']) : '';
        $company = isset($data['company']) ? trim($data['company']) : '';
        $title = isset($data['title']) ? trim($data['title']) : '';
        $employ_type = isset($data['employ_type']) ? trim($data['employ_type']) : '';
        $location = isset($data['location']) ? trim($data['location']) : '';
        $environment = isset($data['environment']) ? trim($data['environment']) : '';
        $benefits = isset($data['benefits']) ? trim($data['benefits']) : '';
        $qualify = isset($data['qualify']) ? trim($data['qualify']) : '';
        $details = isset($data['details']) ? trim($data['details']) : '';
        $contact = isset($data['contact']) ? trim($data['contact']) : '';
        $sns = isset($data['sns']) ? trim($data['sns']) : '';
        $salary = isset($data['salary']) ? trim($data['salary']) : '';
        $region = isset($data['region']) ? trim($data['region']) : '';
        $jobtype = isset($data['jobtype']) ? trim($data['jobtype']) : '';
        $amenity = isset($data['amenity']) ? (is_array($data['amenity']) ? $data['amenity'] : array_filter(explode(',', (string)$data['amenity']))) : array();
        $keyword = isset($data['keyword']) ? (is_array($data['keyword']) ? $data['keyword'] : array_filter(explode(',', (string)$data['keyword']))) : array();
        $mbti_prefer = isset($data['mbti_prefer']) ? (is_array($data['mbti_prefer']) ? $data['mbti_prefer'] : array_filter(explode(',', (string)$data['mbti_prefer']))) : array();

        $fixed_parts = array_filter([$nickname, $company, $contact, $sns, $title, $employ_type, $salary, $region, $jobtype]);
        $fixed_line = !empty($fixed_parts) ? implode(' | ', $fixed_parts) : '';

        // [고정 참고 데이터] 화면에 그대로 표시되므로 참고만 함. [작성할 데이터]로 생성
        $data_block = "[고정 참고 데이터] (참고만 하고 생성 글에 넣지 마라)\n";
        $data_block .= "닉네임: {$nickname}\n";
        $data_block .= "상호: {$company}\n";
        $data_block .= "연락처: {$contact}\n";
        $data_block .= "SNS: {$sns}\n";
        $data_block .= "채용제목: {$title}\n";
        $data_block .= "고용형태: {$employ_type}\n";
        $data_block .= "급여조건: {$salary}\n";
        $data_block .= "근무지역: {$region}\n";
        $data_block .= "업종/직종: {$jobtype}\n\n";

        $data_block .= "[작성할 데이터] (이 데이터를 바탕으로 1,500자 내외 업소소개글 작성)\n";
        if (!empty($amenity)) $data_block .= "편의사항: " . implode(', ', $amenity) . "\n";
        if (!empty($keyword)) $data_block .= "키워드: " . implode(', ', $keyword) . "\n";
        if (!empty($mbti_prefer)) $data_block .= "선호 MBTI: " . implode(', ', $mbti_prefer) . "\n";
        $data_block .= "업소 위치·소개: {$location}\n";
        $data_block .= "근무환경: {$environment}\n";
        $data_block .= "혜택·복리후생: {$benefits}\n";
        $data_block .= "자격·우대사항: {$qualify}\n";
        $data_block .= "추가 상세설명: {$details}";

        $json_instruction = '';
        if ($return_json) {
            $json_instruction = "\n\n반드시 다음 JSON 형식으로만 응답하세요. 설명 없이 JSON만 반환하세요.\n"
                . "AI소개글 종합정리 순서: intro → location → env → benefit → wrapup\n"
                . '{"intro":"인사말 및 편의사항·키워드·MBTI 요약", "location":"업소 위치·교통 안내", "env":"근무환경", "benefit":"혜택·복리후생", "wrapup":"마무리·언니의 약속"}';
        }

        $full_prompt = $base_prompt . "\n" . $data_block . $json_instruction;

        $model = isset($gemini_model) ? $gemini_model : 'gemini-3-flash-preview';
        $log_dir = defined('G5_DATA_PATH') ? G5_DATA_PATH . '/log' : (dirname(__DIR__) . '/data/log');
        $lock_file = $log_dir . '/gemini_ai_queue.lock';
        if (!is_dir($log_dir)) @mkdir($log_dir, 0755, true);
        $fp = @fopen($lock_file, 'c');
        if (!$fp) {
            return 'API 큐 락 파일을 생성할 수 없습니다.';
        }
        @chmod($lock_file, 0666);
        $wait_sec = 0;
        $max_wait = 120;
        while (!flock($fp, LOCK_EX | LOCK_NB) && $wait_sec < $max_wait) {
            sleep(2);
            $wait_sec += 2;
            if (function_exists('_gemini_ai_debug_log')) _gemini_ai_debug_log("QUEUE_WAIT {$wait_sec}s");
        }
        if ($wait_sec >= $max_wait) {
            flock($fp, LOCK_UN);
            fclose($fp);
            return 'AI 생성 대기열이 많습니다. 2분 후 다시 시도해 주세요.';
        }

        // 공식 문서: https://ai.google.dev/gemini-api/docs/quickstart - x-goog-api-key 헤더 사용
        $url_base = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent";
        $payload = array(
            'contents' => array(array('parts' => array(array('text' => $full_prompt)))),
            'generationConfig' => array(
                'temperature' => 0.8,
                'maxOutputTokens' => 2000,
                'topP' => 0.95
            )
        );

        $ch = curl_init($url_base);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'x-goog-api-key: ' . $api_key
        ));
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload, JSON_UNESCAPED_UNICODE));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        flock($fp, LOCK_UN);
        fclose($fp);

        if ($err) {
            return 'API 연결 중 오류가 발생했습니다. 잠시 후 다시 시도해 주세요.';
        }

        $result = json_decode($response, true);
        if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
            $body = trim($result['candidates'][0]['content']['parts'][0]['text']);
            if ($return_json) return $body;
            return $fixed_line ? ($fixed_line . "\n\n" . $body) : $body;
        }

        $err_msg = isset($result['error']['message']) ? $result['error']['message'] : '알 수 없는 오류';
        return '글 생성 중 오류가 발생했습니다. (' . $err_msg . ')';
    }

    /**
     * 섹션별 AI 생성 (Option A) — ai_intro, ai_location, ai_env, ai_benefit, ai_wrapup
     * JSON 형식으로 응답받아 파싱
     */
    if (!function_exists('generate_store_description_gemini_sections')) {
        function generate_store_description_gemini_sections($data, $role_id = 'unnie') {
            $body = generate_store_description_gemini($data, $role_id, true);
            if (strpos($body, '오류') !== false || strpos($body, '설정') !== false || strpos($body, '대기열') !== false || strpos($body, '큐 락') !== false) {
                return array('error' => $body);
            }
            $raw = trim($body);
            $raw = preg_replace('/^```(?:json)?\s*/i', '', $raw);
            $raw = preg_replace('/\s*```\s*$/', '', $raw);
            $dec = @json_decode($raw, true);
            if (is_array($dec) && (isset($dec['intro']) || isset($dec['location']) || isset($dec['env']) || isset($dec['benefit']) || isset($dec['wrapup']))) {
                return array(
                    'ai_intro' => isset($dec['intro']) ? trim($dec['intro']) : '',
                    'ai_location' => isset($dec['location']) ? trim($dec['location']) : '',
                    'ai_env' => isset($dec['env']) ? trim($dec['env']) : '',
                    'ai_benefit' => isset($dec['benefit']) ? trim($dec['benefit']) : '',
                    'ai_wrapup' => isset($dec['wrapup']) ? trim($dec['wrapup']) : '',
                );
            }
            return array('error' => 'AI 응답 형식 오류. JSON 파싱 실패.');
        }
    }
}
