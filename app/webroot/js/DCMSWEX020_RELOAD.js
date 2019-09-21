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

					//工程
					var resultRows = $$("body")[0].getElementsBySelector(".tableRow");

					for(var i=0 ; i< resultRows.length; i++) {

						var flg = resultRows[i].getElementsBySelector(".hiddenData")[3].innerHTML;

						if (parseInt(flg,10) < 3) {

							setRowColor(flg,resultRows[i]);
						} else {

							setDeletedRowColor(resultRows[i])
						}
					}
			}
		}
	}
}
