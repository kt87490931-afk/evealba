-- 점프 기능 관련 컬럼 추가 (g5_jobs_register)
ALTER TABLE g5_jobs_register
  ADD COLUMN IF NOT EXISTS jr_jump_remain INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '잔여 점프 횟수',
  ADD COLUMN IF NOT EXISTS jr_jump_used INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '사용한 점프 횟수',
  ADD COLUMN IF NOT EXISTS jr_jump_total INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '총 부여 점프 횟수',
  ADD COLUMN IF NOT EXISTS jr_jump_datetime DATETIME DEFAULT NULL COMMENT '마지막 점프 시각',
  ADD COLUMN IF NOT EXISTS jr_auto_jump TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '자동점프 활성화',
  ADD COLUMN IF NOT EXISTS jr_auto_jump_next DATETIME DEFAULT NULL COMMENT '다음 자동 점프 예정 시각';

ALTER TABLE g5_jobs_register
  ADD INDEX IF NOT EXISTS idx_jump_datetime (jr_jump_datetime),
  ADD INDEX IF NOT EXISTS idx_auto_jump_next (jr_auto_jump, jr_auto_jump_next);

-- 점프 이력 테이블
CREATE TABLE IF NOT EXISTS g5_jobs_jump_log (
  jl_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  jr_id INT UNSIGNED NOT NULL,
  mb_id VARCHAR(20) NOT NULL DEFAULT '',
  jl_type ENUM('manual','auto') NOT NULL DEFAULT 'manual',
  jl_remain_before INT UNSIGNED NOT NULL DEFAULT 0,
  jl_remain_after INT UNSIGNED NOT NULL DEFAULT 0,
  jl_datetime DATETIME NOT NULL,
  KEY idx_jr_id (jr_id),
  KEY idx_datetime (jl_datetime)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 점프 추가 구매 테이블
CREATE TABLE IF NOT EXISTS g5_jobs_jump_purchase (
  jp_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  jr_id INT UNSIGNED NOT NULL,
  mb_id VARCHAR(20) NOT NULL DEFAULT '',
  jp_count INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '구매 횟수',
  jp_amount INT UNSIGNED NOT NULL DEFAULT 0 COMMENT '결제 금액',
  jp_status VARCHAR(20) NOT NULL DEFAULT 'pending' COMMENT 'pending/confirmed',
  jp_datetime DATETIME NOT NULL,
  jp_confirmed_datetime DATETIME DEFAULT NULL,
  KEY idx_jr_id (jr_id),
  KEY idx_mb_id (mb_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
