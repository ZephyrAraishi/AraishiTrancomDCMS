//選択行
var selectedRow;

//行の値保持
var orgValstaff_cd = "";
var orgValstaff_nm = "";
var orgValsubsystem_id = "";
var orgValkino_id = "";


var actionUrl = "";

var dbclickFlag;//ダブルクリック防止フラグ
var areaCoverElement = null;//エリアロックのDIV
var popUpView = null;


//初期化
function initPopUp(url) {

	actionUrl = url;


	//該当行にイベントを付加
	var rows = $("lstTable").getElementsBySelector(".tableRow");

    for(i=0; i < rows.length; i++)
	{
		var data = rows[i].getElementsBySelector(".hiddenData");

		if (parseInt(data[1].innerHTML.trim(), 10) < 3) {

			rows[i].observe("click",clickPopUp);
			rows[i].observe("mouseover",overRow);
			rows[i].observe("mouseout",leaveRow);

		} else {

			rows[i].observe("click",clickDeletedRow);
		}
	}

}

//テーブルのオーバーイベントハンドラ
//フォーカスマークをつける
function overRow(event) {

	var row = Event.element(event).up("div");

	if (row.hasClassName("tableRow") == false) {

		while (true) {

			row = row.up("div");
			
			if (row == null) {
				
				return;
			}

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
			
			if (row == null) {
				
				return;
			}

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

	//更新の場合はそのまま
	setRowColor(data[1].innerHTML, row)

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
	    location.href = "/DCMS/HRD040/index?return_cd=91&message=" + Base64.encode(DCMSMessage.format("CMNE000101"));
	  },
	  onException : function( event, ex )  {
	    location.href = "/DCMS/HRD040/index?return_cd=92&message=" + Base64.encode(DCMSMessage.format("CMNE000102"));
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
					title: "スタッフ許可マスタ",
					width:900,
					height:240,
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
	deleteButton.addClassName("buttonsHRD04001");

	$$(".alphacube_buttons")[0].insert({
		top: deleteButton
	});

	deleteButton.observe("click",clickDeleteButton);


	//初期値設定
	var cells = selectedRow.getElementsBySelector(".tableCell");
	var data = selectedRow.getElementsBySelector(".hiddenData");



	$("staff_Cd").value = cells[0].innerHTML;
	$("staff_Nm").value = cells[1].innerHTML;
	$("subsystem_id").value = cells[2].innerHTML;
	$("kino_id").value = cells[3].innerHTML;


	//値を保持
	//　clickOKButtonで使用するため
	orgValstaff_cd = cells[0].innerHTML;
	orgValstaff_nm = cells[1].innerHTML;
	orgValsubsystem_id = cells[2].innerHTML;
	orgValkino_id = cells[3].innerHTML;

}


//更新ボタンクリックイベントハンドラ
function clickOKButton(window) {

	//値を設置
	//　下、両方親Viewの中
	var cells = selectedRow.getElementsBySelector(".tableCell");
	var data = selectedRow.getElementsBySelector(".hiddenData");

	// テキスト値取得
	// 　下、子Viewの中
	var staff_cd = $F("staff_cd");
	var subsystem_id = $F("subsystem_id");
	var kino_id = $F("kino_id");

	//　入力チェック
	//必須チェック
	if (!DCMSValidation.notEmpty($F("staff_cd"))) {
		alert(DCMSMessage.format("CMNE000001", "スタッフコード"));
		return;
	}
	if (!DCMSValidation.notEmpty($F("subsystem_id"))) {
		alert(DCMSMessage.format("CMNE000001", "サブシステムID"));
		return;
	}
	if (!DCMSValidation.notEmpty($F("kino_id"))) {
		alert(DCMSMessage.format("CMNE000001", "機能ID"));
		return;
	}

	//更新
	// 　入力前と変化した場合

	if (subsystem_id != orgsubsystem_id) {
		setUpdatedCellColor(cells[2]);		//項目のbackground変更

		cells[1].innerHTML = subsystem_id;		//値のセット

		//新規の場合はそのまま
		if (data[1].innerHTML == "0") {
			data[1].innerHTML = "1";
		}
		setRowColor(data[1].innerHTML, selectedRow)	//行のbackground変更
	}

	if (kino_id != orgValkino_id) {
		setUpdatedCellColor(cells[2]);		//項目のbackground変更

		cells[2].innerHTML = kino_id;		//値のセット

		//新規の場合はそのまま（行のbackground変更）
		if (data[1].innerHTML == "0") {
			data[1].innerHTML = "1";
		}
		setRowColor(data[1].innerHTML, selectedRow)	//行のbackground変更
	}


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
					title: "スタッフ許可マスタ",
					width:400,
					height:100,
					draggable: true,
					destroyOnclose: true,
					recenterAuto: false,
					buttonClass : "buttonsHRD04001",
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
	if (data[1].innerHTML == "3") {

		data[1].innerHTML = "0";

	//更新の場合
	} else if (data[1].innerHTML == "4") {

		data[1].innerHTML = "1";

	//新規の場合
	} else if (data[1].innerHTML == "5") {

		data[1].innerHTML = "2";

	}

	//新規の場合はそのまま
	setRowColor(data[1].innerHTML, selectedRow)

	window.close();
	dbclickFlag = 0;
	areaCoverElement.remove();
}


//キャンセルボタンクリックイベントハンドラ
function clickCANCELButton(windows) {

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
	if (data[1].innerHTML == "0") {

		data[1].innerHTML = "3";

	//更新の場合
	} else if (data[1].innerHTML == "1") {

		data[1].innerHTML = "4";

	//新規の場合
	} else if (data[1].innerHTML == "2") {

		data[1].innerHTML = "5";

	}



	popUpView.close();
	dbclickFlag = 0;
	areaCoverElement.remove();
}
