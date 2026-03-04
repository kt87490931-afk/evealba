-- 쿠폰 발급 기간 및 1인당 제한 (g5_ev_coupon 테이블 필요)
ALTER TABLE `g5_ev_coupon`
  ADD COLUMN `ec_issue_from` date DEFAULT NULL COMMENT '발급 가능 시작일' AFTER `ec_valid_to`,
  ADD COLUMN `ec_issue_to` date DEFAULT NULL COMMENT '발급 가능 종료일' AFTER `ec_issue_from`,
  ADD COLUMN `ec_issue_limit_per_member` int NOT NULL DEFAULT 0 COMMENT '1인당 최대 발급 횟수, 0=무제한' AFTER `ec_use_count`;
