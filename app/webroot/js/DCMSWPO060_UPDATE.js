var actionUrl3 = ''

//アップデートイベント初期化
function initUpdate(url) {

	actionUrl3 = url;

	$("updateButton").observe("click",clickUploadButton);
}

//更新ボタンクリックイベントハンドラ
function clickUploadButton(event) {

	var kotei = $("koteiCd").getValue();

	if (kotei == null || kotei == "") {
		alert(DCMSMessage.format("CMNE000001",'差込工程'));
		return;
	}

	 var timeFrom2 = $("timeFrom2").getValue();

	if (timeFrom2 == null || timeFrom2 == "") {
		alert(DCMSMessage.format("CMNE000001",'開始時間'));
		return;
	}

	if (!timeFrom2.match(/^\d*$/)) {
		alert(DCMSMessage.format("CMNE000002",'開始時間'));
		return;
	}

	if (timeFrom2.length < 4) {
		alert(DCMSMessage.format("CMNE000004",'開始時間','4'));
		return;
	}

	var timeTo2 = $("timeTo2").getValue();

	if (timeTo2 == null || timeTo2 == "") {
		alert(DCMSMessage.format("CMNE000001",'終了時間'));
		return;
	}

	if (!timeTo2.match(/^\d*$/)) {
		alert(DCMSMessage.format("CMNE000002",'終了時間'));
		return;
	}

	if (timeTo2.length < 4) {
		alert(DCMSMessage.format("CMNE000004",'終了時間','4'));
		return;
	}

	var tmpFrom = Number(timeFrom2.replace(':',''));
	var tmpTo = Number(timeTo2.replace(':',''));
	var max = Number($("MAX_TIME").innerHTML.replace(':','').substr(0,4));
	var min = Number($("MIN_TIME").innerHTML.replace(':','').substr(0,4));

	if (tmpTo <= tmpFrom) {
		alert(DCMSMessage.format("CMNE000008",'終了時間','開始時間'));
		return;
	}

	if (tmpTo <= tmpFrom) {
		alert(DCMSMessage.format("CMNE000008",'終了時間','開始時間'));
		return;
	}

	if (tmpFrom < min) {
		alert('工程開始時刻が、就業開始可能時間の' + $("MIN_TIME").innerHTML.replace(':','').substr(0,4) + 'より上に設定されています。');
		return;
	}

	if (tmpTo < min) {
		alert('工程終了時刻が、就業開始可能時間の' + $("MIN_TIME").innerHTML.replace(':','').substr(0,4) + 'より上に設定されています。');
		return;
	}

	if (tmpFrom > max) {
		alert('工程開始時刻が、就業終了可能時間の' + $("MAX_TIME").innerHTML.replace(':','').substr(0,4) + 'より上に設定されています。');
		return;
	}

	if (tmpTo > max) {
		alert('工程終了時刻が、就業終了可能時間の' + $("MAX_TIME").innerHTML.replace(':','').substr(0,4) + 'より上に設定されています。');
		return;
	}

	//更新アラート
	if(!window.confirm(DCMSMessage.format("CMNQ000002"))){
		return;
	}

	//全体を隠すカーテンをつける
	$$("body")[0].insert(createUpdatingCurtain());

	var ymd = $("YMD").innerHTML;
	var timeFrom = $("TIME_FROM").innerHTML;
	var timeTo = $("TIME_TO").innerHTML;
	var haken = $("HAKEN").innerHTML;
	var yakushoku = $("YAKUSHOKU").innerHTML;
	var keiyaku = $("KEIYAKU").innerHTML;
	var staff_cd = $("STAFF_CD").innerHTML;
	var staff_nm = $("STAFF_NM").innerHTML;
	timeFrom2 = timeFrom2.substr(0,2) + ':' + timeFrom2.substr(2,2);
	timeTo2 = timeTo2.substr(0,2) + ':' + timeTo2.substr(2,2);

	//渡す配列
	var resultArray = new Array();

	//全体のデータを取得する
	var resultRows = $("lstTable").getElementsBySelector(".tableRow");

	for (var i=0; i < resultRows.length; i++) {

		//セル配列
		var cellsArray = new Array();
		var cells = resultRows[i].getElementsBySelector(".tableCell");

		for (var m=0; m < cells.length; m++) {

			if (m==0) {
				var checked = cells[m].getElementsBySelector(".check")[0].getValue();
				cellsArray[m] = checked;
				continue;
			}

			cellsArray[m] = cells[m].innerHTML.strip();
		}

		//データ配列
		var dataArray = new Array();
		var data = resultRows[i].getElementsBySelector(".hiddenData");

		for (var m=0; m < data.length; m++) {

			dataArray[m] = data[m].innerHTML.strip();
		}


		//行配列
		var rowArray = {
			'cells' : cellsArray,
			'data' : dataArray,
		};

		//行追加
		resultArray[i] = rowArray;
	}

	//ポストの送信データを作る
	//JSONへ変更

	var sendArray = {

		'data' : resultArray,
		'timestamp' : $("timestamp").innerHTML
	}

	var postData = Object.toJSON(sendArray);

	var postUrl = actionUrl3 + "?ymd=" + ymd
							 + "&timeFrom=" + timeFrom
							 + "&timeTo=" + timeTo
							 + "&haken=" + haken
							 + "&yakushoku=" + yakushoku
							 + "&keiyaku=" + keiyaku
							 + "&staff_cd=" + staff_cd
							 + "&staff_nm=" + staff_nm
							 + "&kotei=" + kotei
							 + '&timeFrom2=' + timeFrom2
							 + "&timeTo2=" + timeTo2;

    //ポップアップを読み込む
    new Ajax.Request( postUrl, {
	  method : 'post',
	  parameters: postData,

	  onSuccess : onSuccessEvent,

	  onFailure : function( event )  {
	    location.href = "/DCMS/WPO060/index?return_cd=91&message=" + Base64.encode(DCMSMessage.format("CMNE000103"));
	  },
	  onException : function( event, ex )  {
	    location.href = "/DCMS/WPO060/index?return_cd=92&message=" + Base64.encode(DCMSMessage.format("CMNE000104"));
	  }
	});
}

//更新処理の後、帰ってきたデータの処理
function onSuccessEvent(res) {

	//レスポンス
	var result = escapePHP(res.responseText.trim());

	//ロケーションの変更
	location.href = "/DCMS/WPO060/index?" + result;
}
