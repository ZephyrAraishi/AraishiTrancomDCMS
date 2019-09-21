var rowElements1 = new Array();//現在のソーターAに属するオブジェクト配列
var rowElements2 = new Array();//現在のソーターBに属するオブジェクト配列
var rowElements3 = new Array();//現在のソーターCに属するオブジェクト配列
var rowElements4 = new Array();//現在のソーター仮置に属するオブジェクト配列

var srcSorter = '';//移動前のドラッグオブジェクトのソーター位置
var srcPosition = '';//移動前のドラッグオブジェクトの縦位置
var spaceSorter = '';//現在のスペースオブジェクトの横市
var spacePosition = '';//現在のスペースオブジェクトの縦位置

var wrapperX;//DnDエリアのラッパーの絶対X位置(pt)
var wrapperY;//DnDエリアのラッパーの絶対Y位置(pt)
var dragObjX;//移動前のドラッグオブジェクトのラッパー相対位置(pt)
var dragObjY;//移動前のドラッグオブジェクトのラッパー相対位置(pt)
var clickDiffX;//クリック座標のドラッグオブジェクト相対位置(pt)
var clickDiffY;//クリック座標のドラッグオブジェクト相対位置(pt)

var dragObj;//ドラッグ中のオブジェクト
var dragSrcObj;//元の位置を表すオブジェクト
var dragDestObj;//ドラッグ中に発生する空白オブジェクト
var curtainObj;//カーテンオブジェクト

var dbclickFlag = 0;//ダブルクリック防止フラグ
var dragStartFlg = 0;//ドラッグ中を表すフラグ
var mouseDownFlg = 0;//オブジェクト内でマウスが押されたとき:1マウスから離れたとき:0

var limitedSorter = '';//ソーター制限かかった時の移動可能ソーター


//初期化
function initPanel() {

	//オブジェクトに影をつける。
	applyDropShadows('div.' + DIV_PANEL_BASE_CLASS,DIV_PANEL_SHADOW_CLASS2);

	//オブジェクトにイベントをつける
	var panels = $(DIV_PANEL_AREA_ID).getElementsBySelector("." + DIV_PANEL_CLASS);

    for(i=0; i < panels.length; i++) {

		panels[i].observe("mousedown",mouseDownEvent);
		panels[i].observe("mousemove",mouseMoveEvent);
		panels[i].observe("mouseup",mouseUpEvent);
		panels[i].observe("mouseleave",mouseLeaveEvent);

		panels[i].setStyle({zIndex:"0"});

		//現在のソーター内のオブジェクト数を取得
		if (panels[i].getStyle("left") == "0px") {

			rowElements1[rowElements1.length] = panels[i];
			
		} else if (panels[i].getStyle("left") == "330px") {

			rowElements2[rowElements2.length] = panels[i];
			
		} else if (panels[i].getStyle("left") == "660px") {

			rowElements3[rowElements3.length] = panels[i];
			
		} else {

			rowElements4[rowElements4.length] = panels[i];
		}
	}
	
	//カバーの設定
	var covers = $$("DIV." + DIV_SORTER_CLASS);
	var h = Math.max.apply( null, [document.body.clientHeight ,
							       document.body.scrollHeight,
						           document.documentElement.scrollHeight,
						           document.documentElement.clientHeight] ); 
						           
	h = h - 300;
	
	for(var i=0; i < covers.length; i++) {
		
		covers[i].setStyle({
			height: h + "px",
			display: "none"
		});
	}
	

	//ボディイベント
	$$("body")[0].observe("mousemove",mouseMoveBodyEvent);
	$$("body")[0].observe("mouseleave",mouseLeaveBodyEvent);
	$$("body")[0].observe("mouseup",mouseUpBodyEvent);

}

