var refreshIntervalId;

var actionUrl3 = "";

//
//リロード時の初期処理
function initReload() {
	
	//更新メッセージ
	var get = getRequest();
	
	if (get.length != 0) {
	
		if (get["date_from"] != null) {
			$("date_from").setValue(decodeURI(get["date_from"]));
		}
		
		if (get["date_to"] != null) {
			$("date_to").setValue(decodeURI(get["date_to"]));
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
		
		if (get["kbn_fukakachi"] != null) {
			$("kbnFukakachiCombo").setValue(decodeURI(get["kbn_fukakachi"]));
		}
		
		if (get["kbn_senmon"] != null) {
			$("kbnSenmonCombo").setValue(decodeURI(get["kbn_senmon"]));
		}
		
		if (get["kbn_ktd_type"] != null) {
			$("kbnKtdTypeCombo").setValue(decodeURI(get["kbn_ktd_type"]));
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

