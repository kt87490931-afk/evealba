-- 쪽지 테이블 utf8mb4 변환 (₩ 원화기호, 이모지 정상 표시)
-- 참고: 테이블명이 g5_memo가 아닌 경우 수동 실행 필요
ALTER TABLE `g5_memo` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
