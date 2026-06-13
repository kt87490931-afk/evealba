# 이브알바 UI 리뉴얼 — Cursor 작업 지시서 v2

> **기준일:** 2026년  
> **기술스택:** 그누보드5 (PHP) + MySQL + 커스텀 채용 모듈  
> **참고 UI:** https://pcimzg.readdy.co/ (레이아웃·IA만 참고)  
> **기존 사이트:** https://evealba.co.kr/ (기능·브랜드·데이터 100% 유지)  
> **실제 테마 경로:** `theme/evealba/` (v1 지시서의 `skin/zero`는 **사용하지 않음**)

---

## 0. v1 대비 v2 핵심 변경

| v1 (잘못된 가정) | v2 (실제 구조) |
|------------------|----------------|
| `skin/zero/basic/` | `theme/evealba/` |
| `g5_write_recruit` 게시판 | `g5_jobs_register` + `extend/jobs_list_helper.php` |
| `skin/board/recruit/list.skin.php` | `jobs.php` → `jobs_main.php`, `index_main.php` |
| `bo_table=chat` | `plugin/chat/eve_chat_frame.php`, `chat_ajax.php` |
| `bo_table=sudabang` | `sudabang.php` (독립 페이지) |
| `/bbs/register.php` | `/eve_register.php` |
| `/mypage` | 분산: `jobs_ongoing.php`, `resume_register.php`, `member_confirm` 등 |

---

## 1. 핵심 원칙 (절대 준수)

1. **백업 선행** — UI 작업 전 반드시 백업·브랜치 생성 (2장 참고)
2. **레이아웃만 Readdy형** — 인스타그램 스타일 3컬럼(PC) / 하단 탭(모바일)
3. **비즈니스 로직·DB·URL 유지** — `g5_jobs_register`, 결제·점프·승인·SEO URL 변경 금지
4. **단일 데이터 소스** — 채용 목록은 `get_jobs_by_type()`, 카드는 `render_job_card()` 등 기존 헬퍼 사용
5. **ZERO REGRESSION** — 기존 엔드포인트·관리자·크론·AJAX 삭제·축소 금지
6. **성인 고지·카카오·이미지 경로** — 푸터 문구, `kakao-btn`, `/data/` 경로 유지

---

## 2. 백업 절차 (작업 시작 전 필수)

### 2.1 백업 대상

| 구분 | 경로 | 비고 |
|------|------|------|
| 테마 전체 | `theme/evealba/` | **최우선** |
| 채용 헬퍼 | `extend/jobs_list_helper.php` | 카드·조회 로직 |
| 채용 진입점 | `jobs.php`, `jobs_view.php`, `jobs_register.php` 등 루트 `jobs*.php` | URL 라우팅 |
| 채팅 플러그인 | `plugin/chat/` | UI 연동 시 |
| CSS 백업 | `theme/evealba/css/evealba.css` | 2,400줄+ — 단독 복사 권장 |

### 2.2 백업 실행 순서

```bash
# 1) Git 브랜치 생성 (main에서 분기)
cd D:\evealba
git checkout main
git pull origin main
git checkout -b feature/ui-renewal-readdy

# 2) 테마 스냅샷 백업 (날짜 접미사)
$date = Get-Date -Format "yyyyMMdd_HHmm"
Copy-Item -Recurse "theme\evealba" "theme\evealba_backup_$date"

# 3) 핵심 단일 파일 백업
Copy-Item "extend\jobs_list_helper.php" "extend\jobs_list_helper_backup_$date.php"
Copy-Item "theme\evealba\css\evealba.css" "theme\evealba\css\evealba_backup_$date.css"
```

### 2.3 백업 완료 체크리스트

- [ ] `theme/evealba_backup_YYYYMMDD_HHMM/` 폴더 존재
- [ ] `jobs_list_helper_backup_*.php` 존재
- [ ] `evealba_backup_*.css` 존재
- [ ] `feature/ui-renewal-readdy` 브랜치에서 작업 중
- [ ] **main에 직접 push 하지 않음** (완료·QA 후 merge)

### 2.4 롤백 방법 (문제 발생 시)

