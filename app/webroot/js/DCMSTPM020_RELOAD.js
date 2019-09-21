var refreshIntervalId;


function initReload() {

	//更新メッセージ
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

	//ラベルの切り替えと、該当画面のリロード
	var labels = $("toggle").getElementsBySelector("label");

	for (var i=0; i < labels.length; i++) {

		if (labels[i].hasClassName("selected")) {

			if (labels[i].getAttribute("id") == "jidoButton") {

				startReload();

			}
		} else {

			labels[i].observe("click",clickReloadButton);
		}
	}
}

function startReload() {

	refreshIntervalId = setInterval(reloading, 180000);
}

function reloading() {

	//updateを外す
	var get = getRequest();
	var prams = "";

	for (var keyString in get) {
	　　
		if (keyString != "return_cd" && keyString != "message") {

			var value = get[keyString];

			if (value == null ) {
				value = "";
			}

			prams = prams + keyString + "=" + value + "&"
		}
	}

	location.href="/DCMS/TPM020/index?" + prams;
}

var reloadButtonFlg = 0;

function clickReloadButton(event) {

	if (reloadButtonFlg == 1) {

		return;
	}

	//トグル切り替え
	var labels = $("toggle").getElementsBySelector("label");
	var selectedLabel = Event.element(event);

	if (!selectedLabel.hasClassName("selected")) {

		reloadButtonFlg = 1;
	}

	for (var i=0; i < labels.length; i++) {

		if (labels[i] != selectedLabel) {

			labels[i].removeClassName("selected");

		} else {

			labels[i].addClassName("selected");
		}
	}

	//検索条件を持ったまま切り替え
	if ($("koshinButton").hasClassName("selected") ) {

		//updateを外す
		var get = getRequest();
		var prams = "";

		for (var keyString in get) {
		　　
			if (keyString != "return_cd" && keyString != "message") {

				var value = get[keyString];

				if (value == null ) {
					value = "";
				}

				prams = prams + keyString + "=" + value + "&"
			}
		}

		location.href="/DCMS/TPM020/update?" + prams;

	} else {

		//updateを外す
		var get = getRequest();
		var prams = "";

		for (var keyString in get) {
		　　
			if (keyString != "return_cd" && keyString != "message") {

				var value = get[keyString];

				if (value == null ) {
					value = "";
				}

				prams = prams + keyString + "=" + value + "&"
			}
		}

		location.href="/DCMS/TPM020/index?" + prams;

	}

	$("reloadingMessage").setStyle({
		display:"block"
	});

}
