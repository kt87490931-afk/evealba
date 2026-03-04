-- 줄광고 무료 쿠폰 기간 선택 (30일/60일/90일)
ALTER TABLE `g5_ev_coupon`
  ADD COLUMN `ec_line_ad_days` int NOT NULL DEFAULT 0 COMMENT '줄광고무료일 때 무료 기간(일): 30,60,90 등' AFTER `ec_type`;
