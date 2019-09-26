var updateURL = "";
var updateCoverElement = null;

/**
 * 更新初期化処理
 * 
 */
function initUpdate(url) {
	updateURL = url;
}

/**
 * 更新ボタン押下時処理
 * 
 */
function clickUpdateButton() {
	
	// 確認ダイアログを表示
	var res = confirm(DCMSMessage.format("CMNQ000001"));
	if(res == false) {
		return;
	}
	
	// 全体を隠すカーテンをつける
	updateCoverElement = createUpdatingCurtain();
	$$("body")[0].insert(updateCoverElement);
	
	var rows = $("lstTable").getElementsBySelector(".tableRow");
	var dataArray = new Array();
	
	// 更新する一覧情報を設定
	var type = $("type").value;
	for (var i = 0; i < rows.length; i++) {
		var cells = rows[i].getElementsBySelector(".tableCell");
		var datas = rows[i].getElementsBySelector(".hiddenData");
		dataArray[i] = {
				"updType" : datas[0].innerHTML.trim(),
				"kznDataNo" : datas[1].innerHTML.trim(),
				"tgtKikanSt" : datas[2].innerHTML.trim(),
				"tgtKikanEd" : datas[3].innerHTML.trim(),
				"bnriDaiCd" : datas[4].innerHTML.trim(),
				"bnriCyuCd" : datas[5].innerHTML.trim(),
				"bnriSaiCd" : datas[6].innerHTML.trim(),
				"kbnKznKmk" : datas[7].innerHTML.trim(),
				"kznTitle" : datas[8].innerHTML.trim(),
				"kznText" : datas[9].innerHTML.trim(),
				"kznValue" : datas[10].innerHTML.trim(),
				"kznCost" : datas[11].innerHTML.trim(),
				"kbnSyuryo" : datas[12].innerHTML.trim(),
				
				"row" : cells[0].innerHTML.trim(),
				"bnriDaiNm" : cells[4].innerHTML.trim(),
				"bnriCyuNm" : cells[5].innerHTML.trim(),
				"bnriSaiNm" : cells[6].innerHTML.trim(),
				"kznKmkValue" : cells[7].innerHTML.trim()
		}
	}
	
	// 送信するデータをJSON形式に変更
	var resultArray = {
			"timestamp" : $("timestamp").innerHTML,
			"kikanKbn" : $("type").value,
			'kaizenData' : dataArray
	};
	var postData = Object.toJSON(resultArray);
	
	new Ajax.Request(updateURL, {
		method : 'post',
		parameters: postData,
		onSuccess : onSuccessEvent,
		onFailure : function(event)  {
			location.href = "/DCMS/WEX090/index?message=" + Base64.encode(DCMSMessage.format("CMNE000103"));
		},
		onException : function(event, ex)  {
			location.href = "/DCMS/WEX090/index?message=" + Base64.encode(DCMSMessage.format("CMNE000104"));
		}
	});
}

/**
 * 更新成功処理
 * 
 */
function onSuccessEvent(event) {
	
	var result = escapePHP(event.responseText.trim());
	var get = getRequest();
	
	var url = "/DCMS/WEX090/index";
	url += "?" + result;
	url += "&type=" + $("type").value;
	url += "&maxCnt=" + $("maxCnt").value;
	if (get["viewFrom"] != null) {
		url += "&from=" + get["viewFrom"];
		url += "&viewFrom=" + $("viewFrom").value;
	}
	if (get["viewTo"] != null) {
		url += "&to=" + get["viewTo"];
		url += "&viewTo=" + $("viewTo").value;
	}
	if (get["kotei"] != null) {
		url += "&kotei=" + get["kotei"];
	}
	if (get["kznTitle"] != null) {
		url += "&kznTitle=" + get["kznTitle"];
	}
	if (get["kznKmk"] != null) {
		url += "&kznKmk=" + get["kznKmk"];
	}
	
	location.href = url;
}