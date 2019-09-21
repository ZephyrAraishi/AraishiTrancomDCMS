var popupUrl = "";
var reloadUrl = "";
var bnriDaiCd = "";
var bnriCyuCd = "";
var kinmuYmd = "";
var popupView = null;
var popupCoverElement = null;

/**
 * ポップアップ初期化
 * 
 * @param url1 ポップアップ起動用URL
 * @param url2 スタッフ一覧再読み込み用URL
 */
function initPopup(url1, url2) {
	
	popupUrl = url1;
	reloadUrl = url2;
}


/**
 * 「設定」ボタン押下（予測シュミレーションの人数列）
 * 
 * @param daiCd 大分類コード
 * @param cyuCd 中分類コード
 */
function clickStaffSetupButton(daiCd, cyuCd) {

	bnriDaiCd = daiCd;
	bnriCyuCd = cyuCd;
	kinmuYmd = $("viewYmd").value;
	
	var url = popupUrl;
	url += "?daiCd=" + bnriDaiCd;
	url += "&cyuCd=" + bnriCyuCd;
	url += "&ymd=" + kinmuYmd;
	
	// 端末遅い人用に、ポップアップがでるまでのカバーをつける
	popupCoverElement = createAreaCoverElement();
	$$("body")[0].insert(popupCoverElement);
	
	new Ajax.Request(url, {
		method : 'post',
		onSuccess : function(event)  {
			var popup = Dialog.alert(event.responseText,
					{
						id: "popup",
						className: "alphacube",
						title: "スタッフ工程配置",
						width:750,
						height:450,
						draggable: true,
						destroyOnclose: false,
						recenterAuto: false,
						buttonClass : "buttons",
						okLabel: "完了",
						onOk: clickComplete,
						showEffectOptions: {duration:0.2},
						hideEffectOptions: {duration:0}
					});
				popup.setZIndex(1000);
				popupView = popup
		},
		onFailure : function(event)  {
			alert(DCMSMessage.format("CMNE000101"));
		},
		onException : function(event, ex)  {
			alert(DCMSMessage.format("CMNE000102"));
		}
	});
}

/**
 * 完了ボタン押下処理
 * 
 * @param event イベント
 */
function clickComplete(event) {

	var target = bnriDaiCd + "_" + bnriCyuCd;
	$("ni_" + target).innerHTML = $("staffCnt").value;
	$("nh_" + target).innerHTML = $("staffCnt").value;
	
	popupView.close();
	popupCoverElement.remove();
}

/**
 * 「勤務設定」ボタン押下処理
 * 
 */
function clickStaffWorkButton() {
	
	var url = "/DCMS/LSP010/";
	url += "?kotei=" + (bnriDaiCd + "_" + bnriCyuCd);
	url += "&target_ymd=" + kinmuYmd;
	url += "&sel_date=";
	url += "&set_date=";
	url += "&staff_cd_hidden=";
	url += "&staff_nm_hidden=";
	url += "&mode=1";
	
	window.open(url, "勤務設定");
}

/**
 * スタッフ一覧再読み込み
 * 
 */
function reloadStaffList() {
	
	var sendArray = {
			'daiCd' : bnriDaiCd,
			'cyuCd' : bnriCyuCd,
			'ymd' : kinmuYmd,
	}
	var postData = Object.toJSON(sendArray);
	
    new Ajax.Request( reloadUrl, {
		method : 'post',
		parameters: postData,
		onSuccess : function(event)  {
			$('staff_list').innerHTML = event.responseText;
		},
		onFailure : function(event)  {
		},
		onException : function(event, ex)  {
		}
  	});
}

