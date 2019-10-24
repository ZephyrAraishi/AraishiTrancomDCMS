var actionUrl2;

//ポップアップ表示と、行選択時に色を変えるための初期化関数
function initAddRow(url) {

	actionUrl2 = url;

	$("addButton").observe("click",clickAddButton);

}

//追加ボタンのクリックイベントハンドラ
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
	areaCoverElement = createAreaCoverElement();
	$$("body")[0].insert(areaCoverElement);


	//ポップアップを読み込む
	new Ajax.Request( actionUrl2, {
	  method : 'post',

	  onSuccess : viewInsertPopUp,

	  onFailure : function( event )  {
	    location.href = "/DCMS/WPO040/index?return_cd=91&message=" + Base64.encode(DCMSMessage.format("CMNE000101"));
	  },
	  onException : function( event, ex )  {
	    location.href = "/DCMS/WPO040/index?return_cd=92&message=" + Base64.encode(DCMSMessage.format("CMNE000102"));
	  }
	});
}

//ポップアップのデータが取得完了したとき、ポップアップを表示する
function viewInsertPopUp(event) {

	//ポップアップを画面に配置
	var popup = Dialog.confirm(event.responseText,
				   {
					id: "popup",
					className: "alphacube",
					title: "工程設定",
					width:900,
					height:570,
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

	$("ymd_syugyo_area").insert($("ymd_syugyo"));


	//ポップアップのZINDEXを設定
	popup.setZIndex(1000);

	popup.content.setStyle({overflow:"visible"});

	//分類を設定
	initBunruiPopup();

	//値を設置
	$("bango").innerHTML = "新規";

	//ポップアップを保持
	popUpView = popup


}

//新規追加ポップアップ用、OKボタンクリックイベントハンドラ
function clickOKButton2(window) {

	//入力チェック

	//スタッフコードが指定されていないとき
	if ($("staff_cd").innerHTML.trim() == "") {
		alert(DCMSMessage.format("CMNE000006", "スタッフ"));

		return;
	}

	//就業日付のフォーマットが不正なとき
	if (!$F("ymd_syugyo").trim().match(/^[0-9]{8}/)) {
		alert(DCMSMessage.format("CMNE000004", "就業日", "8"));

		return;
	}

	//就業日付が不正なとき
	if (DCMSValidation.checkDate($F("ymd_syugyo").trim()) == false) {
		alert(DCMSMessage.format("CMNE000007", "就業日"));

		return;
	}

	//就業開始時刻のフォーマットが不正なとき
	if (!$F("dt_syugyo_st").trim().match(/^[0-9]{4}/)) {
		alert(DCMSMessage.format("CMNE000004", "就業開始時間", "4"));

		return;
	}

	//就業開始時刻が不正なとき
	if (DCMSValidation.checkTime($F("dt_syugyo_st").trim().substr(2,4)) == false ) {

		alert(DCMSMessage.format("CMNE000007", "就業開始時刻"));
		return;
	}

	//就業終了時刻がある場合
	if ($F("dt_syugyo_ed").trim() != "") {

		//就業終了時刻のフォーマットが不正
		if ($F("dt_syugyo_ed").trim() != "" && !$F("dt_syugyo_ed").match(/^[0-9]{4}/)) {
			alert(DCMSMessage.format("CMNE000004", "就業終了時間", "4"));

			return;
		}

		//就業終了時刻が不正
		if (DCMSValidation.checkTime($F("dt_syugyo_ed").trim().substr(2,4)) == false ) {

			alert(DCMSMessage.format("CMNE000007", "就業終了時刻"));
			return;
		}

		//就業開始時刻は、就業終了時刻より前
		if (parseInt($F("dt_syugyo_st").trim(), 10) > parseInt($F("dt_syugyo_ed"), 10)) {
			alert(DCMSMessage.format("CMNE000008", "就業終了時刻", "就業開始時刻"));
			return;
		}

	}

	//工程データを取得
	var koteiPopups = $$("body")[0].getElementsBySelector(".koteiPopup");

	//工程項目を一つずつチェック
	for (var i=0; i < koteiPopups.length ; i++) {

		var koteiPopup = koteiPopups[i];

		var selectElement = koteiPopup.getElementsBySelector(".koteiCd")[0];
		var kaishiElement = koteiPopup.getElementsBySelector(".dt_kotei_st")[0];
		var syuryoElement = koteiPopup.getElementsBySelector(".dt_kotei_ed")[0];
		var kaisiKyoriElement = koteiPopup.getElementsBySelector(".kaisi_kyori")[0];
		var syuryoKyoriElement = koteiPopup.getElementsBySelector(".syuryo_kyori")[0];

		//工程名と工程開始時刻は必須
		if ((selectElement.getValue().trim() == "" &&
			kaishiElement.getValue().trim() != "") ||
			(selectElement.getValue().trim() != "" &&
			kaishiElement.getValue().trim() == "")) {

			alert(DCMSMessage.format("CMNE000009", "工程名" + (i+1), "工程名", "工程開始時刻"));
			return;
		}

		//工程開始時刻のフォーマットが不正
		if (!kaishiElement.getValue().trim().match(/^[0-9]{4}/) &&
		     kaishiElement.getValue().trim() != "") {

			alert(DCMSMessage.format("CMNE000012", "工程名" + (i+1), "工程開始時刻", "4"));
			return;
		}

		//工程開始時刻が不正
		if (DCMSValidation.checkTime(kaishiElement.getValue().trim().substr(2,4)) == false ) {

			alert(DCMSMessage.format("CMNE000010", "工程名" + (i+1), "工程開始時刻"));
			return;
		}

		//工程終了時刻がある場合
		if (syuryoElement.getValue().trim() != "") {

			//工程終了時刻のフォーマットが不正
			if (!syuryoElement.getValue().trim().match(/^[0-9]{4}/) &&
			     syuryoElement.getValue().trim() != "") {

				alert(DCMSMessage.format("CMNE000012", "工程名" + (i+1), "工程終了時刻", "4"));
				return;
			}

			//工程終了時刻が不正
			if (DCMSValidation.checkTime(syuryoElement.getValue().trim().substr(2,4)) == false ) {

				alert(DCMSMessage.format("CMNE000010", "工程名" + (i+1), "工程終了時刻"));
				return;
			}

			//工程終了時刻は、工程終了開始時刻の後でないといけない
			if (parseInt(kaishiElement.getValue().trim(), 10) > parseInt(syuryoElement.getValue().trim(), 10)) {
				alert(DCMSMessage.format("CMNE000011", "工程名" + (i+1), "工程終了時刻", "工程開始時刻"));
				return;
			}
		}

		//工程開始時刻は、就業開始時刻より後でないといけない
		if (parseInt(kaishiElement.getValue().trim(), 10) < parseInt($F("dt_syugyo_st").trim(), 10)) {
			alert(DCMSMessage.format("CMNE000011", "工程名" + (i+1), "工程開始時刻", "就業開始時刻"));
			return;
		}

		//就業終了がある場合
		if (syuryoElement.getValue().trim() != "") {

			//工程終了時刻は、就業開始時刻より後でないといけない
			if (parseInt(syuryoElement.getValue().trim(), 10) < parseInt($F("dt_syugyo_st").trim(), 10)) {
				alert(DCMSMessage.format("CMNE000011", "工程名" + (i+1), "工程終了時刻", "就業開始時刻"));
				return;
			}
		}

		//就業終了時間がある場合
		if ($F("dt_syugyo_ed").trim() != "") {

			//工程開始時刻は、就業終了時刻より前でないといけない
			if (parseInt(kaishiElement.getValue().trim(), 10) > parseInt($F("dt_syugyo_ed").trim(), 10)) {
				alert(DCMSMessage.format("CMNE000013", "工程名" + (i+1), "工程開始時刻", "就業終了時刻"));
				return;
			}
		}

		//就業終了時刻があり、工程終了時刻がない場合
		if (kaishiElement.getValue().trim() != "" && syuryoElement.getValue().trim() == "" && $F("dt_syugyo_ed").trim() != "") {

			alert(DCMSMessage.format("CMNE000022", "就業終了時刻", "工程名" + (i+1) + "の工程終了時刻"));
			return;
		}

		//就業終了時刻があり、工程終了時刻がある場合
		if (syuryoElement.getValue().trim() != "" && $F("dt_syugyo_ed").trim() != "") {

			//工程終了時刻は、就業終了時刻より前でないといけない
			if (parseInt(syuryoElement.getValue().trim(), 10) > parseInt($F("dt_syugyo_ed").trim(), 10)) {
				alert(DCMSMessage.format("CMNE000013", "工程名" + (i+1), "工程終了時刻", "就業終了時刻"));
				return;
			}
		}

		//距離は数字
		if (!isFinite(kaisiKyoriElement.getValue().trim())) {

			alert(DCMSMessage.format("CMNE000002", "開始距離"));

			return;
		}

		//距離は数字
		if (!isFinite(syuryoKyoriElement.getValue().trim())) {

			alert(DCMSMessage.format("CMNE000002", "終了距離"));

			return;
		}

	}

	//工程時間の時間範囲がかぶってないかチェック
	var koteiPopups = $$("body")[0].getElementsBySelector(".koteiPopup");
	var countShorichu = 0;

	//工程項目を総当たりでチェックする
	for (var i=0; i < koteiPopups.length ; i++) {

		//工程データの取得
		var koteiPopupBase = koteiPopups[i];

		var kotei_base = koteiPopupBase.getElementsBySelector(".koteiCd")[0].getValue().trim();
		var dt_kotei_st_base = koteiPopupBase.getElementsBySelector(".dt_kotei_st")[0].getValue().trim();
		var dt_kotei_ed_base = koteiPopupBase.getElementsBySelector(".dt_kotei_ed")[0].getValue().trim();

		//工程データがない場合スキップ
		if (kotei_base == "" &&
			dt_kotei_st_base == "" &&
			dt_kotei_ed_base == "" ) {

			continue;
		}

		//工程が完了している場合
		if (dt_kotei_ed_base != "") {

			for (var m=0; m < koteiPopups.length ; m++) {

				//工程管理データを取得
				var koteiPopup = koteiPopups[m];

				var kotei = koteiPopup.getElementsBySelector(".koteiCd")[0].getValue().trim();
				var dt_kotei_st = koteiPopup.getElementsBySelector(".dt_kotei_st")[0].getValue().trim();
				var dt_kotei_ed = koteiPopup.getElementsBySelector(".dt_kotei_ed")[0].getValue().trim();

				//工程データがない場合スキップ
				if (kotei == "" &&
					dt_kotei_st == "" &&
					dt_kotei_ed == "" ) {

					continue;
				}

				//オブジェクトが被る場合
				if (koteiPopups[i] == koteiPopups[m]) {

					continue;
				}

				//工程が未処理の場合
				if (dt_kotei_ed == "") {
					continue;
				}

				//工程が被っている場合
				if ((parseInt(dt_kotei_st, 10) < parseInt(dt_kotei_st_base, 10) &&
					 parseInt(dt_kotei_st_base, 10) < parseInt(dt_kotei_ed, 10)) ||
					(parseInt(dt_kotei_st, 10) < parseInt(dt_kotei_ed_base, 10) &&
					 parseInt(dt_kotei_ed_base, 10) < parseInt(dt_kotei_ed, 10)) ||
					(parseInt(dt_kotei_st, 10) > parseInt(dt_kotei_st_base, 10) &&
					 parseInt(dt_kotei_ed_base, 10) > parseInt(dt_kotei_ed, 10))
					 ) {

					alert(DCMSMessage.format("CMNE000016", "工程名" + (i+1), "工程名" + (m+1)));
					return;
				}

			}

		//工程が未完了な場合
		} else {

			//未完了の工程が２つ以上ある場合
			countShorichu++;

			if (countShorichu > 1) {

				alert(DCMSMessage.format("CMNE000018"));
				return;
			}

			for (var m=0; m < koteiPopups.length ; m++) {

				//工程管理データを取得
				var koteiPopup = koteiPopups[m];

				var kotei = koteiPopup.getElementsBySelector(".koteiCd")[0].getValue().trim();
				var dt_kotei_st = koteiPopup.getElementsBySelector(".dt_kotei_st")[0].getValue().trim();
				var dt_kotei_ed = koteiPopup.getElementsBySelector(".dt_kotei_ed")[0].getValue().trim();

				//工程データがない
				if (kotei == "" &&
					dt_kotei_st == "" &&
					dt_kotei_ed == "" ) {

					continue;
				}

				//オブジェクトが被る
				if (koteiPopups[i] == koteiPopups[m]) {

					continue;
				}

				//工程が未完了
				if (dt_kotei_ed == "") {
					continue;
				}

				//工程集開始時刻は、一番遅いじかんでなければならない
				if (parseInt(dt_kotei_st_base, 10) < parseInt(dt_kotei_ed, 10)) {

					alert(DCMSMessage.format("CMNE000017", "工程名" + (i+1)));
					return false;
				}

			}
		}
	}

	//更新処理

	//行を作成
	var tableRow = new Element("div");
	tableRow.addClassName("tableRow");
	tableRow.addClassName("row_dat_sp_2");
	tableRow.setStyle({
		width:"12460px"
	});

	//先に、工程以外の6個セル作成
	var tableCell = new Element("div");
	tableCell.addClassName("tableCell");
	tableCell.addClassName("cell_dat_sp_2");
	tableCell.addClassName("bango");
	tableCell.setStyle({
		width:"60px"
	});
	tableRow.insert(tableCell);

	tableCell = new Element("div");
	tableCell.addClassName("tableCell");
	tableCell.addClassName("cell_dat_sp_2");
	tableCell.addClassName("kbn");
	tableCell.setStyle({
		width:"120px"
	});
	tableRow.insert(tableCell);

	tableCell = new Element("div");
	tableCell.addClassName("tableCell");
	tableCell.addClassName("cell_dat_sp_2");
	tableCell.addClassName("nm");
	tableCell.setStyle({
		width:"100px"
	});
	tableRow.insert(tableCell);

	tableCell = new Element("div");
	tableCell.addClassName("tableCell");
	tableCell.addClassName("cell_dat_sp_2");
	tableCell.addClassName("date");
	tableCell.setStyle({
		width:"100px"
	});
	tableRow.insert(tableCell);

	tableCell = new Element("div");
	tableCell.addClassName("tableCell");
	tableCell.addClassName("cell_dat_sp_2");
	tableCell.addClassName("date");
	tableCell.setStyle({
		width:"70px"
	});
	tableRow.insert(tableCell);

	tableCell = new Element("div");
	tableCell.addClassName("tableCell");
	tableCell.addClassName("cell_dat_sp_2");
	tableCell.addClassName("date");
	tableCell.setStyle({
		width:"70px"
	});
	tableRow.insert(tableCell);

	//データDIVを5個配置
	for (var i=0 ; i< 5; i++) {

		var hiddenData = new Element("div");
		hiddenData.addClassName("hiddenData");
		tableRow.insert(hiddenData);

	}

	//工程グループを配置
	for (var i=0; i<40; i++) {

		var tableCellGroupKotei = new Element("div");
		tableCellGroupKotei.addClassName("tableCellGroupKotei");
		tableCellGroupKotei.setStyle({
			float:"left",
			width:"120px",
			height:"60px"
		});

		var tableCellKotei = new Element("div");
		tableCellKotei.addClassName("tableCellKotei");
		tableCellKotei.addClassName("cell_dat");
		tableCellKotei.addClassName("nm");
		tableCellKotei.setStyle({
			width:"110px"
		});
		tableCellGroupKotei.insert(tableCellKotei);

		tableCellKotei = new Element("div");
		tableCellKotei.addClassName("tableCellKotei");
		tableCellKotei.addClassName("cell_dat");
		tableCellKotei.addClassName("date");
		tableCellKotei.setStyle({
			width:"50px"
		});
		tableCellGroupKotei.insert(tableCellKotei);

		tableCellKotei = new Element("div");
		tableCellKotei.addClassName("tableCellKotei");
		tableCellKotei.addClassName("cell_dat");
		tableCellKotei.addClassName("date");
		tableCellKotei.setStyle({
			width:"50px"
		});
		tableCellGroupKotei.insert(tableCellKotei);

		//データDIVを3個配置
		for (var m=0 ; m< 10; m++) {

			var hiddenDataKotei = new Element("div");
			hiddenDataKotei.addClassName("hiddenDataKotei");
			tableCellGroupKotei.insert(hiddenDataKotei);

		}

		tableRow.insert(tableCellGroupKotei);
	}

	//　工程以外データ取得
	var cells = tableRow.getElementsBySelector(".tableCell");
	var data = tableRow.getElementsBySelector(".hiddenData");
	var YMD_SYUGYO = $F("ymd_syugyo");
	YMD_SYUGYO = $F("ymd_syugyo").substr(0,4) + "-" + $F("ymd_syugyo").substr(4,2) + "-" + $F("ymd_syugyo").substr(6,2);

	//番号
	if ($("bango").innerHTML != "") {

		cells[0].innerHTML = $("bango").innerHTML;
	}

	//スタッフCD
	if ($("staff_cd").innerHTML != "") {

		cells[1].innerHTML = $("staff_cd").innerHTML;
		data[0].innerHTML = $("staff_cd").innerHTML;
	}

	//スタッフNM
	if ($("staff_nm").innerHTML != "") {

		cells[2].innerHTML = $("staff_nm").innerHTML;
	}

	//就業日
	if ($F("ymd_syugyo") != "") {

		cells[3].innerHTML = YMD_SYUGYO.replace(/-/g, "/");
		data[1].innerHTML = YMD_SYUGYO;
	}

	//就業開始時間
	if ($F("dt_syugyo_st") != "") {

		var text = $F("dt_syugyo_st");

		cells[4].innerHTML = text.substr(0,2) + ":" + text.substr(2,4);
		data[2].innerHTML = reverseDateString(YMD_SYUGYO,text.substr(0,2),text.substr(2,4));
	}

	//就業終了時間
	if ($F("dt_syugyo_ed") != originValEdSyugyoEd) {

		var text = $F("dt_syugyo_ed");

		if (text != "") {

			cells[5].innerHTML = text.substr(0,2) + ":" + text.substr(2,4);
			data[3].innerHTML = reverseDateString(YMD_SYUGYO,text.substr(0,2),text.substr(2,4));
		} else {

			cells[5].innerHTML = "";
			data[3].innerHTML = "";
		}
	}

	//新規フラグ
	data[4].innerHTML = "2";

	//工程
	var koteis = tableRow.getElementsBySelector(".tableCellGroupKotei");
	var koteiPopups = $$("body")[0].getElementsBySelector(".koteiPopup");

	//工程項目の更新がある場合
	for (var i=0; i < koteis.length ; i++) {

		//工程データの取得
		var kotei = koteis[i];
		var koteiPopup = koteiPopups[i];

		//工程データを設定

		var cellsKotei = kotei.getElementsBySelector(".tableCellKotei");
		var dataKotei = kotei.getElementsBySelector(".hiddenDataKotei");

		//要素取得
		var selectElement = koteiPopup.getElementsBySelector(".koteiCd")[0];
		var kaishiElement = koteiPopup.getElementsBySelector(".dt_kotei_st")[0];
		var syuryoElement = koteiPopup.getElementsBySelector(".dt_kotei_ed")[0];
		var biko1Element = koteiPopup.getElementsBySelector(".biko1")[0];
		var biko2Element = koteiPopup.getElementsBySelector(".biko2")[0];
		var biko3Element = koteiPopup.getElementsBySelector(".biko3")[0];
		var kaisiKyoriElement = koteiPopup.getElementsBySelector(".kaisi_kyori")[0];
		var syuryoKyoriElement = koteiPopup.getElementsBySelector(".syuryo_kyori")[0];
		var syaryoBangoElement = koteiPopup.getElementsBySelector(".syaryo_cd")[0];
		var areaCdElement = koteiPopup.getElementsBySelector(".area_cd")[0];

		//工程名
		if ("" != selectElement.getValue().trim()){


			cellsKotei[0].innerHTML = koteiPopup.getElementsBySelector(".koteiComboPopup")[0].getValue().trim();
			dataKotei[0].innerHTML = selectElement.getValue().trim();
		}

		//工程開始時刻
		if ("" != kaishiElement.getValue().trim()){

			//データを設定
			var text = kaishiElement.getValue().trim();

			cellsKotei[1].innerHTML = text.substr(0,2) + ":" + text.substr(2,4);
			dataKotei[1].innerHTML = reverseDateString(YMD_SYUGYO,text.substr(0,2),text.substr(2,4));
		}

		//工程終了時刻
		if ("" != syuryoElement.getValue().trim()){

			//データを設定
			var text = syuryoElement.getValue().trim();

			if (text != "") {

				cellsKotei[2].innerHTML = text.substr(0,2) + ":" + text.substr(2,4);
				dataKotei[2].innerHTML = reverseDateString(YMD_SYUGYO,text.substr(0,2),text.substr(2,4));
			} else {

				cellsKotei[2].innerHTML = "";
				dataKotei[2].innerHTML = "";
			}

		}

		//biko1
		if ("" != biko1Element.getValue().trim()){

			//データを設定
			var text = htmlspecialchars(biko1Element.getValue().trim());

			dataKotei[3].innerHTML = text;
		}

		//biko2
		if ("" != biko2Element.getValue().trim()){

			//データを設定
			var text = htmlspecialchars(biko2Element.getValue().trim());

			dataKotei[4].innerHTML = text;
		}

		//biko3
		if ("" != biko3Element.getValue().trim()){

			//データを設定
			var text = htmlspecialchars(biko3Element.getValue().trim());

			dataKotei[5].innerHTML = text;
		}

		//koteiKaishi
		if ("" != kaisiKyoriElement.getValue().trim()){

			//データを設定
			var text = htmlspecialchars(kaisiKyoriElement.getValue().trim());

			dataKotei[6].innerHTML = text;
		}


		//koteiSyuryo
		if ("" != syuryoKyoriElement.getValue().trim()){

			//データを設定
			var text = htmlspecialchars(syuryoKyoriElement.getValue().trim());

			dataKotei[7].innerHTML = text;
		}


		//syaryo
		if ("" != syaryoBangoElement.getValue().trim()){

			//データを設定
			var text = htmlspecialchars(syaryoBangoElement.getValue().trim());

			dataKotei[8].innerHTML = text;
		}


		//area
		if ("" != areaCdElement.getValue().trim()){

			//データを設定
			var text = htmlspecialchars(areaCdElement.getValue().trim());

			dataKotei[9].innerHTML = text;
		}
	}

	//イベントを設置
	tableRow.observe("click",clickPopUp);
	tableRow.observe("mouseover",overRow);
	tableRow.observe("mouseout",leaveRow);

	//テーブルに配置
	$("lstTable").insert(tableRow);

	//スクロールを下まで下げる
	$("lstTable").scrollTop = $("lstTable").scrollHeight;

	//色を変更
	setAddedRowColor(tableRow);

	//ボタンを追加
	$("updateButton").setStyle({
		display: "inline"
	});

	$("ymd_syugyo").setValue("");
	$("ymd_syugyo_storage").insert($("ymd_syugyo"));
	window.close();
	dbclickFlag = 0;
	areaCoverElement.remove();
}

//キャンセルボタンクリックイベントハンドラ
function clickCANCELButton2(window) {

	$("ymd_syugyo").setValue("");
	$("ymd_syugyo_storage").insert($("ymd_syugyo"));
	window.close();
	dbclickFlag = 0;
	areaCoverElement.remove();
}


//----------------------------　コンボ ----------------------------

// 選択した内容をテキストボックスに反映
function setMenuProcessPopup( dai_value, cyu_value, sai_value, koutei_dai, koutei_cyu, koutei_sai , koteiNo){
	// 値格納
	var processVal = '';
	var processNmVal = '';

	// 値の判定
	if((koutei_dai != '') && (koutei_cyu != '') && (koutei_sai != '')){

		processVal = dai_value + '_' + cyu_value + '_' + sai_value;
		processNmVal = koutei_dai + ' > ' + koutei_cyu + ' > ' + koutei_sai;;
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
	top.getElementsBySelector('.koteiComboPopup')[0].setValue(processNmVal);

	//　メニューを消す
	menuDispPopup(koteiNo);

}

var menuThreadObjArray = new Array();
var openCodeCyuArray = new Array();
var openCodeSaiArray = new Array();


function menuDispPopup(koteiNo) {

	/*var bodyHeight = Math.max.apply( null, [document.body.clientHeight ,
											document.body.scrollHeight,
											document.documentElement.scrollHeight,
											document.documentElement.clientHeight] );

	var scrollTop = document.viewport.getScrollOffsets().top;

	bodyHeight += scrollTop;*/

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

		/*if (bottomPosition >= bodyHeight) {

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


					//outProcessClass[i].setStyle({position: "absolute",
					//				   top: (outProcessClass[i].getHeight() * count + positionTop) + "px"
					//				   });

					count++;
				}

			}
		}*/

	} else {

		for (var i=0; i < outProcessClass.length; i++) {

			if (outProcessClass[i].tagName = "LI") {

				if (count == 1) {

					positionTop += 1;
				}

				outProcessClass[i].setStyle({position: "relative",
								   display: "block",
								   //top: "0px"
								   });

				count++;
			}

		}
	}


}

function initBunruiPopup() {

	for (var m=0; m < 40; m++) {

		var rows = $("ul_class_" + m).getElementsBySelector(".ul_first");
	    for(i=0; i < rows.length; i++)
		{
			rows[i].observe("mouseover",setProcessDaiPopup);
			rows[i].observe("mouseout",outProcessDaiPopup);
		}

		var rows2 = $("ul_class_" + m).getElementsBySelector(".ul_second");
	    for(i=0; i < rows2.length; i++)
		{
			rows2[i].observe("mouseover",setProcessCyuPopup);
			rows2[i].observe("mouseout",outProcessCyuPopup);
		}

		var rows3 = $("ul_class_" + m).getElementsBySelector(".ul_third");
	    for(i=0; i < rows3.length; i++)
		{
			rows3[i].observe("mouseover",setProcessSaiPopup);
			rows3[i].observe("mouseout",outProcessSaiPopup);
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

		// 工程(中)情報の表示を非表示にする
	for (var i=0; i < outProcessClass.length; i++) {

		var code = (String)(outProcessClass[i].readAttribute('value'));

		// 表示しようとする工程(中)は除く
		if(code != openCodeCyuArray[koteiNo] + "_" + openCodeSaiArray[koteiNo]){

			outProcessClass[i].style.display = 'none';
		} else {

			outProcessClass[i].style.display = 'block';
		}
	}

	//ポジション設定
	li.style.position = "relative";


}

// マウスオーバ時(工程:細)
function setProcessSaiPopup(event){


	// エレメント取得
	var row = Event.element(event).up('ul');
	// class名が[ul_third]以外はEND
	if (row.hasClassName("ul_third") == false) {
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

function outProcessSaiPopup(event){

			// エレメント取得
	var row = Event.element(event).up('ul');
	// class名が[ul_second]以外はEND
	if (row.hasClassName("ul_third") == false) {
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