var actionUrl3 = "";

/**
 * リロード時の初期処理
 */
function initReload() {
	//更新メッセージ
	var get = getRequest();
	if (get.length != 0) {
		if (get["return_cd"] != null) {
			//画面上部にメッセージを表示
			if (get["return_cd"] == "0") {
				$("infoMsg").update(Base64.decode(get["message"]));

			} else {
				$("errorMsg").update(Base64.decode(get["message"]));
				$$("#lstTable .tableRow").each(function(rowElem) {
					var changed = rowElem.getElementsBySelector(".hiddenData.CHANGED")[0].innerHTML;
					if (parseInt(changed, 10) < 3) {
						setRowColor(changed, rowElem);

					} else {
						setDeletedRowColor(rowElem);
					}
				});
			}
		}
	}
}

/**
 * アップデートイベント初期化
 * @param url
 */
function initUpdate(url) {
	actionUrl3 = url;
	$("updateButton").observe("click", clickUploadButton);
}

var updatingCurtain = null;

/**
 * 更新ボタンクリックイベント
 */
function clickUploadButton(event) {

	// 確認ダイアログを表示
	var res = confirm(DCMSMessage.format("CMNQ000001"));
	// 選択結果で分岐
	if (res == false) {
		return;
	}

	//全体を隠すカーテンをつける
	$$("body")[0].insert(createUpdatingCurtain());

	/**
	 * 行のデータを保持するためのインナークラス
	 */
	var RowData = function() {
	};

	// POSTする配列。RowDataをためる
	var resultArray = new Array();

	//データを取得する
	$$("#lstTable .tableRow").each(function(rowElem) {
		var postTargets = rowElem.getElementsBySelector("div");
		var rowData = new RowData();
		rowData.pre_hassei_date = postTargets[1].innerHTML;
		rowData.hassei_date = postTargets[2].innerHTML.replace(/\//g, "");
		rowData.gyo_no = postTargets[3].innerHTML;
		rowData.kbn_hinshitsu_naiyo_nm = postTargets[4].innerHTML;
		rowData.kbn_hinshitsu_naiyo_cd = postTargets[5].innerHTML;
		rowData.dai_bunrui_nm = postTargets[6].innerHTML;
		rowData.dai_bunrui_cd = postTargets[7].innerHTML;
		rowData.chu_bunrui_nm = postTargets[8].innerHTML;
		rowData.chu_bunrui_cd = postTargets[9].innerHTML;
		rowData.sai_bunrui_nm = postTargets[10].innerHTML;
		rowData.sai_bunrui_cd = postTargets[11].innerHTML;
		rowData.s_kanri_no = postTargets[12].innerHTML;
		rowData.staff_nm = postTargets[13].innerHTML.unescapeHTML();
		rowData.t_kin = postTargets[14].innerHTML.replace(/,/g, "");
		rowData.t_hoho = postTargets[15].innerHTML.unescapeHTML();
		rowData.changed = postTargets[16].innerHTML;
		// リストにためる
		resultArray.push(rowData);
	});

	//ポストの送信データを作る
	var staffCode = $$(".hiddenData.STAFF_CODE")[0].innerHTML;
	var sendArray = {
		'data' : resultArray,
		'staff_cd' : staffCode,
		'timestamp' : $$(".hiddenData.TIME_STAMP")[0].innerHTML
	};

	//JSONへ変更
	var postData = Object.toJSON(sendArray);

	// 更新処理実行
	new Ajax.Request(actionUrl3, {
		method : 'post',
		parameters : postData,

		onSuccess : onSuccessEvent,

		onFailure : function(event) {
			location.href = "/DCMS/HRD010/hinshitsu?staff_cd=" + staffCode + "&return_cd=91&message=" + Base64.encode(DCMSMessage.format("CMNE000103"));
		},

		onException : function(event, ex) {
			location.href = "/DCMS/HRD010/hinshitsu?staff_cd=" + staffCode + "&return_cd=92&message=" + Base64.encode(DCMSMessage.format("CMNE000104"));
		}
	});
}

/**
 * 更新成功時
 * @param res
 */
function onSuccessEvent(res) {

	//レスポンス
	var result = escapePHP(res.responseText.trim());

	var staff_cd = "";
	var pageID = "1";

	var get = getRequest();
	if (get.length != 0) {

		if (get["staff_cd"] != null) {
			staff_cd = get["staff_cd"];
		}

		if (get["pageID"] != null) {
			pageID = get["pageID"];
		}
	}

	location.href = "/DCMS/HRD010/hinshitsu?" + result + "&staff_cd=" + staff_cd + "&pageID=" + pageID + "&display=true";

}
