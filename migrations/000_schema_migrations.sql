-- 이브알바 SQL Migration: 실행 이력 테이블
-- 최초 1회 수동 실행 또는 run_migration.php에서 common.php 로드 전에 수동 실행 가능

CREATE TABLE IF NOT EXISTS `g5_schema_migrations` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` VARCHAR(255) NOT NULL,
  `executed_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `migration` (`migration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