//マウスダウン
function mouseDownEvent(event) {

	//チェック処理

	//右クリックだったら終了
	if (event.isRightClick()) {

		return;
	}

	//ダブルクリックだったら終了
	if (dbclickFlag == 1) {
		dbclickFlag = 0;
		return;
	}

	//イベントオブジェクトが想定外のクラスだったら終了
	var eventObj = Event.element(event);

	if (checkPanelObject(eventObj) == false) {

		return;
	}

	//パネルオブジェクト取得
	var panel = Event.element(event);

	while(true) {

		if (panel.hasClassName(DIV_PANEL_CLASS)) {

			break;
		}

		try {
			panel = panel.up("div");
		} catch(e) {
			return;
		}
	}

	//パネルに影がついていない場合終了
	var elements = panel.getElementsBySelector("div");
	var shadowFlg = false;

	for (var i = 0; i < elements.length; i++) {

		if (elements[i].hasClassName(DIV_PANEL_SHADOW_CLASS2)) {
			shadowFlg = true;
		}

	}

	if (shadowFlg == false) {
		return;
	}


	//バッチデータ設定
	
	var data = panel.getElementsBySelector(".hiddenData");

	//ドラッグオブジェクトを取得
	dragObj = panel;

	//現在の位置を取得
	srcSorter = data[9].innerHTML.trim();
	srcPosition = data[10].innerHTML.trim();


	//現在座標を保持
	dragObjX = parseInt(dragObj.getStyle("left").replace("px", ""), 10);
	dragObjY = parseInt(dragObj.getStyle("top").replace("px", ""), 10);
	var offsets = dragObj.cumulativeOffset();
    clickDiffX =  Event.pointerX(event) - offsets[0];
    clickDiffY =  Event.pointerY(event) - offsets[1];

    offsets = $(DIV_PANEL_AREA_ID).cumulativeOffset();
    wrapperX = offsets[0];
	wrapperY = offsets[1];

	//ダウンフラグを立てる
	mouseDownFlg = 1;
	
	//UI設定
	ObjToMouseDownObj();

}


//マウスムーブイベント
function mouseMoveEvent(event) {

	//ドラッグ中か
	if (mouseDownFlg == 1 ) {

		//ドラッグ開始時のみ実行
		if (dragStartFlg == 0) {

			//イベントオブジェクトが想定内のクラスである
			var eventObj = Event.element(event);

			if (checkPanelObject(eventObj) == false) {

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
					return;
				}
			}

			//イベントオブジェクトがドラッグオブジェクトである
			if (panel != dragObj) {

				return;
			}

			//ドラッグ開始
			dragStartFlg = 1;

			//オブジェクトを配置
			var data = dragObj.getElementsBySelector(".hiddenData");
			
			LocateSorterCurtain(data[5].innerHTML.trim());
			ObjToDragObj();
			LocateMarkObj();
			
		}

		//ドラッグオブジェクトをマウス位置に
		LocateDragPanel(event);

		//現在地取得
		var sorter = positionSorter(parseInt(dragObj.getStyle("left").replace("px", ""), 10) + clickDiffX);
		var position = positionOrder(parseInt(dragObj.getStyle("top").replace("px", ""), 10) + clickDiffY,sorter);

		//オブジェクトがドラッグ対象範囲内
		if (sorter != "" && position != "") {
		
			//ソーター制限があるか
			if (limitedSorter != "" && limitedSorter != sorter) {
				
				return;
			}

			//オブジェクト取得
			var elements;

			if (sorter == SORTER_CD_1) {
				elements = rowElements1;
			} else if (sorter == SORTER_CD_2) {
				elements = rowElements2;
			} else if (sorter == SORTER_CD_3) {
				elements = rowElements3;
			} else if (sorter == SORTER_CD_4) {
				elements = rowElements4;
			}

			
			//移動先がスペース挿入対象だが、一番下の場合
			if( position > elements.length) {

				//スペースがある場合
				if (spaceSorter != "" && spacePosition != "") {

					removeSpaceDataFromArray();
					setTimeout(ReLocatePanel,1);
					
					spaceSorter = "";
					spacePosition = "";
				
				}
			
			//移動先がスペース挿入対象
			} else if (!elements[position - 1].hasClassName(DIV_PANEL_TMP_MARK_CLASS) && !elements[position - 1].hasClassName(DIV_PANEL_TMP_SPACE_CLASS)) {

				//スペースの位置に変更がある場合
				if (sorter != spaceSorter || position != spacePosition) {
	
					removeSpaceDataFromArray();
					InsertSpaceToArray(sorter,position)
					setTimeout(ReLocatePanel,1);
					
					spaceSorter = sorter;
					spacePosition = position;
				}
			
			//移動先がスペース挿入対象外
			} else if (elements[position - 1].hasClassName(DIV_PANEL_TMP_MARK_CLASS)) {

				//スペースがある場合
				if (spaceSorter != "" && spacePosition != "") {

					removeSpaceDataFromArray();
					setTimeout(ReLocatePanel,1);
					
					spaceSorter = "";
					spacePosition = "";
				
				}
			}
			
		}
	} 
}

//ボディのマウスムーブイベント
function mouseMoveBodyEvent(event) {

	//ドラッグ中か
	if (mouseDownFlg == 1) {

		if (dragStartFlg == 1) {

			//ドラッグオブジェクトをマウスから離れないようにする
			LocateDragPanel(event);
		}
	}

}

//マウスリーブイベント
function mouseLeaveEvent(event) {
	
	//ドラッグ中か
	if (mouseDownFlg == 1) {

		if (dragStartFlg == 1) {

			//ドラッグオブジェクトをマウスから離れないようにする
			LocateDragPanel(event);
		}
	}
}

