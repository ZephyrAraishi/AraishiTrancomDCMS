var actionUrl3 = ''

//アップデートイベント初期化
function initUpdate(url) {

	actionUrl3 = url;

	$("updateButton").observe("click",clickUploadButton);
}

//更新ボタンクリックイベントハンドラ
function clickUploadButton(event) {

	//更新アラート
	if(!window.confirm(DCMSMessage.format("CMNQ000001"))){
		return;
	}

	//全体を隠すカーテンをつける
	$$("body")[0].insert(createUpdatingCurtain());

	//渡す配列
	var resultArray = new Array();

	//全体のデータを取得する
	var resultRows = $("lstTable").getElementsBySelector(".tableRow");

	for (var i=0; i < resultRows.length; i++) {

		//セル配列
		var cellsArray = new Array();
		var cells = resultRows[i].getElementsBySelector(".tableCell");

		for (var m=0; m < cells.length; m++) {

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

    //ポップアップを読み込む
    new Ajax.Request( actionUrl3, {
	  method : 'post',
	  parameters: postData,

	  onSuccess : onSuccessEvent,

	  onFailure : function( event )  {
	    location.href = "/DCMS/TPM050/index?return_cd=91&message=" + Base64.encode(DCMSMessage.format("CMNE000103"));
	  },
	  onException : function( event, ex )  {
	    location.href = "/DCMS/TPM050/index?return_cd=92&message=" + Base64.encode(DCMSMessage.format("CMNE000104"));
	  }
	});
}

//更新処理の後、帰ってきたデータの処理
function onSuccessEvent(res) {

	//レスポンス
	var result = escapePHP(res.responseText.trim());

	var ymd_from = '';
	var ymd_to = '';
	var kotei = '';
	var pageID = '1';

	//ゲットデータを取得
	var get = getRequest();

	//ゲットの長さを確認
	if (get.length != 0) {

		if (get["ymd_from"] != null) {
			ymd_from = get["ymd_from"];
		}

		if (get["ymd_to"] != null) {
			ymd_to = get["ymd_to"];
		}

		if (get["kotei"] != null) {
			kotei = decodeURI(get["kotei"]);
		}

		if (get["pageID"] != null) {
			pageID = decodeURI(get["pageID"]);
		}

	}

	//ロケーションの変更
	location.href = "/DCMS/TPM050/index?" + result + "&ymd_from=" + ymd_from +
													 "&ymd_to=" + ymd_to +
													 "&kotei=" + kotei +
													 "&pageID=" + pageID +
													 "&display=true";
}
