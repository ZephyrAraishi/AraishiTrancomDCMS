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
	    location.href = "/DCMS/WEX030/index?return_cd=91&message=" + Base64.encode(DCMSMessage.format("CMNE000101"));
	  },
	  onException : function( event, ex )  {
	    location.href = "/DCMS/WEX030/index?return_cd=92&message=" + Base64.encode(DCMSMessage.format("CMNE000102"));
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
					title: "スタッフ許可マスタ",
					width:900,
					height:240,
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
	if (!DCMSValidation.notEmpty($F("staff_cd"))) {
		alert(DCMSMessage.format("CMNE000001", "スタッフコード"));
		return;
	}
	if (!DCMSValidation.notEmpty($F("subsystem_id"))) {
		alert(DCMSMessage.format("CMNE000001", "サブシステムID"));
		return;
	}
	if (!DCMSValidation.notEmpty($F("kino_id"))) {
		alert(DCMSMessage.format("CMNE000001", "機能ID"));
		return;
	}

	
	//更新
	
	//行作成
	var tableRow = new Element("div");
	tableRow.addClassName("tableRow row_dat");
	tableRow.setStyle({
		width:"1230px"
	});
	
	var tableCellBango = new Element("div");
	tableCellBango.addClassName("tableCellBango cell_dat");
	tableCellBango.setStyle({
		width:"60px"
	});
	tableCellBango.innerHTML = "新規";
	tableRow.insert(tableCellBango);
	
	//6個セル作成
	var tableCell = new Element("div");
	tableCell.addClassName("tableCell cell_dat cd");
	tableCell.setStyle({
		width:"100px"
	});
	tableRow.insert(tableCell);
	
	tableCell = new Element("div");
	tableCell.addClassName("tableCell cell_dat nm");
	tableCell.setStyle({
		width:"300px"
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
		width:"415px"
	});
	tableRow.insert(tableCell);
	
	//データDIVを3個配置
	var hiddenData = new Element("div");
	hiddenData.addClassName("hiddenData LINE_COUNT");
	tableRow.insert(hiddenData);
	
	var hiddenData = new Element("div");
	hiddenData.addClassName("hiddenData CHANGED");
	tableRow.insert(hiddenData);
	
	var hiddenData = new Element("div");

	//値を設置
	//　下、両方親Viewの中
	var cells = tableRow.getElementsBySelector(".tableCell");
	var data = tableRow.getElementsBySelector(".hiddenData");

	
	// テキスト値取得
	// 　下、子Viewの中
	var staff_cd = $F("staff_cd");
	var staff_nm = $F("staff_nm");
	var subsystem_id = $F("subsystem_id");
	var kino_id = $F("kino_id");

	//変更項目の処理
	cells[0].innerHTML = staff_cd;
	cells[1].innerHTML = staff_nm;
	cells[2].innerHTML = subsystem_id;
	cells[3].innerHTML = kino_id;
	cells[5].innerHTML = "0";

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
