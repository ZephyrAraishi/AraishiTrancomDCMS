var actionUrl10 = '';

//アップデートイベント初期化
function initLog(url) {

	actionUrl10 = url;
	$("popupButton").observe("click",clickPopupButton);
}

//行のクリックイベントハンドラ
function clickPopupButton(event) {

	//ポップアップを読み込む
	new Ajax.Request( actionUrl10, {
	  method : 'post',

	  onSuccess : viewUpdatePopUp,

	  onFailure : function( event )  {
	    location.href = "/DCMS/TPM050/index?return_cd=91&message=" + Base64.encode(DCMSMessage.format("CMNE000101"));
	  },
	  onException : function( event, ex )  {
	    location.href = "/DCMS/TPM050/index?return_cd=92&message=" + Base64.encode(DCMSMessage.format("CMNE000102"));
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
					title: "前回更新内容",
					width:800,
					height:470,
					draggable: true,
					destroyOnclose: true,
					recenterAuto: false,
					buttonClass : "buttons",
					cancelLabel:"閉じる",
					onCancel: clickCANCELButton,
					showEffectOptions: {duration:0.2},
					hideEffectOptions: {duration:0}
				   });

	//ポップアップのZINDEXを設定
	popup.setZIndex(1000);

	popup.content.setStyle({overflow:"visible"});

	//ポップアップを保持
	popUpView = popup

}


//キャンセルボタンクリックイベントハンドラ
function clickCANCELButton(window) {

	window.close();
	dbclickFlag = 0;
	areaCoverElement.remove();
}

