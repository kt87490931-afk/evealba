<?php
/**
 * 알림 & 채팅 — 메인 UI (evealba_chat.html)
 */
if (!defined('_GNUBOARD_')) exit;

$_ch_regions = array(
    array('name' => '서울', 'emoji' => '🏙️'),
    array('name' => '경기', 'emoji' => '🌿'),
    array('name' => '인천', 'emoji' => '⚓'),
    array('name' => '부산', 'emoji' => '🌊'),
    array('name' => '대구', 'emoji' => '🌹'),
    array('name' => '광주', 'emoji' => '🌸'),
    array('name' => '대전', 'emoji' => '🏞️'),
    array('name' => '울산', 'emoji' => '🏭'),
    array('name' => '강원', 'emoji' => '🏔️'),
    array('name' => '충청', 'emoji' => '🌾'),
    array('name' => '전라', 'emoji' => '🍃'),
    array('name' => '경상', 'emoji' => '⛵'),
);

$_ch_jobs_url = $_ch_base ? $_ch_base . '/jobs_view.php' : '/jobs_view.php';
?>
<div class="chat-hub-main" id="chatHubApp">

  <div class="main-tabs">
    <button type="button" class="main-tab<?php echo $_ch_tab === 'noti' ? ' active' : ''; ?>" data-tab="noti">🔔 알림 <span class="tab-cnt" id="tabCntNoti"><?php echo $_ch_unread ? (int)$_ch_unread : ''; ?></span></button>
    <button type="button" class="main-tab<?php echo $_ch_tab === 'chat' ? ' active' : ''; ?>" data-tab="chat">💬 1:1 채팅 <span class="tab-cnt" id="tabCntChat"><?php echo $_ch_unread ? (int)$_ch_unread : ''; ?></span></button>
    <button type="button" class="main-tab<?php echo $_ch_tab === 'region' ? ' active' : ''; ?>" data-tab="region">🗺️ 지역별 채팅</button>
  </div>

  <!-- 알림 -->
  <div class="tab-panel<?php echo $_ch_tab === 'noti' ? ' active' : ''; ?>" id="panel-noti">
    <div class="noti-filter">
      <button type="button" class="noti-filter-btn active" data-noti-filter="all">전체</button>
      <button type="button" class="noti-filter-btn" data-noti-filter="chat">채팅</button>
    </div>
    <div class="noti-list" id="notiList">
      <div class="noti-empty"><span class="e-icon">🔔</span><p>알림을 불러오는 중...</p></div>
    </div>
  </div>

  <!-- 1:1 채팅 -->
  <div class="tab-panel<?php echo $_ch_tab === 'chat' ? ' active' : ''; ?>" id="panel-chat">
<?php if (!$_ch_can_dm) { ?>
    <div class="biz-lock">
      <div class="lock-icon">🔒</div>
      <h3>1:1 채팅 이용 불가</h3>
      <p>1:1 채팅은 <strong>일반회원(여성)</strong>과 <strong>기업회원</strong>만 이용할 수 있습니다.<br>채팅은 채용공고 상세에서 시작할 수 있습니다.</p>
    </div>
<?php } else { ?>
    <div class="chat-wrap">
      <div class="chat-list-pane" id="chatListPane">
<?php if ($_ch_is_female) { ?>
        <div class="policy-banner">
          <strong>💬 업소에 먼저 채팅을 시작</strong>할 수 있습니다.<br>
          채용공고 상세의 「1:1 채팅」 버튼으로 시작해 주세요.
        </div>
<?php } else { ?>
        <div class="policy-banner biz">
          <strong>⚠️ 기업회원 안내</strong><br>
          먼저 메시지를 보낼 수 없습니다.<br>
          일반회원이 먼저 채팅을 시작한 경우에만 답장 가능합니다.
        </div>
<?php } ?>
        <div class="chat-search">
          <span>🔍</span>
          <input type="text" id="chatSearchInput" placeholder="채팅방 검색" autocomplete="off">
        </div>
        <div class="chat-room-list" id="chatRoomList">
          <div class="chat-list-empty"><span class="e-icon">💬</span><p>대화 목록을 불러오는 중...</p></div>
        </div>
      </div>

      <div class="chat-room-pane" id="chatRoomPane">
        <div class="chat-room-empty" id="chatEmpty">
          <div class="e-icon">💬</div>
          <h3>채팅방을 선택하세요</h3>
          <p>왼쪽 목록에서 대화할 상대를 선택하세요.<br>새 채팅은 채용공고 상세에서 시작할 수 있습니다.</p>
        </div>
        <div id="chatRoomActive" class="chat-room-active" hidden>
          <div class="chat-room-header">
            <button type="button" class="back-btn" id="chatBackBtn" aria-label="목록">←</button>
            <div class="chat-header-avatar" id="chatHeaderEmoji">🏢</div>
            <div class="chat-header-info">
              <div class="chat-header-name" id="chatHeaderName">—</div>
              <div class="chat-header-sub"><span id="chatHeaderSub">—</span></div>
            </div>
            <div class="chat-header-actions">
              <a class="chat-action-btn" id="chatJobLink" href="#" title="공고 보기">📋</a>
            </div>
          </div>
          <div class="chat-messages" id="chatMessages"></div>
          <div class="chat-quick-replies" id="chatQuickReplies">
            <button type="button" class="extra-btn" data-reply="면접 가능해요!">면접 가능해요!</button>
            <button type="button" class="extra-btn" data-reply="급여 조건이 궁금해요">급여 조건 문의</button>
            <button type="button" class="extra-btn" data-reply="초보도 가능한가요?">초보 가능?</button>
          </div>
          <div class="chat-input-area" id="chatInputArea">
            <div class="chat-input-row">
              <div class="chat-input-box">
                <textarea id="chatInputText" placeholder="메시지를 입력하세요..." rows="1"></textarea>
              </div>
              <button type="button" class="send-btn" id="chatSendBtn" aria-label="전송">➤</button>
            </div>
          </div>
          <div class="biz-lock chat-input-lock" id="chatInputLock" hidden>
            <div class="lock-badge">🔒 일반회원의 첫 메시지를 기다리는 중입니다</div>
          </div>
        </div>
      </div>
    </div>
<?php } ?>
  </div>

  <!-- 지역별 채팅 -->
  <div class="tab-panel<?php echo $_ch_tab === 'region' ? ' active' : ''; ?>" id="panel-region">
