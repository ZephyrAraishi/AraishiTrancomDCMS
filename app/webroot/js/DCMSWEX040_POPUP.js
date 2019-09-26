//選択行
var selectedRow;

//行の値保持
var orgValDaiCd = "";
var orgValCyuCd = "";
var orgValCyuNm = "";
var orgValCyuRyaku = "";
var orgValCyuExp = "";
var originValKbnTani = "";
var originValKbnUriage = "";

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
	    location.href = "/DCMS/WEX040/index?return_cd=91&message=" + Base64.encode(DCMSMessage.format("CMNE000101"));
	  },
	  onException : function( event, ex )  {
	    location.href = "/DCMS/WEX040/index?return_cd=92&message=" + Base64.encode(DCMSMessage.format("CMNE000102"));
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
					title: "中分類マスタ設定",
					width:900,
					height:270,
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
	deleteButton.addClassName("buttonsWEX04001");

	$$(".alphacube_buttons")[0].insert({
		top: deleteButton
	});

	deleteButton.observe("click",clickDeleteButton);


	//初期値設定
	var cells = selectedRow.getElementsBySelector(".tableCell");
	var data = selectedRow.getElementsBySelector(".hiddenData");



	$("bnriDaiCd").value = cells[0].innerHTML;
	$("bnriCyuCd").value = cells[1].innerHTML;
	$("bnriCyuNm").value = cells[2].innerHTML;
	$("bnriCyuRyaku").value = cells[3].innerHTML;
	$("bnriCyuExp").value = cells[4].innerHTML;
	$("kbn_tani").value = data[2].innerHTML;		// 単位
	$("kbn_uriage").value = data[3].innerHTML;		// 売上


	//値を保持
	//　clickOKButtonで使用するため
	orgValCyuCd = cells[1].innerHTML;
	orgValCyuNm = cells[2].innerHTML;
	orgValCyuRyaku = cells[3].innerHTML;
	orgValCyuExp = cells[4].innerHTML;
	originValKbnTani = data[2].innerHTML;
	originValKbnUriage = data[3].innerHTML;

}


