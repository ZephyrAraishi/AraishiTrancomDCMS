var actionUrl3;

//アップデートイベント初期か
function initUpdate(url) {

	actionUrl3 = url;

	$("updateButton").observe("click",clickUploadButton);
}

function clickUploadButton(event) {


	if(!window.confirm(DCMSMessage.format('CMNQ000001'))){
		return;
	}

	$$("body")[0].insert(createUpdatingCurtain());

	var resultArray = new Array();

	//データを取得する
	var resultRows = $("lstTable").getElementsBySelector(".tableRow");

	for (var i=0; i < resultRows.length; i++) {

		var bnri_dai_cd;	// 大分類
		var bnri_cyu_cd;	// 中分類
		var kbn_tani;		// 単位区分
		var suryo;			// 数量
		var hiyou;			// 費用
		var exp;			// 説明

		var elements = resultRows[i].getElementsBySelector(".hiddenData");
		var cells    = resultRows[i].getElementsBySelector(".tableCell");
		var suryo = removeComma(cells[4].innerHTML);
		var hiyou = removeComma(cells[5].innerHTML);
		var exp = cells[6].innerHTML;

		var array = {
				"bnri_dai_cd" : elements[0].innerHTML,
				"bnri_cyu_cd" : elements[1].innerHTML,
				"bnri_dai_ryaku" : cells[1].innerHTML,
				"bnri_cyu_ryaku" : cells[2].innerHTML,
				"kbn_tani_nm" : cells[3].innerHTML,
				"kbn_tani" : elements[2].innerHTML,
				"suryo" : suryo,
				"hiyou" : hiyou,
				"exp" : exp,
				"row" : cells[0].innerHTML,
				"changed" : elements[3].innerHTML,
			};

		resultArray[resultArray.length] = array;
	}
	
	var res = {
			"timestamp" : $("timestamp").value,
			"pre_kbn_keiyaku" : $("pre_kbn_keiyaku").value,
			"pre_seikyu_sime" : $("pre_seikyu_sime").value,
			"pre_siharai_sight" : $("pre_siharai_sight").value,
			"kbn_keiyaku" : $("kbn_keiyaku").value,
			"seikyu_sime" : $("seikyu_sime").value,
			"siharai_sight" : $("siharai_sight").value,
			'data' : resultArray
	};

	//ポストの送信データを作る
	//JSONへ変更
	
	var postData = Object.toJSON(res);

    //ポップアップを読み込む
    new Ajax.Request( actionUrl3, {
	  method : 'post',
	  parameters: postData,

	  onSuccess : onSuccessEvent,

	  onFailure : function( event )  {
	    location.href = "/DCMS/WEX020/index?return_cd=91&message="
	    	+ Base64.encode(DCMSMessage.format("CMNE000103"));
	  },
	  onException : function( event, ex )  {
	    location.href = "/DCMS/WEX020/index?return_cd=92&message="
	    	+ Base64.encode(DCMSMessage.format("CMNE000104"));
	  }
	});
}

function onSuccessEvent(res) {

	var result = escapePHP(res.responseText.trim());

	var url = "/DCMS/WEX020/index?";
	url += result;
	url += "&display=true";
	url += "&kbn_keiyaku=" + $("kbn_keiyaku").value;
	url += "&seikyu_sime=" + $("seikyu_sime").value;
	url += "&siharai_sight=" + $("siharai_sight").value;
	url += "&pre_kbn_keiyaku=" + $("pre_kbn_keiyaku").value;
	url += "&pre_seikyu_sime=" + $("pre_seikyu_sime").value;
	url += "&pre_siharai_sight=" + $("pre_siharai_sight").value;
	
	location.href = url;

}
