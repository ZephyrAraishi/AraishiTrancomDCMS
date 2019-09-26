var popupUrl = "";
var kznDataNo = "";
var popupView = null;
var popupCoverElement = null;
var dbclickFlag = 0;
var isNew = false;
var selectedRow = null;

/**
 * ポップアップ初期化
 * 
 * @param url ポップアップ起動用URL
 */
function initPopup(url) {
	popupUrl = url;
	
	var rows = $("lstTable").getElementsBySelector(".tableRow");
    for (i = 0; i < rows.length; i++) {
		rows[i].observe("click", clickTableRow);
		rows[i].observe("mouseover", overRow);
		rows[i].observe("mouseout", leaveRow);
	}
}

/**
 * 「テーブル行」押下時イベント
 */
function clickTableRow(event) {
	
	if (event.isRightClick()) {
		return;
	}
	if (dbclickFlag == 1) {
		dbclickFlag = 0;
		return;
	}
	dbclickFlag = 1;
	
	// 改善表題クリック時は、戻る
	var elm = Event.element(event) + "";
	if (Event.element(event).id == "hrefTitle"
		||  elm.substr(0,7)         == "http://") {
		dbclickFlag = 0;
		return;
	}
	
	// 選択行を保持
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
	
	openPopup(false);
}

/**
 * フォーカスＯＮ時イベント
 * @param event
 */
function overRow(event) {

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
	if (row == null) {
		return;
	}
	setSelectedRowColor(row);
}

/**
 * フォーカスＯＦＦ時イベント
 * @param event
 */
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
	if (row == null) {
		return;
	}

	var data = row.getElementsBySelector(".hiddenData");
	if (data[0].innerHTML == '0' && data[12].innerHTML == '1') {
		row.setStyle({
			background : "#eeeeee"
		});
	} else {
		setRowColor(data[0].innerHTML, row);
	}
}

/**
 * ポップアップ起動
 * @param flg 新規登録の場合true、そうでない場合false
 */
function openPopup(flg) {
	
	var url = popupUrl;
	url += "?type=" + $("type").value;
	
	isNew = flg;
	
	// 端末遅い人用に、ポップアップがでるまでのカバーをつける
	popupCoverElement = createAreaCoverElement();
	$$("body")[0].insert(popupCoverElement);
	
	new Ajax.Request(url, {
		method : 'post',
		onSuccess : viewPopup,
		onFailure : function(event)  {
			alert(event.responseText);
		},
		onException : function(event, ex)  {
			alert(event.responseText);
		}
	});
}

/**
 * ポップアップ表示
 */
