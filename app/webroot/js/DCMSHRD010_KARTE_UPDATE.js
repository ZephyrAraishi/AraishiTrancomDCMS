var actionUrl = '';

/**
 * アップデートイベント初期化
 * @param url
 */
function initUpdate(url) {
	actionUrl = url;
	$$("input[type='button'][id^='updateButton']").each(function(updateButton) {
		updateButton.observe("click", clickUploadButton);
	});
}

var HRD010_KARTE = {

	/**
	 * 入力チェック
	 *
	 * @deprecated クライントでのバリデーションは行わないため、使用していません。
	 * @returns {Boolean}
	 */
	validate : function() {
		var errorMessage = "";

		// 評価月
		if (DCMSValidation.notEmpty($F("hyoka_date_input"))) {
			Element.removeClassName($("hyoka_date_input"), "errorItem");
			// 日付チェック
			if (!DCMSValidation.checkDateYm($F("hyoka_date_input"))) {
				errorMessage += DCMSMessage.format("CMNE000007", "評価月") + "\n";
				Element.addClassName($("hyoka_date_input"), "errorItem");
			} else {
				Element.removeClassName($("hyoka_date_input"), "errorItem");
			}
		} else {
			errorMessage += DCMSMessage.format("CMNE000001", "評価月") + "\n";
			Element.addClassName($("hyoka_date_input"), "errorItem");
		}

		// 基本能力
		var titleInsert = false;
		$$("#baseSkillArea #lstTable .tableCell select[id^='baseSkill']").each(function(elem, index) {
			if (DCMSValidation.notEmpty(elem.value)) {
				Element.removeClassName(elem, "errorItem");
			} else {
				Element.addClassName(elem, "errorItem");
				if (!titleInsert) {
					if (DCMSValidation.notEmpty(errorMessage)) {
						errorMessage += "\n";
					}
					errorMessage += $("baseSkillTitle").innerHTML + "\n";
					titleInsert = true;
				}
				errorMessage += DCMSMessage.format("CMNE000023", "・" + $("baseSkillName" + (index + 1)).innerHTML) + "\n";
			}
		});

		// 特殊能力
		titleInsert = false;
		$$("#SPSkillArea #lstTable .tableCell select[id^='SPSkill']").each(function(elem, index) {
			if (DCMSValidation.notEmpty(elem.value)) {
				Element.removeClassName(elem, "errorItem");
			} else {
				Element.addClassName(elem, "errorItem");
				if (!titleInsert) {
					if (DCMSValidation.notEmpty(errorMessage)) {
						errorMessage += "\n";
					}
					errorMessage += $("SPSkillTitle").innerHTML + "\n";
					titleInsert = true;
				}
				errorMessage += DCMSMessage.format("CMNE000023", "・" + $("SPSkillName" + (index + 1)).innerHTML) + "\n";
			}
		});

		// エラーが発生していればメッセージを表示
		if (DCMSValidation.notEmpty(errorMessage)) {
			alert(errorMessage);
			return false;
		}

		// エラーなし
		return true;

	}
};

/**
 * 更新ボタンクリックイベントハンドラ
 * @param event
 */
