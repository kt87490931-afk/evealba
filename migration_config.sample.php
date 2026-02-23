<?php
/**
 * SQL Migration 전용 DB 설정 (그누보드 설치 전 사용)
 * 복사: migration_config.sample.php → migration_config.php
 * migration_config.php는 .gitignore에 추가 권장 (비밀번호 포함)
 */

$host = 'localhost';
$db   = 'evealba_db';
$user = 'evealba';
$pass = '비밀번호';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$opt = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];
$GLOBALS['migration_pdo'] = new PDO($dsn, $user, $pass, $opt);
