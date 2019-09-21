//選択行
var selectedRow;

//行の値保持
var orgValMeiKbn = "";
var orgValMeiCd = "";
var orgValMei1 = "";
var orgValMei2 = "";
var orgValMei3 = "";
var orgValStr1 = "";
var orgValStr2 = "";
var orgValStr3 = "";
var orgValNum1 = "";
var orgValNum2 = "";
var orgValNum3 = "";


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
	    location.href = "/DCMS/WEX110/index?return_cd=91&message=" + Base64.encode(DCMSMessage.format("CMNE000101"));
	  },
	  onException : function( event, ex )  {
	    location.href = "/DCMS/WEX110/index?return_cd=92&message=" + Base64.encode(DCMSMessage.format("CMNE000102"));
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
					title: "名称マスタ設定",
					width:600,
					height:420,
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


	//初期値設定
	var cells = selectedRow.getElementsBySelector(".tableCell");


	$("mei_kbn").value = cells[1].innerHTML;
	$("mei_cd").value = cells[2].innerHTML;
	$("mei_1").value = cells[3].innerHTML;
	$("mei_2").value = cells[4].innerHTML;
	$("mei_3").value = cells[5].innerHTML;
	$("val_free_str_1").value = cells[6].innerHTML;
	$("val_free_str_2").value = cells[7].innerHTML;
	$("val_free_str_3").value = cells[8].innerHTML;
	$("val_free_num_1").value = cells[9].innerHTML;
	$("val_free_num_2").value = cells[10].innerHTML;
	$("val_free_num_3").value = cells[11].innerHTML;
	


	//値を保持
	//　clickOKButtonで使用するため
	orgValMeiKbn = cells[1].innerHTML;
	orgValMeiCd = cells[2].innerHTML;
	orgValMei1 = cells[3].innerHTML;
	orgValMei2 = cells[4].innerHTML;
	orgValMei3 = cells[5].innerHTML;
	orgValStr1 = cells[6].innerHTML;
	orgValStr2 = cells[7].innerHTML;
	orgValStr3 = cells[8].innerHTML;
	orgValNum1 = cells[9].innerHTML;
	orgValNum2 = cells[10].innerHTML;
	orgValNum3 = cells[11].innerHTML;

}


