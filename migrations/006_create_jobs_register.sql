-- 채용정보등록 테이블 (입금대기중/진행중/마감)
CREATE TABLE IF NOT EXISTS `g5_jobs_register` (
  `jr_id` int unsigned NOT NULL AUTO_INCREMENT,
  `mb_id` varchar(20) NOT NULL DEFAULT '',
  `jr_status` varchar(20) NOT NULL DEFAULT 'pending' COMMENT 'pending=입금대기중, ongoing=진행중, ended=마감',
  `jr_nickname` varchar(100) NOT NULL DEFAULT '',
  `jr_company` varchar(200) NOT NULL DEFAULT '',
  `jr_title` varchar(200) NOT NULL DEFAULT '',
  `jr_subject_display` varchar(300) NOT NULL DEFAULT '' COMMENT '리스트 표시 제목: 입금대기=[닉네임]님의 광고글, 진행중=AI제목',
  `jr_data` longtext COMMENT 'JSON 폼전체데이터',
  `jr_ad_period` int NOT NULL DEFAULT 30,
  `jr_jump_count` int NOT NULL DEFAULT 0,
  `jr_total_amount` int NOT NULL DEFAULT 0,
  `jr_datetime` datetime NOT NULL,
  `jr_end_date` date DEFAULT NULL COMMENT '광고종료일',
  PRIMARY KEY (`jr_id`),
  KEY `mb_id` (`mb_id`),
  KEY `jr_status` (`jr_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