function viewPopup(event) {

	 var popup = Dialog.confirm(event.responseText,
				{
					id: "popup",
					className: "alphacube",
					title: "改善情報設定",
					width:1200,
					height:500,
					draggable: true,
					destroyOnclose: true,
					recenterAuto: false,
					buttonClass : "buttons",
					okLabel: (isNew ? "追加" : "更新"),
					cancelLabel:"キャンセル",
					onOk: (isNew ? clickPopupAddButton : clickPopupUpdateButton),
					onCancel: clickPopupCancelButton,
					showEffectOptions: {duration:0.2},
					hideEffectOptions: {duration:0}
				}
		);
	popup.setZIndex(1000);
	popup.content.setStyle({overflow:"visible"});
	popupView = popup;

	// 削除ボタン付加
	if (!isNew) {
		var deleteButton = new Element("input");
		deleteButton.writeAttribute({
				type : "button",
				value : "削除"
		});
		deleteButton.addClassName("buttonsWEX05001");
		$$(".alphacube_buttons")[0].insert({
			top: deleteButton
		});
		deleteButton.observe("click",clickPopupDeleteButton);	
	}
	
	// 分類を設定
	initBunruiPopup();
	
	// カレンダーを設置
	if ($("type").value == "2") {
		new InputCalendar('pop_tgt_kikan_st', {
			inputReadOnly: true,
			lang : 'ja',
			weekFirstDay : ProtoCalendar.SUNDAY,
			format : 'yyyymmdd'
		});
		new InputCalendar('pop_tgt_kikan_ed', {
			inputReadOnly: true,
			lang : 'ja',
			weekFirstDay : ProtoCalendar.SUNDAY,
			format : 'yyyymmdd'
		});	
	} else {
		new InputMonthCalendar('pop_tgt_kikan_st', {
			inputReadOnly: true,
			lang : 'ja',
			weekFirstDay : ProtoCalendar.SUNDAY,
			format : 'yyyymm'
		});
		new InputMonthCalendar('pop_tgt_kikan_ed', {
			inputReadOnly: true,
			lang : 'ja',
			weekFirstDay : ProtoCalendar.SUNDAY,
			format : 'yyyymm'
		});
	}

	
	// 更新の場合
	if (!isNew) {
		
		var datas = selectedRow.getElementsBySelector(".hiddenData");
		var cells = selectedRow.getElementsBySelector(".tableCell");
		
		// 工程の表示値を設定
		var bnriDaiNm = cells[4].innerHTML;
		var bnriCyuNm = cells[5].innerHTML;
		var bnriSaiNm = cells[6].innerHTML;
		var bnriDaiCd = datas[4].innerHTML;
		var bnriCyuCd = datas[5].innerHTML;
		var bnriSaiCd = datas[6].innerHTML;
		var koteiText = bnriDaiNm;
		koteiText += bnriCyuNm == "" ? "" : " > " + bnriCyuNm;
		koteiText += bnriSaiNm == "" ? "" : " > " + bnriSaiNm;
		var koteiCd = bnriDaiCd + "_" + bnriCyuCd + "_" + bnriSaiCd;
		
		$("pop_kzn_data_no").value  = datas[1].innerHTML;     // 改善データ番号
		$("pop_kzn_title").value  = datas[8].innerHTML;       // 改善表題
		$("pop_kzn_text").value  = datas[9].innerHTML;        // 改善内容
		$("pop_tgt_kikan_st").value  = datas[2].innerHTML;    // 対象期間（自）
		$("pop_tgt_kikan_ed").value  = datas[3].innerHTML;    // 対象期間（至）
		$("pop_kotei_text").value = koteiText;                // 工程（ラベル値）
		$("pop_kotei_cd").value = koteiCd;                    // 工程（コード値）
		$("pop_kbn_kzn_kmk").setValue(datas[7].innerHTML);    // 改善項目
		$("pop_kzn_value").value  = datas[10].innerHTML;      // 数値目標
		$("pop_kzn_cost").value  = datas[11].innerHTML;       // 改善コスト
		
		$('pop_bnri_dai_cd').setValue(bnriDaiCd);
		$('pop_bnri_cyu_cd').setValue(bnriCyuCd);
		$('pop_bnri_sai_cd').setValue(bnriSaiCd);
		$('pop_bnri_dai_nm').setValue(bnriDaiNm);
		$('pop_bnri_cyu_nm').setValue(bnriCyuNm);
		$('pop_bnri_sai_nm').setValue(bnriSaiNm);
	}
}

/**
 * 追加ボタン押下処理（ポップアップ）
 */
