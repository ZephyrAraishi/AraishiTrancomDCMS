var updUrlRank = '';

//アップデートイベント初期化
function initUpdateRank(url) {

	updUrlRank = url;

	$("updateButton1").observe("click",clickUploadButtonRank);
}

function clickUploadButtonRank(event) {

	//全体を隠すカーテンをつける
	$$("body")[0].insert(createUpdatingCurtain());

	var resultArray = new Array();

	//データを取得する
	var resultRows = $("lstTableRank").getElementsBySelector(".tableRow");

	for (var i=0; i < resultRows.length; i++) {

		var data = resultRows[i].getElementsBySelector(".hiddenData");

		var array = {
			"STATUS" : data[0].innerHTML.trim(),
			"NUM" : data[1].innerHTML.trim(),
			"RANK" : data[2].innerHTML.trim(),
			"SURYO_ITEM_FR" : data[3].innerHTML.trim(),
			"SURYO_ITEM_TO" : data[4].innerHTML.trim(),
			"SURYO_PIECE_FR" : data[5].innerHTML.trim(),
			"SURYO_PIECE_TO" : data[6].innerHTML.trim()
		}

		resultArray[resultArray.length] = array;
	}
	
	var sendArray = {
		"TIMESTAMP" : $("timeStamp1").innerHTML,
		"DATA" : resultArray
	};

	//ポストの送信データを作る
	//JSONへ変更
	var postData = Object.toJSON(sendArray);

    //ポップアップを読み込む
    new Ajax.Request( updUrlRank, {
	  method : 'post',
	  parameters: postData,

	  onSuccess : onSuccessEventRank,

	  onFailure : function( event )  {
	    location.href = "/DCMS/WPO030/index?return_cd=91&message=" + Base64.encode(DCMSMessage.format("CMNE000103"));
	  },
	  onException : function( event, ex )  {
	    location.href = "/DCMS/WPO030/index?return_cd=92&message=" + Base64.encode(DCMSMessage.format("CMNE000104"));
	  }
	});
}

//更新処理後の初期表示処理
function onSuccessEventRank(res) {

	//レスポンス
	var result = escapePHP(res.responseText.trim());

	location.href = "/DCMS/WPO030/index?" + result + "&display=true";
}
