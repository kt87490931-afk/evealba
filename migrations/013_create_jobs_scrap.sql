-- 채용정보 스크랩 테이블 (일반회원용)
CREATE TABLE IF NOT EXISTS g5_jobs_scrap (
  js_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  jr_id INT UNSIGNED NOT NULL,
  mb_id VARCHAR(20) NOT NULL DEFAULT '',
  js_datetime DATETIME NOT NULL,
  UNIQUE KEY uk_jr_mb (jr_id, mb_id),
  KEY idx_mb_id (mb_id),
  KEY idx_jr_id (jr_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='채용정보 스크랩';