function clickPopupAddButton(window) {

	var tableRow = null;
	var hiddenData = null;
	var tableCell = null;
	
	// 入力値を検証
	if (!validatePopup()) {
		return;
	}
	
	// 行作成
	tableRow = new Element("div");
	tableRow.addClassName("tableRow row_dat");
	tableRow.setStyle({ width:"1160px" });
	
	var options = $A($('pop_kbn_kzn_kmk').getElementsByTagName('option'));
	var kznKmkLbl = "";
	options.each(function(option){
		if (option.selected) {
			kznKmkLbl = option.innerHTML;
		}
	});
	
	// 隠し項目のdiv要素をを設定
	tableRow.insert(createHiddenDataElement("CHANGED", "2"));  
	tableRow.insert(createHiddenDataElement("KZN_DATA_NO", $("pop_kzn_data_no").value));
	tableRow.insert(createHiddenDataElement("TGT_KIKAN_ST", $("pop_tgt_kikan_st").value));
	tableRow.insert(createHiddenDataElement("TGT_KIKAN_ED", $("pop_tgt_kikan_ed").value));
	tableRow.insert(createHiddenDataElement("BNRI_DAI_CD", $("pop_bnri_dai_cd").value));
	tableRow.insert(createHiddenDataElement("BNRI_CYU_CD", $("pop_bnri_cyu_cd").value));
	tableRow.insert(createHiddenDataElement("BNRI_SAI_CD", $("pop_bnri_sai_cd").value));
	tableRow.insert(createHiddenDataElement("KBN_KZN_KMK", $("pop_kbn_kzn_kmk").value));
	tableRow.insert(createHiddenDataElement("KZN_TITLE", $("pop_kzn_title").value));
	tableRow.insert(createHiddenDataElement("KZN_TEXT", $("pop_kzn_text").value));
	tableRow.insert(createHiddenDataElement("KZN_VALUE", $("pop_kzn_value").value));
	tableRow.insert(createHiddenDataElement("KZN_COST", $("pop_kzn_cost").value));
	tableRow.insert(createHiddenDataElement("KBN_SYURYO", ""));
	
	// テーブルに表示する列を設定
	tableRow.insert(createViewCellElement(60, "新規", ""));
	tableRow.insert(createViewCellElement(200, $("pop_kzn_title").value, "nm"));
	tableRow.insert(createViewCellElement(100, $("pop_type").value == "1" ? formatYm($("pop_tgt_kikan_st").value) : formatDate($("pop_tgt_kikan_st").value), "date"));
	tableRow.insert(createViewCellElement(100, $("pop_type").value == "1" ? formatYm($("pop_tgt_kikan_ed").value) : formatDate($("pop_tgt_kikan_ed").value), "date"));
	tableRow.insert(createViewCellElement(100, $("pop_bnri_dai_nm").value, "nm"));
	tableRow.insert(createViewCellElement(100, $("pop_bnri_cyu_nm").value, "nm"));
	tableRow.insert(createViewCellElement(100, $("pop_bnri_sai_nm").value, "nm"));
	tableRow.insert(createViewCellElement(100, kznKmkLbl, "kbn"));
	tableRow.insert(createViewCellElement(100, num3Format($("pop_kzn_value").value), "kin"));
	tableRow.insert(createViewCellElement(100, num3Format($("pop_kzn_cost").value), "kin"));
	
	tableRow.observe("click", clickTableRow);
	tableRow.observe("mouseover", overRow);
	tableRow.observe("mouseout", leaveRow);
	
	//テーブルに配置
	$("lstTable").insert(tableRow);

	tableRow.setStyle({
		background: "#b0c4de"
	});
	
	window.close();
	dbclickFlag = 0;
	popupCoverElement.remove();
}

/**
 * 更新ボタン押下処理（ポップアップ）
 * 
 * @param window
 */
