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
	    location.href = "/DCMS/WEX040/index?return_cd=91&message=" + Base64.encode(DCMSMessage.format("CMNE000101"));
	  },
	  onException : function( event, ex )  {
	    location.href = "/DCMS/WEX040/index?return_cd=92&message=" + Base64.encode(DCMSMessage.format("CMNE000102"));
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
					title: "中分類マスタ設定",
					width:900,
					height:270,
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
	
	popUpView = popup
		
}


//OKボタンクリックイベントハンドラ
function clickOKButton2(window) {


	//　入力チェック
	//必須チェック
	if (!DCMSValidation.notEmpty($F("koteiCd"))) {
		alert(DCMSMessage.format("CMNE000001", "大分類コード"));
		return;
	}
	if (!DCMSValidation.notEmpty($F("bnriCyuCd"))) {
		alert(DCMSMessage.format("CMNE000001", "中分類コード"));
		return;
	}
	if (!DCMSValidation.notEmpty($F("bnriCyuNm"))) {
		alert(DCMSMessage.format("CMNE000001", "中分類名称"));
		return;
	}
	if (!DCMSValidation.notEmpty($F("bnriCyuRyaku"))) {
		alert(DCMSMessage.format("CMNE000001", "中分類略称"));
		return;
	}
	if (!DCMSValidation.notEmpty($F("kbn_tani"))) {
		alert(DCMSMessage.format("CMNE000001", "単位区分"));
		return;
	}
	if (!DCMSValidation.notEmpty($F("kbn_uriage"))) {
		alert(DCMSMessage.format("CMNE000001", "売上区分"));
		return;
	}

	//数字チェック
	if (!DCMSValidation.numeric($F("koteiCd"))) {
		alert(DCMSMessage.format("CMNE000002", "大分類コード"));
		return;
	}
	if (!DCMSValidation.numeric($F("bnriCyuCd"))) {
		alert(DCMSMessage.format("CMNE000002", "中分類コード"));
		return;
	}

	//桁数チェック
	if (!DCMSValidation.numLength($F("koteiCd"), 3)) {
		alert(DCMSMessage.format("CMNE000004", "大分類コード","3"));
		return;
	}
	if (!DCMSValidation.numLength($F("bnriCyuCd"), 3)) {
		alert(DCMSMessage.format("CMNE000004", "中分類コード","3"));
		return;
	}

	//"000"は入力不可
	if (parseInt($F("bnriCyuCd"), 10) <= 0 ) {
		alert(DCMSMessage.format("CMNE000019", "中分類コード","001"));
		return;
	}

	//最大桁数チェック
	if (!DCMSValidation.maxLength($F("bnriCyuNm"), 20)) {
		alert(DCMSMessage.format("CMNE000003", "中分類名称"));
		return;
	}
	if (!DCMSValidation.maxLength($F("bnriCyuRyaku"), 6)) {
		alert(DCMSMessage.format("CMNE000003", "中分類略称"));
		return;
	}
	if (!DCMSValidation.maxLength($F("bnriCyuExp"), 40)) {
		alert(DCMSMessage.format("CMNE000003", "中分類説明"));
		return;
	}


	
	//更新
	
	//行作成
	var tableRow = new Element("div");
	tableRow.addClassName("tableRow row_dat");
	tableRow.setStyle({
		width:"1235px"
	});
	
	var tableCellBango = new Element("div");
	tableCellBango.addClassName("tableCellBango cell_dat");
	tableCellBango.setStyle({
		width:"50px"
	});
	tableCellBango.innerHTML = "新規";
	tableRow.insert(tableCellBango);
	
	//8個セル作成
	var tableCell = new Element("div");
	tableCell.addClassName("tableCell cell_dat nm");
	tableCell.setStyle({
		width:"140px"
	});
	tableRow.insert(tableCell);
	
	var tableCell = new Element("div");
	tableCell.addClassName("tableCell cell_dat cd");
	tableCell.setStyle({
		width:"90px"
	});
	tableRow.insert(tableCell);
	
	tableCell = new Element("div");
	tableCell.addClassName("tableCell cell_dat nm");
	tableCell.setStyle({
		width:"240px"
	});
	tableRow.insert(tableCell);
	
	tableCell = new Element("div");
	tableCell.addClassName("tableCell cell_dat nm");
	tableCell.setStyle({
		width:"140px"
	});
	tableRow.insert(tableCell);
	
	tableCell = new Element("div");
	tableCell.addClassName("tableCell cell_dat nm");
	tableCell.setStyle({
		width:"320px"
	});
	tableRow.insert(tableCell);
	
	tableCell = new Element("div");
	tableCell.addClassName("tableCell cell_dat kbn");
	tableCell.setStyle({
		width:"75px"
	});
	tableRow.insert(tableCell);
	
	tableCell = new Element("div");
	tableCell.addClassName("tableCell cell_dat kbn");
	tableCell.setStyle({
		width:"40px"
	});
	tableRow.insert(tableCell);
	
	tableCell = new Element("div");
	tableCell.addClassName("tableCell cell_dat kbn");
	tableCell.setStyle({
		width:"50px"
	});
	tableRow.insert(tableCell);

	//データDIVを4個配置
//	for (var i=0 ; i< 2; i++) {
	
		var hiddenData = new Element("div");
		hiddenData.addClassName("hiddenData LINE_COUNT");
		tableRow.insert(hiddenData);
	
		var hiddenData = new Element("div");
		hiddenData.addClassName("hiddenData CHANGED");
		tableRow.insert(hiddenData);
	
		var hiddenData = new Element("div");
		hiddenData.addClassName("hiddenData KBN_TANI");
		tableRow.insert(hiddenData);
	
		var hiddenData = new Element("div");
		hiddenData.addClassName("hiddenData KBN_URIAGE");
		tableRow.insert(hiddenData);
	
		var hiddenData = new Element("div");
		hiddenData.addClassName("hiddenData BNRI_DAI_CD");
		tableRow.insert(hiddenData);

//	}




	//値を設置
	//　下、両方親Viewの中
	var cells = tableRow.getElementsBySelector(".tableCell");
	var data = tableRow.getElementsBySelector(".hiddenData");

	
	// テキスト値取得
	// 　下、子Viewの中
	var bnriCyuCd = $F("bnriCyuCd");
	var bnriCyuNm = $F("bnriCyuNm");
	var bnriCyuRyaku = $F("bnriCyuRyaku");
	var bnriCyuExp = $F("bnriCyuExp");

	// selectは未選択時にnullになるため""に置き換え
	var bnriDaiCd = $F("koteiCd");
	if(bnriDaiCd == null){
		bnriDaiCd = "";
	}
	var kbnTani = $F("kbn_tani");
	if(kbnTani == null){
		kbnTani = "";
	}
	var kbnUriage = $F("kbn_uriage");
	if(kbnUriage == null){
		kbnUriage = "";
	}

	//変更項目の処理
	cells[1].innerHTML = bnriCyuCd;
	cells[2].innerHTML = bnriCyuNm;
	cells[3].innerHTML = bnriCyuRyaku;
	cells[4].innerHTML = bnriCyuExp;
	cells[7].innerHTML = "0";

	// 大分類コードの名称を取得
	cells[0].innerHTML = $F("koteiText");

	// 区分の名称を取得
	var kbn_tani_nm = "";
	var options = $A($('kbn_tani').getElementsByTagName('option'));
	options.each(function(option){
		if (option.selected) {
			kbn_tani_nm = option.innerHTML;
		}
	});
	cells[5].innerHTML = kbn_tani_nm;

	// 区分の名称を取得
	var kbn_uriage_nm = "";
	var options = $A($('kbn_uriage').getElementsByTagName('option'));
	options.each(function(option){
		if (option.selected) {
			kbn_uriage_nm = option.innerHTML;
		}
	});
	cells[6].innerHTML = kbn_uriage_nm;

	data[0].innerHTML = "0";
	data[1].innerHTML = "2";	//新規フラグ
	data[2].innerHTML = $F("kbn_tani");
	data[3].innerHTML = $F("kbn_uriage");
	data[4].innerHTML = $F("koteiCd");


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
	
	// hiddenに値をセット
	$('koteiCd').setValue(processVal);

	// 値を代入する
	$('koteiText').setValue(processNmVal);

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
