var updUrlStaff = '';

//アップデートイベント初期化
function initUpdateStaff(url) {

	updUrlStaff = url;

	$("updateButton2").observe("click",clickUploadButtonStaff);
}

function clickUploadButtonStaff(event) {

	//全体を隠すカーテンをつける
	$$("body")[0].insert(createUpdatingCurtain());

	var resultArray = new Array();

	//データを取得する
	var resultRows = $("lstTableStaff").getElementsBySelector(".tableRow");

	for (var i=0; i < resultRows.length; i++) {

		var cells = resultRows[i].getElementsBySelector(".tableCell");
		var data = resultRows[i].getElementsBySelector(".hiddenData");

		var array = {
			"STATUS" : data[0].innerHTML.trim(),
			"NUM" : cells[0].innerHTML.trim(),
			"STAFF_CD" : cells[1].innerHTML.trim(),
			"STAFF_NM" : cells[2].innerHTML.trim(),
			"TOD_PROD" : cells[3].innerHTML.trim().replace(",", ""),
			"ACT_PROD" : cells[4].innerHTML.trim().replace(",", ""),
			"RANK" : cells[5].innerHTML.trim(),
			"RANK_UPD" : cells[6].innerHTML.trim()
		};

		resultArray[resultArray.length] = array;
	}
	
	var sendArray = {
		"TIMESTAMP" : $("timeStamp2").innerHTML,
		"DATA" : resultArray
	};

	//ポストの送信データを作る
	//JSONへ変更
	var postData = Object.toJSON(sendArray);

    //ポップアップを読み込む
    new Ajax.Request( updUrlStaff, {
	  method : 'post',
	  parameters: postData,

	  onSuccess : onSuccessEventStaff,

	  onFailure : function( event )  {
	    location.href = "/DCMS/WPO030/index?return_cd=91&message=" + Base64.encode(DCMSMessage.format("CMNE000103"));
	  },
	  onException : function( event, ex )  {
	    location.href = "/DCMS/WPO030/index?return_cd=92&message=" + Base64.encode(DCMSMessage.format("CMNE000104"));
	  }
	});
}

//更新処理後の初期表示処理
function onSuccessEventStaff(res) {

	//レスポンス
	var result = escapePHP(res.responseText.trim());

	location.href = "/DCMS/WPO030/index?" + result + "&display=true";
}
