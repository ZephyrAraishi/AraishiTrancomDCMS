//選択行
var selectedRow;


//行の値保持
var originValSuryo = "";
var originValHiyou = "";
var originValExp = "";

var actionUrl = "";

var dbclickFlag;//ダブルクリック防止フラグ
var areaCoverElement = null;
var popUpView = null;

//初期化
function initPopUp(url) {

	actionUrl = url;

	var rows = $("lstTable").getElementsBySelector(".tableRow");

    for(i=0; i < rows.length; i++)
	{
		rows[i].observe("click",clickPopUp);
		rows[i].observe("mouseover",overRow);
		rows[i].observe("mouseout",leaveRow);
	}

}

//テーブルのオーバーイベントハンドラ
//フォーカスマークをつける
function overRow(event) {

	var row = Event.element(event).up("div");

	if (row.hasClassName("tableRow") == false) {

		while (true) {

			row = row.up("div");

			if (row.hasClassName("tableRow") == true) {

				break
			}
		}
	}

	if (row == null)
	{
		return;
	}
	setSelectedRowColor(row);

}

//テーブルのリーブイベントハンドラ
//フォーカスマークを外す
function leaveRow(event) {

	var row = Event.element(event).up("div");

	if (row.hasClassName("tableRow") == false) {

		while (true) {

			row = row.up("div");

			if (row.hasClassName("tableRow") == true) {

				break
			}
		}
	}

	if (row == null)
	{
		return;
	}

	var data = row.getElementsBySelector(".hiddenData");

	//背景色設定
	var flg = data[3].innerHTML;
	if (flg < 3) {
		setRowColor(flg, row);
	} else {
		setDeletedRowColor(row);
	}
}

//テーブル行クリックイベントハンドラ
function clickPopUp(event) {

	//右クリックだったら終了
	if (event.isRightClick()) {

		return;
	}

	//ダブルクリックだったら終了
	if (dbclickFlag == 1) {
		dbclickFlag = 0;
		return;
	}

	dbclickFlag = 1;

	//端末遅い人用に、ポップアップがでるまでのカバーをつける
	areaCoverElement = createAreaCoverElement();
	$$("body")[0].insert(areaCoverElement);

	//選択行
	var row = Event.element(event).up("div");

	if (row.hasClassName("tableRow") == false) {

		while (true) {

			row = row.up("div");

			if (row.hasClassName("tableRow") == true) {

				break
			}
		}
	}

	selectedRow = row;

	//ポップアップを読み込む
	new Ajax.Request( actionUrl, {
	  method : 'post',

	  onSuccess : viewPopUp,

	  onFailure : function( event )  {
	    alert(DCMSMessage.format("CMNE000101"));
	  },
	  onException : function( event, ex )  {
	    alert(DCMSMessage.format("CMNE000102") + "\n" + ex);
	  }
	});

}

/* ポップアップ */
function viewPopUp(event) {

	//画面に配置
	var popup = Dialog.confirm(event.responseText,
				   {
					id: "popup",
					className: "alphacube",
					title: "荷主契約情報設定",
					width:500,
					height:300,
					draggable: true,
					destroyOnclose: true,
					recenterAuto: false,
					buttonClass : "buttons",
					okLabel: "更新",
					cancelLabel:"キャンセル",
					onOk: clickOKButton,
					onCancel: clickCANCELButton,
					showEffectOptions: {duration:0.2},
					hideEffectOptions: {duration:0}
				   });

	popup.setZIndex(1000);

	popup.content.setStyle({overflow:"visible"});

	popUpView = popup

	//キャンセルボタン負荷
	var deleteButton = new Element("input");
	deleteButton.writeAttribute({
			type : "button",
			value : "削除"
	});
	deleteButton.addClassName("buttonsWEX03001");

	$$(".alphacube_buttons")[0].insert({
		top: deleteButton
	});

	deleteButton.observe("click",clickDeleteButton);

	//初期値設定
	var cells = selectedRow.getElementsBySelector(".tableCell");
	var data = selectedRow.getElementsBySelector(".hiddenData");

	//値を設置
	$("bango").update(cells[0].innerHTML);			// 行番号
	$("bnri_dai_ryaku").update(cells[1].innerHTML);	// 大分類略
	if (cells[2].innerHTML != "") {
		$("bnri_cyu_ryaku").update(" > " + cells[2].innerHTML);	// 中分類略
	}
	$("kbn_tani_nm").update(cells[3].innerHTML);	// 単位

	var suryo = removeComma(cells[4].innerHTML);
	var hiyou = removeComma(cells[5].innerHTML);
	var exp = cells[6].innerHTML;

	$("suryo").value = suryo;			// 数量
	$("hiyou").value = hiyou;			// 費用
	$("exp").value = exp;			// 詳細

	//値を保持
	originValSuryo = suryo;
	originValHiyou = hiyou;
	originValExp = exp;

}

