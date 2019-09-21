//画面更新や描画を初期化する関数
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

	var daihyo = $$("body")[0].getElementsBySelector(".daihyo");
	
	for (var i=0; i < daihyo.length; i++) {
	
		daihyo[i].observe("change",selectKyoten);
	}

}

function selectKyoten(event) {
	
	//選択行を取得
	var element = Event.element(event);
	
	var daihyo = $$("body")[0].getElementsBySelector(".daihyo");
	var kanoKyoten = $$("body")[0].getElementsBySelector(".kanoKyoten");
	
	var count = null;
	
	for(var i=0; i < daihyo.length; i++) {
		if (daihyo[i] == element) {
			count = i;
			break;
		}
	}
	
	if (count != null) {
	
		if (daihyo[count].checked == true) {
			kanoKyoten[count].checked = true;
		} else {
			kanoKyoten[count].checked = false;
		}
	}
}