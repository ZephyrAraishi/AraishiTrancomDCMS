/**
 * 初期処理
 */
function initReload() {
	// 値復元
	WEX091.restoreView();

	// イベント付加
	WEX091.addEvent();
	

}

/**
 * WEX091の処理をまとめたクラス
 */
var WEX091 = new function() {

	/*---------- public ----------*/

	/**
	 * 画面の値をリクエスト時の値で復元する
	 */
	this.restoreView = function() {
		//ゲットメッセージ取得
		var get = getRequest();

		//パラメータがある場合
		if (get.length != 0) {
			for ( var paramName in get) {
				if (paramName == "kotei") {
					restoreKotei(get);
				} else {
					var targetObj = $(paramName);
					if (targetObj != null) {
						targetObj.setValue(get[paramName]);
					}
				}
			}
		}
	};

	/**
	 * 画面の部品にイベントリスナーを付加する
	 */
	this.addEvent = function() {
		$$("#WEX091ModelIndexForm input[type='submit']").each(function(elem) {
			elem.observe("click", setActionName);
		});
	};

	/*---------- private ----------*/

	/**
	 * 押されたボタンのidをアクション名に設定
	 */
	function setActionName(event) {
		$("actionName").setValue(Event.element(event).id);
		$("pageID").setValue(1);
	}

	/**
	 * 工程の値をリクエストの時の値で復元する
	 */
	function restoreKotei(get) {

		if (get["kotei"] == null) {
			return;
		}

		var koteiCd = decodeURI(get["kotei"]);
		$("koteiCd").setValue(koteiCd);

		if (koteiCd == '') {
			return;
		}

		var koteis = koteiCd.split("_");
		var koteiDai = null;
		var koteiCyu = null;
		var koteiSai = null;

		if (koteis.length >= 3) {
			var lis = $('ul_class').getElementsBySelector('li');

			// 工程(細)情報の表示をすべて非表示にする
			for ( var i = 0; i < lis.length; i++) {
				var liKotei = lis[i].readAttribute('value');

				if (liKotei == null) {
					continue;
				}

				if (liKotei.length < 3) {
					liKotei = ("00" + liKotei).slice(-3);
				}

				if (liKotei == (koteis[0] + "_" + koteis[1] + "_" + koteis[2])) {
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
			for ( var i = 0; i < lis.length; i++) {
				var liKotei = lis[i].readAttribute('value');

				if (liKotei == null) {
					continue;
				}

				if (liKotei.length < 3) {
					liKotei = ("00" + liKotei).slice(-3);
				}

				if (liKotei == (koteis[0] + "_" + koteis[1])) {
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
			for ( var i = 0; i < lis.length; i++) {
				var liKotei = lis[i].readAttribute('value');

				if (liKotei == null) {
					continue;
				}

				if (liKotei.length < 3) {
					liKotei = ("00" + liKotei).slice(-3);
				}

				if (liKotei == koteis[0]) {
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
};