function clickPopupUpdateButton(window) {
	
	// 入力値を検証
	if (!validatePopup()) {
		return;
	}
	
	var datas = selectedRow.getElementsBySelector(".hiddenData");
	var cells = selectedRow.getElementsBySelector(".tableCell");
	
	var options = $A($('pop_kbn_kzn_kmk').getElementsByTagName('option'));
	var tgtKikanSt = $("pop_type").value == "1" ? formatYm($("pop_tgt_kikan_st").value) : formatDate($("pop_tgt_kikan_st").value);
	var tgtKikanEd = $("pop_type").value == "1" ? formatYm($("pop_tgt_kikan_ed").value) : formatDate($("pop_tgt_kikan_ed").value);
	var kznKmkLbl = "";
	options.each(function(option){
		if (option.selected) {
			kznKmkLbl = option.innerHTML;
		}
	});
	
	// 入力内容に変更がある場合のみ
	if (datas[2].innerHTML != $("pop_tgt_kikan_st").value
			|| datas[3].innerHTML != $("pop_tgt_kikan_ed").value
			|| datas[4].innerHTML != $("pop_bnri_dai_cd").value
			|| datas[5].innerHTML != $("pop_bnri_cyu_cd").value
			|| datas[6].innerHTML != $("pop_bnri_sai_cd").value
			|| datas[7].innerHTML != $("pop_kbn_kzn_kmk").value
			|| datas[8].innerHTML != $("pop_kzn_title").value
			|| datas[9].innerHTML != $("pop_kzn_text").value
			|| datas[10].innerHTML != $("pop_kzn_value").value
			|| datas[11].innerHTML != $("pop_kzn_cost").value) {
		
		// 隠し項目を更新
		datas[0].innerHTML = (datas[0].innerHTML == "2" ? "2" : "1");
		datas[1].innerHTML = $("pop_kzn_data_no").value;
		datas[2].innerHTML = $("pop_tgt_kikan_st").value;
		datas[3].innerHTML = $("pop_tgt_kikan_ed").value;
		datas[4].innerHTML = $("pop_bnri_dai_cd").value;
		datas[5].innerHTML = $("pop_bnri_cyu_cd").value;
		datas[6].innerHTML = $("pop_bnri_sai_cd").value;
		datas[7].innerHTML = $("pop_kbn_kzn_kmk").value;
		datas[8].innerHTML = $("pop_kzn_title").value;
		datas[9].innerHTML = $("pop_kzn_text").value;
		datas[10].innerHTML = $("pop_kzn_value").value;
		datas[11].innerHTML = $("pop_kzn_cost").value;
		
		
		// 値を変更した箇所の背景職を変更
		if (cells[2].innerHTML.trim() != tgtKikanSt) {
			setUpdatedCellColor(cells[2]);
		}
		if (cells[3].innerHTML.trim() != tgtKikanEd) {
			setUpdatedCellColor(cells[3]);
		}
		if (cells[4].innerHTML.trim() != $("pop_bnri_dai_nm").value 
				|| cells[5].innerHTML.trim() != $("pop_bnri_cyu_nm").value 
				|| cells[6].innerHTML.trim() != $("pop_bnri_sai_nm").value) {
			setUpdatedCellColor(cells[4]);
			setUpdatedCellColor(cells[5]);
			setUpdatedCellColor(cells[6]);
		}
		if (cells[7].innerHTML.trim() != kznKmkLbl) {
			setUpdatedCellColor(cells[7]);
		}
		if (cells[8].innerHTML.trim() != num3Format($("pop_kzn_value").value)) {
			setUpdatedCellColor(cells[8]);
		}
		if (cells[9].innerHTML.trim() != num3Format($("pop_kzn_cost").value)) {
			setUpdatedCellColor(cells[9]);
		}
		
		// 表示項目を更新
		if (datas[0].innerHTML == "2") {
			if (cells[1].innerHTML.trim() != $("pop_kzn_title").value) {
				setUpdatedCellColor(cells[1]);
			}
			cells[1].innerHTML = $("pop_kzn_title").value;
		} else {
			var hrefTtl = selectedRow.getElementsBySelector(".hrefKznTitle");
			if (hrefTtl[0].innerHTML.trim() != $("pop_kzn_title").value) {
				setUpdatedCellColor(cells[1]);
			}
			hrefTtl[0].innerHTML = $("pop_kzn_title").value;
		}
		cells[2].innerHTML = tgtKikanSt;
		cells[3].innerHTML = tgtKikanEd;
		cells[4].innerHTML = $("pop_bnri_dai_nm").value;
		cells[5].innerHTML = $("pop_bnri_cyu_nm").value;
		cells[6].innerHTML = $("pop_bnri_sai_nm").value;
		cells[7].innerHTML = kznKmkLbl;
		cells[8].innerHTML = num3Format($("pop_kzn_value").value);
		cells[9].innerHTML = num3Format($("pop_kzn_cost").value);
		
		setRowColor(datas[0].innerHTML, selectedRow);
	}
	
	window.close();
	dbclickFlag = 0;
	popupCoverElement.remove();
}

/**
 * 削除ボタン押下処理（ポップアップ）
 * 
 */
function clickPopupDeleteButton() {

	var cells = selectedRow.getElementsBySelector(".tableCell");
	var datas = selectedRow.getElementsBySelector(".hiddenData");
	
	setDeletedRowColor(selectedRow);
	
	for (var i = 0; i < cells.length; i++) {
		cells[i].setStyle({
			background: "inherit"
		});
	}
	
	selectedRow.stopObserving("click");
	selectedRow.stopObserving("mouseover");
	selectedRow.stopObserving("mouseout");
	selectedRow.observe("click", clickDeletedRow);
	
	// 何もない場合
	if (datas[0].innerHTML == "0") {
		datas[0].innerHTML = "3";
		
	// 更新の場合
	} else if (datas[0].innerHTML == "1") {
		datas[0].innerHTML = "4";
		
	// 新規の場合
	} else if (datas[0].innerHTML == "2") {
		datas[0].innerHTML = "5";
		
	}

	popupView.close();
	dbclickFlag = 0;
	popupCoverElement.remove();
}

/**
 * ポップアップの入力値検証
 * 
 */