| 상황 | 해결 |
|------|------|
| CSS만 깨짐 | `evealba_backup_*.css` → `evealba.css` 복원 |
| 테마 전체 이상 | `theme/evealba_backup_*` → `theme/evealba` 덮어쓰기 |
| 헬퍼 함수 오류 | `jobs_list_helper_backup_*.php` 복원 |
| Git으로 되돌리기 | `git checkout main -- theme/evealba` (브랜치 작업분 폐기) |
| 서버 배포 후 문제 | 서버 `/var/www/evealba`에서 `git reset --hard <이전커밋>` |

---

## 3. 브랜드 컬러 (기존 유지 — `evealba.css`와 통합)

```css
:root {
  /* 기존 변수명 유지 + v2 별칭 추가 */
  --hot-pink: #FF1B6B;      /* = --pink-main */
  --deep-pink: #C90050;
  --light-pink: #FF6BA8;    /* = --pink-light */
  --pale-pink: #FFD6E7;
  --bg: #FFF0F5;            /* = --pink-pale */
  --dark2: #2D0020;         /* = --dark */
  --orange: #FF6B35;
  --purple-main: #8B2FC9;

  /* 광고 등급 (jr_ad_labels 기준 표시용) */
  --grade-vip: #C9860A;
  --grade-udae: #E65100;    /* 우대 */
  --grade-premium: #FF1B6B;
  --grade-special: #FF6B35;
  --grade-urgent: #D32F2F;  /* 급구 */
  --grade-recommend: #AD1457; /* 추천 */
}
```

**주의:** CSS 변수 추가만 허용. 기존 `.g1`~`.g12` 그라데이션 클래스 삭제 금지.

---

## 4. 전체 레이아웃 구조

### 4.1 PC (1024px 이상) — 3컬럼

```
┌──────────────────────────────────────────────────────────────┐
│  top-bar + header(슬림) + 급구 ticker (기존 head_top.php)    │
├──────────┬─────────────────────────────┬─────────────────────┤
│ 좌 220px │ 중앙 피드 (가변)             │ 우 270px            │
│ sidebar  │ story-slider                │ panel-right         │
│ _nav     │ view-toggle (list/feed/grid)│ 필터·추천·알림·채팅 │
│ 로그인   │ recruit-feed (카드 피드)     │                     │
│ 지역검색 │                             │                     │
│ 고객지원 │                             │                     │
└──────────┴─────────────────────────────┴─────────────────────┘
│ footer (성인 고지 유지)                                        │
└──────────────────────────────────────────────────────────────┘
```

### 4.2 모바일 (768px 이하)

- 좌측 사이드바·우측 패널 **숨김**
- 상단: 로고 + 검색 아이콘 (슬림 header)
- 하단: **고정 탭바 4개** (`mobile-tabbar`)
- 기존 `mobileSlideMenu`는 **보조 메뉴**로 유지 (완전 삭제 금지)

### 4.3 레이아웃 CSS 목표

```css
/* evealba.css 내 .page-layout 확장 */
.page-layout-renewal {
  max-width: 1200px;
  margin: 14px auto 0;
  padding: 0 14px;
  display: grid;
  grid-template-columns: 220px minmax(0, 1fr) 270px;
  gap: 14px;
  align-items: start;
}
@media (max-width: 1023px) {
  .page-layout-renewal { grid-template-columns: 1fr; }
  .sidebar-nav-renewal, .panel-right { display: none; }
}
@media (max-width: 768px) {
  .mobile-tabbar { display: grid; }
  body { padding-bottom: 70px; }
}
```

**전략:** 기존 `.page-layout`을 한 번에 바꾸지 말고, `.page-layout-renewal` 클래스를 **단계적으로** 적용 후 QA 통과 시 기본값 전환.

---

## 5. 실제 파일 수정·신규 목록

### 5.1 공통 레이아웃

| 파일 | 작업 | 우선순위 |
|------|------|----------|
| `theme/evealba/inc/head_top.php` | header 슬림화, 가로 nav 6개 → 좌측 nav로 이전 준비 | P1 |
| `theme/evealba/inc/sidebar_nav_renewal.php` | **신규** — Readdy형 좌측 4메뉴 | P1 |
| `theme/evealba/inc/panel_right.php` | **신규** — 검색·추천·알림·채팅 | P2 |
| `theme/evealba/inc/sidebar_main.php` | 기존 위젯 유지, nav는 renewal로 분리 | P2 |
| `theme/evealba/head.php` | 3컬럼 grid, panel_right include | P2 |
| `theme/evealba/head_jobs.php` | head.php와 동일 레이아웃 적용 | P3 |
| `theme/evealba/head_sudabang.php` | 동일 | P3 |
| `theme/evealba/head_talent.php` | 동일 | P3 |
| `theme/evealba/tail.php` | 모바일 탭바 include | P2 |
| `theme/evealba/mobile/tail.php` | 하단 탭바 | P2 |
| `theme/evealba/css/evealba_renewal.css` | **신규** — 리뉴얼 전용 (기존 CSS 병행 로드) | P1 |
| `theme/evealba/js/evealba_renewal.js` | **신규** — 스토리·뷰전환·탭 active | P2 |
| `theme/evealba/head.sub.php` | renewal CSS/JS 조건부 로드 | P1 |

