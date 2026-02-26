<?php
/**
 * Gemini API를 이용한 업소 소개글 자동 생성
 * 역할(톤)별 프롬프트 지원: unnie, boss_male, pro
 * 단락별 생성: 고정(연락처,상호,닉네임,SNS,급여,근무지역,업종) + AI꾸밈(채용제목/위치, 근무환경, 혜택/우대/마무리)
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
    function generate_store_description_gemini($data, $role_id = 'unnie') {
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
        $title = isset($data['title']) ? trim($data['title']) : '';
        $location = isset($data['location']) ? trim($data['location']) : '';
        $environment = isset($data['environment']) ? trim($data['environment']) : '';
        $benefits = isset($data['benefits']) ? trim($data['benefits']) : '';
        $details = isset($data['details']) ? trim($data['details']) : '';
        $contact = isset($data['contact']) ? trim($data['contact']) : '';
        $sns = isset($data['sns']) ? trim($data['sns']) : '';
        $salary = isset($data['salary']) ? trim($data['salary']) : '';
        $region = isset($data['region']) ? trim($data['region']) : '';
        $jobtype = isset($data['jobtype']) ? trim($data['jobtype']) : '';

        $fixed_parts = array_filter([$nickname, $contact, $sns, $salary, $region, $jobtype]);
        $fixed_line = !empty($fixed_parts) ? implode(' | ', $fixed_parts) : '';

        $tone_unnie = "말투: ~해요, ~답니다. 이모지 2~3개 사용. 2~3문장만 작성해줘.";
        $tone_boss = "말투: ~합니다 체. 이모지 2~3개 사용. 2~3문장만 작성해줘.";
        $tone_pro = "말투: 간결 전문체. 이모지 2~3개 사용. 2~3문장만 작성해줘.";
        $tone_guide = ($role_id === 'boss_male') ? $tone_boss : (($role_id === 'pro') ? $tone_pro : $tone_unnie);

        $paragraphs = array(
            array(
                'prompt' => "구인 인사 + 채용제목 느낌 + 업소 위치/소개를 친근하게 꾸며줘. {$tone_guide}\n[데이터] 채용제목: {$title}\n위치/소개: {$location}",
                'maxTokens' => 200
            ),
            array(
                'prompt' => "근무환경을 매력적으로 2~3문장으로 꾸며줘. {$tone_guide}\n[데이터] 근무환경: {$environment}",
                'maxTokens' => 150
            ),
            array(
                'prompt' => "지원 혜택·우대사항·마무리 인사를 2~3문장으로 꾸며줘. {$tone_guide}\n[데이터] 혜택: {$benefits}\n추가상세: {$details}",
                'maxTokens' => 200
            )
        );

        $model = isset($gemini_model) ? $gemini_model : 'gemini-2.0-flash-exp';
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

        $url_base = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key=" . $api_key;
        $combined = array();

        foreach ($paragraphs as $idx => $para) {
            $payload = array(
                'contents' => array(array('parts' => array(array('text' => $para['prompt'])))),
                'generationConfig' => array(
                    'temperature' => 0.8,
                    'maxOutputTokens' => (int)$para['maxTokens'],
                    'topP' => 0.95
                )
            );
            $ch = curl_init($url_base);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload, JSON_UNESCAPED_UNICODE));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 20);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            $response = curl_exec($ch);
            $err = curl_error($ch);
            curl_close($ch);
            if ($err) {
                flock($fp, LOCK_UN);
                fclose($fp);
                return 'API 연결 중 오류가 발생했습니다. 잠시 후 다시 시도해 주세요.';
            }
            $result = json_decode($response, true);
            if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
                $combined[] = trim($result['candidates'][0]['content']['parts'][0]['text']);
            } else {
                $err_msg = isset($result['error']['message']) ? $result['error']['message'] : '알 수 없는 오류';
                if (strpos($err_msg, '429') !== false || strpos($err_msg, 'quota') !== false || strpos($err_msg, 'RESOURCE_EXHAUSTED') !== false) {
                    sleep(15);
                    $ch = curl_init($url_base);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload, JSON_UNESCAPED_UNICODE));
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 20);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
                    $response = curl_exec($ch);
                    curl_close($ch);
                    $result = json_decode($response, true);
                    if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
                        $combined[] = trim($result['candidates'][0]['content']['parts'][0]['text']);
                    } else {
                        flock($fp, LOCK_UN);
                        fclose($fp);
                        return '글 생성 중 오류가 발생했습니다. (' . $err_msg . ')';
                    }
                } else {
                    flock($fp, LOCK_UN);
                    fclose($fp);
                    return '글 생성 중 오류가 발생했습니다. (' . $err_msg . ')';
                }
            }
            if ($idx < count($paragraphs) - 1) {
                sleep(5);
            }
        }

        flock($fp, LOCK_UN);
        fclose($fp);
        $body = implode("\n\n", $combined);
        return $fixed_line ? ($fixed_line . "\n\n" . $body) : $body;
    }
}