function validatePopup() {
	
	// 改善表題
	var kznTitle = $("pop_kzn_title").value;
	if (kznTitle == "") {
		alert(DCMSMessage.format("CMNE000001", "改善表題"));
		return;
	}
	if (15 < kznTitle.length) {
		alert(DCMSMessage.format("CMNE000003", "改善表題", "15"));
		return;
	}
	
	// 改善内容
	var kznText = $("pop_kzn_text").value;
	if (kznText != "" && 5000 < kznText.length) {
		alert(DCMSMessage.format("CMNE000003", "改善内容", "5000"));
		return false;
	}
	
	// 対象期間
	var tgtKikanSt = $("pop_tgt_kikan_st").value;
	var tgtKikanEd = $("pop_tgt_kikan_ed").value;
	if (tgtKikanSt == "") {
		alert(DCMSMessage.format("CMNE000001", "対象期間（自）"));
		return false;
	}
	if (tgtKikanEd == "") {
		alert(DCMSMessage.format("CMNE000001", "対象期間（至）"));
		return false;
	}
	if ($("pop_type").value == "1") {
		if (tgtKikanSt.length != 6) {
			alert(DCMSMessage.format("CMNE000004", "対象期間（自）", "6"));
			return false;
		}
		if (tgtKikanEd.length != 6) {
			alert(DCMSMessage.format("CMNE000004", "対象期間（至）", "6"));
			return false;
		}
		if (!DCMSValidation.checkDateYm(tgtKikanSt)) {
			alert(DCMSMessage.format("CMNE000007", "対象期間（自）"));
			return false;
		}
		if (!DCMSValidation.checkDateYm(tgtKikanEd)) {
			alert(DCMSMessage.format("CMNE000007", "対象期間（自）"));
			return false;
		}
	} else {
		if (tgtKikanSt.length != 8) {
			alert(DCMSMessage.format("CMNE000004", "対象期間（自）", "8"));
			return false;
		}
		if (tgtKikanEd.length != 8) {
			alert(DCMSMessage.format("CMNE000004", "対象期間（至）", "8"));
			return false;
		}
		if (!DCMSValidation.checkDate(tgtKikanSt)) {
			alert(DCMSMessage.format("CMNE000007", "対象期間（自）"));
			return false;
		}
		if (!DCMSValidation.checkDate(tgtKikanEd)) {
			alert(DCMSMessage.format("CMNE000007", "対象期間（自）"));
			return false;
		}
	}
	if (tgtKikanEd < tgtKikanSt) {
		alert(DCMSMessage.format("CMNE000011", "対象期間", "終了年月日", "開始年月日"));
		return false;
	}
	
	// 工程
	var kotei = $("pop_bnri_dai_cd").value;
	if (kotei == "") {
		alert(DCMSMessage.format("CMNE000001", "工程"));
		return false;
	}
	
	// 改善項目
	var kbnKznKmk = $("pop_kbn_kzn_kmk").value;
	if (kbnKznKmk == "") {
		alert(DCMSMessage.format("CMNE000001", "改善項目"));
		return false;
	}
	
	// 数値目標
	var kznValue = $("pop_kzn_value").value;
	var intLen = 0;
	var decLen = 0;
	if (kznValue == "") {
		alert(DCMSMessage.format("CMNE000001", "数値目標"));
		return false;
	}
	if (kbnKznKmk == "01" 
			|| kbnKznKmk == "06" 
			|| kbnKznKmk == "07") {  // 単価／売上／利益
		intLen = 12;
	} else if (kbnKznKmk == "02") { // 時間
		intLen = 9; decLen = 2;
	} else if (kbnKznKmk == "03") { // 人数
		intLen = 4;
	} else if (kbnKznKmk == "04") { // 平均物量
		intLen = 10;
	} else if (kbnKznKmk == "05") { // 平均生産性
		intLen = 6;	decLen = 1;
	}
	if (decLen != 0) {
		if (!DCMSValidation.decLength(kznValue, intLen, decLen)) {
			alert(DCMSMessage.format("CMNE000005", "数値目標", intLen, decLen));
			return false;
		}
	} else {
		if (!DCMSValidation.numeric(kznValue) || intLen < kznValue.length) {
			alert(DCMSMessage.format("CMNE000026", "数値目標", intLen));
			return false;
		}
	}

	// 改善コスト
	var kznCost = $("pop_kzn_cost").value;
	if (kznCost == "") {
		alert(DCMSMessage.format("CMNE000001", "改善コスト"));
		return false;
	}
	if (!DCMSValidation.numeric(kznCost)) {
		alert(DCMSMessage.format("CMNE000002", "改善コスト"));
		return false;
	}
	if (12 < kznCost.length) {
		alert(DCMSMessage.format("CMNE000003", "改善コスト", "12"));
		return false;
	}
	
	return true;
}

/**
 * 隠し項目のdiv要素の生成
 * 
 * @param name 識別名称
 * @param value 値
 * @returns 隠し項目のdiv要素
 */
