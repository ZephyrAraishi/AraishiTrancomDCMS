var refreshIntervalId;

var actionUrl3 = "";

//
//リロード時の初期処理
function initReload() {



	//更新メッセージ
	var get = getRequest();

	if (get.length != 0) {

		if (get["return_cd"] != null) {

			//画面上部にメッセージを表示
			if (get["return_cd"] == "0") {

					$("infoMsg").update(Base64.decode(get["message"]));
			}  else {

					$("errorMsg").update(Base64.decode(get["message"]));
			}
		}

	}



}

