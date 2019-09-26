var actionUrl2;

/**
 * 初期処理
 * @param url
 */
function initAddRow(url) {
	actionUrl2 = url;
	$("addButton").observe("click", clickAddButton);
}

/**
 * 追加ボタンクリックイベントハンドラ
 * @param event
 */
function clickAddButton(event) {

	// 右クリックだったら終了
	if (event.isRightClick()) {
		return;
	}

	// ダブルクリックだったら終了
	if (dbclickFlag == 1) {
		dbclickFlag = 0;
		return;
	}

	dbclickFlag = 1;

	// 端末遅い人用に、ポップアップがでるまでのカバーをつける
	areaCoverElement = new Element("div");
	var height = Math.max.apply(null, [ document.body.clientHeight, document.body.scrollHeight, document.documentElement.scrollHeight, document.documentElement.clientHeight ]);

	areaCoverElement.setStyle({
		height : height + "px"
	});
	areaCoverElement.addClassName("displayCover");
	$$("body")[0].insert(areaCoverElement);

	var staffCode = $$(".hiddenData.STAFF_CODE")[0].innerHTML;
	// ポップアップを読み込む
	new Ajax.Request(actionUrl2, {
		method : 'post',

		onSuccess : viewInsertPopUp,

		onFailure : function(event) {
			location.href = "/DCMS/HRD010/hinshitsu?staff_cd=" + staffCode + "&return_cd=91&message=" + Base64.encode(DCMSMessage.format("CMNE000101"));
		},
		onException : function(event, ex) {
			location.href = "/DCMS/HRD010/hinshitsu?staff_cd=" + staffCode + "&return_cd=92&message=" + Base64.encode(DCMSMessage.format("CMNE000102"));
		}
	});

}

/**
 * ポップアップ
 * @param event
 */
function viewInsertPopUp(event) {

	// 画面に配置
	var popup = Dialog.confirm(event.responseText, {
		id : "popup",
		className : "alphacube",
		title : "スタッフ品質情報設定",
		width : 600,
		height : 340,
		draggable : true,
		destroyOnclose : true,
		recenterAuto : false,
		buttonClass : "buttonsHRD01001",
		okLabel : "追加",
		cancelLabel : "キャンセル",
		onOk : clickOKButton2,
		onCancel : clickCANCELButton2,
		showEffectOptions : {
			duration : 0.2
		},
		hideEffectOptions : {
			duration : 0
		}
	});

	popup.setZIndex(1000);

	popup.content.setStyle({
		overflow : "visible"
	});

	//分類を設定
	initBunruiPopup();
	
	// カレンダーを設置
	new InputCalendar('hassei_date', {
		lang : 'ja',
		startYear : 1900,
		weekFirstDay : ProtoCalendar.SUNDAY,
		format : 'yyyymmdd'
	});

	popUpView = popup;

}

// メソッド名がどこかで重複しそうなため、オブジェクト化しておく
var DCMSHRD010_HINSHITSU = {

	/**
	 * 入力チェック
	 *
	 * @returns true..エラーなし
	 */
	validate : function() {

		var errorMessage = "";

		// 入力チェック
		// 日付
		// 必須チェック
		if (DCMSValidation.notEmpty($F("hassei_date"))) {
			// 日付チェック
			if (!DCMSValidation.checkDate($F("hassei_date"))) {
				errorMessage += DCMSMessage.format("CMNE000007", "日付") + "\n";
			}
		} else {
			errorMessage += DCMSMessage.format("CMNE000001", "日付") + "\n";
		}

		// 品質内容
		// 必須チェック
		if (!DCMSValidation.notEmpty($F("kbn_hinshitsu_naiyo"))) {
			errorMessage += DCMSMessage.format("CMNE000001", "品質内容") + "\n";
		}

		// 担当業務
		// 必須チェック
		if (!DCMSValidation.notEmpty($F("koteiText"))) {
			errorMessage += DCMSMessage.format("CMNE000001", "担当業務") + "\n";
		}

		// 集計管理No
		if (DCMSValidation.notEmpty($F("s_kanri_no"))) {
			// 桁数チェック
			if (!DCMSValidation.maxLength($F("s_kanri_no"), 10)) {
				errorMessage += DCMSMessage.format("CMNE000003", "集計管理No", 10) + "\n";
			}

			// 数値チェック
			if (!DCMSValidation.numeric($F("s_kanri_no"))) {
				errorMessage += DCMSMessage.format("CMNE000002", "集計管理No") + "\n";
			}
		}

		// 対応者
		// 桁数チェック
		if (DCMSValidation.notEmpty($F("staff_nm")) && !DCMSValidation.maxLength($F("staff_nm"), 10)) {
			errorMessage += DCMSMessage.format("CMNE000003", "対応者", 10) + "\n";
		}

		// 対応金額
		if (DCMSValidation.notEmpty($F("t_kin"))) {
			// 桁数チェック
			if (!DCMSValidation.maxLength($F("t_kin"), 12)) {
				errorMessage += DCMSMessage.format("CMNE000003", "対応金額", 12) + "\n";
			}

			// 数値チェック
			if (!DCMSValidation.numeric($F("t_kin"))) {
				errorMessage += DCMSMessage.format("CMNE000002", "対応金額") + "\n";
			}
		}

		// 対応方法
		// 桁数チェック
		if (DCMSValidation.notEmpty($F("t_hoho")) && !DCMSValidation.maxLength($F("t_hoho"), 500)) {
			errorMessage += DCMSMessage.format("CMNE000003", "対応方法", 500) + "\n";
		}

		// エラーが発生していればメッセージを表示
		if (DCMSValidation.notEmpty(errorMessage)) {
			alert(errorMessage);
			return false;
		}

		// エラーなし
		return true;
	}
};

