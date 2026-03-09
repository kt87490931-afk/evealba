<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

add_stylesheet('<link rel="stylesheet" href="'.$member_skin_url.'/style.css">', 0);
add_stylesheet('<link rel="stylesheet" href="'.$member_skin_url.'/register_form.css">', 1);
add_javascript('<script src="'.G5_JS_URL.'/jquery.register_form.js"></script>', 0);
if ($config['cf_cert_use'] && ($config['cf_cert_simple'] || $config['cf_cert_ipin'] || $config['cf_cert_hp']))
    add_javascript('<script src="'.G5_JS_URL.'/certify.js?v='.G5_JS_VER.'"></script>', 0);

$ev_ro = ($w=='u' || (isset($ev_fixed_fields) && $ev_fixed_fields));
$ev_hp_hint = ($w=='u' && isset($ev_hp_changeable) && $ev_hp_changeable) ? '휴대폰 인증 후 변경 가능합니다.' : '';
?>

<!-- 회원정보 입력/수정 시작 { -->

<div class="register">
	<form id="fregisterform" name="fregisterform" action="<?php echo $register_action_url ?>" onsubmit="return fregisterform_submit(this);" method="post" enctype="multipart/form-data" autocomplete="off">
	<input type="hidden" name="w" value="<?php echo $w ?>">
	<input type="hidden" name="url" value="<?php echo $urlencode ?>">
	<input type="hidden" name="agree" value="<?php echo $agree ?>">
	<input type="hidden" name="agree2" value="<?php echo $agree2 ?>">
	<input type="hidden" name="cert_type" value="<?php echo $member['mb_certify']; ?>">
	<input type="hidden" name="cert_no" value="">
	<input type="hidden" name="mb_open" value="<?php echo ($w=='' || $member['mb_open'])?'1':'0'; ?>">
	<input type="hidden" name="mb_open_default" value="<?php echo $member['mb_open']; ?>">
	<?php if (isset($member['mb_sex'])) {  ?><input type="hidden" name="mb_sex" value="<?php echo $member['mb_sex'] ?>"><?php }  ?>
	<?php if (isset($member['mb_nick_date']) && $member['mb_nick_date'] > date("Y-m-d", G5_SERVER_TIME - ($config['cf_nick_modify'] * 86400))) {  ?>
	<input type="hidden" name="mb_nick_default" value="<?php echo get_text($member['mb_nick']) ?>">
	<input type="hidden" name="mb_nick" value="<?php echo get_text($member['mb_nick']) ?>">
	<?php }  ?>
	
	<div id="register_form" class="form-wrap">   
	    <div class="form-card sh-pink">
	        <div class="sec-head">
	            <span class="sec-head-icon">🔐</span>
	            <span class="sec-head-title">사이트 이용정보 입력</span>
	            <span class="sec-head-sub">필수항목(*)을 모두 입력해주세요</span>
	        </div>
	        <div class="form-row">
	            <div class="form-label">아이디 <span class="req">*</span></div>
	            <div class="form-cell col">
	                <div style="display:flex;gap:8px;width:100%;max-width:360px;flex-wrap:wrap;">
	                    <input type="text" name="mb_id" value="<?php echo $member['mb_id'] ?>" id="reg_mb_id" <?php echo $required ?> <?php echo $readonly ?> class="fi fi-md <?php echo ($ev_ro || $readonly) ? 'fi-readonly' : '' ?>" minlength="3" maxlength="20" placeholder="아이디를 입력해주세요" <?php echo $ev_ro ? 'readonly' : ''; ?>>
	                    <?php if (!$ev_ro && !$readonly) { ?><span id="msg_mb_id"></span><?php } ?>
	                </div>
	                <span class="fi-hint">영문자, 숫자, _ 만 입력 가능. 최소 3자 이상 입력하세요.</span>
	            </div>
	        </div>
	        <?php if ($req_nick) { ?>
	        <div class="form-row">
	            <div class="form-label">본인 닉네임 <span class="req">*</span></div>
	            <div class="form-cell col">
	                <input type="hidden" name="mb_nick_default" value="<?php echo isset($member['mb_nick'])?get_text($member['mb_nick']):''; ?>">
	                <input type="text" name="mb_nick" value="<?php echo isset($member['mb_nick'])?get_text($member['mb_nick']):''; ?>" id="reg_mb_nick" required class="fi fi-md nospace" maxlength="20" placeholder="닉네임을 입력해주세요" <?php echo $ev_ro ? 'readonly' : ''; ?>>
	                <span id="msg_mb_nick"></span>
	                <span class="fi-hint">게시판에서 이름을 대신하여 사용됩니다.</span>
	            </div>
	        </div>
	        <?php } ?>
	        <?php if ($config['cf_use_hp'] || ($config["cf_cert_use"] && ($config['cf_cert_hp'] || $config['cf_cert_simple']))) { ?>
	        <div class="form-row">
	            <div class="form-label">연락처<?php if (!empty($hp_required)) { ?> <span class="req">*</span><?php } ?></div>
	            <div class="form-cell col">
	                <input type="text" name="mb_hp" value="<?php echo get_text($member['mb_hp']) ?>" id="reg_mb_hp" <?php echo $hp_required; ?> <?php echo $hp_readonly; ?> class="fi fi-md <?php echo $hp_readonly ? 'fi-readonly' : '' ?>" maxlength="20" placeholder="010-0000-0000">
	                <?php if ($config['cf_cert_use'] && ($config['cf_cert_hp'] || $config['cf_cert_simple'])) { ?><input type="hidden" name="old_mb_hp" value="<?php echo get_text($member['mb_hp']) ?>"><?php } ?>
	                <span class="fi-hint"><?php echo $ev_hp_hint ? $ev_hp_hint : (($config['cf_cert_use'] && ($config['cf_cert_hp'] || $config['cf_cert_simple'])) ? '본인확인 시 자동입력' : "'-' 없이 숫자만 입력하셔도 됩니다."); ?></span>
	            </div>
	        </div>
	        <?php } ?>
	        <div class="form-row">
	            <div class="form-label">비밀번호 <span class="req">*</span></div>
	            <div class="form-cell col">
	                <div class="pw-wrap">
	                    <input type="password" name="mb_password" id="reg_mb_password" <?php echo $required ?> class="fi fi-full" minlength="3" maxlength="20" placeholder="비밀번호 입력">
	                    <span class="pw-toggle" onclick="var i=document.getElementById('reg_mb_password');i.type=i.type==='password'?'text':'password';this.textContent=i.type==='password'?'👁':'🙈';">👁</span>
	                </div>
	                <span class="fi-hint">4자 이상 입력해주세요.</span>
	            </div>
	        </div>
	        <div class="form-row">
	            <div class="form-label">비밀번호 확인 <span class="req">*</span></div>
	            <div class="form-cell col">
	                <div class="pw-wrap">
	                    <input type="password" name="mb_password_re" id="reg_mb_password_re" <?php echo $required ?> class="fi fi-full" minlength="3" maxlength="20" placeholder="비밀번호를 다시 입력해주세요">
	                    <span class="pw-toggle" onclick="var i=document.getElementById('reg_mb_password_re');i.type=i.type==='password'?'text':'password';this.textContent=i.type==='password'?'👁':'🙈';">👁</span>
	                </div>
	            </div>
	        </div>
	    </div>
	
	    <div class="form-card sh-orange">
	        <div class="sec-head">
	            <span class="sec-head-icon">👤</span>
	            <span class="sec-head-title">개인정보 입력</span>
	            <span class="sec-head-sub">개인정보는 안전하게 보호됩니다</span>
	        </div>
	            <?php if ($config['cf_cert_use']) { ?>
	        <div class="form-row">
	            <div class="form-label">본인확인 <span class="req">*</span></div>
	            <div class="form-cell">
	                <?php if ($config['cf_cert_simple']) echo '<button type="button" id="win_sa_kakao_cert" class="btn-check win_sa_cert" data-type="">간편인증</button>'; ?>
	                <?php if ($config['cf_cert_hp']) echo '<button type="button" id="win_hp_cert" class="btn-check">휴대폰 본인확인</button>'; ?>
	                <?php if ($config['cf_cert_ipin']) echo '<button type="button" id="win_ipin_cert" class="btn-check">아이핀 본인확인</button>'; ?>
	                <noscript>본인확인을 위해서는 자바스크립트 사용이 가능해야합니다.</noscript>
	                <?php if ($member['mb_certify']) {
	                    $mb_cert = ($member['mb_certify']=='simple')?'간편인증':(($member['mb_certify']=='ipin')?'아이핀':'휴대폰'); ?>
	                <div id="msg_certify"><strong><?php echo $mb_cert; ?> 본인확인</strong><?php if ($member['mb_adult']) { ?> 및 <strong>성인인증</strong><?php } ?> 완료</div>
	                <?php } ?>
	            </div>
	        </div>
	            <?php } ?>
	        <div class="form-row">
	            <div class="form-label">이름 <span class="req">*</span></div>
	            <div class="form-cell">
	                <input type="text" id="reg_mb_name" name="mb_name" value="<?php echo get_text($member['mb_name']) ?>" <?php echo $required ?> <?php echo $name_readonly; ?> class="fi fi-sm <?php echo ($ev_ro || $name_readonly) ? 'fi-readonly' : '' ?>" size="10" placeholder="실명을 입력해주세요" <?php echo ($ev_ro || $name_readonly) ? 'readonly' : ''; ?>>
	            </div>
	        </div>
	        <div class="form-row">
	            <div class="form-label">E-mail <span class="req">*</span></div>
	            <div class="form-cell">
	                <input type="hidden" name="old_email" value="<?php echo $member['mb_email'] ?>">
	                <input type="text" name="mb_email" value="<?php echo isset($member['mb_email'])?$member['mb_email']:''; ?>" id="reg_mb_email" required class="fi fi-md email" size="70" maxlength="100" placeholder="E-mail" <?php echo $ev_ro ? 'readonly' : ''; ?>>
	            </div>
	        </div>
	            <?php if ($config['cf_use_homepage']) { ?>
	        <div class="form-row">
	            <div class="form-label">홈페이지<?php if ($config['cf_req_homepage']){ ?> <span class="req">*</span><?php } ?></div>
	            <div class="form-cell">
	                <input type="text" name="mb_homepage" value="<?php echo get_text($member['mb_homepage']) ?>" id="reg_mb_homepage" <?php echo $config['cf_req_homepage']?"required":""; ?> class="fi fi-full" size="70" maxlength="255" placeholder="홈페이지">
	            </div>
	        </div>
	            <?php } ?>
	            <?php if ($config['cf_use_tel']) { ?>
	        <div class="form-row">
	            <div class="form-label">전화번호<?php if ($config['cf_req_tel']) { ?> <span class="req">*</span><?php } ?></div>
	            <div class="form-cell">
	                <input type="text" name="mb_tel" value="<?php echo get_text($member['mb_tel']) ?>" id="reg_mb_tel" <?php echo $config['cf_req_tel']?"required":""; ?> class="fi fi-md" maxlength="20" placeholder="전화번호">
	            </div>
	        </div>
	            <?php } ?>
	            <?php if ($config['cf_use_addr']) { ?>
	        <div class="form-row">
	            <div class="form-label">주소<?php if ($config['cf_req_addr']) { ?> <span class="req">*</span><?php } ?></div>
	            <div class="form-cell col">
	                <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
	                    <input type="text" name="mb_zip" value="<?php echo $member['mb_zip1'].$member['mb_zip2']; ?>" id="reg_mb_zip" <?php echo $config['cf_req_addr']?"required":""; ?> class="fi fi-sm" size="5" maxlength="6" placeholder="우편번호">
	                    <button type="button" class="btn-check" onclick="win_zip('fregisterform', 'mb_zip', 'mb_addr1', 'mb_addr2', 'mb_addr3', 'mb_addr_jibeon');">주소 검색</button>
	                </div>
	                <input type="text" name="mb_addr1" value="<?php echo get_text($member['mb_addr1']) ?>" id="reg_mb_addr1" <?php echo $config['cf_req_addr']?"required":""; ?> class="fi fi-full" size="50" placeholder="기본주소">
	                <input type="text" name="mb_addr2" value="<?php echo get_text($member['mb_addr2']) ?>" id="reg_mb_addr2" class="fi fi-full" size="50" placeholder="상세주소">
	                <input type="text" name="mb_addr3" value="<?php echo get_text($member['mb_addr3']) ?>" id="reg_mb_addr3" class="fi fi-full" size="50" readonly placeholder="참고항목">
	                <input type="hidden" name="mb_addr_jibeon" value="<?php echo get_text($member['mb_addr_jibeon']); ?>">
	            </div>
	        </div>
	            <?php } ?>
	    </div>
	
	    <div class="form-card sh-dark">
	        <div class="sec-head">
	            <span class="sec-head-icon">🎀</span>
	            <span class="sec-head-title">추천인</span>
	            <span class="sec-head-sub"><?php echo $w=='u' ? '나를 추천한 회원 목록을 확인하세요' : '추천인 아이디를 입력해주세요 (선택)'; ?></span>
	        </div>
	        <div class="referrer-wrap">
	            <?php if ($w == 'u') { ?>
	            <button type="button" id="evBtnReferralList" class="btn-referrer-list">👥 본인을 추천한 회원들 목록보기 <span class="count-badge" id="ev_referral_count_txt">0명</span></button>
	            <?php } else if ($config['cf_use_recommend']) { ?>
	            <div class="form-row">
	                <div class="form-label">추천인 아이디</div>
	                <div class="form-cell">
	                    <input type="text" name="mb_recommend" id="reg_mb_recommend" class="fi fi-md" placeholder="추천인 아이디 (선택)">
	                </div>
	            </div>
	            <?php } ?>
	        </div>
	    </div>

		<?php if($config['cf_use_promotion'] == 1) { ?>
		<div class="form-card">
			<div class="sec-head">
				<span class="sec-head-icon">📢</span>
				<span class="sec-head-title">수신설정</span>
			</div>
			<div style="padding:18px 22px;">
			<ul>
				<li class="chk_box">
				<div class="consent-line">
					<input type="checkbox" name="mb_marketing_agree" value="1" id="reg_mb_marketing_agree" aria-describedby="desc_marketing" <?php echo $member['mb_marketing_agree'] ? 'checked' : ''; ?> class="selec_chk marketing-sync">
					<label for="reg_mb_marketing_agree"><span></span><b class="sound_only">(선택) 마케팅 목적의 개인정보 수집 및 이용</b></label>
					<span class="chk_li">(선택) 마케팅 목적의 개인정보 수집 및 이용</span>
					<button type="button" class="js-open-consent" data-title="마케팅 목적의 개인정보 수집 및 이용" data-template="#tpl_marketing" data-check="#reg_mb_marketing_agree" aria-controls="consentDialog">자세히보기</button>
				</div>
				<input type="hidden" name="mb_marketing_agree_default" value="<?php echo $member['mb_marketing_agree'] ?>">
				<div id="desc_marketing" class="sound_only">마케팅 목적의 개인정보 수집·이용에 대한 안내입니다.</div>
				<div class="consent-date"><?php if ($member['mb_marketing_agree'] == 1 && $member['mb_marketing_date'] != "0000-00-00 00:00:00") echo "(동의일자: ".$member['mb_marketing_date'].")"; ?></div>
				<template id="tpl_marketing">
					* 목적: 서비스 마케팅 및 프로모션<br>
					* 항목: 이름, 이메일<?php echo ($config['cf_use_hp'] || ($config["cf_cert_use"] && ($config['cf_cert_hp'] || $config['cf_cert_simple']))) ? ", 휴대폰 번호" : "";?><br>
					* 보유기간: 회원 탈퇴 시까지<br>
					동의를 거부하셔도 서비스 기본 이용은 가능하나, 맞춤형 혜택 제공은 제한될 수 있습니다.
				</template>
				</li>
				<li class="chk_box consent-group">
				<div class="consent-line">
					<input type="checkbox" name="mb_promotion_agree" value="1" id="reg_mb_promotion_agree" aria-describedby="desc_promotion" class="selec_chk marketing-sync parent-promo">
					<label for="reg_mb_promotion_agree"><span></span><b class="sound_only">(선택) 광고성 정보 수신 동의</b></label>
					<span class="chk_li">(선택) 광고성 정보 수신 동의</span>
					<button type="button" class="js-open-consent" data-title="광고성 정보 수신 동의" data-template="#tpl_promotion" data-check="#reg_mb_promotion_agree" data-check-group=".child-promo" aria-controls="consentDialog">자세히보기</button>
				</div>
				<div id="desc_promotion" class="sound_only">광고성 정보 수신 동의 상위 항목입니다.</div>
				<ul class="sub-consents">
					<li class="chk_box is-inline">
						<input type="checkbox" name="mb_mailling" value="1" id="reg_mb_mailling" <?php echo $member['mb_mailling'] ? 'checked' : ''; ?> class="selec_chk child-promo">
						<label for="reg_mb_mailling"><span></span><b class="sound_only">광고성 이메일 수신 동의</b></label>
						<span class="chk_li">광고성 이메일 수신 동의</span>
						<input type="hidden" name="mb_mailling_default" value="<?php echo $member['mb_mailling']; ?>">
						<div class="consent-date"><?php if ($w == 'u' && $member['mb_mailling'] == 1 && $member['mb_mailling_date'] != "0000-00-00 00:00:00") echo "(동의일자: ".$member['mb_mailling_date'].")"; ?></div>
					</li>
					<?php if ($config['cf_use_hp'] || $config['cf_req_hp']) { ?>
					<li class="chk_box is-inline">
						<input type="checkbox" name="mb_sms" value="1" id="reg_mb_sms" <?php echo $member['mb_sms'] ? 'checked' : ''; ?> class="selec_chk child-promo">
						<label for="reg_mb_sms"><span></span><b class="sound_only">광고성 SMS/카카오톡 수신 동의</b></label>
						<span class="chk_li">광고성 SMS/카카오톡 수신 동의</span>
						<input type="hidden" name="mb_sms_default" value="<?php echo $member['mb_sms']; ?>">
						<div class="consent-date"><?php if ($w == 'u' && $member['mb_sms'] == 1 && $member['mb_sms_date'] != "0000-00-00 00:00:00") echo "(동의일자: ".$member['mb_sms_date'].")"; ?></div>
					</li>
					<?php } ?>
				</ul>
				<template id="tpl_promotion">
					수집·이용에 동의한 개인정보를 이용하여 이메일/SMS/카카오톡 등으로 광고성 정보를 전송할 수 있습니다.<br>
					동의는 언제든지 마이페이지에서 철회할 수 있습니다.
				</template>
				</li>
				<?php
					$configKeys = ['cf_sms_use'];
					$companies = ['icode' => '아이코드'];
					$usedCompanies = [];
					foreach ($configKeys as $key) {
						if (!empty($config[$key]) && isset($companies[$config[$key]])) {
							$usedCompanies[] = $companies[$config[$key]];
						}
					}
				?>
				<?php if (!empty($usedCompanies)) { ?>
				<li class="chk_box">
				<div class="consent-line">
					<input type="checkbox" name="mb_thirdparty_agree" value="1" id="reg_mb_thirdparty_agree" aria-describedby="desc_thirdparty" <?php echo $member['mb_thirdparty_agree'] ? 'checked' : ''; ?> class="selec_chk marketing-sync">
					<label for="reg_mb_thirdparty_agree"><span></span><b class="sound_only">(선택) 개인정보 제3자 제공 동의</b></label>
					<span class="chk_li">(선택) 개인정보 제3자 제공 동의</span>
					<button type="button" class="js-open-consent" data-title="개인정보 제3자 제공 동의" data-template="#tpl_thirdparty" data-check="#reg_mb_thirdparty_agree" aria-controls="consentDialog">자세히보기</button>
				</div>
				<input type="hidden" name="mb_thirdparty_agree_default" value="<?php echo $member['mb_thirdparty_agree'] ?>">
				<div id="desc_thirdparty" class="sound_only">개인정보 제3자 제공 동의 안내입니다.</div>
				<div class="consent-date"><?php if ($member['mb_thirdparty_agree'] == 1 && $member['mb_thirdparty_date'] != "0000-00-00 00:00:00") echo "(동의일자: ".$member['mb_thirdparty_date'].")"; ?></div>
				<template id="tpl_thirdparty">
					* 목적: 상품/서비스, 사은/판촉행사, 이벤트 등의 마케팅 안내(카카오톡 등)<br>
					* 항목: 이름, 휴대폰 번호<br>
					* 제공받는 자: <?php echo implode(', ', $usedCompanies);?><br>
					* 보유기간: 제공 목적 서비스 기간 또는 동의 철회 시까지
				</template>
				</li>
				<?php } ?>
			</ul>
			</div>
		</div>
		<?php } ?>

		<div class="form-card">
			<div class="sec-head">
				<span class="sec-head-icon">🛡</span>
				<span class="sec-head-title">자동등록방지</span>
			</div>
			<div class="captcha-wrap">
				<?php echo captcha_html(); ?>
			</div>
		</div>

		<div class="form-card">
			<div class="form-btns">
				<a href="<?php echo G5_URL ?>" class="btn-cancel">← 취소</a>
				<button type="submit" id="btn_submit" class="btn-submit" accesskey="s"><?php echo $w==''?'회원가입':'정보수정'; ?></button>
			</div>
		</div>
	</div>
	</form>
</div>

<?php include_once(__DIR__ . '/consent_modal.inc.php'); ?>

<?php if ($w == 'u') { ?>
<!-- 추천인 모달 (기업정보 모달과 동일 방식: inline display 제어) -->
<div id="evReferralModal" class="ev-referral-modal" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,.5);z-index:99999;align-items:center;justify-content:center;padding:16px;box-sizing:border-box;" onclick="if(event.target===this)ev_close_referral_modal()">
	<div class="ev-referral-modal-box" onclick="event.stopPropagation()">
		<div class="ev-referral-modal-head">
			<span class="ev-referral-modal-head-icon">🎀</span>
			<span class="ev-referral-modal-head-title">본인을 추천한 회원들</span>
			<button type="button" class="ev-referral-modal-close" onclick="ev_close_referral_modal()">✕</button>
		</div>
		<div class="ev-referral-modal-body" id="evReferralModalBody">
			<p style="text-align:center;padding:24px;color:#999;">⏳ 로딩중...</p>
		</div>
		<div class="ev-referral-modal-foot">
			<button type="button" class="ev-referral-modal-ok" onclick="ev_close_referral_modal()">확인</button>
		</div>
	</div>
</div>
<script>
(function(){
	var u='<?php echo addslashes(G5_BBS_URL); ?>/eve_referral_list.php';
	var x=new XMLHttpRequest();
	x.open('GET',u+'?mode=count');
	x.onload=function(){try{var j=JSON.parse(x.responseText);if(j.cnt!==undefined){var el=document.getElementById('ev_referral_count_txt');if(el)el.textContent=j.cnt+'명';}}catch(e){}};
	x.send();
})();
function ev_show_referral_list(){
	var modal=document.getElementById('evReferralModal');
	var body=document.getElementById('evReferralModalBody');
	if(!modal||!body){alert('모달을 불러올 수 없습니다. 새로고침 후 다시 시도해 주세요.');return;}
	if(!modal.parentNode||modal.parentNode!==document.body){document.body.appendChild(modal);}
	body.innerHTML='<p style="text-align:center;padding:24px;color:#999;">⏳ 로딩중...</p>';
	modal.style.display='flex';
	document.body.style.overflow='hidden';
	fetch('<?php echo G5_BBS_URL; ?>/eve_referral_list.php?mode=body')
	.then(function(r){return r.text();})
	.then(function(html){body.innerHTML=html;})
	.catch(function(){body.innerHTML='<p style="color:#c00;">데이터를 불러올 수 없습니다.</p>';});
}
function ev_close_referral_modal(){
	var m=document.getElementById('evReferralModal');
	if(m){m.style.display='none';document.body.style.overflow='';}
}
function ev_init_referral(){
	var modal=document.getElementById('evReferralModal');
	if(modal&&modal.parentNode&&modal.parentNode!==document.body){document.body.appendChild(modal);}
	var btn=document.getElementById('evBtnReferralList');
	if(btn){btn.removeEventListener('click',ev_referral_click);btn.addEventListener('click',ev_referral_click);}
}
function ev_referral_click(e){e.preventDefault();e.stopPropagation();ev_show_referral_list();}
if(document.readyState==='loading'){document.addEventListener('DOMContentLoaded',ev_init_referral);}
else{ev_init_referral();}
</script>
<?php } ?>

