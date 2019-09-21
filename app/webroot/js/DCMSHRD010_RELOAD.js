//画面更新や描画を初期化する関数
function initReload() {

	//ゲットメッセージ取得
	var get = getRequest();

	//パラメータがある場合
	if (get.length != 0) {

		//検索条件指定がある場合、デフォルトパラメータとして
		//それぞれのValueをセットする
		if (get["staff_cd"] != null) {
			$("staff_cd").setValue(get["staff_cd"]);
		}

		if (get["staff_nm"] != null) {
			$("staff_nm").setValue(decodeURI(get["staff_nm"]));
		}
		
		if (get["haken_cd"] != null) {
			$("haken_cd").setValue(decodeURI(get["haken_cd"]));
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

			}
		}

	}

	var rows = $("lstTable").getElementsBySelector(".tableRow");
	
	for (var i=0; i < rows.length; i++) {
		
		var data = rows[i].getElementsBySelector(".hiddenData");
		
		if (data[0].innerHTML.trim() == "1") {
			setDeletedRowColor(rows[i]);
		}
	}
}

