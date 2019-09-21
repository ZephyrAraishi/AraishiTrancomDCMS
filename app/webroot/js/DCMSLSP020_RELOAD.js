var viewDayInputURL = "";
var viewDayGlaphURL = "";
var loadCoverElement = null;

/**
 * リロード時の初期処理
 */
function initReload(dayInputURL, dayGlaphURL) {

	viewDayInputURL = dayInputURL;
	viewDayGlaphURL = dayGlaphURL;
	
	$("viewButton").observe("click", clickViewButton);

	if ($("monthViewFlg").value == "1") {
		$("csvButton").observe("click", clickCsvOutButton);

		// 月別サマリテーブル各列のイベントを設定
		var resultRows = $("monthTable").getElementsBySelector(".tableCol");
		for (var i = 0; i < resultRows.length; i++) {
			resultRows[i].observe("mouseover", onMounseOverMonthCol);
			resultRows[i].observe("mouseout", onMounseOutMonthCol);
			resultRows[i].observe("click", onClickMonthCol);
		}
	}
	
	if ($('ymd') != null && $('ymd').value != "") {
		$("inputButton").observe("click", clickInputButton);
		$("glaphButton").observe("click", clickGlaphButton);
	}
}

/**
 * 表示ボタン押下処理
 */
function clickViewButton(event) {
	$("monthViewFlg").value = "1";
	document.forms[0].submit();
}

/**
 * 「CSV出力」ボタンクリックイベント
 */ 
function clickCsvOutButton(event) {
	$('csvStartYmd').value = $('startYmd').value;
	$('csvEndYmd').value = $('endYmd').value;
	document.forms[1].submit();
}

/**
 * 月別サマリテーブルマウスオーバー処理
 */
function onMounseOverMonthCol(event) {
	
	var item = Event.element(event); 
	var ymd = item.id.substring(0, 8);
	
	if ($(ymd).value != "99999999") {
		for (i = 1; i <= 5; i++) {
			setSelectedRowColor($(ymd + "_" + i));
		}
	}
}

/**
 * 月別サマリテーブルマウスアウト処理
 */
function onMounseOutMonthCol(event) {
	
	var item = Event.element(event); 
	var ymd = item.id.substring(0, 8);
	
	if ($(ymd).value != "99999999") {
		for (i = 1; i <= 5; i++) {
			setNormalRowColor($(ymd + "_" + i));
		}
	}
}

/**
 * 月別サマリテーブルクリック処理
 */
function onClickMonthCol(event) {

	var item = Event.element(event);
	var ymd = item.id.substring(0, 8);
	
	// 合計欄が選択された場合は何もしない
	if (ymd == "99999999") {
		return;
	}
	
	$("viewYmd").value = ymd;
	
	setDayTableData("1");
}

/**
 * 「予測入力」ボタンクリックイベント
 */ 
function clickInputButton(event) {
	
	setDayTableData("1");
}

/**
 * 「グラフ表示」ボタンクリックイベント
 */ 
function clickGlaphButton(event) {
	
	setDayTableData("2");
}

/**
 * 日別詳細テーブルデータ設定
 * 
 * @param viewMode 表示モード（1：予測入力、2：グラフ表示）
 */
function setDayTableData(viewMode) {

	var ymd = $('viewYmd').value;
	
	$("errorMsg").innerHTML = "";
	$("infoMsg").innerHTML = "";
	
	// 端末遅い人用に、ポップアップがでるまでのカバーをつける
	loadCoverElement = createAreaCoverElement();
	$$("body")[0].insert(loadCoverElement);
	
	var url = viewMode == "1" ? viewDayInputURL : viewDayGlaphURL;
	
	var sendArray = {
			'ymd' : ymd
	}
	var postData = Object.toJSON(sendArray);
	
    new Ajax.Request( url, {
		method : 'post',
		parameters: postData,
		onSuccess : function(event)  {
			loadCoverElement.remove();
			$('day_table').innerHTML = event.responseText;
			
			if ($('regCnt').value != 0) {
				$("inputButton").observe("click", clickInputButton);
				$("glaphButton").observe("click", clickGlaphButton);
			}
		},
		onFailure : function(event)  {
			location.href = "/DCMS/LSP020/index?return_cd=91&message=" + Base64.encode(DCMSMessage.format("CMNE000101"));
		},
		onException : function(event, ex)  {
			location.href = "/DCMS/LSP020/index?return_cd=91&message=" + Base64.encode(DCMSMessage.format("CMNE000101"));
		}
  	});
}


