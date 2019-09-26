//選択行
var selectedRow;

//行の値保持
var orgValDaiCd = "";
var orgValCyuCd = "";
var orgValSaiCd = "";
var orgValSaiNm = "";
var orgValSaiRyaku = "";
var orgValSaiExp = "";
var originValKbnTani = "";
var originValKbnKtdMktk = "";
var originValKbnHissuWk = "";
var originValKbnGyomu = "";
var originValKbnGetData = "";
var originValKbnFukakachi = "";
var originValKbnSenmon = "";
var originValKbnKtdType = "";
var originValKeiyakuButuryoFlg = "";
var originValButtonSeq = "";
var originValListSeq = "";
var originValSyuryoNasiFlg = "";
var originValKyoriFlg = "";

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


	//ポップアップを読み込む
	new Ajax.Request( actionUrl, {
	  method : 'post',

	  onSuccess : viewPopUp,

	  onFailure : function( event )  {
	    location.href = "/DCMS/WEX050/index?return_cd=91&message=" + Base64.encode(DCMSMessage.format("CMNE000101"));
	  },
	  onException : function( event, ex )  {
	    location.href = "/DCMS/WEX050/index?return_cd=92&message=" + Base64.encode(DCMSMessage.format("CMNE000102"));
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
					title: "細分類マスタ設定",
					width:1000,
					height:350,
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

	//キャンセルボタン負荷
	var deleteButton = new Element("input");
	deleteButton.writeAttribute({
			type : "button",
			value : "削除"
	});
	deleteButton.addClassName("buttonsWEX05001");

	$$(".alphacube_buttons")[0].insert({
		top: deleteButton
	});

	deleteButton.observe("click",clickDeleteButton);


	//初期値設定
	var cells = selectedRow.getElementsBySelector(".tableCell");
	var data = selectedRow.getElementsBySelector(".hiddenData");



	$("bnriDaiCd").value = cells[0].innerHTML;
	$("bnriCyuCd").value = cells[1].innerHTML;
	$("bnriSaiCd").value = cells[2].innerHTML;
	$("bnriSaiNm").value     = cells[3].innerHTML;
	$("bnriSaiRyaku").value  = cells[4].innerHTML;
	$("bnriSaiExp").value = cells[5].innerHTML;

	$("kbn_tani").value      = data[4].innerHTML;		// 単位
	$("kbn_ktd_mktk").value  = data[5].innerHTML;
	$("kbn_hissu_wk").value  = data[6].innerHTML;
	$("kbn_gyomu").value     = data[7].innerHTML;
	$("kbn_get_data").value  = data[8].innerHTML;
	$("kbn_fukakachi").value = data[9].innerHTML;
	$("kbn_senmon").value    = data[10].innerHTML;
	$("kbn_ktd_type").value  = data[11].innerHTML;
	$("keiyaku_buturyo_flg").value  = data[12].innerHTML;
	$("button_seq").value    = data[13].innerHTML;
	$("list_seq").value  = data[14].innerHTML;
	$("syuryo_nasi_flg").value  = data[15].innerHTML;
	$("kyori_flg").value  = data[16].innerHTML;


	//値を保持
	//　clickOKButtonで使用するため
	orgValSaiCd = cells[2].innerHTML;
	orgValSaiNm    = cells[3].innerHTML;
	orgValSaiRyaku = cells[4].innerHTML;
	orgValSaiExp = cells[5].innerHTML;

	originValKbnTani      = data[4].innerHTML;
	originValKbnKtdMktk   = data[5].innerHTML;
	originValKbnHissuWk   = data[6].innerHTML;
	originValKbnGyomu     = data[7].innerHTML;
	originValKbnGetData   = data[8].innerHTML;
	originValKbnFukakachi = data[9].innerHTML;
	originValKbnSenmon    = data[10].innerHTML;
	originValKbnKtdType   = data[11].innerHTML;
	originValKeiyakuButuryoFlg   = data[12].innerHTML;
	originValButtonSeq   = data[13].innerHTML;
	originValListSeq   = data[14].innerHTML;
	originValSyuryoNasiFlg   = data[15].innerHTML;
	originValKyoriFlg   = data[16].innerHTML;

}


//更新ボタンクリックイベントハンドラ
function clickOKButton(window) {

	//値を設置
	//　下、両方親Viewの中
	var cells = selectedRow.getElementsBySelector(".tableCell");
	var data = selectedRow.getElementsBySelector(".hiddenData");

	// テキスト値取得
	// 　下、子Viewの中
	var bnriSaiCd    = $F("bnriSaiCd");
	var bnriSaiExp   = $F("bnriSaiExp");
	var bnriSaiNm    = $F("bnriSaiNm");
	var bnriSaiRyaku = $F("bnriSaiRyaku");

	// selectは未選択時にnullになるため""に置き換え
	var kbnTani = $F("kbn_tani");
	if(kbnTani == null){
		kbnTani = "";
	}
	// selectは未選択時にnullになるため""に置き換え
	var kbnKtdMktk = $F("kbn_ktd_mktk");
	if(kbnKtdMktk == null){
		kbnKtdMktk = "";
	}
	// selectは未選択時にnullになるため""に置き換え
	var kbnHissuWk = $F("kbn_hissu_wk");
	if(kbnHissuWk == null){
		kbnHissuWk = "";
	}
	// selectは未選択時にnullになるため""に置き換え
	var kbnGyomu = $F("kbn_gyomu");
	if(kbnGyomu == null){
		kbnGyomu = "";
	}
	// selectは未選択時にnullになるため""に置き換え
	var kbnGetData = $F("kbn_get_data");
	if(kbnGetData == null){
		kbnGetData = "";
	}
	// selectは未選択時にnullになるため""に置き換え
	var kbnFukakachi = $F("kbn_fukakachi");
	if(kbnFukakachi == null){
		kbnFukakachi = "";
	}
	// selectは未選択時にnullになるため""に置き換え
	var kbnSenmon = $F("kbn_senmon");
	if(kbnSenmon == null){
		kbnSenmon = "";
	}
	// selectは未選択時にnullになるため""に置き換え
	var kbnKtdType = $F("kbn_ktd_type");
	if(kbnKtdType == null){
		kbnKtdType = "";
	}
	// selectは未選択時にnullになるため""に置き換え
	var keiyakuButuryoFlg = $F("keiyaku_buturyo_flg");
	if(keiyakuButuryoFlg == null){
		keiyakuButuryoFlg = "";
	}

	//　入力チェック
	//必須チェック
	if (!DCMSValidation.notEmpty($F("bnriSaiCd"))) {
		alert(DCMSMessage.format("CMNE000001", "細分類コード"));
		return;
	}
	if (!DCMSValidation.notEmpty($F("bnriSaiNm"))) {
		alert(DCMSMessage.format("CMNE000001", "細分類名称"));
		return;
	}
	if (!DCMSValidation.notEmpty($F("bnriSaiRyaku"))) {
		alert(DCMSMessage.format("CMNE000001", "細分類略称"));
		return;
	}

	//数字チェック
	if (!DCMSValidation.numeric($F("bnriSaiCd"))) {
		alert(DCMSMessage.format("CMNE000002", "細分類コード"));
		return;
	}

	if ($F("button_seq") != "" && !DCMSValidation.numeric($F("button_seq"))) {
		alert(DCMSMessage.format("CMNE000002", "ボタン順"));
		return;
	}

	if ($F("list_seq") != "" && !DCMSValidation.numeric($F("list_seq"))) {
		alert(DCMSMessage.format("CMNE000002", "リスト順"));
		return;
	}

	//桁数チェック
	if (!DCMSValidation.numLength($F("bnriSaiCd"), 3)) {
		alert(DCMSMessage.format("CMNE000004", "細分類コード","3"));
		return;
	}

	//"000"は入力不可
	if (parseInt($F("bnriSaiCd"), 10) <= 0 ) {
		alert(DCMSMessage.format("CMNE000019", "細分類コード","001"));
		return;
	}

	//最大桁数チェック
	if (!DCMSValidation.maxLength($F("bnriSaiNm"), 20)) {
		alert(DCMSMessage.format("CMNE000003", "細分類名称"));
		return;
	}
	if (!DCMSValidation.maxLength($F("bnriSaiRyaku"), 6)) {
		alert(DCMSMessage.format("CMNE000003", "細分類略称"));
		return;
	}
	if (!DCMSValidation.maxLength($F("bnriSaiExp"), 40)) {
		alert(DCMSMessage.format("CMNE000003", "細分類説明"));
		return;
	}


	//更新

	var flgUpdate  = "0";

	// 　入力前と変化した場合

	if (bnriSaiNm != orgValSaiNm) {
		setUpdatedCellColor(cells[3]);		//項目のbackground変更

		cells[3].innerHTML = bnriSaiNm;		//値のセット
		flgUpdate  = "1";
	}

	if (bnriSaiRyaku != orgValSaiRyaku) {
		setUpdatedCellColor(cells[4]);		//項目のbackground変更

		cells[4].innerHTML = bnriSaiRyaku;		//値のセット
		flgUpdate  = "1";
	}

	if (bnriSaiExp != orgValSaiExp) {
		setUpdatedCellColor(cells[5]);		//項目のbackground変更

		cells[5].innerHTML = bnriSaiExp;		//値のセット
		flgUpdate  = "1";
	}


	if (kbnTani != originValKbnTani) {
		setUpdatedCellColor(cells[6]);		//項目のbackground変更

		// 単位の名称を取得
		var kbn_tani_nm = "";
		var options = $A($('kbn_tani').getElementsByTagName('option'));
		options.each(function(option){
			if (option.selected) {
				kbn_tani_nm = option.innerHTML;
			}
		});

		cells[6].innerHTML = kbn_tani_nm;
		data[4].innerHTML = $F("kbn_tani");
		flgUpdate  = "1";
	}

	if (kbnKtdMktk != originValKbnKtdMktk) {
		setUpdatedCellColor(cells[7]);		//項目のbackground変更

		// 活動目的の名称を取得
		var kbn_ktd_mktk_nm = "";
		var options = $A($('kbn_ktd_mktk').getElementsByTagName('option'));
		options.each(function(option){
			if (option.selected) {
				kbn_ktd_mktk_nm = option.innerHTML;
			}
		});

		cells[7].innerHTML = kbn_ktd_mktk_nm;
		data[5].innerHTML = $F("kbn_ktd_mktk");
		flgUpdate  = "1";
	}

	if (kbnHissuWk != originValKbnHissuWk) {
		setUpdatedCellColor(cells[8]);		//項目のbackground変更

		// 必須作業の名称を取得
		var kbn_hissu_wk_nm = "";
		var options = $A($('kbn_hissu_wk').getElementsByTagName('option'));
		options.each(function(option){
			if (option.selected) {
				kbn_hissu_wk_nm = option.innerHTML;
			}
		});

		cells[8].innerHTML = kbn_hissu_wk_nm;
		data[6].innerHTML = $F("kbn_hissu_wk");
		flgUpdate  = "1";
	}

	if (kbnGyomu != originValKbnGyomu) {
		setUpdatedCellColor(cells[9]);		//項目のbackground変更

		// 業務区分の名称を取得
		var kbn_gyomu_nm = "";
		var options = $A($('kbn_gyomu').getElementsByTagName('option'));
		options.each(function(option){
			if (option.selected) {
				kbn_gyomu_nm = option.innerHTML;
			}
		});

		cells[9].innerHTML = kbn_gyomu_nm;
		data[7].innerHTML = $F("kbn_gyomu");
		flgUpdate  = "1";
	}

	if (kbnGetData != originValKbnGetData) {
		setUpdatedCellColor(cells[10]);		//項目のbackground変更

		// データ取得区分の名称を取得
		var kbn_get_data_nm = "";
		var options = $A($('kbn_get_data').getElementsByTagName('option'));
		options.each(function(option){
			if (option.selected) {
				kbn_get_data_nm = option.innerHTML;
			}
		});

		cells[10].innerHTML = kbn_get_data_nm;
		data[8].innerHTML = $F("kbn_get_data");
		flgUpdate  = "1";
	}

	if (kbnFukakachi != originValKbnFukakachi) {
		data[9].innerHTML = $F("kbn_fukakachi");
		flgUpdate  = "1";
	}

	if (kbnSenmon != originValKbnSenmon) {
		data[10].innerHTML = $F("kbn_senmon");
		flgUpdate  = "1";
	}

	if (kbnKtdType != originValKbnKtdType) {
		data[11].innerHTML = $F("kbn_ktd_type");
		flgUpdate  = "1";
	}
	
	if (keiyakuButuryoFlg != originValKeiyakuButuryoFlg) {
		data[12].innerHTML = $F("keiyaku_buturyo_flg");
		flgUpdate  = "1";
	}

	if ($F("button_seq") != originValButtonSeq) {
		data[13].innerHTML = $F("button_seq");
		cells[11].innerHTML = $F("button_seq")
		flgUpdate  = "1";
	}


	if ($F("list_seq") != originValListSeq) {
		data[14].innerHTML = $F("list_seq");
		cells[12].innerHTML = $F("list_seq")
		flgUpdate  = "1";
	}

	if ($F("syuryo_nasi_flg") != originValSyuryoNasiFlg) {
		data[15].innerHTML = $F("syuryo_nasi_flg");
		
		if($F("syuryo_nasi_flg") == 1) {
			cells[13].innerHTML = "有";
		} else {
			cells[13].innerHTML = "";
		}
		flgUpdate  = "1";
	}

	if ($F("kyori_flg") != originValSyuryoNasiFlg) {
		data[16].innerHTML = $F("kyori_flg");
		
		if($F("kyori_flg") == 1) {
			cells[14].innerHTML = "有";
		} else {
			cells[14].innerHTML = "";
		}
		flgUpdate  = "1";
	}
//	if (kbnUriage != originValKbnUriage) {
//		data[12].innerHTML = $F("kbn_uriage");
//		flgUpdate  = "1";
//	}
//
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


//デリートした行を元に戻す
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

	//画面に配置
	var popup = Dialog.confirm("<br>削除を解除しますか？",
				   {
					id: "popup",
					className: "alphacube",
					title: "細分類マスタ設定",
					width:400,
					height:100,
					draggable: true,
					destroyOnclose: true,
					recenterAuto: false,
					buttonClass : "buttonsWEX05001",
					okLabel: "解除",
					cancelLabel:"キャンセル",
					onOk: clickOKReverseButton,
					onCancel: clickCANCELButton,
					showEffectOptions: {duration:0.2},
					hideEffectOptions: {duration:0}
				   });

	popup.setZIndex(1000);
}


//キャンセルボタンクリックイベントハンドラ
function clickOKReverseButton(window) {

	var cells = selectedRow.getElementsBySelector(".tableCell");
	var data = selectedRow.getElementsBySelector(".hiddenData");

	selectedRow.setStyle({
		background: "white"
	});

	selectedRow.stopObserving("click");
	selectedRow.observe("click",clickPopUp);
	selectedRow.observe("mouseover",overRow);
	selectedRow.observe("mouseout",leaveRow);

	//何もない場合
	if (data[1].innerHTML == "3") {

		data[1].innerHTML = "0";

	//更新の場合
	} else if (data[1].innerHTML == "4") {

		data[1].innerHTML = "1";

	//新規の場合
	} else if (data[1].innerHTML == "5") {

		data[1].innerHTML = "2";

	}

	//新規の場合はそのまま
	setRowColor(data[1].innerHTML, selectedRow)

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


//削除ボタンクリックイベントハンドラ
function clickDeleteButton(event) {

	var cells = selectedRow.getElementsBySelector(".tableCell");
	var data = selectedRow.getElementsBySelector(".hiddenData");


	setDeletedRowColor(selectedRow);

	for (var i=0; i < cells.length; i++) {

		cells[i].setStyle({
			background: "inherit"
		});
	}


	selectedRow.stopObserving("click");
	selectedRow.stopObserving("mouseover");
	selectedRow.stopObserving("mouseout");
	selectedRow.observe("click",clickDeletedRow);

	//何もない場合
	if (data[1].innerHTML == "0") {

		data[1].innerHTML = "3";

	//更新の場合
	} else if (data[1].innerHTML == "1") {

		data[1].innerHTML = "4";

	//新規の場合
	} else if (data[1].innerHTML == "2") {

		data[1].innerHTML = "5";

	}



	popUpView.close();
	dbclickFlag = 0;
	areaCoverElement.remove();
}
