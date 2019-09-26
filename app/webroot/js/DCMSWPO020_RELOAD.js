//画面更新や描画を初期化する関数
function initReload() {

	//ゲットメッセージ取得
	var get = getRequest();

	//パラメータがある場合
	if (get.length != 0) {

		//検索条件指定がある場合、デフォルトパラメータとして
		//それぞれのValueをセットする
		if (get["batch_nm_cd"] != null) {
			$("WPO020ModelBatchNmCd").setValue(get["batch_nm_cd"]);
		}

		if (get["s_batch_no"] != null) {
			$("WPO020ModelSBatchNo").setValue(decodeURI(get["s_batch_no"]));
		}

		if (get["s_kanri_no"] != null) {
			$("WPO020ModelSKanriNo").setValue(decodeURI(get["s_kanri_no"]));
		}

		if (get["zone"] != null) {

			$("WPO020ModelZone").setValue(get["zone"]);
		}

		if (get["staff_nm"] != null) {
			$("WPO020ModelStaffNm").setValue(decodeURI(get["staff_nm"]));
		}

		if (get["kbn_status"] != null) {
			$("WPO020ModelKbnStatus").setValue(get["kbn_status"]);
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

					var flg = resultRows[i].getElementsBySelector(".hiddenData")[8].innerHTML;

					//何もなし、新規、更新フラグが立っている場合
					if (parseInt(flg,10) < 3) {

						setRowColor(flg,resultRows[i]);

					//削除フラグが立っている場合
					} else {

						setDeletedRowColor(resultRows[i])
					}
				}

			}
		}
	}

	//ラベルの切り替えと、該当画面のリロード
	var labels = $("toggle").getElementsBySelector("label");

	for (var i=0; i < labels.length; i++) {

		if (labels[i].hasClassName("selected")) {

			if (labels[i].getAttribute("id") == "jidoButton") {

				setInterval(reloading, 180000);

			}
		} else {

			labels[i].observe("click",clickReloadButton);
		}
	}
}

//リロードする際はmessageとreturn_cdのみ外す
function reloading() {

	var get = getRequest();
	var prams = "";

	for (var keyString in get) {
	　　
		if (keyString != "message" && keyString != "return_cd") {

			var value = get[keyString];

			if (value == null ) {
				value = "";
			}

			prams = prams + keyString + "=" + value + "&"
		}
	}

	location.href="/DCMS/WPO020/index?" + prams;
}

//トグル切り換えフラグ
var reloadButtonFlg = 0;

//画面の切り替え
function clickReloadButton(event) {

	//トグルの再切り替え防止
	if (reloadButtonFlg == 1) {

		return;
	}

	reloadButtonFlg = 1;

	//トグル切り替え
	var labels = $("toggle").getElementsBySelector("label");
	var selectedLabel = Event.element(event);


	for (var i=0; i < labels.length; i++) {

		if (labels[i] != selectedLabel) {

			labels[i].removeClassName("selected");

		} else {

			labels[i].addClassName("selected");
		}
	}

	//検索条件を持ったまま切り替え
	if ($("koshinButton").hasClassName("selected") ) {

		var get = getRequest();
		var prams = "";

		for (var keyString in get) {
		　　
			if (keyString != "message" && keyString != "return_cd") {

				var value = get[keyString];

				if (value == null ) {
					value = "";
				}

				prams = prams + keyString + "=" + value + "&"
			}
		}

		location.href="/DCMS/WPO020/update?" + prams;

	} else {

		var get = getRequest();
		var prams = "";

		for (var keyString in get) {
		　　
			if (keyString != "message" && keyString != "return_cd") {

				var value = get[keyString];

				if (value == null ) {
					value = "";
				}

				prams = prams + keyString + "=" + value + "&"
			}
		}

		location.href="/DCMS/WPO020/index?" + prams;

	}

	$("reloadingMessage").setStyle({
		display:"block"
	});

}