//更新ボタンクリックイベントハンドラ
function clickOKButton(window) {

	// 入力チェック
	if (!DCMSValidation.notEmpty($F("suryo"))) {
		alert(DCMSMessage.format("CMNE000001", "数量"));
		return;
	}
	if (!DCMSValidation.numeric($F("suryo"))) {
		alert(DCMSMessage.format("CMNE000002", "数量"));
		return;
	} else if(!DCMSValidation.maxLength($F("suryo"), 5)) {
		alert(DCMSMessage.format("CMNE000003", "数量", "5"));
		return;
	}

	if (!DCMSValidation.notEmpty($F("hiyou"))) {
		alert(DCMSMessage.format("CMNE000001", "費用"));
		return;
	}
	if(!DCMSValidation.decLength($F("hiyou"), 12, 2)) {
		alert(DCMSMessage.format("CMNE000005", "費用", "12", "2"));
		return;
	}

	var cells = selectedRow.getElementsBySelector(".tableCell");
	var data = selectedRow.getElementsBySelector(".hiddenData");

	cells[4].innerHTML = num3Format($F("suryo"));
	cells[5].innerHTML = num3Format($F("hiyou"));
	cells[6].innerHTML = $F("exp");

	// 各セルの背景色
	setCellColor($F("suryo"), originValSuryo, cells[4]);
	setCellColor($F("hiyou"), originValHiyou, cells[5]);
	setCellColor($F("exp"), originValExp, cells[6]);

	if($F("suryo") != originValSuryo || $F("hiyou") != originValHiyou || $F("exp") != originValExp) {
		data[3].innerHTML = "1";
		setRowColor(data[3].innerHTML, selectedRow);
	}

	window.close();
	dbclickFlag = 0;
	areaCoverElement.remove();
}

//キャンセルボタンクリックイベントハンドラ
function clickCANCELButton(window) {

	window.close();
	dbclickFlag = 0;
	areaCoverElement.remove();
}


//デリートした行を元に戻す
function clickDeletedRow(event) {

	//右クリックだったら終了
	if (event.isRightClick()) {

		return;
	}

	//ダブルクリックだったら終了
	if (dbclickFlag == 1) {
		dbclickFlag = 0;
		return;
	}



	dbclickFlag = 1;

	//端末遅い人用に、ポップアップがでるまでのカバーをつける
	areaCoverElement = createAreaCoverElement();
	$$("body")[0].insert(areaCoverElement);


	//選択行
	var row = Event.element(event).up("div");

	if (row.hasClassName("tableRow") == false) {

		while (true) {

			row = row.up("div");

			if (row.hasClassName("tableRow") == true) {

				break
			}
		}
	}

	selectedRow = row;

	//画面に配置
	var popup = Dialog.confirm("<br>削除を解除しますか？",
				   {
					id: "popup",
					className: "alphacube",
					title: "荷主契約情報",
					width:400,
					height:100,
					draggable: true,
					destroyOnclose: true,
					recenterAuto: false,
					buttonClass : "buttonsWEX03001",
					okLabel: "解除",
					cancelLabel:"キャンセル",
					onOk: clickOKReverseButton,
					onCancel: clickCANCELButton,
					showEffectOptions: {duration:0.2},
					hideEffectOptions: {duration:0}
				   });

	popup.setZIndex(1000);
}


//キャンセルボタンクリックイベントハンドラ
function clickOKReverseButton(window) {

	var cells = selectedRow.getElementsBySelector(".tableCell");
	var data = selectedRow.getElementsBySelector(".hiddenData");

	selectedRow.setStyle({
		background: "white"
	});

	selectedRow.stopObserving("click");
	selectedRow.observe("click",clickPopUp);
	selectedRow.observe("mouseover",overRow);
	selectedRow.observe("mouseout",leaveRow);

	//何もない場合
	if (data[3].innerHTML == "3") {

		data[3].innerHTML = "0";

	//更新の場合
	} else if (data[3].innerHTML == "4") {

		data[3].innerHTML = "1";

	//新規の場合
	} else if (data[3].innerHTML == "5") {

		data[3].innerHTML = "2";

	}

	//新規の場合はそのまま
	setRowColor(data[3].innerHTML, selectedRow)

	window.close();
	dbclickFlag = 0;
	areaCoverElement.remove();
}


//削除ボタンクリックイベントハンドラ
function clickDeleteButton(event) {

	var cells = selectedRow.getElementsBySelector(".tableCell");
	var data = selectedRow.getElementsBySelector(".hiddenData");


	setDeletedRowColor(selectedRow);

	for (var i=0; i < cells.length; i++) {

		cells[i].setStyle({
			background: "inherit"
		});
	}


	selectedRow.stopObserving("click");
	selectedRow.stopObserving("mouseover");
	selectedRow.stopObserving("mouseout");
	selectedRow.observe("click",clickDeletedRow);

	//何もない場合
	if (data[3].innerHTML == "0") {

		data[3].innerHTML = "3";

	//更新の場合
	} else if (data[3].innerHTML == "1") {

		data[3].innerHTML = "4";

	//新規の場合
	} else if (data[3].innerHTML == "2") {

		data[3].innerHTML = "5";

	}



	popUpView.close();
	dbclickFlag = 0;
	areaCoverElement.remove();
}
