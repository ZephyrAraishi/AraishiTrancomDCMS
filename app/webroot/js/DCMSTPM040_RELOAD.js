//画面更新や描画を初期化する関数
function initReload() {

	//ゲットメッセージ取得
	var get = getRequest();

	if (get.length != 0) {

		if (get["return_cd"] != null) {

			if (get["return_cd"] == "0") {

				$("infoMsg").update(Base64.decode(get["message"]));
			}  else {

				$("errorMsg").update(Base64.decode(get["message"]));
			}
		}
	}

	//ラベルの切り替えと、外とう画面のリロード
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

function reloading() {

	location.href="/DCMS/TPM040/index";
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

	$("reloadingMessage").setStyle({
		display:"block"
	});

}
