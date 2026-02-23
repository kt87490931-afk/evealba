# 이브알바 배포 스크립트
# 사용법: .\deploy.ps1 [-Message "커밋메시지"] [-PushToServer] [-DryRun]
# 예: .\deploy.ps1 -Message "반응형 테마 적용" -PushToServer

param(
    [string]$Message = "Deploy: $(Get-Date -Format 'yyyy-MM-dd HH:mm')",
    [switch]$PushToServer,
    [switch]$DryRun
)

$ErrorActionPreference = "Stop"
$ProjectRoot = $PSScriptRoot
$ServerHost = "root@188.166.179.115"
$ServerPath = "/var/www/evealba"

Set-Location $ProjectRoot

Write-Host "=== 이브알바 배포 ===" -ForegroundColor Cyan
Write-Host "프로젝트: $ProjectRoot"
Write-Host "커밋 메시지: $Message"
Write-Host ""

if ($DryRun) {
    Write-Host "[DRY-RUN] 실행할 명령만 표시합니다." -ForegroundColor Yellow
}

# 1. Git 상태 확인
if (-not (Test-Path ".git")) {
    Write-Host "Git 저장소가 없습니다. git init 실행..." -ForegroundColor Yellow
    if (-not $DryRun) { git init }
}

# 2. 변경사항 스테이징
Write-Host "`n[1/4] git add ." -ForegroundColor Green
if (-not $DryRun) {
    git add .
}

# 3. 상태 확인
Write-Host "`n[2/4] git status" -ForegroundColor Green
if (-not $DryRun) {
    git status --short
}

# 4. 커밋 (변경사항이 있을 경우)
$hasChanges = $false
if (-not $DryRun) {
    $status = git status --porcelain
    $hasChanges = $status -ne ""
}

if ($hasChanges) {
    Write-Host "`n[3/4] git commit" -ForegroundColor Green
    if (-not $DryRun) {
        git commit -m $Message
    } else {
        Write-Host "  [DRY-RUN] git commit -m `"$Message`"" -ForegroundColor Gray
    }
} else {
    Write-Host "`n[3/4] 변경사항 없음 - 커밋 생략" -ForegroundColor Gray
}

# 5. GitHub 푸시
Write-Host "`n[4/4] git push origin main" -ForegroundColor Green
$remote = git remote get-url origin 2>$null
if (-not $remote) {
    Write-Host "원격 저장소(origin)가 설정되지 않았습니다." -ForegroundColor Red
    Write-Host "다음 명령으로 설정하세요:" -ForegroundColor Yellow
    Write-Host "  git remote add origin git@github.com:YOUR_USER/evealba.git"
    Write-Host "  또는"
    Write-Host "  git remote add origin https://github.com/YOUR_USER/evealba.git"
    exit 1
}

if (-not $DryRun) {
    try {
        git push origin main 2>&1
        if ($LASTEXITCODE -ne 0) {
            # main 브랜치가 없으면 master 시도
            git push origin master 2>&1
        }
    } catch {
        Write-Host "Push 실패: $_" -ForegroundColor Red
        exit 1
    }
} else {
    Write-Host "  [DRY-RUN] git push origin main" -ForegroundColor Gray
}

# 6. 서버 배포 (선택)
if ($PushToServer) {
    Write-Host "`n=== 서버 배포 (SSH) ===" -ForegroundColor Cyan
    Write-Host "서버: $ServerHost"
    Write-Host "경로: $ServerPath"
    if (-not $DryRun) {
        ssh $ServerHost "cd $ServerPath && git pull origin main"
        if ($LASTEXITCODE -ne 0) {
            ssh $ServerHost "cd $ServerPath && git pull origin master"
        }
    } else {
        Write-Host "  [DRY-RUN] ssh $ServerHost `"cd $ServerPath && git pull origin main`"" -ForegroundColor Gray
    }
}

Write-Host "`n=== 배포 완료 ===" -ForegroundColor Green