//ボディのマウスリーブイベント
function mouseLeaveBodyEvent(event) {

	//ドラッグ中か
	if (mouseDownFlg == 1) {

		if (dragStartFlg == 1) {

			//ドラッグ中に想定外の場所でオブジェクトがマウスから離れた場合、元の位置へ戻す
			LocateSrcPosition();

		}
	}
}

//ボディのマウスアップイベント
function mouseUpBodyEvent(event) {

	//ドラッグ中か
	if (mouseDownFlg == 1) {

		if (dragStartFlg == 1) {

			//ドラッグ中に想定外の場所でマウスアップした場合、元の位置へ戻す
			LocateSrcPosition();

		}
	}
}

//マウスアップイベント
function mouseUpEvent(event) {

	if (mouseDownFlg == 1) {
		
		//ドラッグ中の場合
		if (dragStartFlg == 1) {

			//ドラッグオブジェがない
			if (dragObj == null) {
				initAllData();
				return;
			}

			//イベントオブジェクトが想定内のクラスである
			var eventObj = Event.element(event);

			if (checkPanelObject(eventObj) == false) {

				LocateSrcPosition();
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

					LocateSrcPosition();
					return;
				}
			}

			//イベントオブジェクトがドラッグオブジェクトである
			if (panel != dragObj) {

				LocateSrcPosition();
				return;
			}

			//現在地取得
			var sorter = positionSorter(parseInt(dragObj.getStyle("left").replace("px", ""), 10) + clickDiffX);
			var position = positionOrder(parseInt(dragObj.getStyle("top").replace("px", ""), 10) + clickDiffY,sorter);

			//ドロップされた場合
			if (sorter != "" && position != "") {
			
				//ソーター制限があるか
				if (limitedSorter != "" && limitedSorter != sorter) {
					
					LocateSrcPosition();
					return;
				}
			
				//オブジェクト取得
				var elements;
	
				if (sorter == SORTER_CD_1) {
					elements = rowElements1;
				} else if (sorter == SORTER_CD_2) {
					elements = rowElements2;
				} else if (sorter == SORTER_CD_3) {
					elements = rowElements3;
				} else if (sorter == SORTER_CD_4) {
					elements = rowElements4;
				}
			
				if (srcSorter == sorter && srcPosition >= position) {
				
					LocateDropPosition(sorter,position);
				} else if (srcSorter == sorter && srcPosition < position) {
					
					LocateDropPosition(sorter,position - 1);
				} else {
				
					LocateDropPosition(sorter,position);
				}
			
			//オブジェクトが範囲外でドロップされた場合
			} else {
			
				LocateSrcPosition();
			}


		//ドラッグされていない場合
		} else {
		
			//正常なクリックイベントか、マウスダウンカーテンで判断する
			var elements = dragObj.getElementsBySelector("div");
			var cartainFlg = false;
	
			for (var i = 0; i < elements.length; i++) {
	
				if (elements[i].hasClassName(DIV_PANEL_DRAG_CARTAIN_CLASS)) {
					cartainFlg = true;
					break;
				}
	
			}
			
			//クリックイベントの場合、ポップアップに移動
			if(cartainFlg){
			
				clickPopUp(event);
				MouseDownObjToDefault();
				initAllData();
			}
		}
	}
}


/************** チェック処理用関数 ******************/


//パネル判定
function checkPanelObject(eventObj) {

	if (eventObj.hasClassName(DIV_PANEL_DRAG_CARTAIN_CLASS)) {

		return true;
	} else if (eventObj.hasClassName(DIV_PANEL_COVER_CLASS)) {

		return true;
	} else if (eventObj.hasClassName(DIV_PANEL_DONT_MOVE_CLASS)) {

		return true;
	}

	return false;
}

/************** パネル状態取得関数 ******************/

//wrapperの相対left(pt)からソーター位置を取得
function positionSorter(leftPosition) {

	if ( 0 <= leftPosition && leftPosition <= 300) {
		return SORTER_CD_1;
	} else if ( 330 <= leftPosition && leftPosition <= 630) {
		return SORTER_CD_2;
	} else if ( 660 <= leftPosition && leftPosition <= 960) {
		return SORTER_CD_3;
	} else if ( 990 <= leftPosition && leftPosition <= 1260) {
		return SORTER_CD_4;
	}

	return "";
}