### 5.2 메인·채용

| 파일 | 작업 |
|------|------|
| `theme/evealba/index_main.php` | 피드형 재배치, quick-stats 스타일 |
| `theme/evealba/jobs_main.php` | 피드 통합 + view-toggle |
| `extend/jobs_list_helper.php` | `render_job_card_feed()` 등 **추가** (기존 함수 삭제 금지) |
| `theme/evealba/inc/story_slider.php` | **신규** — 스토리 UI |
| `theme/evealba/inc/recruit_feed.php` | **신규** — 피드 컨테이너 |
| `theme/evealba/jobs_view_main.php` | 상세 페이지 카드형 스킨 (로직 유지) |

### 5.3 플로팅 → 우측 패널 이전

| 기존 | 변경 |
|------|------|
| `theme/evealba/inc/float_banners.php` | PC: panel_right로 이전, **함수·쿼리 재사용** |
| float CTA (카카오/채팅/맨위로) | 모바일 FAB 또는 탭바에 유지 |

### 5.4 수정하지 않는 파일 (로직 보존)

- `jobs_register_update.php`, `jobs_good.php`, `jobs_jump*.php`
- `adm/jobs_*`, `migrations/*`
- `plugin/chat/chat_ajax.php` (UI만 iframe/래퍼 변경)
- 루트 `jobs.php`, `jobs_view.php` URL 라우팅

---

## 6. 네비게이션·URL 매핑 (실제 경로)

### 6.1 좌측 / 하단 4탭

| 메뉴 | URL | active 판별 |
|------|-----|-------------|
| 구인구직 | `G5_URL` / `jobs.php` / `talent.php` | `_INDEX_`, `_JOBS_`, `_TALENT_` |
| 커뮤니티 | `sudabang.php`, `board.php?bo_table=used` | `_SUDABANG_`, used |
| 알림&채팅 | `memo_full.php`, 채팅 진입 JS | memo, chat |
| 마이페이지 | 로그인 분기 (아래 6.2) | member 관련 |

### 6.2 마이페이지 분기 (기존 mobileSlideMenu 로직 이식)

```php
// 사업자 (mb_1 = biz)
jobs_ongoing.php, jobs_ended.php, jobs_jump_shop.php, jobs_payment_history.php

// 구직자
resume_register.php, talent_view.php, jobs_scrap_list.php

// 공통
member_confirm.php (회원정보), logout.php
```

### 6.3 채팅 진입 (기존 유지)

```javascript
// 기존 패턴 그대로 사용
var u = G5_PLUGIN_URL + '/chat/eve_chat_frame.php';
window.open(u, 'eveChatPopup', 'width=420,height=720,scrollbars=no,resizable=yes');
// v2: 모바일에서는 location 또는 bottom sheet 래퍼 검토 (plugin 내부 URL 동일)
```

---

## 7. 컴포넌트별 작업 상세

### COMPONENT 1: 좌측 사이드바 (`inc/sidebar_nav_renewal.php`)

**데이터 소스:** 변경 없음  
**기존 재사용:** `sidebar_login_widget.php`, `sidebar_cs_widget.php`, 지역 그리드 (`sidebar_main.php`)

```php
// 메뉴 4개 — head_top.php 가로 nav 항목을 세로로 이전
$_base = rtrim(G5_URL, '/');
$nav_items = [
  ['key'=>'jobs',   'icon'=>'🏠', 'label'=>'구인구직', 'href'=>$_base.'/jobs.php'],
  ['key'=>'community', 'icon'=>'💬', 'label'=>'커뮤니티', 'href'=>$_base.'/sudabang.php'],
  ['key'=>'notify', 'icon'=>'🔔', 'label'=>'알림 & 채팅', 'href'=>$_base.'/memo_full.php'],
  ['key'=>'mypage', 'icon'=>'👤', 'label'=>'마이페이지', 'href'=>$is_member ? G5_BBS_URL.'/member_confirm.php?url=...' : G5_BBS_URL.'/login.php'],
];
```

