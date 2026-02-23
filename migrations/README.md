# 이브알바 SQL Migration

## 파일 명명 규칙

- `00X_설명.sql` (예: 002_create_ev_master_tables.sql)
- 번호 순서대로 실행됨

## 실행

```bash
php run_migration.php
```

## dry-run (실제 실행 없이 확인)

```bash
php run_migration.php --dry-run
```
