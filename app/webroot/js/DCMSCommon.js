/**
 * リクエストパラメータの取得
 *
 * @returns リクエストパラメータのオブジェクト
 */
function getRequest() {

	if (location.search.length > 1) {
		var get = new Object();
		var ret = location.search.substr(1).split("&");
		for ( var i = 0; i < ret.length; i++) {
			var r = ret[i].split("=");
			get[r[0]] = r[1];
		}
		return get;
	} else {
		return false;
	}
}

/**
 * 全体を隠すカーテンを作成して返却します。
 *
 * @returns 全体を隠すカーテン
 */
function createUpdatingCurtain() {
	// 全体を隠すカーテンをつける
	var screen = new Object();

	screen.width = document.body.clientWidth
			|| document.documentElement.clientWidth; // 横幅
	screen.nowHeight = document.documentElement.clientHeight; // 現在表示している画面の高さ
	screen.height = document.body.clientHeight || document.body.scrollHeight; // 画面の高さ
	screen.x = document.body.scrollLeft || document.documentElement.scrollLeft; // 横の移動量
	screen.y = document.body.scrollTop || document.documentElement.scrollTop;

	var updatingCurtain = new Element('div');
	updatingCurtain.addClassName("updateCurtain");
	var height = Math.max.apply(null, [ document.body.clientHeight,
			document.body.scrollHeight, document.documentElement.scrollHeight,
			document.documentElement.clientHeight ]);

	updatingCurtain.setStyle({
		height : height + "px"
	});

	var div = new Element('div');
	div.setStyle({
		display : "block",
		position : "absolute",
		width : "50px",
		height : "50px"
	});

	var div2 = new Element('div');
	div2.setStyle({
		display : "block",
		position : "absolute",
		width : "400px",
		height : "30px",
		fontSize : "15pt",
		color : "white",
		textAlign : "center"
	});
	div2.insert("更新中です。しばらくお待ちください。");

	var image = new Element('img');
	image.writeAttribute({
		display : "block",
		position : "absolute",
		src : "/DCMS/img/spinner.gif"
	});

	div.insert(image);

	div.style.left = (screen.width / 2) - 50 / 2 + 'px';
	div.style.top = (screen.nowHeight / 2 + screen.y) - 50 / 2 + 30 + 'px';

	div2.style.left = (screen.width / 2) - 400 / 2 + 'px';
	div2.style.top = (screen.nowHeight / 2 + screen.y) - 30 / 2 - 20 + 'px';

	updatingCurtain.insert(div);
	updatingCurtain.insert(div2);

	return updatingCurtain;
}

/**
 * 端末が遅い人用のポップアップがでるまでのカバーを作成する。
 *
 * @returns {Element} カバー用エレメント
 */
function createAreaCoverElement() {
	// 端末遅い人用に、ポップアップがでるまでのカバーをつける
	var acelement = new Element("div");
	var height = Math.max.apply(null, [ document.body.clientHeight,
			document.body.scrollHeight, document.documentElement.scrollHeight,
			document.documentElement.clientHeight ]);

	acelement.setStyle({
		height : height + "px"
	});
	acelement.addClassName("displayCover");
	return acelement;
}

/**
 * 通常行の色設定
 *
 * @param objRow
 */
function setNormalRowColor(objRow) {
	objRow.setStyle({
		background : "white"
	});
}

/**
 * 選択行の色設定
 *
 * @param objRow
 */
function setSelectedRowColor(objRow) {
	objRow.setStyle({
		background : "#ffff7f"
	});
}

/**
 * 更新行の色設定
 *
 * @param objRow
 */
function setUpdatedRowColor(objRow) {
	objRow.setStyle({
		background : "#90ee90"
	});
}

/**
 * 追加行の色設定
 *
 * @param objRow
 */
function setAddedRowColor(objRow) {
	objRow.setStyle({
		background : "#b0c4de"
	});
}

/**
 * 削除行の色設定
 *
 * @param objRow
 */
function setDeletedRowColor(objRow) {
	objRow.setStyle({
		background : "gray"
	});
}