/**
 * OKボタンクリックイベントハンドラ
 *
 * @param window
 */
function clickOKButton2(window) {

	if (!DCMSHRD010_HINSHITSU.validate()) {
		// バリデーションエラーのため処理終了
		return;
	}

	// 行作成
	var tableRow = new Element("div");
	tableRow.setStyle({
		width:"1390px"
	});
	tableRow.addClassName("tableRow row_dat");

	var bangoCell = new Element("div");
	bangoCell.addClassName("tableCellBango  cell_dat bango");
	bangoCell.innerHTML = "新規";
	bangoCell.setStyle({
		width:"60px"
	});
	tableRow.insert(bangoCell);
	
	// セル作成
	// 変更前の日付
	var hiddenData = new Element("div");
	hiddenData.addClassName("tableCell hiddenData");
	tableRow.insert(hiddenData);

	// 日付
	var tableCell = new Element("div");
	tableCell.addClassName("tableCell cell_dat date");
	tableCell.setStyle({
		width:"100px"
	});
	tableCell.innerHTML = formatDate($F("hassei_date"));
	tableRow.insert(tableCell);

	// 行番号
	var hiddenData = new Element("div");
	hiddenData.addClassName("tableCell hiddenData");
	tableRow.insert(hiddenData);

	// 品質内容
	var kbn_hinshitsu_naiyo_nm = "";
	var kbn_hinshitsu_naiyo_cd = "";
	$("kbn_hinshitsu_naiyo").getElementsBySelector("option").each(function(elem) {
		if (elem.selected) {
			kbn_hinshitsu_naiyo_nm = elem.innerHTML;
			kbn_hinshitsu_naiyo_cd = elem.value;
		}
	});

	tableCell = new Element("div");
	tableCell.addClassName("tableCell cell_dat nm");
	tableCell.setStyle({
		width:"150px"
	});
	tableCell.innerHTML = kbn_hinshitsu_naiyo_nm;
	tableRow.insert(tableCell);

	var hiddenData = new Element("div");
	hiddenData.addClassName("tableCell hiddenData");
	hiddenData.innerHTML = kbn_hinshitsu_naiyo_cd;
	tableRow.insert(hiddenData);

	// 担当業務 工程
	var koteiName = $F("koteiText").split(" > ");
	var koteiCd = $F("koteiCd").split("_");

	// 大分
	tableCell = new Element("div");
	tableCell.addClassName("tableCell cell_dat nm");
	tableCell.setStyle({
		width:"100px"
	});
	tableCell.innerHTML = koteiName[0];
	tableRow.insert(tableCell);

	var hiddenData = new Element("div");
	hiddenData.addClassName("tableCell hiddenData");
	hiddenData.innerHTML = koteiCd[0];
	tableRow.insert(hiddenData);

	// 中分
	tableCell = new Element("div");
	tableCell.addClassName("tableCell cell_dat nm");
	tableCell.setStyle({
		width:"100px"
	});
	tableCell.innerHTML = koteiName[1];
	tableRow.insert(tableCell);

	var hiddenData = new Element("div");
	hiddenData.addClassName("tableCell hiddenData");
	hiddenData.innerHTML = koteiCd[1];
	tableRow.insert(hiddenData);

	// 細分
	tableCell = new Element("div");
	tableCell.addClassName("tableCell cell_dat nm");
	tableCell.setStyle({
		width:"100px"
	});
	tableCell.innerHTML = koteiName[2];
	tableRow.insert(tableCell);

	var hiddenData = new Element("div");
	hiddenData.addClassName("tableCell hiddenData");
	hiddenData.innerHTML = koteiCd[2];
	tableRow.insert(hiddenData);

	// 集計管理No.
	tableCell = new Element("div");
	tableCell.addClassName("tableCell cell_dat kbn");
	tableCell.setStyle({
		width:"100px"
	});
	tableCell.innerHTML = $F("s_kanri_no");
	tableRow.insert(tableCell);

	// 対応者
	tableCell = new Element("div");
	tableCell.addClassName("tableCell cell_dat nm");
	tableCell.setStyle({
		width:"140px"
	});
	tableCell.innerHTML = $F("staff_nm").escapeHTML();
	tableRow.insert(tableCell);

	// 対応金額
	tableCell = new Element("div");
	tableCell.addClassName("tableCell cell_dat kin");
	tableCell.setStyle({
		width:"100px"
	});
	tableCell.innerHTML = num3Format($F("t_kin"));
	tableRow.insert(tableCell);

	// 対応方法
	tableCell = new Element("div");
	tableCell.addClassName("tableCell cell_dat nm");
	tableCell.setStyle({
		width:"340px"
	});
	tableCell.innerHTML = $F("t_hoho").escapeHTML();
	tableRow.insert(tableCell);

	// データDIVを配置
	var hiddenData = new Element("div");
	hiddenData.addClassName("tableCell hiddenData CHANGED");
	hiddenData.innerHTML = "2"; // 新規フラグ
	tableRow.insert(hiddenData);

	tableRow.observe("click", clickPopUp);
	tableRow.observe("mouseover", overRow);
	tableRow.observe("mouseout", leaveRow);

	// テーブルに配置
	$("lstTable").insert(tableRow);

	$("lstTable").scrollTop = $("lstTable").scrollHeight;

	tableRow.setStyle({
		background : "#b0c4de"
	});

	window.close();
	dbclickFlag = 0;
	areaCoverElement.remove();
}

/**
 * キャンセルボタンクリックイベントハンドラ
 * @param window
 */
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

