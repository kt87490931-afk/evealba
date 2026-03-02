-- g5_jobs_ai_content: AI 생성 콘텐츠 별도 저장 (버전 관리)
-- 사용자 입력(jr_data)과 AI 생성 데이터를 완전 분리
CREATE TABLE IF NOT EXISTS `g5_jobs_ai_content` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `jr_id` int unsigned NOT NULL COMMENT '채용정보 ID',
  `mb_id` varchar(20) NOT NULL DEFAULT '' COMMENT '회원 ID',
  `version` int unsigned NOT NULL DEFAULT 1 COMMENT '생성 버전',
  `ai_tone` varchar(20) NOT NULL DEFAULT 'unnie' COMMENT '톤 (unnie/boss_male/pro)',
  `ai_data` JSON NOT NULL COMMENT 'AI 생성 14개 섹션 JSON',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT '현재 활성 버전 여부',
  `duration_ms` int unsigned NOT NULL DEFAULT 0 COMMENT 'API 응답 소요시간(ms)',
  `created_at` datetime NOT NULL COMMENT '생성일시',
  PRIMARY KEY (`id`),
  KEY `idx_jr_active` (`jr_id`, `is_active`),
  KEY `idx_mb_id` (`mb_id`),
  KEY `idx_jr_version` (`jr_id`, `version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='AI 생성 콘텐츠 (버전별 보관)';
