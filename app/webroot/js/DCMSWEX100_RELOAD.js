//画面更新や描画を初期化する関数
function initReload() {

	//ゲットメッセージ取得
	var get = getRequest();

	//パラメータがある場合
	if (get.length != 0) {

		if (get["ymd_from"] != null) {
			$("ymd_from").setValue(get["ymd_from"]);
		}

		if (get["ymd_to"] != null) {
			$("ymd_to").setValue(decodeURI(get["ymd_to"]));
		}

		if (get["kaisya_cd"] != null) {
			$("kaisya_cd").setValue(decodeURI(get["kaisya_cd"]));
		}

		if (get["staff_cd_nm"] != null) {
			$("staff_cd_nm").value =decodeURI(get["staff_cd_nm"]).replace('%3A',':');
		}
		
		if (get["bnri_cyu_cd"] != null) {
			$("bnri_cyu_cd").setValue(decodeURI(get["bnri_cyu_cd"]));
		}
		
		if (get["syaryo_cd"] != null) {
			$("syaryo_cd").setValue(decodeURI(get["syaryo_cd"]));
		}
		
		if (get["area_cd"] != null) {
			$("area_cd").setValue(decodeURI(get["area_cd"]));
		}
		
		if (get["more"] != null) {

			if (get["more"] == 'on') {
				$("more").checked = true;
			} else {
				$("more").checked = false;
			}

		}

		if (get["jikan_from"] != null && get["jikan_to"] != null && get["jikan_from"] != "" && get["jikan_to"] != "") {
			$("jikan_from").value =decodeURI(get["jikan_from"]).replace('%3A',':');
			$("jikan_to").value =decodeURI(get["jikan_to"]).replace('%3A',':');
		}
		
		if (get["kyori_from"] != null && get["kyori_to"] != null && get["kyori_from"] != "" && get["kyori_to"] != "") {
			$("kyori_from").value =decodeURI(get["kyori_from"]).replace('%3A',':');
			$("kyori_to").value =decodeURI(get["kyori_to"]).replace('%3A',':');
		}


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

				//行取得
				var resultRows = $$("body")[0].getElementsBySelector(".tableRow");

				for(var i=0 ; i< resultRows.length; i++) {

					var flg = resultRows[i].getElementsBySelector(".hiddenData")[0].innerHTML;

					//何もなし、更新フラグが立っている場合
					if (parseInt(flg,10) < 3) {
						setRowColor(flg,resultRows[i]);
					}
				}
			}
		}
	}

	$('search_form').observe('submit', function(e) { 
		
		//工程開始時刻のフォーマットが不正なとき
		if ($F("jikan_from") != "" && !$F("jikan_from").trim().match(/^[0-9]{4}$/)) {
			alert(DCMSMessage.format("CMNE000004", "工程開始時間", "4"));
	
			e.stop();
		}
		
		//工程終了時刻のフォーマットが不正なとき
		if ($F("jikan_to") != "" && !$F("jikan_to").trim().match(/^[0-9]{4}$/)) {
			alert(DCMSMessage.format("CMNE000004", "工程開始時間", "4"));
	
			e.stop();
		}
		
		//開始距離のフォーマットが不正なとき
		if ($F("kyori_from") != "" && !$F("kyori_from").trim().match(/^[0-9]+$/)) {
			alert(DCMSMessage.format("CMNE000002", "開始距離"));
	
			e.stop();
		}
		
		//終了距離のフォーマットが不正なとき
		if ($F("kyori_to") != "" && !$F("kyori_to").trim().match(/^[0-9]+$/)) {
			alert(DCMSMessage.format("CMNE000002", "終了距離"));
	
			e.stop();
		}
	    
	});
}

