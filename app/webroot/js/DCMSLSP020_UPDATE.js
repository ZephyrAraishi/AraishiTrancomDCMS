var updateURL = "";
var updateCoverElement = null;

/**
 * 更新初期処理
 */
function initUpdate(url) {
	updateURL = url;
}

/**
 * 更新ボタンクリックイベント
 */
function clickUploadButton(event) {
	
	// 確認ダイアログを表示
	var res = confirm(DCMSMessage.format("CMNQ000001"));
	if(res == false) {
		return;
	}
	
	// 全体を隠すカーテンをつける
	updateCoverElement = createUpdatingCurtain();
	$$("body")[0].insert(updateCoverElement);
	
	var simulationArray = new Array();
	var anbunArray = new Array();
	
	// 按分率の入力データを格納
	var anbunRows = $("inputTable").getElementsBySelector(".anbunRow");
	var timeCells = anbunRows[0].getElementsBySelector(".inputCell");
	var rateDatas = anbunRows[0].getElementsBySelector(".hiddenData");
	for (var i = 0; i < timeCells.length; i++) {
		anbunArray[i] = {
			"time" : rateDatas[i].innerHTML.trim(),
			"rate" : timeCells[i].getElementsBySelector("input")[0].value
		}
	}
	
	// 予測シュミレーションの入力データを格納
	var simulationRows = $("inputTable").getElementsBySelector(".simulationRow");
	for (var i = 0; i < simulationRows.length; i++) {
		var cells = simulationRows[i].getElementsBySelector(".inputCell");
		var datas = simulationRows[i].getElementsBySelector(".hiddenData");
		simulationArray[i] = {
			"daiCd" : datas[0].innerHTML.trim(),
			"daiNm" : datas[1].innerHTML.trim(),
			"cyuCd" : datas[2].innerHTML.trim(),
			"cyuNm" : datas[3].innerHTML.trim(),
			"uriageKbn" : datas[4].innerHTML.trim(),
			"cyuCnt" : datas[5].innerHTML.trim(),
			"ninzu" : datas[6].innerHTML.trim(),
			"buturyo" : cells[0].getElementsBySelector("input")[0].value,
			"start" : cells[1].getElementsBySelector("input")[0].value,
			"end" : cells[2].getElementsBySelector("input")[0].value
		}
	}
		
	// 送信するデータをJSON形式に変更
	var resultArray = {
			"ymd" : $("ymd").value,
			'anbun' : anbunArray,
			'simulation' : simulationArray,
	};
	var postData = Object.toJSON(resultArray);
	
	new Ajax.Request(updateURL, {
		method : 'post',
		parameters: postData,
		onSuccess : onSuccessEvent,
		onFailure : function(event)  {
			location.href = "/DCMS/LSP020/index?message=" + Base64.encode(DCMSMessage.format("CMNE000103"));
		},
		onException : function(event, ex)  {
			location.href = "/DCMS/LSP020/index?message=" + Base64.encode(DCMSMessage.format("CMNE000104"));
		}
	});
}

/**
 * 更新成功処理
 */
function onSuccessEvent(event) {
	
	var result = escapePHP(event.responseText.trim());
	
	var url = "/DCMS/LSP020/index";
	url += "?" + result;
	url += "&monthViewFlg=1";
	url += "&startYmd=" + $("startYmd").value;
	url += "&endYmd=" + $("endYmd").value;
	url += "&ymd=" + $("ymd").value;
	
	location.href = url;
}