function createHiddenDataElement(name, value) {
	
	var hiddenData = new Element("div");
	
	hiddenData.addClassName("hiddenData " + name);
	hiddenData.innerHTML = value;
	
	return hiddenData;
}

/**
 * テーブルに表示する列のdiv要素の生成
 * 
 * @param width 列幅
 * @param value 値
 * @param clsName クラス名
 * @returns テーブルに表示する列のdiv要素
 */
function createViewCellElement(size, value, clsName) {
	
	var tableCell = new Element("div");
	
	tableCell.addClassName("tableCell cell_dat " + clsName);
	tableCell.setStyle({ width:(size + "px") });
	tableCell.innerHTML = value;

	return tableCell;
}

/**
 * キャンセルボタン押下処理（ポップアップ）
 * 
 */
function clickPopupCancelButton() {

	window.close();
	dbclickFlag = 0;
	popupCoverElement.remove();
}

/**
 * 解除ボタン押下処理（ポップアップ）
 * 
 * @param window
 */
function clickOKReverseButton(window) {

	var cells = selectedRow.getElementsBySelector(".tableCell");
	var datas = selectedRow.getElementsBySelector(".hiddenData");

	selectedRow.setStyle({
		background: "white"
	});

	selectedRow.stopObserving("click");
	selectedRow.observe("click", clickTableRow);
	selectedRow.observe("mouseover", overRow);
	selectedRow.observe("mouseout", leaveRow);

	// 何もない場合
	if (datas[0].innerHTML == "3") {
		datas[0].innerHTML = "0";

	// 更新の場合
	} else if (datas[0].innerHTML == "4") {
		datas[0].innerHTML = "1";

	// 新規の場合
	} else if (datas[0].innerHTML == "5") {
		datas[0].innerHTML = "2";

	}

	// 新規の場合はそのまま
	setRowColor(datas[0].innerHTML, selectedRow)

	window.close();
	dbclickFlag = 0;
	popupCoverElement.remove();
}

/**
 * 削除行押下時処理
 * 
 * @param event
 */
function clickDeletedRow(event) {

	if (event.isRightClick()) {
		return;
	}
	if (dbclickFlag == 1) {
		dbclickFlag = 0;
		return;
	}
	dbclickFlag = 1;

	//端末遅い人用に、ポップアップがでるまでのカバーをつける
	popupCoverElement = createAreaCoverElement();
	$$("body")[0].insert(popupCoverElement);

	// 選択行を保持
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

	var popup = Dialog.confirm("<br>削除を解除しますか？", {
					id: "popup",
					className: "alphacube",
					title: "改善DB設定",
					width:400,
					height:100,
					draggable: true,
					destroyOnclose: true,
					recenterAuto: false,
					buttonClass : "buttons",
					okLabel: "解除",
					cancelLabel:"キャンセル",
					onOk: clickOKReverseButton,
					onCancel: clickPopupCancelButton,
					showEffectOptions: {duration:0.2},
					hideEffectOptions: {duration:0}
	});
	popup.setZIndex(1000);
}















//----------------------------　コンボ ----------------------------

//選択した内容をテキストボックスに反映
function setMenuProcessPopup( dai_value, cyu_value, sai_value, koutei_dai, koutei_cyu, koutei_sai , koteiNo){
	// 値格納
	var processVal;
	var processNmVal;
	
	// 値の判定
	if((koutei_dai != '') && (koutei_cyu != '') && (koutei_sai != '')){
	
		processVal = dai_value + '_' + cyu_value + '_' + sai_value;
		processNmVal = koutei_dai + ' > ' + koutei_cyu + ' > ' + koutei_sai;
	}else if(koutei_cyu != ''){
	
		processVal = dai_value + '_' + cyu_value;
		processNmVal = koutei_dai + ' > ' + koutei_cyu;
	}else{
	
		processVal = dai_value;
		processNmVal = koutei_dai;
	}
	
	$('pop_kotei_cd').setValue(processVal);
	$('pop_kotei_text').setValue(processNmVal);
	$('pop_bnri_dai_cd').setValue(dai_value);
	$('pop_bnri_cyu_cd').setValue(cyu_value);
	$('pop_bnri_sai_cd').setValue(sai_value);
	$('pop_bnri_dai_nm').setValue(koutei_dai);
	$('pop_bnri_cyu_nm').setValue(koutei_cyu);
	$('pop_bnri_sai_nm').setValue(koutei_sai);

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

//マウスオーバ時(工程:大)
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

//マウスオーバ時(工程:中)
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

//マウスアウト時
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

