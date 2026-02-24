-- 이브알바 마스터 데이터 시드 (1차 기반)
-- 001_create_ev_master_tables.sql 실행 후 실행

-- 1) 업종 (1차)
INSERT IGNORE INTO g5_ev_industry (ei_code, ei_name, ei_ord) VALUES
('room', '룸싸롱', 1),
('karaoke', '노래주점', 2),
('massage', '마사지', 3),
('etc', '기타', 4);

-- 2) 직종 (2차) - ei_id 매핑: 1=룸싸롱, 2=노래주점, 3=마사지, 4=기타
INSERT IGNORE INTO g5_ev_job (ei_id, ej_code, ej_name, ej_ord) VALUES
(1, 'agassi', '아가씨', 1),
(1, 'chomissi', '초미씨', 2),
(1, 'missi', '미씨', 3),
(1, 'tc', 'TC', 4),
(2, 'agassi', '아가씨', 1),
(2, 'chomissi', '초미씨', 2),
(2, 'missi', '미씨', 3),
(2, 'tc', 'TC', 4),
(3, 'masseuse', '마사지사', 1),
(3, 'etc', '기타', 2),
(4, 'etc', '기타', 1);

-- 3) 지역 (1차)
INSERT IGNORE INTO g5_ev_region (er_code, er_name, er_ord) VALUES
('seoul', '서울', 1),
('gyeonggi', '경기', 2),
('incheon', '인천', 3),
('busan', '부산', 4),
('daegu', '대구', 5),
('gwangju', '광주', 6),
('daejeon', '대전', 7),
('ulsan', '울산', 8),
('gangwon', '강원', 9),
('chungcheong', '충청', 10),
('jeolla', '전라', 11),
('gyeongsang', '경상', 12),
('jeju', '제주', 13);

-- 4) 세부지역 (2차) - er_id 매핑: 1=서울, 2=경기, 3=인천
INSERT IGNORE INTO g5_ev_region_detail (er_id, erd_code, erd_name, erd_ord) VALUES
(1, 'gangnam', '강남구', 1),
(1, 'seocho', '서초구', 2),
(1, 'mapo', '마포구', 3),
(1, 'gangseo', '강서구', 4),
(1, 'seongdong', '성동구', 5),
(1, 'hongdae', '홍대', 6),
(1, 'itaewon', '이태원', 7),
(1, 'sinsa', '신사동', 8),
(2, 'bucheon', '부천시', 1),
(2, 'paju', '파주시', 2),
(2, 'suwon', '수원시', 3),
(2, 'goyang', '고양시', 4),
(2, 'ansan', '안산시', 5),
(2, 'hwaseong', '화성시', 6),
(3, 'junggu', '중구', 1),
(3, 'gyeyang', '계양구', 2),
(3, 'namdong', '남동구', 3),
(4, 'haeundae', '해운대구', 1),
(4, 'junggu', '중구', 2),
(5, 'junggu', '중구', 1),
(5, 'suseong', '수성구', 2);

-- 5) 편의사항
INSERT IGNORE INTO g5_ev_convenience (ec_code, ec_name, ec_ord) VALUES
('prepay', '선불가능', 1),
('order_ok', '순번확실', 2),
('oneroom', '원룸제공', 3),
('fullatt', '만근비지원', 4),
('plastic', '성형지원', 5),
('commute', '출퇴근지원', 6),
('meal', '식사제공', 7),
('tip_sep', '팁별도', 8),
('incentive', '인센티브', 9),
('count_guar', '갯수보장', 10),
('no_choice', '초이스없음', 11),
('daily_pay', '당일지급', 12);

-- 6) 키워드 (기본)
INSERT IGNORE INTO g5_ev_keyword (ek_code, ek_name, ek_ord) VALUES
('beginner', '초보환영', 1),
('urgent', '급구', 2),
('top', '1등', 3),
('daily', '당일지급', 4);
