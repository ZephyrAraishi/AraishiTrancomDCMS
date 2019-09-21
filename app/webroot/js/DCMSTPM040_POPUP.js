//選択パネル
var selectedPanel;

//行の値保持
var originValue = "";

//エリアロックのDIV
var areaCoverElement;


//テーブル行クリックイベントハンドラ
function clickPopUp(event) {

	dbclickFlag = 1;
	
	areaCoverElement = new Element("div");
	var height = Math.max.apply( null, [document.body.clientHeight ,
	                                    document.body.scrollHeight,
	                                    document.documentElement.scrollHeight,
	                                    document.documentElement.clientHeight] ); 
	
	areaCoverElement.setStyle({
		height: (height + 100) + "px"
	});
	areaCoverElement.addClassName("panelAreaCover");
	$$("body")[0].insert(areaCoverElement);
	
	//イベントオブジェクトが想定内のクラスである
	var eventObj = Event.element(event);
	
	if (checkPanelObject(eventObj) == false) {
		
		returnPosition();
		return;
	}

	//イベントオブジェクトがパネルクラス内である
	var panel = Event.element(event);
	
	while(true) {
		
		if (panel.hasClassName(DIV_PANEL_CLASS)) {
			
			break;
		}
		
		try {
			panel = panel.up("div");
		} catch(e) {
		
			returnPosition();
			return;
		}
	}
	
	//行ID
	selectedPanel = panel;
	
	//ポップアップを読み込む
	new Ajax.Request( '../TPM040/popup', {
	  method : 'get',

	  onSuccess : viewPopUp,
	  
	  onFailure : function( event )  {
	    location.href = "/DCMS/TPM040/index?return_cd=91&message=" + Base64.encode(DCMSMessage.format("CMNE000101"));
	  },
	  onException : function( event, ex )  {
	    location.href = "/DCMS/TPM040/index?return_cd=92&message=" + Base64.encode(DCMSMessage.format("CMNE000102"));
	  }
	});
}

/* ポップアップ */
function viewPopUp(event) {
	
	var data = selectedPanel.getElementsBySelector(".hiddenData");
	
	var S_BATCH_NO;
	var S_BATCH_NM;
	var TOP_PRIORITY_FLG;
	var WMS_FLG;
	var KBN_BATCH_STATUS;
	var SORTER;
	
	for ( var i=0; i < data.length; i++) {
		
		if (data[i].hasClassName("S_BATCH_NO")) {
			
			S_BATCH_NO = data[i].innerHTML;
		} else if (data[i].hasClassName("S_BATCH_NM")) {
			
			S_BATCH_NM = data[i].innerHTML;
		} else if (data[i].hasClassName("TOP_PRIORITY_FLG")) {
			
			TOP_PRIORITY_FLG = data[i].innerHTML;
			
		} else if (data[i].hasClassName("WMS_FLG")) {
			
			WMS_FLG = data[i].innerHTML;
		} else if (data[i].hasClassName("KBN_BATCH_STATUS")) {
			
			KBN_BATCH_STATUS = data[i].innerHTML;
		} else if (data[i].hasClassName("CURRENT_SORTER_CD")) {
			
			SORTER = data[i].innerHTML;
		}
	}
	
	//画面に配置
	var popup = Dialog.confirm(event.responseText, 
			   {
				id: "popup",
				className: "alphacube",
				title: "最優先変更",
				width:400,
				height:230,
				draggable: true,
				destroyOnclose: true,
				recenterAuto: false,
				buttonClass : "buttonsTPM01001",
				okLabel: "決定",
				cancelLabel:"キャンセル",
				onOk: clickOKButton,
				onCancel: clickCANCELButton,
				showEffectOptions: {duration:0.2},
				hideEffectOptions: {duration:0}
			   });
				   
	popup.setZIndex(2000);
	//初期値設定
	$("POPUP_S_BATCH_NM").update(S_BATCH_NM);
	$("POPUP_S_BATCH_NO").update(S_BATCH_NO);

	if (TOP_PRIORITY_FLG == "1") {
		$("POPUP_TOP_PRIORITY_FLG").checked = true;
	} else {
		$("POPUP_TOP_PRIORITY_FLG").checked = false;
	}
	
	if (WMS_FLG == "1") {
		$("POPUP_WMS_FLG").checked = true;
	} else {
		$("POPUP_WMS_FLG").checked = false;
	}
	
	if (KBN_BATCH_STATUS == "89") {
		$("tr_yusen_check").hide();
	}
	
	if (SORTER == SORTER_CD_4) {
		$("tr_yusen_check").hide();
	}

	//値を保持
	originValue = TOP_PRIORITY_FLG;
	

}