function clickUploadButton(event) {
/*  クライアントでのバリデーションは行わない
	if (!HRD010_KARTE.validate()) {
		// バリデーションエラーのため処理終了
		return;
	}
*/
	//更新アラート
	if (!window.confirm(DCMSMessage.format("CMNQ000001"))) {
		return;
	}

	//全体を隠すカーテンをつける
	$$("body")[0].insert(createUpdatingCurtain());

	// ポストする情報を作成

	/**
	 * 能力情報を保持するクラス
	 */
	var SkillDto = function() {
	};

	// 基本能力
	var baseSkillArray = new Array();
	$$("#baseSkillArea #lstTable .tableCell select[id^='baseSkill']").each(function(elem) {
		var skills = elem.value.split(":");
		var dto = new SkillDto();
		dto.kmk_nm_cd = skills[0];
		dto.kmk_lv_cd = skills[1];
		baseSkillArray.push(dto);
	});

	// 特殊能力
	var SPSkillArray = new Array();
	$$("#SPSkillArea #lstTable .tableCell select[id^='SPSkill']").each(function(elem) {
		var skills = elem.value.split(":");
		var dto = new SkillDto();
		dto.kmk_nm_cd = skills[0];
		dto.kmk_lv_cd = skills[1];
		SPSkillArray.push(dto);
	});

	// スタッフコード
	var staffCode = $$(".hiddenData.STAFF_CODE")[0].innerHTML;
	var hyoka_date = $("hyoka_date").innerHTML.replace("/", "");

	var sendArray = {
			"staff_cd" : staffCode,
			"hyoka_date" : hyoka_date,
			"hyoka_date_input" : $F("hyoka_date_input"),
			"tuyomi" : $F("tuyomi"),
			"yowami" : $F("yowami"),
			"comment" : $F("comment"),
			"baseSkillArray" : baseSkillArray,
			"SPSkillArray" : SPSkillArray,
			"mokuhyo1" : $F("mokuhyo1"),
			"mokuhyo2" : $F("mokuhyo2"),
			"mokuhyo3" : $F("mokuhyo3"),
			"mokuhyo4" : $F("mokuhyo4"),
			"mokuhyo5" : $F("mokuhyo5"),
			"mokuhyo6" : $F("mokuhyo6"),
			"mokuhyo7" : $F("mokuhyo7"),
			"mokuhyo8" : $F("mokuhyo8"),
			"mokuhyo9" : $F("mokuhyo9"),
			"mokuhyo10" : $F("mokuhyo10"),
			"mokuhyo11" : $F("mokuhyo11"),
			"mokuhyo12" : $F("mokuhyo12"),
			"tasseido1" : $F("tasseido1"),
			"tasseido2" : $F("tasseido2"),
			"tasseido3" : $F("tasseido3"),
			"tasseido4" : $F("tasseido4"),
			"tasseido5" : $F("tasseido5"),
			"tasseido6" : $F("tasseido6"),
			"tasseido7" : $F("tasseido7"),
			"tasseido8" : $F("tasseido8"),
			"tasseido9" : $F("tasseido9"),
			"tasseido10" : $F("tasseido10"),
			"tasseido11" : $F("tasseido11"),
			"tasseido12" : $F("tasseido12"),
			"timestamp" : $$(".hiddenData.TIME_STAMP")[0].innerHTML
	};

	var postData = Object.toJSON(sendArray);

	// XHR送信
	new Ajax.Request(actionUrl, {
		method : 'post',
		parameters : postData,

		onSuccess : onUpdateSuccessEvent,

		onFailure : function(event) {
			location.href = "/DCMS/HRD010/karte?staff_cd=" + staffCode + "&hyoka_date=" + hyoka_date + "return_cd=91&message=" + Base64.encode(DCMSMessage.format("CMNE000103"));
		},
		onException : function(event, ex) {
			location.href = "/DCMS/HRD010/karte?staff_cd=" + staffCode + "&hyoka_date=" + hyoka_date + "&return_cd=92&message=" + Base64.encode(DCMSMessage.format("CMNE000104"));
		}
	});
}

/**
 * 更新処理の後、返ってきたデータの処理
 * @param res
 */
function onUpdateSuccessEvent(res) {

	//レスポンス
	var result = escapePHP(res.responseText.trim());

	var staff_cd = "";

	var get = getRequest();
	if (get.length != 0) {
		if (get["staff_cd"] != null) {
			staff_cd = get["staff_cd"];
		}
	}

	location.href = "/DCMS/HRD010/karte?" + result + "&staff_cd=" + staff_cd + "&hyoka_date=" + $F("hyoka_date_input") + "&display=true";
}
