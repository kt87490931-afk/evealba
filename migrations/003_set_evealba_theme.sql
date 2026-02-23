-- 이브알바 테마를 기본 테마로 설정
-- 그누보드 테이블 prefix가 g5_ 가 아닌 경우 해당 prefix로 수정 후 실행

UPDATE `g5_config` SET `cf_theme` = 'evealba' WHERE 1=1;
