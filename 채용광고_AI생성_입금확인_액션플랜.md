# 채용광고 AI 생성 · 입금확인 · 폼 수정 액션플랜

## 0. 개요

### 0.1 이해한 요구사항

| 구분 | 내용 |
|------|------|
| **입금대기중** | 목록에 보이되, 클릭해도 상세 열리지 않음 (읽기 차단) |
| **입금확인 후** | AI소개글 종합정리 + 선택 톤으로 AI 생성 → eve_alba_ad_post 스타일 폼에 표시 |
| **의도** | 기업회원이 단계별로 쉽게 이해·사용할 수 있게 |

### 0.2 추가 고려사항

- **입금대기중 클릭 시**: "입금확인 후 이용 가능합니다" 안내 (목록에서 "입금확인 후 이용 가능" 문구 노출)
- **AI 생성 실패 시**: "AI 생성 처리 중" 표시, 재시도·관리자 개입 방식 정의
- **입금확인 직후**: AI 미완성 시 "AI 소개글 생성 중입니다" 표시

### 0.3 배포 규칙

- **각 Part 완료 시 반드시 자동 배포** (Git push + 서버 SCP 또는 deploy.ps1 -PushToServer)

---

## Part 1: 입금대기중 상세 열람 차단

### 1.1 목표

- 입금대기중인 채용정보: 목록 클릭 시 상세 페이지 진입 불가
- 클릭 시 "입금확인 후 이용 가능합니다" 안내

### 1.2 액션 항목

| 순번 | 액션 | 파일 | 산출물 |
|------|------|------|--------|
| 1.1 | `jobs_ongoing_main.php` 리스트: 입금대기중 행 클릭 시 `jobs_view.php` 대신 `#` 또는 모달 안내 | theme/evealba/jobs_ongoing_main.php | 클릭 시 상세 진입 차단 |
| 1.2 | 입금대기중 항목에 "입금확인 후 이용 가능" 문구 또는 배지 표시 | theme/evealba/jobs_ongoing_main.php | 시각적 안내 |
| 1.3 | `jobs_view.php` 진입 시 입금대기중이면 리다이렉트 또는 안내 화면 표시 (직접 URL 접근 차단) | theme/evealba/jobs_view_main.php 또는 jobs_view.php | URL 직접 접근 차단 |

### 1.3 해결책 (실패 시)

- 링크가 여전히 상세로 이동할 경우: `jobs_ongoing_main.php`의 view_href 생성 로직에서 status 체크 추가
- URL 직접 접근: `jobs_view_main.php` 상단에서 `$status === 'pending' && !$payment_ok` 체크 후 `alert + history.back()` 또는 전용 안내 페이지 출력

### 1.4 완료 후

- **자동 배포 실행**

---

## Part 2: eve_alba_ad_post 스타일 폼 완성

### 2.1 목표

- `jobs_view_main.php`를 eve_alba_ad_post.html 스타일로 전면 교체
- 기준: `c:\Users\DOGE\Downloads\eve_alba_ad_post.html`

### 2.2 폼 섹션 구조 (eve_alba_ad_post.html 기준)

| 섹션 | 데이터 소스 | 비고 |
|------|-------------|------|
| 상단 배너 | 업종, 업소명, 채용제목, 태그(위치·급여·편의) | jr_data 기반 |
| 기본 정보 테이블 | 업소명, 연락처, SNS, 급여, 근무지역, 업종 | 고정 필드 |
| 인사말 | ai_intro | AI 생성 |
| 포인트 카드 4개 | ai_content 또는 파싱 | AI 생성 (옵션: 추후 파싱) |
| 상세 섹션 | 업소위치(ai_location), 근무환경(ai_env), 혜택(ai_benefit), 복리후생 | AI 생성 |
| 언니 사장의 약속 | ai_wrapup | AI 생성 |
| 연락처 CTA | 카카오·라인·텔레그램 버튼, 전화번호 | job_contact, job_kakao 등 |