**실수 방지:** `bo_table=chat`, `/mypage` URL 사용 금지.

---

### COMPONENT 2: 스토리 슬라이더 (`inc/story_slider.php`)

**데이터 소스 (v1 SQL 사용 금지):**

```php
@include_once(G5_PATH.'/extend/jobs_list_helper.php');
$_story_udae    = get_jobs_by_type('우대', 8);
$_story_premium = get_jobs_by_type('프리미엄', 7);
$_story_items   = array_merge($_story_udae, $_story_premium);
// 썸네일: jr_data JSON 내 thumb_* 필드, render_job_card와 동일 파싱 로직 재사용
```

**링크:** `jobs_view.php?jr_id=` 또는 `_jlh_clean_url()` SEO URL

**등급 링 컬러:** `jr_ad_labels`에 '우대'/'프리미엄' 포함 여부로 class 결정

---

### COMPONENT 3: 리스트/피드/그리드 뷰 전환

**적용 위치:** `jobs_main.php`, `index_main.php` (채용 섹션)

```html
<div class="view-toggle" role="tablist">
  <button type="button" class="view-btn active" data-view="feed">피드</button>
  <button type="button" class="view-btn" data-view="list">리스트</button>
  <button type="button" class="view-btn" data-view="grid">그리드</button>
</div>
<div id="recruitContainer" class="recruit-container view-feed">
```

**JS:** `evealba_renewal.js` — `localStorage.preferredView` (기본값 `feed`)

**CSS 클래스:**
- `.view-feed` — 인스타 카드 (세로 풀폭)
- `.view-list` — 가로 리스트
- `.view-grid` — 2열 그리드

**광고 등급 노출 정책 (필수):**

| 순서 | 유형 | 피드 내 처리 |
|------|------|-------------|
| 1 | 우대 | 상단 고정 슬롯 (최대 전체 노출) |
| 2 | 프리미엄 | 우대 다음 |
| 3 | 스페셜 | 섹션 배지 또는 카드 border |
| 4 | 급구 | 스토리 링 + 카드 배지 |
| 5 | 추천 | 우측 패널 + 피드 중간 삽입 |
| 6 | 줄광고 | 시간순 피드 본문 |

`get_jobs_by_type()` 호출 순서와 기존 섹션 우선순위를 **동일하게** 유지.

---

### COMPONENT 4: 광고 카드 (`extend/jobs_list_helper.php` 확장)

**기존 함수 유지:** `render_job_card()`, `render_urgency_card()`, `render_premium_card()`

**신규 추가 (예시):**

```php
function render_job_card_feed($row, $view = 'feed') {
    // render_job_card 내부 데이터 파싱 재사용
    // view에 따라 wrapper class만 변경
}
```

**표시 필드 (기존 DB):**
- `jr_nickname`, `jr_company`, `jr_title`, `jr_ad_labels`
- `jr_data` → 지역, 임금, 썸네일, AI intro
- `jr_good` (좋아요 — `jobs_good.php`)
- 광고일수 — 기존 카드 footer 로직 동일

**삭제 금지:** HOT/NEW, crown 배지, 점프 정렬 (`jr_jump_datetime`)

---

### COMPONENT 5: 우측 패널 (`inc/panel_right.php`)

| 섹션 | 기존 소스 |
|------|-----------|
| 채용 검색 | `jobs_main.php` filter-box (GET `er_id`, `erd_id`, `ei_id`, `ej_id`, `ec_id`, `stx`) |
| 추천 구인 | `float_banners.php` — `g5_special_banner` + `render_premium_card` |
| 새 알림 | `get_memo_not_read()`, 쪽지 latest |
| 1:1 채팅 | 채팅 플러그인 진입 버튼 + 최근 대화 (있으면) |

**PC 전환 시:** `float_banners.php`의 PC 플로팅 패널은 `panel_right` 적용 페이지에서 **중복 출력 방지** (`define('_PANEL_RIGHT_DONE_')` 플래그 사용)

---

### COMPONENT 6: 기존 핵심 기능 (UI만)

