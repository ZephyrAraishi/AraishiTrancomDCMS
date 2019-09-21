var refreshIntervalId;

var actionUrl3 = "";

//
//リロード時の初期処理
function initReload() {
	
	//完了ボタン押下イベント
	if ($("completeButton") != null) {
		$("completeButton").observe("click",clickCompleteButton);
	}

	//更新メッセージ
	var get = getRequest();


	
	if (get.length != 0) {
	
		if (get["sel_date"] != null) {
			if (get["target_ymd"] != null) {
				$("selDateCombo").setValue(decodeURI($("sel_date_lsp020").value));
			} else {
				$("selDateCombo").setValue(decodeURI(get["sel_date"]));
			}
			$("sel_date_hidden").setValue($("selDateCombo").options[$("selDateCombo").selectedIndex].text);
			
		} else {
			//画面を開いた時の初期値として表示
			$("selDateCombo").setValue(decodeURI(0));
		}

		if (get["set_date"] != null) {
			if (get["display"] != null) {
				$("setDateCombo").setValue(decodeURI(get["set_date"]));
			}
		}

		if (get["staff_cd_hidden"] != null) {
			$("staff_cd_hidden").setValue(decodeURI(get["staff_cd_hidden"]));
			$("staff_cd").innerHTML = decodeURI(get["staff_cd_hidden"]);
			$("staff_cd_srh_hidden").setValue(decodeURI(get["staff_cd_hidden"]));
		}

		if (get["staff_nm_hidden"] != null) {
			$("staff_nm_hidden").setValue(decodeURI(get["staff_nm_hidden"]));
			$("staff_nm").innerHTML = decodeURI(get["staff_nm_hidden"]);
			$("staff_nm_srh_hidden").setValue(decodeURI(get["staff_nm_hidden"]));
		}
		if (get["mode"] != null) {
			$("mode").setValue(decodeURI(get["mode"]));
		}

		
		//分類コード
		if (get["kotei"] != null) {
		
			var koteiCd = decodeURI(get["kotei"]);
		
			$("koteiCd").setValue(koteiCd);
			$("koteiCd_hidden").setValue(koteiCd);
			
			if (koteiCd != '') {
			
				var koteis = koteiCd.split("_");
				
				var koteiDai = null;
				var koteiCyu = null;
				var koteiSai = null;
				
				if (koteis.length >= 3) {
					
					var lis = $('ul_class').getElementsBySelector('li');
					
					// 工程(細)情報の表示をすべて非表示にする
					for (var i=0; i < lis.length; i++) {
					
						var liKotei = lis[i].readAttribute('value');
						
						if (liKotei.length < 3) {
							
							liKotei = ("00" + liKotei).slice(-3);
						}
				
						if (liKotei == (koteis[0] + "_" + koteis[1] + "_" + koteis[2]) ) {
							
							koteiSai = lis[i].getElementsBySelector("div")[0].innerHTML.trim();
							
							if (koteiSai == '»') {
								
								koteiSai = lis[i].getElementsBySelector("div")[1].innerHTML.trim();
							}
							
							break;
						}
					}
					
				}
				
				if (koteis.length >= 2) {
					
					var lis = $('ul_class').getElementsBySelector('li');
					
					// 工程(細)情報の表示をすべて非表示にする
					for (var i=0; i < lis.length; i++) {
					
						var liKotei = lis[i].readAttribute('value');
						
						if (liKotei.length < 3) {
							
							liKotei = ("00" + liKotei).slice(-3);
						}
				
						if (liKotei == (koteis[0] + "_" + koteis[1]) ) {
							
							koteiCyu = lis[i].getElementsBySelector("div")[0].innerHTML.trim();
							
							if (koteiCyu == '»') {
								
								koteiCyu = lis[i].getElementsBySelector("div")[1].innerHTML.trim();
							}
							
							break;
						}
					}
					
				}
			
				if (koteis.length >= 1) {
					
					var lis = $('ul_class').getElementsBySelector('li');
					
					// 工程(細)情報の表示をすべて非表示にする
					for (var i=0; i < lis.length; i++) {
					
						var liKotei = lis[i].readAttribute('value');
						
						if (liKotei.length < 3) {
							
							liKotei = ("00" + liKotei).slice(-3);
						}
				
						if (liKotei == koteis[0] ) {
							
							koteiDai = lis[i].getElementsBySelector("div")[0].innerHTML.trim();
							
							if (koteiDai == '»') {
								
								koteiDai = lis[i].getElementsBySelector("div")[1].innerHTML.trim();
							}
							
							break;
						}
					}
					
				}
				
				if (koteiDai != null && koteiCyu != null && koteiSai != null) {
					
					$("koteiText").setValue(koteiDai + ' > ' + koteiCyu + ' > ' + koteiSai);
					
				} else if (koteiDai != null && koteiCyu != null) {
					
					$("koteiText").setValue(koteiDai + ' > ' + koteiCyu);
					
				} else if (koteiDai != null) {
					
					$("koteiText").setValue(koteiDai);
				}
				
				$("koteiText_hidden").setValue($("koteiText").value);
			
			}
		}
		


		if (get["return_cd"] != null) {

			//画面上部にメッセージを表示
			if (get["return_cd"] == "0") {

					$("infoMsg").update(Base64.decode(get["message"]));
			}  else {

					$("errorMsg").update(Base64.decode(get["message"]));
					
					//
					var resultRows = $$("body")[0].getElementsBySelector(".tableRow");
					
					for(var i=0 ; i< resultRows.length; i++) {
						
						var flg = resultRows[i].getElementsBySelector(".hiddenData")[1].innerHTML;
						
						if (parseInt(flg,10) < 3) {
							
							setRowColor(flg,resultRows[i]);
						} else {
							
							setDeletedRowColor(resultRows[i])
						}
					}
			}
		}

	}
	
}