### 2.3 액션 항목

| 순번 | 액션 | 파일 | 산출물 |
|------|------|------|--------|
| 2.1 | 상단 배너 HTML·스타일 적용 (그라데이션, 업종뱃지, 업소명, 채용제목, 태그) | theme/evealba/jobs_view_main.php | 배너 영역 |
| 2.2 | 기본 정보 테이블 (eve_alba_ad_post 2번째 섹션) 적용 | theme/evealba/jobs_view_main.php | 기본정보 테이블 |
| 2.3 | 인사말·상세·언니의 약속·CTA 영역 적용 | theme/evealba/jobs_view_main.php | 전체 폼 구조 |
| 2.4 | 포인트 카드 4개 ("이런 점이 달라요") — AI 데이터 매핑 또는 임시 비노출 | theme/evealba/jobs_view_main.php | 카드 영역 (옵션) |
| 2.5 | CSS 분리 또는 eve_skin/style.css에 eve_alba_ad_post 스타일 추가 | theme/evealba/skin/board/eve_skin/style.css | 스타일 |

### 2.4 해결책 (실패 시)

- 스타일 깨짐: 인라인 스타일 우선, 외부 CSS는 `!important` 최소화
- 데이터 매핑 누락: jr_data 키 목록과 HTML 필드 매핑표 작성 후 검수

### 2.5 완료 후

- **자동 배포 실행**

---

## Part 3: 섹션별 수정 버튼 및 편집 기능

### 3.1 목표

- 각 섹션 우측 상단에 ✏️ 수정 버튼 배치
- 기본정보 섹션만 수정 / 문구영역만 수정 등 **섹션 단위 편집**

### 3.2 섹션별 수정 범위

| 섹션 | 수정 가능 필드 | 저장 대상 |
|------|----------------|----------|
| 기본 정보 | 업소명, 연락처, SNS, 급여, 근무지역, 업종 | jr_data (해당 키들) |
| 인사말 | ai_intro | jr_data.ai_intro |
| 업소 위치 | ai_location | jr_data.ai_location |
| 근무환경 | ai_env | jr_data.ai_env |
| 혜택·복리후생 | ai_benefit | jr_data.ai_benefit |
| 언니의 약속 | ai_wrapup | jr_data.ai_wrapup |
| (레거시) ai_content | ai_content | jr_data.ai_content |

### 3.3 액션 항목

| 순번 | 액션 | 파일 | 산출물 |
|------|------|------|--------|
| 3.1 | 기본정보 섹션 헤더에 수정 버튼 추가, 클릭 시 인라인 편집 또는 모달 | theme/evealba/jobs_view_main.php | 기본정보 수정 UI |
| 3.2 | 기본정보 수정용 AJAX 엔드포인트 (또는 jobs_ai_section_save 확장) | jobs_ai_section_save.php 또는 jobs_basic_info_save.php | 기본정보 저장 API |
| 3.3 | 문구영역(인사말, 업소위치 등) 각 섹션 헤더에 수정 버튼 (기존 구현 활용) | theme/evealba/jobs_view_main.php | 문구 수정 버튼 |
| 3.4 | 수정 권한: 입금확인(또는 진행중) 상태인 경우에만 수정 버튼 노출 | theme/evealba/jobs_view_main.php | 권한 제어 |

### 3.4 해결책 (실패 시)

- 기존 jobs_ai_section_save.php가 ai_intro 등만 지원 시: 기본정보용 별도 엔드포인트 생성
- 인라인 편집 UX: textarea/input 토글 방식 유지 (기존 jobs_view_main 패턴)

### 3.5 완료 후

- **자동 배포 실행**

---

## Part 4: 입금확인 → AI 생성 · 상태 표시

### 4.1 목표

- 입금확인 시 AI 큐 등록 (기존 jobs_register_confirm_update 로직 활용)
- AI 미완성 시 "AI 소개글 생성 중입니다" 표시
- AI 생성 실패 시 "AI 생성 처리 중" 또는 재시도 안내

