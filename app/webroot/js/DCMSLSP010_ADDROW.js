var actionUrl2;

function initAddRow(url) {

	actionUrl2 = url;
	
	if ($("addButton") != null) {
		$("addButton").observe("click",clickAddButton);
	}

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
//	areaCoverElement = new Element("div");
//	var height = Math.max.apply( null, [document.body.clientHeight ,
//	                                    document.body.scrollHeight,
//	                                    document.documentElement.scrollHeight,
//	                                    document.documentElement.clientHeight] ); 
	
//	areaCoverElement.setStyle({
//		height: height + "px"
//	});
//	areaCoverElement.addClassName("displayCover");
	areaCoverElement = createAreaCoverElement();
	$$("body")[0].insert(areaCoverElement);

	
	//ポップアップを読み込む
	new Ajax.Request( actionUrl2, {
	  method : 'post',

	  onSuccess : viewInsertPopUp,
	  
	  onFailure : function( event )  {
	    location.href = "/DCMS/LSP010/index?return_cd=91&message=" + Base64.encode(DCMSMessage.format("CMNE000101"));
	  },
	  onException : function( event, ex )  {
	    location.href = "/DCMS/LSP010/index?return_cd=92&message=" + Base64.encode(DCMSMessage.format("CMNE000102"));
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
					title: "勤務情報設定",
					width:650,
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
	
	//分類を設定
	initBunruiPopup();

	//ポップアップを保持
	popUpView = popup

	//日付変更時のイベント
	$("syukin1").observe("change",changeCheckBox);
	$("syukin2").observe("change",changeCheckBox);
	$("syukin3").observe("change",changeCheckBox);
	$("syukin4").observe("change",changeCheckBox);
	$("syukin5").observe("change",changeCheckBox);
	$("syukin6").observe("change",changeCheckBox);
	$("syukin7").observe("change",changeCheckBox);

	//見出し部分
	var resultRows = $("lstTable").getElementsBySelector(".tableRowTitle");
	var cellsTtl = resultRows[0].getElementsBySelector(".tableCell");
	var dataTtl = resultRows[0].getElementsBySelector(".hiddenData");

	var get = getRequest();

	//メイン画面がら検索値取得
	$koteiCd = $F("koteiCd_hidden");		//工程コード
	$koteiNm = $F("koteiText_hidden");		//工程名称

	//検索値により、表示・非表示切り替えと値セット
	if ($koteiCd != ""){
		//工程入力時、ポップアップは未入力とする
		$("koteiIn").hide(); 
		$("koteiDsp").show(); 

		$("kouteiCdDsp").value   = $koteiCd;
		$("kouteiNmDsp").value   = $koteiNm;
		$("kouteiFlag").value   = "0";
	} else {
		//工程未入力時、ポップアップは入力とする
		$("koteiIn").show(); 
		$("koteiDsp").hide(); 

		$("kouteiFlag").value   = "1";
	}

	//メイン画面がら検索値取得
	$staffCd = $("staff_cd_srh_hidden").value;		//staffコード
	$staffNm = $("staff_nm_srh_hidden").value;		//staff名称

	//検索値により、表示・非表示切り替えと値セット
	if ($staffCd != ""){
		//staff入力時、ポップアップは未入力とする
		$("staff_btn").hide(); 
		$("staff_cd").hide(); 
		$("staff_nm").hide(); 

		$("staffDsp1").show(); 
		$("staffDsp2").show(); 

		$("staffCdDsp").value   = $staffCd;
		$("staffNmDsp").value   = $staffNm;
		$("staffFlag").value   = "0";
	} else {
		//staff未入力時、ポップアップは入力とする
		$("staff_btn").show(); 
		$("staff_cd").show(); 
		$("staff_nm").show(); 

		$("staffDsp1").hide(); 
		$("staffDsp2").hide(); 

		$("staffFlag").value   = "1";
	}



	//曜日表示
	$("Youbi1").update(cellsTtl[5].innerHTML);
	$("Youbi2").update(cellsTtl[6].innerHTML);
	$("Youbi3").update(cellsTtl[7].innerHTML);
	$("Youbi4").update(cellsTtl[8].innerHTML);
	$("Youbi5").update(cellsTtl[9].innerHTML);
	$("Youbi6").update(cellsTtl[10].innerHTML);
	$("Youbi7").update(cellsTtl[11].innerHTML);

	// 自動設定をONに設定
	$("auto1").checked  = true;
	$("auto2").checked  = true;
	$("auto3").checked  = true;
	$("auto4").checked  = true;
	$("auto5").checked  = true;
	$("auto6").checked  = true;
	$("auto7").checked  = true;
}


//テーブルのオーバーイベントハンドラ
//フォーカスマークをつける
function changeCheckBox(event) {

	if (Event.element(event).id == "syukin1" 
	&&  $("syukin1").checked  == true) {
		$("stTime1").value  = "";
		$("edTime1").value  = "";
	}
	if (Event.element(event).id == "syukin2" 
	&&  $("syukin2").checked  == true) {
		$("stTime2").value  = "";
		$("edTime2").value  = "";
	}
	if (Event.element(event).id == "syukin3" 
	&&  $("syukin3").checked  == true) {
		$("stTime3").value  = "";
		$("edTime3").value  = "";
	}
	if (Event.element(event).id == "syukin4" 
	&&  $("syukin4").checked  == true) {
		$("stTime4").value  = "";
		$("edTime4").value  = "";
	}
	if (Event.element(event).id == "syukin5" 
	&&  $("syukin5").checked  == true) {
		$("stTime5").value  = "";
		$("edTime5").value  = "";
	}
	if (Event.element(event).id == "syukin6" 
	&&  $("syukin6").checked  == true) {
		$("stTime6").value  = "";
		$("edTime6").value  = "";
	}
	if (Event.element(event).id == "syukin7" 
	&&  $("syukin7").checked  == true) {
		$("stTime7").value  = "";
		$("edTime7").value  = "";
	}

}


//OKボタンクリックイベントハンドラ
function clickOKButton2(window) {

	var bnriDaiNm = "";
	var bnriCyuNm = "";
	var bnriDaiCd = "";
	var bnriCyuCd = "";
	var searchPos = 0;


	if ($F("kouteiFlag") == "1") {

		//工程
		var koteiPopups = $$("body")[0].getElementsBySelector(".koteiPopup");
		//工程データの取得
		var koteiPopup = koteiPopups[0];
		//要素取得
		var selectElement = koteiPopup.getElementsBySelector(".koteiCd")[0];
		//工程名
		if ("" != selectElement.getValue().trim()){
			bnriDaiNm = koteiPopup.getElementsBySelector(".koteiDaiComboPopup")[0].getValue().trim();
			bnriCyuNm = koteiPopup.getElementsBySelector(".koteiCyuComboPopup")[0].getValue().trim();
			bnriDaiCd = selectElement.getValue().trim().substr(0,3);
			bnriCyuCd = selectElement.getValue().trim().substr(4,3);
		}

	} else {
		if ("" != $F("kouteiCdDsp").trim()){

			searchPos = $F("kouteiNmDsp").indexOf(">");
			if (searchPos >= 0) {
				bnriDaiNm = $F("kouteiNmDsp").substr(0, searchPos);
				bnriCyuNm = $F("kouteiNmDsp").substr(searchPos + 1);
			}
			bnriDaiCd = $F("kouteiCdDsp").trim().substr(0,3);
			bnriCyuCd = $F("kouteiCdDsp").trim().substr(4,3);
		}
	}


	if ($F("stTime1").length == 3) {
		$("stTime1").value = "0" + $F("stTime1");
	}
	if ($F("stTime2").length == 3) {
		$("stTime2").value = "0" + $F("stTime2");
	}
	if ($F("stTime3").length == 3) {
		$("stTime3").value = "0" + $F("stTime3");
	}
	if ($F("stTime4").length == 3) {
		$("stTime4").value = "0" + $F("stTime4");
	}
	if ($F("stTime5").length == 3) {
		$("stTime5").value = "0" + $F("stTime5");
	}
	if ($F("stTime6").length == 3) {
		$("stTime6").value = "0" + $F("stTime6");
	}
	if ($F("stTime7").length == 3) {
		$("stTime7").value = "0" + $F("stTime7");
	}

	if ($F("edTime1").length == 3) {
		$("edTime1").value = "0" + $F("edTime1");
	}
	if ($F("edTime2").length == 3) {
		$("edTime2").value = "0" + $F("edTime2");
	}
	if ($F("edTime3").length == 3) {
		$("edTime3").value = "0" + $F("edTime3");
	}
	if ($F("edTime4").length == 3) {
		$("edTime4").value = "0" + $F("edTime4");
	}
	if ($F("edTime5").length == 3) {
		$("edTime5").value = "0" + $F("edTime5");
	}
	if ($F("edTime6").length == 3) {
		$("edTime6").value = "0" + $F("edTime6");
	}
	if ($F("edTime7").length == 3) {
		$("edTime7").value = "0" + $F("edTime7");
	}

	// テキスト値取得
	// 　下、子Viewの中

	if ($F("staffFlag") == "1") {
		var staffCd    = $("staff_cd").innerHTML.trim();
		var staffNm    = $("staff_nm").innerHTML.trim();
		//スタッフ検索で値がセットされてしまうので、強制でクリアする
		$("staff_cd_hidden").value = "";		//staffコード
		$("staff_nm_hidden").value = "";		//staff名称
	} else {
		var staffCd    = $F("staffCdDsp").trim();
		var staffNm    = $F("staffNmDsp").trim();
	}

	var stTime = Array();
	var edTime = Array();
	var syukin = Array();
	var auto = Array();

	stTime[0]    = $F("stTime1");
	edTime[0]    = $F("edTime1");
	syukin[0]    = $("syukin1").checked;
	auto[0]      = $("auto1").checked;
	stTime[1]    = $F("stTime2");
	edTime[1]    = $F("edTime2");
	syukin[1]    = $("syukin2").checked;
	auto[1]      = $("auto2").checked;
	stTime[2]    = $F("stTime3");
	edTime[2]    = $F("edTime3");
	syukin[2]    = $("syukin3").checked;
	auto[2]      = $("auto3").checked;
	stTime[3]    = $F("stTime4");
	edTime[3]    = $F("edTime4");
	syukin[3]    = $("syukin4").checked;
	auto[3]      = $("auto4").checked;
	stTime[4]    = $F("stTime5");
	edTime[4]    = $F("edTime5");
	syukin[4]    = $("syukin5").checked;
	auto[4]      = $("auto5").checked;
	stTime[5]    = $F("stTime6");
	edTime[5]    = $F("edTime6");
	syukin[5]    = $("syukin6").checked;
	auto[5]      = $("auto6").checked;
	stTime[6]    = $F("stTime7");
	edTime[6]    = $F("edTime7");
	syukin[6]    = $("syukin7").checked;
	auto[6]      = $("auto7").checked;

	//　入力チェック
	//スタッフ　未入力
	if (!DCMSValidation.notEmpty(staffCd)) {
		alert(DCMSMessage.format("CMNE000001", "スタッフ"));
		return;
	}
	//工程　未入力
	if (!DCMSValidation.notEmpty(bnriDaiCd)) {
		alert(DCMSMessage.format("CMNE000001", "工程"));
		return;
	}


	for (var i=0; i <= 6; i++) {

		var strI = String(i + 1);

		//時刻
		if (syukin[i]  == true) {
			if (DCMSValidation.notEmpty(stTime[i]) 
			||  DCMSValidation.notEmpty(edTime[i])) {
				//休日チェック時、入力してたらダメ
				alert(DCMSMessage.format("CMNE000007", "開始時刻" + strI + "・終了時刻" + strI));
				return;
			}
		} else {
			if (DCMSValidation.notEmpty(stTime[i]) 
			||  DCMSValidation.notEmpty(edTime[i])) {
				if (!DCMSValidation.notEmpty(stTime[i])) {
					//片方入力してたら、入力してないとダメ
					alert(DCMSMessage.format("CMNE000001", "開始時刻" + strI));
					return;
				}
				if (!DCMSValidation.notEmpty(edTime[i])) {
					//片方入力してたら、入力してないとダメ
					alert(DCMSMessage.format("CMNE000001", "終了時刻" + strI));
					return;
				}
				//フォーマットが不正
				if (stTime[i].trim() != "" && !stTime[i].match(/^[0-9]{4}/)) {
					alert(DCMSMessage.format("CMNE000004", "開始時刻" + strI, "4"));
					return;
				}
				if (edTime[i].trim() != "" && !edTime[i].match(/^[0-9]{4}/)) {
					alert(DCMSMessage.format("CMNE000004", "終了時刻" + strI, "4"));
					return;
				}
				//時刻が不正
				if (stTime[i].trim() < 0000 
				||  stTime[i].trim() > 2400 ) {
					alert(DCMSMessage.format("CMNE000007", "開始時刻" + strI));	//		時の方
					return;
				}
				if (stTime[i].trim().substr(2) < 00 
				||  stTime[i].trim().substr(2) > 59 ) {
					alert(DCMSMessage.format("CMNE000007", "開始時刻" + strI));	//		分の方
					return;
				}
				if (edTime[i].trim() < 0000 
				||  edTime[i].trim() > 3100 ) {
					alert(DCMSMessage.format("CMNE000007", "終了時刻" + strI));	//		時の方
					return;
				}
				if (edTime[i].trim().substr(2) < 00 
				||  edTime[i].trim().substr(2) > 59 ) {
					alert(DCMSMessage.format("CMNE000007", "終了時刻" + strI));	//		分の方
					return;
				}
				//開始時刻は、終了時刻より前
				if (parseInt(stTime[i].trim(), 10) >= parseInt(edTime[i], 10)) {
					alert(DCMSMessage.format("CMNE000008", "終了時刻" + strI, "開始時刻" + strI));
					return;
				}
			}
		}

	}


	//前後重なりチェック
	for (var i=0; i <= 6; i++) {

		var strI = String(i + 1);

		//時刻
		if (syukin[i]  == false) {
			var tmpTimeSt =  String(parseInt(stTime[i].trim().substr(0,2),10) + 24) + stTime[i].trim().substr(2);
	
			if (parseInt(edTime[i].trim(), 10) >= 2400) {
				var tmpTimeEd =  String(parseInt(edTime[i].trim().substr(0,2),10) - 24) + edTime[i].trim().substr(2);
				if (tmpTimeEd.length == 3) {
					tmpTimeEd = "0" + tmpTimeEd;
				}
			} else {
				var tmpTimeEd =  "0000";
			}
	
			//前日の終了時刻と開始時刻をチェック
			if (i  != 0) {
				j = i - 1;
				var strJ = String(j + 1);
				if (parseInt(edTime[j].trim(), 10) > parseInt(tmpTimeSt, 10)
				&&  syukin[j]  == false) {
					alert(DCMSMessage.format("CMNE000007", "終了時刻" + strJ + "　または　開始時刻" + strI));
					return;
				}
			}
			//翌日の開始時刻と終了時刻をチェック
			if (i  != 6) {
				j = i + 1;
				var strJ = String(j + 1);
				if (parseInt(stTime[j].trim(), 10) < parseInt(tmpTimeEd, 10)
				&&  syukin[j]  == false) {
					alert(DCMSMessage.format("CMNE000007", "終了時刻" + strI + "　または　開始時刻" + strJ));
					return;
				}
			}
		}

	}

	
	//更新
	
	//行作成
	var tableRow = new Element("div");
	tableRow.addClassName("tableRow row_dat");
	tableRow.setStyle({
		width:"1370px"
	});
	
	//12個セル作成
	var tableCell = new Element("div");
	tableCell.addClassName("tableCell cell_dat");
	tableCell.setStyle({
		width:"60px"
	});
	tableRow.insert(tableCell);
	
	var tableCell = new Element("div");
	tableCell.addClassName("tableCell cell_dat cd");
	tableCell.setStyle({
		width:"90px"
	});
	tableRow.insert(tableCell);
	
	var tableCell = new Element("div");
	tableCell.addClassName("tableCell cell_dat nm");
	tableCell.setStyle({
		width:"130px"
	});
	tableRow.insert(tableCell);

	tableCell = new Element("div");
	tableCell.addClassName("tableCell cell_dat nm");
	tableCell.setStyle({
		width:"100px"
	});
	tableRow.insert(tableCell);

	tableCell = new Element("div");
	tableCell.addClassName("tableCell cell_dat nm");
	tableCell.setStyle({
		width:"100px"
	});
	tableRow.insert(tableCell);
	
	tableCell = new Element("div");
	tableCell.addClassName("tableCell cell_dat date");
	tableCell.setStyle({
		width:"110px"
	});
	tableRow.insert(tableCell);
	
	tableCell = new Element("div");
	tableCell.addClassName("tableCell cell_dat date");
	tableCell.setStyle({
		width:"110px"
	});
	tableRow.insert(tableCell);
	
	tableCell = new Element("div");
	tableCell.addClassName("tableCell cell_dat date");
	tableCell.setStyle({
		width:"110px"
	});
	tableRow.insert(tableCell);
	
	tableCell = new Element("div");
	tableCell.addClassName("tableCell cell_dat date");
	tableCell.setStyle({
		width:"110px"
	});
	tableRow.insert(tableCell);
	
	tableCell = new Element("div");
	tableCell.addClassName("tableCell cell_dat date");
	tableCell.setStyle({
		width:"110px"
	});
	tableRow.insert(tableCell);
	
	tableCell = new Element("div");
	tableCell.addClassName("tableCell cell_dat date");
	tableCell.setStyle({
		width:"110px"
	});
	tableRow.insert(tableCell);
	
	tableCell = new Element("div");
	tableCell.addClassName("tableCell cell_dat date");
	tableCell.setStyle({
		width:"110px"
	});
	tableRow.insert(tableCell);

	//データDIVを36個配置
//	for (var i=0 ; i< 2; i++) {
	
		var hiddenData = new Element("div");
		hiddenData.addClassName("hiddenData LINE_COUNT");
		tableRow.insert(hiddenData);
	
		var hiddenData = new Element("div");
		hiddenData.addClassName("hiddenData CHANGED");
		tableRow.insert(hiddenData);
	
		var hiddenData = new Element("div");
		hiddenData.addClassName("hiddenData BNRI_DAI_CD");
		tableRow.insert(hiddenData);
	
		var hiddenData = new Element("div");
		hiddenData.addClassName("hiddenData BNRI_CYU_CD");
		tableRow.insert(hiddenData);
	
		var hiddenData = new Element("div");
		hiddenData.addClassName("hiddenData TIME_SYUGYO_ST_1");
		tableRow.insert(hiddenData);
	
		var hiddenData = new Element("div");
		hiddenData.addClassName("hiddenData TIME_SYUGYO_ED_1");
		tableRow.insert(hiddenData);
	
		var hiddenData = new Element("div");
		hiddenData.addClassName("hiddenData SYUKIN_FLG_1");
		tableRow.insert(hiddenData);
	
		var hiddenData = new Element("div");
		hiddenData.addClassName("hiddenData TIME_SYUGYO_ST_2");
		tableRow.insert(hiddenData);
	
		var hiddenData = new Element("div");
		hiddenData.addClassName("hiddenData TIME_SYUGYO_ED_2");
		tableRow.insert(hiddenData);
	
		var hiddenData = new Element("div");
		hiddenData.addClassName("hiddenData SYUKIN_FLG_2");
		tableRow.insert(hiddenData);
	
		var hiddenData = new Element("div");
		hiddenData.addClassName("hiddenData TIME_SYUGYO_ST_3");
		tableRow.insert(hiddenData);
	
		var hiddenData = new Element("div");
		hiddenData.addClassName("hiddenData TIME_SYUGYO_ED_3");
		tableRow.insert(hiddenData);
	
		var hiddenData = new Element("div");
		hiddenData.addClassName("hiddenData SYUKIN_FLG_3");
		tableRow.insert(hiddenData);
	
		var hiddenData = new Element("div");
		hiddenData.addClassName("hiddenData TIME_SYUGYO_ST_4");
		tableRow.insert(hiddenData);
	
		var hiddenData = new Element("div");
		hiddenData.addClassName("hiddenData TIME_SYUGYO_ED_4");
		tableRow.insert(hiddenData);
	
		var hiddenData = new Element("div");
		hiddenData.addClassName("hiddenData SYUKIN_FLG_4");
		tableRow.insert(hiddenData);
	
		var hiddenData = new Element("div");
		hiddenData.addClassName("hiddenData TIME_SYUGYO_ST_5");
		tableRow.insert(hiddenData);
	
		var hiddenData = new Element("div");
		hiddenData.addClassName("hiddenData TIME_SYUGYO_ED_5");
		tableRow.insert(hiddenData);
	
		var hiddenData = new Element("div");
		hiddenData.addClassName("hiddenData SYUKIN_FLG_5");
		tableRow.insert(hiddenData);
	
		var hiddenData = new Element("div");
		hiddenData.addClassName("hiddenData TIME_SYUGYO_ST_6");
		tableRow.insert(hiddenData);
	
		var hiddenData = new Element("div");
		hiddenData.addClassName("hiddenData TIME_SYUGYO_ED_6");
		tableRow.insert(hiddenData);
	
		var hiddenData = new Element("div");
		hiddenData.addClassName("hiddenData SYUKIN_FLG_6");
		tableRow.insert(hiddenData);
	
		var hiddenData = new Element("div");
		hiddenData.addClassName("hiddenData TIME_SYUGYO_ST_7");
		tableRow.insert(hiddenData);
	
		var hiddenData = new Element("div");
		hiddenData.addClassName("hiddenData TIME_SYUGYO_ED_7");
		tableRow.insert(hiddenData);
	
		var hiddenData = new Element("div");
		hiddenData.addClassName("hiddenData SYUKIN_FLG_7");
		tableRow.insert(hiddenData);
	
		var hiddenData = new Element("div");
		hiddenData.addClassName("hiddenData KOUTEI_INP_FLG");
		tableRow.insert(hiddenData);
	
		var hiddenData = new Element("div");
		hiddenData.addClassName("hiddenData STOK_SYUGYO_ST_1");
		tableRow.insert(hiddenData);
	
		var hiddenData = new Element("div");
		hiddenData.addClassName("hiddenData STOK_SYUGYO_ST_2");
		tableRow.insert(hiddenData);
	
		var hiddenData = new Element("div");
		hiddenData.addClassName("hiddenData STOK_SYUGYO_ST_3");
		tableRow.insert(hiddenData);
	
		var hiddenData = new Element("div");
		hiddenData.addClassName("hiddenData STOK_SYUGYO_ST_4");
		tableRow.insert(hiddenData);
	
		var hiddenData = new Element("div");
		hiddenData.addClassName("hiddenData STOK_SYUGYO_ST_5");
		tableRow.insert(hiddenData);
	
		var hiddenData = new Element("div");
		hiddenData.addClassName("hiddenData STOK_SYUGYO_ST_6");
		tableRow.insert(hiddenData);
	
		var hiddenData = new Element("div");
		hiddenData.addClassName("hiddenData STOK_SYUGYO_ST_7");
		tableRow.insert(hiddenData);
	
		var hiddenData = new Element("div");
		hiddenData.addClassName("hiddenData SEQ_NO");
		tableRow.insert(hiddenData);
	
		var hiddenData = new Element("div");
		hiddenData.addClassName("hiddenData COPY_FLG");
		tableRow.insert(hiddenData);
	
		var hiddenData = new Element("div");
		hiddenData.addClassName("hiddenData STAFF_NAME");
		tableRow.insert(hiddenData);
		
		var hiddenData = new Element("div");
		hiddenData.addClassName("hiddenData AUTO_SETUP_1");
		hiddenData.innerHTML = auto[0] ? 1 : 0; 
		tableRow.insert(hiddenData);
	
		var hiddenData = new Element("div");
		hiddenData.addClassName("hiddenData AUTO_SETUP_2");
		hiddenData.innerHTML = auto[1] ? 1 : 0;
		tableRow.insert(hiddenData);
	
		var hiddenData = new Element("div");
		hiddenData.addClassName("hiddenData AUTO_SETUP_3");
		hiddenData.innerHTML = auto[2] ? 1 : 0;
		tableRow.insert(hiddenData);
	
		var hiddenData = new Element("div");
		hiddenData.addClassName("hiddenData AUTO_SETUP_4");
		hiddenData.innerHTML = auto[3] ? 1 : 0;
		tableRow.insert(hiddenData);
	
		var hiddenData = new Element("div");
		hiddenData.addClassName("hiddenData AUTO_SETUP_5");
		hiddenData.innerHTML = auto[4] ? 1 : 0;
		tableRow.insert(hiddenData);
	
		var hiddenData = new Element("div");
		hiddenData.addClassName("hiddenData AUTO_SETUP_6");
		hiddenData.innerHTML = auto[5] ? 1 : 0;
		tableRow.insert(hiddenData);
	
		var hiddenData = new Element("div");
		hiddenData.addClassName("hiddenData AUTO_SETUP_7");
		hiddenData.innerHTML = auto[6] ? 1 : 0;
		tableRow.insert(hiddenData);
	
	

//	}




	//値を設置
	//　下、両方親Viewの中
	var cells = tableRow.getElementsBySelector(".tableCell");
	var data = tableRow.getElementsBySelector(".hiddenData");

	
	//変更項目の処理
	cells[0].innerHTML = "新規";
	cells[1].innerHTML = staffCd;
	cells[2].innerHTML = staffNm;
	cells[3].innerHTML = bnriDaiNm;
	cells[4].innerHTML = bnriCyuNm;


	for (var i=0; i <= 6; i++) {

		//　時刻
		var displayCell  = i + 5;
		if (syukin[i] == true) {
			cells[displayCell].innerHTML = "休";		//値のセット
		} else {
			if (stTime[i] == "") {
				cells[displayCell].innerHTML = "";		//値のセット
			} else {
				cells[displayCell].innerHTML = stTime[i].substr(0,2) + ":" + stTime[i].substr(2) + "～" + edTime[i].substr(0,2) + ":" + edTime[i].substr(2);		//値のセット
			}
		}

	}


	data[0].innerHTML = "0";
	data[1].innerHTML = "2";	//新規フラグ
	data[2].innerHTML = bnriDaiCd;
	data[3].innerHTML = bnriCyuCd;

	data[25].innerHTML = $F("kouteiFlag");
	data[33].innerHTML = 0;
	data[34].innerHTML = 0;
	data[35].innerHTML = staffNm;

	for (var i=0; i <= 6; i++) {

		var j  = (i+1) * 3 + 1;
		data[j].innerHTML  = stTime[i];		//値のセット
		var j  = (i+1) * 3 + 2;
		data[j].innerHTML  = edTime[i];		//値のセット
		if (syukin[i] == true) {
			var j  = (i+1) * 3 + 3;
			data[j].innerHTML  = 1;		//値のセット
		} else {
			var j  = (i+1) * 3 + 3;
			data[j].innerHTML  = 0;		//値のセット
		}

	}

	//修正前のＤＢ内、開始時刻なので、セットしてはダメ
	for (var i=0; i <= 6; i++) {
		var j  = i + 26;
		data[j].innerHTML  = "";		//値のセット
	}


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





//----------------------------　コンボ ----------------------------

// 選択した内容をテキストボックスに反映
function setMenuProcessPopup( dai_value, cyu_value, sai_value, koutei_dai, koutei_cyu, koutei_sai , koteiNo){
	// 値格納
	var processVal = '';
	var processDaiNmVal = '';
	var processCyuNmVal = '';
	
	// 値の判定
	if((koutei_dai != '') && (koutei_cyu != '')){
	
		processVal = dai_value + '_' + cyu_value + '_' + sai_value;
//		processNmVal = koutei_sai;
		processDaiNmVal = koutei_dai;
		processCyuNmVal = koutei_cyu;
	}
	
	var top = $('ul_class_' + koteiNo);
	
	while (true) {
	
		if (top.hasClassName("koteiPopup") == true) {
			
			break;
		}

		top = top.up("div");
		
		if (top == null) {
		
			return;
		}
	}
	
	// hiddenに値をセット
	top.getElementsBySelector('.koteiCd')[0].setValue(processVal);

	
	// 値を代入する
	top.getElementsBySelector('.koteiDaiComboPopup')[0].setValue(processDaiNmVal);
	top.getElementsBySelector('.koteiCyuComboPopup')[0].setValue(processCyuNmVal);
	
	//　メニューを消す
	menuDispPopup(koteiNo);
	
}

var menuThreadObjArray = new Array();
var openCodeCyuArray = new Array();
var openCodeSaiArray = new Array();


function menuDispPopup(koteiNo) {

	var bodyHeight = Math.max.apply( null, [document.body.clientHeight ,
											document.body.scrollHeight,
											document.documentElement.scrollHeight,
											document.documentElement.clientHeight] ); 
	
	var scrollTop = document.viewport.getScrollOffsets().top;
	
	bodyHeight += scrollTop;

	// 現状のメニュー描画状態確認
	if($('ul_class_' + koteiNo).style.display == 'none'){
		// メニューの表示
		$('ul_class_' + koteiNo).style.display = 'block';

	}else{
		// メニューの非表示
		$('ul_class_' + koteiNo).style.display = 'none';
	}
	
	
	// class情報を取得(工程:中)
	var outProcessClass = $('ul_class_' + koteiNo).getElementsBySelector('.ul_second');
	
	// 工程(中)情報の表示を非表示にする
	for (var i=0; i < outProcessClass.length; i++) {

		outProcessClass[i].style.display = 'none';

	}
	
	// class情報を取得(工程:細)
	outProcessClass = $('ul_class_' + koteiNo).getElementsBySelector('.ul_third');
	
	// 工程(細)情報の表示をすべて非表示にする
	for (var i=0; i < outProcessClass.length; i++) {

		outProcessClass[i].style.display = 'none';
	}
	
	//Z-Indexを設置
	outProcessClass = $('ul_class_' + koteiNo).getElementsBySelector('.ul_first')[0].getElementsBySelector('li');
	
	for (var i=0; i < outProcessClass.length; i++) {
		
		outProcessClass[i].setStyle({zIndex:"6000"});

	}
	
	outProcessClass = $('ul_class_' + koteiNo).getElementsBySelector('.ul_first')[0].childElements();
	
	var count = 0;
	
	for (var i=0; i < outProcessClass.length; i++) {
	
		if (outProcessClass[i].tagName = "LI") {
							   
			count++;
		}
		
	}
	
	if($('ul_class_' + koteiNo).style.display != 'none'){
	
		//オブジェクトの下端
		var bottomPosition = $('menu_img_' + koteiNo).cumulativeOffset()[1]
							+ $('ul_class_' + koteiNo).getElementsBySelector('li')[0].getHeight() * count + 5;
		
		var positionTop = 0;
		
		if (bottomPosition >= bodyHeight) {
			
			positionTop = bodyHeight - bottomPosition - 30;
			
			if ( positionTop * -1 > ($('menu_img_' + koteiNo).cumulativeOffset()[1] + 24) ) {
				positionTop = $('menu_img_' + koteiNo).cumulativeOffset()[1] * -1 - 24;
			}
			
			count = 0;
			
			for (var i=0; i < outProcessClass.length; i++) {
		
				if (outProcessClass[i].tagName = "LI") {
				
					if (count == 1) {
						
						positionTop += 1;
					}
					
					outProcessClass[i].setStyle({position: "absolute",
									   top: (outProcessClass[i].getHeight() * count + positionTop) + "px"
									   });
									   
					count++;
				}
				
			}
		}
	
	} else {
		
		for (var i=0; i < outProcessClass.length; i++) {
		
			if (outProcessClass[i].tagName = "LI") {
			
				if (count == 1) {
					
					positionTop += 1;
				}
				
				outProcessClass[i].setStyle({position: "relative",
								   display: "block",
								   top: "0px"
								   });
								   
				count++;
			}
				
		}
	}
	

}

function initBunruiPopup() {
	
	for (var m=0; m < 1; m++) {
		
		// prototype.jsの定義を行う
		var rows = $("ul_class_" + m).getElementsBySelector(".ul_first");
	    for(i=0; i < rows.length; i++)
		{
			rows[i].observe("mouseover",setProcessDaiPopup);
			rows[i].observe("mouseout",outProcessDaiPopup);
		}
		
		// prototype.jsの定義を行う
		var rows2 = $("ul_class_" + m).getElementsBySelector(".ul_second");
	    for(i=0; i < rows2.length; i++)
		{
			rows2[i].observe("mouseover",setProcessCyuPopup);
			rows2[i].observe("mouseout",outProcessCyuPopup);
		}
		
		$("menu_img_" + m).observe("mouseout",outProcessImgPopup);
		
		var rows4 = $("ul_class_" + m).getElementsBySelector("ul");
		for(i=0; i < rows4.length; i++)
		{
			rows4[i].observe("mouseover",mouseOverEventUL);
		}
		
	}
}

function mouseOverEventUL(event) {

	// 工程番号取得取得
	var top = Event.element(event).up('div');

	while (true) {
	
		if (top.hasClassName("ul_top") == true) {
			
			break;
		}

		top = top.up("div");
		
		if (top == null) {
		
			return;
		}
	}
	
	var koteiNo = parseInt(top.readAttribute("id").split("_")[2],10);

	if (menuThreadObjArray[koteiNo] != null) {
		
		menuThreadObjArray[koteiNo].stopRemove();
	}

}

// マウスオーバ時(工程:大)
function setProcessDaiPopup(event){

	
	// エレメント取得
	var row = Event.element(event).up('ul');
	// class名が[ul_first]以外はEND
	if (row.hasClassName("ul_first") == false) {
		return;
	}
	
	// イベント取得
	var li = Event.element(event);
	if (li.tagName != "li") {

		while (true) {

			li = li.up("li");
			
			if (li == null) {
			
				return;
			}

			if (li.tagName == "LI") {

				break;
			}
		}
	}
	
	// 工程番号取得取得
	var top = Event.element(event).up('div');

	while (true) {
	
		if (top.hasClassName("ul_top") == true) {
			
			break;
		}

		top = top.up("div");
		
		if (top == null) {
		
			return;
		}
	}
	
	var koteiNo = parseInt(top.readAttribute("id").split("_")[2],10);
	
	// マウスオーバフラグ更新
	if (menuThreadObjArray[koteiNo] != null) {
		
		menuThreadObjArray[koteiNo].stopRemove();
	}
	
	// 削除してはいけないコードのセット
	openCodeCyuArray[koteiNo] = ("00" + li.readAttribute('value')).slice(-3)
	openCodeSaiArray[koteiNo] = '';
	
	// class情報を取得(工程:中)
	var outProcessClass = $('ul_class_' + koteiNo).getElementsBySelector('.ul_second');
	
	// 工程(中)情報の表示を非表示にする
	for (var i=0; i < outProcessClass.length; i++) {
	
		var code = (String)(outProcessClass[i].readAttribute('value'));
	
		// 表示しようとする工程(中)は除く
		if(code != openCodeCyuArray[koteiNo]){
		
			outProcessClass[i].style.display = 'none';
		} else {
		
			outProcessClass[i].style.display = 'block';
		}
	}
	
	// class情報を取得(工程:細)
	outProcessClass = $('ul_class_' + koteiNo).getElementsBySelector('.ul_third');
	// 工程(細)情報の表示をすべて非表示にする
	for (var i=0; i < outProcessClass.length; i++) {

		outProcessClass[i].style.display = 'none';
	}
	
	//ポジション設定
	//li.style.position = "relative";

}

// マウスオーバ時(工程:中)
function setProcessCyuPopup(event){


	// エレメント取得
	var row = Event.element(event).up('ul');
	// class名が[ul_second]以外はEND
	if (row.hasClassName("ul_second") == false) {
		return;
	}
	
	// イベント取得
	var li = Event.element(event);
	if (li.tagName != "LI") {

		while (true) {

			li = li.up("li");
			
			if (li == null) {
			
				return;
			}

			if (li.tagName == "LI") {

				break;
			}
		}
	}
	
		// 工程番号取得取得
	var top = Event.element(event).up('div');

	while (true) {
	
		if (top.hasClassName("ul_top") == true) {
			
			break;
		}

		top = top.up("div");
		
		if (top == null) {
		
			return;
		}
	}
	
	var koteiNo = parseInt(top.readAttribute("id").split("_")[2],10);

	
	// マウスオーバフラグ更新
	if (menuThreadObjArray[koteiNo] != null) {
		
		menuThreadObjArray[koteiNo].stopRemove();
	}
	
	// liのvalueを取得
	openCodeSaiArray[koteiNo] = ("00" + li.readAttribute('value')).slice(-3)
	
	// class情報を取得(工程:中)
	var outProcessClass = $('ul_class_' + koteiNo).getElementsBySelector('.ul_second');
	
	// 工程(中)情報の表示を非表示にする
	for (var i=0; i < outProcessClass.length; i++) {
	
		var code = (String)(outProcessClass[i].readAttribute('value'));
	
		// 表示しようとする工程(中)は除く
		if(code != openCodeCyuArray[koteiNo]){
		
			outProcessClass[i].style.display = 'none';
		} else {
		
			outProcessClass[i].style.display = 'block';
		}
	}
	
		// class情報を取得(工程:細)
	outProcessClass = $('ul_class_' + koteiNo).getElementsBySelector('.ul_third');
	
		// 工程(細)情報の表示を非表示にする
	for (var i=0; i < outProcessClass.length; i++) {
	
		var code = (String)(outProcessClass[i].readAttribute('value'));
	
		// 表示しようとする工程(細)は除く
		if(code != openCodeCyuArray[koteiNo] + "_" + openCodeSaiArray[koteiNo]){
		
			outProcessClass[i].style.display = 'none';
		} else {
		
			outProcessClass[i].style.display = 'block';
		}
	}
	
	//ポジション設定
	li.style.position = "relative";
	

}

function outProcessImgPopup(event){
	
	// 工程番号取得取得
	var top = Event.element(event).up('div');

	while (true) {
	
		if (top.hasClassName("img_top") == true) {
			
			break;
		}

		top = top.up("div");
		
		if (top == null) {
		
			return;
		}
	}
	
	var koteiNo = parseInt(top.readAttribute("id").split("_")[2],10);

	
	// マウスオーバフラグ更新
	if (menuThreadObjArray[koteiNo] != null) {
		
		menuThreadObjArray[koteiNo].stopRemove();
	}
	
	menuThreadObjArray[koteiNo] = new RemoveMenuPopup(koteiNo);
	menuThreadObjArray[koteiNo].remove();

}

// マウスアウト時
function outProcessDaiPopup(event){

		// エレメント取得
	var row = Event.element(event).up('ul');
	// class名が[ul_second]以外はEND
	if (row.hasClassName("ul_first") == false) {
		return;
	}
	// イベント取得
	var li = Event.element(event);
	if (li.tagName != "LI") {

		while (true) {

			li = li.up("li");
			
			if (li == null) {
			
				return;
			}

			if (li.tagName == "LI") {

				break;
			}
		}
	}
	
		// 工程番号取得取得
	var top = Event.element(event).up('div');

	while (true) {
	
		if (top.hasClassName("ul_top") == true) {
			
			break;
		}

		top = top.up("div");
		
		if (top == null) {
		
			return;
		}
	}
	
	var koteiNo = parseInt(top.readAttribute("id").split("_")[2],10);
	
	// マウスオーバフラグ更新
	if (menuThreadObjArray[koteiNo] != null) {
		
		menuThreadObjArray[koteiNo].stopRemove();
	}
	
	menuThreadObjArray[koteiNo] = new RemoveMenuPopup(koteiNo);
	menuThreadObjArray[koteiNo].remove();
	
}

function outProcessCyuPopup(event){

		// エレメント取得
	var row = Event.element(event).up('ul');
	// class名が[ul_second]以外はEND
	if (row.hasClassName("ul_second") == false) {
		return;
	}
	// イベント取得
	var li = Event.element(event);
	if (li.tagName != "LI") {

		while (true) {

			li = li.up("li");
			
			if (li == null) {
			
				return;
			}

			if (li.tagName == "LI") {

				break;
			}
		}
	}
	
		// 工程番号取得取得
	var top = Event.element(event).up('div');

	while (true) {
	
		if (top.hasClassName("ul_top") == true) {
			
			break;
		}

		top = top.up("div");
		
		if (top == null) {
		
			return;
		}
	}
	
	var koteiNo = parseInt(top.readAttribute("id").split("_")[2],10);
	
	// マウスオーバフラグ更新
	if (menuThreadObjArray[koteiNo] != null) {
		
		menuThreadObjArray[koteiNo].stopRemove();
	}
	
	menuThreadObjArray[koteiNo] = new RemoveMenuPopup(koteiNo);
	menuThreadObjArray[koteiNo].remove();

}


var RemoveMenuPopup = Class.create();

RemoveMenuPopup.prototype = {

	initialize : function(koteiNo) {
	
        this.notRemove = 0;
        this.koteiNo = koteiNo
        
    },
    
    remove : function() {
        
	    setTimeout(this.removeThread.bind(this), 500);  
	},
	
	stopRemove : function() {
	
		this.notRemove = 1;	
	},
  
	removeThread: function() {
	
		if(this.notRemove == 0){
		
			// メニュー全体を非表示とする
			$('ul_class_' + this.koteiNo).style.display = 'none';
			
			// class情報を取得(工程:中)
			var outProcessClass = $('ul_class_' + this.koteiNo).getElementsBySelector('.ul_second');
			
			// 工程(中)情報の表示を非表示にする
			for (var i=0; i < outProcessClass.length; i++) {
		
				outProcessClass[i].style.display = 'none';
		
			}
			
			// class情報を取得(工程:細)
			outProcessClass = $('ul_class_' + this.koteiNo).getElementsBySelector('.ul_third');
			
			// 工程(細)情報の表示をすべて非表示にする
			for (var i=0; i < outProcessClass.length; i++) {
		
				outProcessClass[i].style.display = 'none';
			}
			
			//Z-Indexを設置
			outProcessClass = $('ul_class_' + this.koteiNo).getElementsBySelector('.ul_first')[0].getElementsBySelector('li');
			
			for (var i=0; i < outProcessClass.length; i++) {
				
				outProcessClass[i].setStyle({zIndex:"6000"});
		
			}
			
			outProcessClass = $('ul_class_' + this.koteiNo).getElementsBySelector('.ul_first')[0].childElements();
			
			for (var i=0; i < outProcessClass.length; i++) {
			
				if (outProcessClass[i].tagName = "LI") {
				

					
					outProcessClass[i].setStyle({position: "relative",
									   top: "0px"
									   });
									   
				}
					
			}

			
		}
	}  
    
    
}; 