| 기능 | 파일 | 변경 범위 |
|------|------|-----------|
| AI 소개글 | `jobs_register_main.php` | 버튼·카드 스킨만 |
| AI 썸네일 | `jobs_thumb_shop_main.php` | 옵션 UI 카드형 |
| 채팅 | `plugin/chat/eve_chat_frame.php` | 말풍선 CSS (PHP 로직 유지) |
| 급구 티커 | `inc/head_top.php` | 스타일 정리, 삭제 금지 |
| 통계 | `index_main.php` `.quick-stats` | 카드형 CSS |
| 카카오 | `head_top.php` `.kakao-btn` | 유지 |

---

### COMPONENT 7: 커뮤니티 (`sudabang_main.php`)

**탭 UI (표시용):** 전체 | 인기 | 자유 | 홍보 | 기업

**실제 연결:** 기존 게시판 `bo_table` 및 `sudabang_main.php` 쿼리에 맞게 매핑 (임의 카테고리 DB 추가 금지)

---

### COMPONENT 8: 모바일 하단 탭바 (`inc/mobile_tabbar.php`)

```php
// tail.php, mobile/tail.php에서 include
// 4탭: 홈(/), 커뮤니티(sudabang.php), 채팅(메모/채팅), 마이(로그인 분기)
// 배지: get_memo_not_read() — 기존 mobile-quick-menu 배지 로직 재사용
```

---

## 8. CSS/JS 로드 전략 (기존과 병행)

`head.sub.php`에 추가:

```php
// 리뉴얼 플래그 — 단계적 적용용
if (defined('EVEALBA_RENEWAL_UI') && EVEALBA_RENEWAL_UI) {
    $_renewal_ver = filemtime(G5_THEME_PATH.'/css/evealba_renewal.css');
    echo '<link rel="stylesheet" href="'.G5_THEME_CSS_URL.'/evealba_renewal.css?ver='.$_renewal_ver.'">'.PHP_EOL;
    echo '<script src="'.G5_THEME_URL.'/js/evealba_renewal.js?ver='.$_renewal_ver.'" defer></script>'.PHP_EOL;
}
```

**단계 적용:**
1. `index.php`만 `define('EVEALBA_RENEWAL_UI', true);`
2. QA 후 `jobs.php`, `sudabang.php` … 순차 확대
3. 전체 QA 후 상수를 `head.sub.php` 기본값으로 전환

---

## 9. 작업 우선순위·일정

| 순서 | 작업 | 난이도 | 예상 | 완료 기준 |
|------|------|--------|------|-----------|
| 0 | **백업·브랜치** | ⭐ | 0.5h | 2장 체크리스트 전부 ✓ |
| 1 | `evealba_renewal.css` 변수·grid | ⭐ | 2h | 3컬럼 PC / 1컬럼 모바일 표시 |
| 2 | `sidebar_nav_renewal.php` | ⭐⭐ | 3h | 4메뉴 링크 정상 |
| 3 | `head.php` 3컬럼 레이아웃 | ⭐⭐ | 3h | 메인만 renewal 적용 |
| 4 | `panel_right.php` | ⭐⭐ | 4h | 추천·필터·쪽지 표시 |
| 5 | `story_slider.php` | ⭐⭐ | 3h | 우대/프리미엄 15건 |
| 6 | `render_job_card_feed` + 피드 | ⭐⭐⭐ | 6h | 등급 순서 유지 |
| 7 | view-toggle JS | ⭐⭐ | 3h | 3뷰 전환·localStorage |
| 8 | `mobile_tabbar.php` | ⭐⭐⭐ | 4h | 768px 이하 탭 동작 |
| 9 | `head_jobs` 등 서브헤더 통일 | ⭐⭐ | 4h | 채용·인재·수다방 동일 레이아웃 |
| 10 | jobs_view·register 스킨 | ⭐⭐⭐ | 6h | 등록·결제·점프 정상 |
| 11 | float → panel 중복 제거 | ⭐⭐ | 2h | PC 이중 추천업소 없음 |
| 12 | 전체 QA·회귀 테스트 | ⭐⭐⭐ | 8h | 10장 체크리스트 전부 ✓ |

**총 예상:** 48~55시간 (v1의 29h보다 현실적)

---

## 10. QA·회귀 테스트 체크리스트

### 10.1 레이아웃
- [ ] PC 1024+ : 3컬럼 정상
- [ ] 태블릿 : 우측 패널 숨김 또는 하단 이동
- [ ] 모바일 : 하단 탭 fixed, 콘텐츠 가림 없음