### 4.2 액션 항목

| 순번 | 액션 | 파일 | 산출물 |
|------|------|------|--------|
| 4.1 | 입금확인 시 g5_jobs_ai_queue 등록 확인 (adm/jobs_register_confirm_update.php) | adm/jobs_register_confirm_update.php | 큐 등록 검증 |
| 4.2 | jobs_view_main: ai_intro 등이 비어있고 큐 status가 pending/processing이면 "AI 소개글 생성 중입니다" 문구 표시 | theme/evealba/jobs_view_main.php | 로딩 상태 UI |
| 4.3 | AI 생성 실패 시 (g5_jobs_ai_queue status=failed) "AI 생성에 실패했습니다. 관리자에게 문의하세요" 또는 재시도 버튼 | theme/evealba/jobs_view_main.php | 실패 안내 |
| 4.4 | (선택) 관리자 재시도: adm에서 failed 건 재큐잉 기능 | adm/jobs_ai_fix_bad_content.php 등 | 재시도 기능 |

### 4.3 해결책 (실패 시)

- 큐 등록 누락: jobs_register_confirm_update.php에서 INSERT g5_jobs_ai_queue 로직 재확인
- 상태 판단: jr_data에 ai_intro 등 없음 + g5_jobs_ai_queue 해당 jr_id가 pending/processing → "생성 중"

### 4.4 완료 후

- **자동 배포 실행**

---

## Part 5: 톤 선택 반영 및 AI 프롬프트 검증

### 5.1 목표

- 채용정보등록 시 선택한 톤(언니/남사장/전문가)이 jr_data.ai_tone에 저장됨
- 입금확인 후 큐 처리 시 해당 톤으로 AI 생성

### 5.2 액션 항목

| 순번 | 액션 | 파일 | 산출물 |
|------|------|------|--------|
| 5.1 | jobs_register_main 톤 선택 UI → job_data에 ai_tone 저장 확인 | theme/evealba/jobs_register_main.php | 톤 저장 검증 |
| 5.2 | jobs_ai_queue_process: formData에 ai_tone 전달 확인 | jobs_ai_queue_process.php | 큐 톤 전달 |
| 5.3 | gemini_api.lib / gemini_config 톤별 프롬프트 동작 검증 | lib/gemini_api.lib.php, extend/gemini_config.php | 톤 반영 확인 |

### 5.3 완료 후

- **자동 배포 실행**

---

## 6. 작업 순서 요약

```
Part 1: 입금대기중 상세 열람 차단
  → 자동 배포

Part 2: eve_alba_ad_post 스타일 폼 완성
  → 자동 배포

Part 3: 섹션별 수정 버튼 및 편집 기능
  → 자동 배포

Part 4: 입금확인 → AI 생성 · 상태 표시
  → 자동 배포

Part 5: 톤 선택 반영 및 AI 프롬프트 검증
  → 자동 배포
```

---

## 7. 관련 파일 경로

| 구분 | 경로 |
|------|------|
| 채용 상세 뷰 | theme/evealba/jobs_view_main.php |
| 채용 상세 진입점 | jobs_view.php |
| 진행중 리스트 | theme/evealba/jobs_ongoing_main.php |
| 입금확인 처리 | adm/jobs_register_confirm_update.php |
| AI 큐 워커 | jobs_ai_queue_process.php |
| AI 섹션 저장 | jobs_ai_section_save.php |
| eve 스킨 CSS | theme/evealba/skin/board/eve_skin/style.css |
| 기준 HTML | c:\Users\DOGE\Downloads\eve_alba_ad_post.html |

---

## 8. 배포 절차 (각 Part 완료 시)

1. `git add` (변경 파일)
2. `git commit -m "part N: ..."`
3. `git push origin main`
4. 서버 SCP 또는 `.\deploy.ps1 -Message "part N" -PushToServer`
