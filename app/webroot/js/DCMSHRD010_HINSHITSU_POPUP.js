//選択行
var selectedRow;

// 行の値保持
var originKoteiCd = "";

var actionUrl = "";

var dbclickFlag;// ダブルクリック防止フラグ
var areaCoverElement = null;// エリアロックのDIV
var popUpView = null;

/**
 * 初期化
 *
 * @param url
 */
function initPopUp(url) {

	actionUrl = url;

	// 該当行にイベントを付加
	$$("#lstTable .tableRow").each(function(rowElem) {
		var changed = rowElem.getElementsBySelector(".hiddenData.CHANGED")[0].innerHTML;
		if (parseInt(changed.trim(), 10) < 3) {
			rowElem.observe("click", clickPopUp);
			rowElem.observe("mouseover", overRow);
			rowElem.observe("mouseout", leaveRow);

		} else {
			rowElem.observe("click", clickDeletedRow);
		}
	});
}

/**
 * テーブルのオーバーイベントハンドラ フォーカスマークをつける
 *
 * @param event
 */
function overRow(event) {
	var row = Event.element(event).up("div");

	if (row.hasClassName("tableRow") == false) {
		while (true) {
			row = row.up("div");
			if (row.hasClassName("tableRow") == true) {
				break;
			}
		}
	}

	if (row == null) {
		return;
	}

	setSelectedRowColor(row);

}

/**
 * テーブルのリーブイベントハンドラ フォーカスマークを外す
 */
function leaveRow(event) {
	var row = Event.element(event).up("div");

	if (row.hasClassName("tableRow") == false) {
		while (true) {
			row = row.up("div");
			if (row.hasClassName("tableRow") == true) {
				break;
			}
		}
	}

	if (row == null) {
		return;
	}

	var changed = row.getElementsBySelector(".hiddenData.CHANGED");

	// 更新の場合はそのまま
	setRowColor(changed[0].innerHTML, row);
}

/**
 * テーブル行クリックイベントハンドラ
 *
 * @param event
 */
function clickPopUp(event) {
	// 右クリックだったら終了
	if (event.isRightClick()) {
		return;
	}

	// ダブルクリックだったら終了
	if (dbclickFlag == 1) {
		dbclickFlag = 0;
		return;
	}

	dbclickFlag = 1;

	// 端末遅い人用に、ポップアップがでるまでのカバーをつける
	areaCoverElement = createAreaCoverElement();
	$$("body")[0].insert(areaCoverElement);

	// 選択行
	var row = Event.element(event).up("div");
	if (row.hasClassName("tableRow") == false) {
		while (true) {
			row = row.up("div");
			if (row.hasClassName("tableRow") == true) {
				break;
			}
		}
	}
	selectedRow = row;

	var staffCode = $$(".hiddenData.STAFF_CODE")[0].innerHTML;
	// ポップアップを読み込む
	new Ajax.Request(actionUrl, {
		method : 'post',

		onSuccess : viewPopUp,

		onFailure : function(event) {
			location.href = "/DCMS/HRD010/hinshitsu?staff_cd=" + staffCode + "&return_cd=91&message=" + Base64.encode(DCMSMessage.format("CMNE000101"));
		},
		onException : function(event, ex) {
			alert(ex);
			location.href = "/DCMS/HRD010/hinshitsu?staff_cd=" + staffCode + "&return_cd=92&message=" + Base64.encode(DCMSMessage.format("CMNE000102"));
		}
	});
}

/**
 * ポップアップ描画処理
 *
 * @param event
 */
