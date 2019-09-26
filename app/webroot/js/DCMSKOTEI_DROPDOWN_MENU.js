
// 選択した内容をテキストボックスに反映
function setMenuProcess( dai_value, cyu_value, sai_value, koutei_dai, koutei_cyu, koutei_sai){
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
	$('ul_class').style.display = 'none';
	
}

// 各パラメータセット
var menuThreadObj = null;
var openCodeCyu = '';
var openCodeSai = '';

// メニューの表示・非表示
function menuDisp(){

	// 現状のメニュー描画状態確認
	if($('ul_class').style.display == 'none'){
		// メニューの表示
		$('ul_class').style.display = 'block';

	}else{
		// メニューの非表示
		$('ul_class').style.display = 'none';
	}
	
		// 削除してはいけないコードのセット
	openCodeCyu = '';
	openCodeSai = '';
	
	// class情報を取得(工程:中)
	var outProcessClass = $('ul_class').getElementsBySelector('.ul_second');
	
	// 工程(中)情報の表示を非表示にする
	for (var i=0; i < outProcessClass.length; i++) {

		outProcessClass[i].style.display = 'none';

	}
	
	// class情報を取得(工程:細)
	outProcessClass = $('ul_class').getElementsBySelector('.ul_third');
	// 工程(細)情報の表示をすべて非表示にする
	for (var i=0; i < outProcessClass.length; i++) {

		outProcessClass[i].style.display = 'none';
	}
}

// 画面ロード時に読込
function initBunrui(){
	// prototype.jsの定義を行う
	var rows = $("ul_class").getElementsBySelector(".ul_first");
    for(i=0; i < rows.length; i++)
	{
		rows[i].observe("mouseover",setProcessDai);
		rows[i].observe("mouseout",outProcessDai);
	}
	
	// prototype.jsの定義を行う
	var rows2 = $("ul_class").getElementsBySelector(".ul_second");
    for(i=0; i < rows2.length; i++)
	{
		rows2[i].observe("mouseover",setProcessCyu);
		rows2[i].observe("mouseout",outProcessCyu);
	}
	
	// prototype.jsの定義を行う
	var rows3 = $("ul_class").getElementsBySelector(".ul_third");
    for(i=0; i < rows3.length; i++)
	{
		rows3[i].observe("mouseover",setProcessSai);
		rows3[i].observe("mouseout",outProcessSai);
	}
	
	$("menu_img").observe("mouseout",outProcessImg);
}

// マウスオーバ時(工程:大)
function setProcessDai(event){

	// マウスオーバフラグ更新
	if (menuThreadObj != null) {
		
		menuThreadObj.stopRemove();
	}
	
	
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
	
	// 削除してはいけないコードのセット
	openCodeCyu = ("00" + li.readAttribute('value')).slice(-3)
	openCodeSai = '';
	
	// class情報を取得(工程:中)
	var outProcessClass = $('ul_class').getElementsBySelector('.ul_second');
	
	// 工程(中)情報の表示を非表示にする
	for (var i=0; i < outProcessClass.length; i++) {
	
		var code = (String)(outProcessClass[i].readAttribute('value'));
	
		// 表示しようとする工程(中)は除く
		if(code != openCodeCyu){
		
			outProcessClass[i].style.display = 'none';
		} else {
		
			outProcessClass[i].style.display = 'block';
		}
	}
	
	// class情報を取得(工程:細)
	outProcessClass = $('ul_class').getElementsBySelector('.ul_third');
	// 工程(細)情報の表示をすべて非表示にする
	for (var i=0; i < outProcessClass.length; i++) {

		outProcessClass[i].style.display = 'none';
	}
	
	//ポジション設定
	li.style.position = "relative";

}

// マウスオーバ時(工程:中)
function setProcessCyu(event){

	// マウスオーバフラグ更新
	if (menuThreadObj != null) {
		
		menuThreadObj.stopRemove();
	}
	
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
	// liのvalueを取得
	openCodeSai = ("00" + li.readAttribute('value')).slice(-3)
	
	// class情報を取得(工程:中)
	var outProcessClass = $('ul_class').getElementsBySelector('.ul_second');
	
	// 工程(中)情報の表示を非表示にする
	for (var i=0; i < outProcessClass.length; i++) {
	
		var code = (String)(outProcessClass[i].readAttribute('value'));
	
		// 表示しようとする工程(中)は除く
		if(code != openCodeCyu){
		
			outProcessClass[i].style.display = 'none';
		} else {
		
			outProcessClass[i].style.display = 'block';
		}
	}
	
		// class情報を取得(工程:細)
	outProcessClass = $('ul_class').getElementsBySelector('.ul_third');
	
		// 工程(中)情報の表示を非表示にする
	for (var i=0; i < outProcessClass.length; i++) {
	
		var code = (String)(outProcessClass[i].readAttribute('value'));
	
		// 表示しようとする工程(中)は除く
		if(code != openCodeCyu + "_" + openCodeSai){
		
			outProcessClass[i].style.display = 'none';
		} else {
		
			outProcessClass[i].style.display = 'block';
		}
	}
	
	//ポジション設定
	li.style.position = "relative";
	

}

// マウスオーバ時(工程:細)
function setProcessSai(event){

	// マウスオーバフラグ更新
	if (menuThreadObj != null) {
		
		menuThreadObj.stopRemove();
	}
	
	
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
	
	//ポジション設定
	li.style.position = "relative";
	
}

function outProcessImg(event){
	
	if (menuThreadObj != null) {
		
		menuThreadObj.stopRemove();
	}
	
	menuThreadObj = new RemoveMenu();
	menuThreadObj.remove();

}

// マウスアウト時
function outProcessDai(event){

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
	
	if (menuThreadObj != null) {
		
		menuThreadObj.stopRemove();
	}
	
	
	menuThreadObj = new RemoveMenu();
	menuThreadObj.remove();
	
}

function outProcessCyu(event){

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
	
	if (menuThreadObj != null) {
		
		menuThreadObj.stopRemove();
	}
	
	menuThreadObj = new RemoveMenu();
	menuThreadObj.remove();

}

function outProcessSai(event){
	
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
	
	if (menuThreadObj != null) {
		
		menuThreadObj.stopRemove();
	}
	
	menuThreadObj = new RemoveMenu();
	menuThreadObj.remove();

}


var RemoveMenu = Class.create();

RemoveMenu.prototype = {

	initialize : function() {
	
        this.notRemove = 0;
        
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
			$('ul_class').style.display = 'none';
			
		}
	}  
    
    
}; 
