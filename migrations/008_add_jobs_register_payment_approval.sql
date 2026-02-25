-- 입금상태/승인상태 분리, 신청한광고목록 저장
ALTER TABLE `g5_jobs_register`
  ADD COLUMN `jr_payment_confirmed` tinyint NOT NULL DEFAULT 0 COMMENT '0=입금대기 1=입금확인' AFTER `jr_status`,
  ADD COLUMN `jr_approved` tinyint NOT NULL DEFAULT 0 COMMENT '0=승인대기 1=승인' AFTER `jr_payment_confirmed`,
  ADD COLUMN `jr_approved_datetime` datetime DEFAULT NULL COMMENT '승인일시(광고기간시작)' AFTER `jr_approved`,
  ADD COLUMN `jr_ad_labels` varchar(500) NOT NULL DEFAULT '' COMMENT '신청한광고목록(줄광고 30일,우대 60일 등)' AFTER `jr_data`;

-- 기존 ongoing 건: 입금확인+승인 완료로 간주
UPDATE `g5_jobs_register` SET jr_payment_confirmed=1, jr_approved=1, jr_approved_datetime=jr_datetime WHERE jr_status='ongoing' AND jr_approved_datetime IS NULL;
