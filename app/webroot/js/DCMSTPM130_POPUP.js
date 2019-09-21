//選択行
var selectedRow;


var actionUrl = "";

var dbclickFlag;//ダブルクリック防止フラグ
var areaCoverElement = null;//エリアロックのDIV
var popUpView = null;


//初期化
function initPopup(url) {

	actionUrl = url;


	//該当行にイベントを付加
	var rows = $("lstTable").getElementsBySelector(".tableRow");

    for(i=0; i < rows.length; i++)
	{
//		var data = rows[i].getElementsBySelector(".hiddenData");

//		if (parseInt(data[1].innerHTML.trim(), 10) < 3) {

			rows[i].observe("click",clickPopUp);
//			rows[i].observe("mouseover",overRow);
//			rows[i].observe("mouseout",leaveRow);

//		} else {

//			rows[i].observe("click",clickDeletedRow);
//		}
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

//	var data = row.getElementsBySelector(".hiddenData");

	//更新の場合はそのまま
//	setRowColor(data[1].innerHTML, row)
	setRowColor("0", row)

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
	var cells = selectedRow.getElementsBySelector(".tableCell");

	var url = actionUrl;
	url += "?ymd=" +  $("current_ymd").value;
	url += "&homen=" +  encodeParam(cells[0].innerHTML);
	
	//ポップアップを読み込む
	new Ajax.Request( url, {
	  method : 'post',

	  onSuccess : viewPopUp,

	  onFailure : function( event )  {
	    location.href = "/DCMS/TPM130/index?return_cd=91&ymd=" +  $("current_ymd").value + "&message=" + Base64.encode(DCMSMessage.format("CMNE000101"));
	  },
	  onException : function( event, ex )  {
	    location.href = "/DCMS/TPM130/index?return_cd=92&ymd=" +  $("current_ymd").value + "&message=" + Base64.encode(DCMSMessage.format("CMNE000102"));
	  }
	});
}

function encodeParam(param) {
	// タブ、改行を取り除く
	return encodeURIComponent(param.replace(/\t|\r|\n/g, ''));
}


/* ポップアップ */
function viewPopUp(event) {

	//画面に配置
	var popup = Dialog.confirm(event.responseText,
				   {
					id: "popup",
					className: "alphacube",
					title: "作業進捗詳細",
					width:550,
					height:400,
					draggable: true,
					destroyOnclose: true,
					recenterAuto: false,
					buttonClass : "buttons",
					cancelLabel:"閉じる",
					onCancel: clickCANCELButton,
					showEffectOptions: {duration:0.2},
					hideEffectOptions: {duration:0}
				   });

	popup.setZIndex(1000);

	popup.content.setStyle({overflow:"visible"});

	popUpView = popup
}


//キャンセルボタンクリックイベントハンドラ
function clickCANCELButton(windows) {

	if ( popUpView != null ) {
		popUpView.close();
	}
	dbclickFlag = 0;
	areaCoverElement.remove();
	popUpView = null;
}