//wrapperの相対top(pt)とソーター位置から、順番を取得
function positionOrder(topPosition,sorter) {

	if (sorter == SORTER_CD_1) {

		limit = 160 * rowElements1.length;

		if (0 > topPosition) {
			return "";
		}

		if (topPosition > limit) {
			return rowElements1.length + 1;
		}

		return Math.floor(topPosition / 160) + 1;

	} else if (sorter == SORTER_CD_2) {

		limit = 160 * rowElements2.length;

		if (0 > topPosition) {
			return "";
		}

		if (topPosition > limit) {
			return rowElements2.length + 1;
		}

		return Math.floor(topPosition / 160) + 1;

	} else if (sorter == SORTER_CD_3) {

		limit = 160 * rowElements3.length;

		if (0 > topPosition) {
			return "";
		}

		if (topPosition > limit) {
			return rowElements3.length + 1;
		}

		return Math.floor(topPosition / 160) + 1;

	} else if (sorter == SORTER_CD_4) {

		limit = 160 * rowElements4.length;

		if (0 > topPosition) {
			return "";
		}

		if (topPosition > limit) {
			return rowElements4.length + 1;
		}

		return Math.floor(topPosition / 160) + 1;

	}


	return "";
}

//ソーターからleftを取得
function getLeft(sorter) {

	if (sorter == SORTER_CD_1) {
		return 0;
	} else if (sorter == SORTER_CD_2) {
		return 330;
	} else if (sorter == SORTER_CD_3) {
		return 660;
	} else if (sorter == SORTER_CD_4) {
		return 990;
	}

	return 0;
}

//順番からTOPを取得
function getTop(order) {
	return (order - 1) * 160;
}

/************** パネルポジション配列設定関数(再配置と共に使われる) ******************/

//空白を消す
function removeSpaceDataFromArray() {

	if (dragDestObj != null) {

		var tempElements = new Array();
		var count =0;
		for ( var i=0; i < rowElements1.length; i++) {

			if(rowElements1[i] == null) {
				continue;
			}

			if (!rowElements1[i].hasClassName(DIV_PANEL_TMP_SPACE_CLASS)) {

				tempElements[count++] = rowElements1[i];
			}
		}
		rowElements1 = tempElements;

		tempElements = new Array();
		count =0;
		for ( var i=0; i < rowElements2.length; i++) {

			if(rowElements2[i] == null) {
				continue;
			}

			if (!rowElements2[i].hasClassName(DIV_PANEL_TMP_SPACE_CLASS)) {

				tempElements[count++] = rowElements2[i];
			}
		}
		rowElements2 = tempElements;

		tempElements = new Array();
		count =0;
		for ( var i=0; i < rowElements3.length; i++) {

			if(rowElements3[i] == null) {
				continue;
			}

			if (!rowElements3[i].hasClassName(DIV_PANEL_TMP_SPACE_CLASS)) {


				tempElements[count++] = rowElements3[i];
			}
		}
		rowElements3 = tempElements;

		dragDestObj.remove();
		dragDestObj = null;
		
		tempElements = new Array();
		count =0;
		for ( var i=0; i < rowElements4.length; i++) {

			if(rowElements4[i] == null) {
				continue;
			}

			if (!rowElements4[i].hasClassName(DIV_PANEL_TMP_SPACE_CLASS)) {


				tempElements[count++] = rowElements4[i];
			}
		}
		rowElements4 = tempElements;
		
		if (dragDestObj != null ) {

			dragDestObj.remove();
			dragDestObj = null;
		}
	}
}

//目印を消す
function removeSrcDataFromArray() {

	if (dragSrcObj != null) {

		var tempElements = new Array();
		var count =0;
		for ( var i=0; i < rowElements1.length; i++) {

			if(rowElements1[i] == null) {
				continue;
			}

			if (!rowElements1[i].hasClassName(DIV_PANEL_TMP_MARK_CLASS)) {

				tempElements[count++] = rowElements1[i];
			}
		}
		rowElements1 = tempElements;

		tempElements = new Array();
		count =0;
		for ( var i=0; i < rowElements2.length; i++) {

			if(rowElements2[i] == null) {
				continue;
			}

			if (!rowElements2[i].hasClassName(DIV_PANEL_TMP_MARK_CLASS)) {

				tempElements[count++] = rowElements2[i];
			}
		}
		rowElements2 = tempElements;

		tempElements = new Array();
		count =0;
		for ( var i=0; i < rowElements3.length; i++) {

			if(rowElements3[i] == null) {
				continue;
			}

			if (!rowElements3[i].hasClassName(DIV_PANEL_TMP_MARK_CLASS)) {

				tempElements[count++] = rowElements3[i];
			}
		}
		rowElements3 = tempElements;
		
		tempElements = new Array();
		count =0;
		for ( var i=0; i < rowElements4.length; i++) {

			if(rowElements4[i] == null) {
				continue;
			}

			if (!rowElements4[i].hasClassName(DIV_PANEL_TMP_MARK_CLASS)) {

				tempElements[count++] = rowElements4[i];
			}
		}
		rowElements4 = tempElements;

		dragSrcObj.remove();
		dragSrcObj = null;
	}
}

