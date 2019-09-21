var actionUrl2 = ''

//アップデートイベント初期か
function initUpdate(url) {

	actionUrl2 = url;

	$("updateButton").observe("click",clickUploadButton);
}

function clickUploadButton(event) {

	//全体を隠すカーテンをつける
	$$("body")[0].insert(createUpdatingCurtain());

	var resultArray = new Array();

	//データを取得する
	var resultRows = $("lstTable").getElementsBySelector(".tableRow");

	for (var i=0; i < resultRows.length; i++) {

		var data = resultRows[i].getElementsBySelector(".hiddenData");

		var array = {

			"LINE_COUNT" : data[0].innerHTML,
			"YMD_SYORI" : data[1].innerHTML,
			"S_BATCH_NO" : data[2].innerHTML,
			"PICKING_NUM" : data[3].innerHTML,
			"TOTAL_PIECE_NUM" : data[4].innerHTML,
			"BATCH_NM" : data[5].innerHTML,
			"BATCH_NM_CD" : data[6].innerHTML,
			"CHANGE" : data[7].innerHTML,
			"S_BATCH_NO_CD" : data[8].innerHTML,
			"UPD_CNT_CD" : data[9].innerHTML,
			"YMD_UNYO" : data[10].innerHTML
		};

		resultArray[resultArray.length] = array;

	}

	//ポストの送信データを作る
	//JSONへ変更
	var postData = Object.toJSON(resultArray);

    //ポップアップを読み込む
    new Ajax.Request( actionUrl2, {
	  method : 'post',
	  parameters: postData,

	  onSuccess : onSuccessEvent2,

	  onFailure : function( event )  {
	    location.href = "/DCMS/WPO010/index?return_cd=91&message=" + Base64.encode(DCMSMessage.format("CMNE000103"));
	  },
	  onException : function( event, ex )  {
	    location.href = "/DCMS/WPO010/index?return_cd=92&message=" + Base64.encode(DCMSMessage.format("CMNE000104"));
	  }
	});
}

function onSuccessEvent2(res) {

	var result = escapePHP(res.responseText.trim());

	location.href = "/DCMS/WPO010/index?" + result + "&display=true";

}
