-- AI 소개글 생성 대기열 (DB 기반 큐)
CREATE TABLE IF NOT EXISTS `g5_jobs_ai_queue` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `jr_id` int unsigned NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `retry_count` int NOT NULL DEFAULT 0,
  `error_msg` varchar(500) NOT NULL DEFAULT '',
  `created_at` datetime NOT NULL,
  `processed_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `jr_id` (`jr_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
