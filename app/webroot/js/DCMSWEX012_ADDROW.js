var actionUrl2;

function initAddRow(url) {

	actionUrl2 = url;

	$("addButton").observe("click",clickAddButton);

}

function clickAddButton(event) {

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
	areaCoverElement = new Element("div");
	var height = Math.max.apply( null, [document.body.clientHeight ,
	                                    document.body.scrollHeight,
	                                    document.documentElement.scrollHeight,
	                                    document.documentElement.clientHeight] );

	areaCoverElement.setStyle({
		height: height + "px"
	});
	areaCoverElement.addClassName("displayCover");
	$$("body")[0].insert(areaCoverElement);


	//ポップアップを読み込む
	new Ajax.Request( actionUrl2, {
	  method : 'post',

	  onSuccess : viewInsertPopUp,

	  onFailure : function( event )  {
	    location.href = "/DCMS/WEX012/index?return_cd=91&message=" + Base64.encode(DCMSMessage.format("CMNE000101"));
	  },
	  onException : function( event, ex )  {
	    location.href = "/DCMS/WEX012/index?return_cd=92&message=" + Base64.encode(DCMSMessage.format("CMNE000102"));
	  }
	});
}

/* ポップアップ */
function viewInsertPopUp(event) {

	//画面に配置
	var popup = Dialog.confirm(event.responseText,
				   {
					id: "popup",
					className: "alphacube",
					title: "組織マスタ設定",
					width:1000,
					height:350,
					draggable: true,
					destroyOnclose: true,
					recenterAuto: false,
					buttonClass : "buttons",
					okLabel: "追加",
					cancelLabel:"キャンセル",
					onOk: clickOKButton2,
					onCancel: clickCANCELButton2,
					showEffectOptions: {duration:0.2},
					hideEffectOptions: {duration:0}
				   });

	popup.setZIndex(1000);

	popup.content.setStyle({overflow:"visible"});

	//分類を設定
	initBunruiPopup();

	//ポップアップを保持
	popUpView = popup

}


//OKボタンクリックイベントハンドラ
function clickOKButton2(window) {

	//　入力チェック
	//必須チェック
	if (!DCMSValidation.notEmpty($F("mninusi"))) {
		alert(DCMSMessage.format("CMNE000001", "荷主"));
		return;
	}
	if (!DCMSValidation.notEmpty($F("sosikiCd"))) {
		alert(DCMSMessage.format("CMNE000001", "組織コード"));
		return;
	}
	if (!DCMSValidation.notEmpty($F("sosikiNm"))) {
		alert(DCMSMessage.format("CMNE000001", "組織名称"));
		return;
	}
	if (!DCMSValidation.notEmpty($F("sosikiRyaku"))) {
		alert(DCMSMessage.format("CMNE000001", "組織略称"));
		return;
	}

	//数字チェック
	if (!DCMSValidation.numeric($F("sosikiCd"))) {
		alert(DCMSMessage.format("CMNE000002", "組織コード"));
		return;
	}


	//桁数チェック
	if (!DCMSValidation.numLength($F("sosikiCd"), 10)) {
		alert(DCMSMessage.format("CMNE000004", "組織コード","10"));
		return;
	}

	//"0000000000"は入力不可
	if (parseInt($F("sosikiCd"),10) <= 0 ) {
		alert(DCMSMessage.format("CMNE000019", "組織コード","001"));
		return;
	}

	//最大桁数チェック
	if (!DCMSValidation.maxLength($F("sosikiNm"), 20)) {
		alert(DCMSMessage.format("CMNE000003", "組織名称"));
		return;
	}
	if (!DCMSValidation.maxLength($F("sosikiRyaku"), 6)) {
		alert(DCMSMessage.format("CMNE000003", "組織略称"));
		return;
	}

	//更新

	//行作成
	var tableRow = new Element("div");
	tableRow.addClassName("tableRow row_dat");
	tableRow.setStyle({
		width:"1575px"
	});

	var tableCellBango = new Element("div");
	tableCellBango.addClassName("tableCellBango cell_dat");
	tableCellBango.setStyle({
		width:"60px"
	});
	tableCellBango.innerHTML = "新規";
	tableRow.insert(tableCellBango);

	//5個セル作成
	var tableCell = new Element("div");
	tableCell.addClassName("tableCell cell_dat cd");
	tableCell.setStyle({
		width:"100px"
	});
	tableRow.insert(tableCell);

	var tableCell = new Element("div");
	tableCell.addClassName("tableCell cell_dat nm");
	tableCell.setStyle({
		width:"100px"
	});
	tableRow.insert(tableCell);

	var tableCell = new Element("div");
	tableCell.addClassName("tableCell cell_dat cd");
	tableCell.setStyle({
		width:"50px"
	});
	tableRow.insert(tableCell);

	tableCell = new Element("div");
	tableCell.addClassName("tableCell cell_dat nm");
	tableCell.setStyle({
		width:"200px"
	});
	tableRow.insert(tableCell);

	//データDIVを13個配置
//	for (var i=0 ; i< 2; i++) {

		var hiddenData = new Element("div");
		hiddenData.addClassName("hiddenData LINE_COUNT");
		tableRow.insert(hiddenData);

		var hiddenData = new Element("div");
		hiddenData.addClassName("hiddenData CHANGED");
		tableRow.insert(hiddenData);

//	}


	//値を設置
	//　下、両方親Viewの中
	var cells = tableRow.getElementsBySelector(".tableCell");
	var data = tableRow.getElementsBySelector(".hiddenData");


	// テキスト値取得
	// 　下、子Viewの中
	var ninunsiCd = $F("mninusi");
	// 荷主名称を取得
	var ninusi_nm = "";
	var options = $A($('mninusi').getElementsByTagName('option'));
	options.each(function(option){
		if (option.selected) {
			ninusi_nm = option.innerHTML;
		}
	});
	var ninusiNm = ninusi_nm;
	var sosikiCd = $F("bnriSaiCd");
	var sosikiNm = $F("bnriSaiNm");
	var sosikiRyaku = $F("bnriSaiRyaku");

	//変更項目の処理
	cells[0].innerHTML = ninusiCd;
	cells[1].innerHTML = ninusiNm;
	cells[2].innerHTML = sosikiCd;
	cells[3].innerHTML = sosikiNm;
	cells[4].innerHTML = sosikiRyaku;

	data[0].innerHTML = "0";
	data[1].innerHTML = "2";	//新規フラグ


	tableRow.observe("click",clickPopUp);
	tableRow.observe("mouseover",overRow);
	tableRow.observe("mouseout",leaveRow);

	//テーブルに配置
	$("lstTable").insert(tableRow);

	$("lstTable").scrollTop = $("lstTable").scrollHeight;

	tableRow.setStyle({
		background: "#b0c4de"
	});

	window.close();
	dbclickFlag = 0;
	areaCoverElement.remove();
}


//キャンセルボタンクリックイベントハンドラ
function clickCANCELButton2(window) {

	window.close();
	dbclickFlag = 0;
	areaCoverElement.remove();

}

