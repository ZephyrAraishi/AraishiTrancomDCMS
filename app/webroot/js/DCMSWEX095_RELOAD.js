//画面更新や描画を初期化する関数
function initReload() {

	//ゲットメッセージ取得
	var get = getRequest();

	//パラメータがある場合
	if (get.length != 0) {

		if (get["start_ymd_ins"] != null) {
			$("startYmdInsText").setValue(decodeURI(get["start_ymd_ins"]));
		}

		if (get["end_ymd_ins"] != null) {
			$("endYmdInsText").setValue(decodeURI(get["end_ymd_ins"]));
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

		if (get["staff_nm"] != null) {
			$("staff_nmText").setValue(decodeURI(get["staff_nm"]));
		}
	
		//該当行にイベントを付加
		var rows = $("lstTable").getElementsBySelector(".tableRow");
		if (rows != null) {
		    for(i=0; i < rows.length; i++) {
				rows[i].observe("click",clickPopUp);
				rows[i].observe("mouseover",overRow);
				rows[i].observe("mouseout",leaveRow);
			}
		}
	}
}

function clickPopUp(event) {
	
	//右クリックだったら終了
	if (event.isRightClick()) {
		return;
	}
	
	//選択行
	var row = Event.element(event).up("div");
	if (row.hasClassName("tableRow") == false) {
		while (true) {
			row = row.up("div");
			if (row.hasClassName("tableRow") == true) {
				break
			}
		}
	}
	selectedRow = row;
	
	var data  = selectedRow.getElementsBySelector(".hiddenData");
	var staffCd = data[0].innerHTML;
	
	var url = "/DCMS/HRD010/kihon";
	url += "?staff_cd=" + staffCd;
	
	window.open(url, "スタッフ基本情報登録");
}

//テーブルのオーバーイベントハンドラ
//フォーカスマークをつける
function overRow(event) {

	var row = Event.element(event).up("div");

	if (row.hasClassName("tableRow") == false) {

		while (true) {

			row = row.up("div");

			if (row == null) {
				
				return;
			}

			if (row.hasClassName("tableRow") == true) {

				break
			}
		}
	}

	if (row == null)
	{
		return;
	}

	setSelectedRowColor(row);

}

//テーブルのリーブイベントハンドラ
//フォーカスマークを外す
function leaveRow(event) {

	var row = Event.element(event).up("div");

	if (row.hasClassName("tableRow") == false) {

		while (true) {

			row = row.up("div");

			if (row == null) {
				
				return;
			}

			if (row.hasClassName("tableRow") == true) {

				break
			}
		}
	}

	if (row == null)
	{
		return;
	}
	
	setRowColor("0", row)
}
