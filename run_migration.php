<?php
/**
 * 이브알바 SQL Migration 실행 스크립트
 * 사용법: php run_migration.php [--dry-run]
 *
 * DB 연결: common.php (그누보드) 우선, 없으면 migration_config.php
 */

$dryRun = in_array('--dry-run', $argv ?? []);

// DB 연결 로드
$dbLoaded = false;
if (file_exists(__DIR__ . '/common.php')) {
    define('_RUN_MIGRATION_', true);
    include_once __DIR__ . '/common.php';
    $dbLoaded = function_exists('sql_query');
}
if (!$dbLoaded && file_exists(__DIR__ . '/migration_config.php')) {
    include_once __DIR__ . '/migration_config.php';
    $dbLoaded = isset($GLOBALS['migration_pdo']) || function_exists('migration_sql_query');
}

if (!$dbLoaded) {
    die("DB 연결 불가. common.php(그누보드) 또는 migration_config.php 필요.\n");
}

function migration_sql_query_safe($sql, $ignoreError = true) {
    if (isset($GLOBALS['g5']['connect']) && $GLOBALS['g5']['connect']) {
        return sql_query($sql, $ignoreError);
    }
    if (isset($GLOBALS['migration_pdo'])) {
        try {
            return $GLOBALS['migration_pdo']->exec($sql);
        } catch (Exception $e) {
            if (!$ignoreError) throw $e;
            return false;
        }
    }
    return false;
}

function migration_sql_fetch_safe($sql) {
    if (function_exists('sql_fetch')) {
        return sql_fetch($sql);
    }
    if (isset($GLOBALS['migration_pdo'])) {
        try {
            $stmt = $GLOBALS['migration_pdo']->query($sql);
            return $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : null;
        } catch (Exception $e) {
            return null;
        }
    }
    return null;
}

function migration_sql_escape($s) {
    if (function_exists('sql_real_escape_string')) {
        return sql_real_escape_string($s);
    }
    return addslashes((string)$s);
}

$migrationsDir = __DIR__ . '/migrations';
if (!is_dir($migrationsDir)) {
    die("migrations 디렉토리가 없습니다.\n");
}

// schema_migrations 테이블 없으면 000_schema_migrations.sql로 생성
$check = migration_sql_fetch_safe("SHOW TABLES LIKE 'g5_schema_migrations'");
if (!$check) {
    $initFile = $migrationsDir . '/000_schema_migrations.sql';
    if (file_exists($initFile)) {
        $initSql = file_get_contents($initFile);
        foreach (array_filter(array_map('trim', explode(';', $initSql))) as $q) {
            if ($q === '' || preg_match('/^\s*--/', $q)) continue;
            migration_sql_query_safe($q);
        }
        migration_sql_query_safe("INSERT INTO g5_schema_migrations (migration) VALUES ('000_schema_migrations')");
        echo "[OK] 000_schema_migrations (테이블 생성)\n";
    }
}

$files = glob($migrationsDir . '/*.sql');
usort($files, function ($a, $b) {
    return strcmp(basename($a), basename($b));
});

$run = [];
foreach ($files as $f) {
    $name = basename($f, '.sql');
    if (strpos($name, 'README') !== false) continue;

    $sql = file_get_contents($f);
    if (trim($sql) === '') continue;

    if ($dryRun) {
        echo "[DRY-RUN] would run: $name\n";
        continue;
    }

    $esc = migration_sql_escape($name);
    $exists = migration_sql_fetch_safe("SELECT 1 FROM g5_schema_migrations WHERE migration = '{$esc}' LIMIT 1");
    if ($exists) {
        echo "[SKIP] $name (already executed)\n";
        continue;
    }

    $queries = array_filter(array_map('trim', explode(';', $sql)));
    foreach ($queries as $q) {
        if ($q === '' || preg_match('/^\s*--/', $q)) continue;
        migration_sql_query_safe($q);
    }
    migration_sql_query_safe("INSERT INTO g5_schema_migrations (migration) VALUES ('{$esc}')");
    echo "[OK] $name\n";
    $run[] = $name;
}

echo "\n완료. 실행된 마이그레이션: " . count($run) . "개\n";
