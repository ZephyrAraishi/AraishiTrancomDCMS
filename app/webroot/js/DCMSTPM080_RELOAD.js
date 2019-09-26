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

		if (get["BatchNo_from"] != null) {
			$("BatchNo_from").value =decodeURI(get["BatchNo_from"]);
		}

		if (get["BatchNo_to"] != null) {
			$("BatchNo_to").value =decodeURI(get["BatchNo_to"]);
		}

		if (get["Tokuisaki"] != null) {
			$("Tokuisaki").value =decodeURI(get["Tokuisaki"]).replace("+"," ");
		}

		if (get["homen"] != null) {
			$("homen").value =decodeURI(get["homen"]).replace("+"," ");
		}

		if (get["tenpo"] != null) {
			$("tenpo").value =decodeURI(get["tenpo"]).replace("+"," ");
		}

		if (get["Sinchoku"] != null) {
			$("sinchokukubunCombo").setValue(decodeURI(get["Sinchoku"]));
		}

		if (get["Shohin"] != null) {
			$("Shohin").value =decodeURI(get["Shohin"]).replace("+"," ");
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

}



