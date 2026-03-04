# AI 큐 + jobs_view 배포 스크립트 (PowerShell)
# 사용: .\scripts\deploy_ai_queue.ps1
# 또는: .\scripts\deploy_ai_queue.ps1 root@188.166.179.115:/var/www/evealba

param(
    [string]$Remote = "root@188.166.179.115:/var/www/evealba"
)

$ErrorActionPreference = "Stop"
$ROOT = Split-Path $PSScriptRoot -Parent

$idx = $Remote.IndexOf(":")
$REMOTE_HOST = if ($idx -gt 0) { $Remote.Substring(0, $idx) } else { $Remote }
$REMOTE_PATH = if ($idx -gt 0) { $Remote.Substring($idx + 1) } else { "/var/www/evealba" }

Write-Host "배포 대상: $REMOTE_HOST`:$REMOTE_PATH"
Write-Host "로컬 경로: $ROOT"
Write-Host ""

$files = @(
    @{ src = "$ROOT\extend\gemini_config.php"; dest = "$REMOTE_PATH/extend/" },
    @{ src = "$ROOT\extend\jobs_list_helper.php"; dest = "$REMOTE_PATH/extend/" },
    @{ src = "$ROOT\lib\gemini_api.lib.php"; dest = "$REMOTE_PATH/lib/" },
    @{ src = "$ROOT\lib\jobs_ai_content.lib.php"; dest = "$REMOTE_PATH/lib/" },
    @{ src = "$ROOT\jobs_ai_queue_process.php"; dest = "$REMOTE_PATH/" },
    @{ src = "$ROOT\jobs_basic_info_save.php"; dest = "$REMOTE_PATH/" },
    @{ src = "$ROOT\jobs_ai_section_save.php"; dest = "$REMOTE_PATH/" },
    @{ src = "$ROOT\jobs_editor_bulk_save.php"; dest = "$REMOTE_PATH/" },
    @{ src = "$ROOT\jobs_editor_cards_save.php"; dest = "$REMOTE_PATH/" },
    @{ src = "$ROOT\jobs_image_save.php"; dest = "$REMOTE_PATH/" },
    @{ src = "$ROOT\run_migration.php"; dest = "$REMOTE_PATH/" },
    @{ src = "$ROOT\adm\jobs_register_confirm_update.php"; dest = "$REMOTE_PATH/adm/" },
    @{ src = "$ROOT\adm\jobs_register_list.php"; dest = "$REMOTE_PATH/adm/" },
    @{ src = "$ROOT\adm\jobs_ai_content_list.php"; dest = "$REMOTE_PATH/adm/" },
    @{ src = "$ROOT\adm\jobs_ai_content_action.php"; dest = "$REMOTE_PATH/adm/" },
    @{ src = "$ROOT\adm\jobs_ai_queue_add.php"; dest = "$REMOTE_PATH/adm/" },
    @{ src = "$ROOT\theme\evealba\jobs_view_main.php"; dest = "$REMOTE_PATH/theme/evealba/" },
    @{ src = "$ROOT\jobs_scrap.php"; dest = "$REMOTE_PATH/" },
    @{ src = "$ROOT\cron_auto_jump.php"; dest = "$REMOTE_PATH/" },
    @{ src = "$ROOT\theme\evealba\skin\board\eve_skin\jobs_view_editor.css"; dest = "$REMOTE_PATH/theme/evealba/skin/board/eve_skin/" },
    @{ src = "$ROOT\migrations\009_create_jobs_ai_queue.sql"; dest = "$REMOTE_PATH/migrations/" },
    @{ src = "$ROOT\migrations\010_create_jobs_ai_content.sql"; dest = "$REMOTE_PATH/migrations/" },
    @{ src = "$ROOT\migrations\migrate_ai_data.php"; dest = "$REMOTE_PATH/migrations/" },
    @{ src = "$ROOT\scripts\setup_ai_queue_cron.sh"; dest = "$REMOTE_PATH/scripts/" },
    @{ src = "$ROOT\scripts\setup_auto_jump_cron.sh"; dest = "$REMOTE_PATH/scripts/" }
)

foreach ($f in $files) {
    if (Test-Path $f.src) {
        Write-Host "scp $($f.src) -> $REMOTE_HOST`:$($f.dest)"
        scp -o StrictHostKeyChecking=accept-new $f.src "${REMOTE_HOST}:$($f.dest)"
        if ($LASTEXITCODE -ne 0) { throw "scp failed for $($f.src)" }
    } else {
        Write-Warning "파일 없음: $($f.src)"
    }
}

Write-Host ""
Write-Host "ssh: mkdir -p (skin, scripts)"
ssh -o StrictHostKeyChecking=accept-new $REMOTE_HOST "mkdir -p $REMOTE_PATH/theme/evealba/skin/board/eve_skin $REMOTE_PATH/scripts $REMOTE_PATH/data/jobs_images"

Write-Host ""
Write-Host "배포 완료."
Write-Host "서버에서 다음 실행 (필요 시):"
Write-Host "  cd /var/www/evealba"
Write-Host "  php run_migration.php"
Write-Host "  bash scripts/setup_ai_queue_cron.sh"
Write-Host "  bash scripts/setup_auto_jump_cron.sh"
