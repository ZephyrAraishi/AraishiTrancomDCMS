//画面更新や描画を初期化する関数
function initReload() {

	//ゲットメッセージ取得
	var get = getRequest();

	//パラメータがある場合
	if (get.length != 0) {

		if (get["ymd"] != null) {
			$("ymd").setValue(get["ymd"]);
		}

		//リターンコード 0:更新成功 0以外:更新失敗
		if (get["return_cd"] != null) {
			//更新成功の場合、青色のメッセージを出す
			if (get["return_cd"] != "0") {
			//更新失敗の場合赤色のメッセージを出す。
				$("errorMsg").update(Base64.decode(get["message"]));
			}
		}
	}
	
	setReload();
}

function setReload() {
	var nextMinute = 5;	// ５分後に更新
	var dd = new Date();
	var padding = function(n) {
		return ('0' + n).slice(-2);
	};
	var doReload = function() {
		// ポップアップが表示されていれば更新を行わない
		if ( popUpView != null ) {
			setTimeout(doReload, nextMinute * 60 * 1000);
		} else {
			location.reload();
		}
	};
	
	dd.setMinutes(dd.getMinutes() + nextMinute);
	
	var status = "次回更新時刻　" + padding(dd.getHours()) + ":" + padding(dd.getMinutes()) + ":" + padding(dd.getSeconds());
	Element.update(("nextReload"), status); 
	
	setTimeout(doReload, nextMinute * 60 * 1000);
}