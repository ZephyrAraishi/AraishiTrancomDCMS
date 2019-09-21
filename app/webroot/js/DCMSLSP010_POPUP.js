//選択行
var selectedRow;

//行の値保持
var orgValStaffCd = "";
var orgValKoutei  = "";
var orgValStTime = Array();
var orgValEdTime = Array();
var orgValSyukin = Array();

var actionUrl = "";

var dbclickFlag;//ダブルクリック防止フラグ
var areaCoverElement = null;//エリアロックのDIV
var popUpView = null;


//初期化
function initPopUp(url) {

	actionUrl = url;

	//該当行にイベントを付加
	if ($("lstTable") != null) {
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


	//スタッフ名クリック時は、戻る
	elm = Event.element(event) + "";
	if (Event.element(event).id == "hrefstaffNm"
	||  elm.substr(0,7)         == "http://") {
		dbclickFlag = 0;
		return;
	}


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
	    location.href = "/DCMS/LSP010/index?return_cd=91&message=" + Base64.encode(DCMSMessage.format("CMNE000101"));
	  },
	  onException : function( event, ex )  {
	    location.href = "/DCMS/LSP010/index?return_cd=92&message=" + Base64.encode(DCMSMessage.format("CMNE000102"));
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
					title: "勤務情報設定",
					width:650,
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

	//キャンセルボタン負荷
	var deleteButton = new Element("input");
	deleteButton.writeAttribute({
			type : "button",
			value : "削除"
	});
	deleteButton.addClassName("buttonsLSP01001");

	$$(".alphacube_buttons")[0].insert({
		top: deleteButton
	});

	deleteButton.observe("click",clickDeleteButton);


	//日付変更時のイベント
	$("syukin1").observe("change",changeCheckBox);
	$("syukin2").observe("change",changeCheckBox);
	$("syukin3").observe("change",changeCheckBox);
	$("syukin4").observe("change",changeCheckBox);
	$("syukin5").observe("change",changeCheckBox);
	$("syukin6").observe("change",changeCheckBox);
	$("syukin7").observe("change",changeCheckBox);


	//初期値設定
	var cells = selectedRow.getElementsBySelector(".tableCell");
	var data = selectedRow.getElementsBySelector(".hiddenData");


	//見出し部分
	var resultRows = $("lstTable").getElementsBySelector(".tableRowTitle");
	var cellsTtl = resultRows[0].getElementsBySelector(".tableCell");
	var dataTtl = resultRows[0].getElementsBySelector(".hiddenData");



	$("staffCd").value  = data[35].innerHTML;
	$("koutei").value   = cells[3].innerHTML + " ＞ " + cells[4].innerHTML;

	//曜日表示
	$("Youbi1").update(cellsTtl[5].innerHTML);
	$("Youbi2").update(cellsTtl[6].innerHTML);
	$("Youbi3").update(cellsTtl[7].innerHTML);
	$("Youbi4").update(cellsTtl[8].innerHTML);
	$("Youbi5").update(cellsTtl[9].innerHTML);
	$("Youbi6").update(cellsTtl[10].innerHTML);
	$("Youbi7").update(cellsTtl[11].innerHTML);

	//各曜日の開始時間・終了時間表示
	$("stTime1").value  = data[4].innerHTML;
	$("edTime1").value  = data[5].innerHTML;
	$("stTime2").value  = data[7].innerHTML;
	$("edTime2").value  = data[8].innerHTML;
	$("stTime3").value  = data[10].innerHTML;
	$("edTime3").value  = data[11].innerHTML;
	$("stTime4").value  = data[13].innerHTML;
	$("edTime4").value  = data[14].innerHTML;
	$("stTime5").value  = data[16].innerHTML;
	$("edTime5").value  = data[17].innerHTML;
	$("stTime6").value  = data[19].innerHTML;
	$("edTime6").value  = data[20].innerHTML;
	$("stTime7").value  = data[22].innerHTML;
	$("edTime7").value  = data[23].innerHTML;

	//各曜日の休日有無　チェックボックス設定
	if (data[6].innerHTML  == 1) {
		$("syukin1").checked  = true;
	} else {
		$("syukin1").checked  = false;
	}
	if (data[9].innerHTML  == 1) {
		$("syukin2").checked  = true;
	} else {
		$("syukin2").checked  = false;
	}
	if (data[12].innerHTML  == 1) {
		$("syukin3").checked  = true;
	} else {
		$("syukin3").checked  = false;
	}
	if (data[15].innerHTML  == 1) {
		$("syukin4").checked  = true;
	} else {
		$("syukin4").checked  = false;
	}
	if (data[18].innerHTML  == 1) {
		$("syukin5").checked  = true;
	} else {
		$("syukin5").checked  = false;
	}
	if (data[21].innerHTML  == 1) {
		$("syukin6").checked  = true;
	} else {
		$("syukin6").checked  = false;
	}
	if (data[24].innerHTML  == 1) {
		$("syukin7").checked  = true;
	} else {
		$("syukin7").checked  = false;
	}

	//各曜日の休日有無　チェックボックス設定
	if (data[36].innerHTML  == 1) {
		$("auto1").checked  = true;
	} else {
		$("auto1").checked  = false;
	}
	if (data[37].innerHTML  == 1) {
		$("auto2").checked  = true;
	} else {
		$("auto2").checked  = false;
	}
	if (data[38].innerHTML  == 1) {
		$("auto3").checked  = true;
	} else {
		$("auto3").checked  = false;
	}
	if (data[39].innerHTML  == 1) {
		$("auto4").checked  = true;
	} else {
		$("auto4").checked  = false;
	}
	if (data[40].innerHTML  == 1) {
		$("auto5").checked  = true;
	} else {
		$("auto5").checked  = false;
	}
	if (data[41].innerHTML  == 1) {
		$("auto6").checked  = true;
	} else {
		$("auto6").checked  = false;
	}
	if (data[42].innerHTML  == 1) {
		$("auto7").checked  = true;
	} else {
		$("auto7").checked  = false;
	}
	

	//値を保持
	//　clickOKButtonで使用するため
	orgValStaffCd   = cells[1].innerHTML;
	orgValKoutei    = cells[2].innerHTML;

	orgValStTime[0]   = $("stTime1").value;
	orgValEdTime[0]   = $("edTime1").value;
	orgValSyukin[0]   = $("syukin1").checked;
	orgValStTime[1]   = $("stTime2").value;
	orgValEdTime[1]   = $("edTime2").value;
	orgValSyukin[1]   = $("syukin2").checked;
	orgValStTime[2]   = $("stTime3").value;
	orgValEdTime[2]   = $("edTime3").value;
	orgValSyukin[2]   = $("syukin3").checked;
	orgValStTime[3]   = $("stTime4").value;
	orgValEdTime[3]   = $("edTime4").value;
	orgValSyukin[3]   = $("syukin4").checked;
	orgValStTime[4]   = $("stTime5").value;
	orgValEdTime[4]   = $("edTime5").value;
	orgValSyukin[4]   = $("syukin5").checked;
	orgValStTime[5]   = $("stTime6").value;
	orgValEdTime[5]   = $("edTime6").value;
	orgValSyukin[5]   = $("syukin6").checked;
	orgValStTime[6]   = $("stTime7").value;
	orgValEdTime[6]   = $("edTime7").value;
	orgValSyukin[6]   = $("syukin7").checked;


}


//テーブルのオーバーイベントハンドラ
//フォーカスマークをつける
function changeCheckBox(event) {

	if (Event.element(event).id == "syukin1" 
	&&  $("syukin1").checked  == true) {
		$("stTime1").value  = "";
		$("edTime1").value  = "";
	}
	if (Event.element(event).id == "syukin2" 
	&&  $("syukin2").checked  == true) {
		$("stTime2").value  = "";
		$("edTime2").value  = "";
	}
	if (Event.element(event).id == "syukin3" 
	&&  $("syukin3").checked  == true) {
		$("stTime3").value  = "";
		$("edTime3").value  = "";
	}
	if (Event.element(event).id == "syukin4" 
	&&  $("syukin4").checked  == true) {
		$("stTime4").value  = "";
		$("edTime4").value  = "";
	}
	if (Event.element(event).id == "syukin5" 
	&&  $("syukin5").checked  == true) {
		$("stTime5").value  = "";
		$("edTime5").value  = "";
	}
	if (Event.element(event).id == "syukin6" 
	&&  $("syukin6").checked  == true) {
		$("stTime6").value  = "";
		$("edTime6").value  = "";
	}
	if (Event.element(event).id == "syukin7" 
	&&  $("syukin7").checked  == true) {
		$("stTime7").value  = "";
		$("edTime7").value  = "";
	}

}


//更新ボタンクリックイベントハンドラ
function clickOKButton(window) {

	//値を設置
	//　下、両方親Viewの中
	var cells = selectedRow.getElementsBySelector(".tableCell");
	var data = selectedRow.getElementsBySelector(".hiddenData");

	// テキスト値取得
	// 　下、子Viewの中

	if ($F("stTime1").length == 3) {
		$("stTime1").value = "0" + $F("stTime1");
	}
	if ($F("stTime2").length == 3) {
		$("stTime2").value = "0" + $F("stTime2");
	}
	if ($F("stTime3").length == 3) {
		$("stTime3").value = "0" + $F("stTime3");
	}
	if ($F("stTime4").length == 3) {
		$("stTime4").value = "0" + $F("stTime4");
	}
	if ($F("stTime5").length == 3) {
		$("stTime5").value = "0" + $F("stTime5");
	}
	if ($F("stTime6").length == 3) {
		$("stTime6").value = "0" + $F("stTime6");
	}
	if ($F("stTime7").length == 3) {
		$("stTime7").value = "0" + $F("stTime7");
	}

	if ($F("edTime1").length == 3) {
		$("edTime1").value = "0" + $F("edTime1");
	}
	if ($F("edTime2").length == 3) {
		$("edTime2").value = "0" + $F("edTime2");
	}
	if ($F("edTime3").length == 3) {
		$("edTime3").value = "0" + $F("edTime3");
	}
	if ($F("edTime4").length == 3) {
		$("edTime4").value = "0" + $F("edTime4");
	}
	if ($F("edTime5").length == 3) {
		$("edTime5").value = "0" + $F("edTime5");
	}
	if ($F("edTime6").length == 3) {
		$("edTime6").value = "0" + $F("edTime6");
	}
	if ($F("edTime7").length == 3) {
		$("edTime7").value = "0" + $F("edTime7");
	}

	var stTime = Array();
	var edTime = Array();
	var syukin = Array();
	var auto = Array();

	stTime[0]    = $F("stTime1");
	edTime[0]    = $F("edTime1");
	syukin[0]    = $("syukin1").checked;
	auto[0]      = $("auto1").checked;
	stTime[1]    = $F("stTime2");
	edTime[1]    = $F("edTime2");
	syukin[1]    = $("syukin2").checked;
	auto[1]      = $("auto2").checked;
	stTime[2]    = $F("stTime3");
	edTime[2]    = $F("edTime3");
	syukin[2]    = $("syukin3").checked;
	auto[2]      = $("auto3").checked;
	stTime[3]    = $F("stTime4");
	edTime[3]    = $F("edTime4");
	syukin[3]    = $("syukin4").checked;
	auto[3]      = $("auto4").checked;
	stTime[4]    = $F("stTime5");
	edTime[4]    = $F("edTime5");
	syukin[4]    = $("syukin5").checked;
	auto[4]      = $("auto5").checked;
	stTime[5]    = $F("stTime6");
	edTime[5]    = $F("edTime6");
	syukin[5]    = $("syukin6").checked;
	auto[5]      = $("auto6").checked;
	stTime[6]    = $F("stTime7");
	edTime[6]    = $F("edTime7");
	syukin[6]    = $("syukin7").checked;
	auto[6]      = $("auto7").checked;


	//　入力チェック
	for (var i=0; i <= 6; i++) {

		var strI = String(i + 1);

		//時刻
		if (syukin[i]  == true) {
			if (DCMSValidation.notEmpty(stTime[i]) 
			||  DCMSValidation.notEmpty(edTime[i])) {
				//休日チェック時、入力してたらダメ
				alert(DCMSMessage.format("CMNE000007", "開始時刻" + strI + "・終了時刻" + strI));
				return;
			}
		} else {
			if (DCMSValidation.notEmpty(stTime[i]) 
			||  DCMSValidation.notEmpty(edTime[i])) {
				if (!DCMSValidation.notEmpty(stTime[i])) {
					//片方入力してたら、入力してないとダメ
					alert(DCMSMessage.format("CMNE000001", "開始時刻" + strI));
					return;
				}
				if (!DCMSValidation.notEmpty(edTime[i])) {
					//片方入力してたら、入力してないとダメ
					alert(DCMSMessage.format("CMNE000001", "終了時刻" + strI));
					return;
				}
				//フォーマットが不正
				if (stTime[i].trim() != "" && !stTime[i].match(/^[0-9]{4}/)) {
					alert(DCMSMessage.format("CMNE000004", "開始時刻" + strI, "4"));
					return;
				}
				if (edTime[i].trim() != "" && !edTime[i].match(/^[0-9]{4}/)) {
					alert(DCMSMessage.format("CMNE000004", "終了時刻" + strI, "4"));
					return;
				}
				//時刻が不正
				if (stTime[i].trim() < 0000 
				||  stTime[i].trim() > 2400 ) {
					alert(DCMSMessage.format("CMNE000007", "開始時刻" + strI));	//		時の方
					return;
				}
				if (stTime[i].trim().substr(2) < 00 
				||  stTime[i].trim().substr(2) > 59 ) {
					alert(DCMSMessage.format("CMNE000007", "開始時刻" + strI));	//		分の方
					return;
				}
				if (edTime[i].trim() < 0000 
				||  edTime[i].trim() > 3100 ) {
					alert(DCMSMessage.format("CMNE000007", "終了時刻" + strI));	//		時の方
					return;
				}
				if (edTime[i].trim().substr(2) < 00 
				||  edTime[i].trim().substr(2) > 59 ) {
					alert(DCMSMessage.format("CMNE000007", "終了時刻" + strI));	//		分の方
					return;
				}
				//開始時刻は、終了時刻より前
				if (parseInt(stTime[i].trim(), 10) >= parseInt(edTime[i], 10)) {
					alert(DCMSMessage.format("CMNE000008", "終了時刻" + strI, "開始時刻" + strI));
					return;
				}
			}
		}

	}


	//前後重なりチェック
	for (var i=0; i <= 6; i++) {

		var strI = String(i + 1);

		//時刻
		if (syukin[i]  == false) {
			var tmpTimeSt =  String(parseInt(stTime[i].trim().substr(0,2),10) + 24) + stTime[i].trim().substr(2);
	
			if (parseInt(edTime[i].trim(), 10) >= 2400) {
				var tmpTimeEd =  String(parseInt(edTime[i].trim().substr(0,2),10) - 24) + edTime[i].trim().substr(2);
				if (tmpTimeEd.length == 3) {
					tmpTimeEd = "0" + tmpTimeEd;
				}
			} else {
				var tmpTimeEd =  "0000";
			}
	
			//前日の終了時刻と開始時刻をチェック
			if (i  != 0) {
				j = i - 1;
				var strJ = String(j + 1);
				if (parseInt(edTime[j].trim(), 10) > parseInt(tmpTimeSt, 10)
				&&  syukin[j]  == false) {
					alert(DCMSMessage.format("CMNE000007", "終了時刻" + strJ + "　または　開始時刻" + strI));
					return;
				}
			}
			//翌日の開始時刻と終了時刻をチェック
			if (i  != 6) {
				j = i + 1;
				var strJ = String(j + 1);
				if (parseInt(stTime[j].trim(), 10) < parseInt(tmpTimeEd, 10)
				&&  syukin[j]  == false) {
					alert(DCMSMessage.format("CMNE000007", "終了時刻" + strI + "　または　開始時刻" + strJ));
					return;
				}
			}
		}

	}


	//更新

	var flgUpdate  = "0";

	// 　入力前と変化した場合

	for (var i=0; i <= 6; i++) {

		//　時刻
		var displayCell  = i + 5;
		if (stTime[i] != orgValStTime[i]) {
			setUpdatedCellColor(cells[displayCell]);		//項目のbackground変更
			var j  = (i+1) * 3 + 1;
			data[j].innerHTML  = stTime[i];		//値のセット
			flgUpdate  = "1";
		}
		if (edTime[i] != orgValEdTime[i]) {
			setUpdatedCellColor(cells[displayCell]);		//項目のbackground変更
			var j  = (i+1) * 3 + 2;
			data[j].innerHTML  = edTime[i];		//値のセット
			flgUpdate  = "1";
		}
		if (syukin[i] != orgValSyukin[i]) {
			setUpdatedCellColor(cells[displayCell]);		//項目のbackground変更
			if (syukin[i] == true) {
				var j  = (i+1) * 3 + 3;
				data[j].innerHTML  = 1;		//値のセット
			} else {
				var j  = (i+1) * 3 + 3;
				data[j].innerHTML  = 0;		//値のセット
			}
			flgUpdate  = "1";
		}
		if (syukin[i] == true) {
			cells[displayCell].innerHTML = "休";		//値のセット
		} else {
			if (stTime[i] == "") {
				cells[displayCell].innerHTML = "";		//値のセット
			} else {
				cells[displayCell].innerHTML = stTime[i].substr(0,2) + ":" + stTime[i].substr(2) + "～" + edTime[i].substr(0,2) + ":" + edTime[i].substr(2);		//値のセット
			}
		}

	}
	

	data[36].innerHTML = auto[0] ? 1 : 0;
	data[37].innerHTML = auto[1] ? 1 : 0;
	data[38].innerHTML = auto[2] ? 1 : 0;
	data[39].innerHTML = auto[3] ? 1 : 0;
	data[40].innerHTML = auto[4] ? 1 : 0;
	data[41].innerHTML = auto[5] ? 1 : 0;
	data[42].innerHTML = auto[6] ? 1 : 0;


	//1か所でも修正した場合
	if (flgUpdate  == "1") {
		//新規の場合はそのまま
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
					title: "勤務情報設定",
					width:400,
					height:100,
					draggable: true,
					destroyOnclose: true,
					recenterAuto: false,
					buttonClass : "buttons",
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
function clickCANCELButton(window) {

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
