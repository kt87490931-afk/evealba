-- 채용정보등록 AI 제목/소개글 컬럼 추가 (Gemini 연동 준비)
ALTER TABLE `g5_jobs_register`
  ADD COLUMN `jr_ai_title` varchar(300) NOT NULL DEFAULT '' COMMENT 'AI 생성 제목' AFTER `jr_subject_display`,
  ADD COLUMN `jr_ai_summary` text COMMENT 'AI 생성 소개글' AFTER `jr_ai_title`;