//更新ボタンクリックイベントハンドラ
function clickOKButton(window) {

	//値を設置
	//　下、両方親Viewの中
	var cells = selectedRow.getElementsBySelector(".tableCell");
	var data = selectedRow.getElementsBySelector(".hiddenData");

	// テキスト値取得
	// 　下、子Viewの中
	var bnriCyuCd = $F("bnriCyuCd");
	var bnriCyuNm = $F("bnriCyuNm");
	var bnriCyuRyaku = $F("bnriCyuRyaku");
	var bnriCyuExp = $F("bnriCyuExp");

	// selectは未選択時にnullになるため""に置き換え
	var kbnTani = $F("kbn_tani");
	if(kbnTani == null){
		kbnTani = "";
	}

	// selectは未選択時にnullになるため""に置き換え
	var kbnUriage = $F("kbn_uriage");
	if(kbnUriage == null){
		kbnUriage = "";
	}

	//　入力チェック
	//必須チェック
	if (!DCMSValidation.notEmpty($F("bnriCyuCd"))) {
		alert(DCMSMessage.format("CMNE000001", "中分類コード"));
		return;
	}
	if (!DCMSValidation.notEmpty($F("bnriCyuNm"))) {
		alert(DCMSMessage.format("CMNE000001", "中分類名称"));
		return;
	}
	if (!DCMSValidation.notEmpty($F("bnriCyuRyaku"))) {
		alert(DCMSMessage.format("CMNE000001", "中分類略称"));
		return;
	}
	if (!DCMSValidation.notEmpty($F("kbn_tani"))) {
		alert(DCMSMessage.format("CMNE000001", "単位区分"));
		return;
	}
	if (!DCMSValidation.notEmpty($F("kbn_uriage"))) {
		alert(DCMSMessage.format("CMNE000001", "売上区分"));
		return;
	}

	//数字チェック
	if (!DCMSValidation.numeric($F("bnriCyuCd"))) {
		alert(DCMSMessage.format("CMNE000002", "中分類コード"));
		return;
	}

	//桁数チェック
	if (!DCMSValidation.numLength($F("bnriCyuCd"), 3)) {
		alert(DCMSMessage.format("CMNE000004", "中分類コード","3"));
		return;
	}

	//"000"は入力不可
	if (parseInt($F("bnriCyuCd"), 10) <= 0 ) {
		alert(DCMSMessage.format("CMNE000019", "中分類コード","001"));
		return;
	}

	//最大桁数チェック
	if (!DCMSValidation.maxLength($F("bnriCyuNm"), 20)) {
		alert(DCMSMessage.format("CMNE000003", "中分類名称"));
		return;
	}
	if (!DCMSValidation.maxLength($F("bnriCyuRyaku"), 6)) {
		alert(DCMSMessage.format("CMNE000003", "中分類略称"));
		return;
	}
	if (!DCMSValidation.maxLength($F("bnriCyuExp"), 40)) {
		alert(DCMSMessage.format("CMNE000003", "中分類説明"));
		return;
	}


	//更新
	// 　入力前と変化した場合

	if (kbnTani != originValKbnTani) {
		setUpdatedCellColor(cells[5]);		//項目のbackground変更

		// 区分の名称を取得
		var kbn_tani_nm = "";
		var options = $A($('kbn_tani').getElementsByTagName('option'));
		options.each(function(option){
			if (option.selected) {
				kbn_tani_nm = option.innerHTML;
			}
		});

		cells[5].innerHTML = kbn_tani_nm;
		data[2].innerHTML = $F("kbn_tani");

		//新規の場合はそのまま
		if (data[1].innerHTML == "0") {
			data[1].innerHTML = "1";
		}
		setRowColor(data[1].innerHTML, selectedRow)	//行のbackground変更
	}

	if (kbnUriage != originValKbnUriage) {
		setUpdatedCellColor(cells[6]);		//項目のbackground変更

		// 区分の名称を取得
		var kbn_uriage_nm = "";
		var options = $A($('kbn_uriage').getElementsByTagName('option'));
		options.each(function(option){
			if (option.selected) {
				kbn_uriage_nm = option.innerHTML;
			}
		});

		cells[6].innerHTML = kbn_uriage_nm;
		data[3].innerHTML = $F("kbn_uriage");

		//新規の場合はそのまま
		if (data[1].innerHTML == "0") {
			data[1].innerHTML = "1";
		}
		setRowColor(data[1].innerHTML, selectedRow)	//行のbackground変更
	}

	if (bnriCyuNm != orgValCyuNm) {
		setUpdatedCellColor(cells[2]);		//項目のbackground変更

		cells[2].innerHTML = bnriCyuNm;		//値のセット

		//新規の場合はそのまま
		if (data[1].innerHTML == "0") {
			data[1].innerHTML = "1";
		}
		setRowColor(data[1].innerHTML, selectedRow)	//行のbackground変更
	}

	if (bnriCyuRyaku != orgValCyuRyaku) {
		setUpdatedCellColor(cells[3]);		//項目のbackground変更

		cells[3].innerHTML = bnriCyuRyaku;		//値のセット

		//新規の場合はそのまま（行のbackground変更）
		if (data[1].innerHTML == "0") {
			data[1].innerHTML = "1";
		}
		setRowColor(data[1].innerHTML, selectedRow)	//行のbackground変更
	}

	if (bnriCyuExp != orgValCyuExp) {
		setUpdatedCellColor(cells[4]);		//項目のbackground変更

		cells[4].innerHTML = bnriCyuExp;		//値のセット

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
					title: "中分類マスタ設定",
					width:400,
					height:100,
					draggable: true,
					destroyOnclose: true,
					recenterAuto: false,
					buttonClass : "buttonsWEX04001",
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


	if (cells[7].innerHTML >= "1"
	&&  cells[7].innerHTML != "新規") {
		alert("このデータは、細分類が存在するため、削除できません。");
		return;
	}


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