//空白を設置
function InsertSpaceToArray(sorter,position) {
	
	if (sorter == SORTER_CD_1) {
		elements = rowElements1;
	} else if (sorter == SORTER_CD_2) {
		elements = rowElements2;
	} else if (sorter == SORTER_CD_3) {
		elements = rowElements3;
	} else if (sorter == SORTER_CD_4) {
		elements = rowElements4;
	}


	//位置が目印オブジェクトがある場合スペースを入れない
	var bool = true;

	if (position <= elements.length) {

		if (elements[position - 1].hasClassName(DIV_PANEL_TMP_MARK_CLASS)) {

			bool = false;
		}
	}

	//上に目印オブジェクトがある場合スペースを入れない
	if (position > 1) {

	   if (elements[position - 2].hasClassName(DIV_PANEL_TMP_MARK_CLASS)) {

			bool = false;
		}

	}

	if (bool) {

		//空白を入れる
		dragDestObj = new Element("div");
		dragDestObj.addClassName(DIV_PANEL_TMP_SPACE_CLASS);
		dragDestObj.setStyle({
			display:"block",
			position:"absolute",
			width: "300px",
			top: getTop(position) + "px",
			left: getLeft(sorter) + "px",
			height: "130px",
			zIndex: "0"
		});
		$(DIV_PANEL_AREA_ID).insert(dragDestObj);

		var tempElements = new Array();
		var count =0;
		for ( var i=0; i < elements.length +1; i++) {

			if (i + 1 == position) {
				tempElements[i] = dragDestObj;
			} else {
				tempElements[i] = elements[count++];
			}
		}

		if (sorter == SORTER_CD_1) {
			rowElements1 = tempElements;
		} else if (sorter == SORTER_CD_2) {
			rowElements2 = tempElements;
		} else if (sorter == SORTER_CD_3) {
			rowElements3 = tempElements;
		} else if (sorter == SORTER_CD_4) {
			rowElements4 = tempElements;
		}

	}
}

/************** UI設定関数 ******************/

//ソーターカーテンを表示
function LocateSorterCurtain(status) {

	if (status != "00") {
		
		if (srcSorter == SORTER_CD_1) {
			
			$(DIV_SORTER_COVER_1).setStyle({display:"none"});
			$(DIV_SORTER_COVER_2).setStyle({display:"block",
											width: "330px"
			});
			$(DIV_SORTER_COVER_3).setStyle({display:"block",
											width: "330px"
			});
			$(DIV_SORTER_COVER_4).setStyle({display:"block",
											width: "330px"
			});
			
			limitedSorter = SORTER_CD_1;
			
		} else if (srcSorter == SORTER_CD_2) {
		
			$(DIV_SORTER_COVER_1).setStyle({display:"block",
											width: "315px"
			});
			$(DIV_SORTER_COVER_2).setStyle({display:"none"});
			$(DIV_SORTER_COVER_3).setStyle({display:"block",
											width: "330px"
			});
			$(DIV_SORTER_COVER_4).setStyle({display:"block",
											width: "330px"
			});
			
			limitedSorter = SORTER_CD_2;
			
		} else if (srcSorter == SORTER_CD_3) {
		
			$(DIV_SORTER_COVER_1).setStyle({display:"block",
											width: "330px"
			});
			$(DIV_SORTER_COVER_2).setStyle({display:"block",
											width: "315px"
			});
			$(DIV_SORTER_COVER_3).setStyle({display:"none"});
			$(DIV_SORTER_COVER_4).setStyle({display:"block",
											width: "330px"
			});
			
			limitedSorter = SORTER_CD_3;
			
		} else if (srcSorter == SORTER_CD_4) {
		
			$(DIV_SORTER_COVER_1).setStyle({display:"block",
											width: "330px"
			});
			$(DIV_SORTER_COVER_2).setStyle({display:"block",
											width: "330px"
			});
			$(DIV_SORTER_COVER_3).setStyle({display:"block",
											width: "330px"
			});
			$(DIV_SORTER_COVER_4).setStyle({display:"none"});
			
			limitedSorter = SORTER_CD_4;
		}
	} else {
	
		limitedSorter = "";
	}
}

