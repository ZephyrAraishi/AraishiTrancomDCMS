var viewDayURL = "";

/**
 * リロード時の初期処理
 */
function initReload(dayURL) {
	
	viewDayURL = dayURL;
	
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
	
	if ($(ymd).value == "true") {
		for (i = 1; i <= 5; i++) {
			setSelectedRowColor($(ymd + "_y_" + i));
			setSelectedRowColor($(ymd + "_j_" + i));
		}
	}
}

/**
 * 月別サマリテーブルマウスアウト処理
 */
function onMounseOutMonthCol(event) {
	
	var item = Event.element(event);
	var ymd = item.id.substring(0, 8);
	
	if ($(ymd).value == "true") {
		for (i = 1; i <= 5; i++) {
			setNormalRowColor($(ymd + "_y_" + i));
			setNormalRowColor($(ymd + "_j_" + i));
		}
	}
}

/**
 * 月別サマリテーブルクリック処理
 */
function onClickMonthCol(event) {

	var item = Event.element(event);
	var ymd = item.id.substring(0, 8);
	
	// 合計欄または登録データのない日が選択された場合は何もしない
	if (ymd == "99999999" || $(ymd).value != "true") {
		return;
	}
	
	// 端末遅い人用に、ポップアップがでるまでのカバーをつける
	areaCoverElement = createAreaCoverElement();
	$$("body")[0].insert(areaCoverElement);
	
	var sendArray = {
			'ymd' : ymd
	}
	var postData = Object.toJSON(sendArray);
	
    new Ajax.Request( viewDayURL, {
		method : 'post',
		parameters: postData,
		onSuccess : function(event)  {
			areaCoverElement.remove();
			$('day_table').innerHTML = event.responseText;
		},
		onFailure : function(event)  {
			alert("失敗！" + event.responseText); // テスト用
		},
		onException : function(event, ex)  {
			alert("失敗" + ex);
		}
  	});
}