<script>
$(function() {
    $("#reg_zip_find").css("display", "inline-block");
    var pageTypeParam = "pageType=register";
	<?php if($config['cf_cert_use'] && $config['cf_cert_simple']) { ?>
	var url = "<?php echo G5_INICERT_URL; ?>/ini_request.php";
	var type = "";
    var params = "";
    var request_url = "";
	$(".win_sa_cert").click(function() {
		if(!cert_confirm()) return false;
		type = $(this).data("type");
        params = "?directAgency=" + type + "&" + pageTypeParam;
        request_url = url + params;
        call_sa(request_url);
	});
    <?php } ?>
    <?php if($config['cf_cert_use'] && $config['cf_cert_ipin']) { ?>
    var params = "";
    $("#win_ipin_cert").click(function() {
		if(!cert_confirm()) return false;
        params = "?" + pageTypeParam;
        var url = "<?php echo G5_OKNAME_URL; ?>/ipin1.php"+params;
        certify_win_open('kcb-ipin', url);
        return;
    });
    <?php } ?>
    <?php if($config['cf_cert_use'] && $config['cf_cert_hp']) { ?>
    var params = "";
    $("#win_hp_cert").click(function() {
		if(!cert_confirm()) return false;
        params = "?" + pageTypeParam;
        <?php     
        switch($config['cf_cert_hp']) {
            case 'kcb': $cert_url = G5_OKNAME_URL.'/hpcert1.php'; $cert_type = 'kcb-hp'; break;
            case 'kcp': $cert_url = G5_KCPCERT_URL.'/kcpcert_form.php'; $cert_type = 'kcp-hp'; break;
            case 'lg': $cert_url = G5_LGXPAY_URL.'/AuthOnlyReq.php'; $cert_type = 'lg-hp'; break;
            default: echo 'alert("기본환경설정에서 휴대폰 본인확인 설정을 해주십시오");'; echo 'return false;'; break;
        }
        ?>
        certify_win_open("<?php echo $cert_type; ?>", "<?php echo $cert_url; ?>"+params);
        return;
    });
    <?php } ?>
});