//ソーターカーテンを非表示
function RemoveSorterCurtain() {

	$(DIV_SORTER_COVER_1).setStyle({display:"none"});
	$(DIV_SORTER_COVER_2).setStyle({display:"none"});
	$(DIV_SORTER_COVER_3).setStyle({display:"none"});
	$(DIV_SORTER_COVER_4).setStyle({display:"none"});

}

//指定オブジェクトをマウスダウン状態に
function ObjToMouseDownObj() {
	//カーテン追加
	curtainObj = new Element("div");
	curtainObj.addClassName(DIV_PANEL_DRAG_CARTAIN_CLASS);
	curtainObj.setStyle({
		display:"block",
		position:"absolute",
		width: "309px",
		height: "138px",
		top: "0px",
		left: "0px"
	});
	dragObj.insert(curtainObj);
}

//マウスダウン状態から元の状態へ
function MouseDownObjToDefault() {

	//カーテンを外す
	var divs = dragObj.childElements();

	for (var i=0; i < divs.length; i++) {

		if (divs[i].hasClassName(DIV_PANEL_DRAG_CARTAIN_CLASS)) {

			divs[i].remove();
			break;
		}
	}
}

//指定オブジェクトをドラッグ状態に
function ObjToDragObj() {

	//影削除（影が別オブジェクトに重なったときおかしい)
	var elements = dragObj.getElementsBySelector("div");
	var shadowFlg = false;
	var shadowDiv;
	var panelBaseDiv;

	for (var i = 0; i < elements.length; i++) {

		if (elements[i].hasClassName(DIV_PANEL_SHADOW_CLASS2)) {
			shadowFlg = true;
			shadowDiv = elements[i];
		}

		if (elements[i].hasClassName(DIV_PANEL_BASE_CLASS)) {
			panelBaseDiv = elements[i];
		}
	}

	if (shadowFlg) {

		var element = panelBaseDiv.clone();
		shadowDiv.remove();

		element.setStyle({
			height: "138px"
		});
		dragObj.insert({top:element});

	} else {
		return;
	}
	
	//ドラッグオブジェスタイル設定
	dragObj.setStyle({
		width: "308px",
		height: "138px",
		zIndex: "100"
	});

	//ボーダー追加
	dragObj.setStyle({border: DIV_PANEL_DRAG_BORDER});
	
	//ドラッグオブジェクトの表示優先度を上げる
	var tmpDragObj = dragObj.clone();
	dragObj.remove();
	$(DIV_PANEL_AREA_ID).insert(dragObj);
	
}

//マークオブジェクトをドラッグオブジェの元の位置に
function LocateMarkObj() {

	//元の位置に目印オブジェクト追加
	dragSrcObj = new Element("div");
	dragSrcObj.addClassName(DIV_PANEL_TMP_MARK_CLASS);
	dragSrcObj.setStyle({
		display:"block",
		position:"absolute",
		width: "300px",
		top: dragObjY + "px",
		left: dragObjX + "px",
		height: "138px",
		zIndex: "0"
	});
	$(DIV_PANEL_AREA_ID).insert(dragSrcObj);
	
	//移動中のオブジェクトは配列から外して
	//代わりにクローンオブジェクトを入れる
	var elements;

	if (srcSorter == SORTER_CD_1) {
		elements = rowElements1;
	} else if (srcSorter == SORTER_CD_2) {
		elements = rowElements2;
	} else if (srcSorter == SORTER_CD_3) {
		elements = rowElements3;
	} else if (srcSorter == SORTER_CD_4) {
		elements = rowElements4;
	}

	var tempElements = new Array();
	for ( var i=0; i < elements.length; i++) {
		if (elements[i] != dragObj) {
			tempElements[i] = elements[i];
		} else {
			tempElements[i] = dragSrcObj;
		}
	}

	if (srcSorter == SORTER_CD_1) {
		rowElements1 = tempElements;
	} else if (srcSorter == SORTER_CD_2) {
		rowElements2 = tempElements;
	} else if (srcSorter == SORTER_CD_3) {
		rowElements3 = tempElements;
	} else if (srcSorter == SORTER_CD_4) {
		rowElements4 = tempElements;
	}
}

//ドラッグオブジェをマウス位置に
function LocateDragPanel(event) {

	dragObj.setStyle({
		top: Event.pointerY(event) - wrapperY - clickDiffY + "px",
		left: Event.pointerX(event) -wrapperX - clickDiffX + "px"
	});
}

//パネルを配列データより再配置（データ確定）(setTimeoutからの呼び出し)
function ReLocatePanelWithData() {

	setReLocatePanel();
	setReLocatePanelData();

}

//パネルを配列データより再配置（データ未確定- MouseMove時）(setTimeoutからの呼び出し)
function ReLocatePanel() {

	setReLocatePanel();
}

