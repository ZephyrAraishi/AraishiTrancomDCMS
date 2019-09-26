//選択行
var selectedRow;

//行の値保持
var orgValSuryoItemFr = "";
var orgValSuryoItemTo = "";
var orgValPieceItemFr = "";
var orgValPieceItemTo = "";

//ポップアップのView取得URL
var popUrlRank = "";

//ダブルクリック防止フラグ
var dbclickFlag = 0;

//ポップアップ表示前の全体カーテン
var areaCoverElement = null;


//ポップアップ表示と、行選択時に色を変えるための初期化関数
function initPopUpRank(urlRank) {

	//URLの設定
	popUrlRank = urlRank;

	//ランク行全体を取得
	var rowsRank = $("lstTableRank").getElementsBySelector(".tableRow");

	for(i=0; i < rowsRank.length; i++) {
    	rowsRank[i].observe("click",clickPopUpRank);
    	rowsRank[i].observe("mouseover",overRowRank);
    	rowsRank[i].observe("mouseout",leaveRowRank);
	}
}

//行のオーバーイベントハンドラ
//フォーカスマークをつける
function overRowRank(event) {

	//選択行を取得
	var row = Event.element(event).up("div");

	if (row.hasClassName("tableRow") == false) {
		while (true) {
			row = row.up("div");
			
			if (row == null) {
				
				return;
			}
			
			if (row.hasClassName("tableRow") == true) {
				break;
			}
		}
	}

	//選択行が見つからなければリターン
	if (row == null) {
		return;
	}

	//選択行の色を設定
	setSelectedRowColor(row);

}

//行のリーブイベントハンドラ
//フォーカスマークを外す
function leaveRowRank(event) {

	//選択行を取得
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

	if (row == null) {
		return;
	}

	//行の色を設定
	var data = row.getElementsBySelector(".hiddenData");
	setRowColor(data[0].innerHTML, row);

}

//テーブル行クリックイベントハンドラ
function clickPopUpRank(event) {

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

	//選択行を取得
	var row = Event.element(event).up("div");

	if (row.hasClassName("tableRow") == false) {
		while (true) {
			row = row.up("div");
			if (row.hasClassName("tableRow") == true) {
				break
			}
		}
	}

	//選択行を保持
	selectedRow = row;

	//ポップアップを読み込む
	new Ajax.Request( popUrlRank, {
		method : 'post',

		onSuccess : viewPopUpRank,

		onFailure : function( event )  {
			location.href = "/DCMS/WPO030/index?return_cd=91&message=" + Base64.encode(DCMSMessage.format("CMNE000101"));
		},

		onException : function( event, ex )  {
			location.href = "/DCMS/WPO030/index?return_cd=92&message=" + Base64.encode(DCMSMessage.format("CMNE000102"));
		}
	});
}

/* ポップアップ */
function viewPopUpRank(event) {

	//画面に配置
	var popup = Dialog.confirm(event.responseText,
					{
						id: "popup",
						className: "alphacube",
						title: "スタッフランクマスタ変更",
						width:400,
						height:200,
						draggable: true,
						destroyOnclose: true,
						recenterAuto: false,
						buttonClass : "buttonsWPO03001",
						okLabel: "決定",
						cancelLabel:"キャンセル",
						onOk: clickOKButtonRank,
						onCancel: clickCANCELButtonRank,
						showEffectOptions: {duration:0.2},
						hideEffectOptions: {duration:0}
					});

	//ポップアップのZINDEXを設定
	popup.setZIndex(1000);


	//初期値設定
	var data = selectedRow.getElementsBySelector(".hiddenData");

	$("rank").update(data[2].innerHTML);			//スタッフランク
	$("suryoItemFr").value = data[3].innerHTML;		//アイテム数Fr
	$("suryoItemTo").value = data[4].innerHTML;		//アイテム数To
	$("suryoPieceFr").value = data[5].innerHTML;	//ピース数Fr
	$("suryoPieceTo").value = data[6].innerHTML;	//ピース数To

	//値を保持
	orgValSuryoItemFr = data[3].innerHTML;
	orgValSuryoItemTo = data[4].innerHTML;
	orgValPieceItemFr = data[5].innerHTML;
	orgValPieceItemTo = data[6].innerHTML;

}


