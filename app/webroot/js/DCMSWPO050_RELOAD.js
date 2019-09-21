//画面更新や描画を初期化する関数
function initReload() {

	//ゲットメッセージ取得
	var get = getRequest();
	
	if (get["ymd_sime"] != null) {
		$("ymd_sime").setValue(get["ymd_sime"]);
	}
	
	//リターンコード 0:更新成功 0以外:更新失敗
	if (get["return_cd"] != null) {
	
		//更新成功の場合、青色のメッセージを出す
		if (get["return_cd"] == "0") {
		
			$("infoMsg").update(Base64.decode(get["message"]));
		
		} else {
		
			$("errorMsg").update(Base64.decode(get["message"]));
		
		}
	}
}

