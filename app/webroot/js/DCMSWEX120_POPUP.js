//選択行
var selectedRow;


//行の値保持
var originValStaffCd = "";
var originValStaffNm = "";
var originValYmdSyugyo = "";
var originValDtSyugyoSt = "";
var originValEdSyugyoEd = "";
var originValKotei = null;
var originValDtKoteiSt = null;
var originValDtKoteiEd = null;
var originValBiko1 = null;
var originValBiko2 = null;
var originValBiko3 = null;
var originValKaisiKyori = null;
var originValSyuryoKyori = null;
var originValSyaryoBango = null;
var originValChikuArea = null;

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

		if (parseInt(data[4].innerHTML.trim(),10) < 3) {

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
	setRowColor(data[4].innerHTML, row)

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


	//ポップアップを読み込む
	new Ajax.Request( actionUrl, {
	  method : 'post',

	  onSuccess : viewUpdatePopUp,

	  onFailure : function( event )  {
	    location.href = "/DCMS/WPO040/index?return_cd=91&message=" + Base64.encode(DCMSMessage.format("CMNE000101"));
	  },
	  onException : function( event, ex )  {
	    location.href = "/DCMS/WPO040/index?return_cd=92&message=" + Base64.encode(DCMSMessage.format("CMNE000102"));
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
					title: "工程設定",
					width:900,
					height:570,
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

	//分類を設定
	initBunruiPopup();

	//ポップアップを保持
	popUpView = popup

	//ポップアップにキャンセルボタンを付加
	var deleteButton = new Element("input");
	deleteButton.writeAttribute({
			type : "button",
			value : "削除"
	});
	deleteButton.addClassName("buttonsWPO04001");

	$$(".alphacube_buttons")[0].insert({
		top: deleteButton
	});

	deleteButton.observe("click",clickDeleteButton);


	//選択行のデータを取得
	var cells = selectedRow.getElementsBySelector(".tableCell");
	var data = selectedRow.getElementsBySelector(".hiddenData");

	//値を設置
	$("bango").update(cells[0].innerHTML);// 行番号
	$("staff_cd").update(cells[1].innerHTML);// スタッフコード
	$("staff_nm").update(cells[2].innerHTML);// スタッフ名
	$("ymd_syugyo").update(cells[3].innerHTML);// 就業日付
	$("dt_syugyo_st").value = cells[4].innerHTML.replace(/:/g, "");// 就業開始
	$("dt_syugyo_ed").value = cells[5].innerHTML.replace(/:/g, "");// 就業終了

	//更新が起こった事を判定するために、値を保持
	originValStaffCd = cells[1].innerHTML;
	originValStaffNm = cells[2].innerHTML;
	originValYmdSyugyo = cells[3].innerHTML.replace(/\//g, "");
	originValDtSyugyoSt = cells[4].innerHTML.replace(/:/g, "");
	originValEdSyugyoEd = cells[5].innerHTML.replace(/:/g, "");
	originValKotei = new Array();
	originValDtKoteiSt = new Array();
	originValDtKoteiEd = new Array();
	originValBiko1 = new Array();
	originValBiko2 = new Array();
	originValBiko3 = new Array();
	originValKaisiKyori = new Array();
	originValSyuryoKyori = new Array();
	originValSyaryoBango = new Array();
	originValChikuArea = new Array();

	//上記の事を工程にも適応する
	var koteis = selectedRow.getElementsBySelector(".tableCellGroupKotei");
	var koteiPopups = $$("body")[0].getElementsBySelector(".koteiPopup");

	for (var i=0; i < koteis.length ; i++) {

		var kotei = koteis[i];
		var koteiPopup = koteiPopups[i];

		//工程の行データの取得
		var cells = kotei.getElementsBySelector(".tableCellKotei");
		var data = kotei.getElementsBySelector(".hiddenDataKotei");

		//工程の初期値を設定
		koteiPopup.getElementsBySelector(".koteiCd")[0].setValue(data[0].innerHTML.trim());
		koteiPopup.getElementsBySelector(".koteiComboPopup")[0].setValue(htmlspecialchars_decode(getKoteiNm(data[0].innerHTML.trim())));
		koteiPopup.getElementsBySelector(".dt_kotei_st")[0].setValue(cells[1].innerHTML.replace(/:/g, "").trim());
		koteiPopup.getElementsBySelector(".dt_kotei_ed")[0].setValue(cells[2].innerHTML.replace(/:/g, "").trim());
		koteiPopup.getElementsBySelector(".biko1")[0].setValue(htmlspecialchars_decode(data[3].innerHTML));
		koteiPopup.getElementsBySelector(".biko2")[0].setValue(htmlspecialchars_decode(data[4].innerHTML));
		koteiPopup.getElementsBySelector(".biko3")[0].setValue(htmlspecialchars_decode(data[5].innerHTML));
		koteiPopup.getElementsBySelector(".kaisi_kyori")[0].setValue(htmlspecialchars_decode(data[6].innerHTML));
		koteiPopup.getElementsBySelector(".syuryo_kyori")[0].setValue(htmlspecialchars_decode(data[7].innerHTML));
		koteiPopup.getElementsBySelector(".syaryo_cd")[0].setValue(htmlspecialchars_decode(data[8].innerHTML));
		koteiPopup.getElementsBySelector(".area_cd")[0].setValue(htmlspecialchars_decode(data[9].innerHTML));

		//工程の値を保持
		originValKotei[i] = data[0].innerHTML.trim();
		originValDtKoteiSt[i] = cells[1].innerHTML.replace(/:/g, "").trim();
		originValDtKoteiEd[i] = cells[2].innerHTML.replace(/:/g, "").trim();
		originValBiko1[i] = htmlspecialchars_decode(data[3].innerHTML);
		originValBiko2[i] = htmlspecialchars_decode(data[4].innerHTML);
		originValBiko3[i] = htmlspecialchars_decode(data[5].innerHTML);
		originValKaisiKyori[i] = htmlspecialchars_decode(data[6].innerHTML);
		originValSyuryoKyori[i] = htmlspecialchars_decode(data[7].innerHTML);
		originValSyaryoBango[i] = htmlspecialchars_decode(data[8].innerHTML);
		originValChikuArea[i] = htmlspecialchars_decode(data[9].innerHTML);
	}
}


//ポップアップの決定ボタンクリックイベントハンドラ
function clickOKButton(window) {

	//入力チェックを行う

	//行データの取得
	var cells = selectedRow.getElementsBySelector(".tableCell");
	var data = selectedRow.getElementsBySelector(".hiddenData");
	var YMD_SYOGYO = data[1].innerHTML;


	//就業開始時刻フォーマットが不正
	if (!$F("dt_syugyo_st").trim().match(/^[0-9]{4}/)) {
		alert(DCMSMessage.format("CMNE000004", "就業開始時間","4"));
		return;
	}

	//就業開始時刻入力が不正
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
	var koteis = selectedRow.getElementsBySelector(".tableCellGroupKotei");
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

				//工程データがない場合
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

	//就業開始終了
	var text = $F("dt_syugyo_st");
	cells[4].innerHTML = text.substr(0,2) + ":" + text.substr(2,4);
	data[2].innerHTML = reverseDateString(YMD_SYOGYO,text.substr(0,2),text.substr(2,4));

	//就業終了がはいっている場合秒を0に更新
	text = $F("dt_syugyo_ed");

	if (text != "") {

		cells[5].innerHTML = text.substr(0,2) + ":" + text.substr(2,4);
		data[3].innerHTML = reverseDateString(YMD_SYOGYO,text.substr(0,2),text.substr(2,4));
	} else {

		cells[5].innerHTML = "";
		data[3].innerHTML = "";
	}


	//就業時間開始の更新がある場合
	if ($F("dt_syugyo_st") != originValDtSyugyoSt) {

		//セルカラーを設定
		setUpdatedCellColor(cells[4]);

		//処理フラグを、処理なし状態なら、更新状態へ変える
		if (data[4].innerHTML == "0") {
			data[4].innerHTML = "1";
		}

		//色を指定
		setRowColor(data[4].innerHTML, selectedRow)

	}

	//就業時間終了の更新がある場合
	if ($F("dt_syugyo_ed") != originValEdSyugyoEd) {

		//セルカラーを設定
		setUpdatedCellColor(cells[5]);

		//処理フラグを、処理なし状態なら、更新状態へ変える
		if (data[4].innerHTML == "0") {
			data[4].innerHTML = "1";
		}

		//色を指定
		setRowColor(data[4].innerHTML, selectedRow)

	}

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

		//データの設定

		//工程名
		if (originValKotei[i] != selectElement.getValue().trim()){

			//データを設定
			cellsKotei[0].innerHTML = koteiPopup.getElementsBySelector(".koteiComboPopup")[0].getValue().trim();
			dataKotei[0].innerHTML = selectElement.getValue().trim();

			//セルカラーを設定
			setUpdatedCellColor(cellsKotei[0]);

			//処理フラグを、処理なし状態なら、更新状態へ変える
			if (data[4].innerHTML == "0") {
				data[4].innerHTML = "1";
			}

			//色を指定
			setRowColor(data[4].innerHTML, selectedRow)
		}

		//データを設定
		var text = kaishiElement.getValue().trim();

		if (text != "") {

			cellsKotei[1].innerHTML = text.substr(0,2) + ":" + text.substr(2,4);
			dataKotei[1].innerHTML = reverseDateString(YMD_SYOGYO,text.substr(0,2),text.substr(2,4));

		} else {

			cellsKotei[1].innerHTML = "";
			dataKotei[1].innerHTML = "";
		}

		text = syuryoElement.getValue().trim();

		if (text != "") {

			cellsKotei[2].innerHTML = text.substr(0,2) + ":" + text.substr(2,4);
			dataKotei[2].innerHTML = reverseDateString(YMD_SYOGYO,text.substr(0,2),text.substr(2,4));
		} else {

			cellsKotei[2].innerHTML = "";
			dataKotei[2].innerHTML = "";
		}

		//工程開始時刻
		if (originValDtKoteiSt[i] != kaishiElement.getValue().trim()){

			//セルカラーを設定
			setUpdatedCellColor(cellsKotei[1]);

			//処理フラグを、処理なし状態なら、更新状態へ変える
			if (data[4].innerHTML == "0") {
				data[4].innerHTML = "1";
			}

			//色を指定
			setRowColor(data[4].innerHTML, selectedRow)


		}

		//工程終了時刻
		if (originValDtKoteiEd[i] != syuryoElement.getValue().trim()){

			//セルカラーを設定
			setUpdatedCellColor(cellsKotei[2]);

			//処理フラグを、処理なし状態なら、更新状態へ変える
			if (data[4].innerHTML == "0") {
				data[4].innerHTML = "1";
			}

			//色を指定
			setRowColor(data[4].innerHTML, selectedRow)

		}

		//biko1
		if (originValBiko1[i] != biko1Element.getValue().trim()){

			//データを設定
			dataKotei[3].innerHTML = htmlspecialchars(biko1Element.getValue().trim());

			//処理フラグを、処理なし状態なら、更新状態へ変える
			if (data[4].innerHTML == "0") {
				data[4].innerHTML = "1";
			}

			//色を指定
			setRowColor(data[4].innerHTML, selectedRow)
		}

		//biko2
		if (originValBiko2[i] != biko2Element.getValue().trim()){

			//データを設定
			dataKotei[4].innerHTML = htmlspecialchars(biko2Element.getValue().trim());

			//処理フラグを、処理なし状態なら、更新状態へ変える
			if (data[4].innerHTML == "0") {
				data[4].innerHTML = "1";
			}

			//色を指定
			setRowColor(data[4].innerHTML, selectedRow)
		}

		//biko3
		if (originValBiko3[i] != biko3Element.getValue().trim()){

			//データを設定
			dataKotei[5].innerHTML = htmlspecialchars(biko3Element.getValue().trim());

			//処理フラグを、処理なし状態なら、更新状態へ変える
			if (data[4].innerHTML == "0") {
				data[4].innerHTML = "1";
			}

			//色を指定
			setRowColor(data[4].innerHTML, selectedRow)
		}

		//kaishi_kyori
		if (originValKaisiKyori[i] != kaisiKyoriElement.getValue().trim()){

			//データを設定
			dataKotei[6].innerHTML = htmlspecialchars(kaisiKyoriElement.getValue().trim());

			//処理フラグを、処理なし状態なら、更新状態へ変える
			if (data[4].innerHTML == "0") {
				data[4].innerHTML = "1";
			}

			//色を指定
			setRowColor(data[4].innerHTML, selectedRow)
		}

		//syuryo_kyori
		if (originValSyuryoKyori[i] != syuryoKyoriElement.getValue().trim()){

			//データを設定
			dataKotei[7].innerHTML = htmlspecialchars(syuryoKyoriElement.getValue().trim());

			//処理フラグを、処理なし状態なら、更新状態へ変える
			if (data[4].innerHTML == "0") {
				data[4].innerHTML = "1";
			}

			//色を指定
			setRowColor(data[4].innerHTML, selectedRow)
		}

		//syaryo_cd
		if (originValSyaryoBango[i] != syaryoBangoElement.getValue().trim()){

			//データを設定
			dataKotei[8].innerHTML = htmlspecialchars(syaryoBangoElement.getValue().trim());

			//処理フラグを、処理なし状態なら、更新状態へ変える
			if (data[4].innerHTML == "0") {
				data[4].innerHTML = "1";
			}

			//色を指定
			setRowColor(data[4].innerHTML, selectedRow)
		}

		//area_cd
		if (originValChikuArea[i] != areaCdElement.getValue().trim()){

			//データを設定
			dataKotei[9].innerHTML = htmlspecialchars(areaCdElement.getValue().trim());

			//処理フラグを、処理なし状態なら、更新状態へ変える
			if (data[4].innerHTML == "0") {
				data[4].innerHTML = "1";
			}

			//色を指定
			setRowColor(data[4].innerHTML, selectedRow)
		}
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

//削除ボタンクリックイベントハンドラ
function clickDeleteButton(event) {

	//色を指定
	setDeletedRowColor(selectedRow);

	//セルはinheritへ（色を統一するため）
	var cells = selectedRow.getElementsBySelector(".tableCell");

	for (var i=0; i < cells.length; i++) {

		cells[i].setStyle({
			background: "inherit"
		});
	}

	//工程のセルもinheritへ
	var koteis = selectedRow.getElementsBySelector(".tableCellGroupKotei");

	for (var i=0; i < koteis.length ; i++) {

		var kotei = koteis[i];
		var cells = kotei.getElementsBySelector(".tableCellKotei");

		//セルはinheritへ（色を統一するため）
		for (var m=0; m < cells.length ; m++) {

				cells[m].setStyle({
				background: "inherit"
			});
		}

	}

	//現在のイベントを削除して削除用のイベントを設置
	selectedRow.stopObserving("click");
	selectedRow.stopObserving("mouseover");
	selectedRow.stopObserving("mouseout");
	selectedRow.observe("click",clickDeletedRow);

	//処理フラグを設置
	var data = selectedRow.getElementsBySelector(".hiddenData");

	//未処理フラグからの削除フラグ：３
	if (data[4].innerHTML == "0") {

		data[4].innerHTML = "3";

	//更新フラグからの削除フラグ：４
	} else if (data[4].innerHTML == "1") {

		data[4].innerHTML = "4";

	//新規フラグからの削除フラグ：５
	} else if (data[4].innerHTML == "2") {

		data[4].innerHTML = "5";

	}


	popUpView.close();
	dbclickFlag = 0;
	areaCoverElement.remove();
}

//削除状態をクリックした時に、戻すためのポップアップを出す
function clickDeletedRow(event) {

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

	selectedRow = row;

	//ポップアップ画面に配置
	var popup = Dialog.confirm("<br>削除を解除しますか？",
				   {
					id: "popup",
					className: "alphacube",
					title: "工程設定",
					width:400,
					height:100,
					draggable: true,
					destroyOnclose: true,
					recenterAuto: false,
					buttonClass : "buttonsWPO04001",
					okLabel: "解除",
					cancelLabel:"キャンセル",
					onOk: clickOKReverseButton,
					onCancel: clickCANCELButton,
					showEffectOptions: {duration:0.2},
					hideEffectOptions: {duration:0}
				   });

	//Z-INDEXを設定
	popup.setZIndex(1000);
}

//削除用ポップアップの、OKボタンイベント
function clickOKReverseButton(window) {

	//選択行のイベントを、削除用から元に戻す
	selectedRow.stopObserving("click");
	selectedRow.observe("click",clickPopUp);
	selectedRow.observe("mouseover",overRow);
	selectedRow.observe("mouseout",leaveRow);

	//処理フラグを指定
	var data = selectedRow.getElementsBySelector(".hiddenData");

	//未処理削除フラグの場合ー＞未処理
	if (data[4].innerHTML == "3") {

		data[4].innerHTML = "0";

	//更新削除フラグの場合ー＞更新
	} else if (data[4].innerHTML == "4") {

		data[4].innerHTML = "1";

	//新規削除フラグの場合ー＞新規
	} else if (data[4].innerHTML == "5") {

		data[4].innerHTML = "2";

	}

	//新規の場合はそのまま
	setRowColor(data[4].innerHTML, selectedRow)

	window.close();
	dbclickFlag = 0;
	areaCoverElement.remove();
}


function getKoteiNm(koteiCd) {

	try {

		if (koteiCd != '') {

			var koteis = koteiCd.split("_");

			var koteiSai = null;
			var koteiCyu = null;
			var koteiDai = null;

			if (koteis.length >= 3) {

				var lis = $('popupKoteiArea').getElementsBySelector('.koteiPopup')[0].getElementsBySelector('li');

				// 工程(大)情報の表示をすべて非表示にする
				for (var i=0; i < lis.length; i++) {

					var liKotei = lis[i].readAttribute('value');

					if (liKotei == null) {

						continue;
					}

					if (liKotei.length < 3 && liKotei != "") {

						liKotei = ("00" + liKotei).slice(-3);
					}

					if (liKotei == (koteis[0]) ) {

						koteiDai = lis[i].getElementsBySelector("div")[0].innerHTML.trim();

						if (koteiDai == '»') {

							koteiDai = lis[i].getElementsBySelector("div")[1].innerHTML.trim();
						}

						break;
					}
				}

				// 工程(中)情報の表示をすべて非表示にする
				for (var i=0; i < lis.length; i++) {

					var liKotei = lis[i].readAttribute('value');

					if (liKotei == null) {

						continue;
					}

					if (liKotei.length < 3 && liKotei != "") {

						liKotei = ("00" + liKotei).slice(-3);
					}

					if (liKotei == (koteis[0] + "_" + koteis[1]) ) {

						koteiCyu = lis[i].getElementsBySelector("div")[0].innerHTML.trim();

						if (koteiCyu == '»') {

							koteiCyu = lis[i].getElementsBySelector("div")[1].innerHTML.trim();
						}

						break;
					}
				}


				// 工程(細)情報の表示をすべて非表示にする
				for (var i=0; i < lis.length; i++) {

					var liKotei = lis[i].readAttribute('value');

					if (liKotei == null) {

						continue;
					}

					if (liKotei.length < 3 && liKotei != "") {

						liKotei = ("00" + liKotei).slice(-3);
					}

					if (liKotei == (koteis[0] + "_" + koteis[1] + "_" + koteis[2]) ) {

						koteiSai = lis[i].getElementsBySelector("div")[0].innerHTML.trim();

						if (koteiSai == '»') {

							koteiSai = lis[i].getElementsBySelector("div")[1].innerHTML.trim();
						}

						break;
					}
				}

			}

			if (koteiSai != null && koteiDai != null && koteiCyu != null) {

				return koteiDai + " > " +koteiCyu + " > " + koteiSai;

			}

		}

	} catch (e) {

		alert(e);
	}



	return '';
}

/***************************************************

特殊文字を HTML エンティティに変換する

***************************************************/
var htmlspecialchars = function(str) {
return str.replace(/[&"'<>]/g, function($0) {
if ($0 == "&")  return '&amp;';
if ($0 == "\"") return '&quot;';
if ($0 == "'")  return '&#039;';
if ($0 == "<")  return '&lt;';
if ($0 == ">")  return '&gt;';
});
};

/***************************************************

特殊な HTML エンティティを文字に戻す

***************************************************/
var htmlspecialchars_decode = function(str) {
return str.replace(/&(gt|lt|#039|quot|amp);/ig, function($0, $1) {
if (/^gt$/i.test($1))   return ">";
if (/^lt$/i.test($1))   return "<";
if (/^#039$/.test($1))  return "'";
if (/^quot$/i.test($1)) return "\"";
if (/^amp$/i.test($1))  return "&";
});
};
