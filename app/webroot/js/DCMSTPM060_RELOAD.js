//画面更新や描画を初期化する関数
function initReload() {

	//ゲットメッセージ取得
	var get = getRequest();

	//パラメータがある場合
	if (get.length != 0) {

		if (get["ymd_from"] != null) {
			$("ymd_from").setValue(get["ymd_from"]);
		}

		if (get["ymd_to"] != null) {
			$("ymd_to").setValue(decodeURI(get["ymd_to"]));
		}

		if (get["timeFrom"] != null) {
			$("timeFrom").value =decodeURI(get["timeFrom"]).replace('%3A',':');
		}

		if (get["timeTo"] != null) {
			$("timeTo").value =decodeURI(get["timeTo"]).replace('%3A',':');
		}

		if (get["ninzu"] != null) {

			if (get["ninzu"] == 'on') {
				$("ninzu").checked = true;
			} else {
				$("ninzu").checked = false;
			}

		}

		if (get["jikan"] != null) {

			if (get["jikan"] == 'on') {
				$("jikan").checked = true;
			} else {
				$("jikan").checked = false;
			}

		}

		if (get["kotei2"] != null) {

			$("kotei2").setValue(decodeURI(get["kotei2"]));

		}

		if (get["kotei"] != null) {

			var koteiCd = decodeURI(get["kotei"]);

			$("koteiCd").setValue(koteiCd);

			if (koteiCd != '') {

				var koteis = koteiCd.split("_");

				var koteiDai = null;
				var koteiCyu = null;
				var koteiSai = null;

				if (koteis.length >= 3) {

					var lis = $('ul_class').getElementsBySelector('li');

					// 工程(細)情報の表示をすべて非表示にする
					for (var i=0; i < lis.length; i++) {

						var liKotei = lis[i].readAttribute('value');

						if (liKotei == null) {
							continue;
						}

						if (liKotei.length < 3) {

							liKotei = ("00" + liKotei).slice(-3);
						}

						if (liKotei == (koteis[0] + "_" + koteis[1] + "_" + koteis[2]) ) {

							koteiSai = lis[i].getElementsBySelector("div")[0].innerHTML.trim();

							if (koteiSai == '»') {

								koteiSai = lis[i].getElementsBySelector("div")[1].innerHTML.trim();
							}

							break;
						}
					}

				}

				if (koteis.length >= 2) {

					var lis = $('ul_class').getElementsBySelector('li');

					// 工程(細)情報の表示をすべて非表示にする
					for (var i=0; i < lis.length; i++) {

						var liKotei = lis[i].readAttribute('value');

						if (liKotei == null) {
							continue;
						}

						if (liKotei.length < 3) {

							liKotei = ("00" + liKotei).slice(-3);
						}

						if (liKotei == (koteis[0] + "_" + koteis[1]) ) {

							koteiCyu = lis[i].getElementsBySelector("div")[0].innerHTML.trim();

							if (koteiCyu == '»') {

								koteiCyu = lis[i].getElementsBySelector("div")[1].innerHTML.trim();
							}

							break;
						}
					}

				}

				if (koteis.length >= 1) {

					var lis = $('ul_class').getElementsBySelector('li');

					// 工程(細)情報の表示をすべて非表示にする
					for (var i=0; i < lis.length; i++) {

						var liKotei = lis[i].readAttribute('value');

						if (liKotei == null) {
							continue;
						}

						if (liKotei.length < 3) {

							liKotei = ("00" + liKotei).slice(-3);
						}

						if (liKotei == koteis[0] ) {

							koteiDai = lis[i].getElementsBySelector("div")[0].innerHTML.trim();

							if (koteiDai == '»') {

								koteiDai = lis[i].getElementsBySelector("div")[1].innerHTML.trim();
							}

							break;
						}
					}

				}

				if (koteiDai != null && koteiCyu != null && koteiSai != null) {

					$("koteiText").setValue(koteiDai + ' > ' + koteiCyu + ' > ' + koteiSai);

				} else if (koteiDai != null && koteiCyu != null) {

					$("koteiText").setValue(koteiDai + ' > ' + koteiCyu);

				} else if (koteiDai != null) {

					$("koteiText").setValue(koteiDai);
				}

			}
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

					var flg = resultRows[i].getElementsBySelector(".hiddenData")[0].innerHTML;

					//何もなし、更新フラグが立っている場合
					if (parseInt(flg,10) < 3) {
						setRowColor(flg,resultRows[i]);
					}
				}
			}
		}
	}

}



