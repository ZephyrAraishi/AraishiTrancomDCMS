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
	    location.href = "/DCMS/WEX110/index?return_cd=91&message=" + Base64.encode(DCMSMessage.format("CMNE000101"));
	  },
	  onException : function( event, ex )  {
	    location.href = "/DCMS/WEX110/index?return_cd=92&message=" + Base64.encode(DCMSMessage.format("CMNE000102"));
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
					title: "名称マスタ設定",
					width:600,
					height:420,
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
	
	popUpView = popup
		
}


//OKボタンクリックイベントハンドラ
function clickOKButton2(window) {


	//　入力チェック
	//必須チェック
	if (!DCMSValidation.notEmpty($F("mei_kbn"))) {
		alert(DCMSMessage.format("CMNE000001", "名称区分"));
		return;
	}
	if (!DCMSValidation.notEmpty($F("mei_cd"))) {
		alert(DCMSMessage.format("CMNE000001", "名称コード"));
		return;
	}

	//数字チェック
	var pattern = /^[-]?([1-9]\d*|0)(\.\d+)?$/;
	
	if ($F("val_free_num_1") != "" && !pattern.test($F("val_free_num_1"))) {
		alert(DCMSMessage.format("CMNE000002", "フリー数字１"));
		return;
	}

	//桁数チェック
	if ($F("val_free_num_2") != "" && !pattern.test($F("val_free_num_2"))) {
		alert(DCMSMessage.format("CMNE000002", "フリー数字２"));
		return;
	}
	
	if ($F("val_free_num_3") != "" && !pattern.test($F("val_free_num_3"))) {
		alert(DCMSMessage.format("CMNE000002", "フリー数字３"));
		return;
	}

	
	//更新
	
	//行作成
	var tableRow = new Element("div");
	tableRow.addClassName("tableRow row_dat");
	tableRow.setStyle({
		width:"1540px"
	});
	
	var tableCellBango = new Element("div");
	tableCellBango.addClassName("tableCell cell_dat");
	tableCellBango.setStyle({
		width:"60px"
	});
	tableCellBango.innerHTML = "新規";
	tableRow.insert(tableCellBango);
	
	//セル作成
	var tableCell = new Element("div");
	tableCell.addClassName("tableCell cell_dat cd");
	tableCell.setStyle({
		width:"80px"
	});
	tableRow.insert(tableCell);
	
	var tableCell = new Element("div");
	tableCell.addClassName("tableCell cell_dat cd");
	tableCell.setStyle({
		width:"80px"
	});
	tableRow.insert(tableCell);
	
	tableCell = new Element("div");
	tableCell.addClassName("tableCell cell_dat nm");
	tableCell.setStyle({
		width:"150px"
	});
	tableRow.insert(tableCell);
	
	tableCell = new Element("div");
	tableCell.addClassName("tableCell cell_dat nm");
	tableCell.setStyle({
		width:"150px"
	});
	tableRow.insert(tableCell);
	
	tableCell = new Element("div");
	tableCell.addClassName("tableCell cell_dat kbn");
	tableCell.setStyle({
		width:"150px"
	});
	tableRow.insert(tableCell);
	
	tableCell = new Element("div");
	tableCell.addClassName("tableCell cell_dat kbn");
	tableCell.setStyle({
		width:"150px"
	});
	tableRow.insert(tableCell);
	
	tableCell = new Element("div");
	tableCell.addClassName("tableCell cell_dat kbn");
	tableCell.setStyle({
		width:"150px"
	});
	tableRow.insert(tableCell);
	
	tableCell = new Element("div");
	tableCell.addClassName("tableCell cell_dat kbn");
	tableCell.setStyle({
		width:"150px"
	});
	tableRow.insert(tableCell);
	
	tableCell = new Element("div");
	tableCell.addClassName("tableCell cell_dat kbn");
	tableCell.setStyle({
		width:"100px"
	});
	tableRow.insert(tableCell);
	
	tableCell = new Element("div");
	tableCell.addClassName("tableCell cell_dat kbn");
	tableCell.setStyle({
		width:"100px"
	});
	tableRow.insert(tableCell);
	
	tableCell = new Element("div");
	tableCell.addClassName("tableCell cell_dat kbn");
	tableCell.setStyle({
		width:"100px"
	});
	tableRow.insert(tableCell);


	var hiddenData = new Element("div");
	hiddenData.addClassName("hiddenData LINE_COUNT");
	tableRow.insert(hiddenData);

	var hiddenData = new Element("div");
	hiddenData.addClassName("hiddenData CHANGED");
	tableRow.insert(hiddenData);


	//値を設置
	//　下、両方親Viewの中
	var cells = tableRow.getElementsBySelector(".tableCell");
	var data = tableRow.getElementsBySelector(".hiddenData");

	
	// テキスト値取得
	var mei_kbn = $F("mei_kbn");
	var mei_cd = $F("mei_cd");
	var mei_1 = $F("mei_1");
	var mei_2 = $F("mei_2");
	var mei_3 = $F("mei_3");
	var val_free_str_1 = $F("val_free_str_1");
	var val_free_str_2 = $F("val_free_str_2");
	var val_free_str_3 = $F("val_free_str_3");
	var val_free_num_1 = $F("val_free_num_1");
	var val_free_num_2 = $F("val_free_num_2");
	var val_free_num_3 = $F("val_free_num_3");


	//変更項目の処理
	cells[1].innerHTML = mei_kbn;
	cells[2].innerHTML = mei_cd;
	cells[3].innerHTML = mei_1;
	cells[4].innerHTML = mei_2;
	cells[5].innerHTML = mei_3;
	cells[6].innerHTML = val_free_str_1;
	cells[7].innerHTML = val_free_str_2;
	cells[8].innerHTML = val_free_str_3;
	cells[9].innerHTML = val_free_num_1;
	cells[10].innerHTML = val_free_num_2;
	cells[11].innerHTML = val_free_num_3;

	data[0].innerHTML = "0";
	data[1].innerHTML = "2";

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