//
//アップデートイベント初期か
function initUpdate(url) {

	actionUrl3 = url;

	if ($("updateButton") != null) {
		$("updateButton").observe("click",clickUploadButton);
	}
}


var updatingCurtain = null;

//
//更新ボタンクリックイベント
function clickUploadButton(event) {


//	//照会時の処理
//	if ($F("toggleKbn") != 1) {
//		return;
//	}

      // 確認ダイアログを表示
      var res = confirm(DCMSMessage.format("CMNQ000001"));
      // 選択結果で分岐
      if( res == false ) {
	    return;
      }

	//全体を隠すカーテンをつける
	$$("body")[0].insert(createUpdatingCurtain());

	//渡す配列　タイトル
	var resultArrayTtl = new Array();

	//データを取得する
	var resultRowsTtl = $("lstTable").getElementsBySelector(".tableRowTitle");

//	for (var i=0; i < resultRows.length; i++) {

		//セル配列
		var cellsArrayTtl = new Array();
		var cellsTtl = resultRowsTtl[0].getElementsBySelector(".tableCell");

		for (var m=0; m < cellsTtl.length; m++) {

			cellsArrayTtl[m] = cellsTtl[m].innerHTML.strip();
		}

		//データ配列
		var dataArrayTtl = new Array();
		var dataTtl = resultRowsTtl[0].getElementsBySelector(".hiddenData");

		for (var m=0; m < dataTtl.length; m++) {

			dataArrayTtl[m] = dataTtl[m].innerHTML.strip();
		}

		//行配列
		var rowArrayTtl = {
			'cellsTtl' : cellsArrayTtl,
			'dataTtl' : dataArrayTtl
		};

		//行追加
		resultArrayTtl[0] = rowArrayTtl;
//	}

	//渡す配列　データ
	var resultArray = new Array();

	//データを取得する
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
			'data' : dataArray
		};

		//行追加
		resultArray[i] = rowArray;
	}
		
	//ポストの送信データを作る
	//JSONへ変更
	
	var sendArray = {
		
		'ttl' : resultArrayTtl,
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
	    location.href = "/DCMS/LSP010/index?return_cd=91&message=" + Base64.encode(DCMSMessage.format("CMNE000103"));
	  },
	  onException : function( event, ex )  {
	    location.href = "/DCMS/LSP010/index?return_cd=92&message=" + Base64.encode(DCMSMessage.format("CMNE000104"));
	  }
	});

}

//
//更新成功時
function onSuccessEvent(res) {
	
	//レスポンス
	var result = escapePHP(res.responseText.trim());
//	var result = res.responseText.strip();
	
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


//
//スタッフ入力クリアボタンクリックイベント
function clickStaffClear(event) {


	$("staff_cd_hidden").setValue("");
	$("staff_cd").innerHTML = "";
	$("staff_nm_hidden").setValue("");
	$("staff_nm").innerHTML = "";


}


//n日後、n日前の日付を求める
/**
 * 年月日と加算日からn日後、n日前を求める関数
 * year 年
 * month 月
 * day 日
 * addDays 加算日。マイナス指定でn日前も設定可能
 */
function computeDate(year, month, day, addDays) {
    var dt = new Date(year, month - 1, day);
    var baseSec = dt.getTime();
    var addSec = addDays * 86400000;//日数 * 1日のミリ秒数
    var targetSec = baseSec + addSec;
    dt.setTime(targetSec);
    return dt;
}

/**
 * 完了ボタン押下処理
 * 
 */
function clickCompleteButton() {
	
	if (!(!window.opener || window.opener.closed)){ 
		window.opener.reloadStaffList();
	}
	window.close();
}