//決定ボタンクリックイベントハンドラ
function clickOKButton(window) {

	//チェックボックスの状態を取得	
	var checkedFlg = $("POPUP_TOP_PRIORITY_FLG").checked;
	var checkedFlg2 = $("POPUP_WMS_FLG").checked;
	

	//パネルの優先チェックUIとパネルデータを更新	
	var panels = $(DIV_PANEL_AREA_ID).getElementsBySelector("." + DIV_PANEL_CLASS);

	for (var i=0; i < panels.length; i++) {
	
		var elements = panels[i].getElementsBySelector("div");
		
		for (var m=0; m < elements.length; m++) {
		
			if (panels[i] == selectedPanel) {
				
				if (elements[m].hasClassName(DIV_PANEL_TOP_PRIORITY_FLG_CLASS)) {
		
					if (checkedFlg == true) {
						
						if (!elements[m].hasClassName(DIV_DATA_TOP_PRIORITY_FLG_SELECTED_CLASS)) {
							
							elements[m].addClassName(DIV_DATA_TOP_PRIORITY_FLG_SELECTED_CLASS);
						}
					} else {
						
						if (elements[m].hasClassName(DIV_DATA_TOP_PRIORITY_FLG_SELECTED_CLASS)) {
							
							elements[m].removeClassName(DIV_DATA_TOP_PRIORITY_FLG_SELECTED_CLASS);
						}
					}
		
				} else if (elements[m].hasClassName(DIV_DATA_TOP_PRIORITY_FLG_CLASS)) {
				
					if (checkedFlg == true) {
						
						elements[m].update("1");
					} else {
						
						elements[m].update("0");
					}
					
				} else if (elements[m].hasClassName(DIV_DATA_WMS_FLG_CLASS)) {
				
					if (checkedFlg2 == true) {
					
						elements[m].update("1");
					} else {
						
						elements[m].update("0");
					}
				
				} else if (elements[m].hasClassName(DIV_PANEL_S_BATCH_NO_CLASS)) {
				
					if (checkedFlg2 == true) {
					
						elements[m].setStyle({color:"red"});
					} else {
						
						elements[m].setStyle({color:"inherit"});
					}
				
				}
				
			} else {
				
				if (elements[m].hasClassName(DIV_PANEL_TOP_PRIORITY_FLG_CLASS)) {
		
					if (checkedFlg == true) {
						
						if (elements[m].hasClassName(DIV_DATA_TOP_PRIORITY_FLG_SELECTED_CLASS)) {
							
							elements[m].removeClassName(DIV_DATA_TOP_PRIORITY_FLG_SELECTED_CLASS);
						}
					}
		
				} else if (elements[m].hasClassName(DIV_DATA_TOP_PRIORITY_FLG_CLASS)) {
				
					if (checkedFlg == true) {
						
						elements[m].update("0");
					}
				}
			}
		}
	}
	
	window.close();
	dbclickFlag = 0;
	
	if (areaCoverElement != null) {
		areaCoverElement.remove();
	}
}

//キャンセルボタンクリックイベントハンドラ
function clickCANCELButton(window) {
	
	dbclickFlag = 0;
	
	if (areaCoverElement != null) {
		areaCoverElement.remove();
	}
}

