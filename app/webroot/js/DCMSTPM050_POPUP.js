//選択行
var selectedRow;


//行の値保持
var originYOSOKU_BUTURYO = "";


//ポップアップのView取得URL
var actionUrl = "";

//ダブルクリック防止フラグ
var dbclickFlag = 0;

//ポップアップ表示前の全体カーテン
var areaCoverElement = null;

//ポップアップオブジェクト
var popUpView = null;

//ポップアップ表示と、行選択時に色を変えるための初期化関数
function initPopUp(url) {

	//ポップアップのURLの設定
	actionUrl = url;

	//行全体を取得
	var rows = $("lstTable").getElementsBySelector(".tableRow");

	//イベントを設定
    for(i=0; i < rows.length; i++)
	{
		//削除フラグが立っていたら、イベントが変わる
		var data = rows[i].getElementsBySelector(".hiddenData");

		if (parseInt(data[3].innerHTML.trim(),10) < 3) {

			rows[i].observe("click",clickPopUp);
			rows[i].observe("mouseover",overRow);
			rows[i].observe("mouseout",leaveRow);

		} else {

			rows[i].observe("click",clickDeletedRow);
		}
	}
}

//行のオーバーイベントハンドラ
//フォーカスマークをつける
function overRow(event) {

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
	if (row == null)
	{
		return;
	}

	//選択行の色を設定
	setSelectedRowColor(row);

}

//行のリーブイベントハンドラ
//フォーカスマークを外す
function leaveRow(event) {

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

	if (row == null)
	{
		return;
	}

	//行の色を設定
	var data = row.getElementsBySelector(".hiddenData");
	setRowColor(data[3].innerHTML, row)

}

//行のクリックイベントハンドラ
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

	//値を渡す
	var cells = selectedRow.getElementsBySelector(".tableCell");
	var data = selectedRow.getElementsBySelector(".hiddenData");

	var executeUrl = actionUrl + "?YMD_FROM=" + $("YMD_FROM_H").innerHTML + "&YMD_TO=" + $("YMD_TO_H").innerHTML + "&YMD_SYUGYO=" + cells[0].innerHTML + "&BNRI_DAI_CD=" + data[0].innerHTML + "&BNRI_CYU_CD=" + data[1].innerHTML + "&BNRI_SAI_CD=" + data[2].innerHTML;

	//ポップアップを読み込む
	new Ajax.Request( executeUrl, {
	  method : 'post',

	  onSuccess : viewUpdatePopUp,

	  onFailure : function( event )  {
	    location.href = "/DCMS/TPM050/index?return_cd=91&message=" + Base64.encode(DCMSMessage.format("CMNE000101"));
	  },
	  onException : function( event, ex )  {
	    location.href = "/DCMS/TPM050/index?return_cd=92&message=" + Base64.encode(DCMSMessage.format("CMNE000102"));
	  }
	});


}

//ポップアップのデータが取得完了したとき、ポップアップを表示する
function viewUpdatePopUp(event) {

	//ポップアップを画面に配置
	var popup = Dialog.confirm(event.responseText,
				   {
					id: "popup",
					className: "alphacube",
					title: "工程配置進捗設定",
					width:900,
					height:470,
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

	//ポップアップのZINDEXを設定
	popup.setZIndex(1000);

	popup.content.setStyle({overflow:"visible"});

	//ポップアップを保持
	popUpView = popup


	//選択行のデータを取得
	var cells = selectedRow.getElementsBySelector(".tableCell");
	var data = selectedRow.getElementsBySelector(".hiddenData");

	//値を設置
	$("table_title").update(cells[0].innerHTML + " " + cells[1].innerHTML + " " + cells[2].innerHTML + " " + cells[3].innerHTML);// タイトル
	$("KADO_NINZU").update(cells[4].innerHTML);
	$("RUIKEI_NINZU").update(cells[5].innerHTML);
	$("JIKAN_H").update(cells[6].innerHTML);
	$("JIKAN_M").update(cells[7].innerHTML);
	$("BUTURYO").update(cells[8].innerHTML);
	$("SEISANSEI_JINJI").update(cells[9].innerHTML);
	$("YOSOKU_BUTURYO").value = cells[10].innerHTML;
	$("YOSOKU_JIKAN").update(cells[11].innerHTML);


	//更新が起こった事を判定するために、値を保持
	originYOSOKU_BUTURYO = cells[10].innerHTML;

}


//ポップアップの決定ボタンクリックイベントハンドラ
function clickOKButton(window) {

	//入力チェックを行う

	//行データの取得
	var cells = selectedRow.getElementsBySelector(".tableCell");
	var data = selectedRow.getElementsBySelector(".hiddenData");

	//就業開始時刻フォーマットが不正
	if (!$F("YOSOKU_BUTURYO").trim().match(/^[0-9]+/)) {
		alert(DCMSMessage.format("CMNE000002", "予測物量"));
		return;
	}

	//更新処理

	//就業開始終了
	var varYOSOKU_BUTURYO = $F("YOSOKU_BUTURYO").trim();

	cells[10].innerHTML = varYOSOKU_BUTURYO

	if (cells[9].innerHTML != 0) {

		cells[11].innerHTML = Math.round(varYOSOKU_BUTURYO / cells[9].innerHTML);
	} else {
		cells[11].innerHTML = 0;
	}



	//工程終了時刻
	if (originYOSOKU_BUTURYO != varYOSOKU_BUTURYO){

		//セルカラーを設定
		setUpdatedCellColor(cells[10]);

		//処理フラグを、処理なし状態なら、更新状態へ変える
		if (data[3].innerHTML == "0") {
			data[3].innerHTML = "1";
		}

		//色を指定
		setRowColor(data[3].innerHTML, selectedRow)

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