### 10.2 채용 (핵심)
- [ ] `jobs.php` 필터 검색 (지역·직종·키워드)
- [ ] 광고 등급별 노출 순서 (우대→프리미엄→…)
- [ ] `jobs_view.php` 상세·AI소개·썸네일
- [ ] `jobs_register.php` 등록·결제·승인 대기
- [ ] `jobs_ongoing.php` / `jobs_ended.php`
- [ ] `jobs_jump_shop.php` 점프 구매
- [ ] `jobs_good.php` 좋아요
- [ ] SEO URL `/jobs/지역/직종/...` 동작

### 10.3 회원·기타
- [ ] `eve_register.php` 일반/사업자 가입
- [ ] 로그인/로그아웃
- [ ] `talent.php` / `resume_register.php`
- [ ] `sudabang.php` 게시글 목록·글쓰기
- [ ] 중고거래 `bo_table=used`
- [ ] `memo_full.php` 쪽지
- [ ] 채팅 팝업/iframe
- [ ] 급구 티커 스크롤
- [ ] 푸터 성인 고지 문구
- [ ] 카카오 버튼

### 10.4 관리자 (변경 없어도 확인)
- [ ] `adm/jobs_register_list.php` 승인
- [ ] `adm/eve_special_banner.php` 추천업소

---

## 11. 단계별 실행 액션플랜

### Phase 0 — 백업 (필수, 30분)
1. `git checkout -b feature/ui-renewal-readdy`
2. `theme/evealba` 폴더 전체 복사 백업
3. `jobs_list_helper.php`, `evealba.css` 단독 백업
4. 백업 경로를 이 문서 하단에 기록

### Phase 1 — 기반 (반나절)
1. `evealba_renewal.css` 생성 (변수·grid·탭바·사이드바)
2. `evealba_renewal.js` 생성 (뷰 토글·스토리 스크롤)
3. `head.sub.php`에 조건부 로드
4. `index.php`에만 `EVEALBA_RENEWAL_UI` 활성화

### Phase 2 — 레이아웃 골격 (1일)
1. `sidebar_nav_renewal.php` 작성
2. `panel_right.php` 작성 (float_banners 쿼리 복사)
3. `head.php` — 3컬럼 + include
4. 메인에서 기존 가로 nav 숨김 (CSS), 사이드 nav 표시

### Phase 3 — 피드·스토리 (1~2일)
1. `story_slider.php` — `get_jobs_by_type` 연동
2. `jobs_list_helper.php`에 `render_job_card_feed()` **추가**
3. `index_main.php` / `jobs_main.php` 피드 컨테이너
4. view-toggle 연동

### Phase 4 — 모바일 (1일)
1. `mobile_tabbar.php`
2. `mobile/tail.php`, `tail.php` include
3. `mobileSlideMenu`와 역할 중복 정리 (탭=주, 슬라이드=전체 메뉴)

### Phase 5 — 서브페이지 확장 (2일)
1. `head_jobs.php`, `head_talent.php`, `head_sudabang.php` 통일
2. `jobs_view_main.php` 스킨
3. `float_banners` PC 중복 제거

### Phase 6 — QA·배포 (1일)
1. 10장 체크리스트 전항목
2. `feature/ui-renewal-readdy` → `main` merge
3. GitHub push → Actions 자동 배포
4. evealba.co.kr 스모크 테스트

---

## 12. 같은 실수 재발 시 해결책

| 실수 | 증상 | 해결 |
|------|------|------|
| v1 지시서대로 `g5_write_recruit` 쿼리 사용 | 스토리·카드 빈 화면 | `get_jobs_by_type()` + `g5_jobs_register`로 교체 |
| `skin/zero` 경로 수정 | 파일 not found | `theme/evealba/` 경로 사용 |
| 기존 `render_job_card` 삭제·대체 | 채용목록 전면 오류 | 신규 함수 **추가**만, 기존 함수 유지 |
| `.page-layout` 즉시 전면 교체 | 서브페이지 레이아웃 깨짐 | `.page-layout-renewal` 단계 적용 |
| float + panel 동시 출력 | 추천업소 2중 표시 | `_PANEL_RIGHT_DONE_` / `_FLOAT_BANNERS_SKIP_` 플래그 |
| renewal CSS만 로드 | 기존 페이지 스타일 붕괴 | `evealba.css` + `evealba_renewal.css` 병행 |
| 모바일 탭만 추가하고 padding 미적용 | 하단 콘텐츠 가림 | `body { padding-bottom: 70px }` |
| 채용 필터 GET 파라미터 변경 | 검색·북마크 깨짐 | `er_id`, `erd_id` 등 **기존 이름 유지** |
| main 직접 push | 배포 후 전체 장애 | 반드시 feature 브랜치 → QA → merge |
| 백업 없이 작업 | 롤백 불가 | Phase 0 생략 금지 — `evealba_backup_*` 복원 |