//ドロップする
function LocateDropPosition(sorter,position) {

	//ベースの大きさを元に戻す
	var elements = dragObj.getElementsBySelector("div");

	for (var i = 0; i < elements.length; i++) {


		if (elements[i].hasClassName(DIV_PANEL_BASE_CLASS)) {
			elements[i].setStyle({height:"130px"});
		}
	}

	//カーテンを外す
	var divs = dragObj.childElements();

	for (var i=0; i < divs.length; i++) {

		if (divs[i].hasClassName(DIV_PANEL_DRAG_CARTAIN_CLASS)) {

			divs[i].remove();
			break;
		}
	}

	//ボーダーを外す
	dragObj.setStyle({
		border: "none",
		zIndex: "0"
	});

	//影をつける
	var elements = dragObj.getElementsBySelector("div");
	var shadowFlg = true;

	for (var i = 0; i < elements.length; i++) {

		if (elements[i].hasClassName(DIV_PANEL_SHADOW_CLASS2)) {
			shadowFlg = false;
		}

	}

	if (shadowFlg) {

		var dragId = dragObj.getAttribute("id");

		applyDropShadows('div#' + dragId + " ." + DIV_PANEL_BASE_CLASS,DIV_PANEL_SHADOW_CLASS2);
	}

	//余分なのを消す
	removeSpaceDataFromArray();
	removeSrcDataFromArray();

	//元に戻す
	var elements;
	var tempElements = new Array();
	var count = 0;

	if (sorter == SORTER_CD_1) {
		elements = rowElements1;
	} else if (sorter == SORTER_CD_2) {
		elements = rowElements2;
	} else if (sorter == SORTER_CD_3) {
		elements = rowElements3;
	} else if (sorter == SORTER_CD_4) {
		elements = rowElements4;
	}

	//オブジェクトを挿入する
	for ( var i=0; i < elements.length + 1; i++) {
		if (i == position - 1) {
			tempElements[i] = dragObj;
		} else {
			tempElements[i] = elements[count++];
		}
	}
	
	if (sorter == SORTER_CD_1) {
		rowElements1 = tempElements;
	} else if (sorter == SORTER_CD_2) {
		rowElements2 = tempElements;
	} else if (sorter == SORTER_CD_3) {
		rowElements3 = tempElements;
	} else if (sorter == SORTER_CD_4) {
		rowElements4 = tempElements;
	}

	setTimeout(ReLocatePanelWithData,100);

	initAllData();
}

//元の位置へ戻す
function LocateSrcPosition() {

	//ベースの大きさを元に戻す
	var elements = dragObj.getElementsBySelector("div");

	for (var i = 0; i < elements.length; i++) {


		if (elements[i].hasClassName(DIV_PANEL_BASE_CLASS)) {
			elements[i].setStyle({height:"130px"});
		}
	}

	//カーテンを外す
	var divs = dragObj.childElements();

	for (var i=0; i < divs.length; i++) {

		if (divs[i].hasClassName(DIV_PANEL_DRAG_CARTAIN_CLASS)) {

			divs[i].remove();
			break;
		}
	}

	//ボーダーを外す
	dragObj.setStyle({
		border: "none",
		zIndex: "0"
	});

	//影をつける
	var elements = dragObj.getElementsBySelector("div");
	var shadowFlg = true;

	for (var i = 0; i < elements.length; i++) {

		if (elements[i].hasClassName(DIV_PANEL_SHADOW_CLASS2)) {
			shadowFlg = false;
		}

	}

	if (shadowFlg) {

		var dragId = dragObj.getAttribute("id");

		applyDropShadows('div#' + dragId + " ." + DIV_PANEL_BASE_CLASS,DIV_PANEL_SHADOW_CLASS2);
	}

	//余分なのを消す
	removeSpaceDataFromArray();
	removeSrcDataFromArray();

	//元に戻す
	var elements;

	if (srcSorter == SORTER_CD_1) {
		elements = rowElements1;
	} else if (srcSorter == SORTER_CD_2) {
		elements = rowElements2;
	} else if (srcSorter == SORTER_CD_3) {
		elements = rowElements3;
	} else if (srcSorter == SORTER_CD_4) {
		elements = rowElements4;
	}

	var tempElements = new Array();
	var count = 0;
	for ( var i=0; i < elements.length + 1; i++) {
		if (i == srcPosition - 1) {
			tempElements[i] = dragObj;
		} else {
			tempElements[i] = elements[count++];
		}
	}

	if (srcSorter == SORTER_CD_1) {
		rowElements1 = tempElements;
	} else if (srcSorter == SORTER_CD_2) {
		rowElements2 = tempElements;
	} else if (srcSorter == SORTER_CD_3) {
		rowElements3 = tempElements;
	} else if (srcSorter == SORTER_CD_4) {
		rowElements4 = tempElements;
	}

	setTimeout(ReLocatePanelWithData,100);

	initAllData();
}

