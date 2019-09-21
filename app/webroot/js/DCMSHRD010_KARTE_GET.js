var actionUrl2 = '';

/**
 * アップデートイベント初期化
 * @param url
 */
function initGet(url) {
	actionUrl2 = url;
	$("prevKarte").observe("click", clickPrevKarte);
	$("nextKarte").observe("click", clickNextKarte);
}

/**
 * 「前へ」押下処理
 */
function clickPrevKarte(event) {
	requestGetKarte("prev");
}

/**
 * 「次へ」押下処理
 */
function clickNextKarte(event) {
	requestGetKarte("next");
}

/**
 * カルテ情報を取得
 */
function requestGetKarte (which) {
	// スタッフコード
	var staffCode = $$(".hiddenData.STAFF_CODE")[0].innerHTML;
	var hyoka_date = $("hyoka_date").innerHTML.replace("/", "");

	var sendArray = {
			"staff_cd" : staffCode,
			"hyoka_date" : hyoka_date,
			"which" : which
	};

	var postData = Object.toJSON(sendArray);

	// XHR送信
	new Ajax.Request(actionUrl2, {
		method : 'post',

		parameters : postData,

		onSuccess : onGetSuccessEvent,

		onFailure : function(event) {
			location.href = "/DCMS/HRD010/karte?staff_cd=" + staffCode + "&hyoka_date=" + hyoka_date + "return_cd=91&message=" + Base64.encode(DCMSMessage.format("CMNE000101"));
		},
		onException : function(event, ex) {
			location.href = "/DCMS/HRD010/karte?staff_cd=" + staffCode + "&hyoka_date=" + hyoka_date + "&return_cd=92&message=" + Base64.encode(DCMSMessage.format("CMNE000102"));
		}
	});
}


/**
 * 「次へ」「前へ」イベントの成功時コールバック関数
 * @param res
 */
function onGetSuccessEvent(res) {
	//レスポンス
	var result = escapePHP(res.responseText.trim()).evalJSON(true);

	var karteInfo = result.karteInfo;
	var skillInfo = result.skillInfo;
	var skillSPInfo = result.skillSPInfo;
	var isPrev = result.isPrev;
	var isNext = result.isNext;

	// ページング表示制御
	if (isPrev) {
		Element.show("prevKarte");
	} else {
		Element.hide("prevKarte");
	}

	if (isNext) {
		Element.show("nextKarte");
	} else  {
		Element.hide("nextKarte");
	}

	// カルテ情報-基本
	$("hyoka_date").innerHTML = karteInfo["YMD_HYOKA"];
	$("hyoka_date_input").value = karteInfo["YMD_HYOKA_INPUT"];
	$("tuyomi").value = karteInfo["TUYOMI"];
	$("yowami").value = karteInfo["YOWAMI"];
	$("comment").value = karteInfo["COMENT"];

	// 基本能力
	$$("#baseSkillArea #lstTable .tableCell select[id^='baseSkill']").each(function(elem, index) {
		var kmk_nm_cd = $$("#baseSkillTitle" + (index + 1) + " .hiddenData.KMK_NM_CD")[0].innerHTML;
		elem.value = kmk_nm_cd + ":" + skillInfo[kmk_nm_cd];
	});

	// 特殊能力
	$$("#SPSkillArea #lstTable .tableCell select[id^='SPSkill']").each(function(elem, index) {
		var kmk_nm_cd = $$("#SPSkillTitle" + (index + 1) + " .hiddenData.KMK_NM_CD")[0].innerHTML;
		elem.value = kmk_nm_cd + ":" + skillSPInfo[kmk_nm_cd];
	});

	// カルテ情報-目標
	$("mokuhyo1").value   = karteInfo["JAN_MOKUHYO"];
	$("tasseido1").value  = karteInfo["JAN_TASSEIDO"];
	$("mokuhyo2").value   = karteInfo["FEB_MOKUHYO"];
	$("tasseido2").value  = karteInfo["FEB_TASSEIDO"];
	$("mokuhyo3").value   = karteInfo["MAR_MOKUHYO"];
	$("tasseido3").value  = karteInfo["MAR_TASSEIDO"];
	$("mokuhyo4").value   = karteInfo["APR_MOKUHYO"];
	$("tasseido4").value  = karteInfo["APR_TASSEIDO"];
	$("mokuhyo5").value   = karteInfo["MAY_MOKUHYO"];
	$("tasseido5").value  = karteInfo["MAY_TASSEIDO"];
	$("mokuhyo6").value   = karteInfo["JUN_MOKUHYO"];
	$("tasseido6").value  = karteInfo["JUN_TASSEIDO"];
	$("mokuhyo7").value   = karteInfo["JUL_MOKUHYO"];
	$("tasseido7").value  = karteInfo["JUL_TASSEIDO"];
	$("mokuhyo8").value   = karteInfo["AUG_MOKUHYO"];
	$("tasseido8").value  = karteInfo["AUG_TASSEIDO"];
	$("mokuhyo9").value   = karteInfo["SEP_MOKUHYO"];
	$("tasseido9").value  = karteInfo["SEP_TASSEIDO"];
	$("mokuhyo10").value  = karteInfo["OCT_MOKUHYO"];
	$("tasseido10").value = karteInfo["OCT_TASSEIDO"];
	$("mokuhyo11").value  = karteInfo["NOV_MOKUHYO"];
	$("tasseido11").value = karteInfo["NOV_TASSEIDO"];
	$("mokuhyo12").value  = karteInfo["DEC_MOKUHYO"];
	$("tasseido12").value = karteInfo["DEC_TASSEIDO"];
}