/**
 * 行の背景色を設定
 */
function setRowColor(flg, objRow) {
	// 背景色設定
	if (flg == "0") {
		// 更新なし
		setNormalRowColor(objRow);
	} else if (flg == "1") {
		// 更新
		setUpdatedRowColor(objRow);
	} else if (flg == "2") {
		// 追加
		setAddedRowColor(objRow);
	}
}

/**
 * 更新セルのチェック
 *
 * valとorgValが一致しない場合に背景色を設定します。
 *
 * @param val
 *            値
 * @param orgVal
 *            元の値
 * @param objCell
 *            セルオブジェクト
 */
function setCellColor(val, orgVal, objCell) {
	if (val != orgVal) {
		setUpdatedCellColor(objCell);
	}
}

/**
 * 更新セルの色設定
 *
 * @param objCell
 */
function setUpdatedCellColor(objCell) {
	objCell.setStyle({
		backgroundColor : "#bfff7f"
	});
}

Date.prototype.addHours = function(h) {
	this.setHours(this.getHours() + h);
	return this;
}

Date.prototype.addMinutes = function(m) {
	this.setMinutes(this.getMinutes() + m);
	return this;
}

// 基準日からの日にちに直す
function reverseDateString(baseTime, hours, minutes) {

	var baseDate = new Date(baseTime.replace(/-/g, "/") + " 00:00:00");

	var tempDate = baseDate.addHours(hours);
	tempDate = tempDate.addMinutes(minutes);

	var dateStr = ("0" + tempDate.getFullYear()).slice(-4) + "-"
			+ ("0" + (tempDate.getMonth() + 1)).slice(-2) + "-"
			+ ("0" + tempDate.getDate()).slice(-2) + " "
			+ ("0" + tempDate.getHours()).slice(-2) + ":"
			+ ("0" + tempDate.getMinutes()).slice(-2) + ":00";

	return dateStr;
}
// カンマを削除します。
function removeComma(val) {
	return new String(val).replace(/,/g, "");
}

// PHPの予約語を％表記にします
function escapePHP(val) {

	val = val.replace(/\+/g, "%2B");

	return val;
}

/**
 * yyyyMMdd を yyyy/MM/dd にする
 */
function formatDate(dateStr) {
	// 正規表現による書式チェック
	if (!dateStr.match(/^\d{4}\d{2}\d{2}$/)) {
		return dateStr;
	}
	var vYear = dateStr.substr(0, 4);
	var vMonth = dateStr.substr(4, 2); // Javascriptは、0-11で表現
	var vDay = dateStr.substr(6, 2);

	return vYear + "/" + vMonth + "/" + vDay;

}

/**
 * yyyyMM を yyyy/MM にする
 */
function formatYm(dateStr) {
	// 正規表現による書式チェック
	if (!dateStr.match(/^\d{4}\d{2}$/)) {
		return dateStr;
	}
	var vYear = dateStr.substr(0, 4);
	var vMonth = dateStr.substr(4, 2);

	return vYear + "/" + vMonth;

}

/**
 * 年月日と加算日からn日後、n日前を求める関数
 * year 年
 * month 月
 * day 日
 * addDays 加算日。マイナス指定でn日前も設定可能
 */
function computeDate (year, month, day, addDays) {
    var dt = new Date(year, month - 1, day);
    var baseSec = dt.getTime();
    var addSec = addDays * 86400000;//日数 * 1日のミリ秒数
    var targetSec = baseSec + addSec;
    dt.setTime(targetSec);
    return dt;
}

/**
 * 数字を3桁ごとにカンマ区切りにする
 * @param str
 * @returns
 */
function num3Format(str) {
	if (str == "") {
		return str;
	}

	var temp1 = (new String(str)).split(".")[0].match(/./g).reverse().join("");
	temp1 = temp1.replace(/(\d{3})/g, "$1,");
	temp1 = temp1.match(/./g).reverse().join("").replace(/^,/, "");
	if (!!(new String(str)).split(".")[1]) {
		temp1 = temp1 + "." + str.split(".")[1];
	}
	return temp1;
}