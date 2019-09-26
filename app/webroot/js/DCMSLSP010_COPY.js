var actionCopyUrl = "";

//リロード時の初期処理
function initCopy(url) {
	actionCopyUrl = url;
	
	if ($("copyButton") != null) {
		$("copyButton").observe("click",clickCopyButton);
	}
}

/**
 * コピーボタン押下処理
 */
function clickCopyButton() {
	
	var srcDate = $("sel_date_hidden").value;
	var destDate = $("setDateCombo").options[$("setDateCombo").selectedIndex].text;
	
	if (destDate == "") {
		alert(DCMSMessage.format("CMNE000001", "コピー先日付"));
		return false;
	}
	if (srcDate == destDate) {
		alert(DCMSMessage.format("CMNE000007", "コピー先日付"));
		return false;
	}
	
    // 確認ダイアログを表示
	var msg = "";
	msg += srcDate + "の週の登録内容を" + destDate + "の週にコピーします。\n";
	msg += "コピー先に登録があった場合は全て上書きされます。\n";
	msg += "\n";
	msg += "よろしいですか？";
	
    var res = confirm(msg);
    if (res == false) {
	    return;
    }
    
	//全体を隠すカーテンをつける
	$$("body")[0].insert(createUpdatingCurtain());
    
	var rows = $("lstTable").getElementsBySelector(".tableRow");
	var rowTitles = $("lstTable").getElementsBySelector(".tableRowTitle");
	var dataArray = new Array();
	var titles = rowTitles[0].getElementsBySelector(".tableCell");
	var stTitle = titles[5].innerHTML.trim();
	var edTitle = titles[11].innerHTML.trim();
	for (var i = 0; i < rows.length; i++) {
		var cells = rows[i].getElementsBySelector(".tableCell");
		var datas = rows[i].getElementsBySelector(".hiddenData");
		dataArray[i] = {
				"staffCd" : cells[1].innerHTML.trim(),
				"syugyoSt" : datas[4].innerHTML.trim(),
				"syugyoStLbl" : stTitle,
				"syugyoEd" : datas[23].innerHTML.trim(),
				"syugyoEdLbl" : edTitle
		}
	}
    
    var sendArray = {
			'srcDate' : srcDate.substring(0, 4) + srcDate.substring(5, 7) + srcDate.substring(8, 10),
			'destDate' : destDate.substring(0, 4) + destDate.substring(5, 7) + destDate.substring(8, 10),
			'data' : dataArray
	};
	var postData = Object.toJSON(sendArray);
	
    new Ajax.Request(actionCopyUrl, {
    	method : 'post',
    	parameters: postData,
    	onSuccess : onSuccessCopyEvent,
    	onFailure : function( event )  {
    		location.href = "/DCMS/LSP010/index?return_cd=91&message=" + Base64.encode(DCMSMessage.format("CMNE000103"));
    	},
    	onException : function( event, ex )  {
    		location.href = "/DCMS/LSP010/index?return_cd=92&message=" + Base64.encode(DCMSMessage.format("CMNE000104"));
    	}
    });
}

//更新成功時
function onSuccessCopyEvent(res) {
	
	//レスポンス
	var result = escapePHP(res.responseText.trim());
	
	var kotei = '';
	var sel_date      = '';
	var set_date      = '';
	var toggle_kbn    = '1';
	var staff_cd_hidden = '';
	var staff_nm_hidden = '';
	var mode = '0';

	var get = getRequest();
	
	if (get.length != 0) {
	
		if (get["kotei"] != null) {
			kotei = decodeURI(get["kotei"]);
		}
		
		if (get["toggle_kbn"] != null) {
			toggle_kbn = get["toggle_kbn"];
		}
		
		if (get["sel_date"] != null) {
			sel_date = get["sel_date"];
		}
		
		set_date = $("setDateCombo").value;
		
		if (get["staff_cd_hidden"] != null) {
			staff_cd_hidden = get["staff_cd_hidden"];
		}
		
		if (get["staff_nm_hidden"] != null) {
			staff_nm_hidden = get["staff_nm_hidden"];
		}

		if ($("sel_date_lsp020").value != "") {
			sel_date = $("sel_date_lsp020").value;
		}
		if ($("mode").value != "") {
			mode = $("mode").value;
		}
	}

	location.href = "/DCMS/LSP010/index?" + result + "&kotei=" + kotei +
													 "&staff_cd_hidden=" + staff_cd_hidden + 
													 "&staff_nm_hidden=" + staff_nm_hidden + 
													 "&toggle_kbn=" + toggle_kbn + 
													 "&sel_date=" + sel_date + 
													 "&set_date=" + set_date + 
													 "&mode=" + mode + 
													 "&display=true";


}