---

## 13. 배포·검증

### 로컬/스테이징 확인 URL
- 메인: `/`
- 채용: `/jobs.php`
- 채용상세: `/jobs_view.php?jr_id=1` (실제 ID 사용)
- 등록: `/jobs_register.php`
- 수다방: `/sudabang.php`
- 쪽지: `/memo_full.php`

### 배포 (기존 파이프라인)
- 브랜치: `feature/ui-renewal-readdy` → QA 후 `main` merge
- GitHub Actions: `main` push 시 서버 `/var/www/evealba` 자동 배포
- 배포 후: `php run_migration.php` (기존 워크플로 — 스키마 변경 없으면 영향 없음)

### 배포 후 롤백
```bash
# 서버
cd /var/www/evealba
git log --oneline -5   # 이전 커밋 확인
git reset --hard <이전_커밋해시>
```

---

## 14. 백업 기록 (작업 시填写)

| 항목 | 경로/값 | 일시 |
|------|---------|------|
| Git 브랜치 | `feature/ui-renewal-readdy` | 2026-06-13 23:23 |
| 테마 백업 | `theme/evealba_backup_20260613_2323/` (파일 335개) | 2026-06-13 23:23 |
| CSS 백업 | `theme/evealba/css/evealba_backup_20260613_2323.css` | 2026-06-13 23:23 |
| 헬퍼 백업 | `extend/jobs_list_helper_backup_20260613_2323.php` | 2026-06-13 23:23 |
| 작업 시작 커밋 | `ac342bde6c4039a0fd92af2bca0f2050d9bd5d93` | 2026-06-13 23:23 |

### Phase 0 체크리스트
- [x] `theme/evealba_backup_20260613_2323/` 폴더 존재
- [x] `jobs_list_helper_backup_20260613_2323.php` 존재
- [x] `evealba_backup_20260613_2323.css` 존재
- [x] `feature/ui-renewal-readdy` 브랜치에서 작업 중
- [x] main에 직접 push 하지 않음 (완료·QA 후 merge)

### Phase 1~5 진행 기록 (2026-06-13)
- [x] Phase 1: `evealba_renewal.css`, `evealba_renewal.js`, `head.sub.php`, `index.php`
- [x] Phase 2: `sidebar_nav_renewal.php`, `panel_right.php`, `page_layout_open.php`, `head.php`
- [x] Phase 3: `story_slider.php`, `recruit_feed.php`, `render_job_card_feed()`, `index_main.php`, `jobs_main.php`
- [x] Phase 4: `mobile_tabbar.php`, `tail.php` 연동
- [x] Phase 5: `head_jobs.php`, `head_talent.php`, `head_sudabang.php`, `jobs/talent/sudabang.php`, `float_banners.php`
- [x] Phase 6: 전역 `extend/evealba_renewal_ui.extend.php`, 나머지 head 통일, memo/등록/CS/이력서, 커밋·배포

---

## 15. 참고 URL

- Readdy 참고 UI: https://pcimzg.readdy.co/
- 기존 이브알바: https://evealba.co.kr/
- GitHub: https://github.com/kt87490931-afk/evealba
- 그누보드: https://sir.kr/

---

## 16. Cursor AI 작업 시 프롬프트 예시

```
이브알바_UI리뉴얼_작업지시서_v2.md Phase N을 진행해주세요.
- Phase 0 백업이 완료되었는지 먼저 확인
- theme/evealba만 수정, jobs_list_helper는 함수 추가만
- g5_jobs_register / get_jobs_by_type 사용, 게시판 recruit 사용 금지
- 완료 후 수정 파일 목록·줄 수·검증 URL 보고
```

---

**문서 버전:** v2.0  
**작성 기준:** 실제 코드베이스 `D:\evealba` 분석 반영  
**다음 단계:** Phase 0 백업 실행 → Phase 1 착수
