-- 쿠폰 발급 시 쪽지 발송 옵션
ALTER TABLE `g5_ev_coupon`
  ADD COLUMN `ec_memo_send` tinyint(1) NOT NULL DEFAULT 0 COMMENT '발급 시 쪽지 발송: 0=안함, 1=함' AFTER `ec_is_active`;
