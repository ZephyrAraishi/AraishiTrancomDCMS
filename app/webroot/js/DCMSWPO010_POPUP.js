//選択行
var selectedRow;

//行の値保持
var originValue = "";

//ポップアップのView取得URL
var actionUrl = "";

//ダブルクリック防止フラグ
var dbclickFlag = 0;

//ポップアップ表示前の全体カーテン
var areaCoverElement = null;

//初期化
function initPopUp(url) {

	//ポップアップのURLの設定
	actionUrl = url;

	//行全体を取得
	var rows = $("lstTable").getElementsBySelector(".tableRow");

	//イベントを設定
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

//テーブルのリーブイベントハンドラ
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
	setRowColor(data[7].innerHTML, row)

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
	new Ajax.Request( actionUrl, {
	  method : 'post',

	  onSuccess : viewPopUp,

	  onFailure : function( event )  {
	    location.href = "/DCMS/WPO010/index?return_cd=91&message=" + Base64.encode(DCMSMessage.format("CMNE000101"));
	  },
	  onException : function( event, ex )  {
	    location.href = "/DCMS/WPO010/index?return_cd=92&message=" + Base64.encode(DCMSMessage.format("CMNE000102"));
	  }
	});
}

//ポップアップのデータが取得完了したとき、ポップアップを表示する
function viewPopUp(event) {

	//画面に配置
	var popup = Dialog.confirm(event.responseText,
				   {
					id: "popup",
					className: "alphacube",
					title: "バッチ名設定",
					width:400,
					height:260,
					draggable: true,
					destroyOnclose: true,
					recenterAuto: false,
					buttonClass : "buttons",
					okLabel: "決定",
					cancelLabel:"キャンセル",
					onOk: clickOKButton,
					onCancel: clickCANCELButton,
					showEffectOptions: {duration:0.2},
					hideEffectOptions: {duration:0}
				   });

	//ポップアップのZINDEXを設定
	popup.setZIndex(1000);


	//初期値設定

	var cells = selectedRow.getElementsBySelector(".tableCell");
	var data = selectedRow.getElementsBySelector(".hiddenData");

	$("sijiDay").update(cells[1].innerHTML);
	$("batCD").update(data[2].innerHTML);
	$("pickCnt").update(data[3].innerHTML);
	$("pieceCnt").update(data[4].innerHTML);

    var batchNm = data[6].innerHTML;
	var options = $$('select#batchNm option');

	for (var i = 0; i < options.length; i++) {
	    if(options[i].innerHTML == data[5].innerHTML) {
	    	options[i].selected = true;
	    	break;
	    }
	}

	//値を保持
	originValue = batchNm;

}


//ポップアップの決定ボタンクリックイベントハンドラ
function clickOKButton(window) {

	var batchNm = $F("batchNm");

	//値を設置
	var data = selectedRow.getElementsBySelector(".hiddenData");
	var cells = selectedRow.getElementsBySelector(".tableCell");

	// テキスト値取得
	var options = $A($('batchNm').getElementsByTagName('option'));
	options.each(function(option){
		if (option.value == batchNm) {
			data[5].innerHTML = option.innerHTML;
		}
	});

	if (batchNm != originValue) {

		setUpdatedCellColor(cells[5]);

		cells[5].update(data[5].innerHTML);
		data[6].update(batchNm);
		data[7].innerHTML = "1";


		//色を指定
		setRowColor(data[7].innerHTML, selectedRow)
	}

	window.close();
	dbclickFlag = 0;
	areaCoverElement.remove();
}

function clickCANCELButton(windows) {
	window.close();
	dbclickFlag = 0;
	areaCoverElement.remove();
}