<?php if (!$_ch_can_region) { ?>
    <div class="region-biz-lock">
      <div class="lock-icon">🔒</div>
      <h3>기업회원은 이용할 수 없습니다</h3>
      <p>지역별 채팅은 <strong>일반회원(여성)만</strong> 이용 가능한 서비스입니다.<br>같은 지역 언니들과의 소통 공간입니다.</p>
    </div>
<?php } else { ?>
    <div class="region-chat-wrap">
      <div class="region-select-pane" id="regionSelectPane">
        <div class="region-select-head">
          <h3>🗺️ 지역별 채팅</h3>
          <p>지역을 선택해 채팅방에 입장하세요</p>
        </div>
        <div class="policy-banner">
          <strong style="color:var(--pink)">👩 일반회원 전용</strong> 서비스입니다.<br>
          같은 지역 언니들과 자유롭게 소통해보세요!
        </div>
        <div class="region-select-list" id="regionSelectList">
<?php foreach ($_ch_regions as $_ri => $_rg) { ?>
          <button type="button" class="region-chat-item<?php echo $_ri === 0 ? ' active' : ''; ?>" data-region="<?php echo htmlspecialchars($_rg['name']); ?>" data-emoji="<?php echo htmlspecialchars($_rg['emoji']); ?>">
            <span class="region-emoji"><?php echo $_rg['emoji']; ?></span>
            <span class="region-chat-info">
              <span class="region-chat-name"><?php echo htmlspecialchars($_rg['name']); ?></span>
              <span class="region-chat-cnt"><span class="region-online-dot"></span> <span class="region-online-num">—</span>명 참여중</span>
            </span>
          </button>
<?php } ?>
        </div>
      </div>
      <div class="region-room-pane" id="regionRoomPane">
        <div class="region-room-empty" id="regionEmpty">
          <div class="e-icon">🗺️</div>
          <p>왼쪽에서 지역을 선택해<br>채팅방에 입장하세요!</p>
        </div>
        <div id="regionRoomActive" class="region-room-active" hidden>
          <div class="region-room-header">
            <button type="button" class="back-btn" id="regionBackBtn" aria-label="목록">←</button>
            <div class="region-header-emoji" id="regionHeaderEmoji">🏙️</div>
            <div class="region-header-info">
              <div class="region-header-name" id="regionHeaderName">서울 채팅방</div>
              <div class="region-header-sub" id="regionHeaderSub">—명 참여중</div>
            </div>
            <button type="button" class="region-rules-btn" id="regionRulesBtn">이용규칙</button>
          </div>
          <div class="region-notice" id="regionNotice">
            <span class="n-icon">📢</span>
            <div>채팅방 이용 규칙: 욕설·광고·개인정보 공유 금지. 위반 시 즉시 강퇴될 수 있습니다.</div>
          </div>
          <div class="region-messages" id="regionMessages"></div>
          <div class="chat-input-area">
            <div class="chat-input-row">
              <div class="chat-input-box">
                <textarea id="regionInputText" placeholder="메시지를 입력하세요..." rows="1"></textarea>
              </div>
              <button type="button" class="send-btn" id="regionSendBtn" aria-label="전송">➤</button>
            </div>
          </div>
        </div>
      </div>
    </div>
<?php } ?>
  </div>

</div>

<script>
window.EVE_CHAT_HUB = {
  tab: <?php echo json_encode($_ch_tab, JSON_UNESCAPED_UNICODE); ?>,
  dmId: <?php echo (int)$_ch_dm_id; ?>,
  openJr: <?php echo (int)$_ch_open_jr; ?>,
  isFemale: <?php echo $_ch_is_female ? 1 : 0; ?>,
  isBiz: <?php echo $_ch_is_biz ? 1 : 0; ?>,
  canRegion: <?php echo $_ch_can_region ? 1 : 0; ?>,
  canDm: <?php echo $_ch_can_dm ? 1 : 0; ?>,
  unread: <?php echo (int)$_ch_unread; ?>,
  dmAjax: <?php echo json_encode(G5_PLUGIN_URL . '/chat/chat_dm_ajax.php', JSON_UNESCAPED_UNICODE); ?>,
  regionAjax: <?php echo json_encode(G5_PLUGIN_URL . '/chat/chat_ajax.php', JSON_UNESCAPED_UNICODE); ?>,
  jobsViewUrl: <?php echo json_encode($_ch_jobs_url, JSON_UNESCAPED_UNICODE); ?>,
  hubUrl: <?php echo json_encode($_ch_base . '/memo_full.php', JSON_UNESCAPED_UNICODE); ?>,
  myNick: <?php echo json_encode(get_text($member['mb_nick']), JSON_UNESCAPED_UNICODE); ?>,
  myMbId: <?php echo json_encode($member['mb_id'], JSON_UNESCAPED_UNICODE); ?>
};
</script>
<script src="<?php echo G5_THEME_URL; ?>/js/evealba_chat_hub.js?ver=<?php echo @filemtime(G5_THEME_PATH . '/js/evealba_chat_hub.js'); ?>"></script>