//更新ボタンクリックイベントハンドラ
function clickOKButton(window) {

	//値を設置
	//　下、両方親Viewの中
	var cells = selectedRow.getElementsBySelector(".tableCell");
	var data = selectedRow.getElementsBySelector(".hiddenData");

	// テキスト値取得
	var mei_kbn = $F("mei_kbn");
	var mei_cd = $F("mei_cd");
	var mei_1 = $F("mei_1");
	var mei_2 = $F("mei_2");
	var mei_3 = $F("mei_3");
	var val_free_str_1 = $F("val_free_str_1");
	var val_free_str_2 = $F("val_free_str_2");
	var val_free_str_3 = $F("val_free_str_3");
	var val_free_num_1 = $F("val_free_num_1");
	var val_free_num_2 = $F("val_free_num_2");
	var val_free_num_3 = $F("val_free_num_3");
	

	//　入力チェック
	//必須チェック
	if (!DCMSValidation.notEmpty($F("mei_kbn"))) {
		alert(DCMSMessage.format("CMNE000001", "名称区分"));
		return;
	}
	if (!DCMSValidation.notEmpty($F("mei_cd"))) {
		alert(DCMSMessage.format("CMNE000001", "名称コード"));
		return;
	}

	//数字チェック
	if (!DCMSValidation.numeric($F("mei_kbn"))) {
		alert(DCMSMessage.format("CMNE000002", "名称区分"));
		return;
	}
	
	if (!DCMSValidation.numeric($F("mei_cd"))) {
		alert(DCMSMessage.format("CMNE000002", "名称コード"));
		return;
	}
	
	var pattern = /^[-]?([1-9]\d*|0)(\.\d+)?$/;
	
	if ($F("val_free_num_1") != "" && !pattern.test($F("val_free_num_1"))) {
		alert(DCMSMessage.format("CMNE000002", "フリー数字１"));
		return;
	}

	if ($F("val_free_num_2") != "" && !pattern.test($F("val_free_num_2"))) {
		alert(DCMSMessage.format("CMNE000002", "フリー数字２"));
		return;
	}
	
	if ($F("val_free_num_3") != "" && !pattern.test($F("val_free_num_3"))) {
		alert(DCMSMessage.format("CMNE000002", "フリー数字３"));
		return;
	}


	//更新
	if (mei_kbn != orgValMeiKbn) {
		setUpdatedCellColor(cells[1]);		//項目のbackground変更

		cells[1].innerHTML = mei_kbn;		//値のセット

		//新規の場合はそのまま
		if (data[1].innerHTML == "0") {
			data[1].innerHTML = "1";
		}
		setRowColor(data[1].innerHTML, selectedRow)	//行のbackground変更
	}

	if (mei_cd != orgValMeiCd) {
		setUpdatedCellColor(cells[2]);		//項目のbackground変更

		cells[2].innerHTML = mei_cd;		//値のセット

		//新規の場合はそのまま（行のbackground変更）
		if (data[1].innerHTML == "0") {
			data[1].innerHTML = "1";
		}
		setRowColor(data[1].innerHTML, selectedRow)	//行のbackground変更
	}


	if (mei_1 != orgValMei1) {
		setUpdatedCellColor(cells[3]);		//項目のbackground変更

		cells[3].innerHTML = mei_1;		//値のセット

		//新規の場合はそのまま（行のbackground変更）
		if (data[1].innerHTML == "0") {
			data[1].innerHTML = "1";
		}
		setRowColor(data[1].innerHTML, selectedRow)	//行のbackground変更
	}

	if (mei_2 != orgValMei2) {
		setUpdatedCellColor(cells[4]);		//項目のbackground変更

		cells[4].innerHTML = mei_2;		//値のセット

		//新規の場合はそのまま（行のbackground変更）
		if (data[1].innerHTML == "0") {
			data[1].innerHTML = "1";
		}
		setRowColor(data[1].innerHTML, selectedRow)	//行のbackground変更
	}

	if (mei_3 != orgValMei3) {
		setUpdatedCellColor(cells[5]);		//項目のbackground変更

		cells[5].innerHTML = mei_3;		//値のセット

		//新規の場合はそのまま（行のbackground変更）
		if (data[1].innerHTML == "0") {
			data[1].innerHTML = "1";
		}
		setRowColor(data[1].innerHTML, selectedRow)	//行のbackground変更
	}
	
	if (val_free_str_1 != orgValStr1) {
		setUpdatedCellColor(cells[6]);		//項目のbackground変更

		cells[6].innerHTML = val_free_str_1;		//値のセット

		//新規の場合はそのまま（行のbackground変更）
		if (data[1].innerHTML == "0") {
			data[1].innerHTML = "1";
		}
		setRowColor(data[1].innerHTML, selectedRow)	//行のbackground変更
	}
	
	if (val_free_str_2 != orgValStr2) {
		setUpdatedCellColor(cells[7]);		//項目のbackground変更

		cells[7].innerHTML = val_free_str_2;		//値のセット

		//新規の場合はそのまま（行のbackground変更）
		if (data[1].innerHTML == "0") {
			data[1].innerHTML = "1";
		}
		setRowColor(data[1].innerHTML, selectedRow)	//行のbackground変更
	}
	
	if (val_free_str_3 != orgValStr3) {
		setUpdatedCellColor(cells[8]);		//項目のbackground変更

		cells[8].innerHTML = val_free_str_3;		//値のセット

		//新規の場合はそのまま（行のbackground変更）
		if (data[1].innerHTML == "0") {
			data[1].innerHTML = "1";
		}
		setRowColor(data[1].innerHTML, selectedRow)	//行のbackground変更
	}
	
	if (val_free_num_1 != orgValNum1) {
		setUpdatedCellColor(cells[9]);		//項目のbackground変更

		cells[9].innerHTML = val_free_num_1;		//値のセット

		//新規の場合はそのまま（行のbackground変更）
		if (data[1].innerHTML == "0") {
			data[1].innerHTML = "1";
		}
		setRowColor(data[1].innerHTML, selectedRow)	//行のbackground変更
	}
	
	if (val_free_num_2 != orgValNum2) {
		setUpdatedCellColor(cells[10]);		//項目のbackground変更

		cells[10].innerHTML = val_free_num_2;		//値のセット

		//新規の場合はそのまま（行のbackground変更）
		if (data[1].innerHTML == "0") {
			data[1].innerHTML = "1";
		}
		setRowColor(data[1].innerHTML, selectedRow)	//行のbackground変更
	}
	
	if (val_free_num_3 != orgValNum3) {
		setUpdatedCellColor(cells[11]);		//項目のbackground変更

		cells[11].innerHTML = val_free_num_3;		//値のセット

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
					title: "細分類マスタ設定",
					width:400,
					height:100,
					draggable: true,
					destroyOnclose: true,
					recenterAuto: false,
					buttonClass : "buttonsWEX05001",
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
