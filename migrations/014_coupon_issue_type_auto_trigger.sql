-- 발급유형(자동/수동), 자동 트리거(가입인증 후/매월 1일), 발급대상(전체/개인)
ALTER TABLE `g5_ev_coupon`
  ADD COLUMN `ec_issue_type` varchar(20) NOT NULL DEFAULT 'manual' COMMENT 'manual=수동, auto=자동' AFTER `ec_is_active`,
  ADD COLUMN `ec_auto_trigger` varchar(30) DEFAULT NULL COMMENT 'on_approval=가입인증후, monthly_1st=매월1일 (ec_issue_type=auto일 때만)' AFTER `ec_issue_type`,
  ADD COLUMN `ec_issue_target_scope` varchar(20) NOT NULL DEFAULT 'all' COMMENT 'all=전체, individual=개인' AFTER `ec_auto_trigger`,
  ADD COLUMN `ec_issue_target_mb_id` varchar(20) DEFAULT NULL COMMENT '개인 발급 시 대상 회원ID' AFTER `ec_issue_target_scope`;

-- 기존 쿠폰 마이그레이션: 줄광고3달무료, 채용공고30%할인 → 가입인증 후 자동 발급
UPDATE `g5_ev_coupon`
  SET ec_issue_type = 'auto', ec_auto_trigger = 'on_approval'
  WHERE ec_name IN ('줄광고3달무료', '채용공고30%할인');
