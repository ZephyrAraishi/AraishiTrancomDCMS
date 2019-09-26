//選択行
var selectedRow;

//行の値保持
var orgValRankUpd = "";

//ポップアップのView取得URL
var popUrlStaff = "";

//ダブルクリック防止フラグ
var dbclickFlag = 0;

//ポップアップ表示前の全体カーテン
var areaCoverElement = null;


//ポップアップ表示と、行選択時に色を変えるための初期化関数
function initPopUpStaff(urlStaff) {

	//URLの設定
	popUrlStaff = urlStaff;

	//ランク行全体を取得
	var rowsRank = $("lstTableStaff").getElementsBySelector(".tableRow");
    for(i=0; i < rowsRank.length; i++) {

    	rowsRank[i].observe("click",clickPopUpStaff);
    	rowsRank[i].observe("mouseover",overRowStaff);
    	rowsRank[i].observe("mouseout",leaveRowStaff);
	}

}

//行のオーバーイベントハンドラ
//フォーカスマークをつける
function overRowStaff(event) {

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
function leaveRowStaff(event) {

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
function clickPopUpStaff(event) {

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
	new Ajax.Request( popUrlStaff, {
		method : 'post',

		onSuccess : viewPopUpStaff,

		onFailure : function( event )  {
			location.href = "/DCMS/WPO030/index?return_cd=91&message=" + Base64.encode(DCMSMessage.format("CMNE000101"));
		},

		onException : function( event, ex )  {
			location.href = "/DCMS/WPO030/index?return_cd=92&message=" + Base64.encode(DCMSMessage.format("CMNE000102"));
		}
	});
}

/* ポップアップ */
function viewPopUpStaff(event) {

	//画面に配置
	var popup = Dialog.confirm(event.responseText,
					{
						id: "popup",
						className: "alphacube",
						title: "スタッフランク変更",
						width:400,
						height:200,
						draggable: true,
						destroyOnclose: true,
						recenterAuto: false,
						buttonClass : "buttonsWPO03001",
						okLabel: "決定",
						cancelLabel:"キャンセル",
						onOk: clickOKButtonStaff,
						onCancel: clickCANCELButtonStaff,
						showEffectOptions: {duration:0.2},
						hideEffectOptions: {duration:0}
					});

	//ポップアップのZINDEXを設定
	popup.setZIndex(1000);


	//初期値設定
	var cells = selectedRow.getElementsBySelector(".tableCell");
	var data = selectedRow.getElementsBySelector(".hiddenData");

	$("staffCd").update(cells[1].innerHTML);		//スタッフコード
	$("staffNm").update(cells[2].innerHTML);		//スタッフ名

	//変更ランク
    var rankUpdOption = cells[6].innerHTML;
	var options = $$('select#rankUpdOption option');

	for (var i = 0; i < options.length; i++) {
		if(options[i].innerHTML == cells[6].innerHTML) {
			options[i].selected = true;
			break;
	    }
	}

	//値を保持
	orgValRankUpd = cells[6].innerHTML;

}


//決定ボタンクリックイベントハンドラ
function clickOKButtonStaff(window) {

	var cells = selectedRow.getElementsBySelector(".tableCell");
	var data = selectedRow.getElementsBySelector(".hiddenData");

	// 変更時処理
	if( $F("rankUpdOption") != orgValRankUpd ) {

		cells[6].innerHTML = $F("rankUpdOption");
		data[0].innerHTML = "1";
		setCellColor( $F("rankUpdOption"), orgValRankUpd, cells[6]);
		setRowColor(data[0].innerHTML, selectedRow);
	}

	window.close();
	dbclickFlag = 0;
	areaCoverElement.remove();
}

function clickCANCELButtonStaff(windows) {
	window.close();
	dbclickFlag = 0;
	areaCoverElement.remove();
}
