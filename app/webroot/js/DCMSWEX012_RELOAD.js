var refreshIntervalId;

var actionUrl3 = "";

//
//リロード時の初期処理
function initReload() {
	
	//更新メッセージ
	var get = getRequest();
	
	if (get.length != 0) {
	
		if (get["bnri_sai_cd"] != null) {
			$("WEX050ModelBnriSaiCd").setValue(get["bnri_sai_cd"]);
		}
		
		if (get["bnri_sai_nm"] != null) {
			$("WEX050ModelBnriSaiNm").setValue(decodeURI(get["bnri_sai_nm"]));
		}
		
		if (get["kbn_tani"] != null) {
			$("kbnTaniCombo").setValue(decodeURI(get["kbn_tani"]));
		}
		
		if (get["kbn_ktd_mktk"] != null) {
			$("kbnKtdMktkCombo").setValue(decodeURI(get["kbn_ktd_mktk"]));
		}
		
		if (get["kbn_hissu_wk"] != null) {
			$("kbnHissuWkCombo").setValue(decodeURI(get["kbn_hissu_wk"]));
		}
		
		if (get["kbn_gyomu"] != null) {
			$("kbnGyomuCombo").setValue(decodeURI(get["kbn_gyomu"]));
		}
		
		if (get["kbn_get_data"] != null) {
			$("kbnGetDataCombo").setValue(decodeURI(get["kbn_get_data"]));
		}
		
		if (get["kbn_fukakachi"] != null) {
			$("kbnFukakachiCombo").setValue(decodeURI(get["kbn_fukakachi"]));
		}
		
		if (get["kbn_senmon"] != null) {
			$("kbnSenmonCombo").setValue(decodeURI(get["kbn_senmon"]));
		}
		
		if (get["kbn_ktd_type"] != null) {
			$("kbnKtdTypeCombo").setValue(decodeURI(get["kbn_ktd_type"]));
		}
		
		if (get["kbn_uriage"] != null) {
			$("kbnUriageCombo").setValue(decodeURI(get["kbn_uriage"]));
		}
		
		if (get["keiyaku_buturyo_flg"] != null) {
			$("keiyakuButuryoFlgCombo").setValue(decodeURI(get["keiyaku_buturyo_flg"]));
		}
		
		//分類コード
		if (get["kotei"] != null) {
		
			var koteiCd = decodeURI(get["kotei"]);
		
			$("koteiCd").setValue(koteiCd);
			
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
						
						if (liKotei == null) {
							
							continue;
						}
						
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
						
						if (liKotei == null) {
							
							continue;
						}
						
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
						
						if (liKotei == null) {
							
							continue;
						}
						
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

	$("updateButton").observe("click",clickUploadButton);
}


var updatingCurtain = null;

//
//更新ボタンクリックイベント
function clickUploadButton(event) {

      // 確認ダイアログを表示
      var res = confirm(DCMSMessage.format("CMNQ000001"));
      // 選択結果で分岐
      if( res == false ) {
	    return;
      }

	//全体を隠すカーテンをつける
	$$("body")[0].insert(createUpdatingCurtain());

	//渡す配列
	var resultArray = new Array();

	//データを取得する
	var resultRows = $("lstTable").getElementsBySelector(".tableRow");

	for (var i=0; i < resultRows.length; i++) {

		//番号
		var bango = resultRows[i].getElementsBySelector(".tableCellBango")[0].innerHTML.strip();
		
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
			'bango' : bango,
			'cells' : cellsArray,
			'data' : dataArray
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
	    location.href = "/DCMS/WEX050/index?return_cd=91&message=" + Base64.encode(DCMSMessage.format("CMNE000103"));
	  },
	  onException : function( event, ex )  {
	    location.href = "/DCMS/WEX050/index?return_cd=92&message=" + Base64.encode(DCMSMessage.format("CMNE000104"));
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
	var bnri_sai_cd = '';
	var bnri_sai_nm = '';

	var kbn_tani      = '';
	var kbn_ktd_mktk  = '';
	var kbn_hissu_wk  = '';
	var kbn_gyomu     = '';
	var kbn_get_data  = '';
	var kbn_fukakachi = '';
	var kbn_senmon    = '';
	var kbn_ktd_type  = '';
	var keiyaku_buturyo_flg  = '';
	var kbn_uriage    = '';

	var pageID    = '1';
	
	var get = getRequest();
	
	if (get.length != 0) {
	
		if (get["kotei"] != null) {
			kotei = decodeURI(get["kotei"]);
		}
		
		if (get["bnri_sai_cd"] != null) {
			bnri_sai_cd = get["bnri_sai_cd"];
		}
		
		if (get["bnri_sai_nm"] != null) {
			bnri_sai_nm = get["bnri_sai_nm"];
		}
		
		if (get["kbn_tani"] != null) {
			kbn_tani = get["kbn_tani"];
		}
		
		if (get["kbn_ktd_mktk"] != null) {
			kbn_ktd_mktk = get["kbn_ktd_mktk"];
		}
		
		if (get["kbn_hissu_wk"] != null) {
			kbn_hissu_wk = get["kbn_hissu_wk"];
		}
		
		if (get["kbn_gyomu"] != null) {
			kbn_gyomu = get["kbn_gyomu"];
		}
		
		if (get["kbn_get_data"] != null) {
			kbn_get_data = get["kbn_get_data"];
		}
		
		if (get["kbn_fukakachi"] != null) {
			kbn_fukakachi = get["kbn_fukakachi"];
		}
		
		if (get["kbn_senmon"] != null) {
			kbn_senmon = get["kbn_senmon"];
		}
		
		if (get["kbn_ktd_type"] != null) {
			kbn_ktd_type = get["kbn_ktd_type"];
		}
		
		if (get["keiyaku_buturyo_flg"] != null) {
			keiyaku_buturyo_flg = get["keiyaku_buturyo_flg"];
		}
		
		if (get["kbn_uriage"] != null) {
			kbn_uriage = get["kbn_uriage"];
		}

		if (get["pageID"] != null) {
			pageID = get["pageID"];
		}
		
	}

	location.href = "/DCMS/WEX050/index?" + result + "&kotei=" + kotei +
													 "&bnri_sai_cd=" + bnri_sai_cd + 
													 "&bnri_sai_nm=" + bnri_sai_nm + 
													 "&kbn_tani=" + kbn_tani + 
													 "&kbn_ktd_mktk=" + kbn_ktd_mktk + 
													 "&kbn_hissu_wk=" + kbn_hissu_wk + 
													 "&kbn_gyomu=" + kbn_gyomu + 
													 "&kbn_get_data=" + kbn_get_data + 
													 "&kbn_fukakachi=" + kbn_fukakachi + 
													 "&kbn_senmon=" + kbn_senmon + 
													 "&kbn_ktd_type=" + kbn_ktd_type + 
													 "&keiyaku_buturyo_flg=" + keiyaku_buturyo_flg + 
													 "&kbn_uriage=" + kbn_uriage + 
													 "&pageID=" + pageID + 
													 "&display=true";

}
