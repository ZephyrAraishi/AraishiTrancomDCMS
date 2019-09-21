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


//ポップアップ表示と、行選択時に色を変えるための初期化関数
function initPopUp(url) {

	//ポップアップのURLの設定
	actionUrl = url;

	//行全体を取得
	var rows = $("lstTable").getElementsBySelector(".tableRow");

    for(i=0; i < rows.length; i++)
	{
		var data = rows[i].getElementsBySelector(".hiddenData");

		if (data.length < 5) {

			continue;
		}

		var kbnStatusKetu = data[5].innerHTML;

		//イベントを設定
		if (kbnStatusKetu.substr(0,1) == '1') {

    		rows[i].observe("click",clickPopUp);
    		rows[i].observe("mouseover",overRow);
    		rows[i].observe("mouseout",leaveRow);
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
	setRowColor(data[8].innerHTML, row);

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
	
	var row = Event.element(event)

	if (row.hasClassName("tableCell") == false) {

		while (true) {

			if (row.hasClassName("tableCell") == true) {

				break
			}
			
			row = row.up("div");
			
			if (row == null) {
				break;
			}
		}
	}

	if (row.hasClassName("nopopup")) {
		dbclickFlag = 0;
		return;
	}

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

	//ステータスが棚欠以外では表示しない
	var data = selectedRow.getElementsBySelector(".hiddenData");

    var kbnStatusKetu = data[5].innerHTML;
    if (kbnStatusKetu.substr(0,1) != '1') {
    	return;
    }

	//ポップアップを読み込む
	new Ajax.Request( actionUrl, {
	  method : 'post',

	  onSuccess : viewPopUp,

	  onFailure : function( event )  {
	    location.href = "/DCMS/WPO020/index?return_cd=91&message=" + Base64.encode(DCMSMessage.format("CMNE000101"));
	  },
	  onException : function( event, ex )  {
	    location.href = "/DCMS/WPO020/index?return_cd=92&message=" + Base64.encode(DCMSMessage.format("CMNE000102"));
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
					title: "ステータス変更",
					width:400,
					height:210,
					draggable: true,
					destroyOnclose: true,
					recenterAuto: false,
					buttonClass : "buttonsWPO02001",
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
	var cell = selectedRow.getElementsBySelector(".tableRow");
	var data = selectedRow.getElementsBySelector(".hiddenData");

	$("bango").update(data[0].innerHTML);
	$("batNM").update(data[2].innerHTML);
	$("sKanriNo").update(data[3].innerHTML);

    var kbnStatusKetu = data[5].innerHTML;
	var options = $$('select#kbnStatusKetu option');

	for (var i = 0; i < options.length; i++) {

	    if(options[i].innerHTML == data[4].innerHTML) {

	    	options[i].selected = true;
	    } else {
	    	if (data[7].innerHTML == '') {
	    		options[i].remove();
	    	}
	    }
	}

	//値を保持
	originValue = kbnStatusKetu;

}


//決定ボタンクリックイベントハンドラ
function clickOKButton(window) {

	var kbnStatusKetu = $F("kbnStatusKetu");

	//値を設置
	var cells = selectedRow.getElementsBySelector(".tableCell");
	var data = selectedRow.getElementsBySelector(".hiddenData");

	// テキスト値取得
	var kbnStatusKetuNm;
	
	var options = $A($('kbnStatusKetu').getElementsByTagName('option'));
	options.each(function(option){
	
		if (option.value == kbnStatusKetu) {
			kbnStatusKetuNm = option.innerHTML;
		}
		
	});

	if (kbnStatusKetu != originValue) {
	
		setUpdatedCellColor(cells[6]);
		
		cells[6].innerHTML = kbnStatusKetuNm;
		data[4].update(kbnStatusKetuNm);
		data[5].update(kbnStatusKetu);
		data[8].update("1");

		//色を指定
		setRowColor(data[8].innerHTML, selectedRow);
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
