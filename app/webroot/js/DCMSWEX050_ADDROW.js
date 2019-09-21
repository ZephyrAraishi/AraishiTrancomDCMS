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
	    location.href = "/DCMS/WEX050/index?return_cd=91&message=" + Base64.encode(DCMSMessage.format("CMNE000101"));
	  },
	  onException : function( event, ex )  {
	    location.href = "/DCMS/WEX050/index?return_cd=92&message=" + Base64.encode(DCMSMessage.format("CMNE000102"));
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
					title: "細分類マスタ設定",
					width:1000,
					height:350,
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

}


//OKボタンクリックイベントハンドラ
function clickOKButton2(window) {

	var bnriDaiNm = "";
	var bnriCyuNm = "";
	var bnriDaiCd = "";
	var bnriCyuCd = "";

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


	//　入力チェック
	//必須チェック
	if (!DCMSValidation.notEmpty(bnriDaiCd)) {
		alert(DCMSMessage.format("CMNE000001", "大分類コード"));
		return;
	}
	if (!DCMSValidation.notEmpty(bnriCyuCd)) {
		alert(DCMSMessage.format("CMNE000001", "中分類コード"));
		return;
	}
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
	if (parseInt($F("bnriSaiCd"),10) <= 0 ) {
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
	
	//行作成
	var tableRow = new Element("div");
	tableRow.addClassName("tableRow row_dat");
	tableRow.setStyle({
		width:"1575px"
	});
	
	var tableCellBango = new Element("div");
	tableCellBango.addClassName("tableCellBango cell_dat");
	tableCellBango.setStyle({
		width:"60px"
	});
	tableCellBango.innerHTML = "新規";
	tableRow.insert(tableCellBango);
	
	//11個セル作成
	var tableCell = new Element("div");
	tableCell.addClassName("tableCell cell_dat nm");
	tableCell.setStyle({
		width:"100px"
	});
	tableRow.insert(tableCell);
	
	var tableCell = new Element("div");
	tableCell.addClassName("tableCell cell_dat nm");
	tableCell.setStyle({
		width:"100px"
	});
	tableRow.insert(tableCell);
	
	var tableCell = new Element("div");
	tableCell.addClassName("tableCell cell_dat cd");
	tableCell.setStyle({
		width:"50px"
	});
	tableRow.insert(tableCell);

	tableCell = new Element("div");
	tableCell.addClassName("tableCell cell_dat nm");
	tableCell.setStyle({
		width:"200px"
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
		width:"205px"
	});
	tableRow.insert(tableCell);
	
	tableCell = new Element("div");
	tableCell.addClassName("tableCell cell_dat kbn");
	tableCell.setStyle({
		width:"80px"
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
		width:"60px"
	});
	tableRow.insert(tableCell);
	
	tableCell = new Element("div");
	tableCell.addClassName("tableCell cell_dat kbn");
	tableCell.setStyle({
		width:"60px"
	});
	tableRow.insert(tableCell);
	
	tableCell = new Element("div");
	tableCell.addClassName("tableCell cell_dat kbn");
	tableCell.setStyle({
		width:"60px"
	});
	tableRow.insert(tableCell);

	tableCell = new Element("div");
	tableCell.addClassName("tableCell cell_dat kbn");
	tableCell.setStyle({
		width:"60px"
	});
	tableRow.insert(tableCell);

	tableCell = new Element("div");
	tableCell.addClassName("tableCell cell_dat kbn");
	tableCell.setStyle({
		width:"60px"
	});
	tableRow.insert(tableCell);

	tableCell = new Element("div");
	tableCell.addClassName("tableCell cell_dat kbn");
	tableCell.setStyle({
		width:"60px"
	});
	tableRow.insert(tableCell);

	tableCell = new Element("div");
	tableCell.addClassName("tableCell cell_dat kbn");
	tableCell.setStyle({
		width:"60px"
	});
	tableRow.insert(tableCell);

	//データDIVを13個配置
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
		hiddenData.addClassName("hiddenData KBN_TANI");
		tableRow.insert(hiddenData);
	
		var hiddenData = new Element("div");
		hiddenData.addClassName("hiddenData KBN_KTD_MKTK");
		tableRow.insert(hiddenData);
	
		var hiddenData = new Element("div");
		hiddenData.addClassName("hiddenData KBN_HISSU_WK");
		tableRow.insert(hiddenData);
	
		var hiddenData = new Element("div");
		hiddenData.addClassName("hiddenData KBN_GYOMU");
		tableRow.insert(hiddenData);
	
		var hiddenData = new Element("div");
		hiddenData.addClassName("hiddenData KBN_GET_DATA");
		tableRow.insert(hiddenData);
	
		var hiddenData = new Element("div");
		hiddenData.addClassName("hiddenData KBN_FUKAKACHI");
		tableRow.insert(hiddenData);
	
		var hiddenData = new Element("div");
		hiddenData.addClassName("hiddenData KBN_SENMON");
		tableRow.insert(hiddenData);
	
		var hiddenData = new Element("div");
		hiddenData.addClassName("hiddenData KBN_KTD_TYPE");
		tableRow.insert(hiddenData);
		
		var hiddenData = new Element("div");
		hiddenData.addClassName("hiddenData KEIYAKU_BUTURYO_FLG");
		tableRow.insert(hiddenData);

		var hiddenData = new Element("div");
		hiddenData.addClassName("hiddenData BUTTON_SEQ");
		tableRow.insert(hiddenData);
	
		var hiddenData = new Element("div");
		hiddenData.addClassName("hiddenData LIST_SEQ");
		tableRow.insert(hiddenData);
	
		var hiddenData = new Element("div");
		hiddenData.addClassName("hiddenData SYURYO_NASI_FLG");
		tableRow.insert(hiddenData);
		
		var hiddenData = new Element("div");
		hiddenData.addClassName("hiddenData KYORI_FLG");
		tableRow.insert(hiddenData);

//	}




	//値を設置
	//　下、両方親Viewの中
	var cells = tableRow.getElementsBySelector(".tableCell");
	var data = tableRow.getElementsBySelector(".hiddenData");

	
	// テキスト値取得
	// 　下、子Viewの中
	var bnriSaiCd = $F("bnriSaiCd");
	var bnriSaiNm = $F("bnriSaiNm");
	var bnriSaiRyaku = $F("bnriSaiRyaku");
	var bnriSaiExp = $F("bnriSaiExp");

	var kbnTani = $F("kbn_tani");
	if(kbnTani == null){
		kbnTani = "";
	}
	var kbnKtdMktk = $F("kbn_ktd_mktk");
	if(kbnKtdMktk == null){
		kbnKtdMktk = "";
	}
	var kbnHissuWk = $F("kbn_hissu_wk");
	if(kbnHissuWk == null){
		kbnHissuWk = "";
	}
	var kbnGyomu = $F("kbn_gyomu");
	if(kbnGyomu == null){
		kbnGyomu = "";
	}

	var kbnGetData = $F("kbn_get_data");
	if(kbnGetData == null){
		kbnGetData = "";
	}
	var kbnFukakachi = $F("kbn_fukakachi");
	if(kbnFukakachi == null){
		kbnFukakachi = "";
	}
	var kbnSenmon = $F("kbn_senmon");
	if(kbnSenmon == null){
		kbnSenmon = "";
	}
	var kbnKtdType = $F("kbn_ktd_type");
	if(kbnKtdType == null){
		kbnKtdType = "";
	}
	var keiyakuButuryoFlg = $F("keiyaku_buturyo_flg");
	if(keiyakuButuryoFlg == null){
		keiyakuButuryoFlg = "";
	}
	
	//変更項目の処理
	cells[2].innerHTML = bnriSaiCd;
	cells[3].innerHTML = bnriSaiNm;
	cells[4].innerHTML = bnriSaiRyaku;
	cells[5].innerHTML = bnriSaiExp;
//	cells[6].innerHTML = "新規";

	cells[0].innerHTML = bnriDaiNm;
	cells[1].innerHTML = bnriCyuNm;

	// 単位の名称を取得
	var kbn_tani_nm = "";
	var options = $A($('kbn_tani').getElementsByTagName('option'));
	options.each(function(option){
		if (option.selected) {
			kbn_tani_nm = option.innerHTML;
		}
	});
	cells[6].innerHTML = kbn_tani_nm;

	// 活動目的の名称を取得
	var kbn_ktd_mktk_nm = "";
	var options = $A($('kbn_ktd_mktk').getElementsByTagName('option'));
	options.each(function(option){
		if (option.selected) {
			kbn_ktd_mktk_nm = option.innerHTML;
		}
	});
	cells[7].innerHTML = kbn_ktd_mktk_nm;

	// 必須作業区分の名称を取得
	var kbn_hissu_wk_nm = "";
	var options = $A($('kbn_hissu_wk').getElementsByTagName('option'));
	options.each(function(option){
		if (option.selected) {
			kbn_hissu_wk_nm = option.innerHTML;
		}
	});
	cells[8].innerHTML = kbn_hissu_wk_nm;

	// 業務区分の名称を取得
	var kbn_gyomu_nm = "";
	var options = $A($('kbn_gyomu').getElementsByTagName('option'));
	options.each(function(option){
		if (option.selected) {
			kbn_gyomu_nm = option.innerHTML;
		}
	});
	cells[9].innerHTML = kbn_gyomu_nm;

	// データ取得区分の名称を取得
	var kbn_get_data_nm = "";
	var options = $A($('kbn_get_data').getElementsByTagName('option'));
	options.each(function(option){
		if (option.selected) {
			kbn_get_data_nm = option.innerHTML;
		}
	});
	cells[10].innerHTML = kbn_get_data_nm;
	cells[11].innerHTML = $F("button_seq");
	cells[12].innerHTML = $F("list_seq");

	var syuryo_nasi_flg = $F("syuryo_nasi_flg");
	if(syoryo_nasi_flg = "1"){
		cells[13].innerHTML = "有";
	}

	var kyori_flg = $F("kyori_flg");
	if(kyori_flg = "1"){
		cells[14].innerHTML = "有";
	}

	data[0].innerHTML = "0";
	data[1].innerHTML = "2";	//新規フラグ
	data[2].innerHTML = bnriDaiCd;
	data[3].innerHTML = bnriCyuCd;
	data[4].innerHTML = $F("kbn_tani");
	data[5].innerHTML = $F("kbn_ktd_mktk");
	data[6].innerHTML = $F("kbn_hissu_wk");
	data[7].innerHTML = $F("kbn_gyomu");
	data[8].innerHTML = $F("kbn_get_data");
	data[9].innerHTML = $F("kbn_fukakachi");
	data[10].innerHTML = $F("kbn_senmon");
	data[11].innerHTML = $F("kbn_ktd_type");
	data[12].innerHTML = $F("keiyaku_buturyo_flg");
	data[13].innerHTML = $F("button_seq");
	data[14].innerHTML = $F("list_seq");
	data[15].innerHTML = $F("syuryo_nasi_flg");
	data[16].innerHTML = $F("kyori_flg");


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