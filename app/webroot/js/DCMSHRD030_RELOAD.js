//画面更新や描画を初期化する関数
function initReload() {

	//ゲットメッセージ取得
	var get = getRequest();

	//パラメータがある場合
	if (get.length != 0) {

		if (get["haken_kaisya_cd"] != null) {
			$("hakenkaisyaCombo1").setValue(decodeURI(get["haken_kaisya_cd"]));
		}
		
		if (get["haken_kaisya_cd2"] != null) {
			$("hakenkaisyaCombo").setValue(decodeURI(get["haken_kaisya_cd"]));
		}
		
	}
}

