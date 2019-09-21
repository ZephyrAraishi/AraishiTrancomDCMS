/**
 * 画面更新や描画を初期化する関数
 */
function initReload() {

	//ゲットメッセージ取得
	var get = getRequest();

	//パラメータがある場合
	if (get.length != 0) {
	
		//リターンコード 0:更新成功 0以外:更新失敗
		if (get["return_cd"] != null) {
			//更新成功の場合、青色のメッセージを出す
			if (get["return_cd"] == "0") {
				$("infoMsg").update(Base64.decode(get["message"]));

			//更新失敗の場合赤色のメッセージを出す。
			//前回のテーブルデータがそのままでているので
			//ここで行の色だけかえてやる
			} else {
				$("errorMsg").update(Base64.decode(get["message"]));
			}
		}
	}
	
	//分類コード
	if ($("kotei_hidden").innerHTML != null) {
	
		var koteiCd = $("kotei_hidden").innerHTML;
	
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

	// カレンダー有無切り替え
	$$("#jissekiForm input[type='radio'][id^='PeriodType']").each(function(elem) {
		elem.observe("click", clickPeriodTypeRadio);
	});

	$$(".pagaerNavi a").each(function(elem) {
		elem.observe("click", submitByPageID);
	});

	// メッセージがない場合は、検索結果の視認性を上げるため検索フォームまでスクロールしておく
	if ($("errorMsg").innerHTML == "" && $("infoMsg").innerHTML == "" && $$(".hiddenData.RESULT_COUNT")[0].innerHTML > 0) {
		location.href = "#jissekiForm";
	}
}

/**
 * ラジオボタンクリックイベント
 * @param event
 */
function clickPeriodTypeRadio(event) {
	var clickRadio = Event.element(event);
	if (clickRadio.value == 'daily') {
		$("monthlyPeriodFrom").hide();
		$("monthlyPeriodTo").hide();
		$("dailyPeriodFrom").show();
		$("dailyPeriodTo").show();
	} else {
		$("monthlyPeriodFrom").show();
		$("monthlyPeriodTo").show();
		$("dailyPeriodFrom").hide();
		$("dailyPeriodTo").hide();
	}
}

/**
 * ページングのパスをformのaction属性に置き換えてサブミットする
 * @param event
 */
function submitByPageID(event) {
	if (event.preventDefault) {
		event.preventDefault();
	} else {
		event.returnValue = false;
	}

	var formObj = $("jissekiForm");
	formObj.action = Event.element(event).href;
	formObj.submit();
}