//決定ボタンクリックイベントハンドラ
function clickOKButtonRank(window) {

	// 入力チェック
	//アイテム数自 必須チェック
	if(!DCMSValidation.notEmpty($F("suryoItemFr"))) {
		alert(DCMSMessage.format("CMNE000001", "アイテム数自"));
		return;
	}else{
		//アイテム数自 数字チェック
		if (!DCMSValidation.numeric($F("suryoItemFr"))) {
			alert(DCMSMessage.format("CMNE000002", "アイテム数自"));
			return;
		}
		//アイテム数自 桁数チェック
		else if(!DCMSValidation.maxLength($F("suryoItemFr"), 4)) {
			alert(DCMSMessage.format("CMNE000003", "アイテム数自", "4"));
			return;
		}
	}
	//アイテム数至 必須チェック
	if(!DCMSValidation.notEmpty($F("suryoItemTo"))) {
		alert(DCMSMessage.format("CMNE000001", "アイテム数至"));
		return;
	}else{
		//アイテム数至 数字チェック
		if (!DCMSValidation.numeric($F("suryoItemTo"))) {
			alert(DCMSMessage.format("CMNE000002", "アイテム数至"));
			return;
		}
		//アイテム数至 桁数チェック
		else if(!DCMSValidation.maxLength($F("suryoItemTo"), 4)) {
			alert(DCMSMessage.format("CMNE000003", "アイテム数至", "4"));
			return;
		}
	}
	//アイテム数 大小チェック
	if(parseInt($F("suryoItemFr")) > parseInt($F("suryoItemTo")) ) {
		alert(DCMSMessage.format("CMNE000019", "アイテム数至", "アイテム数自"));
		return;
	}

	//ピース数自 必須チェック
	if(!DCMSValidation.notEmpty($F("suryoPieceFr"))) {
		alert(DCMSMessage.format("CMNE000001", "ピース数自"));
		return;
	}else{
		//ピース数自 数字チェック
		if (!DCMSValidation.numeric($F("suryoPieceFr"))) {
			alert(DCMSMessage.format("CMNE000002", "ピース数自"));
			return;
		}
		//ピース数自 桁数チェック
		else if(!DCMSValidation.maxLength($F("suryoPieceFr"), 5)) {
			alert(DCMSMessage.format("CMNE000003", "ピース数自", "5"));
			return;
		}
	}
	//ピース数至 必須チェック
	if(!DCMSValidation.notEmpty($F("suryoPieceTo"))) {
		alert(DCMSMessage.format("CMNE000001", "ピース数至"));
		return;
	}else{
		//ピース数至 数字チェック
		if (!DCMSValidation.numeric($F("suryoPieceTo"))) {
			alert(DCMSMessage.format("CMNE000002", "ピース数至"));
			return;
		}
		//ピース数至 桁数チェック
		else if(!DCMSValidation.maxLength($F("suryoPieceTo"), 5)) {
			alert(DCMSMessage.format("CMNE000003", "ピース数至", "5"));
			return;
		}
	}
	//ピース数 大小チェック
	if(parseInt($F("suryoPieceFr")) > parseInt($F("suryoPieceTo")) ) {
		alert(DCMSMessage.format("CMNE000019", "ピース数至", "ピース数自"));
		return;
	}


	var cells = selectedRow.getElementsBySelector(".tableCell");
	var data = selectedRow.getElementsBySelector(".hiddenData");

	//アイテム数の設定
	cells[2].innerHTML = $F("suryoItemFr") + "～" + $F("suryoItemTo");
	data[3].innerHTML = $F("suryoItemFr");
	data[4].innerHTML = $F("suryoItemTo");
	setCellColor(	$F("suryoItemFr") + "～" + $F("suryoItemTo"),
					orgValSuryoItemFr + "～" + orgValSuryoItemTo,
					cells[2]);

	//ピース数
	cells[3].innerHTML = $F("suryoPieceFr") + "～" + $F("suryoPieceTo");
	data[5].innerHTML = $F("suryoPieceFr");
	data[6].innerHTML = $F("suryoPieceTo");
	setCellColor(	$F("suryoPieceFr") + "～" + $F("suryoPieceTo"),
					orgValPieceItemFr + "～" + orgValPieceItemTo,
					cells[3]);

	// 変更時処理
	if( $F("suryoItemFr") != orgValSuryoItemFr
	 || $F("suryoItemTo") != orgValSuryoItemTo
	 || $F("suryoPieceFr") != orgValPieceItemFr
	 || $F("suryoPieceTo") != orgValPieceItemTo) {

		data[0].innerHTML = "1";
		setRowColor(data[0].innerHTML, selectedRow);
	}

	window.close();
	dbclickFlag = 0;
	areaCoverElement.remove();
}

function clickCANCELButtonRank(windows) {
	window.close();
	dbclickFlag = 0;
	areaCoverElement.remove();
}
