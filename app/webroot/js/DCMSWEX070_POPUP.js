//選択行
var selectedRow;

//行の値保持
var orgValButuryo = "";
var orgValSuryoLine = "";

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
	
	
	var data = row.getElementsBySelector(".hiddenData");
	
	var KOTEI_KBN = data[8].innerHTML;
	var KBN_URIAGE = data[9].innerHTML;
	
	//何もなし、新規、更新フラグが立っている場合
	if (KBN_URIAGE == "01" || KOTEI_KBN == 1) {
		
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
	
	var KOTEI_KBN = data[8].innerHTML;
	var KBN_URIAGE = data[9].innerHTML;
	
	//何もなし、新規、更新フラグが立っている場合
	if (KBN_URIAGE == "01" || KOTEI_KBN == 1) {
		
		return;
		
	}

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
	
	var data = row.getElementsBySelector(".hiddenData");
	
	var KOTEI_KBN = data[8].innerHTML;
	var KBN_URIAGE = data[9].innerHTML;
	
	//何もなし、新規、更新フラグが立っている場合
	if (KBN_URIAGE == "01" || KOTEI_KBN == 1) {
		
		return;
		
	}
	
	dbclickFlag = 1;

	//端末遅い人用に、ポップアップがでるまでのカバーをつける
	areaCoverElement = createAreaCoverElement();
	$$("body")[0].insert(areaCoverElement);

	//ポップアップを読み込む
	new Ajax.Request( actionUrl, {
	  method : 'post',

	  onSuccess : viewPopUp,
	  
	  onFailure : function( event )  {
	    location.href = "/DCMS/WEX070/index?return_cd=91&message=" + Base64.encode(DCMSMessage.format("CMNE000101"));
	  },
	  onException : function( event, ex )  {
	    location.href = "/DCMS/WEX070/index?return_cd=92&message=" + Base64.encode(DCMSMessage.format("CMNE000102"));
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
					title: "実績登録",
					width:580,
					height:390,
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
	var data = selectedRow.getElementsBySelector(".hiddenData");

	$("ymd").update(cells[0].innerHTML.substr(0,4) + "年" +
					cells[0].innerHTML.substr(5,2) + "月" +
					cells[0].innerHTML.substr(8,2) + "日");

	$("bnriDaiNm").update(cells[1].innerHTML);
	$("bnriCyuNm").update(cells[2].innerHTML);
	$("bnriSaiNm").update(cells[3].innerHTML);
	$("tanka").update(cells[6].innerHTML);
	$("jikan").update(cells[7].innerHTML);
	$("ninzu").update(cells[8].innerHTML);


	$("buturyo").value = data[6].innerHTML;
	$("suryo_line").value = data[7].innerHTML;


	//値を保持
	orgValButuryo = data[6].innerHTML;
	orgValSuryoLine = data[7].innerHTML;


}


//更新ボタンクリックイベントハンドラ
function clickOKButton(window) {

	//値を設置
	//　下、両方親Viewの中
	var cells = selectedRow.getElementsBySelector(".tableCell");
	var data = selectedRow.getElementsBySelector(".hiddenData");

	// テキスト値取得
	// 　下、子Viewの中
	var suryo_line   = $F("suryo_line");
	var buturyo = $F("buturyo");


	//　入力チェック
	//必須チェック
	if (!DCMSValidation.notEmpty($F("buturyo"))) {
		alert(DCMSMessage.format("CMNE000001", "物量"));
		return;
	}
	
	if (!DCMSValidation.notEmpty($F("suryo_line"))) {
		alert(DCMSMessage.format("CMNE000001", "ライン数量"));
		return;
	}

	//数字チェック
	if (!DCMSValidation.numeric($F("buturyo"))) {
		alert(DCMSMessage.format("CMNE000002", "物量"));
		return;
	}
	
	//数字チェック
	if (!DCMSValidation.numeric($F("suryo_line"))) {
		alert(DCMSMessage.format("CMNE000002", "ライン数量"));
		return;
	}

	//最大桁数チェック
	if (!DCMSValidation.maxLength($F("buturyo"), 8)) {
		alert(DCMSMessage.format("CMNE000003", "物量",8));
		return;
	}
	
	if (!DCMSValidation.maxLength($F("suryo_line"), 8)) {
		alert(DCMSMessage.format("CMNE000003", "ライン数量",8));
		return;
	}


	//更新

	var flgUpdate  = "0";
	
	// 　入力前と変化した場合

	if (buturyo != orgValButuryo) {
		setUpdatedCellColor(cells[9]);		//項目のbackground変更

		cells[9].innerHTML = num3Format(buturyo);		//値のセット
		data[6].innerHTML = buturyo;
		flgUpdate  = "1";
	}
	
	if (suryo_line != orgValSuryoLine) {
		setUpdatedCellColor(cells[10]);		//項目のbackground変更

		cells[10].innerHTML = num3Format(suryo_line);		//値のセット
		data[7].innerHTML = suryo_line;
		flgUpdate  = "1";
	}


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


//キャンセルボタンクリックイベントハンドラ
function clickCANCELButton(windows) {
	
	window.close();
	dbclickFlag = 0;
	areaCoverElement.remove();
}