/************** 内部処理用関数 ******************/

function initAllData() {

	srcSorter = '';
	srcPosition = '';
	wrapperX = 0;
	wrapperY = 0;
	dragObjX = 0;
	dragObjY = 0;
	clickDiffX = 0;
	clickDiffY = 0;
	dragObj = null;
	dragSrcObj = null;
	dragDestObj = null;
	mouseDownFlg = 0;
	dragStartFlg = 0;
	limitedSorter = '';
	
	RemoveSorterCurtain();
}

//パネルを再配置
function setReLocatePanel() {

	for (var i = rowElements1.length -1 ; i >= 0 ; i--) {

		var distX = 0;
		var distY = i * 160;

		var dragObjX = parseInt(rowElements1[i].getStyle("left").replace("px", ""), 10);
		var dragObjY = parseInt(rowElements1[i].getStyle("top").replace("px", ""), 10);

		if (distX != dragObjX || distY != dragObjY) {

			new Effect.Move(rowElements1[i], {x: distX,
											 y: distY,
											 mode: 'absolute',
											 duration: DURATION
									        });

		}
	}

	for (var i = rowElements2.length -1 ; i >= 0 ; i--) {

		var distX = 330;
		var distY = i * 160;

		var dragObjX = parseInt(rowElements2[i].getStyle("left").replace("px", ""), 10);
		var dragObjY = parseInt(rowElements2[i].getStyle("top").replace("px", ""), 10);

		if (distX != dragObjX || distY != dragObjY) {

			var x = distX - dragObjX;
			var y = distY - dragObjY;

			new Effect.Move(rowElements2[i], {x: distX,
											 y: distY,
											 mode: 'absolute',
											 duration: DURATION
									        });

		}
	}

	for (var i = rowElements3.length -1 ; i >= 0 ; i--) {

		var distX = 660;
		var distY = i * 160;

		var dragObjX = parseInt(rowElements3[i].getStyle("left").replace("px", ""), 10);
		var dragObjY = parseInt(rowElements3[i].getStyle("top").replace("px", ""), 10);

		if (distX != dragObjX || distY != dragObjY) {

			new Effect.Move(rowElements3[i], {x: distX,
											 y: distY,
											 mode: 'absolute',
											 duration: DURATION
									        });

		}
	}
	
	for (var i = rowElements4.length -1 ; i >= 0 ; i--) {

		var distX = 990;
		var distY = i * 160;

		var dragObjX = parseInt(rowElements4[i].getStyle("left").replace("px", ""), 10);
		var dragObjY = parseInt(rowElements4[i].getStyle("top").replace("px", ""), 10);

		if (distX != dragObjX || distY != dragObjY) {

			new Effect.Move(rowElements4[i], {x: distX,
											 y: distY,
											 mode: 'absolute',
											 duration: DURATION
									        });

		}
	}
}

//HTMLデータを振り直す
function setReLocatePanelData() {

	try {

		for (var i = 0; i < rowElements1.length; i++) {

			rowElements1[i].getElementsBySelector("div." + DIV_DATA_CURRENT_SORTER_CLASS)[0].update(SORTER_CD_1);
			rowElements1[i].getElementsBySelector("div." + DIV_DATA_CURRENT_ORDER_CLASS)[0].update( (i + 1) + "");
		}

		for (var i = 0; i < rowElements2.length; i++) {

			rowElements2[i].getElementsBySelector("div." + DIV_DATA_CURRENT_SORTER_CLASS)[0].update(SORTER_CD_2);
			rowElements2[i].getElementsBySelector("div." + DIV_DATA_CURRENT_ORDER_CLASS)[0].update( (i + 1) + "");
		}

		for (var i = 0; i < rowElements3.length; i++) {

			rowElements3[i].getElementsBySelector("div." + DIV_DATA_CURRENT_SORTER_CLASS)[0].update(SORTER_CD_3);
			rowElements3[i].getElementsBySelector("div." + DIV_DATA_CURRENT_ORDER_CLASS)[0].update( (i + 1) + "");
		}
		
		for (var i = 0; i < rowElements4.length; i++) {

			rowElements4[i].getElementsBySelector("div." + DIV_DATA_CURRENT_SORTER_CLASS)[0].update(SORTER_CD_4);
			rowElements4[i].getElementsBySelector("div." + DIV_DATA_CURRENT_ORDER_CLASS)[0].update( (i + 1) + "");
		}

	} catch(e) {

	}
}