function viewPopUp(event) {

	// 画面に配置
	var popup = Dialog.confirm(event.responseText, {
		id : "popup",
		className : "alphacube",
		title : "スタッフ品質情報設定",
		width : 600,
		height : 340,
		draggable : true,
		destroyOnclose : true,
		recenterAuto : false,
		buttonClass : "buttonsHRD01001",
		okLabel : "更新",
		cancelLabel : "キャンセル",
		onOk : clickOKButton,
		onCancel : clickCANCELButton,
		showEffectOptions : {
			duration : 0.2
		},
		hideEffectOptions : {
			duration : 0
		}
	});

	popup.setZIndex(1000);

	popup.content.setStyle({
		overflow : "visible"
	});

	//分類を設定
	initBunruiPopup();
	
	popUpView = popup;

	// 削除付加
	var deleteButton = new Element("input");
	deleteButton.writeAttribute({
		type : "button",
		value : "削除"
	});
	deleteButton.addClassName("buttonsHRD01001");
	
	$$(".alphacube_buttons")[0].insert({
		top : deleteButton
	});

	deleteButton.observe("click", clickDeleteButton);
	
	// カレンダーを設置
	new InputCalendar('hassei_date', {
		lang : 'ja',
		startYear : 1900,
		weekFirstDay : ProtoCalendar.SUNDAY,
		format : 'yyyymmdd'
	});

	// 初期値設定
	var cells = selectedRow.getElementsBySelector(".tableCell");

	$("hassei_date").value = cells[1].innerHTML.replace(/\//g, "");
	$("kbn_hinshitsu_naiyo").value = cells[4].innerHTML;
	var dai_bunrui_nm = cells[5].innerHTML;
	var chu_bunrui_nm = cells[7].innerHTML;
	var sai_bunrui_nm = cells[9].innerHTML;
	var koteiText = dai_bunrui_nm;
	koteiText += chu_bunrui_nm == "" ? "" : " > " + chu_bunrui_nm;
	koteiText += sai_bunrui_nm == "" ? "" : " > " + sai_bunrui_nm;
	$("koteiText").value = koteiText;
	var dai_bunrui_cd = cells[6].innerHTML;
	var chu_bunrui_cd = cells[8].innerHTML;
	var sai_bunrui_cd = cells[10].innerHTML;
	$("koteiCd").value = dai_bunrui_cd + "_" + chu_bunrui_cd + "_" + sai_bunrui_cd;
	$("s_kanri_no").value = cells[11].innerHTML;
	$("staff_nm").value = cells[12].innerHTML.unescapeHTML();
	$("t_kin").value = cells[13].innerHTML.replace(/,/g, "");
	$("t_hoho").value = cells[14].innerHTML.unescapeHTML();
	
	// 値を保持
	// clickOKButtonで使用するため
	originKoteiCd = $F("koteiCd");

}

/**
 * 更新ボタンクリックイベントハンドラ
 */
function clickOKButton(window) {
	// DCMSHRD010_HINSHITSU_ADDROW.js の validateメソッドを使用
	if (!DCMSHRD010_HINSHITSU.validate()) {
		// バリデーションエラーのため処理終了
		return;
	}

	var cells = selectedRow.getElementsBySelector(".tableCell");
	// 画面更新
	// 入力前と変化した場合は色をつける

	/**
	 * 変更行の背景色を変更する
	 */
	var updateRowColor = function() {
		// 新規の場合はそのまま
		var changed = cells[15].innerHTML;
		if (changed == "0") {
			changed = "1";
			cells[15].innerHTML = changed;
		}
		// 行のbackground変更
		setRowColor(changed, selectedRow);
	};

	// 発生日付
	var inputValue = formatDate($F("hassei_date"));
	if (inputValue != cells[1].innerHTML) {
		// 変更項目の背景色を変更
		setUpdatedCellColor(cells[1]);
		// 入力値を反映
		cells[1].innerHTML = inputValue;
		// 変更行の背景色を変更
		updateRowColor();
	}

	// 品質内容
	inputValue = $F("kbn_hinshitsu_naiyo");
	if (inputValue != cells[4].innerHTML) {
		setUpdatedCellColor(cells[3]);
		var kbn_hinshitsu_naiyo_nm = "";
		var kbn_hinshitsu_naiyo_cd = "";
		// プルダウンのlabelとvalueを取得
		$("kbn_hinshitsu_naiyo").getElementsBySelector("option").each(function(elem) {
			if (elem.selected) {
				kbn_hinshitsu_naiyo_nm = elem.innerHTML;
				kbn_hinshitsu_naiyo_cd = elem.value;
			}
		});
		cells[3].innerHTML = kbn_hinshitsu_naiyo_nm;
		cells[4].innerHTML = kbn_hinshitsu_naiyo_cd;
		updateRowColor();
	}

	// 担当業務 工程
	inputValue = $F("koteiCd");
	if (inputValue != originKoteiCd) {
		var koteiName = $F("koteiText").split(" > ");
		var koteiCd = $F("koteiCd").split("_");
		// 大分類
		if (koteiCd[0] == cells[6].innerHTML) {
			// 中分類
			if (koteiCd[1] == cells[8].innerHTML) {
				// 細分類
				if (koteiCd[2] != cells[10].innerHTML) {
					// 細分類のみ変更
					setUpdatedCellColor(cells[9]);
					cells[9].innerHTML = koteiName[2];
					cells[10].innerHTML = koteiCd[2];
				}

			} else {
				// 中分類が変わっていたら、中と小を変更
				setUpdatedCellColor(cells[7]);
				cells[7].innerHTML = koteiName[1];
				cells[8].innerHTML = koteiCd[1];
				setUpdatedCellColor(cells[9]);
				cells[9].innerHTML = koteiName[2];
				cells[10].innerHTML = koteiCd[2];
			}

		} else {
			// 大分類が変わっていたら、全て変更
			setUpdatedCellColor(cells[5]);
			cells[5].innerHTML = koteiName[0];
			cells[6].innerHTML = koteiCd[0];
			setUpdatedCellColor(cells[7]);
			cells[7].innerHTML = koteiName[1];
			cells[8].innerHTML = koteiCd[1];
			setUpdatedCellColor(cells[9]);
			cells[9].innerHTML = koteiName[2];
			cells[10].innerHTML = koteiCd[2];

		}
		updateRowColor();
	}

	// 集計管理No.
	inputValue = $F("s_kanri_no");
	if (inputValue != cells[11].innerHTML) {
		setUpdatedCellColor(cells[11]);
		cells[11].innerHTML = inputValue;
		updateRowColor();
	}

	// 対応者
	inputValue = $F("staff_nm").escapeHTML();
	if (inputValue != cells[12].innerHTML) {
		setUpdatedCellColor(cells[12]);
		cells[12].innerHTML = inputValue.escapeHTML();
		updateRowColor();
	}

	// 対応者金額
	inputValue = num3Format($F("t_kin"));
	if (inputValue != cells[13].innerHTML) {
		setUpdatedCellColor(cells[13]);
		cells[13].innerHTML = inputValue;
		updateRowColor();
	}

	// 対応方法
	inputValue = $F("t_hoho").escapeHTML();
	if (inputValue != cells[14].innerHTML) {
		setUpdatedCellColor(cells[14]);
		cells[14].innerHTML = inputValue;
		updateRowColor();
	}

	window.close();
	dbclickFlag = 0;
	areaCoverElement.remove();
}

/**
 * 削除ボタンクリックイベントハンドラ
 *
 * @param event
 */
function clickDeleteButton(event) {

	// 削除カラーにする
	setDeletedRowColor(selectedRow);

	var cells = selectedRow.getElementsBySelector(".tableCell");
	for ( var i = 0; i < cells.length; i++) {
		cells[i].setStyle({
			background : "inherit"
		});
	}

	selectedRow.stopObserving("click");
	selectedRow.stopObserving("mouseover");
	selectedRow.stopObserving("mouseout");
	selectedRow.observe("click", clickDeletedRow);

	// 何もない場合
	var changed = cells[15];
	if (changed.innerHTML == "0") {
		cells[15].innerHTML = "3";

		// 更新の場合
	} else if (changed.innerHTML == "1") {
		cells[15].innerHTML = "4";

		// 新規の場合
	} else if (changed.innerHTML == "2") {
		cells[15].innerHTML = "5";

	}

	popUpView.close();
	dbclickFlag = 0;
	areaCoverElement.remove();
}

/**
 * 削除解除ダイアログ表示
 */
function clickDeletedRow(event) {

	// 右クリックだったら終了
	if (event.isRightClick()) {
		return;
	}

	// ダブルクリックだったら終了
	if (dbclickFlag == 1) {
		dbclickFlag = 0;
		return;
	}

	dbclickFlag = 1;

	// 端末遅い人用に、ポップアップがでるまでのカバーをつける
	areaCoverElement = createAreaCoverElement();
	$$("body")[0].insert(areaCoverElement);

	// 選択行
	var row = Event.element(event).up("div");
	if (row.hasClassName("tableRow") == false) {
		while (true) {
			row = row.up("div");
			if (row.hasClassName("tableRow") == true) {
				break;
			}
		}
	}

	selectedRow = row;

	// 画面に配置
	var popup = Dialog.confirm("<br>削除を解除しますか？", {
		id : "popup",
		className : "alphacube",
		title : "スタッフ品質情報設定",
		width : 400,
		height : 100,
		draggable : true,
		destroyOnclose : true,
		recenterAuto : false,
		buttonClass : "buttonsHRD01001",
		okLabel : "解除",
		cancelLabel : "キャンセル",
		onOk : clickOKReverseButton,
		onCancel : clickCANCELButton,
		showEffectOptions : {
			duration : 0.2
		},
		hideEffectOptions : {
			duration : 0
		}
	});

	popup.setZIndex(1000);
}

/**
 * 削除解除ダイアログの解除ボタンクリックイベントハンドラ
 *
 * @param window
 */
function clickOKReverseButton(window) {

	var cells = selectedRow.getElementsBySelector(".tableCell");

	selectedRow.setStyle({
		background : "white"
	});

	selectedRow.stopObserving("click");
	selectedRow.observe("click", clickPopUp);
	selectedRow.observe("mouseover", overRow);
	selectedRow.observe("mouseout", leaveRow);

	// 何もない場合
	var changed = cells[15];
	if (changed.innerHTML == "3") {
		cells[15].innerHTML = "0";

		// 更新の場合
	} else if (changed.innerHTML == "4") {
		cells[15].innerHTML = "1";

		// 新規の場合
	} else if (changed.innerHTML == "5") {
		cells[15].innerHTML = "2";
	}

	// 新規の場合はそのまま
	setRowColor(cells[15].innerHTML, selectedRow);

	window.close();
	dbclickFlag = 0;
	areaCoverElement.remove();
}

/**
 * 削除解除ダイアログのキャンセルボタンクリックイベントハンドラ
 *
 * @param windows
 */
function clickCANCELButton(windows) {
	window.close();
	dbclickFlag = 0;
	areaCoverElement.remove();
}

