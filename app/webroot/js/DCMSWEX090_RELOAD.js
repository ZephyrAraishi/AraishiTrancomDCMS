/**
 * 初期化処理
 */
function initReload() {

	// 実績参照押下時イベント
	$("monthlyButton").observe("click", clickMonthlyButton);
	$("dailyButton").observe("click", clickDailyButton);
	$("addButton").observe("click", clickAddButton);
	$("updateButton").observe("click", clickUpdateButton);
	$("jissekiButton").observe("click", clickJissekiButton);

	// カレンダーを設置
	if ($("type").value == "2") {
		new InputCalendar('from', {
			inputReadOnly: false,
			lang : 'ja',
			weekFirstDay : ProtoCalendar.SUNDAY,
			format : 'yyyymmdd'
		});
		new InputCalendar('to', {
			inputReadOnly: false,
			lang : 'ja',
			weekFirstDay : ProtoCalendar.SUNDAY,
			format : 'yyyymmdd'
		});
	} else {
		new InputMonthCalendar('from', {
			inputReadOnly: false,
			lang : 'ja',
			weekFirstDay : ProtoCalendar.SUNDAY,
			format : 'yyyymm'
		});
		new InputMonthCalendar('to', {
			inputReadOnly: false,
			lang : 'ja',
			weekFirstDay : ProtoCalendar.SUNDAY,
			format : 'yyyymm'
		});
	}

	//ゲットメッセージ取得
	var get = getRequest();

	//パラメータがある場合
	if (get.length != 0) {

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
	}

	if (get["return_cd"] != null) {
		var resultRows = $$("body")[0].getElementsBySelector(".tableRow");
		for (var i = 0 ; i < resultRows.length; i++) {
			var flg = resultRows[i].getElementsBySelector(".hiddenData")[0].innerHTML;
			var syuryoFlg = resultRows[i].getElementsBySelector(".hiddenData")[12].innerHTML;
			if (syuryoFlg == '1') {
				resultRows[i].setStyle({
					background : "#eeeeee"
				});
			} else if (parseInt(flg,10) < 3) {
				setRowColor(flg,resultRows[i]);
			} else {
				setDeletedRowColor(resultRows[i]);
			}
		}
	}
}

/**
 * 「月別」ボタン押下処理
 */
function clickMonthlyButton() {

	if ($("type").value == "2") {
		location.href="/DCMS/WEX090/index?type=1";
	}
}

/**
 * 「日別」ボタン押下処理
 */
function clickDailyButton() {

	if ($("type").value == "1") {
		location.href="/DCMS/WEX090/index?type=2";
	}
}

/**
 * 「実績参照」ボタン押下時処理
 */
function clickJissekiButton() {

	var url = "";
	var title = "";

	if ($("type").value == "1") {
		url = "/DCMS/WEX091/index";
		title = "月別作業実績参照";
	} else {
		url = "/DCMS/WEX093/index";
		title = "日別作業実績参照";
	}

	window.open(url, title);
}

/**
 * 「追加」ボタン押下時処理
 */
function clickAddButton() {

	openPopup(true);
}

/**
 * 月別・日別推移画面起動処理
 *
 * @param kznDataNo 改善データ番号
 */
function openSui(kznDataNo) {

	var target = "";
	var title = "";

	if ($("type").value == "1") {
		target = "WEX092";
		title = "月別推移";
	} else {
		target = "WEX094";
		title = "日別推移";
	}

	var url = "/DCMS/" + target + "/";
	url += "?no=" + kznDataNo;

	window.open(url, null);
}