function fregisterform_submit(f)
{
    if (f.w.value == "") {
        var msg = reg_mb_id_check();
        if (msg) { alert(msg); f.mb_id.select(); return false; }
    }
    if (f.w.value == "") {
        if (f.mb_password.value.length < 3) {
            alert("비밀번호를 3글자 이상 입력하십시오.");
            f.mb_password.focus();
            return false;
        }
    }
    if (f.mb_password.value != f.mb_password_re.value) {
        alert("비밀번호가 같지 않습니다.");
        f.mb_password_re.focus();
        return false;
    }
    if (f.mb_password.value.length > 0 && f.mb_password_re.value.length < 3) {
        alert("비밀번호를 3글자 이상 입력하십시오.");
        f.mb_password_re.focus();
        return false;
    }
    if (f.w.value=="") {
        if (f.mb_name.value.length < 1) {
            alert("이름을 입력하십시오.");
            f.mb_name.focus();
            return false;
        }
    }
    <?php if($w == '' && $config['cf_cert_use'] && $config['cf_cert_req']) { ?>
    if(f.cert_no.value=="") {
        alert("회원가입을 위해서는 본인확인을 해주셔야 합니다.");
        return false;
    }
    <?php } ?>
    if ((f.w.value == "") || (f.w.value == "u" && typeof f.mb_nick != "undefined" && f.mb_nick.defaultValue != f.mb_nick.value)) {
        var msg = reg_mb_nick_check();
        if (msg) { alert(msg); if (f.reg_mb_nick) f.reg_mb_nick.select(); return false; }
    }
    if ((f.w.value == "") || (f.w.value == "u" && f.mb_email.defaultValue != f.mb_email.value)) {
        var msg = reg_mb_email_check();
        if (msg) { alert(msg); f.reg_mb_email.select(); return false; }
    }
    <?php if (($config['cf_use_hp'] || $config['cf_cert_hp']) && $config['cf_req_hp']) {  ?>
    var msg = reg_mb_hp_check();
    if (msg) { alert(msg); f.reg_mb_hp.select(); return false; }
    <?php } ?>
    if (typeof f.mb_icon != "undefined" && f.mb_icon.value) {
        if (!f.mb_icon.value.toLowerCase().match(/.(gif|jpe?g|png)$/i)) {
            alert("회원아이콘이 이미지 파일이 아닙니다.");
            f.mb_icon.focus();
            return false;
        }
    }
    if (typeof f.mb_img != "undefined" && f.mb_img.value) {
        if (!f.mb_img.value.toLowerCase().match(/.(gif|jpe?g|png)$/i)) {
            alert("회원이미지가 이미지 파일이 아닙니다.");
            f.mb_img.focus();
            return false;
        }
    }
    if (typeof(f.mb_recommend) != "undefined" && f.mb_recommend.value) {
        if (f.mb_id.value == f.mb_recommend.value) {
            alert("본인을 추천할 수 없습니다.");
            f.mb_recommend.focus();
            return false;
        }
        var msg = reg_mb_recommend_check();
        if (msg) { alert(msg); f.mb_recommend.select(); return false; }
    }
    <?php echo chk_captcha_js();  ?>
    document.getElementById("btn_submit").disabled = "disabled";
    return true;
}

jQuery(function($){
    $(document).on("click", ".tooltip_icon", function(e){
        $(this).next(".tooltip").fadeIn(400).css("display","inline-block");
    }).on("mouseout", ".tooltip_icon", function(e){
        $(this).next(".tooltip").fadeOut();
    });
});

document.addEventListener('DOMContentLoaded', function () {
  const parentPromo = document.getElementById('reg_mb_promotion_agree');
  const childPromo  = Array.from(document.querySelectorAll('.child-promo'));
  if (!parentPromo || childPromo.length === 0) return;
  const syncParentFromChildren = () => {
    const anyChecked = childPromo.some(cb => cb.checked);
    parentPromo.checked = anyChecked;
  };
  const syncChildrenFromParent = () => {
    const isChecked = parentPromo.checked;
    childPromo.forEach(cb => {
      cb.checked = isChecked;
      cb.dispatchEvent(new Event('change', { bubbles: true }));
    });
  };
  syncParentFromChildren();
  parentPromo.addEventListener('change', syncChildrenFromParent);
  childPromo.forEach(cb => cb.addEventListener('change', syncParentFromChildren));
});
</script>
<!-- } 회원정보 입력/수정 끝 -->
